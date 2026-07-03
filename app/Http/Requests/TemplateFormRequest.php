<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TemplateFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'header_image' => ['nullable', 'image', 'mimes:png,jpeg,webp', 'max:5120'],
            'footer_image' => ['nullable', 'image', 'mimes:png,jpeg,webp', 'max:5120'],
            'margin_top' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'margin_bottom' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'margin_left' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'margin_right' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'page_size' => ['nullable', Rule::in(['A4', 'A5', 'Letter', 'Legal'])],
            'orientation' => ['nullable', Rule::in(['portrait', 'landscape'])],
            'status' => ['nullable', Rule::in(['active', 'inactive'])],
        ];

        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules['header_image'][0] = 'sometimes';
            $rules['footer_image'][0] = 'sometimes';
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Template name is required.',
            'name.max' => 'Template name cannot exceed 255 characters.',
            'header_image.image' => 'Header image must be a valid image file.',
            'header_image.mimes' => 'Header image must be PNG, JPEG, or WEBP format.',
            'header_image.max' => 'Header image cannot exceed 5MB.',
            'footer_image.image' => 'Footer image must be a valid image file.',
            'footer_image.mimes' => 'Footer image must be PNG, JPEG, or WEBP format.',
            'footer_image.max' => 'Footer image cannot exceed 5MB.',
        ];
    }
}
