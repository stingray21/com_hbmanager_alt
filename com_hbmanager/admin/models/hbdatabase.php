<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
jimport('joomla.application.component.modeladmin');

class hbmanagerModelHbdatabase extends JModel
{	
	private $teams = array();
	private $dbTables = array();
	
	function __construct() 
	{
		parent::__construct();
		
		//setlocale(LC_TIME, "de_DE");
		
		self::setDbTables();
		//echo __FILE__.'('.__LINE__.'):<pre>';print_r($this->dbTables);echo'</pre>';
	}
	
	function getTeams()
	{
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from('hb_mannschaft');
		$db->setQuery($query);
		$teams = $db->loadObjectList();
		
		$dbTables = $this->dbTables;
		foreach ($teams as $team)
		{
			$keyStandings = array_search('hb_data_'.strtolower($team->kuerzel).
					'_tabelle', $dbTables);
			if(false !== $keyStandings)
			{
				$team->tabelleDB = 'hb_data_'.strtolower($team->kuerzel).'_tabelle';
				unset($dbTables[$keyStandings]);
			}
			
			$keySchedule = array_search('hb_data_'.strtolower($team->kuerzel).
					'_spielplan', $dbTables);
			if(false !== $keySchedule)
			{
				$team->spielplanDB = 'hb_data_'.strtolower($team->kuerzel).'_spielplan';
				unset($dbTables[$keySchedule]);
			}
		}
		//echo __FILE__.'('.__LINE__.'):<pre>';print_r($dbTables);echo'</pre>';
		//echo __FILE__.'('.__LINE__.'):<pre>';print_r($teams);echo'</pre>';
		
		$this->dbTables = $dbTables;
		return $this->teams = $teams;
	}
	
	function getDbTables()
	{
		//echo __FILE__.'('.__LINE__.'):<pre>';print_r($this->dbTables);echo'</pre>';
		return $this->dbTables;
	}
	
	function createDbTables($tableType)
	{
		
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('kuerzel');
		$query->from('hb_mannschaft');
		$query->where('`hvwLink` IS NOT NULL');
		$db->setQuery($query);
		$teams = $db->loadObjectList();
		
		switch ($tableType)
		{
			case 'tabelle':
				$query_tmpl = "CREATE TABLE IF NOT EXISTS \n
					`hb_data_%dbTableKey%_tabelle` \n (
					`ID` int(2) unsigned NOT NULL AUTO_INCREMENT, \n
					`Platz` tinyint(2) DEFAULT NULL, \n
					`Verein` varchar(60) CHARACTER SET utf8 
						COLLATE utf8_unicode_ci DEFAULT NULL, \n
					`Spiele` tinyint(2) DEFAULT NULL, \n
					`Siege` tinyint(2) DEFAULT NULL, \n
					`Unentschieden` tinyint(2) DEFAULT NULL, \n
					`Niederlagen` tinyint(2) DEFAULT NULL, \n
					`Plustore` mediumint(4) DEFAULT NULL, \n
					`Minustore` mediumint(4) DEFAULT NULL, \n
					`Pluspunkte` tinyint(2) DEFAULT NULL, \n
					`Minuspunkte` tinyint(2) DEFAULT NULL, \n
					PRIMARY KEY (`ID`) \n
					) ENGINE=InnoDB  
						DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;\n";
				break;
			case 'spielplan':
				$query_tmpl = "CREATE TABLE IF NOT EXISTS \n 
					`hb_data_%dbTableKey%_spielplan` \n (
					`ID` int(2) unsigned NOT NULL AUTO_INCREMENT, \n
					`Klasse` varchar(12) CHARACTER SET utf8 
						COLLATE utf8_unicode_ci DEFAULT NULL, \n
					`SpielNR` mediumint(3) DEFAULT NULL, \n
					`Tag` varchar(2) CHARACTER SET utf8 
						COLLATE utf8_unicode_ci DEFAULT NULL, \n
					`Datum` date DEFAULT NULL, \n
					`Zeit` time DEFAULT NULL, \n
					`Halle` mediumint(4) DEFAULT NULL, \n
					`Heim` varchar(60) CHARACTER SET utf8 
						COLLATE utf8_unicode_ci DEFAULT NULL, \n
					`Gast` varchar(60) CHARACTER SET utf8 
						COLLATE utf8_unicode_ci DEFAULT NULL, \n
					`ToreHeim` int(3) DEFAULT NULL, \n
					`ToreGast` int(3) DEFAULT NULL, \n
					`Bemerkung` varchar(255) CHARACTER SET utf8 
						COLLATE utf8_unicode_ci DEFAULT NULL, \n
					PRIMARY KEY (`ID`) ) \n
					ENGINE=InnoDB  
						DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;";
				break;
			
			default:
				$query = '';
				break;
		}
		
		foreach ($teams as $team)
		{
			$query = str_replace('%dbTableKey%' , $team->kuerzel , $query_tmpl);
			$db->setQuery($query);
			try {
				// Execute the query in Joomla 2.5.
				$result = $db->query();
			} catch (Exception $e) {
				// catch any database errors.
			}
		}
	}
	
	
	// FIX: Variable fuer verschiedene Links/Datenbanken machen 
	function getDbPmaTableLink($table)
	{
		$tableLink = "http://handball.tsv-geislingen.de/pMA3/index.php?db=d013879f&table=".$table.
					"&target=sql.php&token=d4931325391138397ab1d4da7344fb3e#PMAURL:db=d013879f&table=".$table.
					"&target=sql.php&token=d4931325391138397ab1d4da7344fb3e";
		return $tableLink;
	}
	
	function setDbTables($type = 'hb_%')
	{
		$db = $this->getDbo();
		$query = "SHOW TABLES LIKE '".$type."'";
		$db->setQuery($query);
		$dbTables = $db->loadRowList();
		
		foreach ($dbTables as $key => $value)
		{
			$dbTables[$key] = $value[0];
		}
		
		$this->dbTables = $dbTables;
		//echo '=> model->$dbTables<br><pre>"; print_r($dbTables); echo "</pre>';
	}
	
}