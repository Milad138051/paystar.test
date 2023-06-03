<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\CartItem;
use App\Models\Order;
use App\Services\PaymentService;
use Illuminate\Http\Request;

class SalesProceesController extends Controller
{
    public function index()
	{
		$product=Product::first();
		$cartItems=CartItem::first();
		$order=Order::first();
		if($product ==null)
		{
		 Product::factory(1)->create();
		}			
		if($order ==null)
		{
		 Order::factory(1)->create([
		 'user_id'=>auth()->user()->id,
		 ]);	
	    }			
		if($cartItems ==null)
		{
		 CartItem::Factory(1)->create([
		 'user_id'=>auth()->user()->id,
		 'product_id'=>1,
		 ]);		
		}		

		return view('sales-process.cart',compact('cartItems'));
	}
	
	
	public function submitPayment(Request $request)
	{
		$paymentService=new PaymentService;
		if($request->price){
		 $paymentService->payStar($request->price);
		}
	}
	
	
	public function callback(Request $request)
	{
		$inputs=$request->all();
	    $paymentService=new PaymentService;
		return $paymentService->callback($inputs);
	}
	
	

}
