<?php

namespace App\Http\Requests\Messages;

use Illuminate\Foundation\Http\FormRequest;

class MessagePostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    protected function prepareForValidation() 
    {
        error_log(json_encode($this->file));
        $this->merge(['channel_id' => $this->route('id'), 'message'=>$this->message, 'file'=>$this->file]);
    }

    public function rules(): array
    {
        return [
            'channel_id' => 'required|numeric',
            'message' => 'required',
            'file' => 'sometimes|nullable|file'
        ];
    }
}
