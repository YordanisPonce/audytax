<?php

namespace App\Http\Requests;

use App\Models\Document;
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
            'comment'              => 'required|string',
            'fase_id'              => 'required|exists:fases,id',
            'quality_control_id'   => 'required|exists:quality_controls,id',
            'comment_id'           => 'nullable|exists:comments,id',
            'document_id'          => 'nullable|exists:documents,id',
        ];
    }



    protected function prepareForValidation()
    {
        // 0) Si viene sólo document_id, derivamos fase y QC de ahí
        if ($this->filled('document_id')) {
            $doc = Document::find($this->input('document_id'));
            if ($doc) {
                $this->merge([
                    'fase_id'            => $doc->fase_id,
                    'quality_control_id' => $doc->quality_control_id,
                ]);
            }
        }

        // 1) Si ya nos llegó fase_id, garantizamos quality_control_id
        if ($this->filled('fase_id')) {
            $fase = Fase::find($this->input('fase_id'));
            if ($fase) {
                $this->merge([
                    'quality_control_id' => $fase->qualityControl->id,
                ]);
            }
        }
        // 2) (opcional) Si en la ruta viene directament QC
        elseif ($qc = $this->route('qualityControl')) {
            $this->merge([
                'quality_control_id' => $qc->id,
            ]);
        }

        // 3) Normalizamos comment_id y document_id a null si vienen vacíos
        $this->merge([
            'comment_id'  => $this->input('comment_id')  ?: null,
            'document_id' => $this->input('document_id') ?: null,
        ]);
    }
}