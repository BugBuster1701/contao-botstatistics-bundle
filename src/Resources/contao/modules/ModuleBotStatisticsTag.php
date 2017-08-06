<?php 

/**
 * Contao Open Source CMS, Copyright (C) 2005-2017 Leo Feyer
 *
 * Module BotStatistics - Frontend
 * Insert-Tags handling
 * 
 * @copyright  Glen Langer 2012..2017 <http://contao.ninja>
 * @author     Glen Langer (BugBuster)
 * @package    BotStatistics
 * @license    LGPL
 * @filesource
 * @see        https://github.com/BugBuster1701/contao-botstatistics-bundle
 */

namespace BugBuster\BotStatistics;

use BugBuster\BotDetection\ModuleBotDetection;
use BugBuster\BotDetection\CheckBotAgentExtended;
use Psr\Log\LogLevel;
use Contao\CoreBundle\Monolog\ContaoContext;

/**
 * Class ModuleBotStatisticsTag 
 *
 * @copyright  Glen Langer 2012..2017 <http://contao.ninja>
 * @author     Glen Langer (BugBuster)
 * @package    BotStatistics
 */
class ModuleBotStatisticsTag extends \Frontend
{
	protected $BotStatus = false;
	protected $BotName   = '';
	protected $CURDATE   = '';
	
	/**
	 * Generate module
	 */
	public function replaceInsertTagsBotStatistics($strTag)
	{
	    $arrTag = trimsplit('::', $strTag);
	    if ($arrTag[0] != 'cache_botstatistics')
	    {
	        return false; // nicht für uns
	    }
	    if (!isset($arrTag[2])) 
	    {
	        \System::loadLanguageFile('tl_botstatistics');
	        \System::getContainer()
            	        ->get('monolog.logger.contao')
            	        ->log(LogLevel::ERROR,
            	            $GLOBALS['TL_LANG']['tl_botstatistics']['no_key'],
            	            array('contao' => new ContaoContext('ModuleBotStatisticsTag replaceInsertTagsBotStatistics '. BOTSTATISTICS_VERSION .'.'. BOTSTATISTICS_BUILD, TL_ERROR)));
	        return false;  // da fehlt was
	    }
        if (!isset($arrTag[3]) || strlen($arrTag[3])<1) 
        {
            $arrTag[3] = 0; // no page alias
        }
	    if ($arrTag[2] == 'count')
	    {
	        $statusVisit  = $this->setBotCounter( (int)$arrTag[1] ); // Modul ID
	        $statusDetail = $this->setBotCounterDetails( (int)$arrTag[1], $arrTag[3] ); // Modul ID, Page Alias
	        
	        if ($statusVisit === true || $statusDetail === true)
	        {
	            return '<!-- c0n740 f0r3v3r '.$arrTag[3].' -->';
	        }
	        else
	        {
	            return '<!-- n0 c0un7 '.$arrTag[3].' -->';
	        }
	    }
	    else
	    {
	        \System::loadLanguageFile('tl_botstatistics');
	        \System::getContainer()
            	        ->get('monolog.logger.contao')
            	        ->log(LogLevel::ERROR,
            	            $GLOBALS['TL_LANG']['tl_botstatistics']['wrong_key'],
            	            array('contao' => new ContaoContext('ModuleBotStatisticsTag replaceInsertTagsBotStatistics '. BOTSTATISTICS_VERSION .'.'. BOTSTATISTICS_BUILD, TL_ERROR)));
	        return false;  // da ist was falsch
	    }
	}// BotStatReplaceInsertTags
	
