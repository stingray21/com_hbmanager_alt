<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

JToolBarHelper::preferences('com_hbmanager');

$model = $this->getModel();

echo '<h4>HVW Daten Tabellen</h4>';


echo '<table>';
echo '<tr><th>Mannschaft</th><th>DB Tabelle</th><th>DB Spielplan</th><th></th></tr>';
foreach ($this->mannschaften as $mannschaft)
{
	echo '<tr>';
		echo '<td><b>'.$mannschaft->mannschaft.' </b>('.$mannschaft->kuerzel.') </td>';
		
		echo '<td>';
		echo '<a href="'.$model->getDBtableLink($mannschaft->tabelleDB).'" target="_BLANK" >'.$mannschaft->tabelleDB.'</a>';
		echo '</td>';
		
		echo '<td>';
		echo '<a href="'.$model->getDBtableLink($mannschaft->spielplanDB).'" target="_BLANK" >'.$mannschaft->spielplanDB.'</a>';
		echo '</td>';

	echo '</tr>';
}

echo '<tr><td></td><td></td><td></td><td></td><td></td></tr>';

echo '<tr>';
	echo '<td><b>für alle Mannschaften:</b></td>';

	echo '<td>';
	echo '<div class="button">';
	echo '<a href="'.JRoute::_('index.php?option=com_hbmanager&task=createDBtables&createTables=tabelle').'">Tabelle DB erstellen</a>';
	echo '</div>';
	echo '</td>';
	
	echo '<td>';
	echo '<div class="button">';
	echo '<a href="'.JRoute::_('index.php?option=com_hbmanager&task=createDBtables&createTables=spielplan').'">Spielplan DB erstellen</a>';
	echo '</div>';
	echo '</td>';

echo '</tr>';

echo '</table>';

echo '<h4>Weitere DB Tabellen (keiner Mannschaft zugeorndet)</h4>';

echo '<table>';
echo '<tr><th>Name</th></tr>';
foreach ($model->getDBtables() as $table)
{
	echo '<tr>';
	echo '<td>';
	echo '<a href="'.$model->getDBtableLink($table).'" target="_BLANK" >'.$table.'</a>';
	echo '</td>';
	echo '</tr>';
}

echo '</table>';

echo '<h4>Weiteres</h4>';
echo '<div><p>SQL-Code um alle HB DB-Tabellen anzuzeigen: <br /> SHOW TABLES LIKE \'hb_%\'</p></div>';
