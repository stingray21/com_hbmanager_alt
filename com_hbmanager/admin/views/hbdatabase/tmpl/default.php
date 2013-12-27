<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

JToolBarHelper::preferences('com_hbmanager');


echo '<h4>'.JTEXT::_('COM_HBMANAGER_DATABASE_TITLE').'</h4>';


echo '<table>';
echo '<tr>
		<th>Mannschaft</th><th>DB Tabelle</th><th>DB Spielplan</th><th></th>
	</tr>';

foreach ($this->teams as $team)
{
	echo '<tr>';
		echo '<td><b>'.$team->mannschaft.' </b>('.$team->kuerzel.') </td>';
		
		echo '<td>';
		echo '<a href="'.$this->model->getDbPmaTableLink($team->tabelleDB).
				'" target="_BLANK" >'.$team->tabelleDB.'</a>';
		echo '</td>';
		
		echo '<td>';
		echo '<a href="'.$this->model->getDbPmaTableLink($team->spielplanDB).
				'" target="_BLANK" >'.$team->spielplanDB.'</a>';
		echo '</td>';
		
	echo '</tr>';
}
echo '</table>';


echo '<a><b>für alle Mannschaften:</b></a>';

// Buttons
echo '<a class="hbbutton" href="'.
		JRoute::_('index.php?option=com_hbmanager'.
				'&task=createDBtables&dbOption=rankings').
		'">'.JTEXT::_('COM_HBMANAGER_DATABASE_RANKINGS_BUTTON').'</a>';
echo '<a class="hbbutton" href="'.
		JRoute::_('index.php?option=com_hbmanager'.
				'&task=createDBtables&dbOption=schedules').
		'">'.JTEXT::_('COM_HBMANAGER_DATABASE_SCHEDULES_BUTTON').'</a>';

echo '<h4>Weitere DB Tabellen (keiner Mannschaft zugeorndet)</h4>';

echo '<table>';
echo '<tr><th>Name</th></tr>';

//echo __FILE__.'('.__LINE__.'):<pre>';print_r($this->model->getDbTables());echo'</pre>';
foreach ($this->model->getDbTables() as $table)
{
	echo '<tr>';
	echo '<td>';
	echo '<a href="'.$this->model->getDbPmaTableLink($table).'" target="_BLANK" >'.
			$table.'</a>';
	echo '</td>';
	echo '</tr>';
}

echo '</table>';

echo '<h4>Weiteres</h4>';
echo '<div><p>SQL-Code um alle HB DB-Tabellen anzuzeigen: 
	<br /> SHOW TABLES LIKE \'hb_%\'</p></div>';
