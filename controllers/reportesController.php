<?php

class ReportesController extends BaseController
{
    private $ventaModel;
    private $productoModel;

    public function __construct()
    {
        $this->requireAuth();
        $this->ventaModel = new Venta();
        $this->productoModel = new Producto();
    }

    public function listar()
    {
        $fecha_desde = $_GET['fecha_desde'] ?? date('Y-m-01');
        $fecha_hasta = $_GET['fecha_hasta'] ?? date('Y-m-d');

        $reporte = $this->ventaModel->obtenerReporteVentas($fecha_desde, $fecha_hasta);
        $topProductos = $this->ventaModel->obtenerTopProductos($fecha_desde, $fecha_hasta);
        $metodosPago = $this->ventaModel->obtenerVentasPorMetodoPago($fecha_desde, $fecha_hasta);
        $productosBajos = $this->productoModel->obtenerProductosStockBajo();

        include RUTA_APP . '/views/reportes/reportes.php';
        exit();
    }

    /* ═══════════════════════════════════════════════════════
       EXCEL (CSV mejorado con formato profesional)
       ═══════════════════════════════════════════════════════ */

    public function exportarexcel()
    {
        $fecha_desde = $_GET['fecha_desde'] ?? date('Y-m-01');
        $fecha_hasta = $_GET['fecha_hasta'] ?? date('Y-m-d');

        $reporte        = $this->ventaModel->obtenerReporteVentas($fecha_desde, $fecha_hasta);
        $topProductos   = $this->ventaModel->obtenerTopProductos($fecha_desde, $fecha_hasta);
        $metodosPago    = $this->ventaModel->obtenerVentasPorMetodoPago($fecha_desde, $fecha_hasta);
        $productosBajos = $this->productoModel->obtenerProductosStockBajo();

        $filename = "reporte_ventas_{$fecha_desde}_{$fecha_hasta}.csv";

        header('Content-Type: text/csv; charset=UTF-8');
        header("Content-Disposition: attachment; filename=\"{$filename}\"");
        header('Cache-Control: max-age=0');

        $output = fopen('php://output', 'w');

        // BOM UTF-8 para que Excel abra bien los acentos
        fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

        $sep = ';';

        // ── Cabecera del reporte ──
        fputcsv($output, ['REPORTE DE VENTAS — MI BODEGA'], $sep);
        fputcsv($output, ['Periodo:', $fecha_desde . ' al ' . $fecha_hasta, '', 'Generado:', date('d/m/Y H:i')], $sep);
        fputcsv($output, [], $sep);

        // ── Resumen general ──
        fputcsv($output, ['RESUMEN GENERAL'], $sep);
        fputcsv($output, ['Concepto', 'Monto (Bs)'], $sep);
        fputcsv($output, ['Total de ventas', (int) ($reporte['total_ventas'] ?? 0)], $sep);
        fputcsv($output, ['Completadas', number_format((float) ($reporte['total_completadas'] ?? 0), 2, ',', '.')], $sep);
        fputcsv($output, ['Pendientes', number_format((float) ($reporte['total_pendientes'] ?? 0), 2, ',', '.')], $sep);
        fputcsv($output, ['Canceladas', number_format((float) ($reporte['total_canceladas'] ?? 0), 2, ',', '.')], $sep);
        fputcsv($output, [], $sep);

        // ── Top Productos ──
        fputcsv($output, ['PRODUCTOS MÁS VENDIDOS'], $sep);
        fputcsv($output, ['#', 'Producto', 'Código', 'Cantidad vendida', 'Total vendido (Bs)'], $sep);
        $i = 1;
        $totalProductos = 0;
        foreach ($topProductos as $p) {
            $total = (float) $p['total_vendido'];
            $totalProductos += $total;
            fputcsv($output, [
                $i++,
                $p['producto_nombre'],
                $p['producto_codigo'] ?? '',
                (int) $p['cantidad_vendida'],
                number_format($total, 2, ',', '.'),
            ], $sep);
        }
        fputcsv($output, [], $sep);

        // ── Métodos de pago ──
        fputcsv($output, ['VENTAS POR MÉTODO DE PAGO'], $sep);
        fputcsv($output, ['Método de pago', 'Cantidad de ventas', 'Total (Bs)'], $sep);
        $totalMetodo = 0;
        foreach ($metodosPago as $m) {
            $total = (float) $m['total_ventas'];
            $totalMetodo += $total;
            fputcsv($output, [
                ucfirst(str_replace('_', ' ', $m['metodo_pago'])),
                (int) $m['cantidad_ventas'],
                number_format($total, 2, ',', '.'),
            ], $sep);
        }
        fputcsv($output, [], $sep);

        // ── Stock bajo ──
        fputcsv($output, ['ALERTAS DE STOCK BAJO'], $sep);
        fputcsv($output, ['Código', 'Producto', 'Categoría', 'Stock', 'Precio venta (Bs)'], $sep);
        foreach ($productosBajos as $p) {
            fputcsv($output, [
                $p['producto_codigo'] ?? '',
                $p['producto_nombre'],
                $p['categorias_nombre'] ?? '',
                (int) $p['producto_stock'],
                number_format((float) $p['producto_precio_venta'], 2, ',', '.'),
            ], $sep);
        }

        fclose($output);
        exit();
    }

