@extends('user.master')

@section('title','个人日志')
@section('nextCSS2')

@endsection

@section('main')
@include('template.operationlist',['utype'=>'u'])
<!-- <div class="accordion" id="accordionOperation">
  <div class="accordion-item">
    <h2 class="accordion-header" id="operation+">
      <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseOne" aria-expanded="true" aria-controls="panelsStayOpen-collapseOne">
        
      </button>
    </h2>
    <div id="panelsStayOpen-collapseOne" class="accordion-collapse collapse show" aria-labelledby="panelsStayOpen-headingOne">
      <div class="accordion-body">
        
      </div>
    </div>
  </div>
</div> -->

@endsection

@section('nextJS')
@endsection
