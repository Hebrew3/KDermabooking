<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ServiceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Only admin users can manage services
        return $this->user() && $this->user()->role === 'admin';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $isUpdate = $this->isMethod('patch') || $this->isMethod('put');

        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:5000'],
            'short_description' => ['nullable', 'string', 'max:500'],
            'price' => ['required', 'numeric', 'min:0', 'max:999999.99'],
            'duration' => ['required', 'integer', 'min:1', 'max:1440'], // Max 24 hours
            'category' => ['required', 'string', 'max:100'],
            'image' => [
                $isUpdate ? 'nullable' : 'required',
                'image',
                'mimes:jpeg,png,jpg,gif,webp',
                'max:5120' // 5MB
            ],
            'gallery_images' => ['nullable', 'array', 'max:10'],
            'gallery_images.*' => ['image', 'mimes:jpeg,png,jpg,gif,webp', 'max:5120'],
            'is_active' => ['boolean'],
            'is_featured' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'tags' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Service name is required.',
            'name.max' => 'Service name must not exceed 255 characters.',
            'description.required' => 'Service description is required.',
            'description.max' => 'Service description must not exceed 5000 characters.',
            'short_description.max' => 'Short description must not exceed 500 characters.',
            'price.required' => 'Service price is required.',
            'price.numeric' => 'Price must be a valid number.',
            'price.min' => 'Price must be at least 0.',
            'price.max' => 'Price must not exceed 999,999.99.',
            'duration.required' => 'Service duration is required.',
            'duration.integer' => 'Duration must be a whole number.',
            'duration.min' => 'Duration must be at least 1 minute.',
            'duration.max' => 'Duration must not exceed 1440 minutes (24 hours).',
            'category.required' => 'Service category is required.',
            'category.max' => 'Category must not exceed 100 characters.',
            'image.required' => 'Service image is required.',
            'image.image' => 'Service image must be an image file.',
            'image.mimes' => 'Service image must be a JPEG, PNG, JPG, GIF, or WebP file.',
            'image.max' => 'Service image must not be larger than 5MB.',
            'gallery_images.array' => 'Gallery images must be an array.',
            'gallery_images.max' => 'You can upload a maximum of 10 gallery images.',
            'gallery_images.*.image' => 'Each gallery image must be an image file.',
            'gallery_images.*.mimes' => 'Each gallery image must be a JPEG, PNG, JPG, GIF, or WebP file.',
            'gallery_images.*.max' => 'Each gallery image must not be larger than 5MB.',
            'sort_order.integer' => 'Sort order must be a whole number.',
            'sort_order.min' => 'Sort order must be at least 0.',
            'tags.max' => 'Tags must not exceed 1000 characters.',
        ];
    }

    /**
     * Get the validated data from the request.
     */
    public function validated($key = null, $default = null)
    {
        $validated = parent::validated($key, $default);

        // Set default values for boolean fields
        $validated['is_active'] = $this->boolean('is_active', true);
        $validated['is_featured'] = $this->boolean('is_featured', false);

        // Set default sort order if not provided
        if (!isset($validated['sort_order'])) {
            $validated['sort_order'] = 0;
        }

        return $validated;
    }
}
