@extends('sales-process.layouts.master-two-col')

@section('head-tag')
<title>سبد خرید شما</title>
@endsection


@section('content')

<!-- start cart -->
<section class="mb-4">
    <section class="container-xxl" >
        <section class="row">
            <section class="col">
			
			
			
                        @if ($errors->any())
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        @endif

                <section class="row mt-4">
                    <section class="col-md-9 mb-3">
                        <form action="{{route('sales-process.submit')}}" id="cart_items" method="post" class="content-wrapper bg-white p-3 rounded-2">
						@csrf
						<input type="hidden" name="price" value="150000"></input>
                        </form>

                    </section>
                    <section class="col-md-3">
                        <section class="content-wrapper bg-white p-3 rounded-2 cart-total-price">
                            <section class="d-flex justify-content-between align-items-center">
                                <p class="text-muted">قیمت کالاها
                                    1
                             </p>
                                <p class="text-muted" id="total_product_price"><span> 150000</span> </p>
                            </section>

                          
                            <section class="border-bottom mb-3"></section>
                            <section class="d-flex justify-content-between align-items-center">
                                <p class="text-muted">جمع سبد خرید</p>
                                <p class="fw-bolder" id="total_price"> 150000</p>
                            </section>

                            <p class="my-3">
                                <i class="fa fa-info-circle me-1"></i>کاربر گرامی  خرید شما هنوز نهایی نشده است. برای ثبت سفارش و تکمیل خرید باید ابتدا آدرس خود را انتخاب کنید و سپس نحوه ارسال را انتخاب کنید. نحوه ارسال انتخابی شما محاسبه و به این مبلغ اضافه شده خواهد شد. و در نهایت پرداخت این سفارش صورت میگیرد.
                            </p>


                            <section class="">
                                <button onclick="document.getElementById('cart_items').submit();" class="btn btn-danger d-block">تکمیل فرآیند خرید</button>
                            </section>

                        </section>
                    </section>
                </section>
            </section>
        </section>

    </section>
</section>
<!-- end cart -->

@endsection

@section('script')


	<script>
	//start cart
$(document).ready(function() {

    $(".cart-number-up").click(function(){
        var value = parseInt($(this).parent().find('input[type=number]').val());
        var max = parseInt($(this).parent().find('input[type=number]').data('max'));
        if(value < max) {
            $(this).parent().find('input[type=number]').val(value + 1);
        }
    });

    $(".cart-number-down").click(function(){
        var value = parseInt($(this).parent().find('input[type=number]').val());
        if(value > 1) {
            $(this).parent().find('input[type=number]').val(value - 1);
        }
    });

});
//end cart
</script>

@endsection
