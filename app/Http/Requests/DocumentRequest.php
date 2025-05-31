<?php

namespace App\Http\Requests;

use App\Models\Status;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DocumentRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name'        => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            // 'fase_id'     => ['required', 'exists:fases,id'],

            // status_id solo debe validarse si viene en la request.
            // Es opcional, por eso usamos "nullable".
            // Y si se envía, debe existir en la tabla "statuses".
            'status_id'   => ['nullable', 'exists:statuses,id'],
        ];
    }

    protected function prepareForValidation()
    {
        // El merge en prepareForValidation ya no debe forzar 'status_id'
        // a un valor por defecto en el caso de UPDATE (aunque en CREATE sí podrías asignar el estado inicial).
        // Para mantener la lógica original en 'store', puedes hacer algo así:

        if ($this->isMethod('POST')) {
            // En creación (POST), si no viene status_id, asignamos 'waiting_review'.
            $statusWaiting = Status::where('key', 'waiting_review')->first();
            $this->merge([
                'status_id' => $this->status_id ?? ($statusWaiting->id ?? null),
            ]);
        }
        // En un UPDATE (PUT/PATCH), NO hacemos merge de status_id, pues si el admin lo envía
        // tomará ese valor; si no lo envía, no cambiaremos nada.
    }
}
