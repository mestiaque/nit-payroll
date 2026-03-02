<?php
namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class UsersExport implements FromCollection, WithHeadings, WithStyles, WithEvents
{
    protected $users;

    public function __construct($users)
    {
        $this->users = $users;
    }

    public function collection()
    {
        $sl = 1;
        $data = [];
        
        foreach ($this->users as $user) {
            $data[] = [
                'SL' => $sl++,
                'Employee ID' => $user->employee_id ?? '',
                'Name' => $user->name ?? '',
                'Email' => $user->email ?? '',
                'Mobile' => $user->mobile ?? '',
                'Designation' => $user->designation->name ?? '',
                'Department' => $user->department->name ?? '',
                'Section' => $user->section->name ?? '',
                'Line' => $user->line->name ?? '',
                'Join Date' => $user->joining_date ? \Carbon\Carbon::parse($user->joining_date)->format('d M Y') : '',
                'Gross Salary' => $user->gross_salary ?? '',
                'Status' => $user->status == 1 ? 'Active' : 'Inactive',
            ];
        }
        
        return collect($data);
    }

    public function headings(): array
    {
        return [
            'SL',
            'Employee ID',
            'Name',
            'Email',
            'Mobile',
            'Designation',
            'Department',
            'Section',
            'Line',
            'Join Date',
            'Gross Salary',
            'Status',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $row = 1;
        
        // Company Header (Row 1-3)
        $sheet->mergeCells('A1:L1');
        $sheet->mergeCells('A2:L2');
        $sheet->mergeCells('A3:L3');
        
        $sheet->setCellValue('A1', general()->title ?? 'Company Name');
        $sheet->setCellValue('A2', general()->address ?? '');
        $sheet->setCellValue('A3', 'Employee List - Exported on: ' . date('d M Y'));
        
        $sheet->getStyle('A1')->getFont()->setSize(18)->setBold(true);
        $sheet->getStyle('A2')->getFont()->setSize(12);
        $sheet->getStyle('A3')->getFont()->setSize(11);
        
        $sheet->getStyle('A1:A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        // Header Row (Row 4)
        $headerRow = 4;
        $sheet->getStyle('A' . $headerRow . ':L' . $headerRow)->getFont()->setBold(true)->setSize(11);
        $sheet->getStyle('A' . $headerRow . ':L' . $headerRow)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('3498db');
        $sheet->getStyle('A' . $headerRow . ':L' . $headerRow)->getFont()->getColor()->setRGB('FFFFFF');
        $sheet->getStyle('A' . $headerRow . ':L' . $headerRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        // Data starts from row 5
        $dataStartRow = 5;
        $dataEndRow = $dataStartRow + count($this->users) - 1;
        
        $sheet->getStyle('A' . $dataStartRow . ':L' . $dataEndRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        // Column widths
        $sheet->getColumnDimension('A')->setWidth(8);
        $sheet->getColumnDimension('B')->setWidth(15);
        $sheet->getColumnDimension('C')->setWidth(25);
        $sheet->getColumnDimension('D')->setWidth(30);
        $sheet->getColumnDimension('E')->setWidth(15);
        $sheet->getColumnDimension('F')->setWidth(20);
        $sheet->getColumnDimension('G')->setWidth(18);
        $sheet->getColumnDimension('H')->setWidth(18);
        $sheet->getColumnDimension('I')->setWidth(12);
        $sheet->getColumnDimension('J')->setWidth(15);
        $sheet->getColumnDimension('K')->setWidth(15);
        $sheet->getColumnDimension('L')->setWidth(12);
        
        return [];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $headerRow = 4;
                $dataStartRow = 5;
                $dataEndRow = $dataStartRow + count($this->users) - 1;
                $totalRows = $dataEndRow;
                
                // Signature rows
                $signatureRow = $totalRows + 3;
                
                $sheet->setCellValue('A' . $signatureRow, 'Authorized Signature');
                $sheet->setCellValue('F' . $signatureRow, 'HR Manager');
                $sheet->setCellValue('K' . $signatureRow, 'Managing Director');
                
                $sheet->getStyle('A' . $signatureRow . ':L' . $signatureRow)->getFont()->setBold(true);
                $sheet->getStyle('A' . $signatureRow . ':L' . $signatureRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                
                // Border for signature line
                $sheet->getStyle('A' . $signatureRow . ':L' . $signatureRow)->getBorders()->getTop()->setBorderStyle(Border::BORDER_THIN);
            },
        ];
    }
}
