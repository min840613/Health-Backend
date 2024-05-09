<?php

namespace App\Models\Encyclopedia;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class SicknessToOrganModel extends Pivot
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'health_sickness_to_organ';
    protected $fillable = [
        'sickness_id',
        'organ_id'
    ];


    public function SicknessToOrgan()
    {
        return $this->hasOne(SicknessModel::class, 'id', 'sickness_id');
    }
}
