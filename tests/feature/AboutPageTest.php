<?php

use App\Content\AboutContent;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;

/**
 * @internal
 */
final class AboutPageTest extends CIUnitTestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait;

    protected $namespace = null;
    protected $refresh = true;

    public function testDefaultAboutPageShowsSuppliedSectionsInOrder(): void
    {
        $body = $this->get('about')->response()->getBody();

        $this->assertStringContainsString(AboutContent::VISION, $body);
        $this->assertStringContainsString(AboutContent::MISSION, $body);
        $this->assertStringContainsString(AboutContent::BACKGROUND[0], $body);
        $this->assertLessThan(strpos($body, 'Our Mission'), strpos($body, 'Our Vision'));
        $this->assertLessThan(strpos($body, 'Our Core Values'), strpos($body, 'Our Mission'));
        $this->assertLessThan(strpos($body, 'Our Company Background'), strpos($body, 'Our Core Values'));
        foreach (AboutContent::VALUES as $name => $description) {
            $this->assertStringContainsString($name, $body);
            $this->assertStringContainsString($description, $body);
        }
    }

    public function testPublishedAboutContentFormatsHeadingsAndEscapesHtml(): void
    {
        $this->db->table('pages')->insert([
            'title' => 'About the Group', 'slug' => 'about',
            'content' => AboutContent::editorContent() . "\n\n<script>unsafe</script>",
            'status' => 'published', 'published_at' => '2020-01-01 00:00:00',
        ]);
        $body = $this->get('about')->response()->getBody();

        $this->assertStringContainsString('<h2>Our Vision</h2>', $body);
        $this->assertStringContainsString('<h2>Our Mission</h2>', $body);
        $this->assertStringContainsString('<h2>Our Core Values</h2>', $body);
        $this->assertStringContainsString('<h3>Trust and Accountability</h3>', $body);
        $this->assertStringContainsString('&lt;script&gt;unsafe&lt;/script&gt;', $body);
        $this->assertStringNotContainsString('<script>unsafe</script>', $body);
    }
}
