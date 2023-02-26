<?php

namespace App\Library;

class Pager
{
    public $arr;//显示数据
    public $rowCount;//数据库获取

    public $pageSize;
    public $pageCount;//计算所得

    public $pageNow;//用户指定
    public $pageNum;

    /**
     * Pager constructor.
     * @param $pageNow
     * @param $navigation
     **/
    public function __construct($pageNow,$pageSize=8,$pageNum=5)
    {
        $this->pageNow = $pageNow;
        $this->pageSize = $pageSize;
        $this->pageNum = $pageNum;
    }
    public function echoMessage($error,$successFlag=1,$psFlag=1):string
    {
        if($this->pageCount==0){
            return MessageBox::echoWarning($error);
        }
        $ps="";
        if($this->pageCount>$this->pageNum&&$psFlag)
            $ps="<br/>页面太多了？底部页码处输入页数快速到达ლ(╹◡╹ლ)！";
        if($successFlag)
            return MessageBox::echoSuccess('我们为您找到了 <strong>'.$this->rowCount.'</strong> 条符合条件的记录ヾ(^∀^)ﾉ<br/>'.$ps);
        else
            return "";
    }
    public function echoNavigation($navigation): string
    {
        if($this->pageCount==0){
            return "";
        }
        $this->pageNow=$this->pageNow>$this->pageCount?$this->pageCount:$this->pageNow;
        $pagination="";
        $pagenum1=(int)($this->pageNum/2);
        $preCount=$this->pageNow>$pagenum1?$this->pageNow-$pagenum1:1;
        $nextCount=$preCount+$this->pageNum-1<$this->pageCount?$preCount+$this->pageNum-1:$this->pageCount;
        if($preCount-$pagenum1-1>0)
            $pagination.='<li class="page-item"><a class="page-link" href="'.$navigation.($preCount-$pagenum1-1).'"><span aria-hidden="true">&laquo;</span></a></li>';
        else if($this->pageNow==1)
            $pagination.='<li class="page-item disabled"><a class="page-link"  href="#" tabindex="-1" aria-disabled="true"><span aria-hidden="true">&laquo;</span></a></li>';
        else
            $pagination.='<li class="page-item"><a class="page-link" href="'.$navigation.'1"><span aria-hidden="true">&laquo;</span></a></li>';
        for($i=$preCount;$i<$this->pageNow;++$i){
            $pagination.='<li class="page-item"><a class="page-link" href="'.$navigation.$i.'">'.$i.'</a></li>';
        }
        $pagination.='<li class="page-item active" aria-current="page"><span><label><input id="pageTo" type="number" value="'.$i.'" min="1" max="'.$this->pageCount.'" />/<span style="font-size:12px;">'.$this->pageCount.'</span></label><span></li>';
        for($i=$this->pageNow+1;$i<=$nextCount;++$i){
            $pagination.='<li class="page-item"><a class="page-link" href="'.$navigation.$i.'">'.$i.'</a></li>';
        }
        if($nextCount+$pagenum1+1<$this->pageCount)
            $pagination.='<li class="page-item"><a class="page-link" href="'.$navigation.($nextCount+$pagenum1+1).'"><span aria-hidden="true">&raquo;</span></a></li>';
        else if($this->pageNow==$this->pageCount)
            $pagination.='<li class="page-item disabled"><a class="page-link" href="#" tabindex="-1" aria-disabled="true"><span aria-hidden="true">&raquo;</span></a></li>';
        else
            $pagination.='<li class="page-item"><a class="page-link" href="'.$navigation.$this->pageCount.'"><span aria-hidden="true">&raquo;</span></a></li>';


        return
        '<nav aria-label="Page navigation">
              <ul class="pagination justify-content-center">
              '.$pagination.'
              </ul>
         </nav>
         <script>
        $("#pageTo").keydown(function(e){
            if (e.which === 13) {
                window.location.href="'.$navigation.'"+$("#pageTo").val();
            }
        });
        </script>';
    }

    public function echoNavPage($navigation,$funStr): string
    {
        if($this->pageCount==0){
            return "";
        }
        $this->pageNow=$this->pageNow>$this->pageCount?$this->pageCount:$this->pageNow;
        $pagination="";
        $pagenum1=(int)($this->pageNum/2);
        $preCount=$this->pageNow>$pagenum1?$this->pageNow-$pagenum1:1;
        $nextCount=$preCount+$this->pageNum-1<$this->pageCount?$preCount+$this->pageNum-1:$this->pageCount;
        if($preCount-$pagenum1-1>0)
            $pagination.='<li class="page-item"><a class="page-link" onclick="return '.$funStr.'(this);" href=\''.$navigation.($preCount-$pagenum1-1).'\'><<</a></li>';
        else if($this->pageNow==1)
            $pagination.='<li class="page-item disabled"><a class="page-link" href="#" tabindex="-1" aria-disabled="true"><span aria-hidden="true">&laquo;</span></a></li>';
        else
            $pagination.='<li class="page-item"><a class="page-link" onclick="return '.$funStr.'(this);" href=\''.$navigation.'1\'><<</a></li>';
        for($i=$preCount;$i<$this->pageNow;++$i){
            $pagination.='<li class="page-item"><a class="page-link" onclick="return '.$funStr.'(this);" href=\''.$navigation.$i.'\'>'.$i.'</a></li>';
        }
        $pagination.='<li class="page-item active" aria-current="page"><span><label><input id="pageTo" type="number" value="'.$i.'" min="1" max="'.$this->pageCount.'" />/<span style="font-size:12px;">'.$this->pageCount.'</span></label><span></li>';
        for($i=$this->pageNow+1;$i<=$nextCount;++$i){
            $pagination.='<li class="page-item"><a class="page-link" onclick="return '.$funStr.'(this);" href=\''.$navigation.$i.'\'>'.$i.'</a></li>';
        }
        if($nextCount+$pagenum1+1<$this->pageCount)
            $pagination.='<li class="page-item"><a class="page-link" onclick="return '.$funStr.'(this);" href=\''.$navigation.($nextCount+$pagenum1+1).'\'>>></a></li>';
        else if($this->pageNow==$this->pageCount)
            $pagination.='<li class="page-item disabled"><a class="page-link" href="#" tabindex="-1" aria-disabled="true"><span aria-hidden="true">&raquo;</span></a></li>';
        else
            $pagination.='<li class="page-item"><a class="page-link" onclick="return '.$funStr.'(this);" href=\''.$navigation.$this->pageCount.'\'>>></a></li>';

        return
            '<nav aria-label="Page navigation">
              <ul class="pagination justify-content-center">
              '.$pagination.'
              </ul>
         </nav>';
    }
}
