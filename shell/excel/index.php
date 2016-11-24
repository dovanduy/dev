<?php
function __($txt) {
	return $txt;
}
if (!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}
include_once 'base.php';
include ('PhpExcelComponent.php');
$excel = new PhpExcelComponent();
$excel->createWorksheet()
	->setDefaultFont('Calibri', 12);
	
$header = array(
    array('label' => __('No.')),   
    array('label' => __('File')),
    array('label' => __('SQL Script'), 'width' => 400, 'wrap' => true),
    array('label' => __('Table Added')),
    array('label' => __('Status')),
);
// tabiweb-front
$webDir = implode(DS, array(dirname(getcwd()), 'ea', 'kaigai', 'tabiweb-front'));
$sqlDir = implode(DS, array($webDir, 'sql'));
$modelPath = array(
	'htl' => implode(DS, array($webDir, 'htl', 'app', 'models')) . DS . '*',
	'uploadimg' => implode(DS, array($webDir, 'uploadimg', 'app', 'models')) . DS . '*',
	'tour' => implode(DS, array($webDir, 'tour', 'app', 'models')) . DS . '*',
	'my' . DS . 'auth' => implode(DS, array($webDir, 'my', 'auth', 'app', 'models')) . DS . '*',
	'my' . DS . 'members' => implode(DS, array($webDir, 'my', 'members', 'app', 'models')) . DS . '*',
	'my' . DS . 'hotel' => implode(DS, array($webDir, 'my', 'hotel', 'app', 'models')) . DS . '*',
	'my' . DS . 'checkout' => implode(DS, array($webDir, 'my', 'checkout', 'app', 'models')) . DS . '*',
	'my' . DS . 'mytravel' => implode(DS, array($webDir, 'my', 'mytravel', 'app', 'models')) . DS . '*',
	'my' . DS . 'tour' => implode(DS, array($webDir, 'my', 'tour', 'app', 'models')) . DS . '*',
	'trip_web' . DS . 'htl' => implode(DS, array($webDir, 'trip_web', 'htl', 'app', 'models')) . DS . '*',
);

// jal_admin
$webDir = implode(DS, array(dirname(getcwd()), 'ea', 'kaigai', 'cs', 'jal_admin'));
$sqlDir = implode(DS, array($webDir, 'sql'));
$modelPath = array(
	'jal' => implode(DS, array($webDir, 'app', 'models')) . DS . '*',
);

foreach ($modelPath as $module => $path) {
	$sheet = $excel->addSheet(str_replace(DS, '_', $module));
	$sheet->addTableHeader($header, array('name' => 'Cambria', 'bold' => true));
	$sheet->getDefaultStyle()->getAlignment()->setWrapText(true);	
	$sheet->getActiveSheet()->getColumnDimension('B')->setWidth(40);
	$sheet->getActiveSheet()->getColumnDimension('C')->setWidth(400);
	$files = glob($path);
	$i = 1;
	foreach ($files as $file) {
		if (is_file($file)) {
			$content = file_get_contents($file);
			if (preg_match('/class\s+(\w+)(.*)?\{/', $content, $matches)) {
				$class = $matches[1];
				$sqlFile = implode(DS, array($sqlDir, $module, $class . '.sql'));				
				if (file_exists($sqlFile)) {
					$sql = file_get_contents(implode(DS, array($sqlDir, $module, $class . '.sql')));
					$sheet->addTableRow(array(
						$i,
						str_replace(DS, '/', str_replace($webDir, '', $file)),
						$sql,
						'',       
						'',
					));
					$i++;
				}
			}
		}
	}
}
$fileName = 'jal_admin_sql.xls';
$excel->addTableFooter()
      ->output($fileName, 'Excel5');