<?php

/**
 * Contao Open Source CMS, Copyright (C) 2005-2018 Leo Feyer
 *
 * Module BotStatistics Stat - Backend
 * Botstatistic details
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

use Contao\CoreBundle\Exception\AccessDeniedException;

/**
 * Class BotStatisticsDetails
 *
 * @copyright  Glen Langer 2012..2018 <http://contao.ninja>
 * @author     Glen Langer (BugBuster)
 */
class BackendStatisticsDetails extends BotStatisticsHelper
{
	/**
	 * Initialize the controller
	 *
	 * 1. Import the user
	 * 2. Call the parent constructor
	 * 3. Authenticate the user
	 * 4. Load the language files
	 * DO NOT CHANGE THIS ORDER!
	 */
	public function __construct()
	{
		$this->import('BackendUser', 'User');
		parent::__construct();
		//$this->User->authenticate(); //deprecated
		if (false === \System::getContainer()->get('contao.security.token_checker')->hasBackendUser()) 
		{
			throw new AccessDeniedException('Access denied');
		}
		\System::loadLanguageFile('default');
		\System::loadLanguageFile('tl_botstatistics');
	}

	public function run()
	{
		/** @var BackendTemplate|object $objTemplate */
		$objTemplate = new \BackendTemplate('mod_botstatistics_be_stat_partial_details');
		$objTemplate->theme         = \Backend::getTheme();
		$objTemplate->base          = \Environment::get('base');
		$objTemplate->language      = $GLOBALS['TL_LANGUAGE'];
		$objTemplate->title         = \StringUtil::specialchars($GLOBALS['TL_LANG']['MSC']['systemMessages']);
		$objTemplate->charset       = \Config::get('characterSet');

		if (null === \Input::get('action', true) ||
			 null === \Input::get('bmid', true))
		{
			$objTemplate->BotDetailList = '<p class="tl_error">' . $GLOBALS['TL_LANG']['tl_botstatistics']['wrong_parameter'] . '</p>';

			return $objTemplate->getResponse();
		}

		switch (\Input::get('action', true))
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
				$DetailFunction = 'getBotStatDetails' . \Input::get('action', true);
				$objTemplate->BotDetailList = $this->$DetailFunction(\Input::get('action', true), \Input::get('bmid', true));
				break;

			default:
				   $objTemplate->BotDetailList = '<p class="tl_error">' . $GLOBALS['TL_LANG']['tl_botstatistics']['wrong_parameter'] . '</p>';
				break;
		}

		return $objTemplate->getResponse();
	}

	// run
}
