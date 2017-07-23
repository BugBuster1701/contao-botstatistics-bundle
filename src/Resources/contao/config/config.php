<?php 

/**
 * Contao Open Source CMS, Copyright (C) 2005-2013 Leo Feyer
 *
 * @copyright  Glen Langer 2012..2013 <http://www.contao.glen-langer.de>
 * @author     Glen Langer (BugBuster)
 * @package    BotStatistics
 * @license    LGPL
 * @filesource
 * @see        https://github.com/BugBuster1701/botstatistics
 */


/**
 * -------------------------------------------------------------------------
 * BACK END MODULES
 * -------------------------------------------------------------------------
 */
$GLOBALS['BE_MOD']['system']['botstatistics'] = array
(
        'callback'   => 'BotStatistics\ModuleBotStatisticsStat',
        'icon'       => 'system/modules/botstatistics/assets/botstatistics2.png',
        'stylesheet' => 'system/modules/botstatistics/assets/mod_botstatistics_be.css',
);


/**
 * -------------------------------------------------------------------------
 * FRONT END MODULES
 * -------------------------------------------------------------------------
 */
array_insert($GLOBALS['FE_MOD']['miscellaneous'], 0, array
(
        'botstatistics' => 'BotStatistics\ModuleBotStatistics',
)); 


/**
 * -------------------------------------------------------------------------
 * HOOKS
 * -------------------------------------------------------------------------
 */
$GLOBALS['TL_HOOKS']['parseBackendTemplate'][]  = array('BotStatistics\BotStatisticsCheck', 'checkExtensions');
$GLOBALS['TL_HOOKS']['replaceInsertTags'][] = array('BotStatistics\ModuleBotStatisticsTag', 'replaceInsertTagsBotStatistics');


/**
 * -------------------------------------------------------------------------
 * CRON JOBS
 * -------------------------------------------------------------------------
 */
$GLOBALS['TL_CRON']['daily'][]  = array('BotStatistics\BotStatisticsCron', 'deleteStatisticsData');

