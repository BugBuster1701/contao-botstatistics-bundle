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

use Contao\Backend;
use Contao\BackendTemplate;
use Contao\CoreBundle\ContaoCoreBundle;
use Contao\CoreBundle\Exception\AccessDeniedException;
use Contao\Environment;
use Contao\Input;
use Contao\StringUtil;
use Contao\System;

/**
 * Class BotStatisticsDetails
 *
 * @copyright  Glen Langer 2012..2018 <http://contao.ninja>
 */
class BackendStatisticsDetails extends BotStatisticsHelper
{
	/**
	 * Initialize the controller
	 */
	public function __construct()
	{
		// $this->import('BackendUser', 'User');
		parent::__construct();
		// $this->User->authenticate(); //deprecated
		if (false === System::getContainer()->get('contao.security.token_checker')->hasBackendUser())
		{
			throw new AccessDeniedException('Access denied');
		}
		System::loadLanguageFile('default');
		System::loadLanguageFile('tl_botstatistics');
	}

	public function run()
	{
		/** @var BackendTemplate|object $objTemplate */
		$objTemplate = new BackendTemplate('mod_botstatistics_be_stat_partial_details');
		$objTemplate->theme         = Backend::getTheme();
		$objTemplate->base          = Environment::get('base');
		$objTemplate->language      = $GLOBALS['TL_LANGUAGE'];
		$objTemplate->title         = StringUtil::specialchars($GLOBALS['TL_LANG']['MSC']['systemMessages']);
		$objTemplate->charset       = System::getContainer()->getParameter('kernel.charset'); // \Contao\Config::get('characterSet');
		$objTemplate->version       = ContaoCoreBundle::getVersion();

		if (
			null === Input::get('action', true)
			|| null === Input::get('bmid', true)
		) {
			$objTemplate->BotDetailList = '<p class="tl_error">' . $GLOBALS['TL_LANG']['tl_botstatistics']['wrong_parameter'] . '</p>';

			return $objTemplate->getResponse();
		}

		switch (Input::get('action', true))
		{
			case 'AnzBot':
			case 'AnzVisits':
			case 'AnzPages':
			case 'AnzBotToday':
			case 'AnzVisitsToday':
			case 'AnzPagesToday':
			case 'AnzBotYesterday':
			case 'AnzVisitsYesterday':
			case 'AnzPagesYesterday':
			case 'AnzBotWeek':
			case 'AnzVisitsWeek':
			case 'AnzPagesWeek':
			case 'AnzBotLastWeek':
			case 'AnzVisitsLastWeek':
			case 'AnzPagesLastWeek':
				$DetailFunction = 'getBotStatDetails' . Input::get('action', true);
				$objTemplate->BotDetailList = $this->$DetailFunction(Input::get('action', true), Input::get('bmid', true));
				break;

			default:
				$objTemplate->BotDetailList = '<p class="tl_error">' . $GLOBALS['TL_LANG']['tl_botstatistics']['wrong_parameter'] . '</p>';
				break;
		}

		return $objTemplate->getResponse();
	}

	// run
}
