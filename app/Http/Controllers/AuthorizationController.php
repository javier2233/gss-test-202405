<?php

namespace App\Http\Controllers;

use App\Services\AccountServices;
use App\Services\TrxServices;
use Illuminate\Http\Request;

class AuthorizationController extends Controller
{
    private AccountServices $accountServices;
    private TrxServices $trxServices;
    public function __construct(AccountServices $accountServices, TrxServices $trxServices)
    {
        $this->accountServices = $accountServices;
        $this->trxServices = $trxServices;
        $this->defultMessage = env('DEFAULT_MESSAGE', 'The operation could not be performed');
    }
    public function approvedTransfer(Request $request)
    {
        $response = new \stdClass();
        $response->status = false;
        $response->message = $this->defultMessage;
        $response->code = 201;

        try {

            $validated = $request->validate([
                'userId' => 'required|integer',
                'trx' => 'required|integer',
                'approved' => 'required|boolean',
            ]);

            $checkTrx = $this->trxServices->validatePendingTrx($request->trx);
            if ($checkTrx) {

                switch ($request['approved']) {
                    case true:
                        $response = $this->trxServices->approvedTransfer($request->all());
                        break;
                    case false:
                        $response =  $this->trxServices->offTransfer($request->all());
                        break;
                }
            }

        }catch (\Exception $e) {
            $response->debug = $e->getMessage();
            $response->code = 500;

        }

        return response()->json(compact('response'), $response->code);



    }
}
