<?php

namespace App\Models;

use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Model;
use Sushi\Sushi;
use Xendit\BalanceAndTransaction\TransactionApi;
use Xendit\Configuration;

class Transaction extends Model
{
    use Sushi;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $schema = [
        'id' => 'string',
        'reference_id' => 'string',
        'channel_code' => 'string',
        'amount' => 'integer',
        'settlement_status' => 'string',
        'estimated_settlement_time' => 'string',
    ];

    protected $casts = [
        'fee' => 'object',
        'estimated_settlement_time' => 'datetime',
        'created' => 'datetime',
        'updated' => 'datetime',
    ];

    public function getRows()
    {
        // return Cache::remember(Filament::getTenant()->getKey() . '-last-transaction', now()->addHour(), function () {
        Configuration::setXenditKey(config('services.xendit.secret_key'));

        $transactions = (new TransactionApi())->getAllTransactions(
            Filament::getTenant()->business_id,
            ['PAYMENT'],
            ['SUCCESS'],
            [
                'BANK', 'CARDLESS_CREDIT', 'PAYLATER',
                'CARDS', 'CASH', 'DIRECT_DEBIT',
                'EWALLET', 'INVOICE', 'QR_CODE',
                'RETAIL_OUTLET', 'VIRTUAL_ACCOUNT', 'XENPLATFORM',
                'DIRECT_BANK_TRANSFER', 'OTHER',
            ],
            limit: 10,
        );

        return $this->objectToArray($transactions->getData());
        // });
    }

    public static function objectToArray($object)
    {
        $object = json_decode(json_encode($object), true);

        foreach ($object as $objectKey => $transaction) {
            foreach ($transaction as $transactionKey => $value) {
                if (in_array($transactionKey, ['fee', 'type'])) {
                    $transaction[$transactionKey] = json_encode($value);
                }

                $object[$objectKey] = $transaction;
            }
        }

        return $object;
    }
}
