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

class BaseExport implements FromCollection, WithHeadings, WithStyles, WithEvents, WithCustomStartCell
{
    protected $data;
    protected $columns;
    protected $title;
    protected $headersColor = '3498db';

    public function __construct($data, $columns, $title = 'Report')
    {
        $this->data = $data;
        $this->columns = $this->normalizeColumns($columns);
        $this->title = $title;
    }

    /**
     * Accept either associative map or indexed list of [key,label] pairs.
     * Nested keys can be provided as arrays like ['relation','field'].
     */
    protected function normalizeColumns($columns)
    {
        $normalized = [];
        foreach ($columns as $k => $v) {
            if (is_numeric($k)) {
                // expecting [key, label]
                if (is_array($v) && count($v) >= 2) {
                    $normalized[] = [$v[0], $v[1]];
                }
            } else {
                // associative style
                $normalized[] = [$k, $v];
            }
        }
        return $normalized;
    }

    public function startCell(): string
    {
        // leave three rows for company header and one blank row
        return 'A4';
    }

    public function collection()
    {
        return collect($this->data)->map(function($item, $index) {
            $row = ['SL' => $index + 1];
            foreach ($this->columns as [$key, $label]) {
                $row[$label] = $this->getValue($item, $key);
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
        foreach ($this->columns as $_) {
            $headers[] = $_[1];
        }
        return $headers;
    }

    public function styles(Worksheet $sheet)
    {
        $headerRow = 4;

        // Company Header
        $lastCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($this->columns) + 1);

        // style fonts
        $sheet->getStyle('A1')->getFont()->setSize(18)->setBold(true);
        $sheet->getStyle('A2')->getFont()->setSize(12);
        $sheet->getStyle('A3')->getFont()->setSize(11);

        // center align header text
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);


        // Header Row
        $lastCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($this->columns) + 1);
        $sheet->getStyle('A' . $headerRow . ':' . $lastCol . $headerRow)->getFont()->setBold(true)->setSize(11);
        $sheet->getStyle('A' . $headerRow . ':' . $lastCol . $headerRow)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB($this->headersColor);
        $sheet->getStyle('A' . $headerRow . ':' . $lastCol . $headerRow)->getFont()->getColor()->setRGB('FFFFFF');
        $sheet->getStyle('A' . $headerRow . ':' . $lastCol . $headerRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Data rows
        $sheet->getStyle('A5:' . $lastCol . (4 + count($this->data)))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->getColumnDimension('A')->setWidth(3); // Set column A narrow but not too small
        // auto size all columns
        foreach (range('B', $lastCol) as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        return [];
    }


    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                $totalRows = 4 + count($this->data);

                // Calculate merge range for signature row using same logic as Company Name row
                $colCount = count($this->columns) + 1;
                $lastCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colCount);

                // page setup for printing
                $ps = $sheet->getPageSetup();
                $ps->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
                $ps->setFitToWidth(1);
                $ps->setFitToHeight(0);
                // repeat company/header rows at top of each printed page
                // rows 1-4 cover company info and column headings
                $ps->setRowsToRepeatAtTopByStartAndEnd(1, 4);
            },
        ];
    }

    public function setHeadersColor($color)
    {
        $this->headersColor = $color;
        return $this;
    }


    /**
     * Apply company info header to sheet
    */
    public static function applyCompanyHeader($sheet, $lastCol, $title = null)
    {
        // row 1: company name centered across width
        $sheet->mergeCells('A1:' . $lastCol . '1');
        $sheet->setCellValue('A1', general()->title ?? 'Company Name');

        // row 2: address across whole width
        $sheet->mergeCells('A2:' . $lastCol . '2');
        $sheet->setCellValue('A2', websiteSetting('address') ?? '');

        // row 3: export title/date
        $sheet->mergeCells('A3:' . $lastCol . '3');
        $sheet->setCellValue('A3', $title ? ($title . ' - Exported on: ' . date('d M Y')) : 'Exported on: ' . date('d M Y'));

        // increase row heights
        $sheet->getRowDimension(1)->setRowHeight(60);
        $sheet->getRowDimension(2)->setRowHeight(25);
        $sheet->getRowDimension(3)->setRowHeight(20);


        // style fonts
        $sheet->getStyle('A1')->getFont()->setSize(18)->setBold(true);
        $sheet->getStyle('A2')->getFont()->setSize(12);
        $sheet->getStyle('A3')->getFont()->setSize(11);

        // center align header text
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
    }
}
