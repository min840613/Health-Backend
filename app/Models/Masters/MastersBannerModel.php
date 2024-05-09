<?php

namespace App\Models\Masters;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Masters\InstitutionsModel;
use App\Models\Masters\DivisionsModel;
use App\Models\Masters\MastersModel;

class MastersBannerModel extends Model
{
    use HasFactory;
    protected $table = 'health_master_banner';

    protected $casts = [
        'published_at' => 'datetime:Y-m-d H:i',
        'published_end' => 'datetime:Y-m-d H:i'
    ];

    protected $guarded = [];

    public function institution(): BelongsTo
    {
        return $this->belongsTo(InstitutionsModel::class, 'institution_id', 'id');
    }

    public function division(): BelongsTo
    {
        return $this->belongsTo(DivisionsModel::class, 'division_id', 'id');
    }

    public function master(): BelongsTo
    {
        return $this->belongsTo(MastersModel::class, 'master_id', 'id');
    }

    public function scopeActive($query)
    {
        $query->where('status', 1)
                ->where('published_end', '>', Carbon::now());
    }

    public function scopeInactive($query)
    {
        $query->whereNot(function ($q) {
            $q->where('status', '=', 1)
                ->where('published_end', '>', Carbon::now());
        });
    }

    public function getStatusCssAttribute(): string
    {
        if($this->status == 1 && $this->published_at < Carbon::now() && $this->published_end > Carbon::now()){
            return '<i style="color: green;" class="fa fa-check"></i>';
        }else{
            return '<i style="color: #b80000;" class="fa fa-times"></i>';
        }
    }

    public function getTypeWordingAttribute(): string
    {
        if($this->type == 0){
            return '內部連結';
        }
        if($this->type == 1){
            return '外部連結';
        }
    }
}
