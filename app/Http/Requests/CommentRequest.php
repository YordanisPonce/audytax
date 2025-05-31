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
        // 1) Si el form trae 'fase_id', lo usamos para obtener quality_control_id:
        if ($this->has('fase_id')) {
            $fase = Fase::find($this->input('fase_id'));
            if ($fase) {
                $this->merge([
                    'quality_control_id' => $fase->qualityControl->id,
                ]);
            }
        }
        // 2) Si no hay 'fase_id', pero la ruta trae un QualityControl,
        //    aprovechamos el route‐model binding para inyectarlo:
        elseif ($this->route('qualityControl')) {
            // En rutas como /qualityControls/{qualityControl}/comments
            $qc = $this->route('qualityControl');
            $this->merge([
                'quality_control_id' => $qc->id,
            ]);
        }

        // 3) Siempre dejamos comment_id (si viene un “reply” anidado):
        $this->merge([
            'comment_id' => $this->comment_id ?: null,
        ]);
    }
}
