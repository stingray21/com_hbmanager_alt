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

// get the JForm object
JForm::addFieldPath(JPATH_COMPONENT . '/models/fields');
$form = JForm::getInstance('myform', JPATH_COMPONENT_ADMINISTRATOR.DS.
		'models'.DS.'forms'.DS.'hbdates.xml');


//echo __FILE__.'('.__LINE__.'):<pre>';print_r($this->dates);echo'</pre>';		
?>
<form class="form-validate" action="<?php 
		echo JRoute::_('index.php?option=com_hbmanager&task=showPrevGames')
		?>" method="post" id="datesForm" name="datesForm">
 
	<div class="width-100 fltlft">

		<fieldset class="adminform">		
			<legend>
				<?php
				echo JText::_('COM_HBMANAGER_DATE_SETTINGS')
				?>
			</legend>
			
			<dl>
				<dt>
				<?php echo $form->getLabel('startdatePrev', 'hbdates'); ?>
				</dt>
				<dd>
				<?php echo $form->getInput('startdatePrev', 'hbdates', $this->dates['startdatePrev']); ?>
				</dd>
							
				<dt>
				<?php echo $form->getLabel('enddatePrev', 'hbdates'); ?>
				</dt>
				<dd>
				<?php echo $form->getInput('enddatePrev', 'hbdates', $this->dates['enddatePrev']); ?>
				</dd>
			</dl>
			<input class="submit" type="submit" name="date_button" id="date_button" value="<?php echo JText::_('COM_HBMANAGER_DATE_UPDATE_BUTTON');?>" />
		</fieldset>
		
	</div>
	
</form>	
<div class="clr"></div>

 

<?php
$form = JForm::getInstance('myformgames', JPATH_COMPONENT_ADMINISTRATOR.DS.'models'.DS.'forms'.DS.'hbprevgames.xml');
?>
<form class="form-validate" action="<?php echo JRoute::_('index.php?option=com_hbmanager&task=showPrevGames') ?>" method="post" id="gamesForm" name="gamesForm">

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
				//echo __FILE__.'('.__LINE__.'):<pre>';print_r($game);echo'</pre>';
				
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
						echo hbhelper::formatInput($form->getInput('spielIDhvw', 'hbprevgames', $game->spielIDhvw), $i)."\n";
						echo '<dl>'."\n";
							echo '<dt>';
								echo $form->getLabel('bericht', 'hbprevgames');
							echo '</dt>'."\n";
							echo '<dd>';
							echo hbhelper::formatInput($form->getInput('bericht', 'hbprevgames', $game->bericht), $i);
							echo '</dd>'."\n";
						echo '</dl>'."\n";
					echo '</td>';
					echo '</div>'."\n";
		
					echo '<div class="spieleZusatz">'."\n";
						echo '<dl>'."\n";
							echo '<dt>';
								echo $form->getLabel('spielerliste', 'hbprevgames');
							echo '</dt>'."\n";
							echo '<dd>';
								echo hbhelper::formatInput($form->getInput('spielerliste', 'hbprevgames', $game->spielerliste), $i);		
							echo '</dd>'."\n";
							echo '<dt>';
								echo $form->getLabel('zusatz', 'hbprevgames');
							echo '</dt>'."\n";
							echo '<dd>';
								echo hbhelper::formatInput($form->getInput('zusatz', 'hbprevgames', $game->zusatz), $i);		
							echo '</dd>'."\n";
						echo '</dl>'."\n";
						echo '<dl class="spieleTore">'."\n";
							echo '<dt>';
								echo $form->getLabel('halbzeitstand', 'hbprevgames');
							echo '</dt>'."\n";
							echo '<dd>';
								echo hbhelper::formatInput($form->getInput('halbzeitstand', 'hbprevgames', $game->halbzeitstand), $i);		
							echo '</dd>'."\n";
							echo '<dt>';
								echo $form->getLabel('spielverlauf', 'hbprevgames');
							echo '</dt>'."\n";
							echo '<dd>';
								echo hbhelper::formatInput($form->getInput('spielverlauf', 'hbprevgames', $game->spielverlauf), $i);
							echo '</dd>'."\n";
						echo '</dl>'."\n";
					echo '</div>'."\n";
				echo '</div>'."\n";
				
				$i++;
			}
			
		}	
	
?>
<div class="clr"></div>
			<input type="hidden" name="hbdates[startdatePrev]" id="hbdates[startdatePrev]" value="<?php echo $this->dates['startdatePrev']?>" />
			<input type="hidden" name="hbdates[enddatePrev]" id="hbdates[enddatePrev]" value="<?php echo $this->dates['enddatePrev']?>" />
			

			<input type="hidden" name="userid" id="userid" value="<?php echo $userid?>" />
			<?php
			if (isset($_REQUEST["Itemid"])) {
				echo '<input type="hidden" name="Itemid" id "Itemid" value="'.$_REQUEST["Itemid"].'" />';
			}
			?>
			<input class="submit" type="submit" name="update_button" id="update_button" value="Berichte speichern" />
			<input class="submit" type="submit" name="article_button" id="article_button" value="Artikel einstellen" />
		</fieldset>
		
	</div>
	
</form>	
