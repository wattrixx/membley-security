<?php

namespace App\Controllers;

use App\Models\CourseModel;
use App\Models\PostModel;

class Home extends BaseController
{
    public function index(): string
    {
        return view('home/index', [
            'pageTitle' => 'Membley Security Group | Security & Professional Training',
            'metaDescription' => 'Discover Membley Security Group, its professional training and security services.',
            'preloadImage' => 'assets/images/security-technology.jpg',
            'featuredCourses' => (new CourseModel())->findFeaturedPublicCourses(),
            'latestPosts' => (new PostModel())->findLatestPublicPosts(),
        ]);
    }
}
