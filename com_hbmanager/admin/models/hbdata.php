<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
jimport('joomla.application.component.modeladmin');

class hbmanagerModelHbdata extends JModel
{	
	private $updatedRankings = array();
	private $updatedSchedules = array();
	
	function __construct() 
	{
		parent::__construct();
		
		setlocale(LC_TIME, "de_DE");
		
		// set maximum execution time limit
		set_time_limit(90);
		
		// $db = $this->getDbo();
		// $db->setQuery("SET lc_time_names = 'de_DE'");
		// try{
		//	// Execute the query in Joomla 2.5.
		//	// $result = $db->query();
		// }
		// catch (Exception $e) {
		//	// catch any database errors.
		// }
	}
	
	function getTeams()
	{
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from('hb_mannschaft');
		$query->order('reihenfolge');
		//echo '=> model->$query <br><pre>"; print_r($query); echo "</pre>';
		$db->setQuery($query);
		$teams = $db->loadObjectList();
		return $teams;
	}
	
	function getUpdateStatus()
	{
		$updated['rankings'] = $this->updatedRankings;
		$updated['schedules'] = $this->updatedSchedules;
		//echo '=> model->$updated <br><pre>"; print_r($updated); echo "</pre>';
		return $updated;
	}
	
	function updateDb($key = 'none')
	{
		$updatedTeams = array();
		
		if ($key == 'none')
		{
			// no update 
		}
		else 
		{
			$db = $this->getDbo();
			$query = $db->getQuery(true);
			$query->select('*');
			$query->from('hb_mannschaft');
			$query->where($db->qn('hvwLink').' IS NOT NULL');
			if ($key != 'all')
			{
				// request only one team of DB
				$query->where($db->qn('kuerzel').' = '.$db->q($key)); 
			}
			$db->setQuery($query);
			$teams = $db->loadObjectList();
			
			foreach ($teams as $team)
			{
				// update rankings
				$resultRankings = self::updateDbTableRankings($team);
				
				if ($resultRankings == true) 
				{
					$query = $db->getQuery(true);
					$query->update('hb_mannschaft');
					$query->set($db->qn('updateTabelle').' = NOW()');
					$query->where($db->qn('kuerzel').' = '.
								$db->q($team->kuerzel));
					//echo '=> model->$query <br><pre>".$query."</pre>';
					$db->setQuery($query);
					try {
						// Execute the query in Joomla 2.5.
						$result = $db->query();
					} catch (Exception $e) {
						// catch any database errors.
					}
					$updatedTeams['rankings'][] = $team->kuerzel;
				}
				self::updateDbTableAllRankings($team);
				
				// update schedules
				$resultSchedule = self::updateDbTableSchedule($team);
				
				if ($resultSchedule == true)
				{
					$query = $db->getQuery(true);
					$query->update('hb_mannschaft');
					$query->set($db->qn('updateSpielplan').' = NOW()');
					$query->where($db->qn('kuerzel').' = '.
								$db->q($team->kuerzel));
					$db->setQuery($query);
					//echo '=> model->$query <br><pre>".$query."</pre>';
					try 					{
						// Execute the query in Joomla 2.5.
						$result = $db->query();
					} catch (Exception $e) 
					{
						// catch any database errors.
					}
					$updatedTeams['schedules'][] = $team->kuerzel;
				}
				self::updateDbTableAllSchedules($team);
			}
		}
		return;
	}
	
	//*******************************************************************
	// rankings methods
	//*******************************************************************
	
	protected function updateDbTableRankings($team = 'none')
	{
		$updated = false;
		if ($team != 'none')
		{
			$rankingsArray = self::getRankingsArrayFromHvw($team->hvwLink);
			//echo '=> model->rankingsArray <br><pre>';print_r($rankingsArray);echo'</pre>';
			$updated = self::updateRankingsInDB(
					'hb_data_'.$team->kuerzel.'_tabelle', 
					$rankingsArray, $message = true);
		}
		return $updated;
	}
	
