<?php

namespace App\Repositories;

use App\Models\UserType;
use App\Repositories\Interfaces\UserTypeRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class UserTypeRepository extends BaseRepository implements UserTypeRepositoryInterface
{
    public function __construct(UserType $model)
    {
        parent::__construct($model);
    }

    public function all(): ?Collection
    {
        return $this->model->all();
    }

    public function create(array $data): UserType
    {
        return $this->model->create($data);
    }

    public function update(array $data, $id): UserType
    {
        $userType = $this->model->find($id);
        $userType->update($data);

        return $userType;
    }

    public function delete($id): void
    {
        ($this->model->find($id))->delete();

        return;
    }
}
