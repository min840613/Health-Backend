<?php

namespace App\Models\Authors;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuthorsModel extends Model
{
    use HasFactory;

    protected $table = 'health_authors';

    protected $fillable = [
        'name',
        'status',
        'created_user',
        'updated_user'
    ];

    /**
     * @param $query
     */
    public function scopeActive($query)
    {
        $query->where('status', 1);
    }

    public function getStatusCssAttribute(): string
    {
        if($this->status == 0){
            return '<i style="color: #b80000;" class="fa fa-times"></i>';
        }
        if($this->status == 1){
            return '<i style="color: green;" class="fa fa-check"></i>';
        }
    }
}
