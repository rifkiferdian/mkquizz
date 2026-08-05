<?php

namespace App\Models;

use CodeIgniter\Model;

final class QuestionOptionModel extends Model
{
    protected $table = 'question_options';

    protected $primaryKey = 'id';

    protected $returnType = 'array';

    protected $allowedFields = [
        'question_id',
        'option_key',
        'option_text',
        'is_correct',
        'sort_order',
    ];

    protected $useTimestamps = true;
}