    /* ═══════════════════════════════════════════════════════
       PDF (FPDF — diseño profesional mejorado)
       ═══════════════════════════════════════════════════════ */

    public function exportarpdf()
    {
        $fecha_desde = $_GET['fecha_desde'] ?? date('Y-m-01');
        $fecha_hasta = $_GET['fecha_hasta'] ?? date('Y-m-d');

        $reporte        = $this->ventaModel->obtenerReporteVentas($fecha_desde, $fecha_hasta);
        $topProductos   = $this->ventaModel->obtenerTopProductos($fecha_desde, $fecha_hasta);
        $metodosPago    = $this->ventaModel->obtenerVentasPorMetodoPago($fecha_desde, $fecha_hasta);
        $productosBajos = $this->productoModel->obtenerProductosStockBajo();

        $pdf = new \FPDF('L', 'mm', 'A4');
        $pdf->SetAutoPageBreak(true, 20);
        $pdf->SetMargins(15, 15, 15);

        /* ── Página 1: Resumen + Top Productos ── */
        $pdf->AddPage();
        $this->pdfHeader($pdf, $fecha_desde, $fecha_hasta);

        $totalVentas = (int) ($reporte['total_ventas'] ?? 0);
        $completadas = (float) ($reporte['total_completadas'] ?? 0);
        $pendientes  = (float) ($reporte['total_pendientes'] ?? 0);
        $canceladas  = (float) ($reporte['total_canceladas'] ?? 0);

        // ── Tarjetas de resumen ──
        $this->pdfSectionTitle($pdf, 'Resumen del período');
        $cards = [
            ['TOTAL VENTAS', (string) $totalVentas, [58, 99, 65]],
            ['COMPLETADAS', 'Bs ' . number_format($completadas, 2, ',', '.'), [40, 120, 60]],
            ['PENDIENTES', 'Bs ' . number_format($pendientes, 2, ',', '.'), [200, 160, 40]],
            ['CANCELADAS', 'Bs ' . number_format($canceladas, 2, ',', '.'), [180, 60, 60]],
        ];

        $x = 15;
        $cardW = 65;
        $cardH = 22;
        $gap = 3.5;

        foreach ($cards as $card) {
            $color = $card[2];
            // Borde superior coloreado
            $pdf->SetFillColor($color[0], $color[1], $color[2]);
            $pdf->Rect($x, $pdf->GetY(), $cardW, 2.5, 'F');
            // Fondo de tarjeta
            $pdf->SetFillColor(250, 251, 250);
            $pdf->SetDrawColor(230, 230, 230);
            $pdf->Rect($x, $pdf->GetY() + 2.5, $cardW, $cardH - 2.5, 'DF');
            // Label
            $pdf->SetXY($x + 4, $pdf->GetY() + 5);
            $pdf->SetFont('Arial', '', 6.5);
            $pdf->SetTextColor(130, 130, 130);
            $pdf->Cell($cardW - 8, 4, $card[0]);
            // Valor
            $pdf->SetXY($x + 4, $pdf->GetY() + 5);
            $pdf->SetFont('Arial', 'B', 14);
            $pdf->SetTextColor($color[0], $color[1], $color[2]);
            $pdf->Cell($cardW - 8, 8, $card[1]);
            $x += $cardW + $gap;
        }

        $pdf->Ln($cardH + 6);

        // ── Tabla: Top Productos ──
        $this->pdfSectionTitle($pdf, 'Productos más vendidos');
        $colW = [12, 105, 30, 30, 40];
        $headers = ['#', 'Producto', 'Código', 'Cantidad', 'Total (Bs)'];
        $this->pdfTableHeader($pdf, $headers, $colW);

        $pdf->SetFont('Helvetica', '', 7.5);
        $pdf->SetTextColor(40, 40, 40);
        $row = 0;
        $totalCant = 0;
        $totalVend = 0;

        if (!empty($topProductos)) {
            foreach ($topProductos as $i => $p) {
                $this->pdfTableRow($pdf, $row, $colW, [
                    (string) ($i + 1),
                    $this->pdfText($p['producto_nombre'], 52),
                    $this->pdfText($p['producto_codigo'] ?? '', 14),
                    number_format((int) $p['cantidad_vendida'], 0, ',', '.'),
                    number_format((float) $p['total_vendido'], 2, ',', '.'),
                ], ['C', 'L', 'C', 'C', 'R']);
                $totalCant += (int) $p['cantidad_vendida'];
                $totalVend += (float) $p['total_vendido'];
                $row++;
            }
            // Fila total
            $this->pdfTableTotalRow($pdf, $colW, ['TOTAL', '', '', number_format($totalCant, 0, ',', '.'), number_format($totalVend, 2, ',', '.')], ['L', '', '', 'C', 'R']);
        } else {
            $pdf->SetTextColor(140, 140, 140);
            $pdf->SetFont('Helvetica', 'I', 8);
            $pdf->Cell(array_sum($colW), 10, 'Sin datos en el período seleccionado.', 0, 1, 'C');
        }

        /* ── Página 2: Métodos de pago + Stock bajo ── */
        $pdf->AddPage();
        $this->pdfHeader($pdf, $fecha_desde, $fecha_hasta);

        // Métodos de pago
        $this->pdfSectionTitle($pdf, 'Ventas por método de pago');
        $mpColW = [80, 40, 50];
        $this->pdfTableHeader($pdf, ['Método de pago', 'Cantidad', 'Total (Bs)'], $mpColW);

        $pdf->SetFont('Helvetica', '', 7.5);
        $pdf->SetTextColor(40, 40, 40);
        $row = 0;
        $totalMpCant = 0;
        $totalMp = 0;

        if (!empty($metodosPago)) {
            foreach ($metodosPago as $m) {
                $this->pdfTableRow($pdf, $row, $mpColW, [
                    ucfirst(str_replace('_', ' ', $m['metodo_pago'])),
                    number_format((int) $m['cantidad_ventas'], 0, ',', '.'),
                    number_format((float) $m['total_ventas'], 2, ',', '.'),
                ], ['L', 'C', 'R']);
                $totalMpCant += (int) $m['cantidad_ventas'];
                $totalMp += (float) $m['total_ventas'];
                $row++;
            }
            $this->pdfTableTotalRow($pdf, $mpColW, ['TOTAL', number_format($totalMpCant, 0, ',', '.'), number_format($totalMp, 2, ',', '.')], ['L', 'C', 'R']);
        } else {
            $pdf->SetTextColor(140, 140, 140);
            $pdf->SetFont('Helvetica', 'I', 8);
            $pdf->Cell(array_sum($mpColW), 10, 'Sin datos en el período seleccionado.', 0, 1, 'C');
        }

        $pdf->Ln(12);

        // Stock bajo
        $this->pdfSectionTitle($pdf, 'Alertas de stock bajo');
        $stColW = [30, 80, 50, 20, 35];
        $this->pdfTableHeader($pdf, ['Código', 'Producto', 'Categoría', 'Stock', 'Precio (Bs)'], $stColW);

        $pdf->SetFont('Helvetica', '', 7.5);
        $pdf->SetTextColor(40, 40, 40);
        $row = 0;

        if (!empty($productosBajos)) {
            foreach ($productosBajos as $p) {
                $cells = [
                    $this->pdfText($p['producto_codigo'] ?? '', 14),
                    $this->pdfText($p['producto_nombre'], 40),
                    $this->pdfText($p['categorias_nombre'] ?? '', 25),
                    (string) (int) $p['producto_stock'],
                    number_format((float) $p['producto_precio_venta'], 2, ',', '.'),
                ];
                $this->pdfTableRow($pdf, $row, $stColW, $cells, ['C', 'L', 'L', 'C', 'R']);
                $row++;
            }
        } else {
            $pdf->SetTextColor(140, 140, 140);
            $pdf->SetFont('Helvetica', 'I', 8);
            $pdf->Cell(array_sum($stColW), 10, 'Todos los productos tienen stock suficiente.', 0, 1, 'C');
        }

        $filename = "reporte_ventas_{$fecha_desde}_{$fecha_hasta}.pdf";
        $pdf->Output('D', $filename);
        exit();
    }

