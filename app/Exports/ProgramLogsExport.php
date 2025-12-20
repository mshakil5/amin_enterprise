<?php


namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ProgramLogsExport implements FromCollection, WithHeadings, WithMapping
{
    protected $logs;

    public function __construct($logs)
    {
        // We flatten the grouped collection into a single list for the Excel rows
        $this->logs = $logs->flatten();
    }

    public function collection()
    {
        return $this->logs;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Program ID',
            'Dest Qty',
            'Old Qty'
        ];
    }

    /**
    * @var mixed $log
    */
    public function map($log): array
    {
        // Decode the properties JSON if it is a string
        $properties = is_string($log->properties) ? json_decode($log->properties, true) : $log->properties;

        return [
            $log->subject_id,                                     // Log Table ID
            93,                                           // Static Program ID
            $properties['attributes']['dest_qty'] ?? '',  // New Qty
            '',                                           // Old Qty (requested as empty)
        ];
    }
}