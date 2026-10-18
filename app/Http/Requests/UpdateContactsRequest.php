<?php

namespace App\Http\Requests;

use App\Models\Contacts;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateContactsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('contact')) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        /** @var Contacts $contact */
        $contact = $this->route('contact');
        $rules = (new StoreContactsRequest)->rules();
        $rules['taxId'][3] = Rule::unique('contacts', 'taxId')->ignore($contact);
        $rules['addresses.*.id'] = [
            'sometimes',
            'integer',
            Rule::exists('addresses', 'id')
                ->where('addressable_type', $contact->getMorphClass())
                ->where('addressable_id', $contact->getKey()),
        ];

        return $rules;
    }
}
