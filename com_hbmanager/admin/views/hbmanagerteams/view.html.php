<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla view library
jimport('joomla.application.component.view');
 
/**
 * HTML View class for the HB Manager Component
 */
class HBmanagerViewHBmanagerTeams extends JView
{
	// Overwriting JView display method
	function display($tpl = null)
	{
		$document = JFactory::getDocument();
		$document->addScript('https://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js');
		$document->addScript(JURI::Root().'administrator/components/com_hbmanager/hbteams.js');
		//$document->addScript('http://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.js');
		
		$model = $this->getModel('hbmanagerteams');
		$this->assignRef('model', $model);
		
		$staffeln = $model->getStaffeln();
		$this->assignRef('staffeln', $staffeln);
		
				
		//$staffel = $model->getStaffelArrayFromHVW();
		//echo "<pre>"; print_r($staffel); echo "</pre>";
		//$this->assignRef('staffel', $staffel);
		//$model->updateStaffelnInDB($staffel);
		
		$post = JRequest::get('post');
		//echo "<pre>"; print_r($post); echo "</pre>";
		$this->assignRef('post', $post);
		
		//echo $this->getLayout();
		if (strcmp($this->getLayout(), 'default') == 0) {
			$mannschaften = $model->getMannschaften();
		}
		else {
			$mannschaften = $model->getMannschaften4Del();
		}
		$this->assignRef('mannschaften', $mannschaften);
		//echo "<pre>"; print_r($mannschaften); echo "</pre>";
		
		JToolBarHelper::title('HB Manager - Mannschaften','hblogo');
		
		
		// get the stylesheet (with automatic lookup, possible template overrides, etc.)
		//JHtml::stylesheet('admin.stylesheet.css','media/com_hbhvwmanager/css/');
		JHtml::stylesheet('com_hbmanager/admin.stylesheet.css', array(), true);
		
		// Display the view
		parent::display($tpl);
	}
}