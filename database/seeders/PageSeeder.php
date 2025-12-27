<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Page;

class PageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pages = [
            [
                'slug' => 'home',
                'title' => 'Home',
                'content' => 'Welcome to Leveler - A Human Capacity Development Company',
                'meta_description' => 'Leveler is a development and management consulting company providing business advisory, training & development, and recruitment services.',
                'meta_keywords' => 'leveler, business consulting, training, development, recruitment',
                'page_type' => 'page',
                'is_active' => true,
                'order' => 1,
            ],
            [
                'slug' => 'about',
                'title' => 'About Us',
                'content' => "Leveler means Competence\n\nFor over 10 years, we have supported businesses to accelerate growth.\n\nLeveler is a Business & Management consulting company whose mandate is to aid growth and sustainability of businesses through strategy development.\n\nOur reputation is built on the foundation of providing business and management solutions that deliver growth, increase profit and boost efficiency.",
                'meta_description' => 'Learn about Leveler, a business and management consulting company with over 10 years of experience helping businesses grow.',
                'meta_keywords' => 'about leveler, business consulting, management consulting',
                'page_type' => 'page',
                'is_active' => true,
                'order' => 2,
            ],
            [
                'slug' => 'services',
                'title' => 'Our Services',
                'content' => 'We offer comprehensive business solutions including Business Advisory, Training & Development, and Recruitment & Selection services.',
                'meta_description' => 'Leveler offers business advisory, training & development, and recruitment services to help your business grow.',
                'meta_keywords' => 'business services, training, recruitment, business advisory',
                'page_type' => 'page',
                'is_active' => true,
                'order' => 3,
            ],
            [
                'slug' => 'partners',
                'title' => 'Partners',
                'content' => 'We are proud to collaborate with leading organizations and institutions to deliver exceptional training and development services.',
                'meta_description' => 'Learn about Leveler\'s partners and collaborations.',
                'meta_keywords' => 'partners, collaborations, business partners',
                'page_type' => 'page',
                'is_active' => true,
                'order' => 4,
            ],
            [
                'slug' => 'tips-updates',
                'title' => 'Tips & Updates',
                'content' => 'Stay updated with the latest tips, industry insights, and professional development advice from our experts.',
                'meta_description' => 'Get the latest tips and updates on business development and professional growth.',
                'meta_keywords' => 'tips, updates, business tips, professional development',
                'page_type' => 'page',
                'is_active' => true,
                'order' => 5,
            ],
            [
                'slug' => 'contact',
                'title' => 'Contact Us',
                'content' => 'Get in touch with us for business inquiries, training programs, or recruitment services.',
                'meta_description' => 'Contact Leveler for business advisory, training, and recruitment services.',
                'meta_keywords' => 'contact, get in touch, business inquiry',
                'page_type' => 'page',
                'is_active' => true,
                'order' => 6,
            ],
            [
                'slug' => 'faqs',
                'title' => 'Frequently Asked Questions',
                'content' => null, // Will use default content from view
                'meta_description' => 'Frequently asked questions about Leveler courses, registration, payments, and certificates.',
                'meta_keywords' => 'faq, frequently asked questions, help, support',
                'page_type' => 'page',
                'is_active' => true,
                'order' => 7,
            ],
            [
                'slug' => 'careers',
                'title' => 'Careers',
                'content' => null, // Will use default content from view
                'meta_description' => 'Join the Leveler team and make an impact in professional development.',
                'meta_keywords' => 'careers, jobs, employment, join team',
                'page_type' => 'page',
                'is_active' => true,
                'order' => 8,
            ],
            [
                'slug' => 'courses',
                'title' => 'Our Courses',
                'content' => 'Professional development courses designed to enhance your skills and advance your career.',
                'meta_description' => 'Browse our professional development courses including Business Communication, Project Management, and more.',
                'meta_keywords' => 'courses, training courses, professional development, online courses',
                'page_type' => 'page',
                'is_active' => true,
                'order' => 9,
            ],
            [
                'slug' => 'e-learning',
                'title' => 'e-Learning Platform',
                'content' => null, // Will use default content from view
                'meta_description' => 'Access professional development courses from anywhere with our e-learning platform.',
                'meta_keywords' => 'e-learning, online learning, elearning platform',
                'page_type' => 'page',
                'is_active' => true,
                'order' => 10,
            ],
            [
                'slug' => 'register',
                'title' => 'Register For Course',
                'content' => 'Register for our professional development courses. Choose from single course or 4-course package.',
                'meta_description' => 'Register for Leveler professional development courses.',
                'meta_keywords' => 'register, course registration, sign up',
                'page_type' => 'page',
                'is_active' => true,
                'order' => 11,
            ],
            [
                'slug' => 'news',
                'title' => 'News & Updates',
                'content' => null, // Will use default content from view
                'meta_description' => 'Stay informed about the latest developments, course announcements, and industry insights from Leveler.',
                'meta_keywords' => 'news, updates, announcements, industry news',
                'page_type' => 'page',
                'is_active' => true,
                'order' => 12,
            ],
            [
                'slug' => 'terms-of-use',
                'title' => 'Terms of Use',
                'content' => null, // Will use default content from view
                'meta_description' => 'Terms and conditions for using Leveler services and e-learning platform.',
                'meta_keywords' => 'terms, terms of use, conditions, legal',
                'page_type' => 'footer_link',
                'is_active' => true,
                'order' => 13,
            ],
            [
                'slug' => 'privacy-policy',
                'title' => 'Privacy Policy',
                'content' => null, // Will use default content from view
                'meta_description' => 'Privacy policy explaining how Leveler collects and uses your personal information.',
                'meta_keywords' => 'privacy, privacy policy, data protection',
                'page_type' => 'footer_link',
                'is_active' => true,
                'order' => 14,
            ],
            [
                'slug' => 'legal',
                'title' => 'Legal Information',
                'content' => null, // Will use default content from view
                'meta_description' => 'Legal information about Leveler including company details and intellectual property.',
                'meta_keywords' => 'legal, company information, legal details',
                'page_type' => 'footer_link',
                'is_active' => true,
                'order' => 15,
            ],
        ];

        foreach ($pages as $pageData) {
            $existingPage = Page::where('slug', $pageData['slug'])->first();
            
            if ($existingPage) {
                // Preserve existing sections, header, site_settings, and contact_details
                $preservedData = [];
                
                // Preserve sections (especially for home page)
                if ($existingPage->sections && !empty($existingPage->sections)) {
                    $preservedData['sections'] = $existingPage->sections;
                }
                
                // Preserve contact_details (for contact page)
                if ($existingPage->contact_details && !empty($existingPage->contact_details)) {
                    $preservedData['contact_details'] = $existingPage->contact_details;
                }
                
                // Merge preserved data with new page data
                $updateData = array_merge($pageData, $preservedData);
                
                $existingPage->update($updateData);
            } else {
                // Create new page
                Page::create($pageData);
            }
        }

        $this->command->info('Pages seeded successfully!');
        $this->command->info('Total pages created: ' . count($pages));
    }
}
