<?php

namespace App\Controllers\Admin;

use App\Models\AdminAuditLogModel;
use CodeIgniter\Database\BaseBuilder;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\RedirectResponse;
use DateTimeImmutable;
use Throwable;

abstract class SubmissionManagement extends AdminBaseController
{
    protected string $table;
    protected string $entity;
    protected string $permission;
    protected string $route;
    protected string $title;
    protected string $nameField;
    protected array $searchFields;
    protected array $statuses;

    public function index(): string
    {
        $this->authorize('view');
        $filters = session()->get('submission_filters_' . $this->entity) ?? [];
        $builder = $this->records();
        $search = $filters['q'] ?? '';

        if ($search !== '') {
            $builder->groupStart();
            foreach ($this->searchFields as $index => $field) {
                $index === 0 ? $builder->like('s.' . $field, $search) : $builder->orLike('s.' . $field, $search);
            }
            if ($this->entity === 'security_enquiry' && preg_match('/\ASEC-([1-9][0-9]*)\z/i', $search, $matches)) {
                $builder->orWhere('s.id', $matches[1]);
            }
            $builder->groupEnd();
        }

        foreach (['status', 'course_id', 'service_category'] as $field) {
            if (($filters[$field] ?? '') !== '') {
                $builder->where('s.' . $field, $filters[$field]);
            }
        }
        if (($filters['from'] ?? '') !== '') {
            $builder->where('s.created_at >=', $filters['from'] . ' 00:00:00');
        }
        if (($filters['to'] ?? '') !== '') {
            $builder->where('s.created_at <=', $filters['to'] . ' 23:59:59');
        }

        $total = $builder->countAllResults(false);
        $pager = service('pager');
        $page = max(1, min($pager->getCurrentPage(), max(1, (int) ceil($total / 15))));
        $records = $builder->orderBy('s.created_at', 'DESC')->orderBy('s.id', 'DESC')
            ->limit(15, ($page - 1) * 15)->get()->getResultArray();
        $courses = $this->entity === 'application'
            ? $this->db()->table('courses')->select('id, title')->orderBy('title')->get()->getResultArray()
            : [];

        return view('admin/submissions/index', [
            'pageTitle' => $this->title . ' | Membley Administration',
            'title' => $this->title, 'entity' => $this->entity, 'route' => $this->route,
            'nameField' => $this->nameField, 'statuses' => $this->statuses,
            'filters' => $filters, 'records' => $records, 'courses' => $courses,
            'pagination' => $pager->makeLinks($page, 15, $total),
            'message' => session('message'), 'error' => session('error'),
        ]);
    }

    public function filter(): RedirectResponse
    {
        $this->authorize('view');
        $filters = [];
        foreach (['q', 'status', 'from', 'to'] as $field) {
            $filters[$field] = trim($this->postString($field));
        }
        if ($this->entity === 'application') {
            $filters['course_id'] = trim($this->postString('course_id'));
        }
        if ($this->entity === 'security_enquiry') {
            $filters['service_category'] = trim($this->postString('service_category'));
        }

        $invalid = mb_strlen($filters['q']) > 180
            || ($filters['status'] !== '' && ! isset($this->statuses[$filters['status']]))
            || ! $this->validDate($filters['from']) || ! $this->validDate($filters['to'])
            || ($filters['from'] !== '' && $filters['to'] !== '' && $filters['from'] > $filters['to'])
            || (isset($filters['course_id']) && $filters['course_id'] !== '' && ! ctype_digit($filters['course_id']))
            || (isset($filters['service_category']) && ! in_array($filters['service_category'], ['', 'personal_protection', 'investigations', 'event_support', 'premises_review', 'other'], true));
        if ($invalid) {
            return redirect()->route($this->route)->with('error', 'Choose valid filters and a valid date range.');
        }

        session()->set('submission_filters_' . $this->entity, $filters);
        return redirect()->route($this->route);
    }

    public function show(string $id): string
    {
        $this->authorize('view');
        $record = $this->findRecord($id);
        $users = config('Auth')->tables['users'];
        $notes = $this->db()->table('submission_notes n')
            ->select('n.note, n.follow_up_at, n.created_at, u.username')
            ->join($users . ' u', 'u.id = n.author_id', 'left')
            ->where('n.entity_type', $this->entity)->where('n.entity_id', $id)
            ->orderBy('n.id', 'DESC')->get()->getResultArray();
        $history = $this->db()->table('audit_logs a')
            ->select('a.action, a.details, a.created_at, u.username')
            ->join($users . ' u', 'u.id = a.user_id', 'left')
            ->where('a.entity_type', $this->entity)->where('a.entity_id', $id)
            ->whereIn('a.action', [
                'admin.submission_status_changed', 'admin.submission_note_added',
                'public.' . $this->entity . '_submitted',
            ])
            ->orderBy('a.id', 'DESC')->get()->getResultArray();

        return view('admin/submissions/show', [
            'pageTitle' => $this->title . ' | Membley Administration',
            'title' => $this->title, 'entity' => $this->entity, 'route' => $this->route,
            'record' => $record, 'statuses' => $this->statuses, 'notes' => $notes,
            'history' => $history, 'canManage' => auth()->user()->can($this->permission . '.manage'),
            'message' => session('message'), 'error' => session('error'),
        ]);
    }

