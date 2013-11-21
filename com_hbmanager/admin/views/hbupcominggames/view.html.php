<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla view library
jimport('joomla.application.component.view');
 
/**
 * HTML View class for the HB Manager Component
 */
class HBmanagerViewHBupcomingGames extends JView
{
	// Overwriting JView display method
	function display($tpl = null)
	{
		
		$model = $this->getModel('hbupcominggames');
		$this->assignRef('model', $model);
		
		$mannschaften = $model->getMannschaften();
		$this->assignRef('mannschaften', $mannschaften);
		
		$post = JRequest::get('post');
		echo "<pre>"; print_r($post); echo "</pre>";
		$this->assignRef('post', $post);
		
		$model->setDates($post['hbmanagerdates']);
		$dates = $model->getDates();
		//echo "<pre>dates in view.html.php\n"; print_r($dates); echo "</pre>";
		$this->assignRef('dates', $dates);
		
		$games = $model->getGames();
		//echo "<pre>"; print_r($games); echo "</pre>";
		$this->assignRef('games', $games);
		
		JToolBarHelper::title('HB Manager - Kommende Spiele','hblogo');
		

		// get the stylesheet (with automatic lookup, possible template overrides, etc.)
		//JHtml::stylesheet('admin.stylesheet.css','media/com_hbhvwmanager/css/');
		JHtml::stylesheet('com_hbmanager/admin.stylesheet.css', array(), true);
		
		// Display the view
		parent::display($tpl);
		
	}
}