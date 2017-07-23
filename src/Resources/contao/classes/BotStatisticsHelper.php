<?php 

/**
 * Contao Open Source CMS, Copyright (C) 2005-2013 Leo Feyer
 *
 * Module BotStatistics Stat
 * Helper class 
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
 * Class BotStatisticsHelper
 *
 * @copyright  Glen Langer 2012..2013 <http://www.contao.glen-langer.de>
 * @author     Glen Langer (BugBuster)
 * @package    BotStatistics
 */
class BotStatisticsHelper extends \BackendModule
{
    /**
	 * Current object instance
	 * @var object
	 */
    protected static $instance = null;
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->import('BackendUser', 'User');
        parent::__construct();
    }
    
    
    protected function compile()
    {
        
    }
    /**
     * Return the current object instance (Singleton)
     * @return BotStatisticsHelper
     */
    public static function getInstance()
    {
        if (self::$instance == null)
        {
            self::$instance = new BotStatisticsHelper();
        }
    
        return self::$instance;
    }

    
    /**
     * Timestamp nach Datum in deutscher oder internationaler Schreibweise
     *
     * @param	string		$language
     * @param	insteger	$intTstamp
     * @return	string
     */
    protected function parseDateBots($language='en', $intTstamp=null)
    {
        if ($language == 'de')
        {
            $strModified = 'd.m.Y';
        }
        else
        {
            $strModified = 'Y-m-d';
        }
        if (is_null($intTstamp))
        {
            $strDate = date($strModified);
        }
        elseif (!is_numeric($intTstamp))
        {
            return '';
        }
        else
        {
            $strDate = date($strModified, $intTstamp);
        }
        return $strDate;
    }
    
    protected function getModulName($bmid)
    {
        //Modul Namen holen
        $objBotModules = \Database::getInstance()
                                ->prepare("SELECT 
                                                `botstatistics_name`
                                            FROM 
                                                `tl_module`
                                            WHERE 
                                                `type`='botstatistics'
                                            AND 
                                                `id`=?
                                        ")
                                ->execute($bmid);
        return $objBotModules->botstatistics_name;
    }
    
    /*     _____                                           
          / ___/__  ______ ___  ____ ___  ____ ________  __
          \__ \/ / / / __ `__ \/ __ `__ \/ __ `/ ___/ / / /
         ___/ / /_/ / / / / / / / / / / / /_/ / /  / /_/ / 
        /____/\__,_/_/ /_/ /_/_/ /_/ /_/\__,_/_/   \__, /  
                                                  /____/   
    */
    protected function getBotStatSummary()
    {
        $today     = date('Y-m-d');
        $yesterday = date('Y-m-d', mktime(0, 0, 0, date("m"), date("d")-1, date("Y")));

        $this->TemplatePartial = new \BackendTemplate('mod_botstatistics_be_stat_partial_summary');
        
        $this->TemplatePartial->AnzBotYesterday    = 0;
        $this->TemplatePartial->AnzVisitsYesterday = 0;
        $this->TemplatePartial->AnzPagesYesterday  = 0;
        $this->TemplatePartial->AnzBotToday        = 0;
        $this->TemplatePartial->AnzVisitsToday     = 0;
        $this->TemplatePartial->AnzPagesToday      = 0;
        $this->TemplatePartial->bot_module_id      = $this->intModuleID;
        $this->TemplatePartial->theme              = $this->getTheme();
        
        //Anzahl der Bots mit Summe Besuche und Seitenzugriffe
        $objBotStatCount = \Database::getInstance()
                                ->prepare("SELECT 
                                                count(distinct `bot_name`) AS AnzBot, 
                                                    (SELECT 
                                                        sum(`bot_counter`) 
                                                     FROM 
                                                        `tl_botstatistics_counter` 
                                                     WHERE 
                                                        `bot_module_id`=?
                                                     ) AS AnzVisits
                                            FROM 
                                                `tl_botstatistics_counter` 
                                            WHERE 
                                                `bot_module_id`=?
                                        ")
                                ->execute($this->intModuleID, $this->intModuleID);
        $this->TemplatePartial->AnzBot    = $objBotStatCount->AnzBot;
        $this->TemplatePartial->AnzVisits = ($objBotStatCount->AnzVisits) ? $objBotStatCount->AnzVisits : 0;
        //Anzahl Seitenzugriffe
        $objBotStatCount = \Database::getInstance()
                                ->prepare("SELECT 
                                                sum(`bot_page_alias_counter`) AS AnzPages
                                            FROM 
                                                `tl_botstatistics_counter_details` d
                                            INNER JOIN 
                                                `tl_botstatistics_counter` c ON d.pid = c.id
                                            WHERE 
                                                c.`bot_module_id`=?
                                        ")
                                ->execute($this->intModuleID);
        $this->TemplatePartial->AnzPages = ($objBotStatCount->AnzPages) ? $objBotStatCount->AnzPages : 0;
        
        //Anzahl Bots Heute/Gestern Besuche/Hits 
        $objBotStatCount = \Database::getInstance()
                                ->prepare("SELECT 
                                                `bot_date`, 
                                                count(distinct `bot_name`) AS AnzBot, 
                                                sum(`bot_counter`) AS AnzVisits
                                            FROM 
                                                `tl_botstatistics_counter`
                                            WHERE 
                                                `bot_module_id`=?
                                            AND 
                                                `bot_date`>=?
                                            GROUP BY 
                                                `bot_date`
                                        ")
                                ->execute($this->intModuleID, $yesterday);
        while ($objBotStatCount->next())
        {
            if ($objBotStatCount->bot_date == $yesterday)
            {
                $this->TemplatePartial->AnzBotYesterday    = $objBotStatCount->AnzBot;
                $this->TemplatePartial->AnzVisitsYesterday = $objBotStatCount->AnzVisits;
                
            }
            if ($objBotStatCount->bot_date == $today) 
            { 
                $this->TemplatePartial->AnzBotToday    = $objBotStatCount->AnzBot;
                $this->TemplatePartial->AnzVisitsToday = $objBotStatCount->AnzVisits;
            }
        }
        // Anzahl Seiten Gesamt - Heute/Gestern
        $objBotStatCount = \Database::getInstance()
                                ->prepare("SELECT 
                                                `bot_date`, 
                                                sum(`bot_page_alias_counter`) AS AnzPages 
                                            FROM 
                                                `tl_botstatistics_counter`
                                            INNER JOIN 
                                                `tl_botstatistics_counter_details` ON `tl_botstatistics_counter`.id=`tl_botstatistics_counter_details`.pid
                                            WHERE 
                                                `bot_module_id`=? 
                                            AND 
                                                `bot_date`>=?
                                            GROUP BY 
                                                `bot_date`
                                        ")
                                ->execute($this->intModuleID, $yesterday);
        while ($objBotStatCount->next())
        {
            if ($objBotStatCount->bot_date == $yesterday)
            {
                $this->TemplatePartial->AnzPagesYesterday = $objBotStatCount->AnzPages;
        
            }
            if ($objBotStatCount->bot_date == $today)
            {
                $this->TemplatePartial->AnzPagesToday = $objBotStatCount->AnzPages;
            }
        }
        
        //Anzahl Besuche aktuelle Woche, letzte Woche
        $this->TemplatePartial->AnzBotWeek    = 0;
        $this->TemplatePartial->AnzVisitsWeek = 0;
        $this->TemplatePartial->AnzPagesWeek  = 0;
        $this->TemplatePartial->AnzBotLastWeek    = 0;
        $this->TemplatePartial->AnzVisitsLastWeek = 0;
        $this->TemplatePartial->AnzPagesLastWeek  = 0;

        $CurrentWeek     = date('W'); 
        $LastWeek        = date('W', mktime(0, 0, 0, date("m"), date("d")-7, date("Y")) );
        //Besonderheit beachten das der 1.1. die 53. Woche sein kann!
        $YearCurrentWeek = ($CurrentWeek > 40 && (int)date('m') == 1) ? date('Y')-1 : date('Y');
        $YearLastWeek    = ($LastWeek    > 40 && (int)date('m') == 1) ? date('Y')-1 : date('Y');
        $objBotStatCount = \Database::getInstance()
                                ->prepare("SELECT 
                                                YEARWEEK( `bot_date`, 3 ) AS YW, 
                                                COUNT(DISTINCT `bot_name`) AS AnzBotWeek, 
                                                SUM(`bot_counter`) AS AnzVisitsWeek 
                                            FROM 
                                                `tl_botstatistics_counter`
                                            WHERE 
                                                `bot_module_id`=?
                                            AND 
                                                YEARWEEK( `bot_date`, 3 ) BETWEEN ? AND ?
                                            GROUP BY YW
                                            ORDER BY YW DESC
                                        ")
                                ->execute($this->intModuleID, 
                                          $YearLastWeek.$LastWeek, 
                                          $YearCurrentWeek.$CurrentWeek);
        while ($objBotStatCount->next())
        {
            if ($objBotStatCount->YW == $YearCurrentWeek.$CurrentWeek)
            {
                $this->TemplatePartial->AnzBotWeek    = $objBotStatCount->AnzBotWeek;
                $this->TemplatePartial->AnzVisitsWeek = $objBotStatCount->AnzVisitsWeek;
            
            }
            if ($objBotStatCount->YW == $YearLastWeek.$LastWeek)
            {
                $this->TemplatePartial->AnzBotLastWeek    = $objBotStatCount->AnzBotWeek;
                $this->TemplatePartial->AnzVisitsLastWeek = $objBotStatCount->AnzVisitsWeek;
            }
        }
        //Anzahl Hits aktuelle, letzte Woche
        $objBotStatCount = \Database::getInstance()
                                ->prepare("SELECT 
                                                YEARWEEK( c.`bot_date`, 3 ) AS YW, 
                                                sum(d.`bot_page_alias_counter`) AS AnzPages
                                            FROM 
                                                `tl_botstatistics_counter` c
                                            INNER JOIN  
                                                `tl_botstatistics_counter_details` d ON c.id=d.pid
                                            WHERE 
                                                c.`bot_module_id`=?
                                            AND 
                                                YEARWEEK( c.`bot_date`, 3 ) BETWEEN ? AND ?
                                            GROUP BY YW
                                            ORDER BY YW DESC
                                        ")
                                ->execute($this->intModuleID, $YearLastWeek.$LastWeek, $YearCurrentWeek.$CurrentWeek);
        while ($objBotStatCount->next())
        {
            if ($objBotStatCount->YW == $YearCurrentWeek.$CurrentWeek)
            {
                $this->TemplatePartial->AnzPagesWeek = $objBotStatCount->AnzPages;
        
            }
            if ($objBotStatCount->YW == $YearLastWeek.$LastWeek)
            {
                $this->TemplatePartial->AnzPagesLastWeek = $objBotStatCount->AnzPages;
            }
        }
        
        return $this->TemplatePartial->parse();
    }
    
    protected function getBotStatDetailsAnzBot($action,$bmid)
    {
        $this->TemplatePartial = new \BackendTemplate('mod_botstatistics_be_stat_partial_details');
        $this->TemplatePartial->action = $action;
        $this->TemplatePartial->bmid = $bmid;
        $this->TemplatePartial->BotDetailList  = '<div class="tl_listing_container list_view">'."\n";
        $this->TemplatePartial->BotDetailList .= '<table class="tl_listing"><tbody><tr><td class="tl_folder_tlist">'.$this->getModulName($bmid).'</td></tr>'."\n";
        
        $objBotStatDetailsAnzBot = \Database::getInstance()
                                        ->prepare("SELECT DISTINCT 
                                                        `bot_name` 
                                                    FROM 
                                                        `tl_botstatistics_counter`
                                                    WHERE 
                                                        `bot_module_id`=?
                                                    ORDER BY `bot_name`
                                                ")
                                        ->execute($bmid);
        while ($objBotStatDetailsAnzBot->next()) 
        {
            $this->TemplatePartial->BotDetailList .= '<tr><td class="tl_file_list">'.$objBotStatDetailsAnzBot->bot_name.'</td></tr>';
        }
        $this->TemplatePartial->BotDetailList .= '</tbody></table></div>';
        return $this->TemplatePartial->parse();
    }
    
    protected function getBotStatDetailsAnzVisits($action,$bmid)
    {
        $this->TemplatePartial = new \BackendTemplate('mod_botstatistics_be_stat_partial_details');
        $this->TemplatePartial->action = $action;
        $this->TemplatePartial->bmid = $bmid;
        $this->TemplatePartial->BotDetailList  = '<div class="tl_listing_container list_view">'."\n";
        $this->TemplatePartial->BotDetailList .= '<table class="tl_listing"><tbody><tr><td class="tl_folder_tlist">'.$this->getModulName($bmid).'</td><td class="tl_folder_tlist tl_right_nowrap">'.$GLOBALS['TL_LANG']['MSC']['tl_botstatistics_stat']['number_visit'].'</td></tr>'."\n";
        
        $objBotStatDetailsAnzBot = \Database::getInstance()
                                        ->prepare("SELECT DISTINCT 
                                                        `bot_name`, sum(`bot_counter`) AS AnzVisits
                                                    FROM 
                                                        `tl_botstatistics_counter`
                                                    WHERE 
                                                        `bot_module_id`=?
                                                    GROUP BY `bot_name`
                                                    ORDER BY AnzVisits DESC
                                                ")
                                        ->execute($bmid);
        while ($objBotStatDetailsAnzBot->next())
        {
            $this->TemplatePartial->BotDetailList .= '<tr><td class="tl_file_list">'.$objBotStatDetailsAnzBot->bot_name.'</td><td class="tl_file_list tl_right_nowrap">'.$objBotStatDetailsAnzBot->AnzVisits.'</td></tr>';
        }
        $this->TemplatePartial->BotDetailList .= '</tbody></table></div>';
        return $this->TemplatePartial->parse();
    }
    protected function getBotStatDetailsAnzPages($action,$bmid)
    {
        $this->TemplatePartial = new \BackendTemplate('mod_botstatistics_be_stat_partial_details');
        $this->TemplatePartial->action = $action;
        $this->TemplatePartial->bmid = $bmid;
        $this->TemplatePartial->BotDetailList  = '<div class="tl_listing_container list_view">'."\n";
        $this->TemplatePartial->BotDetailList .= '<table class="tl_listing"><tbody><tr><td class="tl_folder_tlist">'.$this->getModulName($bmid).'</td><td class="tl_folder_tlist tl_right_nowrap">'.$GLOBALS['TL_LANG']['MSC']['tl_botstatistics_stat']['number_hit'].'</td></tr>'."\n";
        
        $objBotStatDetailsAnzBot = \Database::getInstance()
                                        ->prepare("SELECT 
                                                        c.`bot_name`, sum(d.`bot_page_alias_counter`) AS AnzPages
                                                    FROM 
                                                        `tl_botstatistics_counter_details` d
                                                    INNER JOIN 
                                                        `tl_botstatistics_counter` c ON d.pid = c.id
                                                    WHERE 
                                                        c.`bot_module_id`=?
                                                    GROUP BY c.`bot_name`
                                                    ORDER BY AnzPages DESC
                                                ")
                                        ->execute($bmid);
        while ($objBotStatDetailsAnzBot->next())
        {
            $this->TemplatePartial->BotDetailList .= '<tr><td class="tl_file_list">'.$objBotStatDetailsAnzBot->bot_name.'</td><td class="tl_file_list tl_right_nowrap">'.$objBotStatDetailsAnzBot->AnzPages.'</td></tr>';
        }
        $this->TemplatePartial->BotDetailList .= '</tbody></table></div>';
        
        return $this->TemplatePartial->parse();
    }
    /*    ______          __           
         /_  __/___  ____/ /___ ___  __
          / / / __ \/ __  / __ `/ / / /
         / / / /_/ / /_/ / /_/ / /_/ / 
        /_/  \____/\__,_/\__,_/\__, /  
                              /____/
    */
    protected function getBotStatDetailsAnzBotToday($action,$bmid)
    {
        $today = date('Y-m-d');
        $this->TemplatePartial = new \BackendTemplate('mod_botstatistics_be_stat_partial_details');
        $this->TemplatePartial->action = $action;
        $this->TemplatePartial->bmid = $bmid;
        $this->TemplatePartial->BotDetailList  = '<div class="tl_listing_container list_view">'."\n";
        $this->TemplatePartial->BotDetailList .= '<table class="tl_listing"><tbody><tr><td class="tl_folder_tlist">'.$this->getModulName($bmid).': '.$GLOBALS['TL_LANG']['MSC']['tl_botstatistics_stat']['today'].'</td></tr>'."\n";
        
        $objBotStatDetailsAnzBotToday = \Database::getInstance()
                                            ->prepare("SELECT DISTINCT 
                                                            `bot_name`
                                                        FROM 
                                                            `tl_botstatistics_counter`
                                                        WHERE 
                                                            `bot_module_id`=?
                                                        AND 
                                                            `bot_date`=?
                                                        ORDER BY `bot_name`
                                                    ")
                                            ->execute($bmid, $today);
        while ($objBotStatDetailsAnzBotToday->next())
        {
            $this->TemplatePartial->BotDetailList .= '<tr><td class="tl_file_list">'.$objBotStatDetailsAnzBotToday->bot_name.'</td></tr>';
        }
        $this->TemplatePartial->BotDetailList .= '</tbody></table></div>';
        
        return $this->TemplatePartial->parse();
    }
    protected function getBotStatDetailsAnzVisitsToday($action,$bmid)
    {
        $today = date('Y-m-d');
        $this->TemplatePartial = new \BackendTemplate('mod_botstatistics_be_stat_partial_details');
        $this->TemplatePartial->action = $action;
        $this->TemplatePartial->bmid = $bmid;
        $this->TemplatePartial->BotDetailList  = '<div class="tl_listing_container list_view">'."\n";
        $this->TemplatePartial->BotDetailList .= '<table class="tl_listing"><tbody><tr><td class="tl_folder_tlist">'.$this->getModulName($bmid).': '.$GLOBALS['TL_LANG']['MSC']['tl_botstatistics_stat']['today'].'</td><td class="tl_folder_tlist tl_right_nowrap">'.$GLOBALS['TL_LANG']['MSC']['tl_botstatistics_stat']['number_visit'].'</td></tr>'."\n";
        
        $objBotStatDetailsAnzVisitsToday = \Database::getInstance()
                                                ->prepare("SELECT DISTINCT 
                                                                `bot_name`, sum(`bot_counter`) AS AnzVisits
                                                            FROM 
                                                                `tl_botstatistics_counter`
                                                            WHERE 
                                                                `bot_module_id`=?
                                                            AND 
                                                                `bot_date`=?
                                                            GROUP BY `bot_name`
                                                            ORDER BY AnzVisits DESC
                                                        ")
                                                ->execute($bmid, $today);
        while ($objBotStatDetailsAnzVisitsToday->next())
        {
            $this->TemplatePartial->BotDetailList .= '<tr><td class="tl_file_list">'.$objBotStatDetailsAnzVisitsToday->bot_name.'</td><td class="tl_file_list tl_right_nowrap">'.$objBotStatDetailsAnzVisitsToday->AnzVisits.'</td></tr>';
        }
        $this->TemplatePartial->BotDetailList .= '</tbody></table></div>';
        return $this->TemplatePartial->parse();
    }
    protected function getBotStatDetailsAnzPagesToday($action,$bmid)
    {
        $today = date('Y-m-d');
        $this->TemplatePartial = new \BackendTemplate('mod_botstatistics_be_stat_partial_details');
        $this->TemplatePartial->action = $action;
        $this->TemplatePartial->bmid = $bmid;
        $this->TemplatePartial->BotDetailList  = '<div class="tl_listing_container list_view">'."\n";
        $this->TemplatePartial->BotDetailList .= '<table class="tl_listing"><tbody><tr><td class="tl_folder_tlist">'.$this->getModulName($bmid).': '.$GLOBALS['TL_LANG']['MSC']['tl_botstatistics_stat']['today'].'</td><td class="tl_folder_tlist tl_right_nowrap">'.$GLOBALS['TL_LANG']['MSC']['tl_botstatistics_stat']['number_hit'].'</td></tr>'."\n";
        
        $objBotStatDetailsAnzBotToday = \Database::getInstance()
                                            ->prepare("SELECT 
                                                            c.`bot_name`, sum(d.`bot_page_alias_counter`) AS AnzPages
                                                        FROM 
                                                            `tl_botstatistics_counter_details` d
                                                        INNER JOIN 
                                                            `tl_botstatistics_counter` c ON d.pid = c.id
                                                        WHERE 
                                                            c.`bot_module_id`=?
                                                        AND 
                                                            `bot_date`=?
                                                        GROUP BY c.`bot_name`
                                                        ORDER BY AnzPages DESC
                                                    ")
                                            ->execute($bmid, $today);
        while ($objBotStatDetailsAnzBotToday->next())
        {
            $this->TemplatePartial->BotDetailList .= '<tr><td class="tl_file_list">'.$objBotStatDetailsAnzBotToday->bot_name.'</td><td class="tl_file_list tl_right_nowrap">'.$objBotStatDetailsAnzBotToday->AnzPages.'</td></tr>';
        }
        $this->TemplatePartial->BotDetailList .= '</tbody></table></div>';
        return $this->TemplatePartial->parse();
    }

    /*  __  __          __                __           
        \ \/ /__  _____/ /____  _________/ /___ ___  __
         \  / _ \/ ___/ __/ _ \/ ___/ __  / __ `/ / / /
         / /  __(__  ) /_/  __/ /  / /_/ / /_/ / /_/ / 
        /_/\___/____/\__/\___/_/   \__,_/\__,_/\__, /  
                                              /____/   
    */
    protected function getBotStatDetailsAnzBotYesterday($action,$bmid)
    {
        $yesterday = date('Y-m-d', mktime(0, 0, 0, date("m"), date("d")-1, date("Y")));
        $this->TemplatePartial = new \BackendTemplate('mod_botstatistics_be_stat_partial_details');
        $this->TemplatePartial->action = $action;
        $this->TemplatePartial->bmid = $bmid;
        $this->TemplatePartial->BotDetailList  = '<div class="tl_listing_container list_view">'."\n";
        $this->TemplatePartial->BotDetailList .= '<table class="tl_listing"><tbody><tr><td class="tl_folder_tlist">'.$this->getModulName($bmid).': '.$GLOBALS['TL_LANG']['MSC']['tl_botstatistics_stat']['yesterday'].'</td></tr>'."\n";
        
        $objBotStatDetailsAnzBotYesterday = \Database::getInstance()
                                                ->prepare("SELECT DISTINCT 
                                                                `bot_name`
                                                          FROM 
                                                                `tl_botstatistics_counter`
                                                          WHERE 
                                                                `bot_module_id`=?
                                                          AND 
                                                                `bot_date`=? 
                                                          ORDER BY `bot_name`
                                                        ")
                                                ->execute($bmid, $yesterday);
        while ($objBotStatDetailsAnzBotYesterday->next())
        {
            $this->TemplatePartial->BotDetailList .= '<tr><td class="tl_file_list">'.$objBotStatDetailsAnzBotYesterday->bot_name.'</td></tr>';
        }
        $this->TemplatePartial->BotDetailList .= '</tbody></table></div>';
        
        return $this->TemplatePartial->parse();
    }
    protected function getBotStatDetailsAnzVisitsYesterday($action,$bmid)
    {
        $yesterday = date('Y-m-d', mktime(0, 0, 0, date("m"), date("d")-1, date("Y")));
        $this->TemplatePartial = new \BackendTemplate('mod_botstatistics_be_stat_partial_details');
        $this->TemplatePartial->action = $action;
        $this->TemplatePartial->bmid = $bmid;
        $this->TemplatePartial->BotDetailList  = '<div class="tl_listing_container list_view">'."\n";
        $this->TemplatePartial->BotDetailList .= '<table class="tl_listing"><tbody><tr><td class="tl_folder_tlist">'.$this->getModulName($bmid).': '.$GLOBALS['TL_LANG']['MSC']['tl_botstatistics_stat']['yesterday'].'</td><td class="tl_folder_tlist tl_right_nowrap">'.$GLOBALS['TL_LANG']['MSC']['tl_botstatistics_stat']['number_visit'].'</td></tr>'."\n";
        
        $objBotStatDetailsAnzVisitsYesterday = \Database::getInstance()
                                                    ->prepare("SELECT DISTINCT 
                                                                    `bot_name`, sum(`bot_counter`) AS AnzVisits
                                                                FROM 
                                                                    `tl_botstatistics_counter`
                                                                WHERE 
                                                                    `bot_module_id`=?
                                                                AND 
                                                                    `bot_date`=?
                                                                GROUP BY `bot_name`
                                                                ORDER BY AnzVisits DESC
                                                            ")
                                                    ->execute($bmid, $yesterday);
        while ($objBotStatDetailsAnzVisitsYesterday->next())
        {
            $this->TemplatePartial->BotDetailList .= '<tr><td class="tl_file_list">'.$objBotStatDetailsAnzVisitsYesterday->bot_name.'</td><td class="tl_file_list tl_right_nowrap">'.$objBotStatDetailsAnzVisitsYesterday->AnzVisits.'</td></tr>';
        }
        $this->TemplatePartial->BotDetailList .= '</tbody></table></div>';
        return $this->TemplatePartial->parse();
    }
    protected function getBotStatDetailsAnzPagesYesterday($action,$bmid)
    {
        $yesterday = date('Y-m-d', mktime(0, 0, 0, date("m"), date("d")-1, date("Y")));
        $this->TemplatePartial = new \BackendTemplate('mod_botstatistics_be_stat_partial_details');
        $this->TemplatePartial->action = $action;
        $this->TemplatePartial->bmid = $bmid;
        $this->TemplatePartial->BotDetailList  = '<div class="tl_listing_container list_view">'."\n";
        $this->TemplatePartial->BotDetailList .= '<table class="tl_listing"><tbody><tr><td class="tl_folder_tlist">'.$this->getModulName($bmid).': '.$GLOBALS['TL_LANG']['MSC']['tl_botstatistics_stat']['yesterday'].'</td><td class="tl_folder_tlist tl_right_nowrap">'.$GLOBALS['TL_LANG']['MSC']['tl_botstatistics_stat']['number_hit'].'</td></tr>'."\n";
        
        $objBotStatDetailsAnzBotYesterday = \Database::getInstance()
                                                ->prepare("SELECT 
                                                                c.`bot_name`, sum(d.`bot_page_alias_counter`) AS AnzPages
                                                            FROM 
                                                                `tl_botstatistics_counter_details` d
                                                            INNER JOIN 
                                                                `tl_botstatistics_counter` c ON d.pid = c.id
                                                            WHERE 
                                                                c.`bot_module_id`=?
                                                            AND 
                                                                `bot_date`=?
                                                            GROUP BY c.`bot_name`
                                                            ORDER BY AnzPages DESC
                                                        ")
                                                ->execute($bmid, $yesterday);
        while ($objBotStatDetailsAnzBotYesterday->next())
        {
            $this->TemplatePartial->BotDetailList .= '<tr><td class="tl_file_list">'.$objBotStatDetailsAnzBotYesterday->bot_name.'</td><td class="tl_file_list tl_right_nowrap">'.$objBotStatDetailsAnzBotYesterday->AnzPages.'</td></tr>';
        }
        $this->TemplatePartial->BotDetailList .= '</tbody></table></div>';
        return $this->TemplatePartial->parse();
    }
    /*   _       __          __  
        | |     / /__  ___  / /__
        | | /| / / _ \/ _ \/ //_/
        | |/ |/ /  __/  __/ ,<   
        |__/|__/\___/\___/_/|_|  
    */                     
    protected function getBotStatDetailsAnzBotWeek($action,$bmid)
    {
        $CurrentWeek = date('W');
        //Besonderheit beachten das der 1.1. die 53. Woche sein kann!
        $YearCurrentWeek = ($CurrentWeek > 40 && (int)date('m') == 1) ? date('Y')-1 : date('Y');
        $this->TemplatePartial = new \BackendTemplate('mod_botstatistics_be_stat_partial_details');
        $this->TemplatePartial->action = $action;
        $this->TemplatePartial->bmid = $bmid;
        $this->TemplatePartial->BotDetailList  = '<div class="tl_listing_container list_view">'."\n";
        $this->TemplatePartial->BotDetailList .= '<table class="tl_listing"><tbody><tr><td class="tl_folder_tlist">'.$this->getModulName($bmid).': '.$GLOBALS['TL_LANG']['MSC']['tl_botstatistics_stat']['current_week'].'</td></tr>'."\n";
        
        $objBotStatDetailsAnzBotWeek = \Database::getInstance()
                                            ->prepare("SELECT DISTINCT 
                                                            `bot_name`
                                                         FROM 
                                                            `tl_botstatistics_counter`
                                                         WHERE 
                                                            `bot_module_id`=?
                                                         AND 
                                                            YEARWEEK( `bot_date`, 3 ) =?
                                                         ORDER BY `bot_name`
                                                    ")
                                            ->execute($bmid, $YearCurrentWeek.$CurrentWeek);
        while ($objBotStatDetailsAnzBotWeek->next())
        {
            $this->TemplatePartial->BotDetailList .= '<tr><td class="tl_file_list">'.$objBotStatDetailsAnzBotWeek->bot_name.'</td></tr>';
        }
        $this->TemplatePartial->BotDetailList .= '</tbody></table></div>';
        
        return $this->TemplatePartial->parse();
    }
    protected function getBotStatDetailsAnzVisitsWeek($action,$bmid)
    {
        $CurrentWeek = date('W');
        //Besonderheit beachten das der 1.1. die 53. Woche sein kann!
        $YearCurrentWeek = ($CurrentWeek > 40 && (int)date('m') == 1) ? date('Y')-1 : date('Y');
        $this->TemplatePartial = new \BackendTemplate('mod_botstatistics_be_stat_partial_details');
        $this->TemplatePartial->action = $action;
        $this->TemplatePartial->bmid = $bmid;
        $this->TemplatePartial->BotDetailList  = '<div class="tl_listing_container list_view">'."\n";
        $this->TemplatePartial->BotDetailList .= '<table class="tl_listing"><tbody><tr><td class="tl_folder_tlist">'.$this->getModulName($bmid).': '.$GLOBALS['TL_LANG']['MSC']['tl_botstatistics_stat']['current_week'].'</td><td class="tl_folder_tlist tl_right_nowrap">'.$GLOBALS['TL_LANG']['MSC']['tl_botstatistics_stat']['number_visit'].'</td></tr>'."\n";
        
        $objBotStatDetailsAnzVisitsWeek = \Database::getInstance()
                                                ->prepare("SELECT DISTINCT 
                                                                `bot_name`, sum(`bot_counter`) AS AnzVisits
                                                            FROM 
                                                                `tl_botstatistics_counter`
                                                            WHERE 
                                                                `bot_module_id`=?
                                                            AND 
                                                                YEARWEEK( `bot_date`, 3 ) =?
                                                            GROUP BY `bot_name`
                                                            ORDER BY AnzVisits DESC
                                                        ")
                                                ->execute($bmid, $YearCurrentWeek.$CurrentWeek);
        while ($objBotStatDetailsAnzVisitsWeek->next())
        {
            $this->TemplatePartial->BotDetailList .= '<tr><td class="tl_file_list">'.$objBotStatDetailsAnzVisitsWeek->bot_name.'</td><td class="tl_file_list tl_right_nowrap">'.$objBotStatDetailsAnzVisitsWeek->AnzVisits.'</td></tr>';
        }
        $this->TemplatePartial->BotDetailList .= '</tbody></table></div>';
        return $this->TemplatePartial->parse();
    }
    protected function getBotStatDetailsAnzPagesWeek($action,$bmid)
    {
        $CurrentWeek = date('W');
        //Besonderheit beachten das der 1.1. die 53. Woche sein kann!
        $YearCurrentWeek = ($CurrentWeek > 40 && (int)date('m') == 1) ? date('Y')-1 : date('Y');
        $this->TemplatePartial = new \BackendTemplate('mod_botstatistics_be_stat_partial_details');
        $this->TemplatePartial->action = $action;
        $this->TemplatePartial->bmid = $bmid;
        $this->TemplatePartial->BotDetailList  = '<div class="tl_listing_container list_view">'."\n";
        $this->TemplatePartial->BotDetailList .= '<table class="tl_listing"><tbody><tr><td class="tl_folder_tlist">'.$this->getModulName($bmid).': '.$GLOBALS['TL_LANG']['MSC']['tl_botstatistics_stat']['current_week'].'</td><td class="tl_folder_tlist tl_right_nowrap">'.$GLOBALS['TL_LANG']['MSC']['tl_botstatistics_stat']['number_hit'].'</td></tr>'."\n";
        
        $objBotStatDetailsAnzBotWeek = \Database::getInstance()
                                            ->prepare("SELECT 
                                                            c.`bot_name`, sum(d.`bot_page_alias_counter`) AS AnzPages
                                                        FROM 
                                                            `tl_botstatistics_counter_details` d
                                                        INNER JOIN 
                                                            `tl_botstatistics_counter` c ON d.pid = c.id
                                                        WHERE 
                                                            c.`bot_module_id`=?
                                                        AND 
                                                            YEARWEEK( `bot_date`, 3 ) =?
                                                        GROUP BY c.`bot_name`
                                                        ORDER BY AnzPages DESC
                                                    ")
                                            ->execute($bmid, $YearCurrentWeek.$CurrentWeek);
        while ($objBotStatDetailsAnzBotWeek->next())
        {
            $this->TemplatePartial->BotDetailList .= '<tr><td class="tl_file_list">'.$objBotStatDetailsAnzBotWeek->bot_name.'</td><td class="tl_file_list tl_right_nowrap">'.$objBotStatDetailsAnzBotWeek->AnzPages.'</td></tr>';
        }
        $this->TemplatePartial->BotDetailList .= '</tbody></table></div>';
        return $this->TemplatePartial->parse();
    }

    /*      __               __     _       __          __  
           / /   ____ ______/ /_   | |     / /__  ___  / /__
          / /   / __ `/ ___/ __/   | | /| / / _ \/ _ \/ //_/
         / /___/ /_/ (__  ) /_     | |/ |/ /  __/  __/ ,<   
        /_____/\__,_/____/\__/     |__/|__/\___/\___/_/|_|  
    */
    protected function getBotStatDetailsAnzBotLastWeek($action,$bmid)
    {
        $LastWeek = date('W', mktime(0, 0, 0, date("m"), date("d")-7, date("Y")) );
        //Besonderheit beachten das der 1.1. die 53. Woche sein kann!
        $YearLastWeek = ($LastWeek > 40 && (int)date('m') == 1) ? date('Y')-1 : date('Y');
        $this->TemplatePartial = new \BackendTemplate('mod_botstatistics_be_stat_partial_details');
        $this->TemplatePartial->action = $action;
        $this->TemplatePartial->bmid = $bmid;
        $this->TemplatePartial->BotDetailList  = '<div class="tl_listing_container list_view">'."\n";
        $this->TemplatePartial->BotDetailList .= '<table class="tl_listing"><tbody><tr><td class="tl_folder_tlist">'.$this->getModulName($bmid).': '.$GLOBALS['TL_LANG']['MSC']['tl_botstatistics_stat']['last_week'].'</td></tr>'."\n";
        
        $objBotStatDetailsAnzBotLastWeek = \Database::getInstance()
                                                ->prepare("SELECT DISTINCT 
                                                                `bot_name`
                                                             FROM 
                                                                `tl_botstatistics_counter`
                                                             WHERE 
                                                                `bot_module_id` = ?
                                                             AND 
                                                                YEARWEEK( `bot_date`, 3 ) = ?
                                                             ORDER BY `bot_name`
                                                        ")
                                                ->execute($bmid, $YearLastWeek.$LastWeek);
        while ($objBotStatDetailsAnzBotLastWeek->next())
        {
            $this->TemplatePartial->BotDetailList .= '<tr><td class="tl_file_list">'.$objBotStatDetailsAnzBotLastWeek->bot_name.'</td></tr>';
        }
        $this->TemplatePartial->BotDetailList .= '</tbody></table></div>';
        
        return $this->TemplatePartial->parse();
    }
    protected function getBotStatDetailsAnzVisitsLastWeek($action,$bmid)
    {
        $LastWeek = date('W', mktime(0, 0, 0, date("m"), date("d")-7, date("Y")) );
        //Besonderheit beachten das der 1.1. die 53. Woche sein kann!
        $YearLastWeek = ($LastWeek > 40 && (int)date('m') == 1) ? date('Y')-1 : date('Y');
        $this->TemplatePartial = new \BackendTemplate('mod_botstatistics_be_stat_partial_details');
        $this->TemplatePartial->action = $action;
        $this->TemplatePartial->bmid = $bmid;
        $this->TemplatePartial->BotDetailList  = '<div class="tl_listing_container list_view">'."\n";
        $this->TemplatePartial->BotDetailList .= '<table class="tl_listing"><tbody><tr><td class="tl_folder_tlist">'.$this->getModulName($bmid).': '.$GLOBALS['TL_LANG']['MSC']['tl_botstatistics_stat']['last_week'].'</td><td class="tl_folder_tlist tl_right_nowrap">'.$GLOBALS['TL_LANG']['MSC']['tl_botstatistics_stat']['number_visit'].'</td></tr>'."\n";
        
        $objBotStatDetailsAnzVisitsLastWeek = \Database::getInstance()
                                                ->prepare("SELECT DISTINCT 
                                                                `bot_name`, sum(`bot_counter`) AS AnzVisits
                                                            FROM 
                                                                `tl_botstatistics_counter`
                                                            WHERE 
                                                                `bot_module_id` = ?
                                                            AND 
                                                                YEARWEEK( `bot_date`, 3 ) = ?
                                                            GROUP BY `bot_name`
                                                            ORDER BY AnzVisits DESC
                                                        ")
                                                ->execute($bmid, $YearLastWeek.$LastWeek);
        while ($objBotStatDetailsAnzVisitsLastWeek->next())
        {
            $this->TemplatePartial->BotDetailList .= '<tr><td class="tl_file_list">'.$objBotStatDetailsAnzVisitsLastWeek->bot_name.'</td><td class="tl_file_list tl_right_nowrap">'.$objBotStatDetailsAnzVisitsLastWeek->AnzVisits.'</td></tr>';
        }
        $this->TemplatePartial->BotDetailList .= '</tbody></table></div>';
        return $this->TemplatePartial->parse();
    }
    protected function getBotStatDetailsAnzPagesLastWeek($action,$bmid)
    {
        $LastWeek = date('W', mktime(0, 0, 0, date("m"), date("d")-7, date("Y")) );
        //Besonderheit beachten das der 1.1. die 53. Woche sein kann!
        $YearLastWeek = ($LastWeek > 40 && (int)date('m') == 1) ? date('Y')-1 : date('Y');
        $this->TemplatePartial = new \BackendTemplate('mod_botstatistics_be_stat_partial_details');
        $this->TemplatePartial->action = $action;
        $this->TemplatePartial->bmid = $bmid;
        $this->TemplatePartial->BotDetailList  = '<div class="tl_listing_container list_view">'."\n";
        $this->TemplatePartial->BotDetailList .= '<table class="tl_listing"><tbody><tr><td class="tl_folder_tlist">'.$this->getModulName($bmid).': '.$GLOBALS['TL_LANG']['MSC']['tl_botstatistics_stat']['last_week'].'</td><td class="tl_folder_tlist tl_right_nowrap">'.$GLOBALS['TL_LANG']['MSC']['tl_botstatistics_stat']['number_hit'].'</td></tr>'."\n";
        
        $objBotStatDetailsAnzBotLastWeek = \Database::getInstance()
                                                ->prepare("SELECT 
                                                                c.`bot_name`, sum(d.`bot_page_alias_counter`) AS AnzPages
                                                            FROM 
                                                                `tl_botstatistics_counter_details` d
                                                            INNER JOIN 
                                                                `tl_botstatistics_counter` c ON d.pid = c.id
                                                            WHERE 
                                                                c.`bot_module_id` = ?
                                                            AND 
                                                                YEARWEEK( `bot_date`, 3 ) = ?
                                                            GROUP BY c.`bot_name`
                                                            ORDER BY AnzPages DESC
                                                        ")
                                                ->execute($bmid, $YearLastWeek.$LastWeek);
        while ($objBotStatDetailsAnzBotLastWeek->next())
        {
            $this->TemplatePartial->BotDetailList .= '<tr><td class="tl_file_list">'.$objBotStatDetailsAnzBotLastWeek->bot_name.'</td><td class="tl_file_list tl_right_nowrap">'.$objBotStatDetailsAnzBotLastWeek->AnzPages.'</td></tr>';
        }
        $this->TemplatePartial->BotDetailList .= '</tbody></table></div>';
        return $this->TemplatePartial->parse();
    }

    protected function getTopBots($limit=20)
    {
        $this->TemplatePartial = new \BackendTemplate('mod_botstatistics_be_stat_partial_top_bots');
        
        $this->TemplatePartial->BotTopBots  = '<div class="mod_botstatistics_be_table_max">'."\n";
        $this->TemplatePartial->BotTopBots .= '<table class="tl_listing">
                <tbody>
                <tr>
					<td colspan="3" style="padding-left: 2px; text-align: center;" class="tl_folder_tlist">'.$GLOBALS['TL_LANG']['MSC']['tl_botstatistics_stat']['bots_top20'].'</td>
				</tr>
                <tr>
                    <td class="tl_folder_tlist">'.$GLOBALS['TL_LANG']['MSC']['tl_botstatistics_stat']['bot_name'].'</td>
                    <td class="tl_folder_tlist">'.$GLOBALS['TL_LANG']['MSC']['tl_botstatistics_stat']['number_visit'].'</td>
                    <td class="tl_folder_tlist">'.$GLOBALS['TL_LANG']['MSC']['tl_botstatistics_stat']['last_visit'].'</td>
                </tr>'."\n";
        
        $objTopBots =  \Database::getInstance()
                            ->prepare("SELECT
                                            bot_name, 
                                            sum(bot_counter) AS bot_counter
                                       FROM
                                            tl_botstatistics_counter
                                       WHERE
                                            bot_module_id = ?
                                       GROUP BY bot_name
                                       ORDER BY bot_counter DESC
                	                    ")
                            ->limit($limit)
                    	    ->execute($this->intModuleID);
        while ($objTopBots->next())
        {
            $objDate = \Database::getInstance()
                            ->prepare("SELECT 
                                            bot_date
                                        FROM
                                            tl_botstatistics_counter
                                        WHERE
                                            bot_module_id = ?
                                        AND
                                            bot_name = ?
                                        ORDER BY 
                                            bot_date DESC
                                    ")
                            ->limit(1)
                            ->execute($this->intModuleID, $objTopBots->bot_name);
            //C2: $this->parseDate($GLOBALS['TL_CONFIG']['datimFormat'], strtotime($objDate->bot_date))
            
            $this->TemplatePartial->BotTopBots .= '<tr>
                    <td class="tl_file_list">'.$objTopBots->bot_name.'</td>
                    <td class="tl_file_list">'.$objTopBots->bot_counter.'</td>
                    <td class="tl_file_list">'.\Date::parse($GLOBALS['TL_CONFIG']['dateFormat'], strtotime($objDate->bot_date)).'</td>
                    </tr>'."\n";
        }
        $this->TemplatePartial->BotTopBots .= '</tbody></table></div>';
        return $this->TemplatePartial->parse();
        
    }
    
    protected function getTopPages($limit=20)
    {
        $this->TemplatePartial = new \BackendTemplate('mod_botstatistics_be_stat_partial_top_pages');
        
        $this->TemplatePartial->BotTopPages  = '<div class="mod_botstatistics_be_table_max">'."\n";
        $this->TemplatePartial->BotTopPages .= '<table class="tl_listing">
                <tbody>
                <tr>
					<td colspan="2" style="padding-left: 2px; text-align: center;" class="tl_folder_tlist">'.$GLOBALS['TL_LANG']['MSC']['tl_botstatistics_stat']['pages_top20'].'</td>
				</tr>
                <tr>
                    <td class="tl_folder_tlist">'.$GLOBALS['TL_LANG']['MSC']['tl_botstatistics_stat']['page_alias'].'</td>
                    <td class="tl_folder_tlist">'.$GLOBALS['TL_LANG']['MSC']['tl_botstatistics_stat']['number_hit'].'</td>
                </tr>'."\n";

        $objTopPages =  \Database::getInstance()
                            ->prepare("SELECT 
                                            d.bot_page_alias,
                                            count(d.bot_page_alias_counter) AS bot_page_alias_counter
                                        FROM
                                            tl_botstatistics_counter_details d
                                        INNER JOIN
                                            tl_botstatistics_counter c ON c.id = d.pid
                                        WHERE
                                            c.bot_module_id = ?
                                        GROUP BY bot_page_alias
                                        ORDER BY bot_page_alias_counter DESC
                	                    ")
    	                    ->limit($limit)
    	                    ->execute($this->intModuleID);
        while ($objTopPages->next())
        {
            $this->TemplatePartial->BotTopPages .= '<tr>
                    <td class="tl_file_list">'.$objTopPages->bot_page_alias.'</td>
                    <td class="tl_file_list">'.$objTopPages->bot_page_alias_counter.'</td>
                    </tr>'."\n";
        }
        $this->TemplatePartial->BotTopPages .= '</tbody></table></div>';
        return $this->TemplatePartial->parse();
    }
    
    
} // class
