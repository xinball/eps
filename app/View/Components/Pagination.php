<?php
/*
 * @Author: XinBall
 * @LastEditors: XinBall
 */

namespace App\View\Components;

use Illuminate\View\Component;

class Pagination extends Component
{
    public $current_page;//
    public $last_page;//
    public $per_page;

    public $from;
    public $to;
    public $total;

    public $path;//
    public $first_page_url;
    public $last_page_url;
    public $prev_page_url;
    public $next_page_url;

    public $pageNum;
    public $preCount;
    public $nextCount;
    public $pageLeng;
    


    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($current_page,$last_page,$per_page,$from,$to,$total,$path,$first_page_url,$last_page_url,$next_page_url,$pageNum=5,$pageSize)
    {
        $this->current_page=$current_page;
        $this->last_page=$last_page;
        $this->per_page=$per_page;
        $this->from=$from;
        $this->to=$to;
        $this->total=$total;
        $this->path=$path;
        $this->first_page_url=$first_page_url;
        $this->last_page_url=$last_page_url;
        $this->next_page_url=$next_page_url;
        $this->pageNum=$pageNum;

        $this->pageLeng=(int)($this->pageNum/2);
        $this->preCount=$this->current_page>$this->pageLeng?$this->current_page-$this->pageLeng:1;
        $this->nextCount=$this->preCount+$this->pageNum-1<$this->last_page?$this->preCount+$this->pageNum-1:$this->last_page;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('components.pagination');
    }
}
