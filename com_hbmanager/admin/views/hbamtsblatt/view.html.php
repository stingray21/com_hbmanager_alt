<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla view library
jimport('joomla.application.component.view');
 
/**
 * HTML View class for the HB Manager Component
 */
class HBmanagerViewHBAmtsblatt extends JView
{
	// Overwriting JView display method
	function display($tpl = null)
	{
		$document = JFactory::getDocument();
		$document->addScript('https://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js');
		//$document->addScript('JPATH_ADMINISTRATOR/components/com_hbmanager/hb_date.js');
		$document->addScript(JURI::Root().'administrator/components/com_hbmanager/hb_date.js');
		
		$model = $this->getModel('hbamtsblatt');
		$this->assignRef('model', $model);
		
		// Letzte Spiele
		$recentGames = $model->getRecentGames();
		$this->assignRef('recentGames', $recentGames);
		//echo "<pre>"; print_r($recentGames); echo "</pre>";
		
		// Kommende Spiele	
		$upcomingGames = $model->getUpcomingGames();
		$this->assignRef('upcomingGames', $upcomingGames);
		//echo "<pre>"; print_r($upcomingGames); echo "</pre>";
		
		// Berichte
		$berichte = $model->getBerichte();
		$this->assignRef('berichte', $berichte);
		//echo "<pre>"; print_r($berichte); echo "</pre>";
		
		// Vorberichte
		$vorberichte = $model->getVorberichte();
		$this->assignRef('vorberichte', $vorberichte);
		//echo "<pre>"; print_r($vorberichte); echo "</pre>";
		
		$post = JRequest::get('post');
		//echo "<pre>"; print_r($post); echo "</pre>";
		if (empty($post['hbmanagerfields']['date'])) $post['hbmanagerfields']['date'] = strftime("%d.%m.%Y", time());
		$this->assignRef('post', $post);
		
		
		JToolBarHelper::title('HB Manager - Amtsblatt','hblogo');
		
		
		// get the stylesheet (with automatic lookup, possible template overrides, etc.)
		//JHtml::stylesheet('admin.stylesheet.css','media/com_hbhvwmanager/css/');
		JHtml::stylesheet('com_hbmanager/admin.stylesheet.css', array(), true);
		
		// Display the view
		parent::display($tpl);
	}
}