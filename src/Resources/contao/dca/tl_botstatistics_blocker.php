<?php

/**
 * Contao Open Source CMS, Copyright (C) 2005-2017 Leo Feyer
 *
 * Modul BotStatistics
 * DCA tl_botstatistics_blocker
 *
 * @copyright  Glen Langer 2012..2017 <http://contao.ninja>
 * @author     Glen Langer (BugBuster)
 * @package    BotStatistics
 * @license    LGPL
 * @filesource
 * @see        https://github.com/BugBuster1701/contao-botstatistics-bundle
 */

/**
 * Table tl_botstatistics_blocker
 */
$GLOBALS['TL_DCA']['tl_botstatistics_blocker'] = array
(

        // Config
        'config' => array
        (
            'sql' => array
            (
                'keys' => array
                (
                    'id' => 'primary',
                    'bot_module_id' => 'index'
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
            'bot_tstamp' => array
            (
                'sql'                     => "timestamp NULL"
            ),
            'bot_ip' => array
            (
                'sql'                     => "varchar(40) NOT NULL default '0.0.0.0'"
            )
        )
);

