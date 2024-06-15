<?php

namespace App\Http\Controllers\Tenant\Employee;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\Employee\EmployeeContactRequest;
use App\Models\Core\Auth\User;
use App\Models\Tenant\Employee\UserContact;
use Illuminate\Http\Request;

class EmployeeBankDetailController extends Controller
{
    public function index(User $employee)
    {
        return $employee->bankDetails()->first();
    }

    public function store(User $employee, Request $request)
    {
        $request->validate([
            'name' => ['required'],
            'branch_name' => ['required'],
            'account_title' => ['required'],
            'account_holder_name' => ['required'],
            'account_number' => ['required'],
        ]);
        $employee->bankDetails()->save(new UserContact([
            'key' => 'bank_details',
            'value' => json_encode($request->only('name', 'code', 'branch_name', 'account_title', 'account_holder_name', 'account_number', 'tax_payer_id'))
        ]));

        return created_responses('bank_details');
    }

    public function show(User $employee, UserContact $bank_detail)
    {
        return $bank_detail;
    }

    public function update(User $employee,UserContact $bank_detail, Request $request)
    {
        $request->validate([
            'name' => ['required'],
            'branch_name' => ['required'],
            'account_title' => ['required'],
            'account_holder_name' => ['required'],
            'account_number' => ['required'],
        ]);
        $bank_detail->update([
            'value' => $request->only('name', 'code', 'branch_name', 'account_title', 'account_holder_name', 'account_number', 'tax_payer_id')
        ]);

        return updated_responses('bank_details');
    }

    public function destroy(User $employee, UserContact $bank_detail)
    {

        $bank_detail->delete();


        return deleted_responses('bank_details');
    }
}
