<?php 

/**
 * Contao Open Source CMS, Copyright (C) 2005-2018 Leo Feyer
 *
 * @copyright  Glen Langer 2012..2018 <http://contao.ninja>
 * @author     Glen Langer (BugBuster)
 * @package    BotStatistics
 * @license    LGPL
 * @filesource
 * @see        https://github.com/BugBuster1701/contao-botstatistics-bundle
 */

define('BOTSTATISTICS_VERSION', '1.0');
define('BOTSTATISTICS_BUILD'  , '5');

/**
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


/**
 * -------------------------------------------------------------------------
 * FRONT END MODULES
 * -------------------------------------------------------------------------
 */
array_insert($GLOBALS['FE_MOD']['miscellaneous'], 0, array
(
        'botstatistics' => 'BugBuster\BotStatistics\ModuleBotStatistics',
)); 


/**
 * -------------------------------------------------------------------------
 * HOOKS
 * -------------------------------------------------------------------------
 */
$GLOBALS['TL_HOOKS']['parseBackendTemplate'][]  = array('BugBuster\BotStatistics\BotStatisticsCheck', 'checkExtensions');
$GLOBALS['TL_HOOKS']['replaceInsertTags'][] = array('BugBuster\BotStatistics\ModuleBotStatisticsTag', 'replaceInsertTagsBotStatistics');


/**
 * -------------------------------------------------------------------------
 * CRON JOBS
 * -------------------------------------------------------------------------
 */
$GLOBALS['TL_CRON']['daily'][]  = array('BugBuster\BotStatistics\BotStatisticsCron', 'deleteStatisticsData');

