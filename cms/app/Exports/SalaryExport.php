<?php

namespace App\Exports;

use App\Models\SalaryRecord;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SalaryExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $month;
    protected $year;

    public function __construct($month, $year)
    {
        $this->month = $month;
        $this->year = $year;
    }

    public function collection()
    {
        return SalaryRecord::with('employee')
            ->where('month', $this->month)
            ->where('year', $this->year)
            ->get();
    }

    public function headings(): array
    {
        return [
            'Employee Name',
            'Email',
            'Designation',
            'Department',
            'Shift',
            'Working Days',
            'In-Hand Salary',
            'Employee PF',
            'Employee ESI',
            'Deduction',
            'Net Salary',
            'Month',
            'Year'
        ];
    }

    public function map($record): array
    {
        return [
            $record->employee->first_name . ' ' . $record->employee->last_name,
            $record->employee->email ?? 'N/A',
            $record->employee->job_title ?? 'N/A',
            $record->employee->department ?? 'N/A',
            $record->shift ?? 'Day',
            number_format($record->working_days, 1),
            number_format($record->basic_salary, 2),
            number_format($record->employee_pf ?? 0, 2),
            number_format($record->employee_esi ?? 0, 2),
            number_format($record->deduction ?? 0, 2),
            number_format($record->net_salary, 2),
            date('F', mktime(0, 0, 0, $this->month, 1)),
            $this->year
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }
}
