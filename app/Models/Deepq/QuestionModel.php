<?php

namespace App\Models\Deepq;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static findOrFail(int $id)
 */
class QuestionModel extends Model
{
    use HasFactory;

    /** @var string  */
    protected $table = 'deepq_keyword_questions';

    /** @var string[]  */
    protected $fillable = [
        'keyword_id',
        'question',
        'sort',
        'created_user',
        'updated_user',
    ];
}
