<?php

namespace App\Exports;

use App\Models\History;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Carbon\Carbon;

class HistoriesExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $qualityControlId;

    public function __construct($qualityControlId)
    {
        $this->qualityControlId = $qualityControlId;
    }

    public function collection()
    {
        return History::where('quality_control_id', $this->qualityControlId)
            ->with(['user', 'qualityControl'])
            ->latest()
            ->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Usuario',
            'Auditoría',
            'Descripción',
            'Fecha',
            'Hora',
        ];
    }

    public function map($history): array
    {
        return [
            $history->id,
            $history->user->name ?? 'N/A',
            $history->qualityControl->name ?? 'N/A',
            $history->description,
            $history->getRawOriginal('created_at') ? Carbon::parse($history->getRawOriginal('created_at'))->format('d/m/Y') : 'N/A',
            $history->getRawOriginal('created_at') ? Carbon::parse($history->getRawOriginal('created_at'))->format('H:i:s') : 'N/A',
        ];
    }
}