    /* ═══════════════════════════════════════════════════════
       Helpers PDF
       ═══════════════════════════════════════════════════════ */

    private function pdfHeader(\FPDF $pdf, string $fecha_desde, string $fecha_hasta): void
    {
        // Barra superior verde
        $pdf->SetFillColor(58, 99, 65);
        $pdf->Rect(0, 0, 297, 20, 'F');

        // Logo "MB"
        $pdf->SetXY(15, 4);
        $pdf->SetFont('Helvetica', 'B', 16);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(15, 12, 'MB', 0, 0, 'L');

        // Título
        $pdf->SetXY(33, 3);
        $pdf->SetFont('Helvetica', 'B', 13);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(0, 7, 'Reporte de Ventas');

        // Subtítulo
        $pdf->SetXY(33, 10);
        $pdf->SetFont('Helvetica', '', 8);
        $pdf->SetTextColor(200, 230, 200);
        $pdf->Cell(0, 5, "Periodo: {$fecha_desde} al {$fecha_hasta}  |  Generado: " . date('d/m/Y H:i'));

        $pdf->Ln(26);
    }

    private function pdfSectionTitle(\FPDF $pdf, string $title): void
    {
        // Cuadrado verde + título
        $y = $pdf->GetY();
        $pdf->SetFillColor(58, 99, 65);
        $pdf->Rect(15, $y, 3, 7, 'F');
        $pdf->SetXY(21, $y);
        $pdf->SetFont('Helvetica', 'B', 10);
        $pdf->SetTextColor(58, 99, 65);
        $pdf->Cell(0, 7, $title);
        $pdf->Ln(10);
    }

