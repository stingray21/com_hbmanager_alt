<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
jimport('joomla.application.component.modeladmin');

class HBmanagerModelHBmanagerDB extends JModel
{	
	private $mannschaften = array();
	private $DBtables = array();
	
	function getMannschaften()
	{
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from('aaa_mannschaft');
		$db->setQuery($query);
		$this->mannschaften = $db->loadObjectList();
		
		$this->setDBtables();
		foreach ($this->mannschaften as $mannschaft)
		{
			if(false !== $key = array_search('hb_data_'.$mannschaft->kuerzel.'tabelle', $this->DBtables))
			{
				$mannschaft->tabelleDB = 'hb_data_'.$mannschaft->kuerzel.'tabelle';
				unset($this->DBtables[$key]);
			}
			if(false !== $key = array_search('hb_data_'.$mannschaft->kuerzel.'spielplan', $this->DBtables))
			{
				$mannschaft->spielplanDB = 'hb_data_'.$mannschaft->kuerzel.'spielplan';
				unset($this->DBtables[$key]);
			}
		}
		//echo "<pre><a>Mannschaften: </a>"; print_r($this->mannschaften); echo "</pre>";
		//echo "<pre><a>DB Tables: </a>"; print_r($this->DBtables); echo "</pre>";

		return $this->mannschaften;
	}
	
	function getDBtables()
	{
		return $this->DBtables;
	}
	
	function createDBtables($table)
	{
		
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('kuerzel');
		$query->from('aaa_mannschaft');
		$query->where('`hvwLink` IS NOT NULL');
		$db->setQuery($query);
		$mannschaften = $db->loadObjectList();
		
		switch ($table)
		{
			case 'tabelle':
				$query_tmpl = "CREATE TABLE IF NOT EXISTS `hb_data___DBtableKuerzel__tabelle` (
					`ID` int(2) unsigned NOT NULL AUTO_INCREMENT,
					`Platz` tinyint(2) DEFAULT NULL,
					`Verein` varchar(60) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
					`Spiele` tinyint(2) DEFAULT NULL,
					`Siege` tinyint(2) DEFAULT NULL,
					`Unentschieden` tinyint(2) DEFAULT NULL,
					`Niederlagen` tinyint(2) DEFAULT NULL,
					`Plustore` mediumint(4) DEFAULT NULL,
					`Minustore` mediumint(4) DEFAULT NULL,
					`Pluspunkte` tinyint(2) DEFAULT NULL,
					`Minuspunkte` tinyint(2) DEFAULT NULL,
					PRIMARY KEY (`ID`)
					) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;";
				break;
			case 'spielplan':
				$query_tmpl = "CREATE TABLE IF NOT EXISTS `hb_data___DBtableKuerzel__spielplan` (
				  `ID` int(2) unsigned NOT NULL AUTO_INCREMENT,
				  `Klasse` varchar(12) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
				  `SpielNR` mediumint(3) DEFAULT NULL,
				  `Tag` varchar(2) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
				  `Datum` date DEFAULT NULL,
				  `Zeit` time DEFAULT NULL,
				  `Halle` mediumint(4) DEFAULT NULL,
				  `Heim` varchar(60) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
				  `Gast` varchar(60) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
				  `ToreHeim` int(3) DEFAULT NULL,
				  `ToreGast` int(3) DEFAULT NULL,
				  `Bemerkung` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
				  PRIMARY KEY (`ID`)
				) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;";
				break;
			
			default:
				$query = '';
				break;
		}
		
		foreach ($mannschaften as $mannschaft)
		{
			$query = str_replace ('__DBtableKuerzel__' , $mannschaft->kuerzel , $query_tmpl);
			$db->setQuery($query);
			try {
				// Execute the query in Joomla 2.5.
				$result = $db->query();
			} catch (Exception $e) {
				// catch any database errors.
			}
		}
	}
	
	
	// ??? Variable f�r verschiedene Links/Datenbanken machen 
	function getDBtableLink($table)
	{
		$tableLink = "http://handball.tsv-geislingen.de/pMA3/index.php?db=d013879f&table=".$table.
					"&target=sql.php&token=d4931325391138397ab1d4da7344fb3e#PMAURL:db=d013879f&table=".$table.
					"&target=sql.php&token=d4931325391138397ab1d4da7344fb3e";
		return $tableLink;
	}
	
	function setDBtables($type = 'hb_%')
	{
		$db = $this->getDbo();
		$query = "SHOW TABLES LIKE '".$type."'";
		$db->setQuery($query);
		$DBtables = $db->loadRowList();
		
		foreach ($DBtables as $table)
		{
			$this->DBtables[] = $table[0];
		}
		//echo "<pre>"; print_r($this->DBtables); echo "</pre>";
		
	}
	
}