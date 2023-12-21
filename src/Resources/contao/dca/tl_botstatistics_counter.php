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
 * Table tl_botstatistics_counter
 */
$GLOBALS['TL_DCA']['tl_botstatistics_counter'] = array
(
	// Config
	'config' => array
	(
		'sql' => array
		(
			'keys' => array
			(
				'id' => 'primary',
				'bot_module_id,bot_date,bot_name' => 'unique'
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
		'bot_date' => array
		(
			'sql'                     => "date NOT NULL default '1999-01-01'"
		),
		'bot_name' => array
		(
			'sql'                     => "varchar(60) NOT NULL default 'Unknown'"
		),
		'bot_counter' => array
		(
			'sql'                     => "int(10) unsigned NOT NULL default '0'"
		)
	)
);
