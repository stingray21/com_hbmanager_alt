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

// get the JForm object
JForm::addFieldPath(JPATH_COMPONENT . '/models/fields');
$form = &JForm::getInstance('myform', JPATH_COMPONENT_ADMINISTRATOR.DS.'models'.DS.'forms'.DS.'hbdates.xml');

//echo '<form name="upcomingGamesForm" id="upcomingGamesForm" method="post" >'; //no "action" attribute to get to same page after submitting
//echo "<a>dates in default.php: </a><pre>"; print_r($this->dates); echo "</pre>";
?>
<form class="form-validate" action="<?php echo JRoute::_('index.php?option=com_hbmanager&task=updateUpcomingGames') ?>" method="post" id="datesForm" name="datesForm">
 
	<div class="width-100 fltlft">

		<fieldset class="adminform">		
			<legend>
				<?php 
				//echo '<pre>';print_r($form->getFieldsets('hbmanagerfields'));echo '</pre>';
				echo JText::_('Datumeinstellung'); ?>
			</legend>
			
			<dl>
				<dt>
				<?php echo $form->getLabel('startdateUpcoming', 'hbmanagerdates'); ?>
				</dt>
				<dd>
				<?php echo $form->getInput('startdateUpcoming', 'hbmanagerdates', $this->dates['startdateUpcoming']); ?>
				</dd>
							
				<dt>
				<?php echo $form->getLabel('enddateUpcoming', 'hbmanagerdates'); ?>
				</dt>
				<dd>
				<?php echo $form->getInput('enddateUpcoming', 'hbmanagerdates', $this->dates['enddateUpcoming']); ?>
				</dd>
			</dl>
			<input type="hidden" name="dateChanged" value="1" />
			<input class="submit" type="submit" name="submit" value="Datum aktualisieren" />
		</fieldset>
		
	</div>
	
</form>	
<div class="clr"></div>

<?php
$form = &JForm::getInstance('myformgames', JPATH_COMPONENT_ADMINISTRATOR.DS.'models'.DS.'forms'.DS.'hbupcominggames.xml');
?>
<form class="form-validate" action="<?php echo JRoute::_('index.php?option=com_hbmanager&task=updateUpcomingGames') ?>" method="post" id="gamesForm" name="gamesForm">

<div class="width-100 fltlft spiele">

		<fieldset class="adminform">		
			<legend>
				<?php 
				echo JText::_('Kommende Spiele'); 
				?>
			</legend>

<?php 
		$i = 0;
		foreach ($this->games as $key => $value)
		{
			echo '<h3>'.strftime("%A, %d.%m.%Y (KW%V)", strtotime($key)).'</h3>'."\n"; 
			
			foreach ($value as $game)
			{
				echo '<div class="spieleSpiel">'."\n";
				
					echo '<h4>'.$game->mannschaft.' ('.$game->ligaKuerzel.')</h4>'."\n";
					
					echo '<div class="spieleInfos">'."\n";
						echo '<dl>'."\n";
							echo '<dt>';
								echo '<label>Spiel-Infos</label>';
							echo '</dt>'."\n";
							echo '<dd>'."\n"; 
								echo '<table>'."\n";
									echo '<tr><th>'.$game->heim.'</th></tr>'."\n";
									echo '<tr><th>'.$game->gast.'</th></tr>'."\n";
								echo '</table>'."\n";
								echo '<p>SpielNr.: '.$game->spielIDhvw.'</p>'."\n"; 
								echo '<p>'.strftime("%d.%m.%Y", strtotime($game->datum)).' um '.substr($game->uhrzeit,0,5).' Uhr</p>'."\n";
								echo '<p>Hallennr.: '.$game->hallenNummer.'</p>'."\n";
								echo '<p>'.$game->bemerkung.'</p>'."\n";
							echo '</dd>'."\n"; 
						echo '</dl>'."\n"; 
					echo '</div>'."\n";
						
					echo '<div class="spieleVorschau">'."\n";
						echo $form->getInput('spielIDhvw', 'hbupcominggames', $game->spielIDhvw)."\n";
						echo '<dl>'."\n";
							echo '<dt>';
								echo $form->getLabel('vorschau', 'hbupcominggames');
							echo '</dt>'."\n";
							echo '<dd>';
							echo $this->model->formatInput($form->getInput('vorschau', 'hbupcominggames', $game->vorschau), $i);
							echo '</dd>'."\n";
						echo '</dl>'."\n";
					echo '</td>';
					echo '</div>'."\n";
		
					echo '<div class="spieleZusatz">'."\n";
						echo '<dl>'."\n";
							echo '<dt>';
								echo $form->getLabel('treffOrt', 'hbupcominggames');
							echo '</dt>'."\n";
							echo '<dd>';
								echo $this->model->formatInput($form->getInput('treffOrt', 'hbupcominggames', $game->treffOrt), $i);		
							echo '</dd>'."\n";
							echo '<dt>';
								echo $form->getLabel('treffZeit', 'hbupcominggames');
							echo '</dt>'."\n";
							echo '<dd>';
								echo $this->model->formatInput($form->getInput('treffZeit', 'hbupcominggames', $game->treffZeit), $i);		
							echo '</dd>'."\n";
						echo '</dl>'."\n";
					echo '</div>'."\n";
				echo '</div>'."\n";
				
				$i++;
			}
			
		}	
	
?>
<div class="clr"></div>

			<input type="hidden" name="sent" id="sent" value="1" />
			<input type="hidden" name="userid" id="userid" value="<?php echo $userid?>" />
			<input type="hidden" name="Itemid" id "Itemid" value="<?php echo $_REQUEST["Itemid"]?>" />
			<input class="submit" type="submit" name="submit" id="submit" value="Okay" />
		</fieldset>
		
	</div>
	
</form>	
