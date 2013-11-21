<?php 
// No Direct Access
defined('_JEXEC') or die('Restricted access');

// Component HTML Helper
class JHtmlIcon
{
	static function msword($item, $params, $attribs = array())
	{
			$url = 'index.php?option=com_hbmanager&task=showAmtsblattWord';
			$text = JHtml::_('image', 'media/com_hbmanager/images/word-doc_32.png', JText::_('Word-Dokument Vorlage'), NULL, false);
			$attribs['title'] = JText::_('Word-Dokument Vorlage');

			$output = JHtml::_('link', JRoute::_($url), $text, $attribs);
			return $output;
	}
}
?>