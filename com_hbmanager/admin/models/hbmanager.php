<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
jimport('joomla.application.component.modeladmin');

class HBmanagerModelHBmanager extends JModel
{	

	
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
	
	function importArticles () {
		$superpath = '../../';
		$verz = opendir ("{$superpath}aktuelleDaten/news");
		
		while ($file = readdir ($verz)) {
			if(preg_match('/news_20(\d{6})_(\d{6})\.php$/', $file)) {
				$files[] = $file;
				//break;
			}
			
		}
		
		closedir($verz);
		sort($files);
		
		//$files = array_reverse($files);
		//echo "<pre>"; print_r($files); echo "</pre>";
		
		foreach ($files as $key => $file) {
			$content = file_get_contents("{$superpath}aktuelleDaten/news/".$file);
			//$content = mb_convert_encoding($content, 'HTML-ENTITIES', "UTF-8");
			//echo "<pre>".$file."<br/>"; print_r($content); echo "</pre>";
		
			$newsData = self::extract_data($file, $content);
			//echo "<pre>".$file."<br/>"; print_r($newsData); echo "</pre>";
		
			//$xmlData = self::data2xml($newsData);
			//echo "<pre>".$file."<br/>"; print_r(htmlspecialchars($xmlData)); echo "</pre>";
		
			self::addNewsArticle($newsData);
		}
	}
	
