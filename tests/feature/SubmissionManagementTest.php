<?php

use App\Libraries\PublicSubmissionTokens;
use CodeIgniter\Shield\Models\UserModel;
use CodeIgniter\Shield\Test\AuthenticationTesting;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\Security\Exceptions\SecurityException;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;

/**
 * @internal
 */
final class SubmissionManagementTest extends CIUnitTestCase
{
    use AuthenticationTesting;
    use DatabaseTestTrait;
    use FeatureTestTrait;

    protected $namespace = null;
    protected $refresh = true;

    private function signInAs(string $role): void
    {
        $this->db->table('users')->insert(['username' => $role, 'active' => 1]);
        $user = (new UserModel())->find($this->db->insertID());
        $user->addGroup($role);
        $this->actingAs($user)->withSession();
    }

    private function insertFixtures(): void
    {
        $this->db->table('applications')->insert([
            'application_number' => 'MSG-TEST', 'full_name' => 'Applicant Alpha',
            'email' => 'applicant@example.test', 'phone' => '123', 'country' => 'Kenya',
            'status' => 'received', 'created_at' => '2026-01-01 10:00:00',
        ]);
        $this->db->table('enquiries')->insert([
            'enquiry_type' => 'college', 'name' => 'College Beta', 'email' => 'college@example.test',
            'subject' => 'Admissions', 'message' => 'Question', 'status' => 'new',
            'created_at' => '2026-01-02 10:00:00',
        ]);
        $this->db->table('enquiries')->insert([
            'enquiry_type' => 'other', 'name' => 'Noncollege Gamma', 'email' => 'other@example.test',
            'subject' => 'Other', 'message' => 'Question', 'status' => 'new',
            'created_at' => '2026-01-02 10:00:00',
        ]);
        $this->db->table('security_enquiries')->insert([
            'contact_name' => 'Security Delta', 'email' => 'security@example.test', 'phone' => '123',
            'service_category' => 'investigations', 'message' => 'Question', 'status' => 'new',
            'created_at' => '2026-01-03 10:00:00',
        ]);
    }

    public function testAdmissionsCannotAccessSecurityOrNonCollegeEnquiries(): void
    {
        $this->insertFixtures();
        $this->signInAs('admissions_officer');

        $list = $this->get('admin/college/enquiries');
        $this->assertSame(200, $list->response()->getStatusCode());
        $this->assertStringContainsString('College Beta', $list->response()->getBody());
        $this->assertStringNotContainsString('Noncollege Gamma', $list->response()->getBody());
        try {
            $this->get('admin/college/enquiries/2');
            $this->fail('A non-college enquiry must not be accessible.');
        } catch (PageNotFoundException $exception) {
            $this->assertSame(404, $exception->getCode());
        }
        $response = $this->get('admin/security/enquiries/1');
        $this->assertNotSame(200, $response->response()->getStatusCode());
        $this->assertStringNotContainsString('Security Delta', (string) $response->response()->getBody());
    }

    public function testSecurityRoleCannotAccessCollegeRecords(): void
    {
        $this->insertFixtures();
        $this->signInAs('security_services_officer');
        $this->assertSame(200, $this->get('admin/security/enquiries/1')->response()->getStatusCode());
        $this->assertNotSame(200, $this->get('admin/college/applications/1')->response()->getStatusCode());
        $this->assertNotSame(200, $this->get('admin/college/enquiries/1')->response()->getStatusCode());
        $dashboard = $this->get('admin');
        $this->assertStringContainsString('New Security Services enquiries', $dashboard->response()->getBody());
        $this->assertStringNotContainsString('Total applications', $dashboard->response()->getBody());
    }

    public function testContentManagerCannotAccessAnySubmissionModule(): void
    {
        $this->insertFixtures();
        $this->signInAs('content_manager');
        foreach (['admin/college/applications/1', 'admin/college/enquiries/1', 'admin/security/enquiries/1'] as $path) {
            $response = $this->get($path);
            $this->assertNotSame(200, $response->response()->getStatusCode());
        }
        $dashboard = $this->get('admin')->response()->getBody();
        $this->assertStringNotContainsString('Total applications', $dashboard);
        $this->assertStringNotContainsString('College enquiries awaiting attention', $dashboard);
        $this->assertStringNotContainsString('New Security Services enquiries', $dashboard);
    }

