<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Course;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $courses = [
            [
                'title' => 'BUSINESS COMMUNICATION & DIPLOMACY',
                'code' => 'BCD',
                'description' => 'Learn effective business communication strategies and diplomatic skills for professional success.',
                'duration_hours' => 1, // 60 mins
                'status' => 'Active',
            ],
            [
                'title' => 'CERTIFIED ADMIN OFFICER',
                'code' => 'CAO',
                'description' => 'Comprehensive training for administrative officers covering office management and administrative procedures.',
                'duration_hours' => 1, // 60 mins
                'status' => 'Active',
            ],
            [
                'title' => 'Customer Service & Relationship Management',
                'code' => 'CSR',
                'description' => 'Master customer service excellence and build lasting customer relationships.',
                'duration_hours' => 1.5, // 90 mins
                'status' => 'Active',
            ],
            [
                'title' => 'DIGITAL MARKETING',
                'code' => 'DMK',
                'description' => 'Comprehensive digital marketing strategies including social media, SEO, and online advertising.',
                'duration_hours' => 1.5, // 90 mins
                'status' => 'Active',
            ],
            [
                'title' => 'ENTREPRENEURSHIP & BUSINESS MANAGEMENT',
                'code' => 'EBM',
                'description' => 'Essential skills for starting and managing a successful business venture.',
                'duration_hours' => 1, // 60 mins
                'status' => 'Active',
            ],
            [
                'title' => 'WORKPLACE EFFECTIVENESS & EFFICIENCY',
                'code' => 'EWM',
                'description' => 'Enhance workplace productivity through effective time management and efficiency strategies.',
                'duration_hours' => 1.5, // 90 mins
                'status' => 'Active',
            ],
            [
                'title' => 'HUMAN RESOURCE MANAGEMENT',
                'code' => 'HRM',
                'description' => 'Complete guide to human resource management including recruitment, training, and employee relations.',
                'duration_hours' => 1.5, // 90 mins
                'status' => 'Active',
            ],
            [
                'title' => 'Health, Safety and Environment',
                'code' => 'HSE',
                'description' => 'Comprehensive health, safety, and environmental management training for workplace safety.',
                'duration_hours' => 1.5, // 90 mins
                'status' => 'Active',
            ],
            [
                'title' => 'LEADING & SUPERVISING PEOPLE AT WORK',
                'code' => 'LSP',
                'description' => 'Develop leadership and supervisory skills to effectively manage teams and drive performance.',
                'duration_hours' => 0.83, // 50 mins (rounded to 0.83 hours)
                'status' => 'Active',
            ],
            [
                'title' => 'ORGANIZATIONAL MANAGEMENT',
                'code' => 'OGM',
                'description' => 'Strategic organizational management principles and practices for effective business operations.',
                'duration_hours' => 1.5, // 90 mins
                'status' => 'Active',
            ],
            [
                'title' => 'Project Management Course',
                'code' => 'PMC',
                'description' => 'Comprehensive project management training covering planning, execution, and project delivery.',
                'duration_hours' => 2.5, // 150 mins
                'status' => 'Active',
            ],
            [
                'title' => 'PROJECT MANAGEMENT',
                'code' => 'PMG',
                'description' => 'Essential project management skills and methodologies for successful project execution.',
                'duration_hours' => 1.5, // 90 mins
                'status' => 'Active',
            ],
            [
                'title' => 'LEVEL III - HEALTH SAFETY & ENVIRONMENT',
                'code' => 'SHE',
                'description' => 'Advanced level health, safety, and environment management certification program.',
                'duration_hours' => 1.5, // 90 mins
                'status' => 'Active',
            ],
        ];

        foreach ($courses as $course) {
            Course::updateOrCreate(
                ['code' => $course['code']], // Use code as unique identifier
                $course
            );
        }

        $this->command->info('Courses seeded successfully!');
    }
}
