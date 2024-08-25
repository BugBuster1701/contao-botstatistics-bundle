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

use Contao\Message;
use Contao\System;

/**
 * Class BotStatisticsCheck
 *
 * @copyright  Glen Langer 2012..2018 <http://contao.ninja>
 */
class BotStatisticsCheck extends System
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
	 * @deprecated 1.1.0
	 */
	public function checkExtensions($strContent, $strTemplate)
	{
		if ($strTemplate == 'be_main')
		{
			$bundles = array_keys(System::getContainer()->getParameter('kernel.bundles')); // old \ModuleLoader::getActive()
			if (!\in_array('BugBusterBotdetectionBundle', $bundles))
			{
				Message::addInfo('Please install the required extension <strong>contao-botdetection-bundle</strong> for the extension contao-botstatistics-bundle.');
			}
		}

		return $strContent;
	}

	// checkExtension
} // class
