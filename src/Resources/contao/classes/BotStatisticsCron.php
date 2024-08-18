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

/**
 * Run in a custom namespace, so the class can be replaced
 */

namespace BugBuster\BotStatistics;

use Contao\CoreBundle\Monolog\ContaoContext;
use Contao\Database;
use Contao\Frontend;
use Contao\System;
use Psr\Log\LogLevel;

/**
 * Class BotStatisticsCron
 *
 * @copyright  Glen Langer 2012..2018 <http://contao.ninja>
 */
class BotStatisticsCron extends Frontend
{
	/**
	 * Delete old statistic data
	 * @return string
	 */
	public function deleteStatisticsData()
	{
		$mindate = date('Y-m-d', mktime(0, 0, 0, date("m"), date("d")-90, date("Y")));

		$objCron = Database::getInstance()
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
			Database::getInstance()
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
			System::getContainer()
						->get('monolog.logger.contao')
						->log(
							LogLevel::INFO,
							'Deletion of old Botstatistics data for module ' . $objCron->id,
							array('contao' => new ContaoContext('BotStatisticsCron deleteStatisticsData()', ContaoContext::CRON))
						);
		}
	}
}// class
