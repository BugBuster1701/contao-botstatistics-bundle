<?php

/*
 * This file is part of a BugBuster Contao Bundle.
 *
 * @copyright  Glen Langer 2024 <http://contao.ninja>
 * @author     Glen Langer (BugBuster)
 * @package    Contao BotStatistics Bundle
 * @link       https://github.com/BugBuster1701/contao-botstatistics-bundle
 *
 * @license    LGPL-3.0-or-later
 */

/*
 * Table tl_botstatistics_counter_details
 */
$GLOBALS['TL_DCA']['tl_botstatistics_counter_details'] = array
(
	// Config
	'config' => array
	(
		'sql' => array
		(
			'keys' => array
			(
				'id' => 'primary',
				'pid,bot_page_alias' => 'unique'
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
		'pid' => array
		(
			'sql'                     => "int(10) unsigned NOT NULL default '0'"
		),
		'bot_page_alias' => array
		(
			'sql'                     => "varchar(255) NOT NULL default 'Unknown'"
		),
		'bot_page_alias_counter' => array
		(
			'sql'                     => "int(10) unsigned NOT NULL default '0'"
		)
	)
);
