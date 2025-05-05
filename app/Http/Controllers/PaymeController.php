<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Transaction;

class PaymeController extends Controller
{
    const test_token = "4aa0n805So%uvv0r@jmCjexE%?is6NtJKtGx";
    const user = "Paycom";

    public function index()
    {

        $auth = \request()->header('Authorization');
        $token = str_replace("Basic ", "", $auth);
        if (base64_encode(self::user . ":" . self::test_token) != $token) {
            return $this->Error(-32504, [
                "ru" => "Not Allowed",
                "en" => "Not Allowed",
                "uz" => "Not Allowed",
            ]);
        }
        $data = \request()->toArray();
        switch ($data['method']) {
            case 'CheckPerformTransaction':
                $perform = $this->CheckPerformTransaction($data['params']);
                if ($perform === true) {
                    $orderItems = OrderItem::query()
                        ->where(['order_id' => $data['params']['account']['order_id']])
                        ->get()
                        ->toArray();
                    $items = [
                        [
                            'title' => "Yetkazib berish",
                            'count' => 1,
                            'price' => @Order::query()->where(['id' => $data['params']['account']['order_id']])->first()->delivery_price * 100 ?? 0,
                            "code"=> "10112006004000000",
                            "vat_percent" => 12,
                            "package_code" => "1202229"
                        ]
                    ];
                    foreach ($orderItems as $orderItem) {
                        $items[] = [
                            'title' => Product::query()->where(['id' => $orderItem['product_id']])->get('name')->first()->name,
                            'count' => $orderItem['quantity'],
                            'price' => Product::query()->where(['id' => $orderItem['product_id']])->get('price')->first()->price * 100,
                            'code' => '10202001001000001',
                            'vat_percent' => 12,
                            'package_code' => '1372863'
                        ];
                    }
                    return json_encode([
                        "result" => [
                            "allow" => true,
                            'detail' =>[
                                'receipt_type'=>0,
                                'items'=>$items
                            ]
                        ]
                    ]);
                }
                return $perform;
                break;
            case 'CreateTransaction':
                return $this->CreateTransaction($data['params']);
                break;
            case 'CheckTransaction':
                if (Transaction::query()->where(['paycom_transaction_id' => $data['params']['id']])->exists()) {
                    $transaction = Transaction::query()->where(['paycom_transaction_id' => $data['params']['id']])->first()->toArray();
                    return json_encode([
                        "result" => [
                            'create_time' => Transaction::datetime2timestamp($transaction['create_time']),
                            'perform_time' => Transaction::datetime2timestamp($transaction['perform_time']) ?? 0,
                            'cancel_time' => $transaction['cancel_time'] ?? 0,
                            'transaction' => (string)$transaction['paycom_transaction_id'],
                            'state' => $transaction['state'],
                            'reason' => $transaction['reason'],
                        ]
                    ]);
                }
                return $this->Error(-31003, [
                    "ru" => "Not Allowed",
                    "en" => "Not Allowed",
                    "uz" => "Not Allowed",
                ]);
                break;
            case 'PerformTransaction':
                return $this->PerformTransaction($data['params']);
            case 'CancelTransaction':
                return $this->CancelTransaction($data['params']);
        }
    }

    private function CheckPerformTransaction(array $params)
    {
        $order = Order::query()->where(['id' => $params['account']['order_id'], 'status' => Order::STATE_WAITING_PAY])->get()->first();
        if (!$order) {
            return $this->Error(-31050, [
                'en' => "Order not found",
                "ru" => "Order not found",
                "uz" => "Order not found",
            ]);
        }
        if ($order->total_price * 100 != $params['amount']) {
            return $this->Error(-31001, [
                'en' => "Order price mismatch",
                "ru" => "Order price mismatch",
                "uz" => "Order price mismatch",
            ]);
        }
        return true;
    }

    private function Error($code, $message)
    {
        return json_encode([
            "error" => [
                "code" => $code,
                "message" => $message
            ]
        ]);
    }

