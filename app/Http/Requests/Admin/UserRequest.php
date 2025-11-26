<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\User;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Only admin users can manage other users
        return $this->user() && $this->user()->role === 'admin';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $userId = $this->route('user') ? $this->route('user')->id : null;
        $isUpdate = $this->isMethod('patch') || $this->isMethod('put');
        
        $isStaffRole = in_array($this->input('role'), ['nurse', 'aesthetician']);
        $passwordRules = ['string', 'min:8'];

        if ($isUpdate) {
            array_unshift($passwordRules, 'nullable');
            $passwordRules[] = 'confirmed';
        } else {
            if ($isStaffRole) {
                array_unshift($passwordRules, 'nullable');
            } else {
                array_unshift($passwordRules, 'required');
                $passwordRules[] = 'confirmed';
            }
        }

        return [
            'first_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($userId),
            ],
            'password' => $passwordRules,
            'gender' => ['required', 'in:male,female,other'],
            'mobile_number' => ['required', 'string', 'max:20'],
            'address' => ['required', 'string', 'max:500'],
            'birth_date' => ['required', 'date', 'before:today'],
            'role' => ['required', 'in:admin,nurse,aesthetician,client'],
            'service_ids' => ['required_if:role,aesthetician', 'array', 'min:1'],
            'service_ids.*' => ['exists:services,id'],
            'profile_picture' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'first_name.required' => 'First name is required.',
            'last_name.required' => 'Last name is required.',
            'email.required' => 'Email address is required.',
            'email.unique' => 'This email address is already taken.',
            'password.required' => 'Password is required.',
            'password.min' => 'Password must be at least 8 characters.',
            'password.confirmed' => 'Password confirmation does not match.',
            'gender.required' => 'Gender selection is required.',
            'gender.in' => 'Please select a valid gender option.',
            'mobile_number.required' => 'Mobile number is required.',
            'address.required' => 'Address is required.',
            'birth_date.required' => 'Birth date is required.',
            'birth_date.before' => 'Birth date must be before today.',
            'role.required' => 'User role is required.',
            'role.in' => 'Please select a valid user role.',
            'profile_picture.image' => 'Profile picture must be an image.',
            'profile_picture.mimes' => 'Profile picture must be a JPEG, PNG, JPG, or GIF file.',
            'profile_picture.max' => 'Profile picture must not be larger than 2MB.',
        ];
    }

    /**
     * Get the validated data from the request.
     */
    public function validated($key = null, $default = null)
    {
        $validated = parent::validated($key, $default);
        
        // Remove password if it's empty during update
        if (($this->isMethod('patch') || $this->isMethod('put')) && empty($validated['password'])) {
            unset($validated['password'], $validated['password_confirmation']);
        }
        
        return $validated;
    }
}
