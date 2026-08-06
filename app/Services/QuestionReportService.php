<?php

namespace App\Services;

final class QuestionReportService
{
    /**
     * @param array{submitted_attempts: int, questions: list<array<string, mixed>>} $questionReport
     * @return array{questions: list<array<string, mixed>>, summary: array<string, int|float>, hardest_question: array<string, mixed>|null, sort: string}
     */
    public function prepare(array $questionReport, mixed $requestedSort): array
    {
        $sort = (string) $requestedSort;
        $sort = in_array($sort, ['wrong', 'correct', 'order'], true) ? $sort : 'wrong';
        $summary = [
            'submitted_attempts' => (int) ($questionReport['submitted_attempts'] ?? 0),
            'total_answers'      => 0,
            'correct_answers'    => 0,
            'wrong_answers'      => 0,
            'unanswered'         => 0,
            'correct_rate'       => 0.0,
        ];
        $hardestQuestion = null;

        foreach ($questionReport['questions'] as $question) {
            $summary['total_answers'] += (int) $question['answered_count'];
            $summary['correct_answers'] += (int) $question['correct_count'];
            $summary['wrong_answers'] += (int) $question['wrong_count'];
            $summary['unanswered'] += (int) $question['unanswered_count'];

            if ((int) $question['wrong_count'] > 0 && ($hardestQuestion === null
                || (float) $question['wrong_rate'] > (float) $hardestQuestion['wrong_rate']
                || ((float) $question['wrong_rate'] === (float) $hardestQuestion['wrong_rate']
                    && (int) $question['wrong_count'] > (int) $hardestQuestion['wrong_count']))) {
                $hardestQuestion = $question;
            }
        }

        $summary['correct_rate'] = $summary['total_answers'] > 0
            ? round(($summary['correct_answers'] / $summary['total_answers']) * 100, 1)
            : 0.0;

        $questions = $questionReport['questions'];
        usort($questions, static function (array $first, array $second) use ($sort): int {
            if ($sort === 'order') {
                return (int) $first['sort_order'] <=> (int) $second['sort_order'];
            }

            $countField = $sort === 'correct' ? 'correct_count' : 'wrong_count';
            $rateField = $sort === 'correct' ? 'correct_rate' : 'wrong_rate';

            return ((int) $second[$countField] <=> (int) $first[$countField])
                ?: ((float) $second[$rateField] <=> (float) $first[$rateField])
                ?: ((int) $first['sort_order'] <=> (int) $second['sort_order']);
        });

        return [
            'questions'        => $questions,
            'summary'          => $summary,
            'hardest_question' => $hardestQuestion,
            'sort'             => $sort,
        ];
    }
}
