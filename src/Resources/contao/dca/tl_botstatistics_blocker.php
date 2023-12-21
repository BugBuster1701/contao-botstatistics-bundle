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
 * Table tl_botstatistics_blocker
 */
$GLOBALS['TL_DCA']['tl_botstatistics_blocker'] = array
(
	// Config
	'config' => array
	(
		'sql' => array
		(
			'keys' => array
			(
				'id' => 'primary',
				'bot_module_id' => 'index'
			)
		)
	),

	// Fields
	'fields' => array
	(
		'id' => array
		(
			'sql'                     => "int(10) unsigned NOT NULL auto_increment"
		),
		'bot_module_id' => array
		(
			'sql'                     => "int(10) unsigned NOT NULL default '0'"
		),
		'bot_tstamp' => array
		(
			'sql'                     => "timestamp NULL"
		),
		'bot_ip' => array
		(
			'sql'                     => "varchar(40) NOT NULL default '0.0.0.0'"
		)
	)
);
