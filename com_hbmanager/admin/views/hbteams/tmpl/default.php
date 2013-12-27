<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

JToolBarHelper::preferences('com_hbmanager');
$document = JFactory::getDocument();
$document->addStyleSheet(JURI::base(true).'/components/com_hbmanager/css/default.css');

$config = new JConfig();
$user = JFactory::getUser();
$userid = $user->id;


setlocale(LC_TIME, "de_DE");

// Button
echo '<a id="addteams" class="hbbutton" href="'.
		JRoute::_('index.php?option=com_hbmanager&task=addTeams').
		'">'.JText::_('COM_HBMANAGER_TEAMS_ADD_TEAMS_BUTTON').'</a>';

// Button
echo '<a id="deleteteams" class="hbbutton" href="'.
		JRoute::_('index.php?option=com_hbmanager&task=deleteTeams').
		'">'.JText::_('COM_HBMANAGER_TEAMS_DELETE_TEAMS_BUTTON').'</a>';


// get the JForm object
$form = JForm::getInstance('myform', JPATH_COMPONENT_ADMINISTRATOR.DS.
		'models'.DS.'forms'.DS.'hbteams.xml');
?>
		
<form class="form-validate" action="<?php 
		echo JRoute::_('index.php?option=com_hbmanager&task=showTeams') 
		?>" method="post" id="updateTeams" name="updateTeams">

	<div class="width-100 fltlft">
	
		<fieldset class="adminform">
			<legend>
				<?php echo JText::_('COM_HBMANAGER_TEAMS_FORM_TITLE'); ?>
			</legend>
			
			
			<table id="teamstable" name="teamstable">
			
				<tr>
				<?php
				foreach($form->getFieldset('team') as $field)
					{
						echo '<th>'.$field->label.'</th>';					
					}
				?>
				</tr>
				
				<?php 
				
				$i = 0;
				foreach ($this->teams as $team)
				{
					
					echo '<tr>';
					
					$team =  (array) $team;
					foreach ($team as $key => $value) {
						$value = preg_replace(
										"#http://www\.hvw-online\.org/\?A#",
										"?A", $value);
						$input = $form->getInput($key, 'hbteam', $value);
						if (!empty($input)) {
							echo '<td>';
							echo hbhelper::formatInput($input, $i);
							echo '</td>';
							echo "\n";
						}
					}
					
					echo '</tr>';
					echo "\n\n";
					$i++;
				}
				
				?>
			</table>
			
			<?php 
			// Button
			echo '<a id="addcustomteam" name="addcustomteam" class="hbbutton">+</a>';
			?>
			
			<div class="clr"></div>
			
			<input class="submit" type="submit" name="updateTeams_button" id="updateTeams_button" value="<?php 
				echo JText::_('COM_HBMANAGER_TEAMS_SUBMIT_UPDATE_TEAMS') ?>" />
		
	
	</div>
</form>	