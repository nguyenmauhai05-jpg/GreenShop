<?php

namespace App\Services\Plants;

use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class PlantExcelExportService
{
    /**
     * @return array{path:string,fileName:string}
     */
    public function create(Collection $plants): array
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Danh sách cây');

        $sheet->mergeCells('A1:H1');
        $sheet->setCellValue('A1', 'DANH SÁCH CÂY - GREENSHOP');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 16,
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
        ]);

        $headers = [
            'STT',
            'Mã cây',
            'Tên cây',
            'Danh mục',
            'Giá bán',
            'Số lượng',
            'Tình trạng kho',
            'Trạng thái',
        ];

        $column = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($column . '3', $header);
            $column++;
        }

        $row = 4;
        foreach ($plants as $index => $plant) {
            $stockStatus = $plant->so_luong <= 0
                ? 'Hết hàng'
                : ($plant->so_luong <= 10 ? 'Sắp hết hàng' : 'Còn hàng');

            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue(
                'B' . $row,
                'CC' . str_pad($plant->plant_id, 3, '0', STR_PAD_LEFT)
            );
            $sheet->setCellValue('C' . $row, $plant->ten_cay);
            $sheet->setCellValue('D' . $row, $plant->ten_danh_muc ?? 'Chưa phân loại');
            $sheet->setCellValue('E' . $row, $plant->gia);
            $sheet->setCellValue('F' . $row, $plant->so_luong);
            $sheet->setCellValue('G' . $row, $stockStatus);
            $sheet->setCellValue('H' . $row, $plant->trang_thai);
            $row++;
        }

        $sheet->getStyle('A3:H3')->getFont()->setBold(true);
        $sheet->getStyle('A3:H3')
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $lastRow = max(3, $row - 1);
        $sheet->getStyle('A3:H' . $lastRow)
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN);

        if ($lastRow >= 4) {
            $sheet->getStyle('E4:E' . $lastRow)
                ->getNumberFormat()
                ->setFormatCode('#,##0" ₫"');
        }

        foreach (range('A', 'H') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        $fileName = 'danh-sach-cay-' . now()->format('d-m-Y-His') . '.xlsx';
        $path = storage_path('app/' . $fileName);

        (new Xlsx($spreadsheet))->save($path);

        return [
            'path' => $path,
            'fileName' => $fileName,
        ];
    }
}
