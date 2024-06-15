<?php

namespace App\Http\Requests\Tenant\Attendance;

use Illuminate\Foundation\Http\FormRequest;

class AttendanceRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }


    public function rules()
    {
        return [
            'ip_data' => 'required',
            'today' => 'required'
        ];
    }
}
