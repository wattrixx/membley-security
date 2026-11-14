<?php

namespace App\Controllers;

use App\Libraries\PublicSubmissionTokens;
use App\Models\AdminAuditLogModel;
use App\Models\ApplicationModel;
use App\Models\CourseModel;
use App\Models\EnquiryModel;
use App\Models\SecurityEnquiryModel;
use CodeIgniter\Database\BaseConnection;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\Model;
use Throwable;

class PublicSubmissions extends BaseController
{
    private const SECURITY_CATEGORIES = [
        'personal_protection',
        'investigations',
        'event_support',
        'premises_review',
        'other',
    ];

    public function application(string $slug = ''): string
    {
        $courseModel = new CourseModel();
        $selectedCourse = null;

        if ($slug !== '') {
            $selectedCourse = $courseModel->findApplicationCourseBySlug($slug);

            if ($selectedCourse === null) {
                throw PageNotFoundException::forPageNotFound('Applications are not open for this course.');
            }
        }

        return view('public-submissions/application', [
            'pageTitle'       => 'Apply to the College | Membley Security Group',
            'metaDescription' => 'Submit an application to an available course at Membley College of Security Studies.',
            'courses'         => $courseModel->findCoursesAcceptingApplications(),
            'selectedCourse'  => $selectedCourse,
            'values'          => [],
            'errors'          => [],
            'submissionToken' => (new PublicSubmissionTokens())->issue('application'),
        ]);
    }

    public function submitApplication(): \CodeIgniter\HTTP\RedirectResponse|string
    {
        if (($limited = $this->rateLimitResponse('application')) !== null) {
            return $limited;
        }

        $values = $this->postValues([
            'full_name',
            'email',
            'phone',
            'country',
            'course_id',
            'message',
            'privacy_accepted',
        ]);
        $token = $this->postString('submission_token');

        if ($this->hasHoneypotContent() || ! (new PublicSubmissionTokens())->isValid('application', $token)) {
            return $this->invalidSubmission();
        }

        if (! $this->validateData($values, [
            'full_name'       => 'required|max_length[180]',
            'email'           => 'required|valid_email|max_length[180]',
            'phone'           => 'required|max_length[50]',
            'country'         => 'required|max_length[100]',
            'course_id'       => 'required|is_natural_no_zero',
            'message'         => 'permit_empty|max_length[2000]',
            'privacy_accepted' => 'required|in_list[1]',
        ])) {
            return $this->applicationForm($values, $this->validator->getErrors(), $token);
        }

        $course = (new CourseModel())->findApplicationCourseById((int) $values['course_id']);

        if ($course === null) {
            return $this->applicationForm(
                $values,
                ['course_id' => 'Select a course that is currently accepting applications.'],
                $token,
            );
        }

        $applicationNumber = 'MSG-' . strtoupper(bin2hex(random_bytes(12)));
        $model = new ApplicationModel();
        $this->persistSubmission($model, [
            'application_number' => $applicationNumber,
            'course_id'          => (int) $course['id'],
            'full_name'          => trim($values['full_name']),
            'email'              => strtolower(trim($values['email'])),
            'phone'              => trim($values['phone']),
            'country'            => trim($values['country']),
            'message'            => trim($values['message']),
            'privacy_accepted'   => true,
            'status'             => 'received',
        ], 'public.application_submitted', 'application');

        (new PublicSubmissionTokens())->consume($token);
        session()->setFlashdata('public_submission_confirmation', [
            'type'      => 'application',
            'reference' => $applicationNumber,
        ]);
        $this->sendNotification('college', 'A new College application was received.', $applicationNumber);

        return redirect()->to(base_url('submission/received'));
    }

    public function collegeEnquiry(): string
    {
        return view('public-submissions/college-enquiry', [
            'pageTitle'       => 'College enquiry | Membley Security Group',
            'metaDescription' => 'Send an enquiry to Membley College of Security Studies.',
            'values'          => [],
            'errors'          => [],
            'submissionToken' => (new PublicSubmissionTokens())->issue('college_enquiry'),
        ]);
    }

