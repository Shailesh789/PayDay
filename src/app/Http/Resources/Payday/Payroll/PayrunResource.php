<?php

namespace App\Http\Resources\Payday\Payroll;

use Illuminate\Http\Resources\Json\JsonResource;

class PayrunResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'default_payrun' => $this->default_payrun,
            'payrun_setting' => $this->payrunSetting ?? null,
            'payrun_beneficiaries' => $this->payrunBeneficiaries ?? [],
        ];
    }
}
