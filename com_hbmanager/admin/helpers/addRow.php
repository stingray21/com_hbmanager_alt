<?php  



define( '_JEXEC', 1 );
define( 'DS', '/' );
define( 'JPATH_BASE', $_SERVER[ 'DOCUMENT_ROOT' ].'joomla' );
define( 'JPATH_COMPONENT_ADMINISTRATOR', $_SERVER[ 'DOCUMENT_ROOT' ].'joomla'.DS.'administrator' );

require_once( JPATH_BASE . DS . 'includes' . DS . 'defines.php' );
require_once( JPATH_BASE . DS . 'includes' . DS . 'framework.php' );
require_once( JPATH_BASE . DS . 'libraries' . DS . 'joomla' . DS . 'factory.php' );

//$mainframe =& JFactory::getApplication('admin');

//$jinput = JFactory::getApplication()->input;
//$newRowNr = $jinput->get('rowNr', 99);
$newRowNr = $_GET["rowNr"];

$form = &JForm::getInstance('myform', JPATH_ADMINISTRATOR.DS.'components'.DS.'com_hbmanager'.DS.'models'.DS.'forms'.DS.'hbmanagerteams.xml');

$newRow = '<tr>';
	
$fields =  array( 'reihenfolge', 'kuerzel', 'mannschaft', 'name', 'nameKurz', 'ligaKuerzel', 'liga', 'geschlecht', 'jugend', 'hvwLink');
foreach ($fields as $field) {
	$input = $form->getInput($field, 'hbmannschaft');
	if (!empty($input)) {
		$newRow .= '<td>';
		$input = preg_replace('/name=\"([\S]{1,})\[([\S]{1,})\]/', "name=\"$1[".$newRowNr."][$2]", $input);
		$input = preg_replace('/id=\"([\S]{1,})_([\S]{1,})/', "id=\"$1_".$newRowNr."_$2", $input);
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
