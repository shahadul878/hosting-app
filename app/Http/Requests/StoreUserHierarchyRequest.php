<?php

namespace App\Http\Requests;

use App\Enums\MvpRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUserHierarchyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isSuperAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'mvp_role' => ['required', Rule::enum(MvpRole::class)],
            'parent_id' => ['nullable', 'exists:users,id'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            $role = $this->input('mvp_role');
            $parentId = $this->input('parent_id');

            if (in_array($role, [MvpRole::SubReseller->value, MvpRole::Client->value], true) && empty($parentId)) {
                $validator->errors()->add('parent_id', 'A parent user is required for this role.');
            }

            if ($role === MvpRole::SuperAdmin->value && $parentId) {
                $validator->errors()->add('parent_id', 'Super admin cannot have a parent.');
            }
        });
    }
}
