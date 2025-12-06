<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;

class StoreTaskRequest extends FormRequest
{
    public function authorize(){ return true; }

    public function rules()
    {
        return [
            'title' => 'required|string|max:191',
            'description' => 'nullable|string',
            'status' => 'nullable|in:pending,in_progress,completed',
            'priority' => 'nullable|in:low,medium,high',
            'due_date' => 'nullable|date',
            'category_id' => 'nullable|integer|exists:categories,id',
            'attachment' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120' // up to 5MB
        ];
    }
}
