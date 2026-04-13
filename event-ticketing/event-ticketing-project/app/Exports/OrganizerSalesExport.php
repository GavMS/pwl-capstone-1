<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class OrganizerSalesExport implements FromCollection, WithHeadings, WithMapping
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return collect($this->data);
    }

    public function headings(): array
    {
        return [
            'Event Name',
            'Event Date',
            'Tickets Sold',
            'Total Revenue (Rp)'
        ];
    }

    public function map($row): array
    {
        return [
            $row['event']->title,
            $row['event']->date->format('Y-m-d H:i'),
            $row['tickets_sold'],
            $row['total_revenue'],
        ];
    }
}
