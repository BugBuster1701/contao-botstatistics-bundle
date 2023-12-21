<?php

/*
 * This file is part of a BugBuster Contao Bundle.
 *
 * @copyright  Glen Langer 2023 <http://contao.ninja>
 * @author     Glen Langer (BugBuster)
 * @package    Contao BotStatistics Bundle
 * @link       https://github.com/BugBuster1701/contao-botstatistics-bundle
 *
 * @license    LGPL-3.0-or-later
 */

/*
 * Add palettes to tl_module
 */
$GLOBALS['TL_DCA']['tl_module']['palettes']['botstatistics']   = 'name,type;botstatistics_name,botstatistics_cron';

/*
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
