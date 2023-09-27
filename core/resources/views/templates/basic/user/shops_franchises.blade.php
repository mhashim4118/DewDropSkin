@extends($activeTemplate.'layouts.master')

@section('content')

<div class="row justify-content-center">
    <div class="col-sm-6 col-md-6 col-lg-6 col-xl-6 mb-4">
        <div class="dashboard-item">
            <div class="dashboard-item-header">
                <div class="header-left">
                    <h6 style="width: 200px;" class="title">@lang('Franchises')</h6>
                    <h3 class="ammount theme-one"> {{$franchises}}</h3>
                </div>
                <div class="icon"><i class="flaticon-tag-1"></i></div>
            </div>
            <div class="dashboard-item-body"></div>
        </div>
    </div>
    <div class="col-sm-6 col-md-6 col-lg-6 col-xl-6 mb-4">
        <div class="dashboard-item">
            <div class="dashboard-item-header">
                <div class="header-left">
                    <h6 style="width: 200px;" class="title">@lang('Shops')</h6>
                    <h3 class="ammount theme-one"> {{$shops}}</h3>
                </div>
                <div class="icon"><i class="flaticon-tag-1"></i></div>
            </div>
            <div class="dashboard-item-body"></div>
        </div>
    </div>
</div>
<div class="table-responsive">
    <table class="table transection-table-2">
        <thead>
            <tr>
                <th scope="col">@lang('S No.')</th>
                <th scope="col">@lang('Username')</th>
                <th scope="col">@lang('Shop/Franchise Name')</th>
                <th scope="col">@lang('Type')</th>
                <th scope="col">@lang('City')</th>
                <th scope="col">@lang('Address')</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $key=>$user)
            <tr style="margin-bottom:20px; box-shadow: rgba(50, 50, 93, 0.25) 0px 13px 27px -5px, rgba(0, 0, 0, 0.3) 0px 8px 16px -8px;">
                <td data-label="@lang('S No.')">{{$users->firstItem()+$key}}</td>
                <td data-label="@lang('Username')">{{$user->username}}</td>
                <?php
                $store = DB::connection('mysql_store')->table('sellers')
                    ->leftJoin('shops', 'shops.seller_id', '=', 'sellers.id')
                    ->where('sellers.dds_username', $user->username)->first();
                ?>
                @if($store)
                <td data-label="@lang('Shop/Franchise Name')">{{ ($store->name) }}</td>
                @else
                <td data-label="@lang('Name')">{{$user->firstname.' '.$user->lastname}}</td>
                @endif
                <td data-label="@lang('Type')">
                    {{($user->type)}}
                </td>
                <td data-label="@lang('City')">
                    {{$user->address->city}}
                </td>
                <td data-label="@lang('Address')">{{$user->address->address}}</td>
</tr>
@endforeach
</tbody>
</table>

</div>
<div class="row">
    <div class="col-lg-12">
        {{$users->links()}}
    </div>
</div>
@endsection