	//=================================================
	//Returns Array with data from HVW online
	//(von ">Punkte</th></tr>" bis "</TABLE></div>")
	//von der als $address uebergebenen url
	/**
	 * Method to get data from HVW online
	 * 
	 * @param 		string		link to website
	 * 
	 * @return		array		text from HVW online 
	 *							(from ">Punkte</th></tr>" to "</TABLE></div>")
	 */
	
	protected function getRankingsArrayFromHVW($address)
	{
		// returns sourcecode of a website with the address $address as string
		$source = file_get_contents($address);
	
		// shortens strings to relevant part
		$start = strpos($source,">Punkte</th></tr>")+17;
		$end = strpos($source,"</tr></TABLE></div>",$start);
		$source = substr($source,$start,($end-$start));
	
		// gets rid of unnecessary stuff
		$garbageClasses = array(' class="gasr"',' class="gac"',' class="gasl"');
		$source = str_replace($garbageClasses,"",$source);
	
		$garbage = array("\n","\t",'<td style="padding:0">:</td>','<tr class="rug">','<tr class="rge">','<b>','</b>','<td> </td>','<td><a href="http://www.im-dienste-des-spiels.de" target="_new" border="0"><img border="0" src="/icons/idds/whistle_black.gif" title="Unterst&uuml;tzer der Aktion im-dienste-des-spiels"></a></td>','<td>');
		$rankingsString = str_replace($garbage,"",$source);
	
		// converts string to two dimensional array
		$rankingsArray = explode("</tr>",$rankingsString);
		foreach ($rankingsArray as $key => $value) 
		{
			$rankingsArray[$key] = explode("</td>",$value);
			unset($rankingsArray[$key][10]);
		}
		return $rankingsArray;
	}


	protected function updateRankingsInDB($tableName, $dataArray) 
	{
		$db = $this->getDbo();
		
		$query = "CREATE TABLE IF NOT EXISTS ".$db->qn($tableName)." (
			`ID` int(2) unsigned NOT NULL AUTO_INCREMENT,
			`Platz` tinyint(2) DEFAULT NULL,
			`Verein` varchar(60) COLLATE utf8_unicode_ci DEFAULT NULL,
			`Spiele` tinyint(2) DEFAULT NULL,
			`Siege` tinyint(2) DEFAULT NULL,
			`Unentschieden` tinyint(2) DEFAULT NULL,
			`Niederlagen` tinyint(2) DEFAULT NULL,
			`Plustore` mediumint(4) DEFAULT NULL,
			`Minustore` mediumint(4) DEFAULT NULL,
			`Pluspunkte` tinyint(2) DEFAULT NULL,
			`Minuspunkte` tinyint(2) DEFAULT NULL,
			PRIMARY KEY (`ID`)
		) ENGINE=InnoDB  
		DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;";
		//echo '=> model->$query <br><pre>'.$query.'</pre>';
		$db->setQuery($query);
		try {
			// Execute the query in Joomla 2.5.
			$result = $db->query();
		} catch (Exception $e) {
			// catch any database errors.
		}
		
		// delete existing data
		$db->truncateTable ($tableName);
		
		$query = $db->getQuery(true); 
		$query = "INSERT INTO ".$tableName;
		$query .= " (`Platz`, `Verein`, `Spiele`, `Siege`, `Unentschieden`, ".
				"`Niederlagen`, `Plustore`, `Minustore`, `Pluspunkte`, ".
				"`Minuspunkte`)";
		$query .= " VALUES \n";
		
