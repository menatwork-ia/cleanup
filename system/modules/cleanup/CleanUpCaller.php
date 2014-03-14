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

/**
 * Call the clean up runner.
 */
$objCleanUp = CleanUp::getInstance();
$objCleanUp->run();