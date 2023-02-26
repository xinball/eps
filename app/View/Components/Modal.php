<?php
/*
 * @Author: XinBall
 * @LastEditors: XinBall
 */

namespace App\View\Components;

use Illuminate\View\Component;
//拟态框
class Modal extends Component
{
    public $id;
    public $title;
    public $class;
    public $footer;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($id="",$title="",$class="",$footer="")
    {
        $this->id=$id;
        $this->title=$title;
        $this->class=$class;
        $this->footer=$footer;
        //
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('components.modal');
    }
}
