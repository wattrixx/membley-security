<?php

namespace App\Controllers;

use App\Models\CourseModel;
use App\Models\AnnouncementModel;
use App\Models\PostModel;

class Home extends BaseController
{
    public function index(): string
    {
        $featuredCourses = $this->addImagePaths(
            (new CourseModel())->findFeaturedPublicCourses(),
        );
        $latestPosts = $this->addImagePaths(
            (new PostModel())->findLatestPublicPosts(),
        );
        $announcements = $this->addImagePaths(
            (new AnnouncementModel())->findPublicAnnouncements(3),
        );

        return view('home/index', [
            'pageTitle' => 'Membley Security Group | Security, Training & Logistics',
            'metaDescription' => 'Discover Membley Security Group, its security, training and transport and logistics services.',
            'preloadImage' => 'assets/images/security-technology.jpg',
            'featuredCourses' => $featuredCourses,
            'latestPosts' => $latestPosts,
            'announcements' => $announcements,
        ]);
    }
}
