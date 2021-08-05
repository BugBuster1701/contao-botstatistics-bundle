<?php

/**
 * Contao Open Source CMS, Copyright (C) 2005-2018 Leo Feyer
 *
 * Module BotStatistics Stat - Backend
 * Backend statistics
 *
 * @copyright  Glen Langer 2012..2018 <http://contao.ninja>
 * @author     Glen Langer (BugBuster)
 * @license    LGPL
 * @filesource
 * @see        https://github.com/BugBuster1701/contao-botstatistics-bundle
 */

/**
 * Run in a custom namespace, so the class can be replaced
 */

namespace BugBuster\BotStatistics;

/**
 * Class ModuleBotStatisticsStat
 *
 * @copyright  Glen Langer 2012..2018 <http://contao.ninja>
 * @author     Glen Langer (BugBuster)
 */
class ModuleBotStatisticsStat extends BotStatisticsHelper
{
	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'mod_botstatistics_be_stat';

	/**
	 * Module ID
	 * @var int
	 */
	protected $intModuleID;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();

		$this->intModuleID = (int) \Contao\Input::post('bot_module_id'); //Modul-ID
		//act=zero&zid=...
		if (\Contao\Input::get('act', true)=='zero')
		{
			$this->setZero();
		}
		//for statistics page directly, callback modules use not the template hook
		BotStatisticsCheck::getInstance()->checkExtensions('', 'be_main');
	}

	/**
	 * Generate module
	 */
	protected function compile()
	{
		$this->Template->href   = $this->getReferer(true);
		$this->Template->title  = \Contao\StringUtil::specialchars($GLOBALS['TL_LANG']['MSC']['backBT']);
		$this->Template->button = $GLOBALS['TL_LANG']['MSC']['backBT'];
		$this->Template->theme  = $this->getTheme();
		$this->Template->theme0 = 'default';

		if ($this->intModuleID == 0)
		{   //direkter Aufruf ohne ID
			$objBotModuleID = \Contao\Database::getInstance()
									->prepare("SELECT 
		                                            MIN(id) AS MID 
		                                        FROM 
		                                            tl_module 
		                                        WHERE 
		                                            `type`='botstatistics'
		                                    ")
									->execute();
			$objBotModuleID->next();

			if ($objBotModuleID->MID !== null)
			{
				$this->intModuleID = $objBotModuleID->MID;
			}
		}
		$this->Template->bot_module_id = $this->intModuleID;

		$this->Template->botstatistics_version = $GLOBALS['TL_LANG']['MSC']['tl_botstatistics_stat']['modname'] . ' ' . BOTSTATISTICS_VERSION . '.' . BOTSTATISTICS_BUILD;

		//Modul Namen holen
		$objBotModules = \Contao\Database::getInstance()
								->prepare("SELECT 
		                                        `id`, 
		                                        `botstatistics_name`
                                            FROM 
		                                        `tl_module`
                                            WHERE 
		                                        `type`='botstatistics'
                                            ORDER BY `botstatistics_name`
		                                ")
								->execute();
		$intBotModules = $objBotModules->numRows;

		if ($intBotModules > 0)
		{
			while ($objBotModules->next())
			{
				$arrBotModules[] = array
				(
					'id'    => $objBotModules->id,
					'title' => $objBotModules->botstatistics_name
				);
				//fuer direkten Zugriff
				$arrBotModules2[$objBotModules->id] = $objBotModules->botstatistics_name;
			}
		}
		else
		{ // es gibt kein Modul
			$arrBotModules[] = array
			(
				'id'    => '0',
				'title' => '---------'
			);
			$arrBotModules2 = array();
		}
		$this->Template->bot_modules = $arrBotModules;
		$this->Template->bot_modules2 = $arrBotModules2;

		//Modul Werte holen
		if ($intBotModules > 0)
		{
			$this->Template->BotSummary  = $this->getBotStatSummary();
			$this->Template->BotTopBots  = $this->getTopBots();
			$this->Template->BotTopPages = $this->getTopPages();
		}
	}

	// compile

	/**
	 * Statistic, set on zero
	 */
	protected function setZero()
	{
		if (is_numeric(\Contao\Input::get('zid')) && \Contao\Input::get('zid') > 0)
		{
			$module_id = \Contao\Input::get('zid');
		}
		else
		{
			return; // wrong zid
		}
		\Contao\Database::getInstance()
			->prepare("DELETE FROM 
                            `tl_botstatistics_counter`, 
                            `tl_botstatistics_counter_details` 
                        USING 
                            `tl_botstatistics_counter`, 
                            `tl_botstatistics_counter_details` 
                        WHERE 
                            `tl_botstatistics_counter`.`id` = `tl_botstatistics_counter_details`.`pid`
                        AND 
                            `tl_botstatistics_counter`.`bot_module_id` = ?
                    ")
			->execute($module_id);
	}
}
