<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla view library
jimport('joomla.application.component.view');
 
/**
 * HTML View class for the HB Manager Component
 */
class HBmanagerViewHbDatabase extends JView
{
	// Overwriting JView display method
	function display($tpl = null)
	{
		$model = $this->getModel('hbdatabase');
		$this->assignRef('model', $model);
		$model->setDBtables('hb_%');
		
		$teams = $model->getTeams();
		$this->assignRef('teams', $teams);
		//echo __FILE__.'('.__LINE__.'):<pre>';print_r($teams);echo'</pre>';
		
		
		JToolBarHelper::title(JTEXT::_('COM_HBMANAGER_DATABASE_TITLE'),'hblogo');
		
		
		// get the stylesheet (with automatic lookup, 
		// possible template overrides, etc.)
		JHtml::stylesheet('com_hbmanager/admin.stylesheet.css', array(), true);
		
		// Display the view
		parent::display($tpl);
	}
}