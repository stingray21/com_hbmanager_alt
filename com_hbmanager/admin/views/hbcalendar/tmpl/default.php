<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

JToolBarHelper::preferences('com_hbmanager');


// Button
echo '<a class="hbbutton" href="'.
		JRoute::_('index.php?option=com_hbmanager&task=updateCal&teamkey=all').
		'">'.JTEXT::_('COM_HBMANAGER_CALENDAR_UPDATE_ALL_BUTTON').'</a>';

echo '<table>';
echo '<tr><th>Mannschaft</th><th></th><th></th></tr>';
foreach ($this->teams as $team)
{
	setlocale(LC_TIME, "de_DE");

	echo '<tr>';
	echo '<td><b>'.$team->name.' </b>('.$team->kuerzel.') </td>';
	
	echo '<td>';
	echo '<a class="hbbutton" href="'.
		JRoute::_('index.php?option=com_hbmanager&task=updateCal&teamkey='.
		$team->kuerzel).'">'.JTEXT::_('COM_HBMANAGER_CALENDAR_UPDATE_BUTTON').'</a>';
	echo '</td>';
	echo '</tr>';
}

echo '</table>';
?>