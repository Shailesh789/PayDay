<?php

namespace App\Services\Api;

use App\Models\Core\Auth\User;
use App\Services\Core\BaseService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthenticationService extends BaseService
{

    public function __construct(User $model)
    {
        $this->model = $model;
    }

    public function login()
    {
        $this->setModel($this->getUser())
            ->checkAuthentication()
            ->createToken();

        return $this->model;
    }

    public function createToken()
    {
        $this->model->token = $this->model->createToken('Payday')->plainTextToken;
        return $this;
    }

    public function getUser()
    {
        $user = $this->model::query()
            ->where('email', $this->getAttr('email'))
            ->select('id', 'first_name', 'last_name', 'email', 'password')
            ->first();

        if (!$user) {
            throw ValidationException::withMessages([
                'email' => ['The provided email not found.'],
            ]);
        }

        return $user;
    }

    public function checkAuthentication()
    {
        if (!Hash::check($this->getAttr('password'), $this->model->password)) {
            throw ValidationException::withMessages([
                'email' => ['The password is incorrect.'],
            ]);
        }
        return $this;
    }

    public function logout()
    {
        $this->model->currentAccessToken()->delete();
        return $this;
    }

}