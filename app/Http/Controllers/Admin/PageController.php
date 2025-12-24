<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pages = Page::orderBy('order')->orderBy('title')->get();
        return view('admin.pages.index', compact('pages'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.pages.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'slug' => 'required|string|unique:pages,slug|max:255',
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'slider_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:255',
            'page_type' => 'required|in:page,section,footer_link',
            'is_active' => 'boolean',
            'order' => 'integer',
        ]);

        $data = [
            'slug' => $request->slug,
            'title' => $request->title,
            'content' => $request->content,
            'meta_description' => $request->meta_description,
            'meta_keywords' => $request->meta_keywords,
            'page_type' => $request->page_type,
            'is_active' => $request->has('is_active'),
            'order' => $request->order ?? 0,
        ];

        // Handle featured image upload
        if ($request->hasFile('featured_image')) {
            $data['featured_image'] = $request->file('featured_image')->store('pages/featured', 'public');
        }

        // Handle slider images upload
        if ($request->hasFile('slider_images')) {
            $sliderImages = [];
            foreach ($request->file('slider_images') as $image) {
                $sliderImages[] = $image->store('pages/sliders', 'public');
            }
            $data['slider_images'] = $sliderImages;
        }

        Page::create($data);

        return redirect()->route('admin.pages.index')
            ->with('success', 'Page created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $page = Page::findOrFail($id);
        return view('admin.pages.show', compact('page'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $page = Page::findOrFail($id);
        return view('admin.pages.edit', compact('page'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $page = Page::findOrFail($id);

        $request->validate([
            'slug' => 'required|string|unique:pages,slug,' . $id . '|max:255',
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'slider_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'remove_featured_image' => 'boolean',
            'remove_slider_images' => 'nullable|array',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:255',
            'page_type' => 'required|in:page,section,footer_link',
            'is_active' => 'boolean',
            'order' => 'integer',
            'contact_address' => 'nullable|string|max:500',
            'contact_address_line2' => 'nullable|string|max:500',
            'contact_phone' => 'nullable|string|max:50',
            'contact_email' => 'nullable|email|max:255',
            'contact_working_hours' => 'nullable|string|max:255',
        ]);

        $data = [
            'slug' => $request->slug,
            'title' => $request->title,
            'content' => $request->content,
            'meta_description' => $request->meta_description,
            'meta_keywords' => $request->meta_keywords,
            'page_type' => $request->page_type,
            'is_active' => $request->has('is_active'),
            'order' => $request->order ?? 0,
        ];

        // Handle contact details if this is the contact page
        if ($request->slug === 'contact' || $page->slug === 'contact') {
            $data['contact_details'] = [
                'address' => $request->contact_address ?? '',
                'address_line2' => $request->contact_address_line2 ?? '',
                'phone' => $request->contact_phone ?? '',
                'email' => $request->contact_email ?? '',
                'working_hours' => $request->contact_working_hours ?? '',
            ];
        }

        // Handle home page sections
        if ($request->slug === 'home' || $page->slug === 'home') {
            $sections = [];
            
            // Hero section
            if ($request->has('sections.hero')) {
                $sections['hero'] = $request->input('sections.hero');
            }
            
            // Vulnerability section
            if ($request->has('sections.vulnerability')) {
                $sections['vulnerability'] = $request->input('sections.vulnerability');
            }
            
            // Features section
            if ($request->has('sections.features')) {
                $sections['features'] = array_values($request->input('sections.features', []));
            }
            
            // About section
            if ($request->has('sections.about')) {
                $sections['about'] = $request->input('sections.about');
            }
            
            // Why section
            if ($request->has('sections.why')) {
                $whyData = $request->input('sections.why');
                if (isset($whyData['items'])) {
                    $whyData['items'] = array_values($whyData['items']);
                }
                $sections['why'] = $whyData;
            }
            
            // Stats section
            if ($request->has('sections.stats')) {
                $statsData = $request->input('sections.stats');
                if (isset($statsData['items'])) {
                    $statsData['items'] = array_values($statsData['items']);
                }
                $sections['stats'] = $statsData;
            }
            
            // Services section
            if ($request->has('sections.services')) {
                $servicesData = $request->input('sections.services');
                if (isset($servicesData['items'])) {
                    $servicesData['items'] = array_values($servicesData['items']);
                }
                $sections['services'] = $servicesData;
            }
            
            // Newsletter section
            if ($request->has('sections.newsletter')) {
                $sections['newsletter'] = $request->input('sections.newsletter');
            }
            
            $data['sections'] = $sections;
        }

        // Handle featured image upload/removal
        if ($request->has('remove_featured_image') && $request->remove_featured_image) {
            if ($page->featured_image) {
                Storage::disk('public')->delete($page->featured_image);
            }
            $data['featured_image'] = null;
        } elseif ($request->hasFile('featured_image')) {
            // Delete old image
            if ($page->featured_image) {
                Storage::disk('public')->delete($page->featured_image);
            }
            $data['featured_image'] = $request->file('featured_image')->store('pages/featured', 'public');
        }

        // Handle hero slides for home page
        if ($request->slug === 'home' || $page->slug === 'home') {
            $heroSlides = [];
            
            if ($request->has('hero_slides') && is_array($request->hero_slides)) {
                foreach ($request->hero_slides as $index => $slideData) {
                    $slide = [
                        'title' => $slideData['title'] ?? '',
                        'subtitle' => $slideData['subtitle'] ?? '',
                        'primary_button_text' => $slideData['primary_button_text'] ?? 'Get a quote',
                        'primary_button_link' => $slideData['primary_button_link'] ?? route('contact'),
                        'secondary_button_text' => $slideData['secondary_button_text'] ?? 'Contact us',
                        'secondary_button_link' => $slideData['secondary_button_link'] ?? route('contact'),
                        'duration' => isset($slideData['duration']) ? max(1, min(30, (int)$slideData['duration'])) : 5,
                    ];
                    
                    // Handle image upload/removal for this slide
                    if (isset($slideData['remove_image']) && $slideData['remove_image']) {
                        // Remove image if exists
                        if (!empty($slideData['old_image'])) {
                            Storage::disk('public')->delete($slideData['old_image']);
                        }
                        $slide['image'] = '';
                    } elseif ($request->hasFile("hero_slides.{$index}.image")) {
                        // Delete old image if exists
                        if (!empty($slideData['old_image'])) {
                            Storage::disk('public')->delete($slideData['old_image']);
                        }
                        // Upload new image
                        $image = $request->file("hero_slides.{$index}.image");
                        $slide['image'] = $image->store('pages/sliders', 'public');
                    } else {
                        // Keep existing image if available
                        $slide['image'] = $slideData['old_image'] ?? '';
                    }
                    
                    // Only add slide if it has at least an image or title
                    if (!empty($slide['image']) || !empty($slide['title'])) {
                        $heroSlides[] = $slide;
                    }
                }
            }
            
            $data['hero_slides'] = !empty($heroSlides) ? $heroSlides : null;
        }

        $page->update($data);

        return redirect()->route('admin.pages.index')
            ->with('success', 'Page updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $page = Page::findOrFail($id);
        $page->delete();

        return redirect()->route('admin.pages.index')
            ->with('success', 'Page deleted successfully!');
    }
}
