<?php

namespace App\Models\Deepq;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @method static findOrFail(int $id)
 */
class KeywordModel extends Model
{
    use HasFactory;
    use SoftDeletes;

    /** @var string  */
    protected $table = 'deepq_keywords';

    /** @var string[]  */
    protected $fillable = [
        'keyword',
        'start_at',
        'end_at',
        'count',
        'created_user',
        'updated_user',
    ];

    /**
     * @return HasMany
     */
    public function questions(): HasMany
    {
        return $this->hasMany(QuestionModel::class, 'keyword_id', 'id');
    }

    public function scopePublished($query)
    {
        $now = Carbon::now();

        $query->where(function ($query) use ($now) {
            $query->where('start_at', '<=', $now);
            $query->where('end_at', '>=', $now);
        });
    }
}
