<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
jimport('joomla.application.component.modeladmin');

class HBmanagerModelHBmanagerHVW extends JModel
{	
	private $updatedTabellen = array();
	private $updatedSpielplaene = array();
	
	
	function getMannschaften()
	{
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from('aaa_mannschaft');
		$query->order('reihenfolge');
		// Zur Kontrolle
		//echo "<a>ModelHB->query: </a><pre>"; echo $query; echo "</pre>";
		$db->setQuery($query);
		$mannschaften = $db->loadObjectList();
		return $mannschaften;
	}
	
	function getUpdateStatus()
	{
		$updated['tabellen'] = $this->updatedTabellen;
		$updated['spielplaene'] = $this->updatedSpielplaene;
		// Zur Kontrolle
		//echo "<pre>"; print_r($updated); echo "</pre>";
		return $updated;
	}
	
	function updateDB($kuerzel = 'kein')
	{
		$updatedMannschaften = array();
		
		if ($kuerzel == 'kein')
		{
			// no update 
		}
		else 
		{
			$db = $this->getDbo();
			$query = $db->getQuery(true);
			$query->select('*');
			$query->from('aaa_mannschaft');
			$query->where($db->quoteName('hvwLink').' IS NOT NULL');
			if ($kuerzel != 'alle')
			{
				// request only one team of DB
				$query->where($db->quoteName('kuerzel').' = '.$db->quote($kuerzel)); 
			}	
			$db->setQuery($query);
			$mannschaften = $db->loadObjectList();
			
			foreach ($mannschaften as $mannschaft)
			{
				$resultTabelle = self::updateDBTabelleTable($mannschaft);
				
				if ($resultTabelle == true)
				{
					$query = $db->getQuery(true);
					$query->update('aaa_mannschaft')->set($db->quoteName('updateTabelle').' = NOW()');
					$query->where($db->quoteName('kuerzel').' = '.$db->quote($mannschaft->kuerzel));
					$db->setQuery($query);
					try 
					{
						// Execute the query in Joomla 2.5.
						$result = $db->query();
					} catch (Exception $e) 
					{
						// catch any database errors.
					}
					$this->updatedTabellen[] = $mannschaft->kuerzel;
				}
				self::updateDBtabellenTable($mannschaft);

				$resultSpielplan = self::updateDBSpielplanTable($mannschaft);
				
				if ($resultSpielplan == true)
				{
					$query = $db->getQuery(true);
					$query->update('aaa_mannschaft')->set($db->quoteName('updateSpielplan').' = NOW()');
					$query->where($db->quoteName('kuerzel').' = '.$db->quote($mannschaft->kuerzel));
					$db->setQuery($query);
					//echo "<pre>"; echo $query; echo "</pre>";
					
					try 
					{
						// Execute the query in Joomla 2.5.
						$result = $db->query();
					} catch (Exception $e) 
					{
						// catch any database errors.
					}
					$this->updatedSpielplaene[] = $mannschaft->kuerzel;
				}
				
				self::updateDBspielTable($mannschaft);
			}
		}
		return;
	}
	
	//*******************************************************************
	// Tabelle
	
