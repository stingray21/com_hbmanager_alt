<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla view library
jimport('joomla.application.component.view');
 
/**
 * HTML View class for the HB Manager Component
 */
class HbmanagerViewHbjournal extends JView
{
	// Overwriting JView display method
	function display($tpl = null)
	{
		$document = JFactory::getDocument();
		$document->addScript('https://ajax.googleapis.com/ajax/libs/jquery/'.
								'1.10.2/jquery.min.js');
		$document->addScript(JURI::base(true).
						'components/media/com_hbmanager/js/hbdates.js');
		
		$model = $this->getModel('hbjournal');
		$this->assignRef('model', $model);
		
		// previous games
		$prevGames = $model->getPrevGames();
		$this->assignRef('prevGames', $prevGames);
		//echo '=> view->prevGames<br><pre>"; print_r($prevGames);echo "</pre>';
		
		// next games
		$nextGames = $model->getNextGames();
		$this->assignRef('nextGames', $nextGames);
		//echo '=> view->nextGames<br><pre>"; print_r($nextGames);echo "</pre>';
		
		// game reports
		$reports = $model->getReports();
		$this->assignRef('reports', $reports);
		//echo '=> view->reports<br><pre>"; print_r($reports);echo "</pre>';
		
		// game forecasts
		$forecasts = $model->getForecasts();
		$this->assignRef('forecasts', $forecasts);
		//echo '=> view->forecasts<br><pre>"; print_r($forecasts);echo "</pre>';
		
		$post = JRequest::get('post');
		$this->assignRef('post', $post);
		
		$dates = $model->getDates();
		//echo '=> view->$dates <br><pre>"; print_r($dates); echo "</pre>';
		$this->assignRef('dates', $dates);
		
		JToolBarHelper::title(JTEXT::_('COM_HBMANAGER_JOURNAL_TITLE'),'hblogo');
		
		
		// get the stylesheet (with automatic lookup, 
		// possible template overrides, etc.)
		JHtml::stylesheet('com_hbmanager/admin.stylesheet.css', array(), true);
		
		// Display the view
		parent::display($tpl);
	}
}