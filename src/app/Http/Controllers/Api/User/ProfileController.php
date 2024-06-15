<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Core\Auth\User\UserThumbnailController;
use App\Http\Controllers\Core\Auth\User\UserUpdateController;
use App\Http\Resources\Payday\User\ProfileResource;
use App\Services\Api\User\ProfileService;
use App\Http\Requests\Core\Auth\User\UpdateUserPasswordRequest;
use App\Http\Requests\Core\Auth\User\UserSettingRequest;
use App\Http\Requests\Core\Auth\User\UserThumbnailRequest;


use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
   public function index()
   {
      try {
         $profile = resolve(ProfileService::class)->profile();
         return success_response('Profile data.', new ProfileResource($profile));
      } catch (\Exception $ex) {
         return error_response('Internal Server Error!', [], 500);
      }
   }

   public function update(UserSettingRequest $request)
   {
      try {
         $response = resolve(UserUpdateController::class)->update($request);
         if (is_array($response)) {
            $response['data'] = [];
         }
         return $response;
      } catch (\Exception $ex) {
         return error_response('Internal server error!', [], 500);
      }
   }

   public function changePicture(UserThumbnailRequest $request)
   {
      try {
         $request = $request->merge(['user_id' => Auth::id()]);
         $response = resolve(UserThumbnailController::class)->store($request);
         if (is_array($response)) {
            $response['data'] = [];
         }
         return $response;
      } catch (\Exception $ex) {
         return error_response('Internal server error!', [], 500);
      }
   }

   public function changePassword(UpdateUserPasswordRequest $request)
   {
      try {
         $response = resolve(ProfileService::class)->changePassword($request, Auth::user());
         if (is_array($response)) {
            $response['data'] = [];
         }
         return $response;
      } catch (\Exception $ex) {
         return error_response('Internal server error!', [], 500);
      }
   }
}
