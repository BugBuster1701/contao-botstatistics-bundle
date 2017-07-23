<?php

/**
 * Contao Open Source CMS
 * 
 * Copyright (C) 2005-2012 Leo Feyer
 * 
 * @package Botstatistics
 * @link    http://contao.org
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */


/**
 * Register the namespaces
 */
ClassLoader::addNamespaces(array
(
	'BugBuster',
));


/**
 * Register the classes
 */
ClassLoader::addClasses(array
(
	// Modules
	'BugBuster\BotStatistics\ModuleBotStatisticsStat' => 'system/modules/botstatistics/modules/ModuleBotStatisticsStat.php',
	'BugBuster\BotStatistics\ModuleBotStatisticsTag'  => 'system/modules/botstatistics/modules/ModuleBotStatisticsTag.php',
	'BugBuster\BotStatistics\ModuleBotStatistics'     => 'system/modules/botstatistics/modules/ModuleBotStatistics.php',

	// Public
	'BugBuster\BotStatistics\BotStatisticsDetails'    => 'system/modules/botstatistics/public/BotStatisticsDetails.php',

	// Classes
	'BugBuster\BotStatistics\BotStatisticsCheck'      => 'system/modules/botstatistics/classes/BotStatisticsCheck.php',
	'BugBuster\BotStatistics\BotStatisticsHelper'     => 'system/modules/botstatistics/classes/BotStatisticsHelper.php',
	'BugBuster\BotStatistics\BotStatisticsCron'       => 'system/modules/botstatistics/classes/BotStatisticsCron.php',
));


/**
 * Register the templates
 */
TemplateLoader::addFiles(array
(
	'mod_botstatistics_be_stat_partial_summary'   => 'system/modules/botstatistics/templates',
	'mod_botstatistics_be_stat_partial_details'   => 'system/modules/botstatistics/templates',
	'mod_botstatistics_fe'                        => 'system/modules/botstatistics/templates',
	'mod_botstatistics_be_stat_partial_top_bots'  => 'system/modules/botstatistics/templates',
	'mod_botstatistics_be_stat_partial_top_pages' => 'system/modules/botstatistics/templates',
	'mod_botstatistics_be_stat'                   => 'system/modules/botstatistics/templates',
));