	function data2xml($article)
	{
		$output = "\n".'<article>'."\n";
	
		$output .= "\t".'<file>'."\n";
		$output .= "\t\t".'<date>';
		$output .= $article->file->date;
		$output .= '</date>'."\n";
		$output .= "\t\t".'<time>';
		$output .= $article->file->time;
		$output .= '</time>'."\n";
		$output .= "\t".'</file>'."\n";
	
		$output .= "\t".'<headerinfo>'."\n";
		$output .= "\t\t".'<time>';
		$output .= $article->header->date;
		$output .= '</time>'."\n";
		$output .= "\t\t".'<date>';
		$output .= $article->header->time;
		$output .= '</date>'."\n";
		$output .= "\t\t".'<day>';
		$output .= $article->header->day;
		$output .= '</day>'."\n";
		$output .= "\t".'</headerinfo>'."\n";
	
		$output .= "\t".'<heading>'."\n";
		$output .= "\t\t".'<text>';
		$output .= $article->heading->text;
		$output .= '</text>'."\n";
		$output .= "\t\t".'<weekday>';
		$output .= $article->heading->weekday;
		$output .= '</weekday>'."\n";
		$output .= "\t\t".'<day>';
		$output .= $article->heading->day;
		$output .= '</day>'."\n";
		$output .= "\t\t".'<month>';
		$output .= $article->heading->month;
		$output .= '</month>'."\n";
		$output .= "\t\t".'<year>';
		$output .= $article->heading->year;
		$output .= '</year>'."\n";
		$output .= "\t".'</heading>'."\n";
	
		foreach ($article->content as $content)
		{
			$output .= "\t".'<content>'."\n";
	
			foreach ($content as $key => $value)
			{
				if (!empty($value)) {
					$output .= "\t\t".'<'.$key.'>';
					$output .= $value;
					$output .= '</'.$key.'>'."\n";
				}
			}
	
			$output .= "\t".'</content>'."\n";
		}
	
		/*
	
		*/
	
		$output .= '</article>'."\n\n";
	
		return $output;
	}
	
	
	function extract_data($file, $content)
	{
		// file info
		$newsData->file->date = preg_replace('/news_(20\d{2})(\d{2})(\d{2})_(\d{6})\.php$/', "$1-$2-$3", $file);
		$newsData->file->time = preg_replace('/news_(20\d{6})_(\d{2})(\d{2})(\d{2})\.php$/', "$2:$3:$4", $file);
		 
		// header info
		$start = strpos($content,'<!-- News-Datei vom ') + strlen('<!-- ');
		$end = strpos($content,' Uhr-->',$start);
		$headerInfo = substr($content,$start,($end + strlen(' Uhr') - $start));
		//echo $headerInfo;
		 
		//$newsData->header->day = preg_replace('/.* News-Datei vom (*{3-15}), .*/', "$1", $headerInfo);
		//$newsData->header->date = preg_replace('/.*, (\d{2})\.(\d{2})\.(\d{6}) um .*/', "$3-$1-$2", $headerInfo);
		//$newsData->header->time = preg_replace('/.* um (\d{2}:\d{2}:\d{2}) Uhr.*/', "$1", $headerInfo);
	
		$subject = $headerInfo;
		$pattern = '/.*News-Datei vom (\w*), (\d{2})\.(\d{2})\.(\d{4}) um (\d{2}:\d{2}:\d{2}) Uhr.*/';
		preg_match($pattern, $subject, $matches);
		//print_r($matches);
		$newsData->header->day = $matches[1];
		$newsData->header->date = $matches[4].'-'.$matches[3].'-'.$matches[2];
		$newsData->header->time = $matches[5];
		// heading
		$start = strpos($content,'<div class="news">') + strlen('<div class="news">');
		$end = strpos($content,'</span></h3>',$start);
		$heading = substr($content,$start,($end - $start));
	
		$subject = $heading;
		$pattern = '/.*<h3>\s*(.*)\s*<span>.*/';
		preg_match($pattern, $subject, $matches);
		//print_r($matches);
		$newsData->heading->text = trim($matches[1]);
		$pattern = '/.*<span>\s*(\w*),\s*(\d{1,2})\. (\w*) (\d{4})\s*.*/';
		preg_match($pattern, $subject, $matches);
		//print_r($matches);
		$newsData->heading->weekday = $matches[1];
		$newsData->heading->day = $matches[2];
		$newsData->heading->month = $matches[3];
		$newsData->heading->year = $matches[4];
	
		// content
		$start = strpos($content,'<div class="einschub">') + strlen('<div class="einschub">');
		$end = strrpos($content,'</div>');
		$end = strrpos($content,'</div>', $end-strlen($content)-1);
		$content = substr($content,$start,($end - $start));
		//echo htmlspecialchars($content);
		$content = preg_replace('/(<h5>)<a href=.*php">(.*)<\/a>(<\/h5>)/', "$1$2$3", $content);
		//echo htmlspecialchars($content);
	
		$content = explode('<h5>', $content);
		//echo "<pre>"; print_r($content); echo "</pre>";
	
		foreach ($content as $key => $value)
		{
			$value = explode('</h5>', $value);
			//echo "<pre>"; print_r($value); echo "</pre>";
			if (count($value) == 2) {
				$newsData->content[$key]['heading'] = htmlspecialchars($value[0]);
				$text = trim($value[1]);
				$pattern = '/<div class="einschub">\s*<table class="ergebnis">\s*'.
						'<tr><td class="text\s?\w*">(.*)<\/td><td>-<\/td><td class="text\s?\w*">(.*)<\/td>'.
						'<td class="figure\s?\w*">(\d{1,3})<\/td><td>:<\/td><td class="figure\s?\w*">(\d{1,3})<\/td><\/tr>'.
						'\s*<\/table>/';
				if (preg_match($pattern, $text, $matches)) {
					//print_r($matches);
					$newsData->content[$key]['heim'] = htmlspecialchars($matches[1]);
					$newsData->content[$key]['gast'] = htmlspecialchars($matches[2]);
					$newsData->content[$key]['toreheim'] = htmlspecialchars($matches[3]);
					$newsData->content[$key]['toregast'] = htmlspecialchars($matches[4]);
	
					if (preg_match('/<p class="spielbericht">((\s|.)*)<\/p>\s*<\/div>/', $text, $matches))
					{
						//print_r($matches);
						$newsData->content[$key]['bericht'] = htmlspecialchars($matches[1]);
					}
	
				}
				else {
					$text = preg_replace('/<div class="einschub">((\s|.)*)<\/div>/',"$1", trim($text));
					$newsData->content[$key]['text'] = htmlspecialchars($text);
				}
			}
	
			else {
				$text = trim($value[0]);
				if (!empty($text)) {
					$newsData->content[$key]['text'] = htmlspecialchars($text);
				}
			}
			 
		}
		return $newsData;
	}
	
