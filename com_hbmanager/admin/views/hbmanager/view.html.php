<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla view library
jimport('joomla.application.component.view');
 
/**
 * HTML View class for the HB Manager Component
 */
class HBmanagerViewHBmanager extends JView
{
	// Overwriting JView display method
	function display($tpl = null)
	{
		$model = $this->getModel('HBmanager');
		$this->assignRef('model', $model);
		
		$mannschaften = $model->getMannschaften();
		$this->assignRef('mannschaften', $mannschaften);
		// Zur Kontrolle
		//echo "<a>View->mannschaften: </a><pre>"; print_r($mannschaften); echo "</pre>";
		
		JToolBarHelper::title('HB Manager','hblogo');
		
		
		
		// get the stylesheet (with automatic lookup, possible template overrides, etc.)
		//JHtml::stylesheet('admin.stylesheet.css','media/com_hbmanager/css/');
		JHtml::stylesheet('com_hbmanager/admin.stylesheet.css', array(), true);
		
		// Display the view
		parent::display($tpl);
	}
}