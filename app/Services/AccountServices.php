<?php

namespace App\Services;

use App\Models\Accounts;
use App\Models\User;

class AccountServices
{
    /**
     * @throws \Exception
     */
    public function validateUserAndAccount($userId, $accountId)
    {
        $account = Accounts::where(['id' => $accountId, 'user_id' => $userId])->first();
        if ($account) {
            return $this->getResponse(true, 'find user account', 200);
        }
        return $this->getResponse(false, 'dont find user account', 201);
    }
    public function validateAccountExist($accountId)
    {
        $account = Accounts::find($accountId);
        if ($account) {
            return $this->getResponse(true, 'find user account', 200);
        }
        return $this->getResponse(false, 'dont find user account', 201);
    }

    public function validateBalance($accountId, $value)
    {
        $account = Accounts::find($accountId);
        if ($account) {
            if($account->value >= $value){
                return $this->getResponse(true, 'balance ok', 200);

            }else {
                return $this->getResponse(true, 'insufficient balance', 200);

            }
        }
        return $this->getResponse(false, 'dont find user account', 201);
    }
    public function updateAccount($accountId, $value)
    {
        $account = Accounts::find($accountId);
        if ($account) {
            $account->value += $value;
            $account->save();
        }

    }
    public function lessAccount($accountId, $value)
    {
        $account = Accounts::find($accountId);
        if ($account) {
            $account->value -= $value;

            $account->save();
        }

    }
    public function plusAccount($accountId, $value)
    {
        $account = Accounts::find($accountId);
        if ($account) {
            $account->value += $value;
            $account->save();
        }

    }
    public function getUserByAccountId($accountId)
    {
        $account = Accounts::find($accountId);
        if ($account) {

            return User::find($account->user_id);
        }
        return false;

    }

    private function getResponse(bool $status, string $message, $code)
    {
        $response = new \stdClass();
        $response->status = $status;
        $response->message = $message;
        $response->code = $code;

        return $response;


    }

}
