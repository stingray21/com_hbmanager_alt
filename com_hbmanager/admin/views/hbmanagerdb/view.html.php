<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla view library
jimport('joomla.application.component.view');
 
/**
 * HTML View class for the HB Manager Component
 */
class HBmanagerViewHBmanagerDB extends JView
{
	// Overwriting JView display method
	function display($tpl = null)
	{
		$model = $this->getModel();
		
		$mannschaften = $model->getMannschaften();
		$this->assignRef('mannschaften', $mannschaften);
		$model->setDBtables('aaa_%');
		
		JToolBarHelper::title('HB Manager - DB Tabellen','hblogo');
		
		
		// get the stylesheet (with automatic lookup, possible template overrides, etc.)
		//JHtml::stylesheet('admin.stylesheet.css','media/com_hbhvwmanager/css/');
		JHtml::stylesheet('com_hbmanager/admin.stylesheet.css', array(), true);
		
		// Display the view
		parent::display($tpl);
	}
}