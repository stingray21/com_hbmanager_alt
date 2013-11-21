<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla view library
jimport('joomla.application.component.view');
 
/**
 * HTML View class for the HB HVW Manager Component
 */
class HBmanagerViewHBmanagerCal extends JView
{
	// Overwriting JView display method
	function display($tpl = null)
	{
		$model = $this->getModel('HBmanagerCal');
		
		$mannschaften = $model->getMannschaften();
		$this->assignRef('mannschaften', $mannschaften);
		
		JToolBarHelper::title('Kalender Manager für Spiele','hblogo');
		
		
		// get the stylesheet (with automatic lookup, possible template overrides, etc.)
		//JHtml::stylesheet('admin.stylesheet.css','media/com_hbhvwmanager/css/');
		JHtml::stylesheet('com_hbmanager/admin.stylesheet.css', array(), true);
		
		// Display the view
		parent::display($tpl);
	}
}