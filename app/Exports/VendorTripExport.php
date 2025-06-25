<?php

namespace App\Exports;

use App\Models\ProgramDetail;
use App\Models\VendorTrip; // Adjust to your model
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class VendorTripExport implements FromCollection, WithHeadings
{
    protected $tab;
    protected $vendor;
    protected $sequenceNumber;
    protected $motherVessel;

    public function __construct($tab, $vendor, $sequenceNumber, $motherVessel)
    {
        $this->tab = $tab;
        $this->vendor = $vendor;
        $this->sequenceNumber = $sequenceNumber;
        $this->motherVessel = $motherVessel;
    }

    public function collection()
    {
        $query = ProgramDetail::where('vendor_id', $this->vendor)
            ->where('sequence_number', $this->sequenceNumber);

        if ($this->tab === 'sequence') {
            $query->where('mother_vessel_id', $this->motherVessel);
        }

        return $query->get([
            'id', 'bill_no', 'date', 'vendor_name', 'headerid', 'truck_number',
            'challan_no', 'destination', 'dest_qty', 'carrying_bill', 'line_charge',
            'scale_fee', 'other_cost', 'cash_amount', 'fuel_qty', 'fuel_amount',
            'fuel_token', 'petrol_pump_name'
        ]);
    }

    public function headings(): array
    {
        return [
            'Sl', 'Bill No', 'Date', 'Vendor', 'Header ID', 'Truck Number',
            'Challan No', 'Destination', 'Qty', 'Carrying Bill', 'Line Charge',
            'Scale Fee', 'Other Cost', 'Cash Advance', 'Fuel Qty', 'Fuel Amount',
            'Fuel Token', 'Pump Name'
        ];
    }
}