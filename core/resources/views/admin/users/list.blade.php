@extends('admin.layouts.app')
@section('panel')
<style>
@media (max-width: 767px) {
          table.style--two tbody tr {
    border: 2px dashed #0000005c;
    display: block;
    margin-bottom: 20px;
    box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.2);

    transition: box-shadow 0.3s ease-in-out;
}
table.style--two tbody tr:hover {
    box-shadow: 0px 10px 20px rgba(0, 0, 0, 0.4);
	transition: box-shadow 0.3s ease-in-out;
}
</style>
    <div class="row">
        <div class="col-lg-12">
            <div class="card b-radius--10 ">
                <div class="card-body p-0">
                    <div class="table-responsive--md  table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                            <tr>
                                <th>@lang('User')</th>
                                <th>@lang('Referrer User')</th>
                                <th>@lang('Under User')</th>
                                <th>@lang('Email-Phone')</th>
                                <th>@lang('CNIC')</th>
                                <th>@lang('City')</th>
                                <th>@lang('Country')</th>
                                <th>@lang('Joined At')</th>
                                <th>@lang('Balance')</th>
                                <th>@lang('Action')</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($users as $user)
                            <tr>
                                <td data-label="@lang('User')">
                                @if($user->firstname == null && $user->lastname == null)

                                <span class="font-weight-bold">{{ getDPSFullname($user->id) }}</span>
                                <br>
                                <span class="small">
                                <a href="{{ route('admin.users.detail', $user->id) }}"><span>@</span>{{ $user->username }}</a>
                                </span>

                                
                                @else
                                    <span class="font-weight-bold">{{$user->fullname}}</span>
                                    <br>
                                    <span class="small">
                                    <a href="{{ route('admin.users.detail', $user->id) }}"><span>@</span>{{ $user->username }}</a>
                                    </span>
                                @endif
                                </td>
                                @if($user->username != 'dds0001')
                                <td data-label="@lang('Referrer User')">
                                    <span class="font-weight-bold">{{getRefFullname($user->ref_id)}}</span>
                                    <br>
                                    <span class="small">
                                    <a href="{{ route('admin.users.detail', $user->ref_id) }}"><span>@</span>{{ getRefUsername($user->ref_id) }}</a>
                                    </span>
                                </td>
                                <td data-label="@lang('Under User')">
                                    @php $o = getUpperId($user->pos_id); @endphp
                                    @if(getUpperFullname($user->pos_id) == 0)
                                    <span class="font-weight-bold">{{  getDPSFullname($o) }}</span>
                                    <br>
                                    <span class="small">
                                    <a href="{{ route('admin.users.detail', getUpperId($user->pos_id)) }}"><span>@</span>{{ getUpperUsername($user->pos_id) }}</a>
                                    </span>
                                    @else
                                    <span class="font-weight-bold">{{ getUpperFullname($user->pos_id)}}</span>
                                    <br>
                                    <span class="small">
                                    <a href="{{ route('admin.users.detail', getUpperId($user->pos_id)) }}"><span>@</span>{{ getUpperUsername($user->pos_id) }}</a>
                                    </span>
                                    @endif
                                </td>
                                @else 
                                <td>-</td>
                                <td>-</td>
                                @endif

                                <td data-label="@lang('Email-Phone')">
                                    {{ $user->email }}<br>{{ $user->mobile }}
                                </td>
                                <td data-label="@lang('CNIC')">
                                    <span class="font-weight-bold" data-toggle="tooltip" data-original-title="{{ @$user->cnicnumber }}">{{ $user->cnicnumber }}</span>
                                </td>
                             
                                <td data-label="@lang('City')">
                                    <span class="font-weight-bold" data-toggle="tooltip" data-original-title="{{ @$user->address->city }}">{{ $user->address->city }}</span>
                                </td>
                                <td data-label="@lang('Country')">
                                    <span class="font-weight-bold" data-toggle="tooltip" data-original-title="{{ @$user->address->country }}">{{ $user->country_code }}</span>
                                </td>



                                <td data-label="@lang('Joined At')">
                                    {{ showDateTime($user->created_at) }} <br> {{ diffForHumans($user->created_at) }}
                                </td>


                                <td data-label="@lang('Balance')">
                                    <span class="font-weight-bold">
                                        
                                    {{ $general->cur_sym }}{{ showAmount($user->balance) }}
                                    </span>
                                </td>



                                <td data-label="@lang('Action')">
                                    <a href="{{ route('admin.users.detail', $user->id) }}" class="icon-btn" data-toggle="tooltip" title="" data-original-title="@lang('Details')">
                                        <i class="las la-desktop text--shadow"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                                <tr>
                                    <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                                </tr>
                            @endforelse

                            </tbody>
                        </table><!-- table end -->
                    </div>
                </div>
                <div class="card-footer py-4">
                    {{ paginateLinks($users) }}
                </div>
            </div>
        </div>


    </div>
@endsection



@push('breadcrumb-plugins')
    <form action="{{ route('admin.users.search', $scope ?? str_replace('admin.users.', '', request()->route()->getName())) }}" method="GET" class="form-inline float-sm-right bg--white">
        <div class="input-group has_append">
            <input type="text" name="search" class="form-control" placeholder="@lang('Name, username or email')" value="{{ $search ?? '' }}">
            <div class="input-group-append">
                <button class="btn btn--primary" type="submit"><i class="fa fa-search"></i></button>
            </div>
        </div>
    </form>
@endpush