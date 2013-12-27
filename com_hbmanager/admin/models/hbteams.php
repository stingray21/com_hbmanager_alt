<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
jimport('joomla.application.component.modeladmin');

class hbmanagerModelHbteams extends JModel
{	
	function __construct() 
	{
		parent::__construct();
		
		// setlocale(LC_TIME, "de_DE");
		
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
	
	function getTeams($option = null)
	{
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		if ($option === 'deleteTeams') {
			$query->select('`kuerzel`, `mannschaft`, `name`, `nameKurz`, 
			`ligaKuerzel`, `liga`, `geschlecht`, `jugend`, `hvwLink`');
		}
		else {
			$query->select('*');
		}
		$query->from('hb_mannschaft');
		$query->order('ISNULL('.$db->qn('reihenfolge').') ASC');
		//echo '=> model->$query <br><pre>".$query."</pre>';
		$db->setQuery($query);
		return $teams = $db->loadObjectList();
	}
	
	function getLeagues()
	{
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from('hb_staffel');
		//echo '=> model->$query <br><pre>".$query."</pre>';
		$db->setQuery($query);
		return $leagues = $db->loadObjectList();
	}
	
	protected function getTeamkeyArray()
	{
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('kuerzel');
		$query->from('hb_mannschaft');
		$query->order('kuerzel ASC');
		//echo '=> model->$query <br><pre>".$query."</pre>';
		$db->setQuery($query);
		$results = $db->loadAssocList();
		
		$teamkeyArray = array();
		foreach ($results as $value) {
			$element = preg_replace ( "/^(M|F)(32)/" , "$1AH" , 
					$value['kuerzel']);
			$element = preg_replace ( "/(^.{1,6})([1-9])?$/" , "$1&&$2" , 
					$element);
			$element = preg_replace ( "/^(M|F)(AH)/" , "$1".'32' , 
					$element);
			//echo __FILE__.'('.__LINE__.'):<pre>';print_r($element);echo'</pre>';
			list($element, $number)  = explode('&&', $element);
			
			$teamkeyArray[strtolower($element)][] = $number;
		}
		
		foreach ($teamkeyArray as $key => $value) {
			$teamkeyArray[$key] = max($value);
			if ($teamkeyArray[$key] == '') $teamkeyArray[$key] = 1;
		}
		// echo '=> model->$teamkeyArray <br><pre>'; 
		// print_r($teamkeyArray); echo '</pre>';
		return $teamkeyArray;
	}
	
	function getLeagueArrayFromHVW($address)
	{
		// set maximum execution time limit
		set_time_limit(90);
		
		// returns sourcecode of a website with the address $address as string
		$source = file_get_contents($address);
		
		// shortens strings to relevant part
		$start = strpos($source,'<th align="center">Bem.</th>')+28;
		$end = strpos($source,'</table>',$start);
		$source = substr($source,$start,($end-$start));
		//echo '=> model->$source <br><pre>'.$source.'</pre>';

		$pattern = "/<td class=\"gal\"><a href=\"(?P<staffelLink>\?A=g_class&".
			"id=[0-9]{2}&orgID=[0-9]{2}&score=[0-9]{4,7})\">".
			"(?P<staffel>.{3,15})<\/a>/";
		preg_match_all($pattern, $source, $leagueArray, PREG_SET_ORDER);
		//$leagueArray = array_slice($leagueArray, 4, 2);
		//echo '=> model->$leagueArray <br><pre>';
		//print_r($leagueArray);echo '</pre>';
		
		
		foreach ($leagueArray as $key => $value) {
			$leagueArray[$key] = array_slice($leagueArray[$key], 3);  
			preg_match("/^(M|F|w|m|g)/", $value['staffel'], $sex);
			switch ($sex[0]) {
				case 'M':
					$leagueArray[$key]['geschlecht'] = 'm';
					$leagueArray[$key]['jugend'] = 0;
					break;
				case 'F':
					$leagueArray[$key]['geschlecht'] = 'w';
					$leagueArray[$key]['jugend'] = 0;
					break;
				default:
					$leagueArray[$key]['geschlecht'] = $sex[0];
					$leagueArray[$key]['jugend'] = 1;
					break;
			}
			
			if (!preg_match("/Pok/", $value['staffel'])) {
				$leagueArray[$key]['staffelLink'] = $value['staffelLink'];
				
				$link = 'http://www.hvw-online.org/Spielbetrieb/index.php'.
						$value['staffelLink'].'&all=1';
				$source = file_get_contents($link);
				//echo '=> model->$source <br><pre>'.$source.'</pre>';
		
				$pattern = "/<h1>Neckar-Zollern<br>(.*) - (.*)". 
							"(20[0-9]{2}\/20[0-9]{2})<\/h1>/";
				preg_match($pattern, $source, $matches);
				//echo '=> model->$matches <br><pre>';
				//print_r($matches);echo '</pre>';
				$leagueArray[$key]['staffelName'] = $matches[1];
				$leagueArray[$key]['saison'] = $matches[3];
				
				$start = strpos($source,'<td class="gac"><b>1</b></td>');
				$end = stripos($source,'</tr></TABLE>',$start);
				$sourceRanking = substr($source,$start,($end-$start));
				
				$pattern = "/<td class=\"gac\">(<b>[0-9]{1,2}<\/b>|&#160;)".
							"<\/td>\s+".
							"<td>.+<\/td>\s+".
							"<td>(.+)<\/td>/";
				preg_match_all($pattern, $sourceRanking, $rankingTeams, 
						PREG_SET_ORDER);
				unset($sourceRanking);
				foreach ($rankingTeams as $innerKey => $innerValue) {
					$rankingTeams[$innerKey] = $rankingTeams[$innerKey][2];
				}
				sort($rankingTeams);
				$leagueArray[$key]['rankingTeams'] = $rankingTeams;
				
				$start = strpos($source,'<th align="center">Bem.</th>')+28;
				$end = strpos($source,'</table>',$start);
				$source = substr($source,$start,($end-$start));
				$pattern = "/<\/td><td class=\"gal\">(.+)<\/td><td>-<\/td>/";
				preg_match_all($pattern, $source, $scheduleTeams, 
						PREG_SET_ORDER);
				unset($source);
				foreach ($scheduleTeams as $innerKey => $innerValue) {
					$scheduleTeams[$innerKey] = $scheduleTeams[$innerKey][1];
				}
				$scheduleTeams = array_unique($scheduleTeams);
				sort($scheduleTeams);
				$leagueArray[$key]['scheduleTeams'] = $scheduleTeams;
			}
			else {
				$deleteElements[] = $key;
			}
		}
		
		if (isset($deleteElements)){
			foreach ($deleteElements as $element) {
				unset($leagueArray[$element]);
			}
		}
		
		//echo '=> model->$leagueArray <br><pre>';print_r($leagueArray); echo'</pre>';
		return $leagueArray;	
	}
	
	
	function updateLeaguesInDB($leagueArray)
	{
		$db = $this->getDbo();
		// delete existing data
		$db->truncateTable ('hb_staffel');
	
		$query = $db->getQuery(true); 
		$query = "INSERT INTO hb_staffel";
		$query .= " (`staffel`, `staffelName`, `staffelLink`, `geschlecht`, 
			`jugend`, `saison`, `rankingTeams`, `scheduleTeams`)";
		$query .= " VALUES \n";
	
		foreach ($leagueArray as $data)
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
	
	function addNewTeams($newTeams) {
		$teamkeyArray = self::getTeamkeyArray();
				
		$db = $this->getDbo();
		
		$query = $db->getQuery(true);
		$query = "INSERT INTO hb_mannschaft";
		$query .= " (`kuerzel`, `mannschaft`, `name`, `nameKurz`, 
			`ligaKuerzel`, `liga`, `geschlecht`, `jugend`, `hvwLink`)";
		$query .= " VALUES \n";
		
		foreach ($newTeams as $data)
		{
			if (isset($data['includeTeam'])) {
				
				// kuerzel
				if ($data['jugend']) {
					$teamkey = preg_replace( "/(^(m|w|g)J[A-E])-[A-Z]{2,3}.*/" ,
							"$1" , $data['staffel']);
				}
				else {
					if (preg_match( "/(^(M|F)-[A-Z]{2,3})/", $data['staffel'])) {
						$teamkey = preg_replace( "/(^(M|F))-[A-Z]{2,3}.*/" ,
								"$1" , $data['staffel']);
					}
					else {
						$teamkey = $data['staffel'];
					}
				}
				$key = $teamkey;
				if (!empty($teamkeyArray[strtolower($key)])) {
					$teamkey .= ++$teamkeyArray[strtolower($key)];
				}
				else {
					$teamkeyArray[strtolower($key)] = 1;
					$teamkey = preg_replace ( "/^((M|F))$/" , "\${1}1" ,
							$teamkey);
				}
				$values = "('".$teamkey."'";
				// mannschaft
				$category = preg_replace(
						'/(((männ|weib)liche|gemischte) Jugend|Männer|Frauen)'.
							'.*/', "$1", $data['staffelName']);
				$youthLetter = preg_replace('/.*Jugend ([A-E]).*/',
						"$1", $data['staffelName']);
				$category = preg_replace('/(Jugend)/', $youthLetter."-$1", $category);
				$teamNumber = null;
				if (preg_match('/.*( [1-9])$/', $data['rankingName'], $matches))
					$teamNumber = $matches[1];
				//echo __FILE__.'('.__LINE__.'):<pre>';print_r($matches);echo'</pre>';
				$values .= ",'".$category.$teamNumber."'";
				// name (Tabelle)
				$values .= ",'".$data['rankingName']."'";
				// name (Spielplan)
				$values .= ",'".$data['scheduleName']."'";
				// ligaKuerzel
				$values .= ",'".$data['staffel']."'";
				// liga
				$liga = preg_replace(
						"/(((männ|weib)liche|gemischte) Jugend|Männer|Frauen)". 
							" ([A-E] )?/", "", $data['staffelName']);
				$values .= ",'".$liga."'";
				// geschlecht
				$values .= ",'".$data['geschlecht']."'";
				// jugend 
				if ($data['jugend']) $values .= ",'".$data['jugend']."'";
				else $values .= ",0";
				// hvwLink
				$values .= ",'http://www.hvw-online.org/".$data['staffelLink'].
						"&all=1'";
				
				$values .= "), \n";				
				$query .= $values;
			}
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
	
	function deleteTeams($teams)
	{
		//echo '=> model->$teams <br><pre>".$teams."</pre>';
		
		$db = $this->getDbo();
		
		foreach ($teams as $team) {
			if (isset($team['deleteTeam'])) {
				$where[] = $db->q($team['kuerzel']);
			}	
		}
		$where = implode(', ', $where);
		$where = $db->qn('kuerzel') . ' IN (' . $where . ')';
				 
		$query = $db->getQuery(true);
		$query->delete('hb_mannschaft');
		$query->where($where);
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
	
	function updateTeams($teams) 
	{
		$db = $this->getDbo();
		
		$query = "INSERT INTO hb_mannschaft";
		$query .= " (`reihenfolge`, `kuerzel`, `mannschaft`, `name`, 
					`nameKurz`, `ligaKuerzel`, `liga`, `geschlecht`, 
					`jugend`, `hvwLink`)";
		$query .= " VALUES \n";
	
		foreach ($teams as $data)
		{
			// 
			$values = "(";
			// reihenfolge
			if ($data['reihenfolge'] != 0) 
				$values .= "'".$data['reihenfolge']."'";
			else $values .= 'NULL';
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
				$values .= ",'".'http://www.hvw-online.org/'.
						$data['hvwLink']."'";
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
	
	function selectHomeTeam($input, $teams, $nameIdentifier)
	{
		$options = '';
		foreach (explode('&&',$teams) as $name){
			$options .= '<option ';
			if (preg_match($nameIdentifier, $name)) 
					$options .= 'selected="selected" ';
			$options .= 'value="'.$name.'">'.$name.'</option>'."\n";
		}
		$input = str_replace('<option value="leer">auswaehlen</option>',
					$options, $input);
		return $input;
	}
}