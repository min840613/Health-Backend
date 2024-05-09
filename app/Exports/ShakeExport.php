<?php

namespace App\Exports;

use App\Models\App\ShakeMemberModel;
use App\Models\App\ShakeModel;
use Illuminate\Support\Collection;
use Facades\App\Helpers\AesHelper;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Support\Facades\Http;

/**
 * Class ShakeExport
 * @package App\Exports
 */
class ShakeExport implements FromCollection
{
    /** @var */
    protected $data;

    /**
     * ShakeExport constructor.
     * @param $data
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * @return Collection
     */
    public function collection(): Collection
    {
        return new Collection($this->createData());
    }

    /**
     * @return array
     */
    public function createData(): array
    {
        $items[] = ['活動ID', '活動名稱', 'OS', 'EMAIL', '使用者參加時間', '搖一搖狀態', '會員ID'];
        $this->data->each(function (ShakeModel $model) use (&$items) {
            $model->members->each(function (ShakeMemberModel $member) use ($model, &$items) {

                if (
                    preg_match('/^[0-9a-fA-F]+$/', $member->xnmail) &&
                    preg_match('/^[0-9a-fA-F]+$/', $member->xnm_id) &&
                    strlen($member->xnmail) % 2 == 0 &&
                    strlen($member->xnm_id) % 2 == 0
                ) {
                    $items[] = [
                        $model->shake_id,
                        $model->shake_title,
                        $member->xnphone,
                        AesHelper::decrypt($member->xnmail),
                        $member->xn_time_activity,
                        $member->shake_status_string,
                        AesHelper::decrypt($member->xnm_id),
                    ];
                }
            });
        });

        return $items;
    }
}
