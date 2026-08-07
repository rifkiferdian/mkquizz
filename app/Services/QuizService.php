<?php

namespace App\Services;

use App\Models\QuizModel;
use RuntimeException;

final class QuizService
{
    public function create(array $quiz, array $questions): int
    {
        $database = db_connect();
        $database->transStart();

        $quizId = (int) model(QuizModel::class)->insert($quiz, true);
        $rows = [];

        foreach ($questions as $index => $question) {
            $rows[] = [
                'quiz_id'     => $quizId,
                'question_id' => (int) $question['id'],
                'score'       => (float) $question['default_score'],
                'sort_order'  => $index + 1,
            ];
        }

        $database->table('quiz_questions')->insertBatch($rows);
        $database->transComplete();

        if (! $database->transStatus()) {
            throw new RuntimeException('Quiz gagal disimpan.');
        }

        return $quizId;
    }

    public function update(int $quizId, array $quiz, array $questions, bool $replaceQuestions): void
    {
        $database = db_connect();
        $database->transStart();

        model(QuizModel::class)->update($quizId, $quiz);

        if ($replaceQuestions) {
            $database->table('quiz_questions')->where('quiz_id', $quizId)->delete();
            $rows = [];

            foreach ($questions as $index => $question) {
                $rows[] = [
                    'quiz_id'     => $quizId,
                    'question_id' => (int) $question['id'],
                    'score'       => (float) $question['default_score'],
                    'sort_order'  => $index + 1,
                ];
            }

            $database->table('quiz_questions')->insertBatch($rows);
        }

        $database->transComplete();

        if (! $database->transStatus()) {
            throw new RuntimeException('Perubahan quiz gagal disimpan.');
        }
    }
}
