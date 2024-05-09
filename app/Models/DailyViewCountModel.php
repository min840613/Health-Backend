<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyViewCountModel extends Model
{
    use HasFactory;

    /** @var string  */
    protected $table = 'daily_views_count';

    /** @var string  */
    protected $primaryKey = 'id';

    public $timestamps = false;
    /** @var string[]  */
    protected $fillable = [
        'date',
        'source_type',
        'source_id',
        'click_count',
    ];

}
