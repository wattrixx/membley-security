<?php

namespace App\Content;

class AboutContent
{
    public const VISION = 'To become a trusted and leading security consultancy and professional services organization, recognized for excellence, integrity and innovation in delivering solutions that create safer, stronger and more resilient communities and institutions.';

    public const MISSION = 'To provide reliable, professional and innovative security consultancy and related services that empower individuals, businesses and institutions to identify risks, enhance their security capabilities and operate with confidence. We are committed to upholding integrity, professionalism, accountability and continuous improvement in everything we do.';

    public const VALUES = [
        'Integrity' => 'We uphold honesty, transparency and ethical conduct in every engagement.',
        'Professionalism' => 'We maintain high standards of competence, discipline and service delivery.',
        'Commitment' => 'We are dedicated to understanding and meeting the needs of those we serve.',
        'Innovation' => 'We embrace continuous learning and practical approaches to evolving security challenges.',
        'Trust and Accountability' => 'We take responsibility for our actions and work to build lasting professional relationships.',
    ];

    public const BACKGROUND = [
        'Established in 2024 as a security consultancy firm, Membley was founded with a commitment to helping individuals, businesses and institutions address security challenges through professional guidance, practical solutions and a culture of excellence.',
        'Since its establishment, the company has focused on building a foundation of trust, professionalism and accountability while pursuing opportunities to expand its contribution to security services and professional development.',
        'At Membley, we believe that effective security goes beyond protection. It involves preparedness, awareness, prevention, professionalism and building confidence among the people and organizations we serve.',
        'Through our commitment to quality service and continuous improvement, we aspire to be a trusted partner in creating safer environments and stronger institutions.',
    ];

    public static function editorContent(): string
    {
        $sections = [
            'Membley Security Group is the shared website brand for Membley College of Security Studies and Membley Investigations and Surveillance Company.',
            'The College handles courses, applications and employer training. The Security Services company handles investigation, protection and security-service enquiries.',
            "Our Vision\n" . self::VISION,
            "Our Mission\n" . self::MISSION,
        ];

        $values = ['Our Core Values'];
        foreach (self::VALUES as $name => $description) {
            $values[] = $name . "\n" . $description;
        }
        $sections[] = implode("\n\n", $values);
        $sections[] = "Our Company Background\nAbout Membley Security & College\n\n" . implode("\n\n", self::BACKGROUND);

        return implode("\n\n", $sections);
    }
}
