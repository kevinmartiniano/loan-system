<?php

namespace App\Repositories;

use App\Models\Wallet;
use App\Repositories\Interfaces\WalletRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class WalletRepository extends BaseRepository implements WalletRepositoryInterface
{
    /**
    * UserRepository constructor.
    *
    * @param Wallet $model
    */
    public function __construct(Wallet $model)
    {
        parent::__construct($model);
    }

    public function update(array $data, $id): Wallet
    {
        $wallet = $this->model->find($id);
        $wallet->update($data);

        return $wallet;
    }
}
