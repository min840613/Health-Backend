<?php

namespace App\Models\App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppActivitiesModel extends Model
{
    use HasFactory;
    protected $table = 'health_app_activities';
    protected $fillable = [
        'title',
        'type_url',
        'url',
        'articles_id',
        'link',
        'released',
        'start',
        'end',
        'status',
        'sort',
        'created_user',
        'updated_user'
    ];

    public function getStatusCssAttribute(): string
    {
        if($this->status == 1 && $this->start < date('Y-m-d H:i:s') && $this->end > date('Y-m-d H:i:s')){
            return '<i style="color: green;" class="fa fa-check"></i>';
        }else{
            return '<i style="color: #b80000;" class="fa fa-times"></i>';
        }
    }

    public function getTypeUrlWordingAttribute(): string
    {
        $option = [
            2 => '輸入URL',
            1 => '文章ID',
            3 => '策展內開',
            4 => '策展外開'
        ];
        return $option[$this->type_url];
    }
}