	protected function updateDBTabelleTable($mannschaft = 'keine')
	{
		$updated = false;
		if ($kuerzel != 'keine')
		{
			$tabelleArray = self::getTabelleArrayFromHVW($mannschaft->hvwLink);
			
			// Zur Kontrolle
			//echo "<pre>"; print_r($tabelleArray); echo "</pre>";
			
			$updated = self::updateTabelleInDB('hb_data_'.$mannschaft->kuerzel.'tabelle', $tabelleArray, $message = true);
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
	 * @return		array		text from HVW online (from ">Punkte</th></tr>" to "</TABLE></div>")
	 */
	
	protected function getTabelleArrayFromHVW($address)
	{
		// Gibt den Quelltext einer mit $address uebergebener url als String zurueck
		$source = file_get_contents($address);
	
		// String auf wesentlichen Teil begrenzen
		$start = strpos($source,">Punkte</th></tr>")+17;
		$end = strpos($source,"</tr></TABLE></div>",$start);
		$source = substr($source,$start,($end-$start));
	
		// Entfernen von unnoetigen Teilen
		$garbageClasses = array(' class="gasr"',' class="gac"',' class="gasl"');
		$source = str_replace($garbageClasses,"",$source);
	
		$garbage = array("\n","\t",'<td style="padding:0">:</td>','<tr class="rug">','<tr class="rge">','<b>','</b>','<td> </td>','<td><a href="http://www.im-dienste-des-spiels.de" target="_new" border="0"><img border="0" src="/icons/idds/whistle_black.gif" title="Unterst&uuml;tzer der Aktion im-dienste-des-spiels"></a></td>','<td>');
		$tabelleString = str_replace($garbage,"",$source);
	
		// String in zweidimensionales Array umwandeln
		$tabelleArray = explode("</tr>",$tabelleString);
		$i=0;
		while ($tabelleArray[$i]) 
		{
			$tabelleArray[$i] = explode("</td>",$tabelleArray[$i]);
			unset($tabelleArray[$i][10]);
			$i++;
		}
		return $tabelleArray;
	}


	protected function updateTabelleInDB($tableName, $dataArray) 
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
		) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;";
		// Zur Kontrolle
		//echo "<pre>"; echo $query; echo "</pre>";
		$db->setQuery($query);
		try {
			// Execute the query in Joomla 2.5.
			$result = $db->query();
		} catch (Exception $e) {
			// catch any database errors.
		}
		
		// delete existing data
		$db->truncateTable ($tableName);
		
		$query = $db->getQuery(true); // !important, true for every new query
	
		$query->insert($tableName);
		
		$query = "INSERT INTO ".$tableName;
		$query .= " (`Platz`, `Verein`, `Spiele`, `Siege`, `Unentschieden`, `Niederlagen`, `Plustore`, `Minustore`, `Pluspunkte`, `Minuspunkte`)";
		$query .= " VALUES \n";
		
		foreach ($dataArray as $data)
		{
			
			if ((int) $data[0] != 0) $game = '('.(int) $data[0].', ';  // Platz
			else $game = '(NULL, ';
			$game .= "'".$data[1]."', ";		//Verein
			$game .= (int) $data[2].", ";		//Spiele
			$game .= (int) $data[3].", ";		//Siege
			$game .= (int) $data[4].", ";		//Unentschieden
			$game .= (int) $data[5].", ";		//Niederlagen
			$game .= (int) $data[6].", ";		//Plustore
			$game .= (int) $data[7].", ";		//Minustore
			$game .= (int) $data[8].", ";		//Pluspunkte
			$game .= (int) $data[9]."), \n";		//Minuspunkte

			
			$query .= $game;
		}
		$query = rtrim($query, ", \n");
		
		
		// Zur Kontrolle
		// echo "<pre>"; print_r($query); echo "</pre>";
		
		$db->setQuery($query);
		try 
		{
			// Execute the query in Joomla 2.5.
			$result = $db->query();
		} 
		catch (Exception $e) 
		{
			// catch any database errors.
		}
		
		if ($result == true) return true;
		else return false;
	}
	
	//*******************************************************************
	// Spielplan
	
