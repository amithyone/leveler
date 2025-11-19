<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Course;
use App\Models\QuestionPool;

class ImportQuestionsFromCsv extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'questions:import-csv {file} {course_code?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import questions from CSV file into question pool';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $fileName = $this->argument('file');
        $courseCode = $this->argument('course_code');
        
        // Try to extract course code from filename if not provided
        if (!$courseCode) {
            // Extract course code from filename (e.g., "QUESTIONS POOL - LEVELER - PMG.csv" -> "PMG")
            if (preg_match('/- ([A-Z]{2,4})\.csv$/i', $fileName, $matches)) {
                $courseCode = strtoupper($matches[1]);
            } else {
                $this->error("Could not determine course code from filename. Please provide it as second argument.");
                $this->info("Usage: php artisan questions:import-csv {file} {course_code}");
                return 1;
            }
        }

        $filePath = base_path($fileName);

        if (!file_exists($filePath)) {
            $this->error("File not found: {$filePath}");
            return 1;
        }

        // Find course by code
        $course = Course::where('code', strtoupper($courseCode))->first();
        
        if (!$course) {
            $this->error("Course with code '{$courseCode}' not found!");
            $this->info("Available courses: " . Course::pluck('code')->implode(', '));
            return 1;
        }

        $this->info("Importing questions for course: {$course->title} ({$course->code})");

        try {
            $file = fopen($filePath, 'r');
            
            if (!$file) {
                $this->error("Could not open file: {$filePath}");
                return 1;
            }

            // Read header row
            $headers = fgetcsv($file);
            
            if (!$headers) {
                $this->error("Could not read CSV headers");
                fclose($file);
                return 1;
            }

            $this->info("CSV Headers: " . implode(', ', $headers));

            $imported = 0;
            $skipped = 0;
            $rowNumber = 1;

            while (($row = fgetcsv($file)) !== false) {
                $rowNumber++;
                
                // Skip empty rows
                if (empty(array_filter($row))) {
                    continue;
                }

                // Parse row based on headers
                $data = array_combine($headers, $row);
                
                if (!$data) {
                    $this->warn("Skipping row {$rowNumber}: Could not parse headers");
                    $skipped++;
                    continue;
                }

                // Extract question data
                $questionText = trim($data['Questions'] ?? $data['Question'] ?? '');
                $optionA = trim($data['Option A'] ?? $data['A'] ?? '');
                $optionB = trim($data['Option B'] ?? $data['B'] ?? '');
                $optionC = trim($data['Option C'] ?? $data['C'] ?? '');
                $optionD = trim($data['Option D'] ?? $data['D'] ?? '');
                $correctAnswer = strtoupper(trim($data['Answer'] ?? $data['Correct Answer'] ?? ''));

                // Validate required fields
                if (empty($questionText)) {
                    $this->warn("Skipping row {$rowNumber}: Missing question text");
                    $skipped++;
                    continue;
                }

                if (empty($correctAnswer) || !in_array($correctAnswer, ['A', 'B', 'C', 'D'])) {
                    $this->warn("Skipping row {$rowNumber}: Invalid or missing correct answer");
                    $skipped++;
                    continue;
                }

                // Build options array
                $options = [];
                if (!empty($optionA)) $options['A'] = $optionA;
                if (!empty($optionB)) $options['B'] = $optionB;
                if (!empty($optionC)) $options['C'] = $optionC;
                if (!empty($optionD)) $options['D'] = $optionD;

                if (empty($options)) {
                    $this->warn("Skipping row {$rowNumber}: No options provided");
                    $skipped++;
                    continue;
                }

                // Create or update question
                try {
                    QuestionPool::updateOrCreate(
                        [
                            'course_id' => $course->id,
                            'question' => $questionText,
                        ],
                        [
                            'type' => 'multiple_choice',
                            'options' => $options,
                            'correct_answer' => $correctAnswer,
                            'points' => 1,
                        ]
                    );
                    $imported++;
                } catch (\Exception $e) {
                    $this->warn("Error importing row {$rowNumber}: " . $e->getMessage());
                    $skipped++;
                }
            }

            fclose($file);

            $this->info("\nImport completed!");
            $this->info("✓ Imported: {$imported} questions");
            $this->info("✗ Skipped: {$skipped} rows");
            $this->info("Total questions for {$course->code}: " . QuestionPool::where('course_id', $course->id)->count());

            return 0;

        } catch (\Exception $e) {
            $this->error("Error reading CSV file: " . $e->getMessage());
            $this->error($e->getTraceAsString());
            return 1;
        }
    }
}
