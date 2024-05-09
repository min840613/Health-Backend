<?php

namespace App\Models\Encyclopedia;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BodyModel extends Model
{
    use HasFactory;

    protected $table = 'health_body';
    protected $fillable = [
        'status',
        'en_name',
        'name',
        'sort',
        'created_user',
        'updated_user',
    ];

    /**
     * @return HasMany
     */
    public function organs(): HasMany
    {
        return $this->hasMany(OrgansModel::class, 'body_id', 'id');
    }
}
