<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla view library
jimport('joomla.application.component.view');
 
/**
 * HTML View class for the HB Manager Component
 */
class HBmanagerViewHbprevgames extends JView
{
	// Overwriting JView display method
	function display($tpl = null)
	{
		$model = $this->getModel('hbprevgames');
		//echo '=> view->post<br><pre>'; print_r($this); echo '</pre>';
		$this->assignRef('model', $model);
		
		$teams = $model->getTeams();
		$this->assignRef('teams', $teams);
		
		$post = JRequest::get('post');
		//echo '=> view->post<br><pre>'; print_r($post); echo '</pre>';
		$this->assignRef('post', $post);
		
		$dates = $model->getDates();
		//echo '=> view->dates<br><pre>'; print_r($dates); echo '</pre>';
		$this->assignRef('dates', $dates);
		
		$games = $model->getGames();
		//echo '=> view->games<br><pre>"; print_r($games); echo "</pre>';
		$this->assignRef('games', $games);
		
		JToolBarHelper::title(JText::_('COM_HBMANAGER_PREVGAMES_TITLE'),'hblogo');
		
		// get the stylesheet (with automatic lookup, 
		// possible template overrides, etc.)
		// JHtml::stylesheet('admin.stylesheet.css','media/com_hbmanager/css/');
		 JHtml::stylesheet('com_hbmanager/admin.stylesheet.css', array(), true);
		
		// Display the view
		parent::display($tpl);
	}
}