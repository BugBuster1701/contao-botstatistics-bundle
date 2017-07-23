<?php

/**
 * Contao Open Source CMS, Copyright (C) 2005-2013 Leo Feyer
 *
 * Modul BotStatistics
 * DCA tl_botstatistics_counter
 *
 * @copyright  Glen Langer 2012..2013 <http://www.contao.glen-langer.de>
 * @author     Glen Langer (BugBuster)
 * @package    BotStatistics
 * @license    LGPL
 * @filesource
 * @see        https://github.com/BugBuster1701/botstatistics
 */

/**
 * Table tl_botstatistics_counter
 */
$GLOBALS['TL_DCA']['tl_botstatistics_counter'] = array
(

        // Config
        'config' => array
        (
            'sql' => array
            (
                'keys' => array
                (
                    'id' => 'primary',
                    'bot_module_id,bot_date,bot_name' => 'unique'
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
                'bot_module_id' => array
                (
                    'sql'                     => "int(10) unsigned NOT NULL default '0'"
                ),
                'bot_date' => array
                (
                    'sql'                     => "date NOT NULL default '1999-01-01'"
                ),
                'bot_name' => array
                (
                    'sql'                     => "varchar(60) NOT NULL default 'Unknown'"
                ),
                'bot_counter' => array
                (
                    'sql'                     => "int(10) unsigned NOT NULL default '0'"
                )
        )
);

