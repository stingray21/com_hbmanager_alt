<?php
// No direct access to this file
defined('_JEXEC') or die;
 
/**
 * HB Manager component helper.
 */

abstract class HBmanagerHelper
{
        /**
         * Configure the Linkbar.
         */
        public static function addSubmenu($submenu) 
        {
                JSubMenuHelper::addEntry('Mannschaften', 'index.php?option=com_hbmanager&task=manageMannschaften',
		                         $submenu == 'hbmanagerteams');
				JSubMenuHelper::addEntry('HVW Daten', 'index.php?option=com_hbmanager&task=zeigeHVWDBTables',
		                         $submenu == 'hbmanagerhvw');
				JSubMenuHelper::addEntry('Kalender updaten', 'index.php?option=com_hbmanager&task=updateCal',
		                         $submenu == 'hbmanagercal');
				JSubMenuHelper::addEntry('Letzte Spiele', 'index.php?option=com_hbmanager&task=updateRecentGames',
		                         $submenu == 'hbrecentgames');
				JSubMenuHelper::addEntry('Kommende Spiele', 'index.php?option=com_hbmanager&task=updateUpcomingGames',
		                         $submenu == 'hbupcominggames');
				JSubMenuHelper::addEntry('Amtsblatt Bericht', 'index.php?option=com_hbmanager&task=showAmtsblatt',
		                         $submenu == 'hbamtsblatt');
				JSubMenuHelper::addEntry('DB Tabellen verwalten', 'index.php?option=com_hbmanager&task=manageDBtables',
		                         $submenu == 'hbmanagerdb');
                
//                 // set some global property
//                 $document = JFactory::getDocument();
//                 $document->addStyleDeclaration('.icon-48-helloworld ' .
//                                                '{background-image: url(../media/com_helloworld/images/tux-48x48.png);}');
//                 if ($submenu == 'categories') 
//                 {
//                         $document->setTitle(JText::_('COM_HELLOWORLD_ADMINISTRATION_CATEGORIES'));
//                 }
                
        }
        
}