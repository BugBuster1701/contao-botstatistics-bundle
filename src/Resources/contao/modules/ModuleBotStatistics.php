<?php
 
/**
 * Contao Open Source CMS, Copyright (C) 2005-2013 Leo Feyer
 *
 * Module BotStatistics - Frontend
 * Insert counting tag in the page.
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
 * Class ModuleBotStatistics 
 *
 * @copyright  Glen Langer 2012..2013 <http://www.contao.glen-langer.de>
 * @author     Glen Langer (BugBuster)
 * @package    BotStatistics
 */
class ModuleBotStatistics extends \Module
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
	    if (TL_MODE == 'BE')
	    {
	        $objTemplate = new \BackendTemplate('be_wildcard');
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
	    global $objPage; // for alias
	    $arrBotStatistics = array();
	    
	    $arrBotStatistics['BotStatisticsID'] = $this->id; // Modul ID
	    $arrBotStatistics['PageAlias']       = $objPage->alias;

	    $this->Template->botstatistics = $arrBotStatistics;
	}
	
}//class

