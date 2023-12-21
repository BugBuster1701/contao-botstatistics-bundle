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

/**
 * Run in a custom namespace, so the class can be replaced
 */

namespace BugBuster\BotStatistics;

use Contao\BackendTemplate;
use Contao\Module;
use Contao\System;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class ModuleBotStatistics
 *
 * @copyright  Glen Langer 2012..2018 <http://contao.ninja>
 */
class ModuleBotStatistics extends Module
{
	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'mod_botstatistics_fe';

	/**
	 * Display a wildcard in the back end
	 * @return string
	 */
	public function generate()
	{
		// if (TL_MODE == 'BE')
		if (
			System::getContainer()->get('contao.routing.scope_matcher')
			->isBackendRequest(System::getContainer()->get('request_stack')->getCurrentRequest() ?? Request::create(''))
		) {
			$objTemplate = new BackendTemplate('be_wildcard');
			$objTemplate->wildcard = '### BotStatistics Counter ###';
			$objTemplate->title = $this->headline;
			$objTemplate->id = $this->id;
			$objTemplate->link = $this->name;
			$objTemplate->href = 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $this->id;

			return $objTemplate->parse();
		}

		return parent::generate();
	}

	/**
	 * Generate module
	 */
	protected function compile()
	{
		// global $objPage; // for alias
		$objPage = System::getContainer()->get('request_stack')->getCurrentRequest()->get('pageModel');
		$arrBotStatistics = array();

		$arrBotStatistics['BotStatisticsID'] = $this->id; // Modul ID
		$arrBotStatistics['PageAlias']       = $objPage->alias;

		$this->Template->botstatistics = $arrBotStatistics;
	}
}// class
