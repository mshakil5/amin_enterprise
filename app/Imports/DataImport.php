<?php

namespace App\Imports;

use App\Models\GeneratingBill;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class DataImport implements GeneratingBill
{
    public function model(array $row)
    {
        return new GeneratingBill([
            'column1' => $row['column1'],
            'column2' => $row['column2'],
            // Add more columns as needed
        ]);
    }

    public function chunkSize(): int
    {
        return 1000;
    }
}
