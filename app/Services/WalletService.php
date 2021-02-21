<?php

namespace App\Services;

use App\Models\Wallet;

class WalletService
{
    public function create(array $wallet): Wallet
    {
        return Wallet::create($wallet);
    }
}
