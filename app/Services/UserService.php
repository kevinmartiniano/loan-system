<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserType;
use Illuminate\Http\Response;
use JsonException;

class UserService {
    public function createUser(array $user): User
    {
        if(!$this->passwordEquals($user['password'], $user['password_confirmation'])) {
            $response = 'Passwords do not match';

            throw new JsonException($response, Response::HTTP_PRECONDITION_FAILED);
        }

        if($this->userExists($user['email'], $user['document'])) {
            $response = 'User already exists!';

            throw new JsonException($response, Response::HTTP_CONFLICT);
        }

        $userType = (!empty($user['user_type_id']) ? $user['user_type_id'] : UserType::GENERAL);

        return User::create([
            'name' => $user['name'],
            'email' => $user['email'],
            'document' => $user['document'],
            'password' => bcrypt($user['password']),
            'user_type_id' => $userType
        ]);
    }

    public function passwordEquals(string $password, string $passwordConfirmation): bool
    {
        return $password === $passwordConfirmation;
    }

    public function userExists(string $email, string $document): bool
    {
        $user = User::where('email', '=', $email)
                        ->orWhere('document', '=', $document)->first();

        return !empty($user);
    }
}