    public function submitCollegeEnquiry(): \CodeIgniter\HTTP\RedirectResponse|string
    {
        if (($limited = $this->rateLimitResponse('college_enquiry')) !== null) {
            return $limited;
        }

        $values = $this->postValues(['name', 'email', 'phone', 'subject', 'message', 'privacy_accepted']);
        $token = $this->postString('submission_token');

        if ($this->hasHoneypotContent() || ! (new PublicSubmissionTokens())->isValid('college_enquiry', $token)) {
            return $this->invalidSubmission();
        }

        if (! $this->validateData($values, [
            'name'            => 'required|max_length[180]',
            'email'           => 'required|valid_email|max_length[180]',
            'phone'           => 'permit_empty|max_length[50]',
            'subject'         => 'required|max_length[220]',
            'message'         => 'required|max_length[10000]',
            'privacy_accepted' => 'required|in_list[1]',
        ])) {
            return view('public-submissions/college-enquiry', [
                'pageTitle'       => 'College enquiry | Membley Security Group',
                'metaDescription' => 'Send an enquiry to Membley College of Security Studies.',
                'values'          => $values,
                'errors'          => $this->validator->getErrors(),
                'submissionToken' => $token,
            ]);
        }

        $this->persistSubmission(new EnquiryModel(), [
            'enquiry_type'     => 'college',
            'name'             => trim($values['name']),
            'email'            => strtolower(trim($values['email'])),
            'phone'            => trim($values['phone']) ?: null,
            'subject'          => trim($values['subject']),
            'message'          => trim($values['message']),
            'privacy_accepted' => true,
            'status'           => 'new',
        ], 'public.college_enquiry_submitted', 'college_enquiry');

        (new PublicSubmissionTokens())->consume($token);
        session()->setFlashdata('public_submission_confirmation', ['type' => 'college_enquiry']);
        $this->sendNotification('college', 'A new College enquiry was received.');

        return redirect()->to(base_url('submission/received'));
    }

    public function securityEnquiry(): string
    {
        return view('public-submissions/security-enquiry', [
            'pageTitle'       => 'Security Services enquiry | Membley Security Group',
            'metaDescription' => 'Send a general enquiry to Membley Investigations and Surveillance Company.',
            'values'          => [],
            'errors'          => [],
            'submissionToken' => (new PublicSubmissionTokens())->issue('security_enquiry'),
        ]);
    }

    public function submitSecurityEnquiry(): \CodeIgniter\HTTP\RedirectResponse|string
    {
        if (($limited = $this->rateLimitResponse('security_enquiry')) !== null) {
            return $limited;
        }

        $values = $this->postValues([
            'contact_name',
            'organization',
            'email',
            'phone',
            'service_category',
            'message',
            'privacy_accepted',
        ]);
        $token = $this->postString('submission_token');

        if ($this->hasHoneypotContent() || ! (new PublicSubmissionTokens())->isValid('security_enquiry', $token)) {
            return $this->invalidSubmission();
        }

        if (! $this->validateData($values, [
            'contact_name'    => 'required|max_length[180]',
            'organization'    => 'permit_empty|max_length[200]',
            'email'           => 'required|valid_email|max_length[180]',
            'phone'           => 'required|max_length[50]',
            'service_category' => 'required|in_list[' . implode(',', self::SECURITY_CATEGORIES) . ']',
            'message'         => 'required|max_length[5000]',
            'privacy_accepted' => 'required|in_list[1]',
        ])) {
            return view('public-submissions/security-enquiry', [
                'pageTitle'       => 'Security Services enquiry | Membley Security Group',
                'metaDescription' => 'Send a general enquiry to Membley Investigations and Surveillance Company.',
                'values'          => $values,
                'errors'          => $this->validator->getErrors(),
                'submissionToken' => $token,
            ]);
        }

        $this->persistSubmission(new SecurityEnquiryModel(), [
            'contact_name'    => trim($values['contact_name']),
            'organization'    => trim($values['organization']) ?: null,
            'email'            => strtolower(trim($values['email'])),
            'phone'            => trim($values['phone']),
            'service_category' => $values['service_category'],
            'message'          => trim($values['message']),
            'privacy_accepted' => true,
            'status'           => 'new',
        ], 'public.security_enquiry_submitted', 'security_enquiry');

        (new PublicSubmissionTokens())->consume($token);
        session()->setFlashdata('public_submission_confirmation', ['type' => 'security_enquiry']);
        $this->sendNotification('security', 'A new Security Services enquiry was received.');

        return redirect()->to(base_url('submission/received'));
    }

    public function received(): string
    {
        $confirmation = session()->getFlashdata('public_submission_confirmation');

        return view('public-submissions/received', [
            'pageTitle'       => 'Submission received | Membley Security Group',
            'metaDescription' => 'Confirmation that a submission was received.',
            'confirmation'    => is_array($confirmation) ? $confirmation : [],
        ]);
    }

