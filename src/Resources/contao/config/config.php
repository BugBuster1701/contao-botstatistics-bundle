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

define('BOTSTATISTICS_VERSION', '1.2');
define('BOTSTATISTICS_BUILD', '2');

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

use Contao\System;
use Symfony\Component\HttpFoundation\Request;

if (!System::getContainer()->get('contao.routing.scope_matcher')->isBackendRequest(System::getContainer()->get('request_stack')->getCurrentRequest() ?? Request::create('')))
{
	$GLOBALS['TL_CSS'][] = 'bundles/bugbusterbotstatistics/mod_botstatistics_fe.css';
}

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
