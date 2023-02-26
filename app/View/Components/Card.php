<?php
/*
 * @Author: XinBall
 * @LastEditors: XinBall
 */

namespace App\View\Components;

use Illuminate\View\Component;

class Card extends Component
{
    public $header;
    public $title;
    public $class;
    public $style;
    public $icon;
    public $id;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($header="",$title="",$icon="",$class="text-dark bg-light",$style="",$id="")
    {
        $this->header=$header;
        $this->icon=$icon;
        $this->title=$title;
        $this->class=$class;
        $this->style=$style;
        $this->id=$id;
        //
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('components.card');
    }
}
