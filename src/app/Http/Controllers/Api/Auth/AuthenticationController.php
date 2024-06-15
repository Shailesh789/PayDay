<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Core\Auth\User\LoginRequest;
use App\Services\Api\AuthenticationService;
use App\Services\Core\Auth\AuthenticateService;
use Illuminate\Http\Request;

class AuthenticationController extends Controller
{
    public function __construct(AuthenticationService $service)
    {
        $this->service = $service;
    }

    public function login(LoginRequest $request)
    {
        try {
            $data = $this->service
                ->setAttributes($request->validated())
                ->login();
            return success_response(__t('login_successfully'), $data);

        } catch (\Exception $exception) {
            return error_response($exception->getMessage());
        }
    }

    public function user()
    {
        return request()->user();
    }

    public function logOut()
    {
        $this->service
            ->setModel(request()->user())
            ->logout();
        return success_response(__t('logout_successfully'));
    }

    public function passwordReset(Request $request)
    {
        try {
            return success_response(__t('password_reset_link') ,['url' => route('password-reset.index')]);
        } catch (\Exception $exception) {
            return error_response($exception->getMessage());
        }
    }

}
