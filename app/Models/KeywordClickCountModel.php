<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KeywordClickCountModel extends Model
{
    use HasFactory;

    /** @var string  */
    protected $table = 'keyword_click_count';

    /** @var string  */
    protected $primaryKey = 'id';

    public $timestamps = false;

    /** @var string[]  */
    protected $fillable = [
        'date',
        'keyword',
        'click_count',
    ];

}
