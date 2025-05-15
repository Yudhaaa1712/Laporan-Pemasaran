<?php
session_start();
require_once '../../config/database.php';
require_once '../../functions/sales_functions.php';

// Cek jika belum login, redirect ke halaman login
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: /freedom_cofe/pages/login.php');
    exit;
}

// Ambil parameter tanggal
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-d', strtotime('-30 days'));
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');
$report_type = isset($_GET['type']) ? $_GET['type'] : 'product';

// Set header untuk file Excel
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename="laporan_penjualan_freedom_cofe_' . date('Ymd') . '.xls"');
header('Pragma: no-cache');
header('Expires: 0');

// Output awal file Excel dengan styling
echo '<?xml version="1.0" encoding="UTF-8"?>
<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"
 xmlns:x="urn:schemas-microsoft-com:office:excel"
 xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"
 xmlns:html="http://www.w3.org/TR/REC-html40">
 <Styles>
  <Style ss:ID="Default" ss:Name="Normal">
   <Alignment ss:Vertical="Center"/>
   <Borders/>
   <Font ss:FontName="Calibri" x:Family="Swiss" ss:Size="11" ss:Color="#000000"/>
   <Interior/>
   <NumberFormat/>
   <Protection/>
  </Style>
  <Style ss:ID="s21">
   <Font ss:FontName="Calibri" x:Family="Swiss" ss:Size="11" ss:Color="#000000" ss:Bold="1"/>
   <Alignment ss:Horizontal="Center" ss:Vertical="Center"/>
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1"/>
   </Borders>
   <Interior ss:Color="#f2f2f2" ss:Pattern="Solid"/>
  </Style>
  <Style ss:ID="s22">
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1"/>
   </Borders>
  </Style>
  <Style ss:ID="s23">
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1"/>
   </Borders>
   <NumberFormat ss:Format="&quot;Rp&quot;\ #,##0"/>
  </Style>
  <Style ss:ID="s24">
   <Font ss:FontName="Calibri" x:Family="Swiss" ss:Size="11" ss:Color="#000000" ss:Bold="1"/>
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1"/>
   </Borders>
  </Style>
  <Style ss:ID="s25">
   <Font ss:FontName="Calibri" x:Family="Swiss" ss:Size="11" ss:Color="#000000" ss:Bold="1"/>
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1"/>
   </Borders>
   <NumberFormat ss:Format="&quot;Rp&quot;\ #,##0"/>
  </Style>
  <Style ss:ID="s26">
   <Font ss:FontName="Calibri" x:Family="Swiss" ss:Size="14" ss:Color="#000000" ss:Bold="1"/>
   <Alignment ss:Horizontal="Center" ss:Vertical="Center"/>
  </Style>
  <Style ss:ID="s27">
   <Font ss:FontName="Calibri" x:Family="Swiss" ss:Size="12" ss:Color="#000000"/>
   <Alignment ss:Horizontal="Center" ss:Vertical="Center"/>
  </Style>
 </Styles>
 <Worksheet ss:Name="Laporan Penjualan">
';

echo '<Table>
  <Column ss:Width="30"/>
  <Column ss:Width="180"/>
  <Column ss:Width="90"/>
  <Column ss:Width="110"/>
  <Row ss:Height="30">
   <Cell ss:MergeAcross="3" ss:StyleID="s26"><Data ss:Type="String">LAPORAN PENJUALAN FREEDOM COFE</Data></Cell>
  </Row>
  <Row ss:Height="25">
   <Cell ss:MergeAcross="3" ss:StyleID="s27"><Data ss:Type="String">Periode: ' . date('d-m-Y', strtotime($end_date)) . ' s/d ' . date('d-m-Y', strtotime($start_date)) . '</Data></Cell>
  </Row>
  <Row></Row>
';

