<?php

namespace App\Models\Articles;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EndTextModel extends Model
{
    use HasFactory;
    protected $table = 'health_end_of_text';
    protected $primaryKey = 'text_id';
    protected $fillable = [
        'text_type',
        'short_title',
        'url',
        'content',
        'order_num',
        'status',
        'created_user',
        'updated_user',
        'published_at',
        'published_end'
    ];

    public function getTextTypeWordingAttribute(): string
    {
        if($this->text_type == 1){
            return '警語';
        }
        if($this->text_type == 2){
            return '廣宣';
        }
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

    public function getPublishedAtAttribute($value)
    {
        if($value){
            return Carbon::parse($value)->format('Y-m-d H:i');
        }
    }

    public function getPublishedEndAttribute($value)
    {
        if($value){
            return Carbon::parse($value)->format('Y-m-d H:i');
        }
    }
}