	protected function updateDBspielplanTable($mannschaft = 'keine')
	{
		$updated = false;
		if ($mannschaft != 'keine')
		{
			$spielplanArray = self::getSpielplanArrayFromHVW($mannschaft->hvwLink);
			
			// Zur Kontrolle
			//echo "<pre>"; print_r($spielplanArray); echo "</pre>";
			
			$updated = self::updateSpielplanInDB('hb_data_'.$mannschaft->kuerzel.'spielplan', $spielplanArray, $message = true);
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
	 * @return		Array		text from HVW online (from "<th align="center">Bem.</th>" to "</table>")
	 */
	
	protected function getSpielplanArrayFromHVW($address)
	{
		// Gibt den Quelltext einer mit $address uebergebener url als String zurueck
		$source = file_get_contents($address);
	
		// String auf wesentlichen Teil begrenzen
		$start = strpos($source,'<th align="center">Bem.</th>')+28;
		$end = strpos($source,'</table>',$start);
		$source = substr($source,$start,($end-$start));
	
		// Entfernen von unnoetigen Teilen
		$source = str_replace(array("</td>",", "),"|",$source);
		$source = str_replace(array("\t",'-|','',' |'),array('','','','|'),$source);
		$spielplanString = strip_tags($source);
	
		// String in zweidimensionales Array umwandeln
		$spielplanArray = explode("\n",$spielplanString);
		array_splice($spielplanArray, 0, 2);
		unset($spielplanArray[count($spielplanArray)-1]);
		$i=0;
		while ($spielplanArray[$i]) {
			$spielplanArray[$i] = explode("|",$spielplanArray[$i]);
			unset($spielplanArray[$i][12]);
			array_splice($spielplanArray[$i], 9, 1);
			$spielplanArray[$i][0] = str_replace(" ","",$spielplanArray[$i][0]);
			$spielplanArray[$i][3] = explode(".",$spielplanArray[$i][3]);
			$spielplanArray[$i][3] = (string) $spielplanArray[$i][3][2]."-".$spielplanArray[$i][3][1]."-".$spielplanArray[$i][3][0];
			$spielplanArray[$i][4] = str_replace("h",":00",$spielplanArray[$i][4]);
			$i++;
		}
	
		// Zur Kontrolle
		//echo "<pre>"; print_r($spielplanString); echo "</pre>";
		//echo "<pre>"; print_r($spielplanArray); echo "</pre>";
		
		return $spielplanArray;
	}
	
	
	protected function updateSpielplanInDB($tableName, $dataArray)
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
			) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;";
		// Zur Kontrolle
		//echo "<pre>"; echo $query; echo "</pre>";
		$db->setQuery($query);
		try {
			// Execute the query in Joomla 2.5.
			$result = $db->query();
		} catch (Exception $e) {
			// catch any database errors.
		}
		
		// delete existing data
		$db->truncateTable ($tableName);
	
		$query = $db->getQuery(true); // !important, true for every new query
	
		$query = "INSERT INTO ".$tableName;
		$query .= " (`Klasse`, `SpielNR`, `Tag`, `Datum`, `Zeit`, `Halle`, `Heim`, `Gast`, `ToreHeim`, `ToreGast`, `Bemerkung`)";
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
			if (preg_match('/(Sa|So|Mo|Di|Mi|Do|Fr)/',$data[2])) $game .= ",'".$data[2]."'";
			else $game .= ",NULL";
			
			// Datum YY-MM-DD
			if (preg_match('/[0-9][0-9]-[0-1][0-9]-[0-3][0-9]/',$data[3])) $game .= ",'".$data[3]."'";
			else $game .= ",NULL";
			
			// Zeit
			if (preg_match('/[0-2][0-9]:[0-5][0-9]:[0-5][0-9]/',$data[4])) $game .= ",'".$data[4]."'";
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
	
		
		// Zur Kontrolle
		//echo "<pre>"; print_r($query); echo "</pre>";
		
		$db->setQuery($query);
		try {
			// Execute the query in Joomla 2.5.
			$result = $db->query();
		} catch (Exception $e) {
			// catch any database errors.
		}
	
		if ($result == true) return true;
		else return false;
	}
	
