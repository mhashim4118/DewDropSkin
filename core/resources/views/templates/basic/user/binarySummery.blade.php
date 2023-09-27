@extends($activeTemplate.'layouts.master')

@section('content')

<div class="row justify-content-center">

    <div class="col-sm-6 col-md-6 col-lg-3 col-xl-3 mb-4">
        <div class="dashboard-item">
            <div class="dashboard-item-header">
                <div class="header-left">
                    <h6 style="width: 200px;" class="title">@lang('Total Free Users')</h6>
                    <h3 class="ammount theme-one"> {{$total_free_users}}</h3>
                </div>
                <div class="icon"><i class="flaticon-tag-1"></i></div>
            </div>
            <div class="dashboard-item-body">
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-md-6 col-lg-3 col-xl-3 mb-4">
        <div class="dashboard-item">
            <div class="dashboard-item-header">
                <div class="header-left">
                    <h6 style="width: 200px;" class="title">@lang('Total Paid Users')</h6>
                    <h3 class="ammount theme-one"> {{$total_paid_users}}</h3>
                </div>
                <div class="icon"><i class="flaticon-tag-1"></i></div>
            </div>
            <div class="dashboard-item-body">
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-md-6 col-lg-3 col-xl-3 mb-4">
        <div class="dashboard-item">
            <div class="dashboard-item-header">
                <div class="header-left">
                    <h6 class="title">@lang('My Total DSP')</h6>
                    <h3 class="ammount theme-one">{{$total_dsp->count()}}</h3>
                </div>
                <div class="icon"><i class="flaticon-tag-1"></i></div>
            </div>
            <div class="dashboard-item-body">
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-md-6 col-lg-3 col-xl-3 mb-4">
        <div class="dashboard-item">
            <div class="dashboard-item-header">
                <div class="header-left">
                    <h6 class="title">@lang('My Store')</h6>
					@if($me->member_ship == 1)
                    <h3 class="ammount theme-one"> Active</h3>
					@else 
					<h3 class="ammount theme-one"> Inactive</h3>
					@endif
                </div>
                <div class="icon"><i class="flaticon-tag-1"></i></div>
            </div>
            <div class="dashboard-item-body">
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-md-6 col-lg-6 col-xl-6 mb-4">
        <div class="dashboard-item">
            <div class="dashboard-item-header">
                <div class="header-left">
                    <h6 width="200" class="title">@lang('Total Earnings')</h6>
                    <h3 style="width:200px" class="ammount theme-one">{{$total_earings}} PKR</h3>
                </div>
                <div class="icon"><i class="flaticon-tag-1"></i></div>
            </div>
            <div class="dashboard-item-body">
            </div>
        </div>
    </div>




    <div class="col-sm-6 col-md-6 col-lg-6 col-xl-6 mb-4">
        <div class="dashboard-item">
            <div class="dashboard-item-header">
                <div class="header-left">
                    <h6 style="width: 200px;" class="title">@lang('Reference Earnings')</h6>
                    <h3 class="ammount theme-one">{{$total_ref_earnings}}</h3>
                </div>
                <div class="icon"><i class="flaticon-tag-1"></i></div>
            </div>
            <div class="dashboard-item-body">
            </div>
        </div>
    </div>

</div>

    <div class="row">
    <div class="col-lg-12">
        <div class="card b-radius--10 overflow-hidden">
            <div class="card-body p-0">
                <div class="table-responsive--sm">
                    <table class="transection-table-2">
                        <thead>
                            <tr>
								<th scope="col">@lang('S No.')</th>
                                <th scope="col">@lang('Username')</th>
                                <th scope="col">@lang('Full Name')</th>
                                <th scope="col">@lang('Under User')</th>
                                <th scope="col">@lang('City')</th>
                                <th scope="col">@lang('Plan')</th>
                                <th scope="col">@lang('Position')</th>
                                <th scope="col">@lang('Rank')</th>
                                <th scope="col">@lang('Level')</th>

                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $key=>$user)
                            <tr style="margin-bottom:20px; box-shadow: rgba(50, 50, 93, 0.25) 0px 13px 27px -5px, rgba(0, 0, 0, 0.3) 0px 8px 16px -8px;">
								<td data-label="@lang('S No.')">{{$users->firstItem()+$key}}</td>
                                <td data-label="@lang('Username')">{{$user->username}}</td>
								
                                @if($user->firstname == null && $user->lastname == null)
                                <td data-label="@lang('Full Name')">{{ getDPSFullname($user->id) }}</td>
                                @else
                                <td data-label="@lang('Full Name')">{{$user->firstname.' '.$user->lastname}}</td>
                                @endif
                                <td data-label="@lang('Under User')">
                                    {{getUpperFullname($user->pos_id)}}
                                    <br>
                                    {{getUpperUsername($user->pos_id)}}
                                </td>
                                
                                <td data-label="@lang('City')">

                                {{$user->address->city}}
                                </td>
                                
                                @if($user->plan_id == 1)
                                <td data-label="@lang('Plan')">Active</td>
                                @else
                                <td data-label="@lang('Plan')">Inactive</td>
                                @endif
                                @if($user->position == 1)
                                <td data-label="@lang('Position')">Left</td>
                                @else
                                <td data-label="@lang('Position')">Right</td>
                                @endif
                                <td data-label="@lang('Rank')">{{$user->rank}}</td>
                                <td data-label="@lang('Level')">{{Str::limit(strval(level($user->id, 0)), 1)}}</td>

                            </tr>
                            @endforeach
                        </tbody>
                    </table>
					
                </div>
            </div>

        </div>
    </div>
</div>
			
  {{$users->links()}}

@endsection