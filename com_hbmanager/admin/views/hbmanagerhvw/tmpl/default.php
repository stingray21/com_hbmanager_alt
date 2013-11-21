<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

JToolBarHelper::preferences('com_hbmanager');

// Button
echo '<a id="hvwupdateall" class="hbbutton" href="'.JRoute::_('index.php?option=com_hbmanager&task=updatehvw&kuerzel=alle').'">Alle DB Tabellen updaten</a>';



echo '<table id="hvwupdate">';
echo '<tr><th>Mannschaft</th><th></th><th>Tabelle</th><th>Spielplan</th><th></th></tr>';
foreach ($this->mannschaften as $mannschaft)
{
	//echo "<pre>"; print_r($mannschaft); echo "</pre>";
	setlocale(LC_TIME, "de_DE");
	
	echo '<tr>';
	echo '<td><b>'.$mannschaft->mannschaft.' </b>('.$mannschaft->kuerzel.') </td>';
	if(!empty($mannschaft->hvwLink)) echo '<td>letztes Update: </td>';
	else echo '<td>kein HVW Daten</td>';
	
	echo '<td';
	if(in_array($mannschaft->kuerzel, $this->updated['tabellen'])) echo ' class="updated"';
	echo '>';
	if (!empty($mannschaft->updateTabelle)) echo strftime("%a, %d.%m.%Y &nbsp;&nbsp;%H:%M:%S Uhr", strtotime($mannschaft->updateTabelle));
	echo '</td>';
	
	echo '<td';
	if(in_array($mannschaft->kuerzel, $this->updated['spielplaene'])) echo ' class="updated"';
	echo '>';
	if (!empty($mannschaft->updateSpielplan)) echo strftime("%a, %d.%m.%Y &nbsp;&nbsp;%H:%M:%S Uhr", strtotime($mannschaft->updateSpielplan));
	echo '</td>';
	
	if (!empty($mannschaft->hvwLink)) echo '<td><a class="hbbutton" href="'.JRoute::_('index.php?option=com_hbmanager&task=updatehvw&kuerzel='.$mannschaft->kuerzel).'"> UPDATE </a></td>';
		
	echo '</tr>';
}

echo '</table>';