	function updateDBspielTable($mannschaft = 'keine')
	{
		if ($mannschaft != 'keine')
		{
			$db = $this->getDbo();
			$query = $db->getQuery(true);
			$query->select('*');
			$query->from('hb_data_'.$mannschaft->kuerzel.'spielplan');
			$query->where($db->quoteName('Heim').' = '.$db->quote($mannschaft->name).' OR '.$db->quoteName('Gast').' = '.$db->quote($mannschaft->name));
			$db->setQuery($query);
			$spiele= $db->loadObjectList();
			
			// Zur Kontrolle
			//echo "<a>Model->query: </a><pre>"; echo $query; echo "</pre>";
			
			
			// INSERT ... ON DUPLICATE KEY UPDATE
			foreach ($spiele as $spiel)
			{
				//$query = $db->getQuery(true);
				
				$query = 'INSERT INTO aaa_spiel (spielIDhvw, kuerzel, hallenNummer, datum, uhrzeit, heim, gast, toreHeim, toreGast, bemerkung) VALUES '."\n";
				$query .= '('.$db->quote($spiel->SpielNR).', '.$db->quote($mannschaft->kuerzel).', '.$db->quote($spiel->Halle).', '.
						$db->quote($spiel->Datum).', '.$db->quote($spiel->Zeit).', '.
						$db->quote($spiel->Heim).', '.$db->quote($spiel->Gast).', ';
				if (!empty($spiel->ToreHeim)) $query .= $db->quote($spiel->ToreHeim);
				else $query .= 'NULL';
				$query .= ', ';
				if (!empty($spiel->ToreGast)) $query .= $db->quote($spiel->ToreGast);
				else $query .= 'NULL';
				$query .= ', ';
				$query .= $db->quote($spiel->Bemerkung).')';
				$query .= "\n".'ON DUPLICATE KEY UPDATE ';
				$query .= 'spielIDhvw = '.$db->quote($spiel->SpielNR).', kuerzel = '.$db->quote($mannschaft->kuerzel).
					', hallenNummer = '.$db->quote($spiel->Halle).', datum = '.$db->quote($spiel->Datum).', uhrzeit = '.$db->quote($spiel->Zeit).
					', heim = '.$db->quote($spiel->Heim).', gast = '.$db->quote($spiel->Gast);
				$query .= ', toreHeim = ';
				//if (!empty($spiel->ToreHeim) || '0' == $spiel->ToreHeim) $query .= $db->quote($spiel->ToreHeim);
				//else $query .= 'NULL';
				if ($spiel->ToreHeim === null) $query .= 'NULL';
				else $query .= $db->quote($spiel->ToreHeim);
				$query .= ', toreGast = ';
				//if (!empty($spiel->ToreGast) || '0' == $spiel->ToreGast) $query .= $db->quote($spiel->ToreGast);
				//else $query .= 'NULL';
				if ($spiel->ToreGast === null) $query .= 'NULL';
				else $query .= $db->quote($spiel->ToreGast);
				$query .= ', bemerkung = '.$db->quote($spiel->Bemerkung)."\n";
				
				// Zur Kontrolle
				//echo "<pre>"; echo $query; echo "</pre>";
				$db->setQuery($query);
				try {
					// Execute the query in Joomla 2.5.
					$result = $db->query();
				} catch (Exception $e) {
					// catch any database errors.
				}
				// display and convert to HTML when SQL error
				/*
				if (is_null($posts=$db->loadRowList()))
				{
					$jAp = JFactory::getApplication();
					$jAp->enqueueMessage(nl2br($db->getErrorMsg()."\n\n"),'error');
				}*/
			}
			
			
			/*
			// REPLACE
			$games = array();
			$query = 'REPLACE aaa_spiel (spielIDhvw, kuerzel, hallenNummer, datum, uhrzeit, heim, gast, toreHeim, toreGast, bemerkung) VALUES '."\n";
			foreach ($spiele as $spiel)
			{
				$query .= '('.$db->quote($spiel->SpielNR).', '.$db->quote($mannschaft->kuerzel).', '.$db->quote($spiel->Halle).', '.
						$db->quote($spiel->Datum).', '.$db->quote($spiel->Zeit).', '.
						$db->quote($spiel->Heim).', '.$db->quote($spiel->Gast).', ';
				if (!empty($spiel->ToreHeim)) $query .= $db->quote($spiel->ToreHeim);
				else $query .= 'NULL';
				$query .= ', ';
				if (!empty($spiel->ToreGast)) $query .= $db->quote($spiel->ToreGast);
				else $query .= 'NULL';
				$query .= ', ';
				$query .= $db->quote($spiel->Bemerkung).'), '."\n";
			
			}
			$query = trim($query, ", \n");
			
			
			// Zur Kontrolle
			echo "<pre>"; print_r($query); echo "</pre>";
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
	
	function updateDBtabellenTable($mannschaft = 'keine')
	{
		if ($mannschaft != 'keine')
		{
			$jAp = JFactory::getApplication();
			
			$db = $this->getDbo();
			$query = $db->getQuery(true);
			$query->select('*');
			$query->from('hb_data_'.$mannschaft->kuerzel.'tabelle');
			$db->setQuery($query);
			$tabelle= $db->loadObjectList();
			//echo "<a>Model->query: </a><pre>"; echo $query; echo "</pre>";
			
			$query = $db->getQuery(true);
			$query->select('Verein');
			$query->from('aaa_tabelle');
			$query->where($db->quoteName('kuerzel').' = '.$db->quote($mannschaft->kuerzel));
			$db->setQuery($query);
			$vereine= $db->loadObjectList();
			
			if (count($tabelle) != count($vereine))
			{
				$query = $db->getQuery(true);
				$query->delete('aaa_tabelle');
				$query->where($db->quoteName('kuerzel').' = '.$db->quote($mannschaft->kuerzel));
				$db->setQuery($query);
				$db->query();
				
				foreach ($tabelle as $zeile)
				{
					//$query = $db->getQuery(true);
					if (!empty($zeile->Platz)) $curPlatz = $zeile->Platz; // damit in leerer 'Platz'-Zelle mit Vorg?ngerwert benutzt wird (f?r direkten Zugriff)
					$torDifferenz = $zeile->Plustore - $zeile->Minustore;
					
					$query = $db->getQuery(true);
					$query->insert('aaa_tabelle');
					$query->set($db->quoteName('kuerzel').' = '.$db->quote($mannschaft->kuerzel).', '.
							$db->quoteName('platz').' = '.$db->quote($curPlatz).', '.$db->quoteName('verein').' = '.$db->quote($zeile->Verein).', '.
							$db->quoteName('spiele').' = '.$db->quote($zeile->Spiele).', '.$db->quoteName('siege').' = '.$db->quote($zeile->Siege).', '.
							$db->quoteName('unentschieden').' = '.$db->quote($zeile->Unentschieden).', '.$db->quoteName('niederlagen').' = '.$db->quote($zeile->Niederlagen).', '.
							$db->quoteName('plustore').' = '.$db->quote($zeile->Plustore).', '.$db->quoteName('minustore').' = '.$db->quote($zeile->Minustore).', '.
							$db->quoteName('torDifferenz').' = '.$db->quote($torDifferenz).', '.
							$db->quoteName('pluspunkte').' = '.$db->quote($zeile->Pluspunkte).', '.$db->quoteName('minuspunkte').' = '.$db->quote($zeile->Minuspunkte));
					$query->where($db->quoteName('kuerzel').' = '.$db->quote($mannschaft->kuerzel).' AND '.$db->quoteName('verein').' = '.$db->quote($zeile->Verein));
					// Zur Kontrolle
					//echo "<pre>"; echo $query; echo "</pre>";
					$db->setQuery($query);
					try {
						// Execute the query in Joomla 2.5.
						$result = $db->query();
					} catch (Exception $e)
					{
						// catch any database errors.
					}
					// display and convert to HTML when SQL error
					
					if ($db->getErrorMsg() != '')
					{
						$jAp->enqueueMessage(nl2br($db->getErrorMsg()."\n\n"),'error');
					}
				}
				
			}
				
			else 
			{	
				// UPDATE
				foreach ($tabelle as $zeile)
				{
					//$query = $db->getQuery(true);
					if (!empty($zeile->Platz)) $curPlatz = $zeile->Platz; // damit in leerer 'Platz'-Zelle mit Vorg?ngerwert benutzt wird (f?r direkten Zugriff)
					$torDifferenz = $zeile->Plustore - $zeile->Minustore;
					
					$query = $db->getQuery(true);
					$query->update('aaa_tabelle');
					$query->set($db->quoteName('kuerzel').' = '.$db->quote($mannschaft->kuerzel).', '.
							$db->quoteName('platz').' = '.$db->quote($curPlatz).', '.$db->quoteName('verein').' = '.$db->quote($zeile->Verein).', '.
							$db->quoteName('spiele').' = '.$db->quote($zeile->Spiele).', '.$db->quoteName('siege').' = '.$db->quote($zeile->Siege).', '.
							$db->quoteName('unentschieden').' = '.$db->quote($zeile->Unentschieden).', '.$db->quoteName('niederlagen').' = '.$db->quote($zeile->Niederlagen).', '.
							$db->quoteName('plustore').' = '.$db->quote($zeile->Plustore).', '.$db->quoteName('minustore').' = '.$db->quote($zeile->Minustore).', '.
							$db->quoteName('torDifferenz').' = '.$db->quote($torDifferenz).', '.
							$db->quoteName('pluspunkte').' = '.$db->quote($zeile->Pluspunkte).', '.$db->quoteName('minuspunkte').' = '.$db->quote($zeile->Minuspunkte));
					$query->where($db->quoteName('kuerzel').' = '.$db->quote($mannschaft->kuerzel).' AND '.$db->quoteName('verein').' = '.$db->quote($zeile->Verein));
					// Zur Kontrolle
					//echo "<pre>"; echo $query; echo "</pre>";
					$db->setQuery($query);
					try {
						// Execute the query in Joomla 2.5.
						$result = $db->query();
					} catch (Exception $e) 
					{
						// catch any database errors.
					}
					// display and convert to HTML when SQL error
					
					if ($db->getErrorMsg() != '')
					{
						$jAp->enqueueMessage(nl2br($db->getErrorMsg()."\n\n"),'error');
					}
					
				}
			}

		}
		return ;
	}

}