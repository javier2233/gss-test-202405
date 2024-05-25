<?php

namespace App\Http\Controllers;

use App\Services\AccountServices;
use App\Services\TrxServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{

    protected Object $accountServices;
    protected Object $trxServices;
    protected String $defaultMessage;

    public function __construct(AccountServices $accountServices, TrxServices $trxServices)
    {
        $this->accountServices = $accountServices;
        $this->trxServices = $trxServices;
        $this->defaultMessage = env('DEFAULT_MESSAGE', 'The operation could not be performed');
    }

   public function recharge(Request $request)
   {
       $response = new \stdClass();
       $response->status = false;
       $response->message = $this->defaultMessage;
       $response->code = 201;

       try {
           $validated = $request->validate([
               'userId' => 'required|integer',
               'value' => 'required|integer|min:1000',
               'accountId' => 'required|integer',
           ]);

           //validate account owner user
           $validateAccount = $this->accountServices->validateUserAndAccount($request->userId, $request->accountId);
           if ($validateAccount->status) {
               //TODO recharge process
               $recharge = $this->trxServices->generateRecharge($request->accountId, $request->value);
               //TODO update account
               $this->accountServices->updateAccount($request->accountId, $request->value);
               $response = $recharge;

           }else {
               $response = $validateAccount;
           }

       }catch (\Exception $e){
           $response->debug = $e->getMessage();
       }

       return response()->json(compact('response'),  $response->code);


   }
   public function transfer(Request $request)
   {
       $response = new \stdClass();
       $response->status = false;
       $response->message = $this->defaultMessage;
       $response->code = 201;

       try {

           $validated = $request->validate([
               'to' => 'required|integer',
               'from' => 'required|integer',
               'value' => 'required|integer',
           ]);

           //validate account owner user
           $user = Auth::user();
           if($user->type_document != 'NIT') {
               $validateAccount = $this->accountServices->validateUserAndAccount($user->id, $request->from);
               $validateAccountExist = $this->accountServices->validateAccountExist($request->to);
               if ($validateAccount->status && $validateAccountExist->status) {

                   //TODO transfer process
                   $response = $this->trxServices->generateTransfer($request->all());
                   $response->status = true;

               }else {
                   if($validateAccount->status) {
                       $response = $validateAccountExist;
                   } else {
                       $response = $validateAccount;

                   }
               }

           } else {
               $response->message = "Companies doesn't transfer to anything";
           }

           //TODO process notification


       }catch (\Exception $e){
           $response->debug = $e->getMessage();
       }

       return response()->json(compact('response'), $response->code);


   }
   private function validateValueAuthorization($value): bool
   {
       $limitValue = env('LIMIT_VALUE', '9000000');
       if($limitValue >= $value) {
           return true;
       }
       return false;

   }
}
