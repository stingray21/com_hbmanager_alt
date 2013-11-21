<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

JToolBarHelper::preferences('com_hbmanager');

setlocale(LC_TIME, "de_DE.UTF-8");


// Include the PHPWord.php, all other classes were loaded by an autoloader
require_once JPATH_SITE.DS.'libraries'.DS.'PHPWord'.DS.'PHPWord.php';

// Create a new PHPWord Object
$PHPWord = new PHPWord();

$document = $PHPWord->loadTemplate(JPATH_SITE.DS.'media'.DS.'com_hbmanager'.DS.'template_Bericht-Handball.docx');

//The search-pattern spelling is: ${YOUR_SEARCH_PATTERN}
$document->setValue('Datum', strftime('%A, %d.%m.%Y'));
$document->setValue('KW', strftime('%V'));

$filename = strftime('%Y%m%d').'_Bericht-Handball_KW'.strftime('%V').'.docx';
$path = JPATH_SITE.DS.'Amtsblattartikel'.DS;
$document->save($path.$filename);

/*

// Eigenschaften des Word-Dokuments
$properties = $PHPWord->getProperties();
$properties->setCreator('Handball Geislingen');  // ändern zu Joomla User
// $properties->setCompany('My factory');
$properties->setTitle('Amtsblatt-Bericht');
$properties->setDescription('Bericht der Abt. Handball für das Amtsblatt Geislingen');
$properties->setCategory('Amtsblatt');
$properties->setLastModifiedBy('Handball Geislingen');  // ändern zu Joomla User
$properties->setCreated( mktime() );
$properties->setModified( mktime() );
$properties->setSubject('Handball');
$properties->setKeywords('Amtsblatt, Bericht, Handball, Geislingen');

// Formatierung
$PHPWord->setDefaultFontName('Arial');
$PHPWord->setDefaultFontSize(9);

$PHPWord->addParagraphStyle('AmtsblattUeberschrift', array('spaceBefore'=>100, 'spaceAfter'=>0,'align'=>'left'));
$PHPWord->addFontStyle('Ueberschrift', array('name'=>'Arial', 'size'=>9, 'bold'=>true));
$PHPWord->addParagraphStyle('AmtsblattText', array('spaceBefore'=>0, 'spaceAfter'=>0,'align'=>'both', 'tabs' => array(new PHPWord_Style_Tab("left", 1000), new PHPWord_Style_Tab("right", 4800))));
$PHPWord->addFontStyle('Text', array('name'=>'Arial', 'size'=>9));


// Inhalt
$section = $PHPWord->createSection();

$section->addText(strftime('%A, %d.%m.%Y'), array('name'=>'Arial', 'size'=>12), array('align'=>'right'));
$section->addText('Artikel für das Amtsblatt Geislingen - KW'.strftime('%V'), array('name'=>'Arial', 'size'=>14, 'color'=>'000000', 'bold'=>true), array('spaceAfter'=>1000));

$section->addText($this->anfang['ueberschrift'], 'Ueberschrift', 'AmtsblattUeberschrift');
if (isset($this->anfang['link'])) $section->addText($this->anfang['link'], 'Text', 'AmtsblattText');
$section->addTextBreak();

if (!empty($this->letzteSpiele))
{
	$section->addText($this->letzteSpiele['ueberschrift'], 'Ueberschrift', 'AmtsblattUeberschrift');
	$section->addText($this->letzteSpiele['dates'], 'Text', 'AmtsblattText');
	$section->addText($this->letzteSpiele['spiele'], 'Text', 'AmtsblattText');
}

if (!empty($this->berichte)) 
{
	$section->addText('Berichte', 'Ueberschrift', 'AmtsblattUeberschrift');
		
	foreach ($this->berichte as $bericht)
	{
		$section->addText($bericht['ueberschrift'], 'Ueberschrift', 'AmtsblattUeberschrift');
		$section->addText($bericht['ergebnis'], 'Text', 'AmtsblattText');
		if (isset($bericht['text'])) $section->addText($bericht['text'], 'Text', 'AmtsblattText');
		if (isset($bericht['spieler'])) $section->addText($bericht['spieler'], 'Text', 'AmtsblattText');
		//$section->addTextBreak();
	}
}

if (!empty($this->kommendeSpiele))
{
	$section->addText($this->kommendeSpiele['ueberschrift'], 'Ueberschrift', 'AmtsblattUeberschrift');
	$section->addText($this->kommendeSpiele['spiele'], 'Text', 'AmtsblattText');
}

if (!empty($this->vorberichte)) 
{
	$section->addText('Vorschau', 'Ueberschrift', 'AmtsblattUeberschrift');
		
	foreach ($this->vorberichte as $bericht)
	{
		$section->addText($bericht['ueberschrift'], 'Ueberschrift', 'AmtsblattUeberschrift');
		$section->addText($bericht['spiel'], 'Text', 'AmtsblattText');
		if (isset($bericht['treff'])) $section->addText($bericht['treff'], 'Text', 'AmtsblattText');
		if (isset($bericht['text'])) $section->addText($bericht['text'], 'Text', 'AmtsblattText');
		//$section->addTextBreak();
	}
}



// At last write the document to webspace:
$objWriter = PHPWord_IOFactory::createWriter($PHPWord, 'Word2007');
$filename = strftime('%Y%m%d').'_Bericht-Handball_KW'.strftime('%V').'.docx';
$path = JURI::Root().'Amtsblattartikel'.DS;
$objWriter->save($path.$filename);
*/

/*
// phpdocx

// include the PHPdocx library
//require_once JPATH_SITE.'/libraries/phpdocx/classes/CreateDocx.inc';
require_once DS.'www'.DS.'htdocs'.DS.'w00f0a0a'.DS.'hb'.DS.'libraries'.DS.'phpdocx'.DS.'classes'.DS.'CreateDocx.inc';

$docx = new CreateDocx();

$text = array();

$paramsText = array('pStyle' => 'Heading5', 'font'=>'Arial', 'sz' => 12, 'b' => 'single', 'lineSpacing' => 200);
foreach ($this->berichte as $bericht)
{
	$docx->addText($bericht['ueberschrift'],$paramsText);
	$docx->addText($bericht['ergebnis']);
	$docx->addText($bericht['text']);
}

// Generate the document
$docx->createDocx(DS.'www'.DS.'htdocs'.DS.'w00f0a0a'.DS.'hb'.DS.'test/testtemp');
*/





// Force the document to the browser
$app = JFactory::getApplication();
$app->redirect(JURI::Root().'Amtsblattartikel'.DS.$filename);

