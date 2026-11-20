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

    public function testDefaultPagesShowPlaceholdersAndLinkToEachOther(): void
    {
        foreach (TransportContent::PAGES as $page) {
            $response = $this->get($page['path'])->response();
            $this->assertSame(200, $response->getStatusCode());
            $body = $response->getBody();
            $this->assertStringContainsString('Illustrative information and stock photography', $body);
            $this->assertStringContainsString($page['image'], html_entity_decode($body, ENT_QUOTES | ENT_HTML5));
            $this->assertFileExists(FCPATH . $page['image']);
            foreach (TransportContent::PAGES as $linkedPage) {
                $this->assertStringContainsString(base_url($linkedPage['path']), $body);
            }
        }
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
            $this->assertStringContainsString($page['content'], $editor->getBody());

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

    public function testSecurityOfficerCannotEditTransportPages(): void
    {
        $this->db->table('users')->insert(['username' => 'security_officer', 'active' => 1]);
        $user = (new UserModel())->find($this->db->insertID());
        $user->addGroup('security_services_officer');
        $this->actingAs($user)->withSession();
        $this->assertNotSame(200, $this->get('admin/pages/transport-logistics/edit')->response()->getStatusCode());
    }
}
