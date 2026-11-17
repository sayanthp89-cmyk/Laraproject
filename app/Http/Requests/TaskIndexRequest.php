<?php

namespace App\Http\Requests;

use App\Models\Task;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TaskIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'search' => ['sometimes', 'nullable', 'string', 'max:120'],
            'status' => ['sometimes', 'nullable', Rule::in(Task::STATUSES)],
            'priority' => ['sometimes', 'nullable', Rule::in(Task::PRIORITIES)],
            'due_date_from' => ['sometimes', 'nullable', 'date_format:Y-m-d'],
            'due_date_to' => ['sometimes', 'nullable', 'date_format:Y-m-d'],
            'sort_by' => ['sometimes', 'string', Rule::in(['created_at', 'due_date', 'priority', 'status', 'title'])],
            'sort_direction' => ['sometimes', 'string', Rule::in(['asc', 'desc'])],
            'per_page' => ['sometimes', 'integer', 'between:1,100'],
        ];
    }
}
