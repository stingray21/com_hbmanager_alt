<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
jimport('joomla.application.component.modeladmin');


setlocale(LC_TIME, "de_DE");

class hbmanagerModelHbnextgames extends JModel
{	
	private $nextGames = array();
	private $dateStart = "";
	private $dateEnd = "";
	
	function __construct() 
	{
		parent::__construct();
		
		setlocale(LC_TIME, "de_DE");
		
		$db = $this->getDbo();
		$db->setQuery("SET lc_time_names = 'de_DE'");
		try{
			// Execute the query in Joomla 2.5.
			$result = $db->query();
		}
		catch (Exception $e) {
			// catch any database errors.
		}
	}
	
	function getTeams()
	{
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from('hb_mannschaft');
		//echo '=> model->$query <br><pre>".$query."</pre>';
		$db->setQuery($query);
		return $teams = $db->loadObjectList();
	}
	
	function setDates($dates)
	{
		//echo '=> model->$dates <br><pre>".$dates."</pre>';
		$db = $this->getDbo();
		
		if (empty($dates)){
			$todaydate = strftime("%Y-%m-%d", time());
			//echo $todaydate = "2013-10-23";
			
			$query = "SELECT `datum` from `hb_spiel` WHERE `datum` BETWEEN ".
				$db->q($todaydate). " AND " . 
				$db->q(strftime("%Y-%m-%d", strtotime('next Monday', 
							strtotime('next friday', strtotime($todaydate))))).
				" ORDER BY `datum` ASC LIMIT 1";
			//echo '=> model->$query <br><pre>".$query."</pre>';
			$db->setQuery($query);
			$result = $db->loadResult();
			if (!empty($result)) {
				$dates['startdateNext'] = $result;
			}
			else {
				$query = "SELECT `datum` from `hb_spiel` WHERE `datum` > ".
						$db->q(strftime("%Y-%m-%d", strtotime('next Monday', 
							strtotime('next friday', strtotime($todaydate))))).
						" ORDER BY `datum` ASC LIMIT 1";
				//echo '=> model->$query <br><pre>".$query."</pre>';
				$db->setQuery($query);
				$dates['startdateNext'] = $db->loadResult();
			}
			$query = "SELECT `datum` from `hb_spiel` WHERE `datum` BETWEEN ".
					$db->q($dates['startdateNext']) . " AND " . 
					$db->q(strftime("%Y-%m-%d", strtotime('next friday', 
						strtotime($dates['startdateNext'])))).
					" ORDER BY `datum` DESC LIMIT 1";
			//echo '=> model->$query <br><pre>".$query."</pre>';
			$db->setQuery($query);
			$dates['enddateNext'] = $db->loadResult();
		}
			
		self::setDateStart($dates['startdateNext']);
		self::setDateEnd($dates['enddateNext']);
	}
		
	function getDates()
	{
		$dates['startdateNext'] = $this->dateStart;
		$dates['enddateNext'] = $this->dateEnd;
	
		return $dates;
	}
	
	function setDateStart($date)
	{
		$this->dateStart = strftime("%Y-%m-%d", strtotime($date));
	}
	
	function setDateEnd($date)
	{
		if ($date > $this->dateStart) {
			$this->dateEnd = strftime("%Y-%m-%d", strtotime($date));
		}
		else {
			$db = $this->getDbo();
			$query = "SELECT `datum` from `hb_spiel` WHERE `datum` < ".
					$db->q(strftime("%Y-%m-%d", strtotime('+1 week', 
							strtotime($date)))).
					" ORDER BY `datum` DESC LIMIT 1";
			//echo '=> model->$query <br><pre>".$query."</pre>';
			$db->setQuery($query);
			$this->dateEnd = $db->loadResult();
		}
	}
	
	function getDateStart()
	{
		return $this->dateStart;
	}
	
	function getDateEnd()
	{
		return $this->dateEnd;
	}
	
