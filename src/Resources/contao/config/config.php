<?php

/*
 * This file is part of a BugBuster Contao Bundle.
 *
 * @copyright  Glen Langer 2023 <http://contao.ninja>
 * @author     Glen Langer (BugBuster)
 * @package    Contao BotStatistic Bundle
 * @link       https://github.com/BugBuster1701/contao-botstatistic-bundle
 *
 * @license    LGPL-3.0-or-later
 */

define('BOTSTATISTICS_VERSION', '1.1');
define('BOTSTATISTICS_BUILD', '0');

/*
 * -------------------------------------------------------------------------
 * BACK END MODULES
 * -------------------------------------------------------------------------
 */
$GLOBALS['BE_MOD']['system']['botstatistics'] = array
(
	'callback'   => 'BugBuster\BotStatistics\ModuleBotStatisticsStat',
	'icon'       => 'bundles/bugbusterbotstatistics/botstatistics2.png',
	'stylesheet' => 'bundles/bugbusterbotstatistics/mod_botstatistics_be.css',
);

/*
 * -------------------------------------------------------------------------
 * FRONT END MODULES
 * -------------------------------------------------------------------------
 */
$GLOBALS['FE_MOD']['miscellaneous']['botstatistics'] = 'BugBuster\BotStatistics\ModuleBotStatistics';

/*
 * -------------------------------------------------------------------------
 * HOOKS
 * -------------------------------------------------------------------------
 */
$GLOBALS['TL_HOOKS']['parseBackendTemplate'][]  = array('BugBuster\BotStatistics\BotStatisticsCheck', 'checkExtensions');
$GLOBALS['TL_HOOKS']['replaceInsertTags'][] = array('BugBuster\BotStatistics\ModuleBotStatisticsTag', 'replaceInsertTagsBotStatistics');

/*
 * -------------------------------------------------------------------------
 * CRON JOBS
 * -------------------------------------------------------------------------
 */
$GLOBALS['TL_CRON']['daily'][]  = array('BugBuster\BotStatistics\BotStatisticsCron', 'deleteStatisticsData');
