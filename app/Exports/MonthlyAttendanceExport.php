<?php
namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class MonthlyAttendanceExport implements FromCollection, WithHeadings, WithStyles, WithEvents, WithCustomStartCell
{
    protected $data;
    protected $columns;
    protected $title;
    protected $dateRange;
    protected $startDate;
    protected $endDate;

    public function __construct($reportData, $dateRange, $startDate, $endDate)
    {
        $this->dateRange = $dateRange;
        $this->startDate = $startDate;
        $this->endDate = $endDate;

        // Build columns for each day in the range
        $this->columns = [
            'Employee ID'   => 'employee_id',
            'Name'          => 'name',
            'Department'   => 'department',
        ];

        // Add columns for each day
        foreach ($dateRange as $date) {
            $this->columns[$date->format('d M')] = 'day_' . $date->format('j');
        }

        // Add summary columns
        $this->columns['Present'] = 'present';
        $this->columns['Absent']  = 'absent';
        $this->columns['Late']   = 'late';
        $this->columns['Leave']  = 'leave';
        $this->columns['Holiday'] = 'holiday';
        $this->columns['Incomplete'] = 'incomplete';

        // Format data for export
        $this->data = collect($reportData)->map(function($row) use ($dateRange) {
            $employee = $row['employee'];

            $data = [
                'employee_id'  => $employee->employee_id ?? $employee->id,
                'name'         => $employee->name,
                'department'   => $employee->department->name ?? 'N/A',
            ];

            // Add each day's status
            foreach ($dateRange as $index => $date) {
                $dayData = $row['daily_data'][$index] ?? null;
                $data['day_' . $date->format('j')] = $dayData ? $dayData['status'] : '-';
            }

            // Add summary counts
            $data['present'] = $row['present_count'];
            $data['absent']  = $row['absent_count'];
            $data['late']    = $row['late_count'];
            $data['leave']   = $row['leave_count'];
            $data['holiday'] = $row['holiday_count'];
            $data['incomplete'] = $row['incomplete_count'];

            return $data;
        })->toArray();

        $this->title = 'Monthly Attendance Report (' . $startDate->format('d M Y') . ' - ' . $endDate->format('d M Y') . ')';
    }

    public function startCell(): string
    {
        return 'A5';
    }

    public function collection()
    {
        return collect($this->data)->map(function($item, $index) {
            $row = ['SL' => $index + 1];
            foreach ($this->columns as $key => $label) {
                $row[$key] = $this->getValue($item, $label);
            }
            return $row;
        });
    }

    protected function getValue($item, $key)
    {
        if (is_array($key)) {
            $value = $item;
            foreach ($key as $k) {
                if (is_object($value)) {
                    $value = $value->{$k} ?? '';
                } elseif (is_array($value)) {
                    $value = $value[$k] ?? '';
                } else {
                    return '';
                }
            }
            return $value;
        }

        if (is_object($item)) {
            return $item->{$key} ?? '';
        }

        return $item[$key] ?? '';
    }

    public function headings(): array
    {
        $headers = ['SL'];
        foreach ($this->columns as $key => $label) {
            $headers[] = $key;
        }
        return $headers;
    }

    public function styles(Worksheet $sheet)
    {
        $headerRow = 5;
        $columnCount = count($this->columns) + 1; // +1 for SL column
        $lastCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($columnCount);

        \App\Exports\BaseExport::applyCompanyHeader($sheet, $lastCol, $this->title);

        $sheet->getStyle('A2')->getFont()->setSize(18)->setBold(true);
        $sheet->getStyle('A3')->getFont()->setSize(12);
        $sheet->getStyle('A4')->getFont()->setSize(11);

        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Header Row
        $sheet->getStyle('A' . $headerRow . ':' . $lastCol . $headerRow)->getFont()->setBold(true)->setSize(11);
        $sheet->getStyle('A' . $headerRow . ':' . $lastCol . $headerRow)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('3498db');
        $sheet->getStyle('A' . $headerRow . ':' . $lastCol . $headerRow)->getFont()->getColor()->setRGB('FFFFFF');
        $sheet->getStyle('A' . $headerRow . ':' . $lastCol . $headerRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Data rows
        $sheet->getStyle('A6:' . $lastCol . (5 + count($this->data)))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Set column widths manually instead of auto-size to avoid range() issue
        for ($i = 1; $i <= $columnCount; $i++) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i);
            // First 3 columns (SL, Employee ID, Name) wider
            if ($i <= 3) {
                $sheet->getColumnDimension($colLetter)->setWidth(18);
            } else {
                $sheet->getColumnDimension($colLetter)->setWidth(10);
            }
        }

        return [];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $totalRows = 5 + count($this->data);
                $columnCount = count($this->columns) + 1;
                $lastCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($columnCount);
                // page setup for printing
                $ps = $sheet->getPageSetup();
                $ps->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
                $ps->setFitToWidth(1);
                $ps->setFitToHeight(0);
                $ps->setRowsToRepeatAtTopByStartAndEnd(1, 5);
            },
        ];
    }
}
