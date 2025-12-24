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

        // Handle slider images upload/removal
        $currentSliderImages = $page->slider_images ?? [];
        
        // Remove selected slider images
        if ($request->has('remove_slider_images') && is_array($request->remove_slider_images)) {
            foreach ($request->remove_slider_images as $imagePath) {
                if (in_array($imagePath, $currentSliderImages)) {
                    Storage::disk('public')->delete($imagePath);
                    $currentSliderImages = array_values(array_diff($currentSliderImages, [$imagePath]));
                }
            }
        }

        // Add new slider images
        if ($request->hasFile('slider_images')) {
            foreach ($request->file('slider_images') as $image) {
                $currentSliderImages[] = $image->store('pages/sliders', 'public');
            }
        }

        $data['slider_images'] = !empty($currentSliderImages) ? $currentSliderImages : null;

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
