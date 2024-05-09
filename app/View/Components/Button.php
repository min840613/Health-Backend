<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Exception;

class Button extends Component
{

    public $color;
    public $type;
    public $name;
    public $addClass;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($name, $type, $addClass = '')
    {
        $this->type = $type;
        $this->name = $name;
        $this->addClass = $addClass;

        $color = match ($name) {
            '儲存排序', '儲存發布','編輯','首頁頭條','推波', '新增' => 'btn-primary',
            '查詢' => 'btn-success',
            '取消查詢' => 'btn-outline-success',
            '預覽' => 'btn-secondary',
            default => throw new Exception('您好，請使用限定名稱，請參考來源 App\View\Components\Button的$color'),
        };


        $this->color = $color;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.button');
    }
}
