<?php

namespace App\Models\HomeArea;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class MeasureModel extends Model
{
    use HasFactory;
    protected $table = 'measure';
    protected $primaryKey = 'id';
    protected $fillable = [
        'title',
        'link',
        'image',
        'start',
        'end',
        'status',
        'sort',
        'created_user',
        'updated_user'
    ];

    public function getStatusCssAttribute(): string
    {
        if($this->status == 0){
            return '<i style="color: #b80000;" class="fa fa-times"></i>';
        }
        if($this->status == 1){
            return '<i style="color: green;" class="fa fa-check"></i>';
        }
    }

    public function scopeActive($query)
    {
        $query->where('status', 1)
               ->where('start', '<=', Carbon::now())
               ->where('end', '>=', Carbon::now());
    }
}
