<?php

namespace App\Repositories\Interfaces;

use App\Models\UserType;
use Illuminate\Database\Eloquent\Collection;

interface UserTypeRepositoryInterface
{
    public function all(): ?Collection;

    public function create(array $data): UserType;

    public function update(array $data, $id): UserType;

    public function delete($id): void;
}
