<?php

namespace App\Services;

use App\Mail\NotificationMail;
use App\Models\Transactions;
use Illuminate\Support\Facades\Mail;
use mysql_xdevapi\Exception;

class TrxServices
{
    const TYPE_RECHARGE = 'recharge';
    const TYPE_TRANSFER = 'transfer';
    const NEED_AUTHORIZATION = 1;
    const NOT_AUTHORIZATION = 0;
    const PENDING_STATUS = 2;
    const COMPLETE_STATUS = 1;
    const OFF_STATUS = 3;
    private AccountServices $accountServices;

    public function __construct(AccountServices $accountServices)
    {
        $this->accountServices = $accountServices;
        $this->defultMessage = env('DEFAULT_MESSAGE', 'The operation could not be performed');
        $this->activeEmail = env('ACTIVE_EMAIL', false);
    }

    public function generateRecharge($accountId, $value): \stdClass
    {
        $recharge = Transactions::create([
            'from' => $accountId,
            'authorization' => 0,
            'status' => 1,
            'value' => $value,
            'type' => self::TYPE_RECHARGE,
            'to' => $accountId,
        ]);
        return $this->getResponse(true, 'recharge success!', 200);

    }

    public function generateTransfer($data): \stdClass
    {
        $to = $data['to'];
        $value = $data['value'];
        $from = $data['from'];
        //validate balance
        $balance = $this->accountServices->validateBalance($from, $value);
        $userTo = $this->accountServices->getUserByAccountId($to);

        if($balance->status) {
            $needAuthorization = $data['value'] > env('LIMIT_VALUE', '10000000');
            if($needAuthorization) {
                $this->generateTrx($data, self::NEED_AUTHORIZATION, self::PENDING_STATUS);


                $data['status'] = 'pending';
            }else {

                $data['status'] = 'complete';
                $this->accountServices->lessAccount($from, $value);
                $this->accountServices->plusAccount($to, $value);
                $this->generateTrx($data, self::NOT_AUTHORIZATION, self::COMPLETE_STATUS);

            }
            if($this->activeEmail) {

                Mail::to($userTo)->send(new NotificationMail($data));
            }
        }

        return $this->getResponse(true, 'transaction success!', 200);


    }
    public function approvedTransfer($data): \stdClass
    {
        $userId = $data['userId'];
        $trx = $data['trx'];
        //validate balance
        $infoTrx = $this->getInfoTrx($trx);
        $from = $infoTrx->from;
        $value = $infoTrx->value;
        $to = $infoTrx->to;
        $validateTrxOwner = $this->validateTrxOwner($userId, $infoTrx);
        if ($validateTrxOwner->status) {

            $balance = $this->accountServices->validateBalance($from, $value);
            $userTo = $this->accountServices->getUserByAccountId($to);

            if ($balance->status) {
                $data['status'] = 'complete';
                $this->accountServices->lessAccount($from, $value);
                $this->accountServices->plusAccount($to, $value);
                $this->updateCompleteTrx($infoTrx);
                if($this->activeEmail) {

                    Mail::to($userTo)->send(new NotificationMail($data));
                }
            } else {
                return $this->getResponse(true, 'Balance problem', 200);

            }

            return $this->getResponse(true, 'Approved transaction', 200);
        }

        return $this->getResponse(true, 'Error Transaction and User', 200);


    }
    public function offTransfer($data): \stdClass
    {
        $userId = $data['userId'];
        $trx = $data['trx'];
        $infoTrx = $this->getInfoTrx($trx);

        $validateTrxOwner = $this->validateTrxOwner($userId, $infoTrx);
        if ($validateTrxOwner->status) {
            $infoTrx->status = self::OFF_STATUS;
            return $this->getResponse(true, 'Transaction declined', 200);

        }

        return $this->getResponse(true, 'Error Transaction and User', 200);



    }

    private function getResponse(bool $status, string $message, $code)
    {
        $response = new \stdClass();
        $response->status = $status;
        $response->message = $message;
        $response->code = $code;

        return $response;


    }

    private function generateTrx($data, $authorization, $status)
    {
        $recharge = Transactions::create([
            'from' => $data['from'],
            'authorization' => $authorization,
            'status' => $status,
            'value' => $data['value'],
            'type' => self::TYPE_TRANSFER,
            'to' => $data['to'],
        ]);

    }
    private function getInfoTrx($trxId)
    {
        $trx = Transactions::find($trxId);
        if ($trx) {
            return $trx;
        }
        return false;

    }
    public function validatePendingTrx($trxId)
    {
        $trx = Transactions::find($trxId);
        if ($trx) {
            if($trx->status == self::PENDING_STATUS) {
                return true;
            }
        }
        return false;

    }
    private function validateTrxOwner($userId, $trx)
    {
       return  $this->accountServices->validateUserAndAccount($userId, $trx->from);

    }
    private function updateCompleteTrx($trx)
    {
        $trx->status = self::COMPLETE_STATUS;
        $trx->save();

    }
}
