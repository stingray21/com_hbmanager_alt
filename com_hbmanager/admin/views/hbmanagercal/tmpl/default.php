<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

JToolBarHelper::preferences('com_hbmanager');


// Button
echo '<div class="button">';
echo '<a href="'.JRoute::_('index.php?option=com_hbmanager&task=updateCal&kuerzel=alle').'">alle Spiele in Kalender updaten</a>';
echo '</div>';

echo '<table>';
echo '<tr><th>Mannschaft</th><th></th><th></th></tr>';
foreach ($this->mannschaften as $mannschaft)
{
	setlocale(LC_TIME, "de_DE");

	echo '<tr>';
	echo '<td><b>'.$mannschaft->name.' </b>('.$mannschaft->kuerzel.') </td>';
	
	echo '<td><div class="button"><a href="'.JRoute::_('index.php?option=com_hbmanager&task=updateCal&kuerzel='.$mannschaft->kuerzel).'"> UPDATE </a></div></td>';

	echo '</tr>';
}

echo '</table>';