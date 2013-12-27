<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

JToolBarHelper::preferences('com_hbmanager');

setlocale(LC_TIME, "de_DE.UTF-8");
require_once JPATH_COMPONENT_ADMINISTRATOR.DS.'helpers'.DS.'icon.php';

// get the JForm object
JForm::addFieldPath(JPATH_COMPONENT . '/models/fields');
$form = JForm::getInstance('myform', JPATH_COMPONENT_ADMINISTRATOR.DS.
				'models'.DS.'forms'.DS.'hbdates.xml');
?>

<form class="form-validate" action="<?php 
	JRoute::_('index.php?option=com_hbmanager&task=showJournal') 
	?>" method="post" id="datesForm" name="datesForm">

	<div class="width-100 fltlft">
	
		<fieldset class="adminform">
			<legend>
				<?php 
				echo JText::_('COM_HBMANAGER_DATE_SETTINGS');
				?>
			</legend>
			<dl>
				<dt>
				<?php echo $form->getLabel('date', 'hbDates'); ?>
				</dt>
				<dd>
				<?php echo $form->getInput('date', 'hbDates', 
						strftime("%d.%m.%Y", strtotime(
								$this->dates['date']))); 
				?>
				</dd>
			</dl>
			<div class="clr"></div>	
			
			<h3><?php echo JText::_('COM_HBMANAGER_DATE_PREV_GAMES');?></h3>
			<dl>
				<dt>
				<?php
					echo $form->getLabel('startdatePrev', 'hbdates'); 
				?>
				</dt>
				<dd>
				<?php
					echo $form->getInput('startdatePrev', 'hbdates', 
							$this->dates['startdatePrev']);
				?>
				</dd>
				
				<dt>
				<?php
					echo $form->getLabel('enddatePrev', 'hbdates');
				?>
				</dt>
				<dd>
				<?php 
					echo $form->getInput('enddatePrev', 'hbdates', 
							$this->dates['enddatePrev']);
				?>
				</dd>
			</dl>
			
			<div class="clr"></div>	
			
			<h3><?php echo JText::_('COM_HBMANAGER_DATE_NEXT_GAMES');?></h3>
			
			<dl>
				<dt>
				<?php
					echo $form->getLabel('startdateNext', 'hbdates'); 
				?>
				</dt>
				<dd>
				<?php
					echo $form->getInput('startdateNext', 'hbdates', 
							$this->dates['startdateNext']);
				?>
				</dd>
				
				<dt>
				<?php
					echo $form->getLabel('enddateNext', 'hbdates');
				?>
				</dt>
				<dd>
				<?php 
					echo $form->getInput('enddateNext', 'hbdates', 
							$this->dates['enddateNext']);
				?>
				</dd>
			</dl>
			
			<div class="clr"></div>
			<input class="submit" type="submit" name="date_button" id="date_button" value="<?php 
				echo JText::_('COM_HBMANAGER_DATE_UPDATE_BUTTON');?>"/>
		</fieldset>
	
	</div>
</form>	
<div class="clr"></div>

<?php 
	// word only keeps direct formatted text
	
	//general
	$style_font = "font-family: Arial; font-size: 9pt;";
	//$style_font .= " line-height: 10pt;";// fuer Aussehen wie im Amtsblatt
	
	// <h2>
	$style_h2 = ' class="AmtsblattUeberschrift" '.
					'style="padding: 0px; margin: 6pt 0 0; '.
					$style_font.' font-weight: bold;"';
	// <p>
	$style_p = ' class="AmtsblattText" '.
					'style=\"padding: 0px; margin: 0; text-align: justify; '.
					$style_font.'"';
	// <pre>
	$style_pre = ' class="AmtsblattText" '.
					'style="margin: 0; tab-size: 8; width: 100%; word-wrap: '.
					'break-word; '.$style_font.'"';
	
	$styles = array('style_h2' => $style_h2, 
					'style_p' => $style_p, 
					'style_pre' => $style_pre);

?>

