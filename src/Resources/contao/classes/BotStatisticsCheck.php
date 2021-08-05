<?php

/**
 * Contao Open Source CMS, Copyright (C) 2005-2018 Leo Feyer
 *
 * Module BotStatistics Stat
 * Check the required extensions
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
 * Class BotStatisticsCheck
 *
 * @copyright  Glen Langer 2012..2018 <http://contao.ninja>
 * @author     Glen Langer (BugBuster)
 */
class BotStatisticsCheck extends \Contao\System
{
	/**
	 * Current object instance
	 * @var object
	 */
	protected static $instance;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Return the current object instance (Singleton)
	 * @return BotStatisticsHelper
	 */
	public static function getInstance()
	{
		if (self::$instance === null)
		{
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Hook: Check the required extensions and files for BotStatistics
	 *
	 * @param  string $strContent
	 * @param  string $strTemplate
	 * @return string
	 */
	public function checkExtensions($strContent, $strTemplate)
	{
		if ($strTemplate == 'be_main')
		{
			$bundles = array_keys(\Contao\System::getContainer()->getParameter('kernel.bundles')); // old \ModuleLoader::getActive()
			if (!\in_array('BugBusterBotdetectionBundle', $bundles))
			{
				\Contao\Message::addInfo('Please install the required extension <strong>contao-botdetection-bundle</strong> for the extension contao-botstatistics-bundle.');
			}
		}

		return $strContent;
	}

	// checkExtension
} // class