	function getGames()
	{
		$db = $this->getDbo();
		
		$query = $db->getQuery(true);
		$query->select('`spielIDhvw`, `kuerzel`, `spielID`, `hallenNummer`, 
			`datum`, `uhrzeit`, `heim`, `gast`, `toreHeim`, `toreGast`, 
			`bemerkung`, `mannschaftID`, `reihenfolge`, `mannschaft`, `name`, 
			`nameKurz`, `ligaKuerzel`, `liga`, `geschlecht`, `jugend`, 
			`spielvorschauID`, `vorschau`, `treffOrt`, `treffZeit`');
		$query->from('hb_spiel');
		$query->leftJoin($db->qn('hb_mannschaft').' USING ('.
						$db->qn('kuerzel').')');
		$query->leftJoin($db->qn('hb_spielvorschau').' USING ('.
						$db->qn('spielIDhvw').')');
		$query->where($db->qn('datum').' BETWEEN '.$db->q($this->dateStart).
						' AND '.$db->q($this->dateEnd));
		$query->order($db->qn('datum').' ASC, '.
						$db->qn('uhrzeit').' ASC');
		$db->setQuery($query);
		//echo '=> model->$query <br><pre>".$query."</pre>';
		$games = $db->loadObjectList();
		//echo '=> model->$games<br><pre>"; print_r($games); echo "</pre>';
		
		// sort for date
		$sortedGames = array();
		foreach ($games as $game){
			$sortedGames[$game->datum][] = $game;
		}
		//echo '=> model->$sortedGames<br><pre>'; 
		//print_r($sortedGames); echo "</pre>";
		
		return $this->nextGames = $sortedGames;
	}
	
	function getNextGamesArray($post = ''){
		$nextGames = array();
		
		for ($day = 0; $day <= 7; $day++) {
			$i = 0;
			
			while ($post["{$day}-{$i}_spielIDhvw"]) {
				$nextGames[$day][$i]['spielIDhvw'] = 
						strip_tags(trim($post["{$day}-{$i}_spielIDhvw"]));
				$nextGames[$day][$i]['vorbericht'] = 
						strip_tags(trim($post["{$day}-{$i}_vorbericht"]));
				$nextGames[$day][$i]['treffOrt'] = 
						strip_tags(trim($post["{$day}-{$i}_treffOrt"]));
				$nextGames[$day][$i]['treffZeit'] = 
						strip_tags(trim($post["{$day}-{$i}_treffZeit"]));
				
				$i++;
			}
		}
		//echo '=> model->$upcmingGames<br><pre>'; 
		//print_r($nextGames); echo "</pre>";
		
		return $nextGames;
	}
	
	function updateDB($nextGames = array())
	{
		if (empty($nextGames)) return;
		
		$db = $this->getDbo();
		
		foreach ($nextGames as $game)
		{
			//echo __FILE__.'('.__LINE__.'):<pre>';print_r($game);echo'</pre>';
				
			foreach ($game as $key => $value)
			{
				$game[$key] = trim($value);
			}
			
			if (count(array_filter($game)) > 1)
			{
				$query = $db->getQuery(true);
				$query = "REPLACE INTO ".$db->qn('hb_spielvorschau').
					"(".$db->qn('SpielIDhvw').", ".$db->qn('vorschau').", ".
					$db->qn('treffOrt').", ".$db->qn('treffZeit').") ".
					"VALUES (".$db->q($game['spielIDhvw']).', ';
					if (empty($game['vorschau'])) $query .= 'NULL, ';
						else $query .= $db->q($game['vorschau']).', ';
					if (empty($game['treffOrt'])) $query .= 'NULL, ';
						else $query .= $db->q($game['treffOrt']).', ';
					if (empty($game['treffZeit'])) $query .= 'NULL';
						else $query .= $db->q($game['treffZeit']);
					$query .=');';
				//echo __FILE__.'('.__LINE__.'):<pre>';print_r($query);echo'</pre>';
				$db->setQuery($query);
				try {
					// Execute the query in Joomla 2.5.
					$result = $db->query();
				}
				catch (Exception $e) {
					// catch any database errors.
				}
			}
		}
	}
	
	function writeNews()
	{
		// content article
		$db = $this->getDbo();
		
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from('hb_spielvorschau');
		$query->leftJoin($db->qn('hb_spiel').' USING ('.
							$db->qn('spielIDhvw').')');
		$query->leftJoin($db->qn('hb_mannschaft').' USING ('.
							$db->qn('kuerzel').')');
		$query->leftJoin($db->qn('hb_halle').' USING ('.
							$db->qn('hallenNummer').')');
		$query->where($db->qn('datum').' BETWEEN '.$db->q($this->dateStart).
							' AND '.$db->q($this->dateEnd));
		$query->order($db->qn('reihenfolge').' ASC');
		//echo '=> model->$query <br><pre>".$query."</pre>';
		$db->setQuery($query);
		$games = $db->loadObjectList();
		//echo '=> model->$games <br><pre>";print_r($games); echo "</pre>';
		
		if (!empty($games))
		{
			// earliest and latest included date 
			$query = $db->getQuery(true);
			$query->select('MIN('.$db->qn('datum').') AS min,'.
							' MAX('.$db->qn('datum').') AS max');
			$query->from('hb_spielvorschau');
			$query->leftJoin($db->qn('hb_spiel').' USING ('.
								$db->qn('spielIDhvw').')');
			$query->where($db->qn('datum').' BETWEEN '.$db->q($this->dateStart).
							' AND '.$db->q($this->dateEnd));
			//echo '=> model->$query <br><pre>".$query."</pre>';
			$db->setQuery($query);
			$dateframe = $db->loadObject();
			//echo '=> model->$dateframe <br><pre>'; 
			//print_r($dateframe); echo "</pre>";
			
			
			// format date
			$minDate = strtotime($dateframe->min);
			$maxDate = strtotime($dateframe->max);
			if ($minDate === $maxDate)
			{
				$titledate = strftime("%A, ", $minDate).
						ltrim(strftime("%d. %b %Y", $minDate),'0');
				$titledateKW = 'KW'.ltrim(strftime("%V", $minDate),'0');
			}
			else
			{
				if (strftime("%u", $minDate) == 6 AND 
						strftime("%u", $maxDate) == 7)
				{
					if (strftime("%m", $minDate) == strftime("%m", $maxDate))
					{
						$titledate = 'Wochenende '.
							ltrim(strftime("%d/", $minDate),'0').
							ltrim(strftime("%d. %b %Y", $maxDate),'0');
					}
					else
					{
						$titledate = 'Wochenende '.
							ltrim(strftime("%d. %b / ", $minDate),'0').
							ltrim(strftime("%d. %b %Y", $maxDate),'0');
					}
				}
				else
				{
					$titledate = ltrim(strftime("%d. %b %Y", $minDate),'0').
							' bis '.ltrim(strftime("%d. %b %Y", $maxDate),'0');
				}

				if (strftime("%V", $minDate) == strftime("%V", $maxDate))
				{

					$titledateKW = 'KW'.ltrim(strftime("%V", $minDate),'0');
				}
				else
				{
					$titledateKW = 'KW'.ltrim(strftime("%V", $minDate),'0').
							' bis KW'.ltrim(strftime("%V", $maxDate),'0');
				}
			}
			
			$prevTeam = NULL;
			$content = '<div class="newsspieltag">';
			foreach ($games as $game)
			{
					
				if ($prevTeam != $game->mannschaft)
				{
					$content .= '<h4>'.
							'<a href="'.JURI::Root().'index.php/'.
							strtolower($game->kuerzel).'-home">'.
							$game->mannschaft.' - '.$game->liga.' ('.
							$game->ligaKuerzel.')</a>'.
							'</h4>';
				}
				$prevTeam = $game->mannschaft;
					
				$content .= '<div class="vorberichtspiel">';
				$content .= '<a class="vorberichtspiel">'.$game->heim.' - '.
						$game->gast.'</a>';
				$content .= '<dl class="vorbericht">'.
						'<dt>Spiel</dt><dd>'.
						strftime("%A, %d.%m.%Y", strtotime($game->datum)).
						' um '.
						strftime("%H:%M Uhr", strtotime($game->uhrzeit)).
						' in '.$game->stadt.'</dd>';
				if (!empty($game->treffOrt) OR !empty($game->treffZeit))
				{
					$content .= '<dt>Treffpunkt';
					if ($game->hallenNummer != '7014') $content.= '/Abfahrt';
					$content .= '</dt>';
					$content .= '<dd>'.$game->treffOrt;
					if (!empty($game->treffZeit)) $content .= ' um '.
							strftime("%H:%M Uhr", strtotime($game->treffZeit));
					$content .= '</dd>';
				}
				$content .= '</dl>';
				if (!empty($game->vorschau))
					$content .= '<p class="vorbericht">'.$game->vorschau.'</p>';
				
				$content .= '</div>';
				
			}
			$content .= '</div>';
			//echo '=> model->$content <br><pre>".$content."</pre>';
			
			$timestamp = time();
			$alias = date('Ymd-His', $timestamp).'-news-vorschau'; 
			
			$table = JTable::getInstance('Content', 'JTable', array());
			
			$data = array(
					'alias' => $alias,
					'title' => 'Vorschau für '.$titledate, 
					'introtext' => $content,
					// for text that appears by clicking on 'more'
					//'fulltext' => '',
					'state' => 1,
					'catid' => 8,
					'featured' => 1
			);
			
			// Bind data
			if (!$table->bind($data))
			{
				$this->setError($table->getError());
				return false;
			}
			
			// Check the data.
			if (!$table->check())
			{
				$this->setError($table->getError());
				return false;
			}
			
			// Store the data.
			if (!$table->store())
			{
				$this->setError($table->getError());
				return false;
			}
			
			//To reorder the category
			//$table->reorder('catid = '.(int) $table->catid.' AND state >= 0');
			
			
			// put article on the frontpage

			// get content_ID
			$query = $db->getQuery(true);
			$query->select($db->qn('id'));
			$query->from($db->qn('#__content'));
			$query->where($db->qn('alias').' = '.$db->q($alias));
			//echo '=> model->$query <br><pre>".$query."</pre>';
			$db->setQuery($query);
			$contentID = $db->loadResult();
			//echo '=> model->$contentID<br><pre>'; 
			//print_r($contentID); echo "</pre>";

			// increment the order of the articles that are already on the frontpage
			$query = $db->getQuery(true);
			$query->update($db->qn('#__content_frontpage'));
			$query->set($db->qn('ordering').' = '.$db->qn('ordering').'+1');
			//echo '=> model->$query <br><pre>".$query."</pre>';
			$db->setQuery($query);
			try {
				// Execute the query in Joomla 2.5.
				$result = $db->query();
			}
			catch (Exception $e) {
				// catch any database errors.
			}

			// insert in frontpage DB table
			$columns = array('content_id', 'ordering');
			$values = array($db->q($contentID), 1);
			$query = $db->getQuery(true);
			$query->insert($db->qn('#__content_frontpage'));
			$query->columns($db->qn($columns));
			$query->values(implode(',', $values));
			//echo '=> model->$query <br><pre>".$query."</pre>';
			$db->setQuery($query);
			try {
				// Execute the query in Joomla 2.5.
				$result = $db->query();
			}
			catch (Exception $e) {
				// catch any database errors.
			}
		}
	}

}