<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    /**
    * UserRepository constructor.
    *
    * @param User $model
    */
   public function __construct(User $model)
   {
       parent::__construct($model);
   }

    public function getByEmailOrDocument(string $email, string $document): ?Collection
    {
        return $this->model->where('email', '=', $email)
                        ->orWhere('document', '=', $document)->get();
    }

    public function getByEmail(string $email): ?Collection
    {
        return $this->model->where('email', '=', $email)->get();
    }

    public function create(array $user): User
    {
        return $this->model->create([
            'name' => $user['name'],
            'email' => $user['email'],
            'document' => $user['document'],
            'password' => bcrypt($user['password']),
            'user_type_id' => $user['user_type_id']
        ]);
    }
}
