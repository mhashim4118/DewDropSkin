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
    }
</style>
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive--md table-responsive">
                    <table class="table table--light style--two">
                        <thead>
                            <tr>
                                <th scope="col">@lang('Username')</th>
                                <th scope="col">@lang('Full Name')</th>
                                <th scope="col">@lang('Email')</th>
                                <th scope="col">@lang('Phone')</th>
                                <th scope="col">@lang('Store Type')</th>
                                <th scope="col">@lang('City')</th>
                                <th scope="col">@lang('Created Date')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($sellers as $key => $seller)
                            <tr>
                                <td data-label="@lang('Username')">{{ __($seller->dds_username) }} </td>
                                <td data-label="@lang('Full Name')">{{ __($seller->f_name) }} {{($seller->l_name)}}</td>
                                <td data-label="@lang('Email')">{{ __($seller->email) }} </td>
                                <td data-label="@lang('Phone')">{{ __($seller->phone) }} </td>
                                <td data-label="@lang('Store Type')">
                                    <?php
                                    $memberships = DB::table('memberships')
                                        ->leftJoin('users', 'users.membership_id', '=', 'memberships.id')
                                        ->where('users.username', ($seller->dds_username))
                                        ->first();
                                    if ($memberships)
                                        echo $memberships->type
                                    ?>
                                </td>
                                <td data-label="@lang('Created Date')">
                                    {{ ($seller->city)}}
                                </td>
                                <td data-label="@lang('Created Date')">
                                    {{ showDateTime($seller->created_at)}}
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
                {{ $sellers->links('admin.partials.paginate') }}
            </div>
        </div>
    </div>
</div>



@endsection
@push('breadcrumb-plugins')
<form action="{{ route('admin.users.pendingSellers')}}" method="GET" class="form-inline float-sm-right bg--white">
    <div class="col-12 col-md-6">
        <div class="input-group">
            <input type="text" name="city" class="form-control" placeholder="@lang('City')" value="{{ $search ?? '' }}">
        </div>
    </div>
    <div class="col-12 col-md-6">
        <div class="input-group has_append">
            <input type="text" name="search" class="form-control" placeholder="@lang('Name, username or email')" value="{{ $search ?? '' }}">
            <div class="input-group-append">
                <button class="btn btn--primary" type="submit"><i class="fa fa-search"></i></button>
            </div>
        </div>
    </div>
</form>
@endpush