	/**
	 * Insert/Update Counter
	 */
	protected function setBotCounter($bid)
	{
	    $visit = false;
	    $ClientIP  = bin2hex(sha1($bid . \Environment::get('ip'),true)); // sha1 20 Zeichen, bin2hex 40 zeichen
	    $BlockTime = 60; //Sekunden
	    $this->CURDATE = date('Y-m-d');
	    
	    // Check Bot und setze $this->BotName
	    if ($this->isSetBot() === false)
	    {
	        return false;
	    }

	    if ($this->BotName === false)
	    {
	        //Bot erkannt aber keine Advanced Kennung :-(
	        $this->BotName = 'noname';
	        return false; // vorerst nicht zählen (GitHub #13)
	    }
	    
	    //Bot Blocker
	    \Database::getInstance()
        	        ->prepare("DELETE FROM 
        	                        tl_botstatistics_blocker
                	            WHERE 
        	                        CURRENT_TIMESTAMP - INTERVAL ? SECOND > bot_tstamp
                        	    AND 
        	                        bot_module_id = ?
        	                ")
                    ->execute($BlockTime, $bid);
	    
	    //Test ob Bot Visits gesetzt werden muessen
	    $objBotIP = \Database::getInstance()
        	                    ->prepare("SELECT 
        	                                    id
                            	            FROM 
        	                                    tl_botstatistics_blocker
                            	            WHERE 
        	                                    bot_module_id = ? AND bot_ip = ?
        	                            ")
                                ->limit(1)
                                ->execute($bid, $ClientIP);
	    
	    if ($objBotIP->numRows == 0) 
	    {
	        // nicht geblockt Visit zählen
	        
    	    // Doppelte Einträge verhindern bei zeitgleichen Zugriffen wenn noch 
    	    // kein Eintrag vorhanden ist durch Insert Ignore und Unique Key
    	    $arrSet = array
    	    (
    	            'bot_module_id'=> $bid,
    	            'bot_date'     => $this->CURDATE,
    	            'bot_name'     => $this->BotName,
    	            'bot_counter'  => 0
    	    );
    	    \Database::getInstance()
            	        ->prepare("INSERT IGNORE INTO tl_botstatistics_counter %s")
            	        ->set($arrSet)
            	        ->execute();
    	    
    	    //Bot Visits lesen
    	    $objBotCounter = \Database::getInstance()
    	                        ->prepare("SELECT 
    	                                        id, bot_counter
                            	            FROM 
    	                                        tl_botstatistics_counter
                            	            WHERE 
    	                                        bot_module_id = ?
            	                            AND 
    	                                        bot_date = ?
                            	            AND 
    	                                        bot_name = ?
    	                                ")
                                ->execute($bid, $this->CURDATE, $this->BotName);
    	    $objBotCounter->next();
    	    //zählen per update
    	    \Database::getInstance()
            	        ->prepare("UPDATE 
            	                        tl_botstatistics_counter 
            	                    SET 
            	                        bot_counter = ? 
            	                    WHERE 
            	                        id = ?
            	                ")
                        ->execute($objBotCounter->bot_counter +1, $objBotCounter->id);
    	    //blocken
    	    \Database::getInstance()
            	        ->prepare("INSERT INTO 
            	                        tl_botstatistics_blocker 
            	                    SET 
            	                        bot_module_id = ?, 
            	                        bot_tstamp = CURRENT_TIMESTAMP, 
            	                        bot_ip = ?
            	                ")
                        ->execute($bid, $ClientIP);
    	    $visit = true;
        }
	    return $visit;
	} //BotCountUpdate
	
	/**
	 * Insert/Update Counter Details
	 */
	protected function setBotCounterDetails($bid,$page_alias)
	{
	    if ($this->BotName === false)
	    {
	        return false; // vorerst nicht zählen (GitHub #13)
	    }
	    
	    //Detail Zählung
	    //detail on/off ermmitteln
	    //tl_botstatistics_counter.id ermitteln als pid
	    $objBotModul = \Database::getInstance()
	                        ->prepare("SELECT 
	                                        tl_botstatistics_counter.id AS pid
                        	            FROM 
	                                        tl_botstatistics_counter
                        	            WHERE 
	                                        tl_botstatistics_counter.bot_module_id = ?
                        	            AND 
	                                        tl_botstatistics_counter.bot_name = ?
                        	            AND 
	                                        tl_botstatistics_counter.bot_date = ?
	                                ")
                            ->execute($bid, $this->BotName, $this->CURDATE);
	    $objBotModul->next();
	    // Doppelte Einträge verhindern bei zeitgleichen Zugriffen 
	    // wenn noch kein Eintrag vorhanden ist durch Insert Ignore und Unique Key
	    $arrSet = array
	    (
	            'id'  => 0,
	            'pid' => $objBotModul->pid,
	            'bot_page_alias'         => $page_alias,
	            'bot_page_alias_counter' => 0
	    );
	    \Database::getInstance()
        	        ->prepare("INSERT IGNORE INTO tl_botstatistics_counter_details %s")
                    ->set($arrSet)
        	        ->execute();
	    
	    \Database::getInstance()
        	        ->prepare("UPDATE 
        	                        tl_botstatistics_counter_details 
                                SET 
        	                        bot_page_alias_counter = bot_page_alias_counter+1
                                WHERE 
        	                        pid=? 
        	                    AND 
        	                        bot_page_alias = ?
        	                ")
                    ->execute($objBotModul->pid,$page_alias);
	    return true;
	}
	
	/**
	 * Spider Bot Check, set Bot Name
	 */
	protected function isSetBot()
	{
	    $this->BotName = false;
	    $bundles = array_keys(\System::getContainer()->getParameter('kernel.bundles')); // old \ModuleLoader::getActive()
	    if ( !in_array( 'BugBusterBotdetectionBundle', $bundles ) )
	    {
	        //BugBusterBotdetectionBundle Package fehlt, Abbruch
	        \System::getContainer()
	                   ->get('monolog.logger.contao')
	                   ->log(LogLevel::ERROR,
	                           'contao-botstatistics-bundle package required for the package: BotStatistics!',
	                           array('contao' => new ContaoContext('ModuleBitStatisticsTag isSetBot ', TL_ERROR)));
	        return false;
	    }
	    
	    $ModuleBotDetection = new ModuleBotDetection();
	    if ($ModuleBotDetection->checkBotAllTests())
	    {
	        $this->BotStatus = true;
	        $this->BotName   = CheckBotAgentExtended::checkAgentName( \Environment::get('httpUserAgent') );
	    }
	    
	    //Debug log_message('BotName: '.$this->BotName,'debug.log');
	    if ($this->BotStatus === true) 
	    {
	        return true;
	    }
	    return false;
	} //CheckBot
	
}//class