    public function status(string $id): RedirectResponse
    {
        $this->authorize('manage');
        $record = $this->findRecord($id);
        $status = $this->postString('status');
        if (! isset($this->statuses[$status])) {
            return redirect()->route($this->route . '.show', [$id])->with('error', 'Choose a valid status.');
        }
        if ($record['status'] === $status) {
            return redirect()->route($this->route . '.show', [$id])->with('message', 'Status is unchanged.');
        }

        $db = $this->db();
        if (! $db->transBegin()) {
            throw new \RuntimeException('Could not start the submission update transaction.');
        }
        try {
            $builder = $db->table($this->table)->where('id', $id)->where('status', $record['status']);
            if ($this->entity === 'college_enquiry') {
                $builder->where('enquiry_type', 'college');
            }
            if (! $builder->update(['status' => $status, 'updated_at' => date('Y-m-d H:i:s')]) || $db->affectedRows() !== 1) {
                throw new \RuntimeException('The submission changed concurrently; reload and try again.');
            }
            $this->audit('admin.submission_status_changed', (int) $id, 'from=' . $record['status'] . ';to=' . $status);
            if (! $db->transStatus() || ! $db->transCommit()) {
                throw new \RuntimeException('Could not commit the submission update.');
            }
        } catch (Throwable $exception) {
            $db->transRollback();
            throw $exception;
        }
        return redirect()->route($this->route . '.show', [$id])->with('message', 'Status updated.');
    }

    public function note(string $id): RedirectResponse
    {
        $this->authorize('manage');
        $this->findRecord($id);
        $note = trim($this->postString('note'));
        $followUp = trim($this->postString('follow_up_at'));
        if ($note === '' || mb_strlen($note) > 5000 || ! $this->validDate($followUp)) {
            return redirect()->route($this->route . '.show', [$id])->with('error', 'Enter a note of up to 5,000 characters and a valid follow-up date.');
        }

        $db = $this->db();
        if (! $db->transBegin()) {
            throw new \RuntimeException('Could not start the submission note transaction.');
        }
        try {
            if (! $db->table('submission_notes')->insert([
                'entity_type' => $this->entity, 'entity_id' => (int) $id,
                'author_id' => (int) auth()->id(), 'note' => $note,
                'follow_up_at' => $followUp === '' ? null : $followUp,
                'created_at' => date('Y-m-d H:i:s'),
            ])) {
                throw new \RuntimeException('Could not save the submission note.');
            }
            $this->audit('admin.submission_note_added', (int) $id, '');
            if (! $db->transStatus() || ! $db->transCommit()) {
                throw new \RuntimeException('Could not commit the submission note.');
            }
        } catch (Throwable $exception) {
            $db->transRollback();
            throw $exception;
        }
        return redirect()->route($this->route . '.show', [$id])->with('message', 'Internal note added.');
    }

    protected function records(): BaseBuilder
    {
        $builder = $this->db()->table($this->table . ' s')->select('s.*');
        if ($this->entity === 'college_enquiry') {
            $builder->where('s.enquiry_type', 'college');
        }
        if ($this->entity === 'application') {
            $builder->select('c.title AS course_title')->join('courses c', 'c.id = s.course_id', 'left');
        }
        return $builder;
    }

    private function findRecord(string $id): array
    {
        $record = $this->records()->where('s.id', $id)->get()->getRowArray();
        if ($record === null) {
            throw PageNotFoundException::forPageNotFound('Submission not found.');
        }
        return $record;
    }

    private function authorize(string $action): void
    {
        $user = auth()->user();
        if ($user === null || ! $user->can($this->permission . '.' . $action)) {
            throw PageNotFoundException::forPageNotFound('Submission not found.');
        }
    }

    private function validDate(string $date): bool
    {
        if ($date === '') {
            return true;
        }
        if (preg_match('/\A[0-9]{4}-[0-9]{2}-[0-9]{2}\z/', $date) !== 1) {
            return false;
        }
        $parsed = DateTimeImmutable::createFromFormat('!Y-m-d', $date);
        return $parsed !== false && $parsed->format('Y-m-d') === $date;
    }

    private function db(): \CodeIgniter\Database\BaseConnection
    {
        return db_connect(config('Auth')->DBGroup);
    }

    private function audit(string $action, int $id, string $details): void
    {
        if (! (new AdminAuditLogModel($this->db()))->record((int) auth()->id(), $action, $this->entity, $id, $details, null)) {
            throw new \RuntimeException('Could not record the submission audit event.');
        }
    }
}
