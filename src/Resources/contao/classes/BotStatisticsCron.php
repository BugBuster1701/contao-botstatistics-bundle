<?php 

/**
 * Contao Open Source CMS, Copyright (C) 2005-2013 Leo Feyer
 *
 * Module BotStatistics
 * FE - Cronjob
 *
 * @copyright  Glen Langer 2012..2013 <http://www.contao.glen-langer.de>
 * @author     Glen Langer (BugBuster)
 * @package    BotStatistics
 * @license    LGPL
 * @filesource
 * @see        https://github.com/BugBuster1701/botstatistics
 */

/**
 * Run in a custom namespace, so the class can be replaced
 */
namespace BugBuster\BotStatistics;

/**
 * Class BotStatisticsCron 
 *
 * @copyright  Glen Langer 2012..2013 <http://www.contao.glen-langer.de>
 * @author     Glen Langer (BugBuster)
 * @package    BotStatistics
 */
class BotStatisticsCron extends \Frontend
{
	/**
	 * Delete old statistic data
	 * @return string
	 */
	public function deleteStatisticsData()
	{
	    $mindate = date('Y-m-d', mktime(0, 0, 0, date("m"), date("d")-90, date("Y")));
	    
		$objCron = \Database::getInstance()
		                ->prepare("SELECT 
                                        * 
                                    FROM 
                                        `tl_module` 
                                    WHERE 
                                        `type`=? 
                                    AND 
                                        `botstatistics_cron`=?
		                        ")
                        ->execute('botstatistics', 1);
		while ($objCron->next())
		{
    	    \Database::getInstance()
                    ->prepare("DELETE FROM 
                                    `tl_botstatistics_counter`, 
                                    `tl_botstatistics_counter_details`
                                USING 
                                    `tl_botstatistics_counter`, 
                                    `tl_botstatistics_counter_details`
                                WHERE 
                                    `tl_botstatistics_counter`.`id` = `tl_botstatistics_counter_details`.`pid`
                                AND 
                                    `tl_botstatistics_counter`.`bot_module_id`=?
                                AND 
                                    `tl_botstatistics_counter`.`bot_date`<?
                            ")
                    ->execute($objCron->id, $mindate);
    	    // Add log entry
    	    $this->log('Deletion of old Botstatistics data for module '.$objCron->id, 'BotStatisticsCron deleteStatisticsData()', TL_CRON);
		}
	}
	
}//class

