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
?>
<form class="form-validate" action="<?php echo JRoute::_('index.php?option=com_hbmanager&task=updateRecentGames') ?>" method="post" id="datesForm" name="datesForm">
 
	<div class="width-100 fltlft">

		<fieldset class="adminform">		
			<legend>
				<?php 
				//echo '<pre>';print_r($form->getFieldsets('hbmanagerfields'));echo '</pre>';
				echo JText::_('Datumeinstellung'); ?>
			</legend>
			
			<dl>
				<dt>
				<?php echo $form->getLabel('startdateRecent', 'hbmanagerdates'); ?>
				</dt>
				<dd>
				<?php echo $form->getInput('startdateRecent', 'hbmanagerdates', $this->dates['startdateRecent']); ?>
				</dd>
							
				<dt>
				<?php echo $form->getLabel('enddateRecent', 'hbmanagerdates'); ?>
				</dt>
				<dd>
				<?php echo $form->getInput('enddateRecent', 'hbmanagerdates', $this->dates['enddateRecent']); ?>
				</dd>
			</dl>
			<input class="submit" type="submit" name="date_button" id="date_button" value="Datum aktualisieren" />
		</fieldset>
		
	</div>
	
</form>	
<div class="clr"></div>

 

<?php
$form = &JForm::getInstance('myformgames', JPATH_COMPONENT_ADMINISTRATOR.DS.'models'.DS.'forms'.DS.'hbrecentgames.xml');
?>
<form class="form-validate" action="<?php echo JRoute::_('index.php?option=com_hbmanager&task=updateRecentGames') ?>" method="post" id="gamesForm" name="gamesForm">

<div class="width-100 fltlft spiele">

		<fieldset class="adminform">		
			<legend>
				<?php 
				echo JText::_('Letzte Spiele'); 
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
									echo '<tr><th>'.$game->heim.'</th><td>'.$game->toreHeim.'</td></tr>'."\n";
									echo '<tr><th>'.$game->gast.'</th><td>'.$game->toreGast.'</td></tr>'."\n";
								echo '</table>'."\n";
								echo '<p>SpielNr.: '.$game->spielIDhvw.'</p>'."\n"; 
								echo '<p>'.strftime("%d.%m.%Y", strtotime($game->datum)).' um '.substr($game->uhrzeit,0,5).' Uhr</p>'."\n";
								echo '<p>Hallennr.: '.$game->hallenNummer.'</p>'."\n";
								echo '<p>'.$game->bemerkung.'</p>'."\n";
							echo '</dd>'."\n"; 
						echo '</dl>'."\n"; 
					echo '</div>'."\n";
						
					echo '<div class="spieleBericht">'."\n";
						echo $this->model->formatInput($form->getInput('spielIDhvw', 'hbrecentgames', $game->spielIDhvw), $i)."\n";
						echo '<dl>'."\n";
							echo '<dt>';
								echo $form->getLabel('bericht', 'hbrecentgames');
							echo '</dt>'."\n";
							echo '<dd>';
							echo $this->model->formatInput($form->getInput('bericht', 'hbrecentgames', $game->bericht), $i);
							echo '</dd>'."\n";
						echo '</dl>'."\n";
					echo '</td>';
					echo '</div>'."\n";
		
					echo '<div class="spieleZusatz">'."\n";
						echo '<dl>'."\n";
							echo '<dt>';
								echo $form->getLabel('spielerliste', 'hbrecentgames');
							echo '</dt>'."\n";
							echo '<dd>';
								echo $this->model->formatInput($form->getInput('spielerliste', 'hbrecentgames', $game->spielerliste), $i);		
							echo '</dd>'."\n";
							echo '<dt>';
								echo $form->getLabel('zusatz', 'hbrecentgames');
							echo '</dt>'."\n";
							echo '<dd>';
								echo $this->model->formatInput($form->getInput('zusatz', 'hbrecentgames', $game->zusatz), $i);		
							echo '</dd>'."\n";
						echo '</dl>'."\n";
						echo '<dl class="spieleTore">'."\n";
							echo '<dt>';
								echo $form->getLabel('halbzeitstand', 'hbrecentgames');
							echo '</dt>'."\n";
							echo '<dd>';
								echo $this->model->formatInput($form->getInput('halbzeitstand', 'hbrecentgames', $game->halbzeitstand), $i);		
							echo '</dd>'."\n";
							echo '<dt>';
								echo $form->getLabel('spielverlauf', 'hbrecentgames');
							echo '</dt>'."\n";
							echo '<dd>';
								echo $this->model->formatInput($form->getInput('spielverlauf', 'hbrecentgames', $game->spielverlauf), $i);
							echo '</dd>'."\n";
						echo '</dl>'."\n";
					echo '</div>'."\n";
				echo '</div>'."\n";
				
				$i++;
			}
			
		}	
	
?>
<div class="clr"></div>

			<input type="hidden" name="userid" id="userid" value="<?php echo $userid?>" />
			<input type="hidden" name="Itemid" id "Itemid" value="<?php echo $_REQUEST["Itemid"]?>" />
			<input class="submit" type="submit" name="update_button" id="update_button" value="Berichte speichern" />
			<input class="submit" type="submit" name="article_button" id="article_button" value="Artikel einstellen" />
		</fieldset>
		
	</div>
	
</form>	