    public function testStatusAndNotesArePersistedAndAuditedWithoutNoteContent(): void
    {
        $this->insertFixtures();
        $this->signInAs('admissions_officer');
        $csrf = [csrf_token() => csrf_hash()];
        $this->withSession();
        $result = $this->post('admin/college/applications/1/status', $csrf + ['status' => 'under_review']);
        $this->assertTrue($result->isRedirect());
        $this->assertSame('under_review', $this->db->table('applications')->where('id', 1)->get()->getRowArray()['status']);
        $csrf = [csrf_token() => csrf_hash()];
        $this->withSession();
        $result = $this->post('admin/college/applications/1/notes', $csrf + ['note' => '<script>private note</script>', 'follow_up_at' => '2026-02-01']);
        $this->assertTrue($result->isRedirect());
        $this->assertSame(1, $this->db->table('submission_notes')->countAllResults());
        $audit = $this->db->table('audit_logs')->where('entity_type', 'application')->get()->getResultArray();
        $this->assertCount(2, $audit);
        $this->assertStringNotContainsString('private note', json_encode($audit));
        $this->withSession();
        $detail = $this->get('admin/college/applications/1');
        $this->assertStringContainsString('&lt;script&gt;private note&lt;/script&gt;', $detail->response()->getBody());
        $this->assertStringContainsString('2026-02-01', $detail->response()->getBody());
    }

    public function testEachEnquiryWorkflowUpdatesOnlyItsOwnRecords(): void
    {
        $this->insertFixtures();
        $this->signInAs('superadmin');
        foreach ([
            ['admin/college/enquiries/1', 'college_enquiry', 'enquiries'],
            ['admin/security/enquiries/1', 'security_enquiry', 'security_enquiries'],
        ] as [$path, $entity, $table]) {
            $csrf = [csrf_token() => csrf_hash()];
            $this->withSession();
            $this->post($path . '/status', $csrf + ['status' => 'in_progress']);
            $this->assertSame('in_progress', $this->db->table($table)->where('id', 1)->get()->getRowArray()['status']);
            $csrf = [csrf_token() => csrf_hash()];
            $this->withSession();
            $this->post($path . '/notes', $csrf + ['note' => $entity . ' note']);
            $this->assertSame(1, $this->db->table('submission_notes')->where('entity_type', $entity)->countAllResults());
        }
        $this->withSession();
        $college = $this->get('admin/college/enquiries/1')->response()->getBody();
        $this->assertStringContainsString('college_enquiry note', $college);
        $this->assertStringNotContainsString('security_enquiry note', $college);
        $security = $this->get('admin/security/enquiries/1')->response()->getBody();
        $this->assertStringContainsString('security_enquiry note', $security);
        $this->assertStringNotContainsString('college_enquiry note', $security);
    }

    public function testInvalidStatusAndUnauthorizedPostDoNotChangeRecords(): void
    {
        $this->insertFixtures();
        $this->signInAs('admissions_officer');
        $csrf = [csrf_token() => csrf_hash()];
        $this->withSession();
        $this->post('admin/college/applications/1/status', $csrf + ['status' => 'invented']);
        $csrf = [csrf_token() => csrf_hash()];
        $this->withSession();
        $this->post('admin/security/enquiries/1/status', $csrf + ['status' => 'closed']);
        $this->assertSame('received', $this->db->table('applications')->where('id', 1)->get()->getRowArray()['status']);
        $this->assertSame('new', $this->db->table('security_enquiries')->where('id', 1)->get()->getRowArray()['status']);
        $this->assertSame(0, $this->db->table('audit_logs')->countAllResults());
    }

