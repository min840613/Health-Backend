<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class HealthExhibitionListModel extends Model
{
    use HasFactory;

    protected $connection = 'mysql_tvbs_2022';

    /** @var string  */
    protected $table = 'health_exhibition_list';

    /** @var string[]  */
    protected $fillable = [];


    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }

}
