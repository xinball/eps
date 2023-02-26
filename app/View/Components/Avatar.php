<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Avatar extends Component
{
    // public $editable;
    // public $huser;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    // public function __construct($editable,$huser)
    // {
    //     //
    //     $this->editable=$editable;
    //     $this->huser=$huser;
    // }
    public function __construct()
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('components.avatar');
    }
}
