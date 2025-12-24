<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Course;

class GenerateCourseOverviews extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'courses:generate-overviews';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate overviews for courses that don\'t have them';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $courses = Course::whereNull('overview')
            ->orWhere('overview', '')
            ->get();
        
        if ($courses->isEmpty()) {
            $this->info('All courses already have overviews!');
            return 0;
        }
        
        $this->info("Found {$courses->count()} courses without overviews.");
        
        $overviews = $this->getCourseOverviews();
        $updated = 0;
        
        foreach ($courses as $course) {
            $overview = $this->getOverviewForCourse($course, $overviews);
            
            if ($overview) {
                $course->update(['overview' => $overview]);
                $updated++;
                $this->info("✓ Generated overview for: {$course->title}");
            } else {
                $this->warn("✗ Could not generate overview for: {$course->title}");
            }
        }
        
        $this->info("\n✅ Successfully generated {$updated} course overviews.");
        
        return 0;
    }

    /**
     * Get overview for a specific course
     */
    private function getOverviewForCourse($course, $overviews)
    {
        // Try exact match first
        if (isset($overviews[$course->code])) {
            return $overviews[$course->code];
        }
        
        // Try by title
        $titleKey = strtoupper($course->title);
        if (isset($overviews[$titleKey])) {
            return $overviews[$titleKey];
        }
        
        // Try partial match
        foreach ($overviews as $key => $overview) {
            if (stripos($course->title, $key) !== false || 
                stripos($key, $course->title) !== false) {
                return $overview;
            }
        }
        
        // Generate generic overview from description
        return $this->generateGenericOverview($course);
    }

    /**
     * Generate a generic overview from course description
     */
    private function generateGenericOverview($course)
    {
        $description = $course->description ?? '';
        
        return "This comprehensive course provides essential knowledge and practical skills in {$course->title}. " .
               "Through structured learning modules and hands-on exercises, participants will gain the expertise needed " .
               "to excel in their professional roles. {$description} " .
               "The course is designed to be practical, engaging, and immediately applicable to real-world scenarios, " .
               "ensuring that participants can implement what they learn right away.";
    }

    /**
     * Get predefined course overviews
     */
    private function getCourseOverviews()
    {
        return [
            'BCD' => "Business Communication & Diplomacy is a critical skill set in today's interconnected business world. " .
                     "Effective communication and diplomatic acumen are essential for building strong professional relationships, " .
                     "negotiating successfully, and navigating complex organizational dynamics. This course provides participants " .
                     "with the tools and techniques needed to communicate clearly, persuasively, and diplomatically in various business contexts.\n\n" .
                     "Participants will learn how to craft compelling messages, handle difficult conversations with tact, " .
                     "build rapport with stakeholders, and represent their organizations professionally. The course covers both " .
                     "written and verbal communication strategies, cross-cultural communication, conflict resolution, and the art " .
                     "of diplomatic negotiation. Through practical exercises and real-world scenarios, participants will develop " .
                     "the confidence and competence to communicate effectively at all levels of an organization.",

            'CAO' => "The Certified Administrative Officer course is designed to equip administrative professionals with the " .
                     "comprehensive knowledge and skills required to excel in administrative roles. Administrative officers are " .
                     "the backbone of organizational efficiency, responsible for coordinating operations, managing resources, and " .
                     "ensuring smooth day-to-day functioning of departments and organizations.\n\n" .
                     "This course covers essential administrative functions including office management, document handling, " .
                     "record keeping, scheduling and coordination, communication protocols, and administrative procedures. " .
                     "Participants will learn best practices for managing administrative tasks, supporting executives and teams, " .
                     "maintaining organizational systems, and contributing to operational excellence. The program combines " .
                     "theoretical knowledge with practical applications, ensuring that graduates are well-prepared to handle " .
                     "the diverse challenges of modern administrative work.",

            'EWM' => "Workplace Effectiveness & Efficiency is fundamental to personal and organizational success. In today's " .
                     "fast-paced business environment, the ability to work effectively and efficiently is not just desirable—it's " .
                     "essential. This course empowers participants to maximize their productivity, optimize their work processes, " .
                     "and achieve better results with less effort.\n\n" .
                     "The course explores proven strategies for time management, task prioritization, workflow optimization, " .
                     "and productivity enhancement. Participants will learn how to eliminate time-wasting activities, streamline " .
                     "processes, set and achieve goals, manage energy levels, and maintain focus in a world full of distractions. " .
                     "Through practical tools and techniques, participants will develop systems and habits that lead to sustained " .
                     "high performance and work-life balance.",

            'LSP' => "Leading & Supervising People at Work is a critical competency for anyone in a management or supervisory role. " .
                     "Effective leadership and supervision are the cornerstones of team success, employee engagement, and " .
                     "organizational achievement. This course provides participants with the essential skills to lead teams, " .
                     "supervise employees, and drive performance effectively.\n\n" .
                     "Participants will learn fundamental leadership principles, supervision techniques, team management strategies, " .
                     "and performance management approaches. The course covers how to motivate and inspire team members, delegate " .
                     "effectively, provide constructive feedback, resolve conflicts, and create a positive work environment. " .
                     "Through interactive learning and practical exercises, participants will develop the confidence and capability " .
                     "to lead teams to success while fostering professional growth and development.",

            'OGM' => "Organizational Management is the art and science of coordinating resources, processes, and people to achieve " .
                     "organizational goals effectively. This course provides a comprehensive understanding of how organizations function, " .
                     "how to manage them efficiently, and how to drive organizational success through strategic management practices.\n\n" .
                     "The course covers key organizational management concepts including organizational structure and design, " .
                     "strategic planning, resource allocation, process management, change management, and organizational culture. " .
                     "Participants will learn how to analyze organizational challenges, develop effective management strategies, " .
                     "implement organizational improvements, and lead organizational change initiatives. Through case studies and " .
                     "practical applications, participants will gain the knowledge and skills needed to manage organizations " .
                     "effectively and contribute to long-term organizational success.",

            'PMC' => "Project Management Course provides comprehensive training in the principles, methodologies, and best practices " .
                     "of project management. Projects are the primary vehicle through which organizations achieve their strategic " .
                     "objectives, making project management skills highly valuable in today's business environment.\n\n" .
                     "This course covers the complete project lifecycle from initiation to closure, including project planning, " .
                     "scope management, scheduling, resource allocation, risk management, quality assurance, and stakeholder " .
                     "management. Participants will learn how to define project objectives, create project plans, manage project " .
                     "teams, monitor project progress, and deliver successful project outcomes. The course emphasizes practical " .
                     "application and provides participants with tools and techniques they can immediately use in their professional " .
                     "contexts.",

            'SHE' => "Level III - Health, Safety & Environment represents advanced training in workplace health, safety, and " .
                     "environmental management. This advanced-level program is designed for professionals who need comprehensive " .
                     "knowledge and skills to manage complex HSE challenges in their organizations.\n\n" .
                     "The course provides in-depth coverage of advanced HSE management systems, risk assessment methodologies, " .
                     "regulatory compliance, emergency response planning, environmental management, and HSE auditing. Participants " .
                     "will learn how to develop and implement comprehensive HSE programs, conduct advanced risk assessments, manage " .
                     "HSE compliance, investigate incidents thoroughly, and lead HSE improvement initiatives. This advanced program " .
                     "prepares participants to take on senior HSE roles and responsibilities, ensuring organizational safety and " .
                     "environmental stewardship.",

            // Alternative keys for matching
            'BUSINESS COMMUNICATION & DIPLOMACY' => "Business Communication & Diplomacy is a critical skill set in today's interconnected business world. " .
                     "Effective communication and diplomatic acumen are essential for building strong professional relationships, " .
                     "negotiating successfully, and navigating complex organizational dynamics. This course provides participants " .
                     "with the tools and techniques needed to communicate clearly, persuasively, and diplomatically in various business contexts.\n\n" .
                     "Participants will learn how to craft compelling messages, handle difficult conversations with tact, " .
                     "build rapport with stakeholders, and represent their organizations professionally. The course covers both " .
                     "written and verbal communication strategies, cross-cultural communication, conflict resolution, and the art " .
                     "of diplomatic negotiation. Through practical exercises and real-world scenarios, participants will develop " .
                     "the confidence and competence to communicate effectively at all levels of an organization.",

            'CERTIFIED ADMIN OFFICER' => "The Certified Administrative Officer course is designed to equip administrative professionals with the " .
                     "comprehensive knowledge and skills required to excel in administrative roles. Administrative officers are " .
                     "the backbone of organizational efficiency, responsible for coordinating operations, managing resources, and " .
                     "ensuring smooth day-to-day functioning of departments and organizations.\n\n" .
                     "This course covers essential administrative functions including office management, document handling, " .
                     "record keeping, scheduling and coordination, communication protocols, and administrative procedures. " .
                     "Participants will learn best practices for managing administrative tasks, supporting executives and teams, " .
                     "maintaining organizational systems, and contributing to operational excellence. The program combines " .
                     "theoretical knowledge with practical applications, ensuring that graduates are well-prepared to handle " .
                     "the diverse challenges of modern administrative work.",

            'WORKPLACE EFFECTIVENESS & EFFICIENCY' => "Workplace Effectiveness & Efficiency is fundamental to personal and organizational success. In today's " .
                     "fast-paced business environment, the ability to work effectively and efficiently is not just desirable—it's " .
                     "essential. This course empowers participants to maximize their productivity, optimize their work processes, " .
                     "and achieve better results with less effort.\n\n" .
                     "The course explores proven strategies for time management, task prioritization, workflow optimization, " .
                     "and productivity enhancement. Participants will learn how to eliminate time-wasting activities, streamline " .
                     "processes, set and achieve goals, manage energy levels, and maintain focus in a world full of distractions. " .
                     "Through practical tools and techniques, participants will develop systems and habits that lead to sustained " .
                     "high performance and work-life balance.",

            'LEADING & SUPERVISING PEOPLE AT WORK' => "Leading & Supervising People at Work is a critical competency for anyone in a management or supervisory role. " .
                     "Effective leadership and supervision are the cornerstones of team success, employee engagement, and " .
                     "organizational achievement. This course provides participants with the essential skills to lead teams, " .
                     "supervise employees, and drive performance effectively.\n\n" .
                     "Participants will learn fundamental leadership principles, supervision techniques, team management strategies, " .
                     "and performance management approaches. The course covers how to motivate and inspire team members, delegate " .
                     "effectively, provide constructive feedback, resolve conflicts, and create a positive work environment. " .
                     "Through interactive learning and practical exercises, participants will develop the confidence and capability " .
                     "to lead teams to success while fostering professional growth and development.",

            'ORGANIZATIONAL MANAGEMENT' => "Organizational Management is the art and science of coordinating resources, processes, and people to achieve " .
                     "organizational goals effectively. This course provides a comprehensive understanding of how organizations function, " .
                     "how to manage them efficiently, and how to drive organizational success through strategic management practices.\n\n" .
                     "The course covers key organizational management concepts including organizational structure and design, " .
                     "strategic planning, resource allocation, process management, change management, and organizational culture. " .
                     "Participants will learn how to analyze organizational challenges, develop effective management strategies, " .
                     "implement organizational improvements, and lead organizational change initiatives. Through case studies and " .
                     "practical applications, participants will gain the knowledge and skills needed to manage organizations " .
                     "effectively and contribute to long-term organizational success.",

            'PROJECT MANAGEMENT COURSE' => "Project Management Course provides comprehensive training in the principles, methodologies, and best practices " .
                     "of project management. Projects are the primary vehicle through which organizations achieve their strategic " .
                     "objectives, making project management skills highly valuable in today's business environment.\n\n" .
                     "This course covers the complete project lifecycle from initiation to closure, including project planning, " .
                     "scope management, scheduling, resource allocation, risk management, quality assurance, and stakeholder " .
                     "management. Participants will learn how to define project objectives, create project plans, manage project " .
                     "teams, monitor project progress, and deliver successful project outcomes. The course emphasizes practical " .
                     "application and provides participants with tools and techniques they can immediately use in their professional " .
                     "contexts.",

            'LEVEL III - HEALTH SAFETY & ENVIRONMENT' => "Level III - Health, Safety & Environment represents advanced training in workplace health, safety, and " .
                     "environmental management. This advanced-level program is designed for professionals who need comprehensive " .
                     "knowledge and skills to manage complex HSE challenges in their organizations.\n\n" .
                     "The course provides in-depth coverage of advanced HSE management systems, risk assessment methodologies, " .
                     "regulatory compliance, emergency response planning, environmental management, and HSE auditing. Participants " .
                     "will learn how to develop and implement comprehensive HSE programs, conduct advanced risk assessments, manage " .
                     "HSE compliance, investigate incidents thoroughly, and lead HSE improvement initiatives. This advanced program " .
                     "prepares participants to take on senior HSE roles and responsibilities, ensuring organizational safety and " .
                     "environmental stewardship.",
        ];
    }
}

