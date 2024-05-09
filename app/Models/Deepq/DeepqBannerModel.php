<?php

namespace App\Models\Deepq;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeepqBannerModel extends Model
{
    use HasFactory;

    protected $table = 'deepq_banners';

    protected $fillable = [
        'title',
        'image',
        'start',
        'end',
        'status',
        'sort',
        'created_user',
        'updated_user',
    ];

    public function getStatusCssAttribute(): string
    {
        if ($this->status == 1 && $this->start < date('Y-m-d H:i:s') && $this->end > date('Y-m-d H:i:s')) {
            return '<i style="color: green;" class="fa fa-check"></i>';
        } else {
            return '<i style="color: #b80000;" class="fa fa-times"></i>';
        }
    }
}
