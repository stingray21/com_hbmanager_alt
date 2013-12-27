<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla view library
jimport('joomla.application.component.view');
 
/**
 * HTML View class for the HB Manager Component
 */
class HbmanagerViewHbteams extends JView
{
	// Overwriting JView display method
	function display($tpl = null)
	{
		$document = JFactory::getDocument();
		$document->addScript('https://ajax.googleapis.com/ajax/libs/jquery/'.
								'1.10.2/jquery.min.js');
		//$document->addScript(JURI::Root().'/media/com_hbmanager/'.
		//					'js/hbteams.js');
		JHTML::script('hbteams.js', 'media/com_hbmanager/js/');
		//$document->addScript('http://ajax.aspnetcdn.com/ajax/'.
		//'jquery.validate/1.11.1/jquery.validate.js');
		
		$model = $this->getModel('hbteams');
		$this->assignRef('model', $model);
		
		$leagues = $model->getLeagues();
		$this->assignRef('leagues', $leagues);
		//echo '=> view->leagues<br><pre>'; print_r($leagues); echo '</pre>';
		
		$post = JRequest::get('post');
		//echo '=> view->post<br><pre>'; print_r($post); echo '</pre>';
		$this->assignRef('post', $post);
		
		//echo $this->getLayout();
		$option = null;
		if ($this->getLayout() === 'deleteteams') {
			$option = 'deleteTeams';
		}
		$teams = $model->getTeams($option);
		$this->assignRef('teams', $teams);
		// echo '=> view->teams<br><pre>'; print_r($teams); echo '</pre>';
		
		JToolBarHelper::title(JText::_('COM_HBMANAGER_TEAMS_TITLE'),'hblogo');
		
		// get the stylesheet (with automatic lookup, possible template overrides, etc.)
		//JHtml::stylesheet('admin.stylesheet.css','media/com_hbhvwmanager/css/');
		JHtml::stylesheet('com_hbmanager/admin.stylesheet.css', array(), true);
		
		// Display the view
		parent::display($tpl);
	}
}