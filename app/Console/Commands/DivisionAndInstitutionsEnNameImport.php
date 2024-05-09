<?php

namespace App\Console\Commands;

use App\Models\Masters\DivisionsModel;
use App\Models\Masters\InstitutionsModel;
use Illuminate\Console\Command;

class DivisionAndInstitutionsEnNameImport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'division_institutions_en_name:import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '匯入科別及院所的英文名稱';

    /** @var string[] */
    private $divisionsMapping = [
        'all' => '科別總覽',
        'med' => '內科',
        'surg' => '外科',
        'neuro' => '神經科',
        'ns' => '神經外科',
        'oph' => '眼科',
        'ent' => '耳鼻喉科',
        'dent' => '牙科',
        'ortho' => '骨科',
        'psy' => '精神科',
        'rehab' => '復健科',
        'obgyn' => '婦產科',
        'derma' => '皮膚科',
        'ped' => '兒科',
        'uro' => '泌尿科',
        'fm' => '家庭醫學科',
        'em' => '急診醫學科',
        'ps' => '整形外科',
        'anes' => '麻醉科',
        'rd' => '放射診斷科',
        'onco' => '放射腫瘤科',
        'om' => '職業醫學科',
        'nm' => '核子醫學科',
        'path' => '解剖病理科',
        'tcm' => '中醫科',
    ];

    /** @var array */
    private $institutionsMapping = [
        'ntuh' => '台大醫院',
        'vghtpe' => '台北榮民總醫院',
        'tsgh' => '三軍總醫院',
        'wf' => '萬芳醫院',
        'vghtc' => '台中榮民總醫院',
        'nckuh' => '成大醫院',
        'vghks' => '高雄榮民總醫院',
        'mmh' => '台北馬偕醫院',
        'mmhts' => '淡水馬偕醫院',
        'cgmhtp' => '台北長庚醫院',
        'cgh' => '國泰醫院',
        'femh' => '亞東醫院',
        'skh' => '新光醫院',
        'mmhc' => '馬偕兒童醫院',
        'cgmhlk' => '林口長庚醫院',
        'cch' => '彰化基督教醫院',
        'csmuh' => '中山附醫',
        'cmuh' => '中醫大附醫',
        'cmuch' => '中醫大兒童醫院',
        'chimei' => '奇美醫院',
        'kmuh' => '高醫大附醫',
        'cgmhks' => '高雄長庚醫院',
        'tzuchihl' => '花蓮慈濟醫院',
        'ntuhyl' => '台大醫院雲林分院',
        'ntuhhc' => '台大醫院新竹分院',
        'cgmhtc' => '土城長庚醫院',
        'cgmhty' => '桃園長庚醫院',
        'cchc' => '彰化基督教兒童醫院',
        'cgmhkl' => '基隆長庚醫院',
        'cghhc' => '新竹國泰醫院',
        'chimeily' => '柳營奇美醫院',
    ];

    /** @var array 找不到的科別 */
    private $notfoundDivisions = [];

    /** @var array 找不到的院所 */
    private $notfoundInstitutions = [];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        \DB::transaction(function () {
            $this->divisionUpdate();
            $this->institutionUpdate();
        });

        dump($this->notfoundDivisions, $this->notfoundInstitutions);
    }

    /**
     * @return void
     * @throws \Exception
     */
    private function divisionUpdate(): void
    {
        $divisions = DivisionsModel::all();

        foreach ($this->divisionsMapping as $enName => $name) {
            $model = $divisions->where('name', $name)->first();

            if ($model === null) {
                $this->notfoundDivisions[] = $name;
                continue;
            }

            $model->en_name = $enName;
            $model->save();
        }
    }

    /**
     * @return void
     * @throws \Exception
     */
    private function institutionUpdate(): void
    {
        $institution = InstitutionsModel::all();

        foreach ($this->institutionsMapping as $enName => $name) {
            $model = $institution->where('nick_name', $name)->first();

            if ($model === null) {
                $this->notfoundInstitutions[] = $name;
                continue;
            }

            $model->en_name = $enName;
            $model->save();
        }
    }
}
