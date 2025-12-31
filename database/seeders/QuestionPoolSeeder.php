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
        // Get CAO course
        $caoCourse = Course::where('code', 'CAO')->first();
        
        if (!$caoCourse) {
            $this->command->warn('CAO course not found. Please seed courses first.');
            return;
        }

        // CAO Questions from CSV
        $questions = [
            [
                'question' => '80/20 rule tells us that: 80% of your activities will bring you 20% of your result.',
                'type' => 'multiple_choice',
                'options' => [
                    'A' => 'FALSE',
                    'B' => 'Partly true',
                    'C' => 'Cannot say',
                    'D' => 'TRUE'
                ],
                'correct_answer' => 'A',
                'points' => 1,
            ],
            [
                'question' => "Admin officer's role requires multitasking across a range of responsibilities, including the following EXCEPT",
                'type' => 'multiple_choice',
                'options' => [
                    'A' => 'event planning and organization',
                    'B' => 'receiving and forwarding communications, and',
                    'C' => 'graphic designs and painting',
                    'D' => 'taking care of more general clerical duties'
                ],
                'correct_answer' => 'C',
                'points' => 1,
            ],
            [
                'question' => 'Administrative officers are responsible for day-to-day task management in within an organization.',
                'type' => 'multiple_choice',
                'options' => [
                    'A' => 'TRUE',
                    'B' => 'Cannot say',
                    'C' => 'FALSE',
                    'D' => 'Partly true'
                ],
                'correct_answer' => 'A',
                'points' => 1,
            ],
            [
                'question' => 'All but one of the following are benefits of effective record management',
                'type' => 'multiple_choice',
                'options' => [
                    'A' => 'Low staff turnover',
                    'B' => 'Effective record retrieval',
                    'C' => 'Reduced storage costs',
                    'D' => 'Maintain regulatory compliance'
                ],
                'correct_answer' => 'A',
                'points' => 1,
            ],
            [
                'question' => 'An effective admin office is concerned with getting the job done and doing it right.',
                'type' => 'multiple_choice',
                'options' => [
                    'A' => 'FALSE',
                    'B' => 'TRUE',
                    'C' => 'Cannot say',
                    'D' => 'Partly true'
                ],
                'correct_answer' => 'B',
                'points' => 1,
            ],
            [
                'question' => 'An impact-effort matrix is a decision-making tool that assists people to manage their time more efficiently. Using an impact-effort matrix, you can assess your activities based on the level of effort required and the potential impact or benefits you will have. Which of the following are not included in the impact-effort matrix?',
                'type' => 'multiple_choice',
                'options' => [
                    'A' => 'Quick Wins',
                    'B' => 'Major Projects',
                    'C' => 'Time Wasters',
                    'D' => 'Turning point'
                ],
                'correct_answer' => 'D',
                'points' => 1,
            ],
            [
                'question' => 'Being Efficient at work means performing or functioning in the best possible manner with the least waste of .........',
                'type' => 'multiple_choice',
                'options' => [
                    'A' => 'Energy and communication',
                    'B' => 'Material and people',
                    'C' => 'time and effort',
                    'D' => 'Distance and workstation'
                ],
                'correct_answer' => 'C',
                'points' => 1,
            ],
            [
                'question' => 'By evaluating the importance of each task and contrasting it with the time you have available to complete it, you\'re able to classify each task into one of four categories. Which is not among the four categories?',
                'type' => 'multiple_choice',
                'options' => [
                    'A' => 'Urgent and Important',
                    'B' => 'Important and Not Urgent',
                    'C' => 'Not Important and Urgent',
                    'D' => 'Not Urgent and Not Urgent'
                ],
                'correct_answer' => 'D',
                'points' => 1,
            ],
            [
                'question' => 'By using a robust document management system, you can save time by quickly retrieving data when you need it.',
                'type' => 'multiple_choice',
                'options' => [
                    'A' => 'FALSE',
                    'B' => 'Partly true',
                    'C' => 'Cannot say',
                    'D' => 'TRUE'
                ],
                'correct_answer' => 'D',
                'points' => 1,
            ],
            [
                'question' => 'Doing things right and doing the right thing will ensure that our organization -----------',
                'type' => 'multiple_choice',
                'options' => [
                    'A' => 'Thrives',
                    'B' => 'Survives',
                    'C' => 'Dies slowly',
                    'D' => 'Dies quickly'
                ],
                'correct_answer' => 'A',
                'points' => 1,
            ],
            [
                'question' => 'Emotional intelligence can be helpful when you are doing any of the following except...',
                'type' => 'multiple_choice',
                'options' => [
                    'A' => 'Deciding on weekly expenditures',
                    'B' => 'Coaching and motivating others',
                    'C' => 'Resolving conflict',
                    'D' => 'Having difficult conversations'
                ],
                'correct_answer' => 'A',
                'points' => 1,
            ],
            [
                'question' => 'Ergonomics refers to',
                'type' => 'multiple_choice',
                'options' => [
                    'A' => 'a study of work environment',
                    'B' => 'data processing system',
                    'C' => 'communication between organization',
                    'D' => 'a study of the economics'
                ],
                'correct_answer' => 'A',
                'points' => 1,
            ],
            [
                'question' => 'If the turnaround time of a particular task is 2 days, it has to be completed within 2 days; else other interrelated tasks will suffer, delaying an entire workflow.',
                'type' => 'multiple_choice',
                'options' => [
                    'A' => 'FALSE',
                    'B' => 'Cannot say',
                    'C' => 'TRUE',
                    'D' => 'Partly true'
                ],
                'correct_answer' => 'C',
                'points' => 1,
            ],
            [
                'question' => 'If you manage your company records effectively, you can avoid the following, except:',
                'type' => 'multiple_choice',
                'options' => [
                    'A' => 'lost productivity',
                    'B' => 'data integrity',
                    'C' => 'wasteful audits',
                    'D' => 'costly compliance penalties'
                ],
                'correct_answer' => 'B',
                'points' => 1,
            ],
            [
                'question' => 'Managing records assists you in organising data and preventing data loss or breaches. This describes Safeguarding essential information',
                'type' => 'multiple_choice',
                'options' => [
                    'A' => 'FALSE',
                    'B' => 'TRUE',
                    'C' => 'Cannot say',
                    'D' => 'Partly true'
                ],
                'correct_answer' => 'B',
                'points' => 1,
            ],
            [
                'question' => 'The following are benefits of an ergonomic workspace except',
                'type' => 'multiple_choice',
                'options' => [
                    'A' => 'Decreased sick leave due to fewer injuries and exhaustion',
                    'B' => 'improved business diversification',
                    'C' => 'Reduced number of employees compensation claims',
                    'D' => 'Increased work satisfaction'
                ],
                'correct_answer' => 'B',
                'points' => 1,
            ],
            [
                'question' => 'The following are key technology skills that is vital for admin functions, EXCEPT.....',
                'type' => 'multiple_choice',
                'options' => [
                    'A' => 'Robotics and Web Design',
                    'B' => 'Word processors and Spreadsheets',
                    'C' => 'Digital presentations and Social media',
                    'D' => 'Virtual conferences and Sending emails'
                ],
                'correct_answer' => 'A',
                'points' => 1,
            ],
            [
                'question' => 'The following are requirements for an administrative officer, Except',
                'type' => 'multiple_choice',
                'options' => [
                    'A' => 'Sales and marketing skills',
                    'B' => 'communication skills',
                    'C' => 'Organizational skills',
                    'D' => 'Working knowledge of necessary productivity tools, including Microsoft Office Suite'
                ],
                'correct_answer' => 'A',
                'points' => 1,
            ],
            [
                'question' => 'The following are ways to safely backup your record management system, except….',
                'type' => 'multiple_choice',
                'options' => [
                    'A' => 'Utilise remote record storage facilities',
                    'B' => 'Start a record mining programme',
                    'C' => 'Design a record management policy',
                    'D' => 'Have automated backups'
                ],
                'correct_answer' => 'B',
                'points' => 1,
            ],
            [
                'question' => 'The four attributes of emotional intelligence are:I. self awareness II. social awareness III. self management IV. social skills',
                'type' => 'multiple_choice',
                'options' => [
                    'A' => 'FALSE',
                    'B' => 'TRUE',
                    'C' => 'Cannot say',
                    'D' => 'Partly'
                ],
                'correct_answer' => 'B',
                'points' => 1,
            ],
            [
                'question' => 'The role of the office manager is to manage the following EXCEPT',
                'type' => 'multiple_choice',
                'options' => [
                    'A' => 'workers',
                    'B' => 'workplace',
                    'C' => 'workstation',
                    'D' => 'workflow'
                ],
                'correct_answer' => 'A',
                'points' => 1,
            ],
            [
                'question' => 'There are for four stages in record management life cycle. The stages are as follows:I. Protection II. Application III. Creation IV. Disposal Arrange them in the correct order',
                'type' => 'multiple_choice',
                'options' => [
                    'A' => 'III, II, I, IV',
                    'B' => 'I, II, III, IV',
                    'C' => 'II, I, III, IV',
                    'D' => 'III, I, II, IV'
                ],
                'correct_answer' => 'A',
                'points' => 1,
            ],
            [
                'question' => 'Those responsible for setting policies in a company are the',
                'type' => 'multiple_choice',
                'options' => [
                    'A' => 'secretaries',
                    'B' => 'chief executive officer',
                    'C' => 'managing director',
                    'D' => 'board of directors'
                ],
                'correct_answer' => 'B',
                'points' => 1,
            ],
            [
                'question' => 'To get an overview of the structure of a corporation, you would read an _____',
                'type' => 'multiple_choice',
                'options' => [
                    'A' => 'problem statement',
                    'B' => 'business policy',
                    'C' => 'organization chart',
                    'D' => 'mission statement'
                ],
                'correct_answer' => 'C',
                'points' => 1,
            ],
            [
                'question' => 'What is the correct order for business meeting preparation?',
                'type' => 'multiple_choice',
                'options' => [
                    'A' => 'V, III, VIII, I, VII, II, VI, IV',
                    'B' => 'III, V, VIII, I, VII, II, VI, IV',
                    'C' => 'I, V, III, VIII, VII, II, VI, IV',
                    'D' => 'VIII, I, V, III, VII, II, VI, IV'
                ],
                'correct_answer' => 'B',
                'points' => 1,
            ],
            [
                'question' => 'What type of business meeting is useful for moring the progress of team projects?',
                'type' => 'multiple_choice',
                'options' => [
                    'A' => 'Informational meetings',
                    'B' => 'Problem-solving meetings',
                    'C' => 'Status update meetings',
                    'D' => 'Team-building meetings'
                ],
                'correct_answer' => 'C',
                'points' => 1,
            ],
            [
                'question' => 'Which of the following business meeting offers an alternative to lengthy company memos or emails?',
                'type' => 'multiple_choice',
                'options' => [
                    'A' => 'Informational meetings',
                    'B' => 'Problem-solving meetings',
                    'C' => 'Status update meetings',
                    'D' => 'Team-building meetings'
                ],
                'correct_answer' => 'A',
                'points' => 1,
            ],
            [
                'question' => 'Which of the following is not an example of company\'s record?',
                'type' => 'multiple_choice',
                'options' => [
                    'A' => 'Emails relating to activity',
                    'B' => 'final annual reports',
                    'C' => 'Office technology',
                    'D' => 'Balance sheets'
                ],
                'correct_answer' => 'C',
                'points' => 1,
            ],
            [
                'question' => 'Which of the following statement is not true?',
                'type' => 'multiple_choice',
                'options' => [
                    'A' => 'A customer-focused brand places the customer experience as the most important part of doing business',
                    'B' => 'Customer centricity is an organizational mindset that places customers (rather than product or sales) at the center of the business.',
                    'C' => 'A customer-centric organization always maintain low profit margin',
                    'D' => 'Customer-centric is a business strategy that\'s based on putting your customer first and at the core of your business in order to provide a positive experience and build long-term relationships.'
                ],
                'correct_answer' => 'C',
                'points' => 1,
            ],
        ];

        foreach ($questions as $questionData) {
            QuestionPool::updateOrCreate(
                [
                    'course_id' => $caoCourse->id,
                    'question' => $questionData['question']
                ],
                array_merge($questionData, ['course_id' => $caoCourse->id])
            );
        }

        $this->command->info('Question Pool seeded successfully!');
        $this->command->info('Total questions created: ' . count($questions));
    }
}
