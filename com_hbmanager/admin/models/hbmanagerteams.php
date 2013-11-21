<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
jimport('joomla.application.component.modeladmin');

class HBmanagerModelHBmanagerTeams extends JModel
{	
	
	function getMannschaften()
	{
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from('aaa_mannschaft');
		$query->order($db->qn('reihenfolge'));
		// Zur Kontrolle
		//echo "<a>ModelHB->query: </a><pre>"; echo $query; echo "</pre>";
		$db->setQuery($query);
		$mannschaften = $db->loadObjectList();
		return $mannschaften;
	}
	
	function getMannschaften4Del()
	{
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('`kuerzel`, `mannschaft`, `name`, `nameKurz`, `ligaKuerzel`, `liga`, `geschlecht`, `jugend`, `hvwLink`');
		$query->from('aaa_mannschaft');
		$query->order($db->qn('reihenfolge'));
		// Zur Kontrolle
		//echo "<a>ModelHB->query: </a><pre>"; echo $query; echo "</pre>";
		$db->setQuery($query);
		$mannschaften = $db->loadObjectList();
		return $mannschaften;
	}
	
	function getStaffeln()
	{
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from('aaa_staffel');
		// Zur Kontrolle
		//echo "<a>ModelHB->query: </a><pre>"; echo $query; echo "</pre>";
		$db->setQuery($query);
		$staffeln = $db->loadObjectList();
		return $staffeln;
	}
	
	protected function getKuerzelArray()
	{
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('kuerzel');
		$query->from('aaa_mannschaft');
		$query->order('kuerzel ASC');
		//$query->order('reihenfolge ASC');
		// Zur Kontrolle
		//echo "<a>ModelHB->query: </a><pre>"; echo $query; echo "</pre>";
		$db->setQuery($query);
		$results = $db->loadAssocList();
		
		foreach ($results as $value) {
			$element = preg_replace ( "/(^.{1,6})([1-9])$/" , "$1&&$2" , $value['kuerzel']);
			$element = preg_replace ( "/(^.{1}3)&&(2)$/" , "$1$2" , $element);
			list($element, $number)  = explode('&&', $element);
			
			$kuerzelArray[strtolower($element)][] = $number;
		}
		
		foreach ($kuerzelArray as $key => $value) {
			$kuerzelArray[$key] = max($value);
			if ($kuerzelArray[$key] == '') $kuerzelArray[$key] = 1;
		}
					
		//echo "<a>kuerzelArray: </a><pre>"; print_r($kuerzelArray); echo "</pre>";
		return $kuerzelArray;
	}
	
