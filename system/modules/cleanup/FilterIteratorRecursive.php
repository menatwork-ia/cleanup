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
 * Class for file filtering operations
 */
class FilterIteratorRecursive extends FilterIteratorBase
{
    /**
     * State if is it allowed to scan the sub folders.
     * @return bool
     */
    protected function  isScanSubFolders()
    {
        return true;
    }

}