<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla view library
jimport('joomla.application.component.view');
 
/**
 * HTML View class for the HB HVW Manager Component
 */
class HBmanagerViewHbcalendar extends JView
{
	// Overwriting JView display method
	function display($tpl = null)
	{
		$model = $this->getModel('hbcalendar');
		
		$teams = $model->getTeams();
		$this->assignRef('teams', $teams);
		
		JToolBarHelper::title(JTEXT::_('COM_HBMANAGER_CALENDAR_TITLE'),'hblogo');
		
		
		// get the stylesheet (with automatic lookup, 
		// possible template overrides, etc.)
		JHtml::stylesheet('com_hbmanager/admin.stylesheet.css', array(), true);
		
		// Display the view
		parent::display($tpl);
	}
}