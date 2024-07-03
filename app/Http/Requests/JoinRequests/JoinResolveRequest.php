<?php

namespace App\Http\Requests\JoinRequests;

use Illuminate\Foundation\Http\FormRequest;

class JoinResolveRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    
    protected function prepareForValidation() 
    {
        $this->merge(['request_id' => $this->route('id')]);
    }
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
           'request_id' => 'required|numeric',
           'accepted' => 'required|boolean'
        ];
    }
}
