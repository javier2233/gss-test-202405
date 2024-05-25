<?php

namespace App\Http\Controllers;

use App\Models\Accounts as Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AccountsController extends Controller
{

    public function create(): \Illuminate\Http\JsonResponse
    {
        $response = new \stdClass();
        $response->status = false;
        $response->message = $this->defaultMessage;
        $response->code = 201;

        try {
            $user = Auth::user();
            $account = Account::create([
                'user_id' => $user->id,
                'value' => 0,
            ]);
            $response->status = true;
            $response->code = 200;

        }catch (\Exception $e){
            $response->code = 500;
            $response->debug = $e;
        }



        return response()->json(compact('response'), $response->code);


    }
}
