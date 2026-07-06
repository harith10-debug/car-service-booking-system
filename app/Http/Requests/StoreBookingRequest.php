<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->role === 'customer';
    }

    public function rules(): array
    {
        return [
            'vehicle_id' => [
                'required',
                Rule::exists('vehicles', 'id')->where('user_id', auth()->id()),
            ],
            'service_package_id' => [
                'required',
                Rule::exists('service_packages', 'id')->where('status', 'Active'),
            ],
            'preferred_date' => ['required', 'date', 'after_or_equal:today'],
            'preferred_time' => ['required', 'date_format:H:i'],
            'additional_notes' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
