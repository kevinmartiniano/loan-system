<?php

namespace App\Repositories\Interfaces;

use App\Models\Transaction;
use Illuminate\Database\Eloquent\Collection;

interface TransactionRepositoryInterface
{
    public function update(array $data, $id): Transaction;

    public function create(array $data): Transaction;
}
