<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Repositories\Eloquent\BaseRepository;
use Illuminate\Database\Eloquent\Collection;

class UserRepository implements UserRepositoryInterface
{
    public function getByEmailOrDocument(string $email, string $document): ?Collection
    {
        return User::where('email', '=', $email)
                        ->orWhere('document', '=', $document)->get();
    }

    public function getByEmail(string $email): ?User
    {
        return User::where('email', '=', $email)->get();
    }

    public function create(array $user): User
    {
        return User::create([
            'name' => $user['name'],
            'email' => $user['email'],
            'document' => $user['document'],
            'password' => bcrypt($user['password']),
            'user_type_id' => $user['user_type_id']
        ]);
    }
}
