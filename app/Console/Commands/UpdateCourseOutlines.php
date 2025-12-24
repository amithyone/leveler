<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Course;
use Illuminate\Support\Str;

class UpdateCourseOutlines extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'courses:update-outlines {file?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update course outlines from a text file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $filePath = $this->argument('file') ?? 'course out line.txt';
        
        if (!file_exists($filePath)) {
            $this->error("File not found: {$filePath}");
            return 1;
        }

        $content = file_get_contents($filePath);
        $courses = $this->parseCourseOutlines($content);
        
        $this->info("Found " . count($courses) . " course outlines to update.");
        
        $updated = 0;
        $notFound = [];
        
        foreach ($courses as $courseData) {
            $course = $this->findCourse($courseData['title']);
            
            if ($course) {
                $this->updateCourse($course, $courseData);
                $updated++;
                $this->info("✓ Updated: {$course->title}");
            } else {
                $notFound[] = $courseData['title'];
                $this->warn("✗ Not found: {$courseData['title']}");
            }
        }
        
        $this->info("\n✅ Successfully updated {$updated} courses.");
        
        if (!empty($notFound)) {
            $this->warn("\n⚠️  Courses not found in database:");
            foreach ($notFound as $title) {
                $this->warn("  - {$title}");
            }
        }
        
        return 0;
    }

    /**
     * Parse course outlines from the text file
     */
    private function parseCourseOutlines($content)
    {
        $courses = [];
        $sections = preg_split('/\*([^*]+)\*/', $content, -1, PREG_SPLIT_DELIM_CAPTURE);
        
        for ($i = 1; $i < count($sections); $i += 2) {
            if (isset($sections[$i]) && isset($sections[$i + 1])) {
                $title = trim($sections[$i]);
                $body = trim($sections[$i + 1]);
                
                $courseData = [
                    'title' => $title,
                    'overview' => $this->extractOverview($body),
                    'objectives' => $this->extractObjectives($body),
                    'curriculum' => $this->extractCurriculum($body),
                    'what_you_will_learn' => $this->extractWhatYouWillLearn($body),
                ];
                
                $courses[] = $courseData;
            }
        }
        
        return $courses;
    }

    /**
     * Extract overview from course content
     */
    private function extractOverview($body)
    {
        // Look for "Course Overview" section
        if (preg_match('/Course Overview\s*\n\n(.*?)(?=\n\nCourse Objectives|\n\nLearning Objectives|\n\n🎯|Course Outline|\n\nCourse Modules|$)/is', $body, $matches)) {
            return trim($matches[1]);
        }
        
        // If no "Course Overview" header, take first paragraph
        $lines = explode("\n", $body);
        $overview = [];
        $inOverview = false;
        
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;
            
            if (stripos($line, 'Course Overview') !== false) {
                $inOverview = true;
                continue;
            }
            
            if ($inOverview && (stripos($line, 'Course Objectives') !== false || 
                                stripos($line, 'Learning Objectives') !== false ||
                                stripos($line, 'Course Outline') !== false ||
                                stripos($line, 'Course Modules') !== false)) {
                break;
            }
            
            if ($inOverview || (empty($overview) && !preg_match('/^(Module|Part|Course|Learning|🎯)/i', $line))) {
                $overview[] = $line;
            }
        }
        
        return implode("\n\n", $overview);
    }

    /**
     * Extract objectives from course content
     */
    private function extractObjectives($body)
    {
        $objectives = [];
        
        // Look for "Course Objectives" or "Learning Objectives"
        if (preg_match('/(?:Course Objectives|Learning Objectives|🎯 Key Learning Objectives|🎯 Course Objectives)\s*\n\n(.*?)(?=\n\nCourse Outline|\n\nCourse Modules|$)/is', $body, $matches)) {
            $objectivesText = trim($matches[1]);
            
            // Split by lines and filter
            $lines = explode("\n", $objectivesText);
            foreach ($lines as $line) {
                $line = trim($line);
                if (empty($line)) continue;
                
                // Remove bullet points and numbering
                $line = preg_replace('/^[•\-\*\d+\.\)]\s*/', '', $line);
                $line = preg_replace('/^the\s+/i', '', $line); // Remove "the" prefix
                $line = trim($line);
                
                if (!empty($line) && strlen($line) > 10) {
                    $objectives[] = $line;
                }
            }
        }
        
        return $objectives;
    }

    /**
     * Extract curriculum/modules from course content
     */
    private function extractCurriculum($body)
    {
        $curriculum = [];
        
        // Look for "Course Outline" or "Course Modules"
        if (preg_match('/(?:Course Outline|Course Modules|The course will cover:)\s*\n\n(.*?)$/is', $body, $matches)) {
            $outlineText = trim($matches[1]);
            
            $lines = explode("\n", $outlineText);
            $currentModule = null;
            
            foreach ($lines as $line) {
                $line = trim($line);
                if (empty($line)) continue;
                
                // Check if it's a module/part header
                if (preg_match('/^(Module \d+|Part \d+|The Concept|Entrepreneurial|Startup|Design|Value|Business|Market|Pricing|Bootstrapping|Developing|Legal|Pitching|Sales|Digital|People|Process|Business Leadership|Customer Service Technique \d+):?\s*(.+)$/i', $line, $moduleMatches)) {
                    // Save previous module
                    if ($currentModule) {
                        $curriculum[] = $currentModule;
                    }
                    
                    $moduleTitle = trim($moduleMatches[2] ?? $moduleMatches[1]);
                    if (empty($moduleTitle)) {
                        $moduleTitle = trim($moduleMatches[1]);
                    }
                    
                    $currentModule = [
                        'module_title' => $moduleTitle,
                        'lessons' => []
                    ];
                } elseif (preg_match('/^(Module \d+[–\-]\s*.+)$/i', $line, $moduleMatches)) {
                    // Format: "Module 1 – Title"
                    if ($currentModule) {
                        $curriculum[] = $currentModule;
                    }
                    
                    $moduleTitle = preg_replace('/^Module \d+[–\-]\s*/i', '', $line);
                    $currentModule = [
                        'module_title' => trim($moduleTitle),
                        'lessons' => []
                    ];
                } elseif ($currentModule) {
                    // Add as lesson to current module
                    if (strlen($line) > 5) {
                        $currentModule['lessons'][] = $line;
                    }
                } else {
                    // Standalone module
                    if (strlen($line) > 5 && !preg_match('/^(Part \d+|Module \d+)/i', $line)) {
                        $curriculum[] = [
                            'module_title' => $line,
                            'lessons' => []
                        ];
                    }
                }
            }
            
            // Add last module
            if ($currentModule) {
                $curriculum[] = $currentModule;
            }
        }
        
        return $curriculum;
    }

    /**
     * Extract "What You Will Learn" from objectives or overview
     */
    private function extractWhatYouWillLearn($body)
    {
        // Use objectives as "what you will learn"
        $objectives = $this->extractObjectives($body);
        
        // If no objectives, extract key points from overview
        if (empty($objectives)) {
            $overview = $this->extractOverview($body);
            $sentences = preg_split('/[.!?]\s+/', $overview);
            $objectives = array_filter(array_slice($sentences, 0, 5), function($s) {
                return strlen(trim($s)) > 20;
            });
        }
        
        return array_values($objectives);
    }

    /**
     * Find course by title (fuzzy matching)
     */
    private function findCourse($title)
    {
        // Exact match first
        $course = Course::where('title', $title)->first();
        if ($course) {
            return $course;
        }
        
        // Try case-insensitive match
        $course = Course::whereRaw('LOWER(title) = ?', [strtolower($title)])->first();
        if ($course) {
            return $course;
        }
        
        // Fuzzy matching - check if title contains key words
        $titleWords = explode(' ', strtolower($title));
        $titleWords = array_filter($titleWords, function($word) {
            return strlen($word) > 3 && !in_array(strtolower($word), ['the', 'and', 'for', 'with', 'from']);
        });
        
        if (!empty($titleWords)) {
            $query = Course::query();
            foreach ($titleWords as $word) {
                $query->whereRaw('LOWER(title) LIKE ?', ['%' . $word . '%']);
            }
            $course = $query->first();
            if ($course) {
                return $course;
            }
        }
        
        // Manual mapping for known variations
        $mappings = [
            'PROJECT MANAGEMENT' => [
                'titles' => ['PROJECT MANAGEMENT', 'Project Management Course'],
                'codes' => ['PMG', 'PMC']
            ],
            'HUMAN RESOURCE MANAGEMENT' => [
                'titles' => ['HUMAN RESOURCE MANAGEMENT'],
                'codes' => ['HRM']
            ],
            'ENTERPRENEURSHIP & BUSINESS DEVELOPMENT' => [
                'titles' => ['ENTREPRENEURSHIP & BUSINESS MANAGEMENT'],
                'codes' => ['EBM']
            ],
            'HEALTH SAFETY & ENVIRONMENT' => [
                'titles' => ['Health, Safety and Environment', 'LEVEL III - HEALTH SAFETY & ENVIRONMENT'],
                'codes' => ['HSE', 'SHE']
            ],
            'CUSTOMER SERVICE MANAGEMENT' => [
                'titles' => ['Customer Service & Relationship Management'],
                'codes' => ['CSR']
            ],
            'DIGITAL MAKERTING' => [
                'titles' => ['DIGITAL MARKETING'],
                'codes' => ['DMK']
            ],
        ];
        
        foreach ($mappings as $outlineTitle => $variations) {
            if (stripos($title, $outlineTitle) !== false || 
                stripos($outlineTitle, $title) !== false) {
                // Try by code first
                foreach ($variations['codes'] ?? [] as $code) {
                    $course = Course::where('code', $code)->first();
                    if ($course) {
                        return $course;
                    }
                }
                // Try by title
                foreach ($variations['titles'] ?? [] as $variation) {
                    $course = Course::where('title', $variation)->first();
                    if ($course) {
                        return $course;
                    }
                }
            }
        }
        
        return null;
    }

    /**
     * Update course with outline data
     */
    private function updateCourse($course, $courseData)
    {
        $updateData = [];
        
        if (!empty($courseData['overview'])) {
            $updateData['overview'] = $courseData['overview'];
        }
        
        if (!empty($courseData['objectives'])) {
            $updateData['objectives'] = $courseData['objectives'];
        }
        
        if (!empty($courseData['curriculum'])) {
            $updateData['curriculum'] = $courseData['curriculum'];
        }
        
        if (!empty($courseData['what_you_will_learn'])) {
            $updateData['what_you_will_learn'] = $courseData['what_you_will_learn'];
        }
        
        $course->update($updateData);
    }
}

