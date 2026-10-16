<?php

namespace App\Http\Requests;

use App\Models\CodeList;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCodeListItemRequest extends FormRequest
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
            'code' => [
                'required',
                'string',
                'max:100',
                Rule::unique('code_list_items', 'code')->where(
                    fn (Builder $query): Builder => $query->where('code_list_id', $codeList->id)
                ),
            ],
            'label' => ['required', 'string', 'max:255'],
            'sort_order' => ['required', 'integer', 'min:0'],
            'active' => ['nullable', 'boolean'],
        ];
    }
}
