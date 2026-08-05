<?php

namespace App\Services;

use App\Models\QuestionModel;
use App\Models\QuestionOptionModel;
use RuntimeException;

final class QuestionService
{
    private QuestionModel $questions;

    private QuestionOptionModel $options;

    public function __construct()
    {
        $this->questions = model(QuestionModel::class);
        $this->options = model(QuestionOptionModel::class);
    }

    public function create(array $question, array $options, int $correctIndex): int
    {
        $database = db_connect();
        $database->transStart();

        $questionId = (int) $this->questions->insert($question, true);
        $this->options->insertBatch($this->optionRows($questionId, $options, $correctIndex));

        $database->transComplete();

        if (! $database->transStatus()) {
            throw new RuntimeException('Pertanyaan gagal disimpan.');
        }

        return $questionId;
    }

    public function update(int $questionId, array $question, array $options, int $correctIndex): void
    {
        $database = db_connect();
        $database->transStart();

        $this->questions->update($questionId, $question);
        $this->options->where('question_id', $questionId)->delete();
        $this->options->insertBatch($this->optionRows($questionId, $options, $correctIndex));

        $database->transComplete();

        if (! $database->transStatus()) {
            throw new RuntimeException('Pertanyaan gagal diperbarui.');
        }
    }

    /** @return list<array<string, mixed>> */
    private function optionRows(int $questionId, array $options, int $correctIndex): array
    {
        $rows = [];

        foreach ($options as $index => $optionText) {
            $rows[] = [
                'question_id' => $questionId,
                'option_key'   => chr(65 + $index),
                'option_text'  => $optionText,
                'is_correct'   => $index === $correctIndex ? 1 : 0,
                'sort_order'   => $index + 1,
            ];
        }

        return $rows;
    }
}
