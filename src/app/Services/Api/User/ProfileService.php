<?php

namespace App\Services\Api\User;

use App\Services\Core\Auth\UserService;
use App\Services\Core\BaseService;
use Illuminate\Support\Facades\Hash;

class ProfileService extends BaseService
{
    public function profile()
    {
        return auth()->user()->load('profile:id,user_id,gender,date_of_birth,address,contact,about_me,employee_id', 'profilePicture', 'status:id,name,class', 'designation:id,name', 'department:id,name', 'employmentStatus', 'workingShift');
    }

    public function changePassword($request, $user)
    {
        resolve(UserService::class)->validateIsNotDemoVersion();

        if (Hash::check($request->get('old_password'),  $user->password)) {
            $user->update([
                'password' => $request->get('password')
            ]);

            return updated_responses('password');
        }

        return error_response(['old_password' => trans('default.old_password_is_in_correct')], [],  422);
    }
}