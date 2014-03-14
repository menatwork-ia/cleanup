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
use RecursiveFilterIterator;

/**
 * Class for file filtering operations
 */
class FilterIteratorBase extends \RecursiveFilterIterator
{
    /**
     * List with the general blacklist.
     *
     * @var array
     */
    protected $arrGeneralBlacklist = array();

    /**
     * The time when a data is, arked as old and added to the list.
     *
     * @var int
     */
    protected $intTimeLimit = PHP_INT_MAX;

    /**
     * Construct
     *
     * @param \RecursiveIterator $iterator
     */
    public function __construct(\RecursiveIterator $iterator)
    {
        // Call parent.
        parent::__construct($iterator);

        // Get some information from the parent class.
        $this->arrGeneralBlacklist = CleanUp::getInstance()->getGeneralBlacklist();
        $this->intTimeLimit        = CleanUp::getInstance()->getTimeLimit();
    }

    /**
     * State if is it allowed to scan the sub folders.
     *
     * @return bool
     */
    protected function  isScanSubFolders()
    {
        return false;
    }

    /**
     * Check if we add the file to the list.
     *
     * If is a dir              => Check the return value of the isScanSubFolders function
     * If on blacklist          => Don't add it
     * If under the time limit  => Don't add it
     */
    public function accept()
    {
        // Check the "go deeper" flag, if set go deeeeeeper.
        if ($this->current()->isDir())
        {
            return $this->isScanSubFolders();
        }

        // Get some meta information for the validation.
        $arrPathInfo         = pathinfo($this->current()->getPathname());
        $intLastModifiedTime = filemtime($this->current()->getPathname());

        // Check if in the blacklist, if yes skip this file.
        foreach ($this->arrGeneralBlacklist as $value)
        {
            // Search with preg for values
            if (preg_match("/" . $value . "$/i", $arrPathInfo['basename']) != 0)
            {
                return false;
            }
        }

        // Check if the file is in the time of removing.
        if ( ($intLastModifiedTime + $this->intTimeLimit) >= time() )
        {
            return false;
        }

        // No return so the file is valid.
        return true;
    }

}