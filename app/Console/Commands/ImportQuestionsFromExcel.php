<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Course;
use App\Models\QuestionPool;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ImportQuestionsFromExcel extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'questions:import {file=QUESTIONS POOL - LEVELER.xlsx}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import questions from Excel file into the question pool';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $fileName = $this->argument('file');
        $filePath = base_path($fileName);

        if (!file_exists($filePath)) {
            $this->error("File not found: {$filePath}");
            return 1;
        }

        $this->info("Reading Excel file: {$fileName}");

        try {
            $spreadsheet = IOFactory::load($filePath);
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            // Skip header row (assuming first row is headers)
            $headers = array_shift($rows);
            
            $this->info("Found " . count($rows) . " question rows");
            $this->info("Headers: " . implode(', ', $headers));

            $imported = 0;
            $skipped = 0;
            $currentCourse = null;
            $currentCourseCode = null;

            foreach ($rows as $index => $row) {
                $rowNumber = $index + 2; // +2 because we removed header and Excel is 1-indexed

                // Skip empty rows
                if (empty(array_filter($row))) {
                    continue;
                }

                // Try to detect course code in the row
                // Common patterns: Course code might be in first column or in a separate column
                $courseCode = $this->extractCourseCode($row, $headers);
                
                if ($courseCode) {
                    $currentCourseCode = $courseCode;
                    $currentCourse = Course::where('code', $courseCode)->first();
                    
                    if (!$currentCourse) {
                        $this->warn("Course with code '{$courseCode}' not found. Skipping questions for this course.");
                        continue;
                    }
                    
                    $this->info("Processing questions for course: {$currentCourse->title} ({$currentCourseCode})");
                    continue;
                }

                // If we have a course, try to parse the question
                if ($currentCourse) {
                    $question = $this->parseQuestionRow($row, $headers);
                    
                    if ($question) {
                        try {
                            QuestionPool::updateOrCreate(
                                [
                                    'course_id' => $currentCourse->id,
                                    'question' => $question['question'],
                                ],
                                [
                                    'type' => $question['type'],
                                    'options' => $question['options'],
                                    'correct_answer' => $question['correct_answer'],
                                    'points' => $question['points'] ?? 1,
                                ]
                            );
                            $imported++;
                        } catch (\Exception $e) {
                            $this->warn("Error importing question at row {$rowNumber}: " . $e->getMessage());
                            $skipped++;
                        }
                    } else {
                        $skipped++;
                    }
                } else {
                    $skipped++;
                }
            }

            $this->info("\nImport completed!");
            $this->info("Imported: {$imported} questions");
            $this->info("Skipped: {$skipped} rows");

            return 0;

        } catch (\Exception $e) {
            $this->error("Error reading Excel file: " . $e->getMessage());
            return 1;
        }
    }

    /**
     * Extract course code from row
     */
    private function extractCourseCode($row, $headers)
    {
        // Check if first column contains a course code
        $firstColumn = trim($row[0] ?? '');
        
        // List of known course codes
        $courseCodes = ['BCD', 'CAO', 'CSR', 'DMK', 'EBM', 'EWM', 'HRM', 'HSE', 'LSP', 'OGM', 'PMC', 'PMG', 'SHE'];
        
        if (in_array($firstColumn, $courseCodes)) {
            return $firstColumn;
        }

        // Check if any column header contains "course" or "code"
        foreach ($headers as $index => $header) {
            if (stripos($header, 'course') !== false || stripos($header, 'code') !== false) {
                $value = trim($row[$index] ?? '');
                if (in_array($value, $courseCodes)) {
                    return $value;
                }
            }
        }

        return null;
    }

    /**
     * Parse question row into question data
     */
    private function parseQuestionRow($row, $headers)
    {
        // Try to find question, options, and correct answer columns
        $question = null;
        $options = [];
        $correctAnswer = null;
        $type = 'multiple_choice';

        // Common column patterns
        $questionIndex = $this->findColumnIndex($headers, ['question', 'questions', 'q']);
        $optionAIndex = $this->findColumnIndex($headers, ['option a', 'a', 'option_a']);
        $optionBIndex = $this->findColumnIndex($headers, ['option b', 'b', 'option_b']);
        $optionCIndex = $this->findColumnIndex($headers, ['option c', 'c', 'option_c']);
        $optionDIndex = $this->findColumnIndex($headers, ['option d', 'd', 'option_d']);
        $correctIndex = $this->findColumnIndex($headers, ['correct', 'answer', 'correct_answer', 'key']);

        // Get question text
        if ($questionIndex !== null) {
            $question = trim($row[$questionIndex] ?? '');
        } else {
            // Try first non-empty column
            foreach ($row as $cell) {
                if (!empty(trim($cell ?? ''))) {
                    $question = trim($cell);
                    break;
                }
            }
        }

        if (empty($question)) {
            return null;
        }

        // Get options
        if ($optionAIndex !== null) {
            $options['A'] = trim($row[$optionAIndex] ?? '');
        }
        if ($optionBIndex !== null) {
            $options['B'] = trim($row[$optionBIndex] ?? '');
        }
        if ($optionCIndex !== null) {
            $options['C'] = trim($row[$optionCIndex] ?? '');
        }
        if ($optionDIndex !== null) {
            $options['D'] = trim($row[$optionDIndex] ?? '');
        }

        // If no structured options found, try to find them in sequential columns
        if (empty($options)) {
            $startIndex = $questionIndex !== null ? $questionIndex + 1 : 1;
            for ($i = $startIndex; $i < min($startIndex + 4, count($row)); $i++) {
                $optionValue = trim($row[$i] ?? '');
                if (!empty($optionValue)) {
                    $options[chr(65 + ($i - $startIndex))] = $optionValue; // A, B, C, D
                }
            }
        }

        // Get correct answer
        if ($correctIndex !== null) {
            $correctAnswer = trim($row[$correctIndex] ?? '');
        } else {
            // Try to find it in the last column or after options
            $lastIndex = count($row) - 1;
            $correctAnswer = trim($row[$lastIndex] ?? '');
        }

        // Normalize correct answer (should be A, B, C, or D)
        if (!empty($correctAnswer)) {
            $correctAnswer = strtoupper(substr($correctAnswer, 0, 1));
            if (!in_array($correctAnswer, ['A', 'B', 'C', 'D'])) {
                // Try to find the answer in the options
                foreach ($options as $key => $value) {
                    if (stripos($value, $correctAnswer) !== false || stripos($correctAnswer, $value) !== false) {
                        $correctAnswer = $key;
                        break;
                    }
                }
            }
        }

        // Validate we have required data
        if (empty($question) || empty($options) || empty($correctAnswer)) {
            return null;
        }

        return [
            'question' => $question,
            'type' => $type,
            'options' => $options,
            'correct_answer' => $correctAnswer,
            'points' => 1,
        ];
    }

    /**
     * Find column index by header name
     */
    private function findColumnIndex($headers, $searchTerms)
    {
        foreach ($headers as $index => $header) {
            $headerLower = strtolower(trim($header));
            foreach ($searchTerms as $term) {
                if (stripos($headerLower, strtolower($term)) !== false) {
                    return $index;
                }
            }
        }
        return null;
    }
}