	function getStaffelArrayFromHVW($address = 'http://www.hvw-online.org/index.php?id=39&orgID=11')
	{
		// Gibt den Quelltext einer mit $address uebergebener url als String zurueck
		$source = file_get_contents($address);
	
		// String auf wesentlichen Teil begrenzen
		$start = strpos($source,'<th align="center">Bem.</th>')+28;
		$end = strpos($source,'</table>',$start);
		$source = substr($source,$start,($end-$start));
		
		
		$pattern = "/<td class=\"gal\"><a href=\"(?P<staffelLink>\?A=g_class&id=[0-9]{2}&orgID=[0-9]{2}&score=[0-9]{4,7})\">(?P<staffel>.{3,15})<\/a>/";
		preg_match_all($pattern, $source, $staffelArray, PREG_SET_ORDER);
		//echo "<pre>"; print_r($staffelArray); echo "</pre>";
		
		foreach ($staffelArray as $key => $value) {
			unset($staffelArray[$key][0]);
			unset($staffelArray[$key][1]);
			unset($staffelArray[$key][2]);
			preg_match("/^(M|F|w|m|g)/", $value[staffel], $geschlecht);
			switch ($geschlecht[0]) {
				case 'M':
					$staffelArray[$key]['geschlecht'] = 'm';
					$staffelArray[$key]['jugend'] = 0;
					break;
				case 'F':
					$staffelArray[$key]['geschlecht'] = 'w';
					$staffelArray[$key]['jugend'] = 0;
					break;
				default:
					$staffelArray[$key]['geschlecht'] = $geschlecht[0];
					$staffelArray[$key]['jugend'] = 1;
					break;
			}
			
			if (!preg_match("/Pok/", $value['staffel'])) {
				$source = file_get_contents('http://www.hvw-online.org/Spielbetrieb/index.php'.$value['staffelLink'].'&all=1');
				
				$pattern = "/<h1>Neckar-Zollern<br>(.*) - (.*) (20[0-9]{2}\/20[0-9]{2})<\/h1>/";
				preg_match($pattern, $source, $matches);
				$staffelArray[$key]['staffelName'] = $matches[1];
				$staffelArray[$key]['saison'] = $matches[3];
				
				$start = strpos($source,'<td class="gac"><b>1</b></td>');
				$end = stripos($source,'</tr></TABLE>',$start);
				$sourceRanking = substr($source,$start,($end-$start));
				
				$pattern = "/<td class=\"gac\">(<b>[0-9]{1,2}<\/b>|&#160;)<\/td>\s+".
				"<td>.+<\/td>\s+".
				"<td>(.+)<\/td>/";
				preg_match_all($pattern, $sourceRanking, $rankingTeams, PREG_SET_ORDER);
				unset($sourceRanking);
				foreach ($rankingTeams as $innerKey => $innerValue) {
					$rankingTeams[$innerKey] = $rankingTeams[$innerKey][2];
				}
				sort($rankingTeams);
				$staffelArray[$key]['rankingTeams'] = $rankingTeams;
				
				// String auf wesentlichen Teil begrenzen
				$start = strpos($source,'<th align="center">Bem.</th>')+28;
				$end = strpos($source,'</table>',$start);
				$source = substr($source,$start,($end-$start));
				$pattern = "/<\/td><td class=\"gal\">(.+)<\/td><td>-<\/td>/";
				preg_match_all($pattern, $source, $scheduleTeams, PREG_SET_ORDER);
				unset($source);
				foreach ($scheduleTeams as $innerKey => $innerValue) {
					$scheduleTeams[$innerKey] = $scheduleTeams[$innerKey][1];
				}
				$scheduleTeams = array_unique($scheduleTeams);
				sort($scheduleTeams);
				$staffelArray[$key]['scheduleTeams'] = $scheduleTeams;
			}
			else {
				$deleteElements[] = $key;
			}
		}
		
		foreach ($deleteElements as $element) {
			unset($staffelArray[$element]);
		}
		
		//echo "<pre>"; print_r($staffelArray); echo "</pre>";
		return $staffelArray;	
	}
	
	
	function updateStaffelnInDB($staffelArray)
	{
		$db = $this->getDbo();
		
		// ??? SWITCH TO: INSERT ... UPDATE ON DUPLICATE ?
		// delete existing data
		$db->truncateTable ('aaa_staffel');
	
		$query = $db->getQuery(true); // !important, true for every new query
	
		$query = "INSERT INTO aaa_staffel";
		$query .= " (`staffel`, `staffelName`, `staffelLink`, `geschlecht`, `jugend`, `saison`, `rankingTeams`, `scheduleTeams`)";
		$query .= " VALUES \n";
	
		foreach ($staffelArray as $data)
		{
			// 
			$values = "('".$data['staffel']."'";
			// 
			$values .= ",'".$data['staffelName']."'";
			//
			$values .= ",'".$data['staffelLink']."'";
			//
			$values .= ",'".$data['geschlecht']."'";
			//
			$values .= ",'".$data['jugend']."'";
			//
			$values .= ",'".$data['saison']."'";
			//
			$values .= ",'".implode('&&',$data['rankingTeams'])."'";
			//
			$values .= ",'".implode('&&',$data['scheduleTeams'])."'), \n";
	
			$query .= $values;
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
	
		return $result;
	}
	
	function addNewTeams2Db($newTeams) {
		$kuerzelArray = self::getKuerzelArray();
		
		$db = $this->getDbo();
		
		$query = $db->getQuery(true); // !important, true for every new query
		
		$query = "INSERT INTO aaa_mannschaft";
		$query .= " (`kuerzel`, `mannschaft`, `name`, `nameKurz`, `ligaKuerzel`, `liga`, `geschlecht`, `jugend`, `hvwLink`)";
		$query .= " VALUES \n";
		
		foreach ($newTeams as $data)
		{
			
			if ($data['includeMannschaft'] == 1) {
				
				// kuerzel
				if ($data['jugend']) {
					$kuerzel = preg_replace ( "/(^(m|w|g)J[A-E])-[A-Z]{2,3}.*/" , "$1" , $data['staffel']);
				}
				else {
					if (preg_match ( "/(^(M|F)-[A-Z]{2,3})/", $data['staffel'])) {
						$kuerzel = preg_replace ( "/(^(M|F))-[A-Z]{2,3}.*/" , "$1" , $data['staffel']);
					}
					else {
						$kuerzel = $data['staffel'];
					}
				}
				$key = $kuerzel;
				if (!empty($kuerzelArray[strtolower($key)])) {
					$kuerzel .= ++$kuerzelArray[strtolower($key)];
				}
				else {
					$kuerzelArray[strtolower($key)] = 1;
					$kuerzel = preg_replace ( "/^((M|F))$/" , "\${1}1" , $kuerzel);
				}
				$values = "('".$kuerzel."'";
				// mannschaft
				$liga_A = preg_replace('/(((männ|weib)liche|gemischte) Jugend|Männer|Frauen) .*/', "$1", $data['staffelName']);
				$liga_B = preg_replace('/.*Jugend ([A-E]).*/', "$1", $data['staffelName']);
				$liga_A = preg_replace('/(Jugend)/', $liga_B."-$1", $liga_A);
				preg_match('/ [1-9]$/', $data['rankingName'], $liga_C);
				$values .= ",'".$liga_A.$liga_C[0]."'";
				// name (Tabelle)
				$values .= ",'".$data['rankingName']."'";
				// name (Spielplan)
				$values .= ",'".$data['scheduleName']."'";
				// ligaKuerzel
				$values .= ",'".$data['staffel']."'";
				// liga
				$liga = preg_replace("/(((männ|weib)liche|gemischte) Jugend|Männer|Frauen) ([A-E] )?/", "", $data['staffelName']);
				$values .= ",'".$liga."'";
				// geschlecht
				$values .= ",'".$data['geschlecht']."'";
				// jugend 
				if ($data['jugend']) $values .= ",'".$data['jugend']."'";
				else $values .= ",0";
				// hvwLink
				$values .= ",'http://www.hvw-online.org/".$data['staffelLink']."&all=1'";
				
				$values .= "), \n";				
				$query .= $values;
			}
                    
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
		
		return $result;
	}
	
	function deleteMannschaften($teams)
	{
		//echo "<pre>"; print_r($teams); echo "</pre>";
		
		$db = $this->getDbo();
		
		foreach ($teams as $team) {
			if ($team['deleteTeam'] == 1) {
				$where[] = $db->q($team['kuerzel']);
			}	
		}
		$where = implode(', ', $where);
		$where = $db->qn('kuerzel') . ' IN (' . $where . ')';
				 
		$query = $db->getQuery(true);
		$query->delete('aaa_mannschaft');
		$query->where($where);
		// Zur Kontrolle
		//echo "<a>ModelHB->query: </a><pre>"; echo $query; echo "</pre>";
		$db->setQuery($query);
		try {
			// Execute the query in Joomla 2.5.
			$result = $db->query();
		} catch (Exception $e) {
			// catch any database errors.
		}
		
		return $result;
	}
	
	function updateMannschaften($teams) 
	{
		$db = $this->getDbo();
		
		$query = "INSERT INTO aaa_mannschaft";
		$query .= " (`reihenfolge`, `kuerzel`, `mannschaft`, `name`, `nameKurz`, ".
					"`ligaKuerzel`, `liga`, `geschlecht`, `jugend`, `hvwLink`)";
		$query .= " VALUES \n";
	
		foreach ($teams as $data)
		{
			// 
			$values = "('".$data['reihenfolge']."'";
			// 
			$values .= ",'".$data['kuerzel']."'";
			//
			$values .= ",'".$data['mannschaft']."'";
			//
			$values .= ",'".$data['name']."'";
			//
			$values .= ",'".$data['nameKurz']."'";
			//
			$values .= ",'".$data['ligaKuerzel']."'";
			//
			$values .= ",'".$data['liga']."'";
			//
			$values .= ",'".$data['geschlecht']."'";
			//
			$values .= ",'".$data['jugend']."'";
			//
			if (!empty($data['hvwLink'])) {
				$values .= ",'".'http://www.hvw-online.org/'.$data['hvwLink']."'";
			}
			else $values .= ", NULL";
			$values .= "), \n";
			$query .= $values;
		}
		$query = rtrim($query, ", \n");
		
		$query .= "\nON DUPLICATE KEY UPDATE ".
				"`reihenfolge` = VALUES(`reihenfolge`),\n".
				"`kuerzel` = VALUES(`kuerzel`),\n".
				"`mannschaft` = VALUES(`mannschaft`),\n".
				"`name` = VALUES(`name`),\n".
				"`nameKurz` = VALUES(`nameKurz`),\n".
				"`ligaKuerzel` = VALUES(`ligaKuerzel`),\n".
				"`liga` = VALUES(`liga`),\n".
				"`geschlecht` = VALUES(`geschlecht`),\n".
				"`jugend` = VALUES(`jugend`),\n".
				"`hvwLink` = VALUES(`hvwLink`),\n".
				"`updateTabelle` = VALUES(`updateTabelle`),\n".
				"`updateSpielplan` = VALUES(`updateSpielplan`)";
		
		
		// Zur Kontrolle
		//echo "<pre>"; print_r($query); echo "</pre>";
	
		$db->setQuery($query);
		try {
			// Execute the query in Joomla 2.5.
			$result = $db->query();
		} catch (Exception $e) {
			// catch any database errors.
		}
	
		return $result;
	}
			

}