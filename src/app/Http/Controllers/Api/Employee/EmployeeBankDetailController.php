<?php

namespace App\Http\Controllers\Api\Employee;

use App\Http\Controllers\Controller;
use App\Models\Core\Auth\User;
use App\Models\Tenant\Employee\UserContact;
use Illuminate\Http\Request;

class EmployeeBankDetailController extends Controller
{
    public function index()
    {
        $employee = User::query()->find(auth()->id());
        $bankDetails = $employee->bankDetails()->first();

        $data = [
            'id' => '',
            'name' => '',
            'code' => '',
            'branch_name' => '',
            'account_title' => '',
            'account_holder_name' => '',
            'account_number' => '',
            'tax_payer_id' => '',
        ];

        if ($bankDetails) {
            $data = [
                'id' => $bankDetails->id,
                'name' => $bankDetails->value->name,
                'code' => $bankDetails->value->code,
                'branch_name' => $bankDetails->value->branch_name,
                'account_title' => $bankDetails->value->account_title,
                'account_holder_name' => $bankDetails->value->account_holder_name,
                'account_number' => $bankDetails->value->account_number,
                'tax_payer_id' => $bankDetails->value->tax_payer_id,
            ];
        }

        return success_response('Account information', $data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required'],
            'branch_name' => ['required'],
            'account_title' => ['required'],
            'account_holder_name' => ['required'],
            'account_number' => ['required'],
        ]);
        $request['user_id'] = auth()->id();

        $employee = User::query()->find(auth()->id());

        $employee->bankDetails()->save(new UserContact([
            'key' => 'bank_details',
            'value' => json_encode($request->only(
                'name',
                'code',
                'branch_name',
                'account_title',
                'account_holder_name',
                'account_number',
                'tax_payer_id',
                'user_id'
            ))
        ]));

        return success_response('Account information added', []);
    }

    public function update(Request $request)
    {
        $request->validate([
            'name' => ['required'],
            'branch_name' => ['required'],
            'account_title' => ['required'],
            'account_holder_name' => ['required'],
            'account_number' => ['required'],
        ]);

        $request['user_id'] = auth()->id();

        $employee = User::query()->find(auth()->id());

        $employee->bankDetails()->update([
            'value' => $request->only('name', 'code', 'branch_name', 'account_title', 'account_holder_name', 'account_number', 'tax_payer_id')
        ]);

        return success_response('Account information updated');
    }

    public function destroy(User $user)
    {
        $contact = UserContact::query()->find(request()->get('contact_id'));

        if ($contact)
            $contact->delete();

        return success_response('Account information deleted');
    }
}