		foreach ($dataArray as $data)
		{
			$row = '(';
			if (!empty($data[0])) $row .= (int) $data[0].', ';	// Platz
			else $row .= 'NULL, ';
			$row .= "'".$data[1]."', ";			//Verein
			$row .= (int) $data[2].", ";		//Spiele
			$row .= (int) $data[3].", ";		//Siege
			$row .= (int) $data[4].", ";		//Unentschieden
			$row .= (int) $data[5].", ";		//Niederlagen
			$row .= (int) $data[6].", ";		//Plustore
			$row .= (int) $data[7].", ";		//Minustore
			$row .= (int) $data[8].", ";		//Pluspunkte
			$row .= (int) $data[9]."), \n";		//Minuspunkte
			
			$query .= $row;
		}
		$query = rtrim($query, ", \n");
		//echo '=> model->$query <br><pre>'.$query.'</pre>';
		$db->setQuery($query);
		try 		{
			// Execute the query in Joomla 2.5.
			$result = $db->query();
		} catch (Exception $e) 
		{
			// catch any database errors.
		}
		
		return $result;
	}
	
	//*******************************************************************
	// schedule methods
	//*******************************************************************
	
	protected function updateDbTableSchedule($team = 'none')
	{
		$updated = false;
		if ($team != 'none')
		{
			$scheduleArray = self::getScheduleArrayFromHVW($team->hvwLink);
			/*echo '=> model->$scheduleArray <br><pre>'; 
			print_r($scheduleArray);echo "</pre>";//*/
			
			$updated = self::updateScheduleInDB(
						'hb_data_'.$team->kuerzel.'_spielplan', 
						$scheduleArray, $message = true );
		}
		return $updated;
	}
	
	//=================================================
	//Returns Array with data from HVW online
	//(from "<th align="center">Bem.</th>" to "</table>")
	//von der als $address uebergebenen url
	/**
	 * Method to get data from HVW online
	 *
	 * @param 		string		link to website
	 *
	 * @return		Array		text from HVW online 
	 *							(from "<th align="center">Bem.</th>" 
	 *							 to "</table>")
	 */
	
	protected function getScheduleArrayFromHVW($address)
	{
		// returns sourcecode of a website with the address $address as string
		$source = file_get_contents($address);
	
		// shortens strings to relevant part
		$start = strpos($source,'<th align="center">Bem.</th>')+28;
		$end = strpos($source,'</table>',$start);
		$source = substr($source,$start,($end-$start));
	
		// gets rid of unnecessary stuff
		$source = str_replace(array("</td>",", "), "|", $source);
		$source = str_replace(' |', '|', $source);
		$source = str_replace(array("\t",'-|'), '', $source);
		$scheduleString = strip_tags($source);
		/*echo '=> model->$scheduleString <br><pre>'; 
			print_r($scheduleString);echo "</pre>";//*/
	
		// converts string to two dimensional array
		$scheduleArray = explode("\n",$scheduleString);
		array_splice($scheduleArray, 0, 2);
		array_pop($scheduleArray);
		/*echo '=> model->$scheduleArray <br><pre>'; 
			print_r($scheduleArray);echo "</pre>";//*/
		
		foreach ($scheduleArray as $key => $value) {
			$scheduleArray[$key] = explode("|",$scheduleArray[$key]);
			unset($scheduleArray[$key][12]);
			array_splice($scheduleArray[$key], 9, 1);
			$scheduleArray[$key][0] 
					= str_replace(" ","",$scheduleArray[$key][0]);
			$scheduleArray[$key][3] 
					= preg_replace('/(\d{2}).(\d{2}).(\d{2})/',
								'20$3-$2-$1', $scheduleArray[$key][3]);
			$scheduleArray[$key][4] 
					= str_replace("h",":00",$scheduleArray[$key][4]);
		}
		/*echo '=> model->$scheduleArray <br><pre>'; 
			print_r($scheduleArray);echo "</pre>";//*/
		
		return $scheduleArray;
	}
	
	
	protected function updateScheduleInDB($tableName, $dataArray)
	{
		$db = $this->getDbo();
		
		$query = "CREATE TABLE IF NOT EXISTS ".$db->qn($tableName)." (
			  `ID` int(2) unsigned NOT NULL AUTO_INCREMENT,
			  `Klasse` varchar(12) COLLATE utf8_unicode_ci DEFAULT NULL,
			  `SpielNR` mediumint(3) DEFAULT NULL,
			  `Tag` varchar(2) COLLATE utf8_unicode_ci DEFAULT NULL,
			  `Datum` date DEFAULT NULL,
			  `Zeit` time DEFAULT NULL,
			  `Halle` mediumint(4) DEFAULT NULL,
			  `Heim` varchar(60) COLLATE utf8_unicode_ci DEFAULT NULL,
			  `Gast` varchar(60) COLLATE utf8_unicode_ci DEFAULT NULL,
			  `ToreHeim` int(3) DEFAULT NULL,
			  `ToreGast` int(3) DEFAULT NULL,
			  `Bemerkung` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
			  PRIMARY KEY (`ID`)
			) ENGINE=InnoDB  
			DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;";
		//echo '=> model->$query <br><pre>".$query."</pre>';
		$db->setQuery($query);
		try {
			// Execute the query in Joomla 2.5.
			$result = $db->query();
		} catch (Exception $e) {
			// catch any database errors.
		}
		
		// delete existing data
		$db->truncateTable ($tableName);
	
		$query = $db->getQuery(true);
		$query = "INSERT INTO ".$tableName;
		$query .= " (`Klasse`, `SpielNR`, `Tag`, `Datum`, `Zeit`, `Halle`,
			`Heim`, `Gast`, `ToreHeim`, `ToreGast`, `Bemerkung`)";
		$query .= " VALUES \n";
	
		foreach ($dataArray as $data)
		{
			// Klasse	
			$game = "('".$data[0]."'";
			// SpielNR
			$data[1] = (int) $data[1];
			if ($data[1] != 0) $game .= ", '".$data[1]."'";
			else $game .= ",NULL";
			
			// Tag
			if (preg_match('/(Sa|So|Mo|Di|Mi|Do|Fr)/',$data[2])) {
				$game .= ",'".$data[2]."'";
			}
			else $game .= ",NULL";
			
			// Datum YY-MM-DD
			if (preg_match('/\d{2}-\d{2}-\d{2}/',$data[3])) {
				$game .= ",'".$data[3]."'";
			}
			else $game .= ",NULL";
			
			// Zeit
			if (preg_match('/\d{2}:\d{2}:\d{2}/',$data[4])) {
				$game .= ",'".$data[4]."'";
			}
			else $game .= ",NULL";
			
			// Halle
			if ((int) $data[5] != 0) $game .= ",'".$data[5]."'";
			else $game .= ",NULL";
			
			// Heim
			$game .= ",'".addslashes($data[6])."'";
			
			// Gast
			$game .= ",'".addslashes($data[7])."'";
			
			// ToreHeim
			if ($data[8] != "") $game .= ",".(int)$data[8]."";
			else $game .= ",NULL";
			
			// ToreGast
			if ($data[9] != "") $game .= ", ".(int)$data[9]."";
			else $game .= ",NULL";
			
			// Bemerkung
			$game .= ",'".$data[10]."'), \n";	
				
			$query .= $game;
		}
		$query = rtrim($query, ", \n");
	
		//echo '=> model->$query <br><pre>".$query."</pre>';
		$db->setQuery($query);
		try {
			// Execute the query in Joomla 2.5.
			$result = $db->query();
		} catch (Exception $e) {
			// catch any database errors.
		}
	
		return $result;
	}
	
	protected function updateDbTableAllSchedules($team = 'none')
	{
		if ($team != 'none')
		{
			$db = $this->getDbo();
			$query = $db->getQuery(true);
			$query->select('*');
			$query->from('hb_data_'.$team->kuerzel.'_spielplan');
			$query->where($db->qn('Heim').' = '.$db->q($team->name).' OR '.
					$db->qn('Gast').' = '.$db->q($team->name));
			$db->setQuery($query);
			$games= $db->loadObjectList();
			
			//echo '=> model->$query <br><pre>'.$query.'</pre>';
			//echo '=> model->$games <br><pre>'; print_r($games);echo '</pre>';
		
			// VARIANT 1: INSERT ... ON DUPLICATE KEY UPDATE
			foreach ($games as $game)
			{
				$query = 'INSERT INTO hb_spiel (spielIDhvw, kuerzel,'. 
							'hallenNummer, datum, uhrzeit, heim, gast,'. 
							'toreHeim, toreGast, bemerkung) VALUES '."\n";
				$query .= '('.$db->q($game->SpielNR).', '.
						$db->q($team->kuerzel).', '.
						$db->q($game->Halle).', '.
						$db->q($game->Datum).', '.
						$db->q($game->Zeit).', '.
						$db->q($game->Heim).', '.
						$db->q($game->Gast).', ';
				if ($game->ToreHeim !== NULL) $query .= $db->q($game->ToreHeim);
				else $query .= 'NULL';
				$query .= ', ';
				if ($game->ToreGast !== NULL) $query .= $db->q($game->ToreGast);
				else $query .= 'NULL';
				$query .= ', ';
				$query .= $db->q($game->Bemerkung).')';
				$query .= "\n".'ON DUPLICATE KEY UPDATE ';
				$query .= 'spielIDhvw = '.$db->q($game->SpielNR).
						', kuerzel = '.$db->q($team->kuerzel).
						', hallenNummer = '.$db->q($game->Halle).
						', datum = '.$db->q($game->Datum).
						', uhrzeit = '.$db->q($game->Zeit).
						', heim = '.$db->q($game->Heim).
						', gast = '.$db->q($game->Gast);
				$query .= ', toreHeim = ';
				//if (!empty($game->ToreHeim) || '0' == $game->ToreHeim) {
				//	$query .= $db->q($game->ToreHeim);
				//}
				//else $query .= 'NULL';
				if ($game->ToreHeim === NULL) $query .= 'NULL';
				else $query .= $db->q($game->ToreHeim);
				$query .= ', toreGast = ';
				//if (!empty($game->ToreGast) || '0' == $game->ToreGast) {
				//	$query .= $db->q($game->ToreGast);
				//}
				//else $query .= 'NULL';
				if ($game->ToreGast === NULL) $query .= 'NULL';
				else $query .= $db->q($game->ToreGast);
				$query .= ', bemerkung = '.$db->q($game->Bemerkung)."\n";
				
				//echo '=> model->$query <br><pre>'.$query.'</pre>';
				$db->setQuery($query);
				try {
					// Execute the query in Joomla 2.5.
					$result = $db->query();
				} catch (Exception $e) {
					// catch any database errors.
				}
				/*// display and convert to HTML when SQL error
				if (is_null($posts=$db->loadRowList())){
					$jAp = JFactory::getApplication();
					$jAp->enqueueMessage(nl2br($db->getErrorMsg()."\n\n"),
						'error');
				}*/
			}
			
			/*
			// VARIANT 1: REPLACE
			$games = array();
			$query = 'REPLACE hb_spiel (spielIDhvw, kuerzel, hallenNummer, 
				datum, uhrzeit, heim, gast, toreHeim, toreGast, bemerkung) 
				VALUES '."\n";
			foreach ($games as $game)
			{
				$query .= '('.$db->q($game->SpielNR).', '.
						$db->q($mannschaft->kuerzel).', '.
						$db->q($game->Halle).', '.
						$db->q($game->Datum).', '.
						$db->q($game->Zeit).', '.
						$db->q($game->Heim).', '.
						$db->q($game->Gast).', ';
				if ($game->ToreHeim !== NULL) $query .= $db->q($game->ToreHeim);
				else $query .= 'NULL';
				$query .= ', ';
				if ($game->ToreGast !== NULL) $query .= $db->q($game->ToreGast);
				else $query .= 'NULL';
				$query .= ', ';
				$query .= $db->q($game->Bemerkung).'), '."\n";
			}
			$query = trim($query, ", \n");
			//echo '=> model->$query <br><pre>".$query."</pre>';
			
			$db->setQuery($query);
			try {
				// Execute the query in Joomla 2.5.
				//$result = $db->query();
			} catch (Exception $e) {
				// catch any database errors.
			}
			*/
			
		}
		return ;
	}
	
	protected function updateDbTableAllRankings($team = 'none')
	{
		if ($team != 'none')
		{
			//$jAp = JFactory::getApplication();
			
			// get the rankings data of team
			$db = $this->getDbo();
			$query = $db->getQuery(true);
			$query->select('*');
			$query->from('hb_data_'.$team->kuerzel.'_tabelle');
			$db->setQuery($query);
			$teamRankings= $db->loadObjectList();
			// echo '=> model->$query <br><pre>'.$query.'</pre>';
			// echo '=> model->$teamRankings <br><pre>'; 
				// print_r($teamRankings);echo "</pre>";
			
			$query = $db->getQuery(true);
			$query->select('Verein');
			$query->from('hb_tabelle');
			$query->where($db->qn('kuerzel').' = '.$db->q($team->kuerzel));
			$db->setQuery($query);
			$clubs= $db->loadObjectList();
			// echo '=> model->$query <br><pre>'.$query.'</pre>';
			// echo '=> model->$clubs <br><pre>'; print_r($clubs);echo '</pre>';
			
			$update = false;
			if (count($teamRankings) == count($clubs)) $update = true;
				
			if ($update) 
			{
				$query = $db->getQuery(true);
				$query->delete('hb_tabelle');
				$query->where($db->qn('kuerzel').' = '.$db->q($team->kuerzel));
				$db->setQuery($query);
				$db->query();
			}
			
			foreach ($teamRankings as $row)
			{
				// use ranking from previous row in case of empty ranking
				// (for direct access of row)
				if (!empty($row->Platz)) $curRanking = $row->Platz; 

				$diff = $row->Plustore - $row->Minustore;

				$query = $db->getQuery(true);
				if ($update) $query->update('hb_tabelle');
				else $query->insert('hb_tabelle');
				
				$query->set(
						$db->qn('kuerzel').' = '.
							$db->q($team->kuerzel).', '.
						$db->qn('platz').' = '.
							$db->q($curRanking).', '.
						$db->qn('verein').' = '.
							$db->q($row->Verein).', '.
						$db->qn('spiele').' = '.
							$db->q($row->Spiele).', '.
						$db->qn('siege').' = '.
							$db->q($row->Siege).', '.
						$db->qn('unentschieden').' = '.
							$db->q($row->Unentschieden).', '.
						$db->qn('niederlagen').' = '.
							$db->q($row->Niederlagen).', '.
						$db->qn('plustore').' = '.
							$db->q($row->Plustore).', '.
						$db->qn('minustore').' = '.
							$db->q($row->Minustore).', '.
						$db->qn('torDifferenz').' = '.
							$db->q($diff).', '.
						$db->qn('pluspunkte').' = '.
							$db->q($row->Pluspunkte).', '.
						$db->qn('minuspunkte').' = '.
							$db->q($row->Minuspunkte));
				if ($update) 
				{
					$query->where(
							$db->qn('kuerzel').' = '.
								$db->q($team->kuerzel).' AND '.
							$db->qn('verein').' = '.
								$db->q($row->Verein));
				}
				//echo '=> model->$query <br><pre>".$query."</pre>';
				$db->setQuery($query);
				try {
					// Execute the query in Joomla 2.5.
					$result = $db->query();
				} catch (Exception $e) {
					// catch any database errors.
				}

				// display and convert to HTML when SQL error
				if ($db->getErrorMsg() != '')
				{
					$jAp->enqueueMessage(nl2br($db->getErrorMsg()."\n\n"),
							'error');
				}
			}

		}
		return ;
	}

}