@extends($activeTemplate.'layouts.master')

@section('content')

<div class="row justify-content-center ">
  <div class="col-sm-6 mx-3 col-md-6 col-lg-5 col-xl-5 mb-4 dashboard-item">
    <div class="header-left" style="display: flex; justify-content:space-between">
      <div>
        <h4 class="title">
        <img src="{{asset('assets/Ranks/Master-Rank.png')}}" style="width:35px" alt="">  
        @lang('Master') </h4>
        <h3 class=" ammount theme-one">Pair Earning</h3>
        <h6> <span>@lang('Requirement: 1 Pair')</span></h6>
      </div>
      <img style="width: 100px; margin: 0 " src="{{asset('assets/Rewards/1 Pair.png')}}" alt="">

    </div>
    <?php
    $per = ($pairs / 1) * 100;
    $left = 1 - $pairs;
    ?>
    @if($per < 100) 
    <div class="bg-dark">
      <h3 class="mt-3" style="width:<?php echo intval($per) ?>%; height:10px; background: #0d6efd;"></h3>
      <p class="bg-white">you need {{$left}} more pairs to achive this award</p>
    </div>
    @else
    <div class="bg-dark">
      <h3 class="mt-3" style="width:100%; height:10px; background: #0d6efd;"></h3>
      <p class="bg-white">This award has been achived</p>
    </div>
    @endif
  </div>
  <div class="col-sm-6 mx-3 col-md-6 col-lg-5 col-xl-5 mb-4 dashboard-item">
    <div class="header-left" style="display: flex; justify-content:space-between">
      <div>
        <h4 class="title">
        <img src="{{asset('assets/Ranks/Director-Rank.png')}}" style="width:35px" alt="">   
        @lang('Director') </h4>
        <h3 class=" ammount theme-one">3200 Cash</h3>
        <h6> <span>@lang('Requirement: 32 Pairs')</span></h6>
      </div>
      <img style="width: 100px; margin: 0 " src="{{asset('assets/Rewards/3200 Cash.png')}}" alt="">

    </div>
    <?php
    $per = ($pairs / 32) * 100;
    $left = 32 - $pairs;
    ?>
    @if($per < 100) 
    <div class="bg-dark">
      <h3 class="mt-3" style="width:<?php echo intval($per) ?>%; height:10px; background: #0d6efd;"></h3>
      <p class="bg-white">you need {{$left}} more pairs to achive this award</p>
    </div>
    @else
    <div class="bg-dark">
      <h3 class="mt-3" style="width:100%; height:10px; background: #0d6efd;"></h3>
      <p class="bg-white">This award has been achived</p>
    </div>
    @endif
  </div>
  <div class="col-sm-6 mx-3 col-md-6 col-lg-5 col-xl-5 mb-4 dashboard-item">
    <div class="header-left" style="display: flex; justify-content:space-between">
      <div>
        <h4 class="title">
        <img src="{{asset('assets/Ranks/Executive-Rank.png')}}" style="width:35px" alt="">  
        @lang('Executive') </h4>
        <h3 class=" ammount theme-one">DSP Voucher </h3>
        <h6> <span>@lang('Requirement: 64 Pairs')</span></h6>
      </div>
      <img style="width: 100px; margin: 0 " src="{{asset('assets/Rewards/E-Pin.png')}}" alt="">

    </div>
    <?php
    $per = ($pairs / 64) * 100;
    $left = 64 - $pairs;
    ?>
    @if($per < 100) 
    <div class="bg-dark">
      <h3 class="mt-3" style="width:<?php echo intval($per) ?>%; height:10px; background: #0d6efd;"></h3>
      <p class="bg-white">you need {{$left}} more pairs to achive this award</p>
    </div>
    @else
    <div class="bg-dark">
      <h3 class="mt-3" style="width:100%; height:10px; background: #0d6efd;"></h3>
      <p class="bg-white">This award has been achived</p>
    </div>
    @endif
  </div>
  <div class="col-sm-6 mx-3 col-md-6 col-lg-5 col-xl-5 mb-4 dashboard-item">
    <div class="header-left" style="display: flex; justify-content:space-between">
      <div>
        <h4 class="title">
        <img src="{{asset('assets/Ranks/Chief-Rank.png')}}" style="width:35px" alt="">    
        @lang('Chief') </h4>
        <h3 class=" ammount theme-one">Mobile</h3>
        <h6> <span>@lang('Requirement: 256 Pairs')</span></h6>
		<h6> <span>@lang('Budget = 25,000 PKR')</span></h6>
      </div>
      <img style="width: 100px; margin: 0 " src="{{asset('assets/Rewards/Mobile.png')}}" alt="">

    </div>
    <?php
    $per = ($pairs / 256) * 100;
    $left = 256 - $pairs;
    ?>
    @if($per < 100) 
    <div class="bg-dark">
      <h3 class="mt-3" style="width:<?php echo intval($per) ?>%; height:10px; background: #0d6efd;"></h3>
      <p class="bg-white">you need {{$left}} more pairs to achive this award</p>
    </div>
    @else
    <div class="bg-dark">
      <h3 class="mt-3" style="width:100%; height:10px; background: #0d6efd;"></h3>
      <p class="bg-white">This award has been achived</p>
    </div>
    @endif
  </div>
  <div class="col-sm-6 mx-3 col-md-6 col-lg-5 col-xl-5 mb-4 dashboard-item">
    <div class="header-left" style="display: flex; justify-content:space-between">
      <div>
        <h4 class="title">
        <img src="{{asset('assets/Ranks/Royal Mentor-Rank.png')}}" style="width:35px" alt="">    
        @lang('Royal Mentor') </h4>
        <h3 class=" ammount theme-one">Laptop</h3>
        <h6> <span>@lang('Requirement: 1024 Pairs')</span></h6>
		<h6> <span>@lang('Budget = 1 Lac Rupees')</span></h6>
      </div>
      <img style="width: 100px; margin: 0 " src="{{asset('assets/Rewards/Laptop.png')}}" alt="">

    </div>
    <?php
    $per = ($pairs / 1024) * 100;
    $left = 1024 - $pairs;
    ?>
    @if($per < 100) 
    <div class="bg-dark">
      <h3 class="mt-3" style="width:<?php echo intval($per) ?>%; height:10px; background: #0d6efd;"></h3>
      <p class="bg-white">you need {{$left}} more pairs to achive this award</p>
    </div>
    @else
    <div class="bg-dark">
      <h3 class="mt-3" style="width:100%; height:10px; background: #0d6efd;"></h3>
      <p class="bg-white">This award has been achived</p>
    </div>
    @endif
  </div>
  <div class="col-sm-6 mx-3 col-md-6 col-lg-5 col-xl-5 mb-4 dashboard-item">
    <div class="header-left" style="display: flex; justify-content:space-between">
      <div>
        <h4 class="title">
        <img src="{{asset('assets/Ranks/Ambassador-Rank.png')}}" style="width:35px" alt="">    
        @lang('Ambassador') </h4>
        <h3 class=" ammount theme-one">Home Appliances</h3>
        <h6> <span>@lang('Requirement: 8192 Pairs')</span></h6>
		<h6> <span>@lang('Budget = 1 Million Rupees')</span></h6>
      </div>
      <img style="width: 100px; margin: 0 " src="{{asset('assets/Rewards/Electronics.png')}}" alt="">

    </div>
    <?php
    $per = ($pairs / 8192) * 100;
    $left = 8192 - $pairs;
    ?>
    @if($per < 100) 
    <div class="bg-dark">
      <h3 class="mt-3" style="width:<?php echo intval($per) ?>%; height:10px; background: #0d6efd;"></h3>
      <p class="bg-white">you need {{$left}} more pairs to achive this award</p>
    </div>
    @else
    <div class="bg-dark">
      <h3 class="mt-3" style="width:100%; height:10px; background: #0d6efd;"></h3>
      <p class="bg-white">This award has been achived</p>
    </div>
    @endif
  </div>
  <div class="col-sm-6 mx-3 col-md-6 col-lg-5 col-xl-5 mb-4 dashboard-item">
    <div class="header-left" style="display: flex; justify-content:space-between">
      <div>
        <h4 class="title">
        <img src="{{asset('assets/Ranks/King-Rank.png')}}" style="width:35px" alt="">    
        @lang('King') </h4>
        <h3 class=" ammount theme-one">Corolla X</h3>
        <h6> <span>@lang('Requirement: 16384 Pairs')</span></h6>
		<h6> <span>@lang('Budget = 4 Million Rupees')</span></h6>
      </div>
      <img style="width: 100px; margin: 0 " src="{{asset('assets/Rewards/Car 1.png')}}" alt="">

    </div>
    <?php
    $per = ($pairs / 16384) * 100;
    $left = 16384 - $pairs;
    ?>
    @if($per < 100) 
    <div class="bg-dark">
      <h3 class="mt-3" style="width:<?php echo intval($per) ?>%; height:10px; background: #0d6efd;"></h3>
      <p class="bg-white">you need {{$left}} more pairs to achive this award</p>
    </div>
    @else
    <div class="bg-dark">
      <h3 class="mt-3" style="width:100%; height:10px; background: #0d6efd;"></h3>
      <p class="bg-white">This award has been achived</p>
    </div>
    @endif
  </div>
  <div class="col-sm-6 mx-3 col-md-6 col-lg-5 col-xl-5 mb-4 dashboard-item">
    <div class="header-left" style="display: flex; justify-content:space-between">
      <div>
        <h4 class="title">
        <img src="{{asset('assets/Ranks/Emperor-Rank.png')}}" style="width:35px" alt="">    
        @lang('Emperor') </h4>
        <h3 class=" ammount theme-one">MG ZS EV</h3>
        <h6> <span>@lang('Requirement: 32768 Pairs')</span></h6>
		<h6> <span>@lang('Budget = 6.2 Million Rupees')</span></h6>
      </div>
      <img style="width: 100px; margin: 0 " src="{{asset('assets/Rewards/Car 2.png')}}" alt="">

    </div>
    <?php
    $per = ($pairs / 32768) * 100;
    $left = 32768 - $pairs;
    ?>
    @if($per < 100) 
    <div class="bg-dark">
      <h3 class="mt-3" style="width:<?php echo intval($per) ?>%; height:10px; background: #0d6efd;"></h3>
      <p class="bg-white">you need {{$left}} more pairs to achive this award</p>
    </div>
    @else
    <div class="bg-dark">
      <h3 class="mt-3" style="width:100%; height:10px; background: #0d6efd;"></h3>
      <p class="bg-white">This award has been achived</p>
    </div>
    @endif
  </div>

</div>

@endsection