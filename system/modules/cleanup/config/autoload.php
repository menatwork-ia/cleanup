<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2014 Leo Feyer
 *
 * @package cleanup
 * @link    https://contao.org
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */


/**
 * Register the namespaces
 */
ClassLoader::addNamespaces(array
(
    'CleanUp',
));


/**
 * Register the classes
 */
ClassLoader::addClasses(array
(
    'CleanUp\CleanUp'                 => 'system/modules/cleanup/CleanUp.php',
    'CleanUp\FilterIteratorBase'      => 'system/modules/cleanup/FilterIteratorBase.php',
    'CleanUp\FilterIteratorRecursive' => 'system/modules/cleanup/FilterIteratorRecursive.php',
));