    private function CreateTransaction($params)
    {
        if (($perform = $this->CheckPerformTransaction($params)) !== true) {
            return $perform;
        }
        $order = Order::query()->where(['id' => $params['account']['order_id'], 'status' => Order::STATE_WAITING_PAY])->get()->first();
        $isExists = Transaction::query()->where(['paycom_transaction_id' => $params['id']])->exists();
        if (!$isExists) {
            if (Transaction::query()->where(['order_id' => $params['account']['order_id']])
                ->whereIn('state', [Transaction::STATE_CREATED, Transaction::STATE_COMPLETED])
                ->exists()) {
                return $this->Error(-31050, [
                    'en' => 'There is other active/completed transaction for this order.',
                    'ru' => 'There is other active/completed transaction for this order.',
                    'uz' => 'There is other active/completed transaction for this order.',
                ]);
            }
            //Yo'q bo'lsa\
            if (Transaction::timestamp2milliseconds(1 * $params['time']) - Transaction::timestamp(true) >= Transaction::TIMEOUT) {
                return $this->Error(-31050, [
                    'С даты создания транзакции прошло ' . Transaction::TIMEOUT . 'мс',
                    'Tranzaksiya yaratilgan sanadan ' . Transaction::TIMEOUT . 'ms o`tgan',
                    'Since create time of the transaction passed ' . Transaction::TIMEOUT . 'ms'
                ]);
            }
            $create_time = Transaction::totimestamp(true);
            $transaction = new Transaction();
            $transaction->fill([
                'paycom_transaction_id' => $params['id'],
                'paycom_time' => $params['time'],
                'paycom_time_datetime' => Transaction::timestamp2datetime($params['time']),
                'create_time' => Transaction::timestamp2datetime($create_time),
                'state' => Transaction::STATE_CREATED,
                'amount' => $params['amount'],
                'order_id' => $params['account']['order_id'],
            ]);
            $transaction->save(); // after save $transaction->id will be populated with the newly created transaction's id.

            // send response
            return json_encode([

                "result" => [
                    "create_time" => $create_time,
                    "transaction" => (string)$transaction["paycom_transaction_id"],
                    "state" => $transaction["state"],
                    "receivers" => null,
                ]
            ]);

        } else {
            $transaction = Transaction::query()->where(['paycom_transaction_id' => $params['id']])->first()->toArray();
            if ($transaction['state'] != Transaction::STATE_CREATED) {
                return $this->Error(-31008, [
                    'en' => 'Transaction already created',
                    'ru' => 'Transaction already created',
                    'uz' => 'Transaction already created',
                ]);
            }
            //        return $this->state == self::STATE_CREATED && abs(Format::datetime2timestamp($this->create_time) - Format::timestamp(true)) > self::TIMEOUT;

            if ($transaction['state'] == Transaction::STATE_CREATED
                && abs(Transaction::datetime2timestamp($transaction['create_time']) - Transaction::timestamp(true)) > Transaction::TIMEOUT
            ) {
                Transaction::query()->where(['paycom_transaction_id' => $params['id']])->update([
                    'state' => Transaction::STATE_CANCELLED,
                    'reason' => Transaction::REASON_CANCELLED_BY_TIMEOUT
                ]);
                return $this->Error(-31008, [
                    'en' => 'Transaction cancelled by timeout',
                    'ru' => 'Transaction cancelled by timeout',
                    'uz' => 'Transaction cancelled by timeout',
                ]);
            }
            return json_encode([
                "result" => [
                    "create_time" => Transaction::datetime2timestamp($transaction['create_time']),
                    "transaction" => (string)$transaction["paycom_transaction_id"],
                    "state" => $transaction["state"],
                    "receivers" => null,
                ]
            ]);
        }
    }

    private function PerformTransaction(array $params)
    {
        $transaction = Transaction::query()->where(['paycom_transaction_id' => $params['id']]);
        if (!$transaction->exists()) {
            return $this->Error(-31003, [
                'en' => "Transaction not found",
                "ru" => "Transaction not found",
                "uz" => "Transaction not found",
            ]);
        }
        $transaction = $transaction->first()->toArray();
        switch ($transaction['state']) {
            case Transaction::STATE_CREATED:
//                $params = ['order_id' => $transaction['order_id']];
                Order::query()->where(['id' => $transaction['order_id']])->get()->first()->update(['status' => Order::STATE_PAY_ACCEPTED]);
                // todo: Mark transaction as completed
                $perform_time = Transaction::timestamp(true);
                Transaction::query()->where(['paycom_transaction_id' => $params['id']])
                    ->get()
                    ->first()
                    ->update([
                        'state' => Transaction::STATE_COMPLETED,
                        'perform_time' => Transaction::timestamp2datetime($perform_time),
                    ]);

                return json_encode([
                    "result" => [
                        'transaction' => $params['id'],
                        'perform_time' => $perform_time,
                        'state' => Transaction::STATE_COMPLETED,
                    ]
                ]);
                break;
            case Transaction::STATE_COMPLETED:
                return json_encode([
                    "result" => [
                        'transaction' => $params['id'],
                        'state' => Transaction::STATE_COMPLETED,
                        'perform_time' => Transaction::datetime2timestamp($transaction['perform_time']),
                    ]
                ]);
                break;
            default:
                //  $this->response->error(
                //                    PaycomException::ERROR_COULD_NOT_PERFORM,
                //                    'Could not perform this operation.'
                //                );
                //                break;
                return $this->Error(-31001, [
                    'en' => "Unknown state",
                    'ru' => "Unknown state",
                    "uz" => "Unknown state",
                ]);
        }
    }

    private function CancelTransaction($params)
    {
        $transaction = Transaction::query()->where(['paycom_transaction_id' => $params['id']])->first();
        $cancel_time = Transaction::totimestamp(true);
        $transaction->state = Transaction::STATE_CANCELLED;
        $transaction->reason = $params['reason'];
        $transaction->cancel_time=$cancel_time;
        $transaction->update();
        $transaction->refresh();
        return json_encode([
            "result" => [
                'transaction' => $params['id'],
                'state' => Transaction::STATE_CANCELLED,
                'cancel_time'=>Transaction::datetime2timestamp($cancel_time),
            ]
        ]);
    }

}
