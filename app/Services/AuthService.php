<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserType;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use JsonException;

class AuthService
{
    private UserRepositoryInterface $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function createUser(array $user): User
    {
        if (!$this->passwordEquals($user['password'], $user['password_confirmation'])) {
            $response = 'Passwords do not match';

            throw new JsonException($response, Response::HTTP_PRECONDITION_FAILED);
        }

        if ($this->userExists($user['email'], $user['document'])) {
            $response = 'User already exists!';

            throw new JsonException($response, Response::HTTP_CONFLICT);
        }

        $user['user_type_id'] = (!empty($user['user_type_id']) ? $user['user_type_id'] : UserType::DEFAULT);

        return $this->userRepository->create($user);
    }

    public function passwordEquals(string $password, string $passwordConfirmation): bool
    {
        return $password === $passwordConfirmation;
    }

    public function userExists(string $email, string $document): bool
    {
        $user = $this->userRepository->getByEmailOrDocument($email, $document)->first();

        return !empty($user);
    }

    public function authenticate(array $data): ?Collection
    {
        $auth = Auth::attempt($data);

        if ($auth) {
            return $this->userRepository->getByEmail($data['email']);
        }
    }
}
