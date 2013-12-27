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

$model = $this->model;

setlocale(LC_TIME, "de_DE");

// Button
echo '<a class="hbbutton" href="'.
		JRoute::_('index.php?option=com_hbmanager&task=showTeams').
		'">'.JText::_('COM_HBMANAGER_BACK').'</a>';

// Button
echo '<a id="addteams" class="hbbutton" href="'.
		JRoute::_('index.php?option=com_hbmanager&task=addTeams&getHvwData=1').
		'">'.JText::_('COM_HBMANAGER_TEAMS_ADD_TEAMS_UPDATEHVW_BUTTON').'</a>';


// get the JForm object
$form = JForm::getInstance('myform', 
		JPATH_COMPONENT_ADMINISTRATOR.DS.'models'.DS.'forms'.DS.'hbteams.xml');
?>
<form class="form-validate" action="<?php 
		echo JRoute::_('index.php?option=com_hbmanager&task=showTeams')
		?>" method="post" id="addTeam" name="addTeam">

	<div class="width-100 fltlft">
	
		<fieldset class="adminform">
			<legend>
				<?php echo JText::_('COM_HBMANAGER_TEAMS_SELECT_TEAM'); ?>
			</legend>
			
			
			<table>
			
				<tr>
				<?php
				foreach($form->getFieldset('addTeam') as $field)
					{
						echo '<th>'.$field->label.'</th>';					
					}
				?>
				</tr>

				<?php 
				$i = 0;
				foreach ($this->leagues as $team)
				{
					
					if (preg_match("/Geisl/",$team->rankingTeams)) {
						echo '<tr>';
						
						$team = (array) $team;
						
						echo '<td>';
						$checked = preg_match("/Geisl/",$team['rankingTeams']);
						echo hbhelper::formatInput($form->getInput('includeTeam', 
								'hbAddTeam', $checked), $i);
						echo '</td>';
						echo "\n";
						
						foreach (array('staffel','staffelName','staffelLink',
							'geschlecht','jugend','saison') as $value)
						{
							echo '<td>';
							echo hbhelper::formatInput($form->getInput($value, 
								'hbAddTeam', $team[$value]), $i);
							echo '</td>';
							echo "\n";
						}
						
						echo '<td>';
						$input = hbhelper::formatInput($form->getInput('rankingName', 
								'hbAddTeam'), $i);
						echo $model->selectHomeTeam($input, $team['rankingTeams'], 
								"/Geisl/");
						echo '</td>';
						echo "\n";
						
						echo '<td>';
						$input = hbhelper::formatInput($form->getInput('scheduleName', 
								'hbAddTeam'), $i);
						echo $model->selectHomeTeam($input, $team['scheduleTeams'], 
								"/Geisl/");
						echo '</td>';
						echo "\n";
						echo '</tr>';
						echo "\n\n";
						$i++;
					}
				}
				?>
			</table>

			<div class="clr"></div>
			<input class="submit" type="submit" name="addTeams_button" id="addTeams_button" value="<?php 
					echo JText::_('COM_HBMANAGER_TEAMS_SUBMIT_ADD_TEAMS') ?>" />
		</fieldset>
	
	</div>
</form>