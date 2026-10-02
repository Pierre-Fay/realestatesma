<?php

namespace App\Http\Requests\Agent;

use App\Enums\LeadStatus;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdateLeadRequest extends FormRequest
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
        return [
            'interested_in' => ['required', 'string', 'max:255'],
            'budget' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:5000'],
            'status' => ['required', Rule::enum(LeadStatus::class)],
        ];
    }

    /**
     * @return array<int, callable>
     */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                $lead = $this->route('lead');

                if ($this->input('status') === LeadStatus::NEW->value && $lead->status !== LeadStatus::NEW) {
                    $validator->errors()->add('status', __('A lead cannot be moved back to new.'));
                }
            },
        ];
    }
}
