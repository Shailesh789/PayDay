<?php

namespace App\Http\Controllers\Api\Emergency;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Tenant\Employee\EmployeeContactController;
use App\Http\Requests\Tenant\Employee\EmployeeContactRequest;
use App\Http\Resources\Payday\Employee\EmergencyContactResource;
use App\Models\Tenant\Employee\UserContact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmergencyContactController extends Controller
{
    public function index()
    {
        try {
            $response = resolve(EmployeeContactController::class)->index(Auth::user());
            $data = new EmergencyContactResource($response);
            return success_response('Emergency Contact', $data);
        } catch (\Exception $ex) {
            return error_response('Server Error!', [], 500);
        }
    }

    public function store(EmployeeContactRequest $request)
    {
        try {
            $response = resolve(EmployeeContactController::class)->store(Auth::user(), $request);
            if (is_array($response)) {
                $response['data'] = [];
            }
            return $response;
        } catch (\Exception $ex) {
            return error_response('Server Error!', [], 500);
        }
    }

    public function update(EmployeeContactRequest $request)
    {
        try {
            $auth = Auth::user();
            $userContact = UserContact::where(['user_id' => $auth->id, 'key' => 'emergency_contacts'])->findOrFail($request->emergency_contact_id);
            $response = resolve(EmployeeContactController::class)->update($auth, $userContact, $request);
            if (is_array($response)) {
                $response['data'] = [];
            }
            return $response;
        } catch (\Exception $ex) {
            return error_response($ex->getMessage(), [], $ex->getCode() != 0 ? $ex->getCode() : 500);
        }
    }

    public function destroy(Request $request)
    {
        try {
            $auth = Auth::user();
            $userContact = UserContact::where(['user_id' => $auth->id, 'key' => 'emergency_contacts'])->findOrFail($request->emergency_contact_id);
            $response = resolve(EmployeeContactController::class)->destroy($auth, $userContact);
            if (is_array($response)) {
                $response['data'] = [];
            }
            return $response;
        } catch (\Exception $ex) {
            return error_response($ex->getMessage(), [], $ex->getCode() != 0 ? $ex->getCode() : 500);
        }
    }
}
