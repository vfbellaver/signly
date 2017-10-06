<?php

namespace App\Http\Requests;

use App\Forms\BillboardForm;

class BillboardCreateRequest extends BaseRequest
{
    public function form(): BillboardForm
    {
        return new BillboardForm($this);
    }

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required',
            'description' => 'nullable',
            'address' => 'required',
            'lat' => 'required',
            'lng' => 'required',
            'digital_driveby' => 'nullable|url',
        ];
    }
}
