<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\StudentPayment;
use http\Client\Curl\User;
use Illuminate\Http\Request;

class ResponsePaymentController extends Controller
{
    public function studentPayment(Request $request, $user)
    {
        $accessToken = $user->createToken('authToken')->accessToken;

        return response(['status_code' => 200,
            'access_token' => $accessToken],
            201);
    }


}