// Berdasarkan jenis laporan yang diminta
if ($report_type == 'product') {
    // Laporan penjualan per produk
    $product_sales = getSalesByProduct($conn, $start_date, $end_date);
    
    echo '
  <Row>
   <Cell ss:StyleID="s21"><Data ss:Type="String">No</Data></Cell>
   <Cell ss:StyleID="s21"><Data ss:Type="String">Nama Produk</Data></Cell>
   <Cell ss:StyleID="s21"><Data ss:Type="String">Jumlah Terjual</Data></Cell>
   <Cell ss:StyleID="s21"><Data ss:Type="String">Total Penjualan</Data></Cell>
  </Row>
    ';
    
    $no = 1;
    $grand_total = 0;
    $total_items = 0;
    
    foreach ($product_sales as $sale) {
        echo '
  <Row>
   <Cell ss:StyleID="s22"><Data ss:Type="Number">' . $no++ . '</Data></Cell>
   <Cell ss:StyleID="s22"><Data ss:Type="String">' . $sale['product_name'] . '</Data></Cell>
   <Cell ss:StyleID="s22"><Data ss:Type="Number">' . $sale['total_quantity'] . '</Data></Cell>
   <Cell ss:StyleID="s23"><Data ss:Type="Number">' . $sale['total_sales'] . '</Data></Cell>
  </Row>
        ';
        $grand_total += $sale['total_sales'];
        $total_items += $sale['total_quantity'];
    }
    
    echo '
  <Row>
   <Cell ss:MergeAcross="1" ss:StyleID="s24"><Data ss:Type="String">TOTAL</Data></Cell>
   <Cell ss:StyleID="s24"><Data ss:Type="Number">' . $total_items . '</Data></Cell>
   <Cell ss:StyleID="s25"><Data ss:Type="Number">' . $grand_total . '</Data></Cell>
  </Row>
    ';
} elseif ($report_type == 'category') {
    // Laporan penjualan per kategori
    $category_sales_query = "SELECT c.name as category_name, SUM(s.quantity) as total_quantity, 
                           SUM(s.total_price) as total_sales
                           FROM sales s
                           JOIN products p ON s.product_id = p.id
                           JOIN categories c ON p.category_id = c.id
                           WHERE s.sale_date BETWEEN '$start_date' AND '$end_date'
                           GROUP BY p.category_id
                           ORDER BY total_sales DESC";
    $category_sales_result = mysqli_query($conn, $category_sales_query);
    
    echo '
  <Row>
   <Cell ss:StyleID="s21"><Data ss:Type="String">No</Data></Cell>
   <Cell ss:StyleID="s21"><Data ss:Type="String">Kategori</Data></Cell>
   <Cell ss:StyleID="s21"><Data ss:Type="String">Jumlah Terjual</Data></Cell>
   <Cell ss:StyleID="s21"><Data ss:Type="String">Total Penjualan</Data></Cell>
  </Row>
    ';
    
    $no = 1;
    $grand_total = 0;
    $total_items = 0;
    
    while ($row = mysqli_fetch_assoc($category_sales_result)) {
        echo '
  <Row>
   <Cell ss:StyleID="s22"><Data ss:Type="Number">' . $no++ . '</Data></Cell>
   <Cell ss:StyleID="s22"><Data ss:Type="String">' . $row['category_name'] . '</Data></Cell>
   <Cell ss:StyleID="s22"><Data ss:Type="Number">' . $row['total_quantity'] . '</Data></Cell>
   <Cell ss:StyleID="s23"><Data ss:Type="Number">' . $row['total_sales'] . '</Data></Cell>
  </Row>
        ';
        $grand_total += $row['total_sales'];
        $total_items += $row['total_quantity'];
    }
    
    echo '
  <Row>
   <Cell ss:MergeAcross="1" ss:StyleID="s24"><Data ss:Type="String">TOTAL</Data></Cell>
   <Cell ss:StyleID="s24"><Data ss:Type="Number">' . $total_items . '</Data></Cell>
   <Cell ss:StyleID="s25"><Data ss:Type="Number">' . $grand_total . '</Data></Cell>
  </Row>
    ';
} elseif ($report_type == 'date') {
    // Laporan penjualan per tanggal
    $daily_sales = getSalesByDate($conn, $start_date, $end_date);
    
    echo '
  <Row>
   <Cell ss:StyleID="s21"><Data ss:Type="String">No</Data></Cell>
   <Cell ss:StyleID="s21"><Data ss:Type="String">Tanggal</Data></Cell>
   <Cell ss:StyleID="s21"><Data ss:Type="String">Jumlah Terjual</Data></Cell>
   <Cell ss:StyleID="s21"><Data ss:Type="String">Total Penjualan</Data></Cell>
  </Row>
    ';
    
    $no = 1;
    $grand_total = 0;
    $total_items = 0;
    
    foreach ($daily_sales as $sale) {
        $date = date('d-m-Y', strtotime($sale['sale_date']));
        echo '
  <Row>
   <Cell ss:StyleID="s22"><Data ss:Type="Number">' . $no++ . '</Data></Cell>
   <Cell ss:StyleID="s22"><Data ss:Type="String">' . $date . '</Data></Cell>
   <Cell ss:StyleID="s22"><Data ss:Type="Number">' . $sale['total_quantity'] . '</Data></Cell>
   <Cell ss:StyleID="s23"><Data ss:Type="Number">' . $sale['total_sales'] . '</Data></Cell>
  </Row>
        ';
        $grand_total += $sale['total_sales'];
        $total_items += $sale['total_quantity'];
    }
    
    echo '
  <Row>
   <Cell ss:MergeAcross="1" ss:StyleID="s24"><Data ss:Type="String">TOTAL</Data></Cell>
   <Cell ss:StyleID="s24"><Data ss:Type="Number">' . $total_items . '</Data></Cell>
   <Cell ss:StyleID="s25"><Data ss:Type="Number">' . $grand_total . '</Data></Cell>
  </Row>
    ';
}

// Tambahkan footer
echo '
  <Row></Row>
  <Row>
   <Cell ss:MergeAcross="3"><Data ss:Type="String">Dicetak pada: ' . date('d-m-Y H:i:s') . '</Data></Cell>
  </Row>
</Table>
</Worksheet>
</Workbook>
';

exit;
?>