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
		
<form class="form-validate" action="<?php echo JRoute::_('index.php?option=com_hbmanager&task=manageMannschaften') ?>" method="post" id="updateTeams" name="updateTeams">


	<div class="width-100 fltlft">
	
		<fieldset class="adminform">
			<legend>
				<?php echo JText::_('Mannschaften'); ?>
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
				foreach ($this->mannschaften as $mannschaft)
				{
					
					echo '<tr>';
						
					echo '<td>';
						$input = $form->getInput('deleteTeam', 'hbmannschaftdelete');
						$input = preg_replace('/name=\"([\S]{1,})\[([\S]{1,})\]/', "name=\"$1[".$i."][$2]", $input);
						$input = preg_replace('/id=\"([\S]{1,})_([\S]{1,})/', "id=\"$1_".$i."_$2", $input);
						echo $input;
						$input = $form->getInput('kuerzel', 'hbmannschaftdelete', $mannschaft->kuerzel);
						$input = preg_replace('/name=\"([\S]{1,})\[([\S]{1,})\]/', "name=\"$1[".$i."][$2]", $input);
						$input = preg_replace('/id=\"([\S]{1,})_([\S]{1,})/', "id=\"$1_".$i."_$2", $input);
						echo $input;
					echo '</td>';
					echo "\n";
					
					$mannschaft =  (array) $mannschaft;
					foreach ($mannschaft as $key => $value) {
						$value = preg_replace("/http:\/\/www\.hvw-online\.org\/\?A/", "?A", $value);
						$value = preg_replace(array('/^m$/','/^w$/','/^g$/'), array('männlich','weiblich','gemischt'), $value);
						if (strcmp($key,'jugend') == 0) $value = preg_replace(array('/0/','/1/'), array('Aktiv','Jugend'), $value);
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
			<input type="hidden" name="teamsDeleted" value="1" />
			<input class="submit" type="submit" name="submit" value="Mannschaften löschen" />
		</fieldset>
	
	</div>
</form>	
