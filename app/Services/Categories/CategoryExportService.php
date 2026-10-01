<?php

namespace App\Services\Categories;

use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class CategoryExportService
{
    public function __construct(private CategoryService $categories) {}

    public function excel(Request $request)
    {
        $rows = $this->categories->exportRows($request);
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Danh mục');
        $sheet->mergeCells('A1:G1');
        $sheet->setCellValue('A1', 'DANH SÁCH DANH MỤC - GREENSHOP');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $headers = ['STT','Mã danh mục','Tên danh mục','Mô tả','Số cây','Trạng thái','Ngày tạo'];
        $column = 'A';
        foreach ($headers as $header) $sheet->setCellValue($column++ . '3', $header);

        $row = 4;
        foreach ($rows as $index => $category) {
            $sheet->setCellValue('A'.$row, $index + 1);
            $sheet->setCellValue('B'.$row, 'DM'.str_pad($category->category_id, 3, '0', STR_PAD_LEFT));
            $sheet->setCellValue('C'.$row, $category->ten_danh_muc);
            $sheet->setCellValue('D'.$row, $category->mo_ta ?? '');
            $sheet->setCellValue('E'.$row, $category->so_cay ?? 0);
            $sheet->setCellValue('F'.$row, $category->trang_thai);
            $sheet->setCellValue('G'.$row, $category->created_at ? \Carbon\Carbon::parse($category->created_at)->format('d/m/Y H:i') : '');
            $row++;
        }
        $lastRow = max(3, $row - 1);
        $sheet->getStyle('A3:G3')->getFont()->setBold(true);
        $sheet->getStyle('A3:G'.$lastRow)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        foreach (range('A','G') as $column) $sheet->getColumnDimension($column)->setAutoSize(true);

        $fileName = 'danh-muc-greenshop-' . now()->format('d-m-Y-His') . '.xlsx';
        $tempPath = storage_path('app/' . $fileName);
        (new Xlsx($spreadsheet))->save($tempPath);
        return response()->download($tempPath, $fileName)->deleteFileAfterSend(true);
    }
}
