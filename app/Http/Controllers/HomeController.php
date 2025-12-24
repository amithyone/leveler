<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Models\Course;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // Get home page - don't filter by is_active for home page
        $page = Page::where('slug', 'home')->first();
        
        // If page doesn't exist, create a default one
        if (!$page) {
            $page = new Page([
                'title' => 'Home',
                'content' => '',
                'slug' => 'home',
                'slider_images' => [],
            ]);
        } else {
            // Refresh the page to get latest data
            $page->refresh();
            
            // Ensure hero_slides is properly cast as array
            if ($page->hero_slides) {
                if (is_string($page->hero_slides)) {
                    $page->hero_slides = json_decode($page->hero_slides, true) ?? [];
                }
                if (!is_array($page->hero_slides)) {
                    $page->hero_slides = [];
                }
            } else {
                $page->hero_slides = [];
            }
            
            // Ensure sections is properly cast as array
            if ($page->sections) {
                if (is_string($page->sections)) {
                    $page->sections = json_decode($page->sections, true) ?? [];
                }
                // Ensure it's an array
                if (!is_array($page->sections)) {
                    $page->sections = [];
                }
            } else {
                $page->sections = [];
            }
        }
        
        $courses = Course::where('status', 'Active')->take(6)->get();
        return view('frontend.home', compact('page', 'courses'));
    }

    public function about()
    {
        $page = Page::findBySlug('about') ?? new Page(['title' => 'About Us', 'content' => '']);
        return view('frontend.about', compact('page'));
    }

    public function services()
    {
        $page = Page::findBySlug('services') ?? new Page(['title' => 'Our Services', 'content' => '']);
        return view('frontend.services', compact('page'));
    }

    public function partners()
    {
        $page = Page::findBySlug('partners') ?? new Page(['title' => 'Partners', 'content' => '']);
        $partners = \App\Models\Partner::active()->ordered()->get();
        return view('frontend.partners', compact('page', 'partners'));
    }

    public function tipsUpdates()
    {
        $page = Page::findBySlug('tips-updates') ?? new Page(['title' => 'Tips & Updates', 'content' => '']);
        return view('frontend.tips-updates', compact('page'));
    }

    public function contact()
    {
        $page = Page::findBySlug('contact') ?? new Page(['title' => 'Contact Us', 'content' => '']);
        return view('frontend.contact', compact('page'));
    }

    public function faqs()
    {
        $page = Page::findBySlug('faqs') ?? new Page(['title' => 'FAQs', 'content' => '']);
        return view('frontend.faqs', compact('page'));
    }

    public function careers()
    {
        $page = Page::findBySlug('careers') ?? new Page(['title' => 'Careers', 'content' => '']);
        return view('frontend.careers', compact('page'));
    }

    public function courses()
    {
        $page = Page::findBySlug('courses') ?? new Page(['title' => 'Courses', 'content' => '']);
        $courses = Course::where('status', 'Active')
            ->withCount('questionPools')
            ->orderBy('title')
            ->get();
        return view('frontend.courses', compact('page', 'courses'));
    }

    public function courseDetails($id)
    {
        $course = Course::withCount('questionPools')
            ->with('questionPools')
            ->findOrFail($id);
        
        return view('frontend.course-details', compact('course'));
    }

    public function eLearning()
    {
        $page = Page::findBySlug('e-learning') ?? new Page(['title' => 'e-Learning', 'content' => '']);
        return view('frontend.e-learning', compact('page'));
    }

    public function register()
    {
        $page = Page::findBySlug('register') ?? new Page(['title' => 'Register For Course', 'content' => '']);
        $courses = Course::where('status', 'Active')->orderBy('title')->get();
        return view('frontend.register', compact('page', 'courses'));
    }

    public function news()
    {
        $page = Page::findBySlug('news') ?? new Page(['title' => 'News', 'content' => '']);
        return view('frontend.news', compact('page'));
    }

    public function terms()
    {
        $page = Page::findBySlug('terms-of-use') ?? new Page(['title' => 'Terms of Use', 'content' => '']);
        return view('frontend.terms', compact('page'));
    }

    public function privacy()
    {
        $page = Page::findBySlug('privacy-policy') ?? new Page(['title' => 'Privacy Policy', 'content' => '']);
        return view('frontend.privacy', compact('page'));
    }

    public function legal()
    {
        $page = Page::findBySlug('legal') ?? new Page(['title' => 'Legal', 'content' => '']);
        return view('frontend.legal', compact('page'));
    }

    public function showPage($slug)
    {
        $page = Page::findBySlug($slug);
        
        if (!$page) {
            abort(404);
        }

        return view('frontend.page', compact('page'));
    }
}