    private function pdfTableHeader(\FPDF $pdf, array $headers, array $colW): void
    {
        $pdf->SetFont('Helvetica', 'B', 7.5);
        $pdf->SetFillColor(58, 99, 65);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetDrawColor(58, 99, 65);
        foreach ($headers as $i => $h) {
            $align = ($i === 0) ? 'C' : (($i === count($headers) - 1) ? 'R' : 'L');
            $pdf->Cell($colW[$i], 7, '  ' . $h, 0, 0, 'L', true);
        }
        $pdf->Ln();
        $pdf->SetDrawColor(220, 220, 220);
    }

    private function pdfTableRow(\FPDF $pdf, int $rowIdx, array $colW, array $values, array $aligns): void
    {
        $fill = ($rowIdx % 2 === 0);
        if ($fill) {
            $pdf->SetFillColor(246, 248, 246);
        } else {
            $pdf->SetFillColor(255, 255, 255);
        }
        foreach ($values as $i => $val) {
            $pdf->Cell($colW[$i], 6, '  ' . $val, 0, 0, $aligns[$i] ?? 'L', $fill);
        }
        $pdf->Ln();
    }

    private function pdfTableTotalRow(\FPDF $pdf, array $colW, array $values, array $aligns): void
    {
        $pdf->SetFillColor(230, 235, 230);
        $pdf->SetDrawColor(58, 99, 65);
        $pdf->SetFont('Helvetica', 'B', 7.5);
        $pdf->SetTextColor(58, 99, 65);
        foreach ($values as $i => $val) {
            $pdf->Cell($colW[$i], 7, '  ' . $val, 0, 0, $aligns[$i] ?? 'L', true);
        }
        $pdf->Ln();
        $pdf->SetDrawColor(220, 220, 220);
        $pdf->SetFont('Helvetica', '', 7.5);
        $pdf->SetTextColor(40, 40, 40);
    }

    private function pdfText(string $text, int $maxLen = 40): string
    {
        $text = mb_convert_encoding($text, 'UTF-8', 'UTF-8');
        if (mb_strlen($text, 'UTF-8') > $maxLen) {
            return mb_substr($text, 0, $maxLen - 1, 'UTF-8') . '…';
        }
        return $text;
    }

    /**
     * Pie de página: número de página y marca.
     */
    public function Footer(): void
    {
        $this->SetY(-15);
        $this->SetFillColor(245, 247, 245);
        $this->Rect(0, $this->GetY(), 297, 15, 'F');
        $this->SetFont('Helvetica', '', 7);
        $this->SetTextColor(150, 150, 150);
        $this->SetX(15);
        $this->Cell(0, 10, 'Mi Bodega — Control de inventario', 0, 0, 'L');
        $this->SetX(-15);
        $this->Cell(0, 10, 'Página ' . $this->PageNo() . '/{nb}', 0, 0, 'R');
    }
}
