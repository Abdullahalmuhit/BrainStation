<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Student;
use http\Client\Curl\User;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function studentStore(Request $request){
        $defaultPassword = 'secret';
        $student = Student::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt("$defaultPassword"),
            'invoice_no' => $request->invoice_no,
        ]);
        $credit_account = "12345678908";
        $invoice_no =$student->invoice_no;
        $return_url = route('payment-request.response');
        $ref_date = date( "Y-m-d" );
        $credit_amt_fee = 100;
        $this->makePaymentRequest($student,$invoice_no, $credit_account, $credit_amt_fee, $return_url, $ref_date);


    }

    public function makePaymentRequest($student,$invoice_no, $credit_account, $credit_amt_fee, $return_url, $ref_date)
    {
        //dd($student,$invoice_no, $credit_account, $credit_amt_fee, $return_url, $ref_date);
        $user_name_by_brain = "BRAIN";
        $user_password_by_brain = "123456";
        $api_url_by_brain = route('payment-check');;
        $UI_url_by_brain = "http://127.0.0.1:8000/student/payment/";

        $CreditInformations = array(
            array(
                "crAmount"  => $credit_amt_fee,
                "crAccount" => $credit_account )
        );

        $CreditInformations2 = array(
            array(
                "slno"      => '1',
                "crAccount" => $credit_account,
                "crAmount"  => $credit_amt_fee,
                "tranMode"  => 'TRN',
                "onbehalf"  => 'Any Name/Party'
            )
        );

        //1st api ...............
        $url = $api_url_by_brain . "/api/v2/student/GetAccessToken";

        $data = array(
            "AccessUser"   => array(
                "userName" => $user_name_by_brain,
                "password" => $user_password_by_brain,
            ),
            "invoiceNo" =>  $invoice_no,
            "amount"=> $credit_amt_fee,
            "invoiceDate"  => $ref_date,
            "accounts"  => $CreditInformations
        );

        $CURLOPT_HTTPHEADER = array( 'Content-Type: application/json' ,
            'Authorization: Basic Q1NCTUM6YXNzIzEzMyNycnIx'
        );



        $options = array(
            CURLOPT_POST           => 1,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER         => false,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_ENCODING       => "",
            CURLOPT_AUTOREFERER    => true,
            CURLOPT_CONNECTTIMEOUT => 300,
            CURLOPT_TIMEOUT        => 300,
            CURLOPT_MAXREDIRS      => 20,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_HTTPHEADER     => $CURLOPT_HTTPHEADER,
            CURLOPT_POSTFIELDS     => json_encode( $data ),
        );

        $ch = curl_init( $url );
        curl_setopt_array( $ch, $options );
        $content = curl_exec( $ch );
        curl_close( $ch );

        $response_arr = json_decode( $content,true );
        $msg = "";
        if(isset($response_arr["Message"])){
            $msg = $response_arr["Message"];

        }
        if(isset($response_arr["msg"])){
            $msg = $response_arr["msg"];
        }


        if( $msg == "Authorization has been denied for this request." ||  $msg== ""){
            echo "get session key error";
            exit();
        }
        else if($msg == 'success'){

            //2nd api
            $url2 = $api_url_by_brain . "/api/v2/student/CreatePaymentRequest";

            $data = array(
                "authentication"     => array(
                    "apiAccessUserId"  => $user_name_by_brain,
                    "apiAccessToken" => $response_arr["access_token"]
                ),
                "referenceInfo"      => array(
                    "InvoiceNo"=>  $invoice_no,
                    "invoiceDate"=> $ref_date,
                    "returnUrl"=> $return_url,
                    "totalAmount"=> $credit_amt_fee,
                    "applicentName"=> $student->name,
                    "applicentContactNo"=> $student->contact_number,
                    "studentId"=> $student->id,
                    "extraRefNo"=> "2132"
                ),
                "creditInformations" => $CreditInformations2
            );

            $options = array(
                CURLOPT_POST           => 1,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HEADER         => false,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_ENCODING       => "",
                CURLOPT_AUTOREFERER    => true,
                CURLOPT_CONNECTTIMEOUT => 120,
                CURLOPT_TIMEOUT        => 120,
                CURLOPT_MAXREDIRS      => 10,
                CURLOPT_SSL_VERIFYPEER => true,
                CURLOPT_SSL_VERIFYHOST => 2,
                CURLOPT_HTTPHEADER     => $CURLOPT_HTTPHEADER,
                CURLOPT_POSTFIELDS     => json_encode( $data ),
            );

            $ch = curl_init( $url2 );
            curl_setopt_array( $ch, $options );
            $content = curl_exec( $ch );
            curl_close( $ch );
            $response_arr2 =  json_decode( $content ,true);
            if($response_arr2 && $response_arr2['status'] == '200' ){
                $redirect_url = $UI_url_by_brain . $response_arr2['session_token'];

                $updateUser =  Student::with(['studentPayments'])->where('invoice_no', $invoice_no)->first();
                $paymentType = 'Student Fee';
                $updateUsers = StudentPayment::updateOrCreate([
                    'student_id' => $updateUser->id,
                    'bank_ref' =>$updateUser->bank_ref,
                    'invoice_no' =>$updateUser->invoice_no,
                    'payment_to' =>$updateUser->payment_to,
                    'payment_type' =>$paymentType,
                    'transaction_id' => null,
                    'transaction_date' => null,
                    'amount' => null,
                    'payment_status' =>'Error data not Proces',
                    'payment_status_code' => 5555,
                    'pay_mode' => null,
                    'session_token' => $response_arr2['session_token']
                ]);


                header('Location: '.$redirect_url);
            }

        }
    }

}
