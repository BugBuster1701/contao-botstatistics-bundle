<?php 

/**
 * Contao Open Source CMS, Copyright (C) 2005-2013 Leo Feyer
 *
 * Modul BotStatistics - Backend 
 * DCA tl_module, modifies the data container array of table tl_module.
 *
 * @copyright  Glen Langer 2012..2013 <http://www.contao.glen-langer.de>
 * @author     Glen Langer (BugBuster)
 * @package    BotStatistics
 * @license    LGPL
 * @filesource
 * @see        https://github.com/BugBuster1701/botstatistics
 */


/**
 * Add palettes to tl_module
 */
//$GLOBALS['TL_DCA']['tl_module']['palettes']['botstatistics']   = 'name,type,headline;botstatistics_name;guests,protected;align,space,cssID';
$GLOBALS['TL_DCA']['tl_module']['palettes']['botstatistics']   = 'name,type;botstatistics_name,botstatistics_cron';



/**
 * Add fields to tl_module
 */
$GLOBALS['TL_DCA']['tl_module']['fields']['botstatistics_name'] = array
(
	'label'         => &$GLOBALS['TL_LANG']['tl_module']['botstatistics_name'],
	'exclude'       => true,
	'inputType'     => 'text',
	'search'        => true,
    'sql'           => "varchar(64) NOT NULL default ''",
	'eval'          => array('mandatory'=>true, 'maxlength'=>64, 'tl_class'=>'w50')
);

$GLOBALS['TL_DCA']['tl_module']['fields']['botstatistics_cron'] = array
(
    'label'			=> &$GLOBALS['TL_LANG']['tl_module']['botstatistics_cron'],
    'inputType'		=> 'checkbox',
    'sql'           => "char(1) NOT NULL default ''",
    'eval'      	=> array('mandatory'=>false, 'tl_class'=>'w50 m12')
);

