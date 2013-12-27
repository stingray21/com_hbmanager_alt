<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla view library
jimport('joomla.application.component.view');

/**
 * HTML View class for the HB Manager Component
 */
class hbmanagerViewHbnextgames extends JView
{
	// Overwriting JView display method
	function display($tpl = null)
	{
		
		$model = $this->getModel('hbnextgames');
		$this->assignRef('model', $model);
		
		$teams = $model->getTeams();
		$this->assignRef('teams', $teams);
		
		$post = JRequest::get('post');
		//echo '=> view->$post <br><pre>"; print_r($post); echo "</pre>';
		$this->assignRef('post', $post);
		
//		$model->setDates($post['hbmanagerdates']);
		$dates = $model->getDates();
		//echo '=> view->$dates <br><pre>"; print_r($dates); echo "</pre>';
		$this->assignRef('dates', $dates);
		
		$games = $model->getGames();
		//echo '=> view->$games <br><pre>"; print_r($games); echo "</pre>';
		$this->assignRef('games', $games);
		
		JToolBarHelper::title(JTEXT::_('COM_HBMANAGER_NEXTGAMES_TITLE'),'hblogo');
		

		// get the stylesheet 
		// (with automatic lookup, possible template overrides, etc.)
		JHtml::stylesheet('com_hbmanager/admin.stylesheet.css', array(), true);
		
		// Display the view
		parent::display($tpl);
		
	}
}