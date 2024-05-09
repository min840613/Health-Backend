<?php

namespace App\Repositories;

use App\Models\ThirdPartyFeed\YahooRssModel;
use Illuminate\Support\Collection;

/**
* Class YahooRssRepository
* @package App\Repositories
*/
class YahooRssRepository
{
    /** @var YahooRssModel */
    private YahooRssModel $model;

    /**
    * YahooRssModel constructor.
    * @param YahooRssModel $model
    */
    public function __construct(YahooRssModel $model)
    {
        $this->model = $model;
    }

    public function getRssToday(string $date, int $limit = 7): Collection
    {

    }

}
