<?php 
// No Direct Access
defined('_JEXEC') or die('Restricted access');

// Component HTML Helper
class JHtmlIcon
{
	static function msword($item, $params, $attribs = array())
	{
			$url = 'index.php?option=com_hbmanager&task=showJournalWord';
			$text = JHtml::_('image', 
					'media/com_hbmanager/images/word-doc-32x32.png', 
					JText::_('COM_HBMANAGER_JOURNAL_WORD_TEMPLATE_ICON'), 
						NULL, false);
			$attribs['title'] = 
					JText::_('COM_HBMANAGER_JOURNAL_WORD_TEMPLATE_ICON');

			$output = JHtml::_('link', JRoute::_($url), $text, $attribs);
			return $output;
	}
}
?>