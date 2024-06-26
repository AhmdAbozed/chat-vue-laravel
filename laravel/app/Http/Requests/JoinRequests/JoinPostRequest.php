<?php

namespace App\Http\Requests\JoinRequests;

use App\Models\Channel;
use Closure;
use Illuminate\Foundation\Http\FormRequest;

class JoinPostRequest extends FormRequest
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
    public function rules(): array
    {
        return [
            'channelName' => 'required|alpha_num:ascii'
        ];
    }
}
