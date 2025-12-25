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
        } elseif ($request->slug === 'partners' || $page->slug === 'partners') {
            // Handle partners page sections
            $sections = [];
            
            if ($request->has('sections.header_title')) {
                $sections['header_title'] = $request->input('sections.header_title');
            }
            if ($request->has('sections.header_subtitle')) {
                $sections['header_subtitle'] = $request->input('sections.header_subtitle');
            }
            if ($request->has('sections.partners_title')) {
                $sections['partners_title'] = $request->input('sections.partners_title');
            }
            if ($request->has('sections.become_partner_title')) {
                $sections['become_partner_title'] = $request->input('sections.become_partner_title');
            }
            if ($request->has('sections.become_partner_description')) {
                $sections['become_partner_description'] = $request->input('sections.become_partner_description');
            }
            if ($request->has('sections.become_partner_button_text')) {
                $sections['become_partner_button_text'] = $request->input('sections.become_partner_button_text');
            }
            if ($request->has('sections.benefits')) {
                $sections['benefits'] = array_values($request->input('sections.benefits', []));
            }
            if ($request->has('sections.partnership_info')) {
                $sections['partnership_info'] = $request->input('sections.partnership_info');
            }
            
            // Handle partner logos
            if ($request->has('partner_logos') && is_array($request->partner_logos)) {
                $partnerLogos = [];
                foreach ($request->partner_logos as $index => $logoData) {
                    $logo = [
                        'name' => $logoData['name'] ?? '',
                    ];
                    
                    // Handle image upload/removal
                    if ($request->hasFile("partner_logos.{$index}.image")) {
                        // Delete old image if exists
                        if (!empty($logoData['existing_image'])) {
                            Storage::disk('public')->delete($logoData['existing_image']);
                        }
                        $logo['image'] = $request->file("partner_logos.{$index}.image")->store('partners/logos', 'public');
                    } elseif (!empty($logoData['remove_image']) && $logoData['remove_image']) {
                        // Remove image if requested
                        if (!empty($logoData['existing_image'])) {
                            Storage::disk('public')->delete($logoData['existing_image']);
                        }
                        $logo['image'] = null;
                    } else {
                        // Keep existing image
                        $logo['image'] = $logoData['existing_image'] ?? null;
                    }
                    
                    // Only add logo if it has an image
                    if (!empty($logo['image'])) {
                        $partnerLogos[] = $logo;
                    }
                }
                $sections['partner_logos'] = $partnerLogos;
            }
            
            $data['sections'] = $sections;
        }

        // Handle header settings (always save to home page for global settings)
        if ($request->has('header')) {
            $homePage = Page::where('slug', 'home')->first();
            
            if ($homePage) {
                $currentSections = $homePage->sections ?? [];
                if (is_string($currentSections)) {
                    $currentSections = json_decode($currentSections, true) ?? [];
                }
                if (!is_array($currentSections)) {
                    $currentSections = [];
                }
                
                $headerSettings = [];
                
                // Handle logo upload/removal
                if ($request->has('header.remove_logo') && $request->input('header.remove_logo')) {
                    if (!empty($request->input('header.existing_logo'))) {
                        Storage::disk('public')->delete($request->input('header.existing_logo'));
                    }
                    $headerSettings['logo'] = null;
                } elseif ($request->hasFile('header.logo')) {
                    // Delete old logo if exists
                    $oldLogo = $currentSections['header']['logo'] ?? $request->input('header.existing_logo');
                    if (!empty($oldLogo)) {
                        Storage::disk('public')->delete($oldLogo);
                    }
                    $headerSettings['logo'] = $request->file('header.logo')->store('header', 'public');
                } elseif (!empty($request->input('header.existing_logo'))) {
                    // Keep existing logo
                    $headerSettings['logo'] = $request->input('header.existing_logo');
                } elseif (isset($currentSections['header']['logo'])) {
                    // Keep existing logo from home page
                    $headerSettings['logo'] = $currentSections['header']['logo'];
                }
                
                // Handle brand name
                if ($request->has('header.brand_name')) {
                    $headerSettings['brand_name'] = $request->input('header.brand_name');
                } elseif (isset($currentSections['header']['brand_name'])) {
                    $headerSettings['brand_name'] = $currentSections['header']['brand_name'];
                }
                
                // Handle menu items
                if ($request->has('header.menu_items') && is_array($request->input('header.menu_items'))) {
                    $menuItems = [];
                    foreach ($request->input('header.menu_items') as $item) {
                        if (!empty($item['label']) && !empty($item['url'])) {
                            $menuItems[] = [
                                'label' => $item['label'],
                                'url' => $item['url'],
                                'order' => isset($item['order']) ? (int)$item['order'] : 999,
                            ];
                        }
                    }
                    // Sort by order
                    usort($menuItems, function($a, $b) {
                        return $a['order'] <=> $b['order'];
                    });
                    $headerSettings['menu_items'] = $menuItems;
                } elseif (isset($currentSections['header']['menu_items'])) {
                    $headerSettings['menu_items'] = $currentSections['header']['menu_items'];
                }
                
                $currentSections['header'] = $headerSettings;
                
                // Update home page with header settings
                $homePage->sections = $currentSections;
                $homePage->save();
            }
        }

        // Handle basic site settings (always save to home page for global settings)
        if ($request->has('site_settings')) {
            $homePage = Page::where('slug', 'home')->first();
            
            if ($homePage) {
                $currentSections = $homePage->sections ?? [];
                if (is_string($currentSections)) {
                    $currentSections = json_decode($currentSections, true) ?? [];
                }
                if (!is_array($currentSections)) {
                    $currentSections = [];
                }
                
                $siteSettings = [];
                
                // Handle site logo upload/removal
                if ($request->has('site_settings.remove_logo') && $request->input('site_settings.remove_logo')) {
                    if (!empty($request->input('site_settings.existing_logo'))) {
                        Storage::disk('public')->delete($request->input('site_settings.existing_logo'));
                    }
                    $siteSettings['logo'] = null;
                } elseif ($request->hasFile('site_settings.logo')) {
                    // Delete old logo if exists
                    $oldLogo = $currentSections['site_settings']['logo'] ?? $request->input('site_settings.existing_logo');
                    if (!empty($oldLogo)) {
                        Storage::disk('public')->delete($oldLogo);
                    }
                    $siteSettings['logo'] = $request->file('site_settings.logo')->store('site', 'public');
                } elseif (!empty($request->input('site_settings.existing_logo'))) {
                    // Keep existing logo
                    $siteSettings['logo'] = $request->input('site_settings.existing_logo');
                } elseif (isset($currentSections['site_settings']['logo'])) {
                    // Keep existing logo from home page
                    $siteSettings['logo'] = $currentSections['site_settings']['logo'];
                }
                
                // Handle favicon upload/removal
                if ($request->has('site_settings.remove_favicon') && $request->input('site_settings.remove_favicon')) {
                    if (!empty($request->input('site_settings.existing_favicon'))) {
                        Storage::disk('public')->delete($request->input('site_settings.existing_favicon'));
                    }
                    $siteSettings['favicon'] = null;
                } elseif ($request->hasFile('site_settings.favicon')) {
                    // Delete old favicon if exists
                    $oldFavicon = $currentSections['site_settings']['favicon'] ?? $request->input('site_settings.existing_favicon');
                    if (!empty($oldFavicon)) {
                        Storage::disk('public')->delete($oldFavicon);
                    }
                    $siteSettings['favicon'] = $request->file('site_settings.favicon')->store('site', 'public');
                } elseif (!empty($request->input('site_settings.existing_favicon'))) {
                    // Keep existing favicon
                    $siteSettings['favicon'] = $request->input('site_settings.existing_favicon');
                } elseif (isset($currentSections['site_settings']['favicon'])) {
                    // Keep existing favicon from home page
                    $siteSettings['favicon'] = $currentSections['site_settings']['favicon'];
                }
                
                // Handle other site settings
                if ($request->has('site_settings.site_name')) {
                    $siteSettings['site_name'] = $request->input('site_settings.site_name');
                } elseif (isset($currentSections['site_settings']['site_name'])) {
                    $siteSettings['site_name'] = $currentSections['site_settings']['site_name'];
                }
                
                if ($request->has('site_settings.site_tagline')) {
                    $siteSettings['site_tagline'] = $request->input('site_settings.site_tagline');
                } elseif (isset($currentSections['site_settings']['site_tagline'])) {
                    $siteSettings['site_tagline'] = $currentSections['site_settings']['site_tagline'];
                }
                
                if ($request->has('site_settings.contact_email')) {
                    $siteSettings['contact_email'] = $request->input('site_settings.contact_email');
                } elseif (isset($currentSections['site_settings']['contact_email'])) {
                    $siteSettings['contact_email'] = $currentSections['site_settings']['contact_email'];
                }
                
                if ($request->has('site_settings.contact_phone')) {
                    $siteSettings['contact_phone'] = $request->input('site_settings.contact_phone');
                } elseif (isset($currentSections['site_settings']['contact_phone'])) {
                    $siteSettings['contact_phone'] = $currentSections['site_settings']['contact_phone'];
                }
                
                // Handle social links
                if ($request->has('site_settings.social_links') && is_array($request->input('site_settings.social_links'))) {
                    $siteSettings['social_links'] = $request->input('site_settings.social_links');
                } elseif (isset($currentSections['site_settings']['social_links'])) {
                    $siteSettings['social_links'] = $currentSections['site_settings']['social_links'];
                }
                
                $currentSections['site_settings'] = $siteSettings;
                
                // Update home page with site settings
                $homePage->sections = $currentSections;
                $homePage->save();
            }
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
