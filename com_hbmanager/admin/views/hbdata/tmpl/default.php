<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

JToolBarHelper::preferences('com_hbmanager');

//setlocale(LC_TIME, "de_DE");

// Button
echo '<a id="hvwupdateall" class="hbbutton" href="'.
		JRoute::_('index.php?option=com_hbmanager&task=updateData&teamkey=all').
		'">'.JText::_('COM_HBMANAGER_DATA_UPDATE_ALL_BUTTON').'</a>';



echo '<table id="hvwupdate">';
echo '<tr><th>Mannschaft</th><th></th><th>Tabelle</th><th>Spielplan</th><th></th></tr>';

//$datePattern = "%A, %d.%m.%Y &nbsp;&nbsp;%H:%M:%S Uhr";
$datePattern = 'D, d.m.Y - H:i:s \U\h\r';

foreach ($this->teams as $team)
{
	echo '<tr>';
	echo '<td><b>'.$team->mannschaft.' </b>('.$team->kuerzel.') </td>';
	if(!empty($team->hvwLink)) echo '<td>letztes Update: </td>';
	else echo '<td>kein HVW Daten</td>';
	
	echo '<td';
	if(in_array($team->kuerzel, $this->updated['rankings'])) 
			echo ' class="updated"';
	echo '>';
	if (!empty($team->updateTabelle)) 
		//echo strftime($datePattern, strtotime($team->updateTabelle));
		echo JHTML::_('date', $team->updateTabelle , $datePattern);
	echo '</td>';
	
	echo '<td';
	if(in_array($team->kuerzel, $this->updated['schedules'])) 
			echo ' class="updated"';
	echo '>';
	if (!empty($team->updateSpielplan)) 
		//echo strftime($datePattern, strtotime($team->updateSpielplan));
		echo JHTML::_('date', $team->updateSpielplan , $datePattern);
	echo '</td>';
	
	if (!empty($team->hvwLink)) {
		echo '<td><a class="hbbutton" href="'.
			JRoute::_('index.php?option=com_hbmanager&task=updateData&teamkey='.
			$team->kuerzel).'"> UPDATE </a></td>';
	}
	echo '</tr>';
}

echo '</table>';