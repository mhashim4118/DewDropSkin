@extends('admin.layouts.app')
@section('panel')
<form action="{{ route('admin.product.update',@$product->id)}}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="row mb-5">

        <div class="col-lg-12">
            <div class="card">
                <div class="card-header font-weight-bold  bg--primary">@lang('Product Basic Information')</div>
                <div class="card-body">
                    <div class="form-group row">
                        <div class="col-md-2">
                            <label for="name" class="font-weight-bold">@lang('Product Name') <strong class="text-danger">*</strong> </label>
                        </div>
                        <div class="col-md-10">
                            <input type="text" class="form-control" name="name" id="name" value="{{ @$product->name}}" placeholder="@lang('Name')">
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-md-2">
                            <label for="" class="font-weight-bold">@lang('Categories') <strong class="text-danger">*</strong> </label>
                        </div>
                        <div class="col-md-10">
                            <select class="form-control" name="category">
                                <option disabled>@lang('Please Select One')</option>
                                @foreach (@$categories as $category)
                                <option value="{{ $category->id}}" @if($category->id == $product->category_id) selected @endif>{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-md-2">
                            <label for="" class="font-weight-bold">@lang('Price') <strong class="text-danger">*</strong> </label>
                        </div>
                        <div class="col-md-10">
                            <input type="text" class="form-control" id="unit_price" value="{{ getAmount($product->price) }}" name="price" placeholder="@lang('Price')">
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-md-2">
                            <label for="" class="font-weight-bold">@lang('Quantity')</label>
                        </div>
                        <div class="col-md-10">
                            <input type="text" class="form-control" value="{{ @$product->quantity }}" name="quantity" placeholder="@lang('Quantity')">
                        </div>
                    </div>

                    <!-- <div class="form-group row">
                        <div class="col-md-2">
                            <label for="" class="font-weight-bold">@lang('BV')</label>
                        </div>
                        <div class="col-md-10">
                            <input type="number" class="form-control" id="bv" value="{{ $product->bv }}" name="bv" placeholder="@lang('BV')">
                        </div>
                    </div>
					
                    <div class="form-group row">
                        <div class="col-md-2">
                            <label for="" class="font-weight-bold">@lang('BV %')</label>
                        </div>
                        <div class="col-md-10">
                            <input type="number" class="form-control" id="bv_per" value="{{ $product->bv_per }}" name="bv_per" placeholder="@lang('BV %')">
                        </div>
                    </div>

					<div class="form-group row">
                        <div class="col-md-2">
                            <label for="" class="font-weight-bold">@lang('PV')</label>
                        </div>
                        <div class="col-md-10">
                            <input type="number" class="form-control" id="pv" value="{{ $product->pv }}" name="pv" placeholder="@lang('PV')" readonly>
                        </div>
                    </div> -->

                    <div class="form-group row">
                    <div class="col-md-2">
                            <label for="" class="font-weight-bold">@lang('BV formula')</label>
                        </div>
                        <div class="col-md-10">
                      
                        <input type="text" id="bv_formula" placeholder="{{ ('BV formula') }}" name="bv_formula" value="{{ $product->bv_formula  }}" class="form-control" required>
                        </div>
                    </div>
                    <div class="form-group row"> 
                    <div class="col-md-2">
                            <label for="" class="font-weight-bold">@lang('BV bonus')</label>
                        </div>
                        <div class="col-md-10">
                        <input type="text" id="bv_bonus" placeholder="{{ ('BV') }}" name="bv_bonus" value="{{ $product->bv }}" class="form-control" readonly>
                        </div>
                    </div>
                    <div class="form-group row">
                    <div class="col-md-2">
                            <label for="" class="font-weight-bold">@lang('PV Formula')</label>
                        </div>
                        <div class="col-md-10">
                        <input type="text" placeholder="{{ ('PV Formula') }}" id="pv_formula" value="{{ $product->pv_formula  }}" name="pv_formula" class="form-control" required>
                        </div>
                    </div>
                    <div class="form-group row">
                    <div class="col-md-2">
                            <label for="" class="font-weight-bold">@lang('PV generated')</label>
                        </div>
                        <div class="col-md-10">
                        <input type="number" placeholder="{{ ('PV') }}" id="pv_bonus" value="{{ $product->pv }}" name="pv_bonus" class="form-control" readonly>
                        </div>
                    </div>

					   <div class="form-group row">
                        <div class="col-md-2">
                            <label for="" class="font-weight-bold">@lang('Shop Reference Formula')</label>
                        </div>
                        <div class="col-md-10">
                            <input type="text" class="form-control" id="shop_reference" value="{{ old('pv')}}" name="shop_reference_formula" placeholder="@lang('Shop Reference %')">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-2">
                            <label for="" class="font-weight-bold">@lang('Shop Ref Bonus')</label>
                        </div>
                        <div class="col-md-10">
                            <input type="number" class="form-control" id="shop_reference_" value="{{ $product->shop_reference}}" name="shop_reference" placeholder="@lang('Shop Reference Bonus')" readonly>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-2">
                            <label for="" class="font-weight-bold">@lang('Featured')</label>
                        </div>
                        <div class="col-md-10">
                            <select class="form-control" name="featured" required>
                                <option value="1">@lang('Yes')</option>
                                <option value="0">@lang('No')</option>
                            </select>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div class="col-lg-12 mt-5">
            <div class="card">
                <div class="card-header font-weight-bold  bg--primary">@lang('Product Description')</div>
                <div class="card-body">
                    <div class="form-group row">
                        <div class="col-md-2">
                            <label class="font-weight-bold">@lang('Product Discription') <strong class="text-danger">*</strong> </label>
                        </div>
                        <div class="col-md-10">
                            <textarea id="my-textarea" class="form-control nicEdit" name="description" rows="3">{{ @$product->description }}</textarea>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-2">
                            <label for="" class="font-weight-bold">@lang('Product Specifications')</label>
                        </div>
                        <div class="col-md-10" id="specification">

                            @if(@$product->specifications !=null && !empty(@$product->specifications) )
                            @foreach (@$product->specifications as $k=> $specification)
                            <div class="row mb-2 specification">
                                <div class="col-lg-5">
                                    <input type="text" class="form-control" name="specification[{{ $k }}][name]" value="{{ @$specification['name'] }}" placeholder="@lang('Enter Specification Name')">
                                </div>
                                <div class="col-lg-5">
                                    <input type="text" class="form-control" name="specification[{{ $k }}][value]" value="{{ @$specification['value'] }}" placeholder="@lang('Enter Specification Value')">
                                </div>
                                <div class="col-lg-2 text-right minus-specification">
                                    <a class="btn btn-outline--danger "><i class="la la-minus"></i></a>
                                </div>
                            </div>
                            @endforeach
                            <div class="row">
                                <div class="col-lg-10"><label for="" id="specifications-title">@lang('Add specifications as you want by clicking the (+) button on the right side')</label></div>
                                <div class="col-lg-2 text-right">
                                    <a class="btn btn-outline--success add-specification"><i class="la la-plus"></i></a>
                                </div>
                            </div>
                            @else
                            <div class="row">
                                <div class="col-lg-10"><label for="" id="specifications-title">@lang('Add specifications as you want by clicking the (+) button on the right side')</label></div>
                                <div class="col-lg-2 text-right">
                                    <a class="btn btn-outline--success add-specification"><i class="la la-plus"></i></a>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-12 mt-5">
            <div class="card">
                <div class="card-header font-weight-bold  bg--primary">@lang('Product Image')</div>
                <div class="card-body">
                    <div class="form-group row">
                        <div class="col-md-2">
                            <label for="" class="font-weight-bold">@lang('Thumbnail') <strong class="text-danger">*</strong></label>
                        </div>
                        <div class="col-md-10">
                            <div class="d-flex">
                                <div class="payment-method-item">
                                    <div class="payment-method-header d-flex flex-wrap">
                                        <div class="thumb">
                                            <div class="avatar-preview">
                                                <div class="profilePicPreview" style="background-image: url({{getImage(imagePath()['products']['path'].'/'.$product->thumbnail,'450x500')}})"></div>
                                            </div>
                                            <div class="avatar-edit">
                                                <input type="file" name="thumbnail" class="profilePicUpload" id="image" accept=".png, .jpg, .jpeg">
                                                <label for="image" class="bg--primary"><i class="la la-pencil"></i></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="form-group row">


                        <div class="col-md-2">
                            <label for="" class="font-weight-bold">@lang('Gallery')</label>
                        </div>
                        <div class="col-md-10">
                            <div class="row">
                                <div class="col-lg-12 text-right">
                                    <a class="btn btn-outline--success add-gallery-image"><i class="la la-plus"></i></a>
                                </div>
                            </div>
                            <div class="row" id="__gallery_image">
                                @if ($product->images  && !empty($product->images ))
                                @php
                                    $imageKey=0;
                                @endphp
                                @foreach ($product->images as $imageKey=> $image)
                                <div class="col-lg-3 __gallery_image">
                                    <div class="form-group">
                                        <a href="{{ route('admin.product.image.remove',[$product->id,$imageKey]) }}" type="button" class="btn btn-sm btn--danger"><i class="fas fa-times mr-0"></i></a>
                                        <div class="image-upload">
                                            <div class="thumb">
                                                <div class="avatar-preview">
                                                    <div class="profilePicPreview" style="background-image: url({{getImage(imagePath()['products']['path'].'/'.$image,'450x500')}})">
                                                    </div>
                                                </div>
                                                <div class="avatar-edit">
                                                    <input type="file" class="profilePicUpload" name="gallery[{{$imageKey}}]" id="profilePicUploadGal{{ $imageKey }}" accept=".png, .jpg, .jpeg">
                                                    <label for="profilePicUploadGal{{ $imageKey }}" class="bg--success">@lang('Upload Image')</label>
                                                    <small class="mt-2 text-facebook">@lang('Supported files'): <b>@lang('jpeg, jpg.')</b> @lang('Image will be resized into 450x500') </small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                                @endif
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="col-lg-12 mt-5">
            <div class="card">
                <div class="card-header font-weight-bold  bg--primary">@lang('SEO Contents')</div>
                <div class="card-body">
                    <div class="form-group row">
                        <div class="col-md-2">
                            <label for="" class="font-weight-bold">@lang('Meta Ttitle') <strong class="text-danger">*</strong></label>
                        </div>
                        <div class="col-md-10">
                            <input type="text" class="form-control" name="meta_title" value="{{ @$product->meta_title}}" placeholder="@lang('Meta Title')">
                        </div>

                    </div>
                    <div class="form-group row">
                        <div class="col-md-2">
                            <label for="" class="font-weight-bold">@lang('Meta Keyword') <strong class="text-danger">*</strong></label>
                        </div>
                        <div class="col-md-10">
                            <select class="form-control select2-auto-tokenize" name="meta_keywords[]" multiple id="">
                                @if(@$product->meta_keyword && !empty(@$product->meta_keyword))
                                @foreach (@$product->meta_keyword as $keyword)
                                <option value="{{$keyword}}" selected>{{ $keyword}}</option>
                                @endforeach
                                @endif

                            </select>
                        </div>

                    </div>
                    <div class="form-group row">
                        <div class="col-md-2">
                            <label for="" class="font-weight-bold">@lang('Meta Description') <strong class="text-danger">*</strong></label>
                        </div>
                        <div class="col-md-10">
                            <textarea name="meta_description" class="form-control" placeholder="@lang('Meta Description')" id="" name="meta_description" cols="30" rows="10">{{@$product->meta_description}}</textarea>
                        </div>

                    </div>

                </div>
            </div>
        </div>

        <div class="col-lg-12 mt-5">
            <button type="submit" class="btn btn--primary btn-block">@lang('Update')</button>
        </div>

    </div>

</form>

@endsection



@push('breadcrumb-plugins')
<a href="{{ route('admin.product.index') }}" class="btn btn-sm btn--primary box--shadow1 text--small"><i class="las la-backward"></i>@lang('Back')</a>

@endpush
@push('style')
<style>
    .profilePicUpload{
        height: 0px;
        padding: 0px;
    }
    .__gallery_image .form-group{
        position: relative;
    }
    .btn--danger{
        position: absolute;
        z-index: 99;
        top: 3px;
        right: 3px;
        border-radius: 5px;
    }
</style>
@endpush
@push('script')
<script>
    "use strict";
    (function($) {
		$("#shop_reference").on('keyup', function(e) {
            var shop_reference = $(this).val();
            var price = $("#price").val();
            var percentage = shop_reference.indexOf('%')
            var result = 0;
            if (percentage > 0) {
                var ref = shop_reference.substring(0, shop_reference.length - 1);
                result = (price / 100) * ref;
                console.log(result)
            } else {
                result = shop_reference;
            }
            $("#shop_reference_").val(result);
        })
        // $("#bv, #price").on('keyup', function(e) {
        //     var bv = $(this).val();
        //     var price = $("#price").val();
        //     var pv = parseFloat(price/200).toFixed(2);
        //     $("#pv").val(pv);
        // })

        $(document).on('keyup', '#bv_formula', function(e) {
            var bv = 0;
            var unit_price = $('#unit_price').val();
            var bv_bonus = $('#bv_formula').val();
            var percentage = bv_bonus.indexOf('%')
            if (percentage > 0) {
                var ddsValue = bv_bonus.substring(0, bv_bonus.length - 1)
                var bv = parseFloat((unit_price / 100) * ddsValue).toFixed(2);
            } else {
                bv = bv_bonus;
            }

            $('#bv_bonus').val(bv)
        });
        $(document).on('keyup', '#pv_formula', function(e) {
            var unit_price = $('#unit_price').val();
            var bv_bonus = $('#bv_bonus').val();
            var bv = (unit_price / 100) * bv_bonus;
            console.log(bv);
            var pv_formula = $('#pv_formula').val();
            var pv = parseFloat(unit_price / pv_formula).toFixed(2);
            $('#pv_bonus').val(pv);
        });
        $(".add-specification").on('click', function(e) {
            let index = $(document).find(".specification").length;
            index = parseInt(index) + parseInt(1);

            let html = `
           <div class="row mb-2 specification">
            <div class="col-lg-5">
                <input type="text" class="form-control" name="specification[${index}][name]" placeholder="@lang('Enter Specification Name')">
            </div>
            <div class="col-lg-5">
                <input type="text" class="form-control" name="specification[${index}][value]" placeholder="@lang('Enter Specification Value')">
            </div>
            <div class="col-lg-2 text-right minus-specification">
                <a class="btn btn-outline--danger "><i class="la la-minus"></i></a>
            </div>
        </div>
           `;
            $("#specification").prepend(html)
            $("#specifications-title").hide()
        })
        $(".add-gallery-image").on('click', function(e) {
            let index = $(document).find(".__gallery_image").length;
            index = index + 1;

            let html = `
            <div class="col-lg-3 __gallery_image">
                <div class="form-group">
                    <button type="button" class="btn btn-sm btn--danger removeBtn"><i class="fas fa-times mr-0"></i></button>
                    <div class="image-upload">
                        <div class="thumb">
                            <div class="avatar-preview">
                                <div class="profilePicPreview" style="background-image: url({{ getImage('', '450x500') }})">
                                </div>
                            </div>
                            <div class="avatar-edit">
                                <input type="file" class="profilePicUpload" name="gallery[]" id="profilePicUploadItem${index}" accept=".png, .jpg, .jpeg">
                                <label for="profilePicUploadItem${index}" class="bg--success">Upload Image</label>
                                <small class="mt-2 text-facebook">Supported files: <b>jpeg, jpg.</b> Image will be resized into 450x500 </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
           `;
            $("#__gallery_image").append(html)

        })

        $("body").on('click', '.minus-specification', function(e) {
            $(this).closest('.specification').remove();
            $(document).find(".specification").length <= 0 ? $("#specifications-title").show() : "" ;

        })

        $(document).on('click','.removeBtn',function (){
            $(this).closest('.__gallery_image').remove();
        });

        function proPicURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    var preview = $(input).parents('.thumb').find('.profilePicPreview');
                    $(preview).css('background-image', 'url(' + e.target.result + ')');
                    $(preview).addClass('has-image');
                    $(preview).hide();
                    $(preview).fadeIn(650);
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
        $("body").on('change','.profilePicUpload',function() {
            proPicURL(this);
        });

        $('select[name=featured]').val({{ $product->is_featured }});

    })(jQuery);

</script>

<script src="{{ asset('assets/admin/js/status-switch.js') }}"></script>


@endpush
