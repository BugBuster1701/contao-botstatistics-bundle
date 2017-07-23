<?php

/**
 * Contao Open Source CMS, Copyright (C) 2005-2013 Leo Feyer
 *
 * Modul BotStatistics
 * DCA tl_botstatistics_counter_details
 *
 * @copyright  Glen Langer 2012..2013 <http://www.contao.glen-langer.de>
 * @author     Glen Langer (BugBuster)
 * @package    BotStatistics
 * @license    LGPL
 * @filesource
 * @see        https://github.com/BugBuster1701/botstatistics
 */

/**
 * Table tl_botstatistics_counter_details
 */
$GLOBALS['TL_DCA']['tl_botstatistics_counter_details'] = array
(

        // Config
        'config' => array
        (
            'sql' => array
            (
                'keys' => array
                (
                    'id' => 'primary',
                    'pid,bot_page_alias' => 'unique'
                )
            )
        ),

        // Fields
        'fields' => array
        (
            'id' => array
            (
                'sql'                     => "int(10) unsigned NOT NULL auto_increment"
            ),
            'pid' => array
            (
                'sql'                     => "int(10) unsigned NOT NULL default '0'"
            ),
            'bot_page_alias' => array
            (
                'sql'                     => "varchar(255) NOT NULL default 'Unknown'"
            ),
            'bot_page_alias_counter' => array
            (
                'sql'                     => "int(10) unsigned NOT NULL default '0'"
            )
        )
);

