<?php

namespace App\Controllers;

use App\Models\CourseModel;
use App\Models\AnnouncementModel;
use App\Models\PageModel;
use App\Models\PostModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class PublicSite extends BaseController
{
    private const CONTACT_PHONE = '+254704084328';
    private const CONTACT_PHONE_DISPLAY = '+254 704 084 328';
    private const CONTACT_MAP = 'https://maps.app.goo.gl/u1fQ5uNFDMz1g3cQ6';

    public function about(): string
    {
        return $this->renderStaticPage(
            'about',
            'pages/about',
            'About the Group | Membley Security Group',
            'Learn how Membley Security Group connects its College and Security Services teams.',
        );
    }

    public function college(): string
    {
        return $this->renderStaticPage(
            'college',
            'pages/college',
            'Membley College of Security Studies',
            'Explore the College training approach, current course listings and admissions contact.',
        );
    }

    public function securityServices(): string
    {
        return $this->renderStaticPage(
            'security-services',
            'pages/security_services',
            'Security Services | Membley Security Group',
            'Contact Membley Investigations and Surveillance Company about security service enquiries.',
        );
    }

    public function courses(): string
    {
        $courses = $this->addImagePaths((new CourseModel())->findPublicCourses());
        $availableCourses = [];
        $developmentCourses = [];

        foreach ($courses as $course) {
            $course['status_label'] = $this->courseStatusLabel($course['status']);

            if ($course['status'] === 'available') {
                $availableCourses[] = $course;
            } else {
                $developmentCourses[] = $course;
            }
        }

        return $this->renderPage(
            'courses/index',
            'Courses | Membley College of Security Studies',
            'Browse published courses and programmes in development at Membley College of Security Studies.',
            [
                'availableCourses' => $availableCourses,
                'developmentCourses' => $developmentCourses,
            ],
        );
    }

    public function course(string $slug): string
    {
        $course = (new CourseModel())->findPublicCourseBySlug($slug);

        if ($course === null) {
            throw PageNotFoundException::forPageNotFound('Course not found.');
        }

        $course['featured_image_path'] = $this->publicImagePath($course['featured_image'] ?? null);
        $course['featured_image_alt'] = $course['featured_image_path'] === null
            ? ''
            : $this->imageAlt($course['featured_image_path']);
        $course['status_label'] = $this->courseStatusLabel($course['status']);
        $course['fee_display'] = $this->verifiedFee($course);

        return $this->renderPage(
            'courses/show',
            $course['title'] . ' | Membley College of Security Studies',
            $course['short_description'] ?: 'Course information from Membley College of Security Studies.',
            ['course' => $course],
        );
    }

    public function news(): string
    {
        $postModel = new PostModel();
        $posts = $this->addImagePaths($postModel->paginatePublicPosts());
        $announcements = $this->addImagePaths(
            (new AnnouncementModel())->findPublicAnnouncements(10),
        );

        return $this->renderPage(
            'news/index',
            'News and Updates | Membley Security Group',
            'Published news and announcements from Membley Security Group.',
            [
                'posts' => $posts,
                'announcements' => $announcements,
                'pager' => $postModel->pager,
            ],
        );
    }

    public function article(string $slug): string
    {
        $post = (new PostModel())->findPublicPostBySlug($slug);

        if ($post === null) {
            throw PageNotFoundException::forPageNotFound('News article not found.');
        }

        $post['featured_image_path'] = $this->publicImagePath($post['featured_image'] ?? null);
        $post['featured_image_alt'] = $post['featured_image_path'] === null
            ? ''
            : $this->imageAlt($post['featured_image_path']);
        $metaTitle = trim((string) ($post['meta_title'] ?? ''));
        $metaDescription = trim((string) ($post['meta_description'] ?? ''));

        return $this->renderPage(
            'news/show',
            $metaTitle !== '' ? $metaTitle : $post['title'] . ' | Membley News',
            $metaDescription !== ''
                ? $metaDescription
                : ($post['excerpt'] ?: 'News and updates from Membley Security Group.'),
            ['post' => $post],
        );
    }

    public function contact(): string
    {
        return $this->contactPage(
            'Contact Membley Security Group',
            'Choose the team that best fits your enquiry. The phone number and location are shared contact details.',
            $this->contactOptions(),
        );
    }

    public function collegeContact(): string
    {
        return $this->contactPage(
            'Contact Membley College of Security Studies',
            'Contact the College about admissions, course availability or employer training.',
            [$this->contactOptions()[0]],
        );
    }

    public function securityContact(): string
    {
        return $this->contactPage(
            'Contact Membley Investigations and Surveillance Company',
            'Contact the Security Services team to discuss your enquiry and the scope of support.',
            [$this->contactOptions()[1]],
        );
    }

    /**
     * @param array<string, mixed> $data
     */
    private function renderPage(
        string $view,
        string $pageTitle,
        string $metaDescription,
        array $data = [],
    ): string {
        return view($view, array_merge($data, [
            'pageTitle' => $pageTitle,
            'metaDescription' => $metaDescription,
        ]));
    }

    private function renderStaticPage(
        string $slug,
        string $view,
        string $pageTitle,
        string $metaDescription,
    ): string {
        $page = (new PageModel())->findPublishedPageBySlug($slug);

        if ($page === null || trim($page['content']) === '') {
            return $this->renderPage($view, $pageTitle, $metaDescription);
        }

        $page['featured_image_path'] = $this->publicImagePath($page['featured_image'] ?? null);
        $page['featured_image_alt'] = $page['featured_image_path'] === null
            ? ''
            : $this->imageAlt($page['featured_image_path']);
        $storedTitle = trim((string) ($page['meta_title'] ?? ''));
        $storedDescription = trim((string) ($page['meta_description'] ?? ''));

        return $this->renderPage(
            'pages/published',
            $storedTitle !== '' ? $storedTitle : $page['title'],
            $storedDescription !== '' ? $storedDescription : $metaDescription,
            ['page' => $page],
        );
    }

    private function courseStatusLabel(string $status): string
    {
        return match ($status) {
            'available' => 'Available',
            'proposed' => 'Proposed',
            default => 'In development',
        };
    }

    /**
     * @param array<string, mixed> $course
     */
    private function verifiedFee(array $course): ?string
    {
        if (empty($course['fee_verified']) || $course['fee'] === null || $course['fee'] === '') {
            return null;
        }

        return number_format((float) $course['fee'], 2, '.', ',');
    }

    /**
     * @return list<array{organisation: string, purpose: string, path: string}>
     */
    private function contactOptions(): array
    {
        return [
            [
                'organisation' => 'Membley College of Security Studies',
                'purpose' => 'Admissions and employer training',
                'path' => 'college',
            ],
            [
                'organisation' => 'Membley Investigations and Surveillance Company',
                'purpose' => 'Security service enquiries',
                'path' => 'security-services',
            ],
        ];
    }

    /**
     * @param list<array{organisation: string, purpose: string, path: string}> $contacts
     */
    private function contactPage(string $title, string $description, array $contacts): string
    {
        return $this->renderPage(
            'contact/index',
            $title . ' | Membley Security Group',
            $description,
            [
                'contactTitle' => $title,
                'contactDescription' => $description,
                'contacts' => $contacts,
                'phone' => self::CONTACT_PHONE,
                'phoneDisplay' => self::CONTACT_PHONE_DISPLAY,
                'mapUrl' => self::CONTACT_MAP,
            ],
        );
    }
}
