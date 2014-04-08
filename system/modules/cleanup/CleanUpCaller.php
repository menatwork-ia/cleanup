<?php

/**
 * Contao Open Source CMS
 *
 * @copyright  MEN AT WORK 2014
 * @package    cleanup
 * @license    GNU/LGPL
 * @filesource
 */

namespace CleanUp;

/**
 * Initialize the system
 */
define('TL_MODE', 'CTO_BE');
require('../../initialize.php');

// If CLI get options and ste them.
if (PHP_SAPI === 'cli' || empty($_SERVER['REMOTE_ADDR']))
{
    /**
     * f:       Required value
     * v::      Optional value
     * "abc";   These options do not accept values
     */

    // Sort opts
    $arrShortOpts = array(
        'v', // See 'verbose'.
    );

    // Long opts
    $arrLongOpts = array(
        'verbose', // Print log msg on screen
        'dry-run', // Don't import into database, just download it.
        'language:' // Contains the language for the backend.
    );

    // Get the options from the cli.
    $options = getopt(implode('', $arrShortOpts), $arrLongOpts);

    // If we have a language key, set it before we call a class.
    if (array_key_exists('language', $options))
    {
        if (array_key_exists($options['language'], \System::getLanguages()))
        {
            $GLOBALS['TL_LANGUAGE'] = $options['language'];
        }
    }

    // Get an instance from the program.
    $objMainProgram = CleanUp::getInstance();

    // Execute them.
    foreach ($options as $strOption => $mixValue)
    {
        switch ($strOption)
        {
            case 'v':
            case 'verbose':
                $objMainProgram->setShowLogs(true);
                break;

            case 'dry-run':
                $objMainProgram->setDryRun(true);
                break;

            default:
                break;
        }
    }

    // Call the clean up runner.
    $objMainProgram->run();
}
else
{
    die('You can call this class only as CLI variant.');
}