    public function privacy(): string
    {
        return view('public-submissions/privacy', [
            'pageTitle'       => 'Privacy notice | Membley Security Group',
            'metaDescription' => 'How information submitted through the Membley Security Group website is used.',
        ]);
    }

    /**
     * @param array<string, string> $values
     * @param array<string, string> $errors
     */
    private function applicationForm(array $values, array $errors, string $token): string
    {
        return view('public-submissions/application', [
            'pageTitle'       => 'Apply to the College | Membley Security Group',
            'metaDescription' => 'Submit an application to an available course at Membley College of Security Studies.',
            'courses'         => (new CourseModel())->findCoursesAcceptingApplications(),
            'selectedCourse'  => null,
            'values'          => $values,
            'errors'          => $errors,
            'submissionToken' => $token,
        ]);
    }

    private function rateLimitResponse(string $form): ?string
    {
        $key = 'public-submission-' . $form . '-' . hash('sha256', $this->request->getIPAddress());

        if (service('throttler')->check($key, 5, 600)) {
            return null;
        }

        $this->response->setStatusCode(429);

        return view('public-submissions/error', [
            'pageTitle' => 'Please wait before submitting',
            'message'   => 'Too many submissions were received from this connection. Please wait a few minutes and try again.',
        ]);
    }

    private function invalidSubmission(): string
    {
        $this->response->setStatusCode(400);

        return view('public-submissions/error', [
            'pageTitle' => 'Unable to accept this submission',
            'message'   => 'This form has expired or could not be validated. Please reopen the form and try again.',
        ]);
    }

    /**
     * @param list<string> $fields
     * @return array<string, string>
     */
    private function postValues(array $fields): array
    {
        $values = [];

        foreach ($fields as $field) {
            $values[$field] = $this->postString($field);
        }

        return $values;
    }

    private function postString(string $field): string
    {
        $value = $this->request->getPost($field);

        return is_string($value) ? trim($value) : '';
    }

    private function hasHoneypotContent(): bool
    {
        $value = $this->request->getPost('website');

        return is_array($value) || (is_string($value) && trim($value) !== '');
    }

    /**
     * @param array<string, mixed> $data
     */
    private function persistSubmission(
        Model $model,
        array $data,
        string $action,
        string $entityType,
    ): void {
        $db = db_connect(config('Auth')->DBGroup);

        if (! $db->transBegin()) {
            throw new \RuntimeException('The submission transaction could not be started.');
        }

        try {
            if ($model->insert($data) === false) {
                throw new \RuntimeException('The submission could not be stored.');
            }

            $entityId = (int) $model->getInsertID();

            if (
                $entityId < 1
                || ! (new AdminAuditLogModel())->record(
                    null,
                    $action,
                    $entityType,
                    $entityId,
                    'Public form submission received',
                    null,
                )
            ) {
                throw new \RuntimeException('The submission audit event could not be recorded.');
            }

            if (! $db->transStatus() || ! $db->transCommit()) {
                throw new \RuntimeException('The submission could not be committed.');
            }
        } catch (Throwable $exception) {
            $db->transRollback();
            throw $exception;
        }

    }

    private function sendNotification(string $recipientType, string $message, ?string $reference = null): void
    {
        $config = config('PublicSubmissions');

        if (! $config->notificationsEnabled) {
            return;
        }

        $recipient = $recipientType === 'security'
            ? $config->securityRecipient
            : $config->collegeRecipient;
        $emailConfig = config('Email');

        if (
            filter_var($emailConfig->fromEmail, FILTER_VALIDATE_EMAIL) === false
            || filter_var($recipient, FILTER_VALIDATE_EMAIL) === false
            || $emailConfig->fromName === ''
        ) {
            log_message('warning', 'Public submission notification skipped because sender or recipient configuration is incomplete.');

            return;
        }

        $body = $message;

        if ($reference !== null) {
            $body .= "\nReference: " . $reference;
        }

        try {
            $email = service('email');
            $email->clear(true);
            $email->setFrom($emailConfig->fromEmail, $emailConfig->fromName);
            $email->setTo($recipient);
            $email->setSubject('New website submission');
            $email->setMessage($body);

            if (! $email->send()) {
                log_message('error', 'A public submission was stored, but its notification email could not be sent.');
            }
        } catch (Throwable) {
            log_message('error', 'A public submission was stored, but its notification email could not be sent.');
        }
    }
}
