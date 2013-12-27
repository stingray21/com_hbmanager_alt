<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

JToolBarHelper::preferences('com_hbmanager');
$document = JFactory::getDocument();
$document->addStyleSheet(JURI::base(true).
		'/components/com_hbmanager/css/default.css');

$config = new JConfig();
$user = JFactory::getUser();
$userid = $user->id;


setlocale(LC_TIME, "de_DE");

// Button
echo '<a class="hbbutton" href="'.
		JRoute::_('index.php?option=com_hbmanager&task=showTeams').
		'">'.JText::_('COM_HBMANAGER_BACK').'</a>';

// get the JForm object
$form = JForm::getInstance('myform', JPATH_COMPONENT_ADMINISTRATOR.DS.'models'.
		DS.'forms'.DS.'hbteams.xml');
?>
		
<form class="form-validate" action="<?php 
		echo JRoute::_('index.php?option=com_hbmanager&task=showTeams')
				?>" method="post" id="updateTeams" name="updateTeams">


	<div class="width-100 fltlft">
	
		<fieldset class="adminform">
			<legend>
				<?php echo JText::_('COM_HBMANAGER_TEAMS_SELECT_TEAM'); ?>
			</legend>
			
			
			<table>
			
				<tr>
					<th>löschen?</th>
					<th>Kürzel</th>
					<th>Mannschaft</th>
					<th>Name</th>
					<th>Name (kurz)</th>
					<th>Liga Kürzel</th>
					<th>Liga</th>
					<th>m/w</th>
					<th>Jugend</th>
					<th>HVW Link Extension (http://www.hvw-online.org/...)</th>
				</tr>

				<?php 
				
				$i = 0;
				foreach ($this->teams as $team)
				{
					
					echo '<tr>';
						
					echo '<td>';
						echo hbhelper::formatInput($form->getInput('deleteTeam', 
								'hbDeleteTeam'), $i);
						echo hbhelper::formatInput($form->getInput('kuerzel', 
								'hbDeleteTeam', $team->kuerzel), $i);
					echo '</td>';
					echo "\n";
					
					$team =  (array) $team;
					foreach ($team as $key => $value) {
						$value = preg_replace(
									"#http://www\.hvw-online\.org/\?A#",
									"?A", $value);
						$value = preg_replace(array('/^m$/','/^w$/','/^g$/'), 
									array('männlich','weiblich','gemischt'), 
									$value);
						if ($key === 'jugend') 
							$value = preg_replace(array('/0/','/1/'), 
									array('Aktiv','Jugend'), $value);
						echo '<td>';
						echo $value;
						echo '</td>';
						echo "\n";
					}
					
					echo '</tr>';
					echo "\n\n";
					$i++;
				}
				
				?>
			</table>

			<div class="clr"></div>
			<input class="submit" type="submit" name="deleteTeams_button" id="deleteTeams_button" value="<?php 
					echo JText::_('COM_HBMANAGER_TEAMS_SUBMIT_DELETE_TEAMS') ?>" />
		</fieldset>
	
	</div>
</form>	
