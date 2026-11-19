<?php

use CodeIgniter\Test\CIUnitTestCase;

/**
 * @internal
 */
final class FooterNavigationTest extends CIUnitTestCase
{
    public function testHeaderAndFooterShowRequestedNavigationLinks(): void
    {
        $html = view('pages/about');
        $document = new DOMDocument();
        $this->assertTrue(@$document->loadHTML($html));
        $xpath = new DOMXPath($document);
        $navigation = [];
        foreach (['Main navigation', 'Footer navigation'] as $label) {
            $navigation[$label] = [];
            foreach ($xpath->query('//nav[@aria-label="' . $label . '"]/a') as $link) {
                $navigation[$label][trim($link->textContent)] = $link->getAttribute('href');
            }
        }

        $this->assertSame([
            'Home' => base_url('/'),
            'About the Group' => base_url('about'),
            'Security Services' => base_url('security-services'),
            'Training College' => base_url('college'),
            'Contact' => base_url('contact'),
        ], $navigation['Main navigation']);
        $this->assertSame([
            'About the Group' => base_url('about'),
            'College' => base_url('college'),
            'Courses' => base_url('college/courses'),
            'Admissions' => base_url('college#admissions'),
            'Security Services' => base_url('security-services'),
            'News & Updates' => base_url('news'),
            'Contact' => base_url('contact'),
        ], $navigation['Footer navigation']);
    }
}