<div id="amtsblatt">
<?php 
$this->item = null;
$params = null;
echo JHtml::_('icon.msword', $this->item, $params);
?>
	
	<p style="font-family: Arial; font-size: 12pt; text-align: right; margin: 1em 0">
		<?php 
		//echo strftime('%A, %d.%m.%Y');
		$datePattern = 'l, d.m.Y';
		echo JHTML::_('date', time() , $datePattern);
		?>
	</p>
	<h1 style="font-family: Arial; font-size: 14pt; margin: 1em 0 2em; color: black;">
		Artikel für das Amtsblatt Geislingen
	</h1>

	<div id="inhalt" style="width: 9cm">
	
		<!-- Abschnitt Anfang - Überschrift und Link -->
		<?php 
		$anfang = $this->model->getAbschnittAnfang();
		
		echo '<div>';
		echo "<h2{$styles['style_h2']}>{$anfang['ueberschrift']}</h2>";
		
		if (isset($anfang['link']))
		{
			echo "<p{$styles['style_p']}>";
			echo nl2br($anfang['link']);
			echo "</p>";
		}
		echo '</div>';
		?>
		
		<!-- Abschnitt "Letzte Spiele" -->
		<?php 
		$letzteSpiele = $this->model->getAbschnittLetzteSpiele();
		
		if (!empty($letzteSpiele))
		{
			echo "<h2{$styles['style_h2']}>{$letzteSpiele['ueberschrift']}</h2>";
			echo "<p{$styles['style_p']}>{$letzteSpiele['dates']}</p>";
				
			echo "<pre{$styles['style_pre']}>";
			echo $letzteSpiele['spiele'];
			echo "</pre>";
			echo '</div>';
		}
		?>
		
		
		<!-- Abschnitt "Berichte" -->
		<?php 
		$berichte = $this->model->getAbschnittBerichte();
		if (!empty($berichte))
		{
			echo '<div>';
			echo '<h2'.$styles['style_h2'].'>Berichte</h2>';
				
			foreach ($berichte as $bericht)
			{
				
				echo "<h2{$styles['style_h2']}>{$bericht['ueberschrift']}</h2>";
				echo "<pre{$styles['style_pre']}>";
				echo "{$bericht['ergebnis']}\n";
				echo "</pre>";
				echo "<p{$styles['style_p']}>";
				echo nl2br($bericht['text']);
				if (isset($bericht['spieler'])) echo "<br />".nl2br($bericht['spieler']);
				echo "</p>";
			}
			echo '</div>';
		}
		
		?>
		
		<!-- Abschnitt "Kommende Spiele" -->
		<?php 
		$kommendeSpiele = $this->model->getAbschnittKommendeSpiele();
		if (!empty($kommendeSpiele))
		{
			//echo "<pre>"; print_r($kommendeSpiele); echo "</pre>";
			
			if (true)
			{
				echo '<div>';
				echo '<h2'.$styles['style_h2'].'>'.$kommendeSpiele['ueberschrift'].'</h2>';
				echo "<pre{$styles['style_pre']}>";
				echo $kommendeSpiele['spiele'];
				echo "</pre>";
				echo '</div>';
			}
		}
		?>
		
		
		<!-- Abschnitt "Vorschau" -->
		<?php 
		$vorberichte = $this->model->getAbschnittVorberichte();
		if (!empty($vorberichte))
		{
			echo '<div>';
			echo '<h2'.$styles['style_h2'].'>Vorschau</h2>';
				
			foreach ($vorberichte as $bericht)
			{
				echo "<h2{$styles['style_h2']}>{$bericht['ueberschrift']}</h2>";
				echo "<pre{$styles['style_pre']}>";
				echo $bericht['spiel']."\n";
				if (!empty($bericht['treff']))
				{
					echo  $bericht['treff']."\n";
				}
				echo "</pre>";
				echo "<p{$styles['style_p']}>";
				echo nl2br($bericht['text']);
				echo "</p>";
			}
			echo '</div>';
		}
		?>
		
	</div>
	

</div> <!-- End of amtsblatt -->



