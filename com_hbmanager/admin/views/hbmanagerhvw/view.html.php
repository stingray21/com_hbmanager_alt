<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla view library
jimport('joomla.application.component.view');
 
/**
 * HTML View class for the HB Manager Component
 */
class HBmanagerViewHBmanagerHVW extends JView
{
	// Overwriting JView display method
	function display($tpl = null)
	{
		
		$model = $this->getModel('HBmanagerHVW');
		
		$mannschaften = $model->getMannschaften();
		$this->assignRef('mannschaften', $mannschaften);
		// Zur Kontrolle
		//echo "<a>View->mannschaften: </a><pre>"; print_r($mannschaften); echo "</pre>";
		$updated = $model->getUpdateStatus();
		$this->assignRef('updated', $updated);
		
		JToolBarHelper::title('HVW Manager','hblogo');
		
// 		JSubMenuHelper::addEntry('Mannschaften', 'index.php?option=com_hbmanager&task=updateMannschaften');
// 		JSubMenuHelper::addEntry('HVW Daten', 'index.php?option=com_hbmanager&task=zeigeHVWDBTables',true);
// 		JSubMenuHelper::addEntry('Kalender updaten', 'index.php?option=com_hbmanager&task=updateCal');
// 		JSubMenuHelper::addEntry('Letzte Spiele', 'index.php?option=com_hbmanager&task=updateRecentGames');
// 		JSubMenuHelper::addEntry('Kommende Spiele', 'index.php?option=com_hbmanager&task=upcomingGames');
// 		JSubMenuHelper::addEntry('Amtsblatt Bericht', 'index.php?option=com_hbmanager&task=showAmtsblatt');
// 		JSubMenuHelper::addEntry('DB Tabellen verwalten', 'index.php?option=com_hbmanager&task=manageDBtables');
		
		
		// get the stylesheet (with automatic lookup, possible template overrides, etc.)
		//JHtml::stylesheet('admin.stylesheet.css','media/com_hbmanager/css/');
		JHtml::stylesheet('com_hbmanager/admin.stylesheet.css', array(), true);
		
		// Display the view
		parent::display($tpl);
	}
}