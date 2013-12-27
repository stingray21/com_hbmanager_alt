<?php  
//echo $_SERVER['PHP_SELF'];

define( '_JEXEC', 1 );
define( 'DS', '/' );
$joomlaUrl = preg_replace('#(.*)(/administrator/.*)#','$2',$_SERVER['PHP_SELF']);
$prefix = '';
for ($i = 1; $i < substr_count($joomlaUrl,'/'); $i++) {
	$prefix .= '..'.DS;
}
define( 'JPATH_BASE', $prefix );

//print_r( JPATH_BASE.'includes'.DS.'defines.php' );
require_once( JPATH_BASE.'includes'.DS.'defines.php' );
require_once( JPATH_BASE.'includes'.DS.'framework.php' );
require_once( JPATH_BASE.'libraries'.DS.'joomla'.DS.'factory.php' );

//$mainframe = JFactory::getApplication('admin');

//$jinput = JFactory::getApplication()->input;
//$newRowNr = $jinput->get('rowNr', 99);
$newRowNr = $_GET["rowNr"];

$form = JForm::getInstance('myform', JPATH_ADMINISTRATOR.DS.
					'components'.DS.'com_hbmanager'.DS.'models'.
					DS.'forms'.DS.'hbteams.xml');

$newRow = '<tr>';
	
$fields =  array( 'reihenfolge', 'kuerzel', 'mannschaft', 'name', 
				'nameKurz', 'ligaKuerzel', 'liga', 'geschlecht',
				'jugend', 'hvwLink');
foreach ($fields as $field) {
	$input = $form->getInput($field, 'hbteam');
	if (!empty($input)) {
		$newRow .= '<td>';
		$input = preg_replace('/name=\"([\S]{1,})\[([\S]{1,})\]/',
							"name=\"$1[".$newRowNr."][$2]", $input);
		$input = preg_replace('/id=\"([\S]{1,})_([\S]{1,})/', 
							"id=\"$1_".$newRowNr."_$2", $input);
		$newRow .= $input;
		$newRow .= '</td>';
		$newRow .=  "\n";
	}
}
	
$newRow .= '</tr>';
$newRow .= "\n\n";

echo $newRow;


//return in JSON format
// echo "{\n";
// echo "newRow: ", json_encode($newRow), "\n";
// echo "}";


?>
