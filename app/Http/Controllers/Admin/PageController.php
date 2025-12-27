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
            'site_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'favicon' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,ico|max:1024',
            'header_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'remove_site_logo' => 'boolean',
            'remove_favicon' => 'boolean',
            'remove_header_logo' => 'boolean',
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

        // Handle sections (for home page and other pages with sections)
        $existingSections = $page->sections ?? [];
        if (is_string($existingSections)) {
            $existingSections = json_decode($existingSections, true) ?? [];
        }
        if (!is_array($existingSections)) {
            $existingSections = [];
        }
        
        // Get new sections from request
        $newSections = $request->input('sections', []);
        
        // Handle site settings file uploads
        if ($request->hasFile('site_logo')) {
            if (!isset($newSections['site_settings'])) {
                $newSections['site_settings'] = $existingSections['site_settings'] ?? [];
            }
            if (isset($existingSections['site_settings']['site_logo'])) {
                Storage::disk('public')->delete($existingSections['site_settings']['site_logo']);
            }
            $newSections['site_settings']['site_logo'] = $request->file('site_logo')->store('site', 'public');
        } elseif ($request->has('remove_site_logo') && $request->remove_site_logo) {
            if (isset($existingSections['site_settings']['site_logo'])) {
                Storage::disk('public')->delete($existingSections['site_settings']['site_logo']);
                if (!isset($newSections['site_settings'])) {
                    $newSections['site_settings'] = $existingSections['site_settings'];
                }
                unset($newSections['site_settings']['site_logo']);
            }
        }
        
        if ($request->hasFile('favicon')) {
            if (!isset($newSections['site_settings'])) {
                $newSections['site_settings'] = $existingSections['site_settings'] ?? [];
            }
            if (isset($existingSections['site_settings']['favicon'])) {
                Storage::disk('public')->delete($existingSections['site_settings']['favicon']);
            }
            $newSections['site_settings']['favicon'] = $request->file('favicon')->store('site', 'public');
        } elseif ($request->has('remove_favicon') && $request->remove_favicon) {
            if (isset($existingSections['site_settings']['favicon'])) {
                Storage::disk('public')->delete($existingSections['site_settings']['favicon']);
                if (!isset($newSections['site_settings'])) {
                    $newSections['site_settings'] = $existingSections['site_settings'];
                }
                unset($newSections['site_settings']['favicon']);
            }
        }
        
        // Handle header logo upload
        if ($request->hasFile('header_logo')) {
            if (!isset($newSections['header'])) {
                $newSections['header'] = $existingSections['header'] ?? [];
            }
            if (isset($existingSections['header']['logo'])) {
                Storage::disk('public')->delete($existingSections['header']['logo']);
            }
            $newSections['header']['logo'] = $request->file('header_logo')->store('header', 'public');
        } elseif ($request->has('remove_header_logo') && $request->remove_header_logo) {
            if (isset($existingSections['header']['logo'])) {
                Storage::disk('public')->delete($existingSections['header']['logo']);
                if (!isset($newSections['header'])) {
                    $newSections['header'] = $existingSections['header'];
                }
                unset($newSections['header']['logo']);
            }
        }
        
        // Merge sections: preserve existing site_settings and header, then merge with new data
        $mergedSections = $existingSections;
        
        // Merge site_settings
        if (isset($newSections['site_settings'])) {
            $mergedSections['site_settings'] = array_merge(
                $existingSections['site_settings'] ?? [],
                $newSections['site_settings']
            );
        }
        
        // Merge header
        if (isset($newSections['header'])) {
            $mergedSections['header'] = array_merge(
                $existingSections['header'] ?? [],
                $newSections['header']
            );
        }
        
        // Merge all other sections
        foreach ($newSections as $key => $value) {
            if ($key !== 'site_settings' && $key !== 'header') {
                $mergedSections[$key] = $value;
            }
        }
        
        if (!empty($mergedSections)) {
            $data['sections'] = $mergedSections;
        }

        // Handle contact_details (for contact page)
        if ($request->has('contact_address') || $request->has('contact_phone') || $request->has('contact_email')) {
            $contactDetails = [
                'address' => $request->input('contact_address', $page->contact_details['address'] ?? ''),
                'address_line2' => $request->input('contact_address_line2', $page->contact_details['address_line2'] ?? ''),
                'phone' => $request->input('contact_phone', $page->contact_details['phone'] ?? ''),
                'email' => $request->input('contact_email', $page->contact_details['email'] ?? ''),
                'working_hours' => $request->input('contact_working_hours', $page->contact_details['working_hours'] ?? ''),
            ];
            $data['contact_details'] = $contactDetails;
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
