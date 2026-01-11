<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EvaluationQuestion;
use App\Models\User;

class EvaluationQuestionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get Super Admin user
        $superAdmin = User::role('Super Admin')->first();
        $createdBy = $superAdmin ? $superAdmin->id : null;

        $questions = [
            [
                'question' => 'How satisfied are you with your consultant\'s communication and responsiveness?',
                'order' => 1,
                'is_active' => true,
                'created_by' => $createdBy,
            ],
            [
                'question' => 'How would you rate your consultant\'s knowledge and expertise about the application process?',
                'order' => 2,
                'is_active' => true,
                'created_by' => $createdBy,
            ],
            [
                'question' => 'How helpful was your consultant in guiding you through document preparation?',
                'order' => 3,
                'is_active' => true,
                'created_by' => $createdBy,
            ],
            [
                'question' => 'How professional and courteous was your consultant\'s behavior?',
                'order' => 4,
                'is_active' => true,
                'created_by' => $createdBy,
            ],
            [
                'question' => 'How satisfied are you with the overall support provided by your consultant?',
                'order' => 5,
                'is_active' => true,
                'created_by' => $createdBy,
            ],
        ];

        foreach ($questions as $question) {
            EvaluationQuestion::create($question);
        }

        $this->command->info('✓ Default evaluation questions created successfully!');
    }
}

