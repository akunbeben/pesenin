<?php

namespace App\Models;

use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Sushi\Sushi;
use Xendit\BalanceAndTransaction\TransactionApi;
use Xendit\Configuration;

class Transaction extends Model
{
    use Sushi;

    public function getRows()
    {
        return Cache::remember(Filament::getTenant()->getKey() . '-last-transaction', now()->addHour(), function () {
            Configuration::setXenditKey(config('services.xendit.secret_key'));

            return (new TransactionApi())->getAllTransactions(
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
            )['data'];
        });
    }
}
