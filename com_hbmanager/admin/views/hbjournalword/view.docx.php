<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
jimport('joomla.application.component.view');

setlocale(LC_TIME, "de_DE.UTF-8");

/**
 * HTML View class for the HB Manager Component
 */
class HBmanagerViewHBAmtsblattartikel extends JView
{
	
	// ?option=com_hbmanager&view=HBAmtsblattartikel&format=docx
	
	// Overwriting JView display method
	function display($tpl = docx)
	{
		$model = $this->getModel('hbamtsblatt');
		$this->assignRef('model', $model);
		
		$get = JRequest::get('get');
		$this->assignRef('get', $get);
		//echo "<pre>"; print_r($get); echo "</pre>";
		
		$model->setDateStartPrev($get['start1']);
		$model->setDateEndPrev($get['end1']);
		$model->setDateStartNext($get['start2']);
		$model->setDateEndNext($get['end2']);
		
		// Anfang
		$anfang = $model->getAbschnittAnfang();
		$this->assignRef('anfang', $anfang);
		//echo "<pre>"; print_r($anfang); echo "</pre>";
		
		
		// Letzte Spiele
		$model->getPrevGames();
		$prevGames = $model->getAbschnittLetzteSpiele();
		$this->assignRef('letzteSpiele', $prevGames);
		//echo "<pre>"; print_r($prevGames); echo "</pre>";
		
		// Berichte
		$model->getBerichte();
		$berichte = $model->getAbschnittBerichte();
		$this->assignRef('berichte', $berichte);
		//echo "<pre>"; print_r($berichte); echo "</pre>";
		
		// Kommende Spiele	
		$model->getNextGames();
		$nextGames = $model->getAbschnittKommendeSpiele();
		$this->assignRef('kommendeSpiele', $nextGames);
		//echo "<pre>"; print_r($nextGames); echo "</pre>";
		
		// Vorberichte
		$model->getVorberichte();
		$vorberichte = $model->getAbschnittVorberichte();
		$this->assignRef('vorberichte', $vorberichte);
		//echo "<pre>"; print_r($vorberichte); echo "</pre>";
		
		
		
		// Display the view
		parent::display($tpl);
		
	}
}