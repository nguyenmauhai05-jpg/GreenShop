<?php

namespace App\Services\Reports;

use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ReportExportService
{
    /**
     * @return array{path:string,fileName:string}
     */
    public function createExcel(array $data): array
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Báo cáo thống kê');

        $sheet->mergeCells('A1:G1');
        $sheet->setCellValue('A1', 'BÁO CÁO THỐNG KÊ GREENSHOP');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A1')
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->mergeCells('A2:G2');
        $sheet->setCellValue(
            'A2',
            'Từ '
            . $data['tuNgay']->format('d/m/Y')
            . ' đến '
            . $data['denNgay']->format('d/m/Y')
        );
        $sheet->getStyle('A2')
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue('A4', 'Tổng doanh thu');
        $sheet->setCellValue('B4', $data['tongDoanhThu']);
        $sheet->setCellValue('D4', 'Tổng đơn hàng');
        $sheet->setCellValue('E4', $data['tongDonHang']);
        $sheet->setCellValue('F4', 'Sản phẩm đã bán');
        $sheet->setCellValue('G4', $data['tongSanPham']);

        $headers = [
            'STT',
            'Mã cây',
            'Tên cây',
            'Danh mục',
            'Đã bán',
            'Doanh thu',
            'Tỷ lệ doanh thu',
        ];

        $column = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($column . '6', $header);
            $column++;
        }
        $sheet->getStyle('A6:G6')->getFont()->setBold(true);

        $row = 7;
        foreach ($data['sanPhams'] as $index => $sanPham) {
            $tyLe = $data['tongDoanhThu'] > 0
                ? round(((float) $sanPham->doanh_thu / $data['tongDoanhThu']) * 100, 1)
                : 0;

            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue(
                'B' . $row,
                'CC' . str_pad($sanPham->plant_id, 3, '0', STR_PAD_LEFT)
            );
            $sheet->setCellValue('C' . $row, $sanPham->ten_cay);
            $sheet->setCellValue('D' . $row, $sanPham->ten_danh_muc ?? 'Chưa phân loại');
            $sheet->setCellValue('E' . $row, $sanPham->so_luong_da_ban);
            $sheet->setCellValue('F' . $row, $sanPham->doanh_thu);
            $sheet->setCellValue('G' . $row, $tyLe . '%');
            $row++;
        }

        $lastRow = max(6, $row - 1);
        $sheet->getStyle('A6:G' . $lastRow)
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN);

        $sheet->getStyle('B4')
            ->getNumberFormat()
            ->setFormatCode('#,##0" ₫"');

        if ($lastRow >= 7) {
            $sheet->getStyle('F7:F' . $lastRow)
                ->getNumberFormat()
                ->setFormatCode('#,##0" ₫"');
        }

        foreach (range('A', 'G') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        $fileName = 'bao-cao-greenshop-' . now()->format('d-m-Y-His') . '.xlsx';
        $path = storage_path('app/' . $fileName);
        (new Xlsx($spreadsheet))->save($path);

        return [
            'path' => $path,
            'fileName' => $fileName,
        ];
    }

    public function createPdf(array $data)
    {
        $pdf = Pdf::loadView('admin.bao_cao.pdf', $data);
        $pdf->setPaper('a4', 'landscape');

        return $pdf;
    }
}
