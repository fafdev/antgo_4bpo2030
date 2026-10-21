<?php

namespace App\Http\Requests;

use App\Models\CodeList;
use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('user')) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, ValidationRule|string>|string>
     */
    public function rules(): array
    {
        /** @var User $user */
        $user = $this->route('user');
        $roleRule = Rule::exists('code_list_items', 'id')
            ->where(fn ($query) => $query
                ->whereIn(
                    'code_list_id',
                    CodeList::query()->select('id')->where('key', 'role_codelist'),
                )
                ->where(function ($query) use ($user): void {
                    $query->where('active', true);

                    if ($user->role_codelist !== null) {
                        $query->orWhere('id', $user->role_codelist);
                    }
                }));

        if ($this->user()?->is($user) === true) {
            $roleRule = Rule::in([$user->role_codelist]);
        }

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user),
            ],
            'role_codelist' => ['nullable', 'integer', $roleRule],
        ];
    }
}
