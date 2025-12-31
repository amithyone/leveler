<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\QuestionPool;
use App\Models\Course;

class QuestionPoolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Starting Question Pool Seeding...');
        
        // Seed all courses from CSV files
        $this->seedFromCSV('CAO', base_path('QUESTIONS POOL - LEVELER - CAO.csv'));
        $this->seedFromCSV('CSR', base_path('QUESTIONS POOL - LEVELER - CSM.csv')); // CSM maps to CSR course code
        $this->seedFromCSV('DMK', base_path('QUESTIONS POOL - LEVELER - DMK.csv'));
        $this->seedFromCSV('EBM', base_path('QUESTIONS POOL - LEVELER - EBM.csv'));
        $this->seedFromCSV('HRM', base_path('QUESTIONS POOL - LEVELER - HRM.csv'));
        $this->seedFromCSV('HSE', base_path('QUESTIONS POOL - LEVELER - HSE.csv'));
        $this->seedFromCSV('PMG', base_path('QUESTIONS POOL - LEVELER - PMG.csv'));
        $this->seedFromCSV('SHE', base_path('QUESTIONS POOL - LEVELER - SHE.csv'));
        
        $this->command->info('Question Pool seeding completed!');
    }

    /**
     * Seed questions from CSV file
     */
    private function seedFromCSV(string $courseCode, string $csvPath): void
    {
        $course = Course::where('code', $courseCode)->first();
        
        if (!$course) {
            $this->command->warn("Course {$courseCode} not found. Skipping...");
            return;
        }

        if (!file_exists($csvPath)) {
            $this->command->warn("CSV file not found: {$csvPath}. Skipping...");
            return;
        }

        $questions = $this->parseCSV($csvPath);
        
        if (empty($questions)) {
            $this->command->warn("No questions found in CSV: {$csvPath}");
            return;
        }

        $count = 0;
        foreach ($questions as $questionData) {
            QuestionPool::updateOrCreate(
                [
                    'course_id' => $course->id,
                    'question' => $questionData['question']
                ],
                array_merge($questionData, ['course_id' => $course->id])
            );
            $count++;
        }

        $this->command->info("✓ {$courseCode}: {$count} questions seeded");
    }

    /**
     * Parse CSV file and convert to question format
     */
    private function parseCSV(string $csvPath): array
    {
        $questions = [];
        
        if (($handle = fopen($csvPath, 'r')) !== false) {
            // Skip header row
            $header = fgetcsv($handle);
            
            while (($data = fgetcsv($handle)) !== false) {
                // CSV format: S/N, Questions, Option A, Option B, Option C, Option D, Answer, Actions
                if (count($data) < 7) {
                    continue; // Skip invalid rows
                }

                $question = trim($data[1] ?? '');
                $optionA = $this->cleanOption($data[2] ?? '');
                $optionB = $this->cleanOption($data[3] ?? '');
                $optionC = $this->cleanOption($data[4] ?? '');
                $optionD = $this->cleanOption($data[5] ?? '');
                $answer = strtoupper(trim($data[6] ?? ''));

                // Skip if question is empty
                if (empty($question)) {
                    continue;
                }

                // Validate answer is A, B, C, or D
                if (!in_array($answer, ['A', 'B', 'C', 'D'])) {
                    continue;
                }

                $questions[] = [
                    'question' => $question,
                    'type' => 'multiple_choice',
                    'options' => [
                        'A' => $optionA,
                        'B' => $optionB,
                        'C' => $optionC,
                        'D' => $optionD,
                    ],
                    'correct_answer' => $answer,
                    'points' => 1,
                ];
            }
            
            fclose($handle);
        }

        return $questions;
    }

    /**
     * Clean option text (remove prefixes like "A.", "B.", etc.)
     */
    private function cleanOption(string $option): string
    {
        // Remove common prefixes
        $option = preg_replace('/^[A-D]\.\s*/', '', $option);
        $option = preg_replace('/^[A-D]\.\.\s*/', '', $option);
        $option = preg_replace('/^[A-D]\.\s+/', '', $option);
        return trim($option);
    }
}
