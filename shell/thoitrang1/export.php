<?php
/*
   php /home/vuong761/public_html/shell/thoitrang1/export.php 
 * php export.php 
 * 
 */
function __($txt) {
	return $txt;
}
if (!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}
include ('base.php');
include ('../excel/PhpExcelComponent.php');
$categoryId = 78;
$limit = 400;
$products = call('/products/allforsendo', ['category_id' => $categoryId, 'limit' => $limit]);
$excel = new PhpExcelComponent();
$excel->createWorksheet()
	->setDefaultFont('Calibri', 12);	
$header = array(
    array('label' => __('ID')),   
    array('label' => __('Name')),
    array('label' => __('Price'), 'width' => 400, 'wrap' => true),    
);
$sheet = $excel->addSheet('Products');
$sheet->addTableHeader($header, array('name' => 'Cambria', 'bold' => true));
$sheet->getDefaultStyle()->getAlignment()->setWrapText(true);	
//$sheet->getActiveSheet()->getColumnDimension('B')->setWidth(40);
//$sheet->getActiveSheet()->getColumnDimension('C')->setWidth(400);
foreach ($products as $product) { 
    $sheet->addTableRow(array(
        $product['product_id'],
        $product['name'],
        $product['price'],
    ));   
}
$fileName = "export_category_{$categoryId}.xls";
$excel->addTableFooter()
      ->output($fileName, 'Excel5');