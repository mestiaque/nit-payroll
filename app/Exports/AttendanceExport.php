<?php
namespace App\Exports;

use App\Models\User;
use App\Models\Attendance;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Carbon\Carbon;

class AttendanceExport implements FromCollection, WithHeadings, WithStyles, WithEvents
{
    protected $attendances;
    protected $month;
    protected $year;

    public function __construct($attendances, $month = null, $year = null)
    {
        $this->attendances = $attendances;
        $this->month = $month ?? date('m');
        $this->year = $year ?? date('Y');
    }

    public function collection()
    {
        $sl = 1;
        $data = [];
        
        foreach ($this->attendances as $attendance) {
            $user = $attendance->user;
            
            $data[] = [
                'SL' => $sl++,
                'Employee ID' => $user->employee_id ?? '',
                'Name' => $user->name ?? '',
                'Department' => $user->department->name ?? '',
                'Date' => Carbon::parse($attendance->date)->format('d M Y'),
                'In Time' => $attendance->in_time ?? '',
                'Out Time' => $attendance->out_time ?? '',
                'Late' => $attendance->late_time ?? '',
                'Over Time' => $attendance->overtime ?? '',
                'Status' => $this->getStatusLabel($attendance->status),
            ];
        }
        
        return collect($data);
    }
    
    protected function getStatusLabel($status)
    {
        $labels = [
            0 => 'Absent',
            1 => 'Present',
            2 => 'Leave',
            3 => 'Holiday',
            4 => 'Weekly Off',
        ];
        return $labels[$status] ?? 'Unknown';
    }

    public function headings(): array
    {
        return [
            'SL',
            'Employee ID',
            'Name',
            'Department',
            'Date',
            'In Time',
            'Out Time',
            'Late',
            'Over Time',
            'Status',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Company Header (Row 1-3)
        $sheet->mergeCells('A1:J1');
        $sheet->mergeCells('A2:J2');
        $sheet->mergeCells('A3:J3');
        
        $sheet->setCellValue('A1', general()->title ?? 'Company Name');
        $sheet->setCellValue('A2', general()->address ?? '');
        $sheet->setCellValue('A3', 'Attendance Report - ' . date('F Y', strtotime($this->year . '-' . $this->month)) . ' - Exported on: ' . date('d M Y'));
        
        $sheet->getStyle('A1')->getFont()->setSize(18)->setBold(true);
        $sheet->getStyle('A2')->getFont()->setSize(12);
        $sheet->getStyle('A3')->getFont()->setSize(11);
        
        $sheet->getStyle('A1:A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        // Header Row (Row 4)
        $headerRow = 4;
        $sheet->getStyle('A' . $headerRow . ':J' . $headerRow)->getFont()->setBold(true)->setSize(11);
        $sheet->getStyle('A' . $headerRow . ':J' . $headerRow)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('3498db');
        $sheet->getStyle('A' . $headerRow . ':J' . $headerRow)->getFont()->getColor()->setRGB('FFFFFF');
        $sheet->getStyle('A' . $headerRow . ':J' . $headerRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        // Data starts from row 5
        $dataStartRow = 5;
        $dataEndRow = $dataStartRow + count($this->attendances) - 1;
        
        $sheet->getStyle('A' . $dataStartRow . ':J' . $dataEndRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        // Column widths
        $sheet->getColumnDimension('A')->setWidth(8);
        $sheet->getColumnDimension('B')->setWidth(15);
        $sheet->getColumnDimension('C')->setWidth(25);
        $sheet->getColumnDimension('D')->setWidth(18);
        $sheet->getColumnDimension('E')->setWidth(15);
        $sheet->getColumnDimension('F')->setWidth(12);
        $sheet->getColumnDimension('G')->setWidth(12);
        $sheet->getColumnDimension('H')->setWidth(10);
        $sheet->getColumnDimension('I')->setWidth(12);
        $sheet->getColumnDimension('J')->setWidth(12);
        
        return [];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $dataEndRow = 4 + count($this->attendances);
                
                // Signature rows
                $signatureRow = $dataEndRow + 3;
                
                $sheet->setCellValue('A' . $signatureRow, 'Authorized Signature');
                $sheet->setCellValue('E' . $signatureRow, 'HR Manager');
                $sheet->setCellValue('I' . $signatureRow, 'Managing Director');
                
                $sheet->getStyle('A' . $signatureRow . ':J' . $signatureRow)->getFont()->setBold(true);
                $sheet->getStyle('A' . $signatureRow . ':J' . $signatureRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                
                // Border for signature line
                $sheet->getStyle('A' . $signatureRow . ':J' . $signatureRow)->getBorders()->getTop()->setBorderStyle(Border::BORDER_THIN);
            },
        ];
    }
}
