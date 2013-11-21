<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Form
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
 
defined('JPATH_PLATFORM') or die;
 
JFormHelper::loadFieldClass('list');
 
class JFormFieldGamedays extends JFormFieldList
{

    public $type = 'gamedays';
 
    protected function getOptions()
    {
        // Initialize variables.
        $options = array();
        
        // Initialize some field attributes.
        $translate = $this->element['translate'] ? (string) $this->element['translate'] : false;
        
        
        // Get the database object.
        $db = JFactory::getDBO();
		$query = $db->getQuery(true);
        
       	$query->select("DISTINCT DATE_FORMAT(datum, '%a, %d.%m.%Y') AS ".$db->qn('value').", datum AS ".$db->qn('key'));
		$query->from('aaa_spiel');
		$query->order($db->quoteName('datum').' ASC');
		$db->setQuery($query);

        
        // Set the query and get the result list.
        $db->setQuery($query);
        $items = $db->loadObjectlist();
        //echo "<pre>"; print_r($items); echo "</pre>";
        
        // Build the field options.
        if (!empty($items))
        {

        	foreach ($items as $item)
            {
                if ($translate == true)
                {
                    $options[] = JHtml::_('select.option', $item->key, JText::_($item->value));
                }
                else
                {
                    $options[] = JHtml::_('select.option', $item->key, $item->value);
                }
            }
        }
 
        // Merge any additional options in the XML definition.
        $options = array_merge(parent::getOptions(), $options);
 
        return $options;
    }
}