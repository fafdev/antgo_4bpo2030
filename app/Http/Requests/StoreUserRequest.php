<?php

namespace App\Http\Requests;

use App\Models\CodeList;
use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('create', User::class) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, ValidationRule|string>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')],
            'role_codelist' => [
                'nullable',
                'integer',
                Rule::exists('code_list_items', 'id')
                    ->where(fn ($query) => $query
                        ->where('active', true)
                        ->whereIn(
                            'code_list_id',
                            CodeList::query()->select('id')->where('key', 'role_codelist'),
                        )),
            ],
        ];
    }
}
