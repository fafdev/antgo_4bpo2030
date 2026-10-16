<?php

namespace App\Http\Requests;

use App\Models\CodeList;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCodeListRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        /** @var CodeList $codeList */
        $codeList = $this->route('codeList');

        return [
            'key' => ['required', 'string', 'max:100', 'alpha_dash', Rule::unique('code_lists', 'key')->ignore($codeList)],
            'name' => ['required', 'string', 'max:255'],
        ];
    }
}
