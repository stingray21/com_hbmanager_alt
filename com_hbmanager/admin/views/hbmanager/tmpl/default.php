<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

JToolBarHelper::preferences('com_hbmanager');


echo 'HB Manager Startseite';

$this->model->importArticles();
?>




