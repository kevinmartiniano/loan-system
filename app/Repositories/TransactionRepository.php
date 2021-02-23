<?php

namespace App\Repositories;

use App\Models\Transaction;
use App\Repositories\Interfaces\TransactionRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class TransactionRepository extends BaseRepository implements TransactionRepositoryInterface
{
    /**
    * UserRepository constructor.
    *
    * @param Wallet $model
    */
   public function __construct(Transaction $model)
   {
       parent::__construct($model);
   }

    public function update(array $data, $id): Transaction
    {
        $transaction = $this->model->find($id);
        $transaction->update($data);

        return $transaction;
    }

    public function create(array $data): Transaction
    {
        return Transaction::create($data);
    }
}
