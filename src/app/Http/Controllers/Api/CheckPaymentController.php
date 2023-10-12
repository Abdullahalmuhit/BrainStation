<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\StudentPayment;
use http\Client\Curl\User;
use Illuminate\Http\Request;

class CheckPaymentController extends Controller
{
    public function CheckPayment(Request $request){
        dd($request->all());
    }
    public function response(Request $request){
        $session_token = $_GET['session_token'];
        $status = $_GET['status'];

        $api_url_by_brain = "https://brain/tranjection";

        $url = $api_url_by_brain . "/api/v2/BrainService/TransactionVerificationWithToken";

        $data = array(
            "session_Token"  => $session_token
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
            CURLOPT_CONNECTTIMEOUT => 120,
            CURLOPT_TIMEOUT        => 120,
            CURLOPT_MAXREDIRS      => 10,
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
        if(isset($response_arr) && $response_arr['status'] == '200'){
            $getUser =  User::with('userDetails')->where('invoice_no', $response_arr['InvoiceNo'])->update([
                'status'=>1
            ]);

            $updateUser =  User::with('userDetails')->where('invoice_no', $response_arr['InvoiceNo'])->first();
            if($updateUser && $updateUser->userDetails->class_id == 12){
                $paymentType = 'Year Change Fee';
            }
            elseif($updateUser && $updateUser->userDetails->class_id == 11){
                $paymentType = 'Admission Fee';
            }
            else{
                $paymentType = '';
            }
            $updateUsers = StudentPayment::where('session_token', $session_token)->update([
                'user_id' => $updateUser->id,
                'bank_ref' =>$updateUser->bank_ref,
                'invoice_no' =>$updateUser->invoice_no,
                'payment_to' =>$updateUser->payment_to,
                'payment_type' =>$paymentType,
                'transaction_id' => $response_arr['TransactionId'],
                'transaction_date' => $response_arr['InvoiceDate'],
                'amount' => $response_arr['TotalAmount'],
                'payment_status' =>$response_arr['PaymentStatus'],
                'payment_status_code' => $response_arr['status'],
                'pay_mode' =>$response_arr['PayAmount'],
                'payment_status' =>'success',
                'session_token' => $session_token
            ]);
            return view('backend.admission.invoice', compact('response_arr'));

            /*echo "This is a successful transaction. Store this transaction as paid.<br>";
            echo "<pre>";
            print_r($response_arr);*/

        }

        elseif(isset($response_arr) && $response_arr['status'] == '5017'){
            $updateUser =  User::with('userDetails')->where('invoice_no', $response_arr['InvoiceNo'])->first();
            if($updateUser && $updateUser->userDetails->class_id == 12){
                $paymentType = 'Year Change Fee';
            }
            elseif($updateUser && $updateUser->userDetails->class_id == 11){
                $paymentType = 'Admission Fee';
            }
            else{
                $paymentType = '';
            }
            $updateUsers =  StudentPayment::where('session_token', $session_token)->update([
                'user_id' => $updateUser->id,
                'session_id' =>$updateUser->userDetails->session_id,
                'institute_id' =>$updateUser->institute_id,
                'class_id' =>$updateUser->userDetails->class_id,
                'program_id' =>$updateUser->userDetails->program_id,
                'section_id' =>$updateUser->userDetails->section_id,
                'group_id' =>$updateUser->userDetails->group_id,
                'bank_ref' =>$updateUser->bank_ref,
                'invoice_no' =>$updateUser->invoice_no,
                'payment_to' =>$updateUser->payment_to,
                'payment_type' =>$paymentType,
                'transaction_id' => $response_arr['TransactionId'],
                'transaction_date' => $response_arr['InvoiceDate'],
                'amount' => $response_arr['TotalAmount'],
                'payment_status' =>$response_arr['PaymentStatus'],
                'payment_status_code' => $response_arr['status'],
                'pay_mode' =>$response_arr['PayAmount'],
                'payment_status' =>'pending',
                'session_token' => $session_token
            ]);
            return view('backend.admission.invoice', compact('response_arr'));
            /*echo 'This is a manual counter voucher. You need to store this value and mark unpaid and generate voucher.<br/>';
            echo "<pre>";
            print_r($response_arr);*/
        }
        else {
            $updateUser =  User::with('userDetails')->where('invoice_no', $response_arr['InvoiceNo'])->first();
            if($updateUser && $updateUser->userDetails->class_id == 12){
                $paymentType = 'Year Change Fee';
            }
            if($updateUser && $updateUser->userDetails->class_id == 11){
                $paymentType = 'Admission Fee';
            }
            else{
                $paymentType = '';
            }
            $updateUsers =  StudentPayment::where('session_token', $session_token)->update([
                'user_id' => $updateUser->id,
                'session_id' =>$updateUser->userDetails->session_id,
                'institute_id' =>$updateUser->institute_id,
                'class_id' =>$updateUser->userDetails->class_id,
                'program_id' =>$updateUser->userDetails->program_id,
                'section_id' =>$updateUser->userDetails->section_id,
                'group_id' =>$updateUser->userDetails->group_id,
                'bank_ref' =>$updateUser->bank_ref,
                'invoice_no' =>$updateUser->invoice_no,
                'payment_to' =>$updateUser->payment_to,
                'payment_type' =>$paymentType,
                'transaction_id' => $response_arr['TransactionId'],
                'transaction_date' => $response_arr['InvoiceDate'],
                'amount' => $response_arr['TotalAmount'],
                'payment_status' =>$response_arr['PaymentStatus'],
                'payment_status_code' => $response_arr['status'],
                'pay_mode' =>$response_arr['PayAmount'],
                'payment_status' =>'fail',
                'session_token' => $session_token
            ]);
            return view('backend.admission.invoice', compact('response_arr'));

            /*            echo "Fail Transaction.";*/
        }

        exit();

    }

}
