<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

JToolBarHelper::preferences('com_hbmanager');
$document =& JFactory::getDocument();
$document->addStyleSheet(JURI::base(true).'/components/com_hbmanager/css/default.css');

$config = new JConfig();
$user =& JFactory::getUser();
$userid = $user->id;


setlocale(LC_TIME, "de_DE");

// Button
echo '<a id="addteams" class="hbbutton" href="'.JRoute::_('index.php?option=com_hbmanager&task=addHvwMannschaften').'">Mannschaften von HVW hinzufügen</a>';

// Button
echo '<a id="deleteteams" class="hbbutton" href="'.JRoute::_('index.php?option=com_hbmanager&task=deleteMannschaften').'">Mannschaften löschen</a>';


// get the JForm object
$form = &JForm::getInstance('myform', JPATH_COMPONENT_ADMINISTRATOR.DS.'models'.DS.'forms'.DS.'hbmanagerteams.xml');
?>
		
<form class="form-validate" action="<?php echo JRoute::_('index.php?option=com_hbmanager&task=manageMannschaften') ?>" method="post" id="updateTeams" name="updateTeams">

	<div class="width-100 fltlft">
	
		<fieldset class="adminform">
			<legend>
				<?php echo JText::_('Mannschaften'); ?>
			</legend>
			
			
			<table id="teamstable" name="teamstable">
			
				<tr>
				<?php
				foreach($form->getFieldset('mannschaft') as $field)
					{
						echo '<th>'.$field->label.'</th>';					
					}
				?>
				</tr>

				<?php 
				
				$i = 0;
				foreach ($this->mannschaften as $mannschaft)
				{
					
					echo '<tr>';
					
					$mannschaft =  (array) $mannschaft;
					foreach ($mannschaft as $key => $value) {
						$value = preg_replace("/http:\/\/www\.hvw-online\.org\/\?A/", "?A", $value);
						$input = $form->getInput($key, 'hbmannschaft', $value);
						if (!empty($input)) {
							echo '<td>';
							//$input = preg_replace('/name=\"([\S]{1,})\[([\S]{1,})\]/', "name=\"$1[][$2]", $input);
							$input = preg_replace('/name=\"([\S]{1,})\[([\S]{1,})\]/', "name=\"$1[".$i."][$2]", $input);
							$input = preg_replace('/id=\"([\S]{1,})_([\S]{1,})/', "id=\"$1_".$i."_$2", $input);
							echo $input;
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
			
			<input type="hidden" name="teamsUpdated" value="1" />
			<input class="submit" type="submit" name="submit" value="Mannschaften aktualisieren" />
		</fieldset>
	
	</div>
</form>	