<?php

use App\Content\TransportContent;
use CodeIgniter\Shield\Models\UserModel;
use CodeIgniter\Shield\Test\AuthenticationTesting;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;

/**
 * @internal
 */
final class TransportPagesTest extends CIUnitTestCase
{
    use AuthenticationTesting;
    use DatabaseTestTrait;
    use FeatureTestTrait;

    protected $namespace = null;
    protected $refresh = true;

    public function testDefaultPagesLinkToEachOther(): void
    {
        foreach (TransportContent::PAGES as $page) {
            $response = $this->get($page['path'])->response();
            $this->assertSame(200, $response->getStatusCode());
            $body = $response->getBody();
            $this->assertStringContainsString($page['image'], html_entity_decode($body, ENT_QUOTES | ENT_HTML5));
            $this->assertFileExists(FCPATH . $page['image']);
            foreach (TransportContent::PAGES as $linkedPage) {
                $this->assertStringContainsString(base_url($linkedPage['path']), $body);
            }
        }
    }

    public function testOverviewShowsDocumentSectionsAndCaseByCaseConditions(): void
    {
        $body = $this->get('transport-logistics')->response()->getBody();
        foreach ([
            'Our Core Services', 'Warehousing and Storage', 'Specialised Logistics Solutions',
            'Taxi and Passenger Transportation', 'Transportation Solutions',
            'Intermodal and Multimodal Transportation', 'Our Geographical Coverage',
            'Kenya', 'Uganda', 'Tanzania', 'Rwanda', 'Ethiopia',
            'Availability and Service Arrangements', 'How We Work', 'Before Every Engagement',
        ] as $text) {
            $this->assertStringContainsString($text, $body);
        }
        $this->assertStringContainsString('verified on a case-by-case basis', $body);
        $this->assertStringNotContainsString('This is illustrative placeholder information', $body);
    }

    public function testContentManagerCanPublishEachPageAndDraftsRemainPrivate(): void
    {
        $this->db->table('users')->insert(['username' => 'content_manager', 'active' => 1]);
        $user = (new UserModel())->find($this->db->insertID());
        $user->addGroup('content_manager');
        $this->actingAs($user)->withSession();

        foreach (TransportContent::PAGES as $slug => $page) {
            $editor = $this->get('admin/pages/' . $slug . '/edit')->response();
            $this->assertSame(200, $editor->getStatusCode());
            $this->assertStringContainsString($page['content'], html_entity_decode($editor->getBody(), ENT_QUOTES | ENT_HTML5));

            $data = [
                'title' => 'Edited transport page', 'content' => 'Private draft ' . $slug,
                'status' => 'draft', 'published_at' => '',
            ];
            $this->withSession();
            $this->post('admin/pages/' . $slug, [csrf_token() => csrf_hash()] + $data);
            $this->withSession();
            $this->assertStringNotContainsString($data['content'], $this->get($page['path'])->response()->getBody());

            $data['status'] = 'published';
            $data['content'] = 'Published ' . $slug . ' <script>unsafe</script>';
            $this->withSession();
            $this->post('admin/pages/' . $slug, [csrf_token() => csrf_hash()] + $data);
            $this->withSession();
            $body = $this->get($page['path'])->response()->getBody();
            $this->assertStringContainsString('Published ' . $slug . ' &lt;script&gt;unsafe&lt;/script&gt;', $body);
            $this->assertStringContainsString(base_url('transport-logistics/services'), $body);
        }
    }

    public function testPublishedOverviewKeepsEditableStructuredContentAndEscapesHtml(): void
    {
        $this->db->table('users')->insert(['username' => 'transport_editor', 'active' => 1]);
        $user = (new UserModel())->find($this->db->insertID());
        $user->addGroup('content_manager');
        $this->actingAs($user)->withSession();
        $content = "## Updated section\n\nIntroduction.\n\n### Updated service\n\n<script>unsafe</script>";
        $data = ['title' => 'Updated transport', 'content' => $content, 'status' => 'published', 'published_at' => ''];
        $preview = $this->post('admin/pages/preview', [csrf_token() => csrf_hash(), 'slug' => 'transport-logistics'] + $data)->response()->getBody();
        $this->assertStringContainsString('<h2>Updated section</h2>', $preview);
        $this->assertStringContainsString('<h3>Updated service</h3>', $preview);
        $this->withSession();
        $this->post('admin/pages/transport-logistics', [csrf_token() => csrf_hash()] + $data);
        $this->withSession();
        $body = $this->get('transport-logistics')->response()->getBody();
        $this->assertStringContainsString('<h2>Updated section</h2>', $body);
        $this->assertStringContainsString('<h3>Updated service</h3>', $body);
        $this->assertStringContainsString('&lt;script&gt;unsafe&lt;/script&gt;', $body);
        $this->assertStringNotContainsString('Our Core Services', $body);
    }

    public function testSecurityOfficerCannotEditTransportPages(): void
    {
        $this->db->table('users')->insert(['username' => 'security_officer', 'active' => 1]);
        $user = (new UserModel())->find($this->db->insertID());
        $user->addGroup('security_services_officer');
        $this->actingAs($user)->withSession();
        $this->assertNotSame(200, $this->get('admin/pages/transport-logistics/edit')->response()->getStatusCode());
    }
}