	//--------------------------------------------------------------
	
	function addNewsArticle($newsData)
	{
	
		$content .= '<div class="newsspieltag">';
		foreach ($newsData->content as $value)
		{
			$content .= '<h4>'.
					$value['heading'].
					'</h4>';
	
			$content .= '<div>';
			if (empty($value['text'])) {
				$content .= '<table class="ergebnis">'.
						'<tbody>'.
						'<tr>'.
						'<td class="text">'.$value['heim'].'</td><td>-</td>'.
						'<td class="text">'.$value['gast'].'</td>'.
						'<td class="figure">'.$value['toreheim'].'</td><td>:</td>'.
						'<td class="figure">'.$value['toregast'].'</td>'.
						'</tr>'.
						'</tbody>'.
						'</table>';
				if (!empty($value['bericht']))$content .= '<p class="spielbericht">'.
						htmlspecialchars_decode($value['bericht']).'</p>';
				$content .= '</div>';
			}
			else {
				$content .= htmlspecialchars_decode($value['text']);
			}
		}
		$content .= '</div>';
	
	
		$timestamp = strtotime($newsData->file->date . $newsData->file->time);
		if (!strcmp('Spiele vom vergangenen Wochenende',$newsData->heading->text)) $addAlias = '-letztespiele';
		$alias = date('Ymd-His', $timestamp).'-news'.$addAlias;
		echo $alias;
	
		echo $content;
	
		$table = JTable::getInstance('Content', 'JTable', array());
	
		$data = array(
				'alias' => $alias,
				'title' => $newsData->heading->text,
				'created' => date('Y-m-d H:i:s', $timestamp),
				'introtext' => $content,
				//'fulltext' => '', //für Text der beim Klicken auf "Weiterlesen" erscheint
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
	
			// auf frontpage setzen
	
			// content_ID ermitteln
			$db = $this->getDbo();
			$query = $db->getQuery(true);
			$query->select($db->quoteName('id'));
			$query->from($db->quoteName('#__content'));
			$query->where($db->quoteName('alias').' = '.$db->quote($alias));
			//echo $query;echo "<br />";
			$db->setQuery($query);
			$contentID = $db->loadResult();
			//echo "<pre>ID (Content): \n"; print_r($contentID); echo "</pre>";
	
			// Reihenfolge bereits auf frontpage dargestellter Artikel inkrementieren
			$query = $db->getQuery(true);
			$query->update($db->quoteName('#__content_frontpage'));
			$query->set($db->quoteName('ordering').' = '.$db->quoteName('ordering').'+1');
			//echo $query;echo "<br />";
			$db->setQuery($query);
			try	{
				// Execute the query in Joomla 2.5.
				$result = $db->query();
			}
			catch (Exception $e) {
				// catch any database errors.
			}
	
			// in Frontpage DB table einfügen
			$columns = array('content_id', 'ordering');
			$values = array($db->quote($contentID), 1);
			$query = $db->getQuery(true);
			$query->insert($db->quoteName('#__content_frontpage'));
			$query->columns($db->quoteName($columns));
			$query->values(implode(',', $values));
			//echo $query;echo "<br />";
			$db->setQuery($query);
			try	{
				// Execute the query in Joomla 2.5.
				$result = $db->query();
			}
			catch (Exception $e) {
				// catch any database errors.
			}
	
	}
		
}