    public function testSecurityReferenceSearchAndDateFilter(): void
    {
        $this->insertFixtures();
        $this->signInAs('security_services_officer');
        $csrf = [csrf_token() => csrf_hash()];
        $this->withSession();
        $this->post('admin/security/enquiries/filter', $csrf + ['q' => 'SEC-1', 'from' => '2026-01-03', 'to' => '2026-01-03']);
        $this->withSession();
        $response = $this->get('admin/security/enquiries');
        $this->assertStringContainsString('Security Delta', $response->response()->getBody());
        $this->assertStringContainsString('SEC-1', $response->response()->getBody());
        $this->assertStringNotContainsString('SEC-1', $response->response()->getHeaderLine('Location'));
    }

    public function testApplicationSearchAndPagination(): void
    {
        $this->insertFixtures();
        for ($i = 1; $i <= 16; $i++) {
            $this->db->table('applications')->insert([
                'application_number' => 'MSG-PAGE-' . $i, 'full_name' => 'Pagination Candidate',
                'email' => 'candidate@example.test', 'phone' => '123', 'country' => 'Kenya',
                'status' => 'received', 'created_at' => '2026-01-04 10:00:00',
            ]);
        }
        $this->signInAs('admissions_officer');
        $firstPage = $this->get('admin/college/applications')->response()->getBody();
        $this->assertStringNotContainsString('Applicant Alpha', $firstPage);
        \Config\Services::resetSingle('pager');
        $secondPage = $this->get('admin/college/applications', ['page' => '2'])->response()->getBody();
        $this->assertStringContainsString('Applicant Alpha', $secondPage);
        $csrf = [csrf_token() => csrf_hash()];
        $this->withSession();
        $response = $this->post('admin/college/applications/filter', $csrf + ['q' => 'MSG-TEST']);
        $this->assertStringNotContainsString('MSG-TEST', $response->response()->getHeaderLine('Location'));
        $this->withSession();
        \Config\Services::resetSingle('pager');
        $filtered = $this->get('admin/college/applications')->response()->getBody();
        $this->assertStringContainsString('Applicant Alpha', $filtered);
        $this->assertStringNotContainsString('Pagination Candidate', $filtered);
    }

    public function testStatusPostRequiresCsrf(): void
    {
        $this->insertFixtures();
        $this->signInAs('admissions_officer');
        $this->expectException(SecurityException::class);
        $this->post('admin/college/applications/1/status', ['status' => 'accepted']);
    }

    public function testPublicSubmissionPathsStillPersistSeparately(): void
    {
        config('PublicSubmissions')->notificationsEnabled = false;
        $this->db->table('courses')->insert([
            'title' => 'Available course', 'slug' => 'available-course', 'status' => 'available',
            'publication_status' => 'published', 'published_at' => '2020-01-01 00:00:00',
        ]);
        foreach ([
            ['college/apply', 'application', [
                'full_name' => 'Test Applicant', 'email' => 'applicant@example.test', 'phone' => '123',
                'country' => 'Kenya', 'course_id' => '1', 'privacy_accepted' => '1',
            ], 'applications'],
            ['college/enquiry', 'college_enquiry', [
                'name' => 'Test College', 'email' => 'college@example.test', 'subject' => 'Course',
                'message' => 'A question', 'privacy_accepted' => '1',
            ], 'enquiries'],
            ['security-services/enquiry', 'security_enquiry', [
                'contact_name' => 'Test Security', 'email' => 'security@example.test', 'phone' => '123',
                'service_category' => 'other', 'message' => 'A question', 'privacy_accepted' => '1',
            ], 'security_enquiries'],
        ] as [$path, $form, $values, $table]) {
            $token = (new PublicSubmissionTokens())->issue($form);
            $csrf = [csrf_token() => csrf_hash()];
            $this->withSession();
            $response = $this->post($path, $csrf + ['submission_token' => $token] + $values);
            $this->assertTrue($response->isRedirect());
            $this->assertSame(1, $this->db->table($table)->countAllResults());
        }
    }
}
