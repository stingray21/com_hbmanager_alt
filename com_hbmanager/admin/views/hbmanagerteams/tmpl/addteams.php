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
echo '<a id="deleteteams" class="hbbutton" href="'.JRoute::_('index.php?option=com_hbmanager&task=manageMannschaften').'">zurück</a>';

// get the JForm object
$form = &JForm::getInstance('myform', JPATH_COMPONENT_ADMINISTRATOR.DS.'models'.DS.'forms'.DS.'hbmanagerteams.xml');
?>
<form class="form-validate" action="<?php echo JRoute::_('index.php?option=com_hbmanager&task=manageMannschaften') ?>" method="post" id="addTeam" name="addTeam">

	<div class="width-100 fltlft">
	
		<fieldset class="adminform">
			<legend>
				<?php echo JText::_('Mannschaften auswählen'); ?>
			</legend>
			
			
			<table>
			
				<tr>
				<?php
				foreach($form->getFieldset('mannschaftNeu') as $field)
					{
						echo '<th>'.$field->label.'</th>';					
					}
				?>
				</tr>

				<?php 
				
				$i = 0;
				foreach ($this->staffeln as $mannschaft)
				{
					
					if (preg_match("/Geisl/",$mannschaft->rankingTeams)) {
						echo '<tr>';
						
						$mannschaft = (array) $mannschaft;
						
						echo '<td>';
						$checked = preg_match("/Geisl/",$mannschaft['rankingTeams']);
						$input = $form->getInput('includeMannschaft', 'hbmannschaftNeu', $checked);
						$input = preg_replace('/name=\"([\S]{1,})\[([\S]{1,})\]/', "name=\"$1[".$i."][$2]", $input);
						$input = preg_replace('/id=\"([\S]{1,})_([\S]{1,})/', "id=\"$1_".$i."_$2", $input);
						echo $input;
						echo '</td>';
						echo "\n";
						
						foreach (array('staffel','staffelName','staffelLink','geschlecht','jugend','saison') as $value)
						{
							echo '<td>';
							$input = $form->getInput($value, 'hbmannschaftNeu', $mannschaft[$value]);
							$input = preg_replace('/name=\"([\S]{1,})\[([\S]{1,})\]/', "name=\"$1[".$i."][$2]", $input);
							$input = preg_replace('/id=\"([\S]{1,})_([\S]{1,})/', "id=\"$1_".$i."_$2", $input);
							echo $input;
							echo '</td>';
							echo "\n";
						}
						
						echo '<td>';
						$input = $form->getInput('rankingName', 'hbmannschaftNeu');
						$input = preg_replace('/name=\"([\S]{1,})\[([\S]{1,})\]/', "name=\"$1[".$i."][$2]", $input);
						$input = preg_replace('/id=\"([\S]{1,})_([\S]{1,})/', "id=\"$1_".$i."_$2", $input);
						$options = '';
						foreach (explode('&&',$mannschaft['rankingTeams']) as $name){
							$options .= '<option ';
							if (preg_match("/Geisl/",$name)) $options .= 'selected="selected" ';
							$options .= 'value="'.$name.'">'.$name.'</option>'."\n";
						}
						$input = str_replace('<option value="leer">auswaehlen</option>', $options, $input);
						echo $input;
						echo '</td>';
						echo "\n";
						
						echo '<td>';
						$input = $form->getInput('scheduleName', 'hbmannschaftNeu');
						$input = preg_replace('/name=\"([\S]{1,})\[([\S]{1,})\]/', "name=\"$1[".$i."][$2]", $input);
						$input = preg_replace('/id=\"([\S]{1,})_([\S]{1,})/', "id=\"$1_".$i."_$2", $input);
						$options = '';
						foreach (explode('&&',$mannschaft['scheduleTeams']) as $name){
							$options .= '<option ';
							if (preg_match("/Geisl/",$name)) $options .= 'selected="selected" ';
							$options .= 'value="'.$name.'">'.$name.'</option>'."\n";
						}
						$input = str_replace('<option value="leer">auswaehlen</option>', $options, $input);
						echo $input;
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
			<input type="hidden" name="teamsAdded" value="1" />
			<input class="submit" type="submit" name="submit" value="Mannschaften hinzufügen" />
		</fieldset>
	
	</div>
</form>	

		

