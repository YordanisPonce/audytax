<?php

namespace App\Http\Requests;

use App\Models\Fase;
use Illuminate\Foundation\Http\FormRequest;

class CommentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'comment' => 'required|string'
        ];
    }

    protected function prepareForValidation()
    {
        $fase = Fase::find($this->fase_id);
        $this->merge([
            'quality_control_id' => $fase->qualityControl->id,
            'comment_id' => $this->comment_id ?: null
        ]);
    }
}
