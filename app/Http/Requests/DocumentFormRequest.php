<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DocumentFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'template_id' => ['required', 'integer', 'exists:templates,id'],
            'recipient_name' => ['required', 'string', 'max:255'],
            'recipient_address' => ['required', 'string', 'max:2000'],
            'subject' => ['required', 'string', 'max:500'],
            'body_html' => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'template_id.required' => 'Please select a template.',
            'template_id.exists' => 'The selected template is invalid.',
            'recipient_name.required' => 'Recipient name is required.',
            'recipient_address.required' => 'Recipient address is required.',
            'subject.required' => 'Subject is required.',
            'body_html.required' => 'Document body is required.',
        ];
    }
}
