<?php

namespace App\Http\Requests;

use App\Models\Agent;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdateLeadAssignmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('assign', $this->route('lead')) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'agent_id' => [
                'present',
                Rule::requiredIf(! $this->user()->isAdmin()),
                'nullable',
                'integer',
                Rule::exists(Agent::class, 'id'),
            ],
        ];
    }

    /**
     * @return array<int, callable>
     */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                if ($validator->errors()->has('agent_id') || ! $this->filled('agent_id')) {
                    return;
                }

                if (! Agent::query()->eligibleForLeadAssignment()->whereKey($this->integer('agent_id'))->exists()) {
                    $validator->errors()->add('agent_id', __('Select an agent with an enabled agent account.'));
                }

                if (! $this->user()->isAdmin() && $this->integer('agent_id') === $this->route('lead')->agent_id) {
                    $validator->errors()->add('agent_id', __('Select another agent to reassign this lead.'));
                }
            },
        ];
    }
}
