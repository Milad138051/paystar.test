<?php

namespace App\Services;

use App\Models\Payment;
use Carbon\Carbon;

class PaymentService
{
    public function payStar($amount)
    {
        $order_id = '1234';
        $v = $amount . '#' . $order_id . '#' . route('sales-process.callback');
        $key = '9A3EC03483556C73714510C507529DF70A1228C83477D1455E0511BD72C5AAB8A6715A414AA48B7C905FCEF45868BD26DA58196EF29C77C194C9F14A4B47456CC6454E9D50B388D6FC5AC91BB08B234A8060FDC85B1CEC32CA036DC907F8A4A635D9CBB9CAA31B42549B8D70B2CE5EDE8274FFB55DABFE92D76BC42D91696FAF';
        $sign = hash_hmac('sha512', $v, $key);


        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://core.paystar.ir/api/pardakht/create',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '{
            "amount": $amount,
            "order_id": "1",
            "callback": "call-back/payment",
            "sign": $sign,
            }',
            CURLOPT_HTTPHEADER => array(
        'Authorization: Bearer 0yovdk2l6e143',
        'Content-Type: application/json'
    ),
          ));

      $response = curl_exec($curl);
      curl_close($curl);
	  
	  $response = json_decode($response, true);
	  $statusCode = $response['status'];
	  $statusMessage = $response['message'];
	  $data = $response['data'];
	  	  if (!is_null($data)) {
              $token = $data['token'];
              $ref_num = $data['ref_num'];
              $order_id = $data['order_id'];
              $payment_amount = $data['payment_amount'];
              $payment = Payment::create(
                  ['token' => $token, 'ref_num' => $ref_num, 'order_id' => $order_id, 'payment_amount' => $payment_amount, 'sign' => $sign]
              );
          }
              switch ($statusCode) {
                  case '1';
                      return $this->headToPaystar($token);
                      break;

                  case '-1';
                      if ($payment->token !== $token) {
                          return redirect()->back()->withErrors('msg', 'درخواست نامعتبر');
                      }
                      if ($payment->ref_num !== $ref_num) {
                          return redirect()->back()->withErrors('msg', 'درخواست نامعتبر');
                      }
                      if ($payment->order_id !== $order_id) {
                          return redirect()->back()->withErrors('msg', 'درخواست نامعتبر');
                      }
                      if ($payment->payment_amount !== $payment_amount) {
                          return redirect()->back()->withErrors('msg', 'درخواست نامعتبر');
                      }
                      break;

                  case '-2';
                      return redirect()->back()->withErrors('msg', 'درگاه فعال نیست');
                      break;

                  case '-3';
                      return redirect()->back()->withErrors('msg', 'توکن تکراری است');
                      break;

                  case '-4';
                      return redirect()->back()->withErrors('msg', 'مبلغ بیشتر از سقف مجاز درگاه است');
                      break;

                  case '-5';
                      return redirect()->back()->withErrors('msg', 'شناسه ref_num معتبر نیست');
                      break;

                  case '-6';
                      return redirect()->back()->withErrors('msg', 'تراکنش قبلا وریفای شده است');
                      break;

                  case '-7';
                      return redirect()->back()->withErrors('msg', 'پارامترهای ارسال شده نامعتبر است');
                      break;

                  case '-8';
                      return redirect()->back()->withErrors('msg', 'تراکنش را نمیتوان وریفای کرد');
                      break;

                  case '-9';
                      return redirect()->back()->withErrors('msg', 'تراکنش وریفای نشد');
                      break;

                  case '-98';
                      return redirect()->back()->withErrors('msg', 'تراکنش ناموفق');
                      break;

                  case '-99';
                      return redirect()->back()->withErrors('msg', 'خطای سامانه');
                      break;
              }
	
	}


    public function headToPaystar($token)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://core.paystar.ir/api/pardakht/payment',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '{
          "token":$token,
          }',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);

    }


    public function callback($inputs)
    {
        $statusCode = $inputs['status'];
        $ref_num = $inputs['ref_num'];
        $order_id = $inputs['order_id'];
        if ($inputs['$transaction_id']) {
            $transaction_id = $inputs['transaction_id'];
        }
        if ($inputs['card_number']) {
            $card_number = $inputs['card_number'];
        }
        if ($inputs['tracking_code']) {
            $tracking_code = $inputs['tracking_code'];
        }

        $payment = Payment::where('order_id', $order_id)->get();
        $payment = Payment::update($inputs);

        switch ($statusCode) {
            case '1';
                return $this->verifyPaystar($order_id);
                break;

            case '-2';
                return redirect()->back()->withErrors('msg', 'درگاه فعال نیست');
                break;

            case '-3';
                return redirect()->back()->withErrors('msg', 'توکن تکراری است');
                break;

            case '-4';
                return redirect()->back()->withErrors('msg', 'مبلغ بیشتر از سقف مجاز درگاه است');
                break;

            case '-5';
                return redirect()->back()->withErrors('msg', 'شناسه ref_num معتبر نیست');
                break;

            case '-6';
                return redirect()->back()->withErrors('msg', 'تراکنش قبلا وریفای شده است');
                break;

            case '-7';
                return redirect()->back()->withErrors('msg', 'پارامترهای ارسال شده نامعتبر است');
                break;

            case '-8';
                return redirect()->back()->withErrors('msg', 'تراکنش را نمیتوان وریفای کرد');
                break;

            case '-9';
                return redirect()->back()->withErrors('msg', 'تراکنش وریفای نشد');
                break;

            case '-98';
                return redirect()->back()->withErrors('msg', 'تراکنش ناموفق');
                break;

            case '-99';
                return redirect()->back()->withErrors('msg', 'خطای سامانه');
                break;
        }
    }


    public function verifyPaystar($order_id)
    {
        $payment = Payment::where('order_id', $order_id)->get();
        $order_id = $payment->order_id;
        $amount = $payment->payment_amount;
        $ref_num = $payment->ref_num;
        $card_number = $payment->card_number;
        $tracking_code = $payment->tracking_code;
        $token = $payment->token;
        $v = $amount . '#' . $ref_num . '#' . $card_number . '#' . $tracking_code;
        $key = '9A3EC03483556C73714510C507529DF70A1228C83477D1455E0511BD72C5AAB8A6715A414AA48B7C905FCEF45868BD26DA58196EF29C77C194C9F14A4B47456CC6454E9D50B388D6FC5AC91BB08B234A8060FDC85B1CEC32CA036DC907F8A4A635D9CBB9CAA31B42549B8D70B2CE5EDE8274FFB55DABFE92D76BC42D91696FAF';
        $sign = hash_hmac('sha512', $v, $key);


        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://core.paystar.ir/api/pardakht/verify',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '{
          "amount": $amount,
          "sign":$sign,
          "ref_num": $ref_num,
		  
        }',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer 0yovdk2l6e143',
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);

        $response = json_decode($response, true);

        $statusCode = $response['status'];
        $statusMessage = $response['message'];
        $data = $response['data'];


        if ($statusCode == 1) {
            $price = $data['price'];
            $ref_num = $data['ref_num'];
            $card_number = $data['card_number'];
            $payment = Payment::update(
                ['payment_amount' => $price, 'ref_num' => $ref_num, 'card_number' => $card_number, 'verified_at', Carbon::now()]
            );
            return redirect()->route('sales-process.cart')->with('msg', 'پرداخت شما با موفقیت انجام شد');

        } else {
            return redirect()->back()->withErrors('msg', $statusMessage);
        }


    }


}