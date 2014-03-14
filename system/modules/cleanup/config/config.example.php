<?php

/**
 * Contao Open Source CMS
 *
 * @copyright  MEN AT WORK 2014
 * @package    cleanup
 * @license    GNU/LGPL
 * @filesource
 */

/**
 * If true the cleaner didn't remove a single file.
 * It writes just into the contao log a fake entrie.
 * Good for testing.
 */
$GLOBALS['CLEAN_UP']['DRY_RUN'] = true;

/**
 * A list with all folder in tl_files/files for scanning.
 * The start folder is the upload path.
 *
 * @var array
 */
$GLOBALS['CLEAN_UP']['FOLDERS'] = array
(
    array
    (
        'path' => 't1',
        'recursive' => true
    ),
    array
    (
        'path' => 't2'
    )
);

/**
 * A list with file names and types that never been touched.
 *
 * @var array
 */
$GLOBALS['CLEAN_UP']['GENERAL_BLACKLIST'] = array
(
    // Basic rules
    '.htaccess',
    '.htpasswd'
    // Examples
    // '*.jpeg',
    // '*.jpg',
    // 'my_text_*.txt'
);

/**
 * The time after a file should remove from the filesystem.
 * For example 30 means 30 days. So each file which is older than 30 day 
 * will be remove from the filesystem.
 *
 * @var int
 */
$GLOBALS['CLEAN_UP']['GENERAL_LIFETIME'] = 30;

/**
 * Activate contao crons
 */
$GLOBALS['TL_CRON']['monthly'][]    = array('CleanUp\CleanUp', 'run');
$GLOBALS['TL_CRON']['weekly'][]     = array('CleanUp\CleanUp', 'run');
$GLOBALS['TL_CRON']['daily'][]      = array('CleanUp\CleanUp', 'run');

// Contao 3 only
$GLOBALS['TL_CRON']['hourly'][]     = array('CleanUp\CleanUp', 'run');
$GLOBALS['TL_CRON']['minutely'][]   = array('CleanUp\CleanUp', 'run');