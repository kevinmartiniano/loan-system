<?php

namespace App\Repositories\Interfaces;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

interface UserRepositoryInterface
{
    public function getByEmailOrDocument(string $email, string $document): ?Collection;

    public function getByEmail(string $email): ?Collection;

    public function create(array $user): User;
}
