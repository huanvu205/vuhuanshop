<?php
class ReportController extends Controller {
    private $orderModel;
    private $productModel;
    private $userModel;
    private $brandModel;

    public function __construct() {
        parent::__construct();
        $this->orderModel = new OrderModel();
        $this->productModel = new ProductModel();
        $this->userModel = new UserModel();
        $this->brandModel = new BrandModel();
    }

    public function index() {
        $this->requireAdmin();
        $period = sanitize($_GET['period'] ?? 'month');
        $year = (int)($_GET['year'] ?? date('Y'));

        $this->view('admin.reports.index', [
            'title' => 'Báo cáo thống kê - Admin',
            'period' => $period,
            'year' => $year,
            'revenueByDay' => $this->orderModel->getRevenueByDay(30),
            'revenueByMonth' => $this->orderModel->getRevenueByMonth($year),
            'revenueByYear' => $this->orderModel->getRevenueByYear(5),
            'topProducts' => $this->productModel->getTopSelling(10),
            'topCustomers' => $this->userModel->getTopCustomers(10),
            'orderStats' => $this->orderModel->getStats(),
            'userStats' => $this->userModel->getStats(),
            'productStats' => $this->productModel->getStats(),
        ]);
    }

    /** Export a real XLSX workbook so Excel does not guess text encoding or delimiters. */
    public function exportOrders() {
        $this->requireAdmin();
        if (!class_exists('ZipArchive')) {
            http_response_code(503);
            echo 'Không thể xuất Excel: PHP ZipArchive chưa được bật trên hosting.';
            return;
        }

        $orders = $this->orderModel->getAll(1, 10000);
        $headers = ['Mã đơn hàng', 'Khách hàng', 'Điện thoại', 'Địa chỉ', 'Tổng tiền', 'Trạng thái', 'Thanh toán', 'Ngày đặt'];
        $xmlEscape = static function ($value) {
            return htmlspecialchars((string)($value ?? ''), ENT_XML1 | ENT_QUOTES, 'UTF-8');
        };
        $columns = ['A','B','C','D','E','F','G','H'];
        $sheetRows = [];
        $allRows = [$headers];
        foreach ($orders as $order) {
            $allRows[] = [
                $order['order_number'] ?? '', $order['customer_name'] ?? '', $order['ship_phone'] ?? '',
                $order['ship_address'] ?? '', $order['final_amount'] ?? 0, $order['status'] ?? '',
                $order['payment_status'] ?? '', $order['created_at'] ?? '',
            ];
        }
        foreach ($allRows as $rowIndex => $row) {
            $rowNumber = $rowIndex + 1;
            $cells = '';
            foreach ($row as $columnIndex => $value) {
                $ref = $columns[$columnIndex] . $rowNumber;
                if ($rowIndex > 0 && $columnIndex === 4 && is_numeric($value)) {
                    $cells .= '<c r="' . $ref . '" s="2"><v>' . (float)$value . '</v></c>';
                } else {
                    $cells .= '<c r="' . $ref . '" t="inlineStr"><is><t xml:space="preserve">' . $xmlEscape($value) . '</t></is></c>';
                }
            }
            $height = $rowIndex === 0 ? '26' : '21';
            $sheetRows[] = '<row r="' . $rowNumber . '" ht="' . $height . '" customHeight="1">' . $cells . '</row>';
        }

        $lastRow = max(1, count($allRows));
        $worksheet = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
            . '<dimension ref="A1:H' . $lastRow . '"/><sheetViews><sheetView workbookViewId="0"><pane ySplit="1" topLeftCell="A2" activePane="bottomLeft" state="frozen"/></sheetView></sheetViews>'
            . '<sheetFormatPr defaultRowHeight="21"/><cols>'
            . '<col min="1" max="1" width="24" customWidth="1"/><col min="2" max="2" width="25" customWidth="1"/>'
            . '<col min="3" max="3" width="19" customWidth="1"/><col min="4" max="4" width="48" customWidth="1"/>'
            . '<col min="5" max="5" width="20" customWidth="1"/><col min="6" max="7" width="18" customWidth="1"/>'
            . '<col min="8" max="8" width="22" customWidth="1"/></cols><sheetData>' . implode('', $sheetRows) . '</sheetData>'
            . '<autoFilter ref="A1:H' . $lastRow . '"/></worksheet>';

        $styles = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            . '<numFmts count="1"><numFmt numFmtId="164" formatCode="#,##0 &quot;₫&quot;"/></numFmts>'
            . '<fonts count="2"><font><sz val="11"/><name val="Calibri"/></font><font><b/><color rgb="FFFFFFFF"/><sz val="11"/><name val="Calibri"/></font></fonts>'
            . '<fills count="3"><fill><patternFill patternType="none"/></fill><fill><patternFill patternType="gray125"/></fill><fill><patternFill patternType="solid"><fgColor rgb="FF1F3B64"/><bgColor indexed="64"/></patternFill></fill></fills>'
            . '<borders count="1"><border><left/><right/><top/><bottom/><diagonal/></border></borders>'
            . '<cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs>'
            . '<cellXfs count="3"><xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/>'
            . '<xf numFmtId="0" fontId="1" fillId="2" borderId="0" xfId="0" applyFont="1" applyFill="1"/>'
            . '<xf numFmtId="164" fontId="0" fillId="0" borderId="0" xfId="0" applyNumberFormat="1"/></cellXfs>'
            . '<cellStyles count="1"><cellStyle name="Normal" xfId="0" builtinId="0"/></cellStyles></styleSheet>';

        $files = [
            '[Content_Types].xml' => '<?xml version="1.0" encoding="UTF-8"?>'
                . '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
                . '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
                . '<Default Extension="xml" ContentType="application/xml"/>'
                . '<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
                . '<Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>'
                . '<Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/></Types>',
            '_rels/.rels' => '<?xml version="1.0" encoding="UTF-8"?>'
                . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
                . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/></Relationships>',
            'xl/workbook.xml' => '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
                . '<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
                . '<sheets><sheet name="Đơn hàng" sheetId="1" r:id="rId1"/></sheets></workbook>',
            'xl/_rels/workbook.xml.rels' => '<?xml version="1.0" encoding="UTF-8"?>'
                . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
                . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>'
                . '<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/></Relationships>',
            'xl/worksheets/sheet1.xml' => $worksheet,
            'xl/styles.xml' => $styles,
        ];

        $tempFile = tempnam(sys_get_temp_dir(), 'vuhuan-orders-');
        $zip = new ZipArchive();
        if ($tempFile === false || $zip->open($tempFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            if ($tempFile) @unlink($tempFile);
            http_response_code(500);
            echo 'Không thể tạo tệp Excel lúc này.';
            return;
        }
        foreach ($files as $path => $content) $zip->addFromString($path, $content);
        $zip->close();

        if (ob_get_length()) ob_clean();
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="orders_' . date('Y-m-d_His') . '.xlsx"');
        header('Content-Length: ' . filesize($tempFile));
        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        header('X-Content-Type-Options: nosniff');
        readfile($tempFile);
        unlink($tempFile);
        exit;
    }
}
?>
