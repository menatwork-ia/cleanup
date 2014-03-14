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

class CleanUp
{

    /**
     * The self instance.
     * @var CleanUp|null
     */
    protected static $objInstance = null;

    /**
     * Holds the flags for the RecursiveDirectoryIterator.
     *
     * @var int
     */
    protected $strRDIFlags;

    /**
     * Holds the time after a file is marked as "ready for delete".
     *
     * @var int|null
     */
    protected $intTimeLimit = null;

    /**
     * Holds the list of folder for the scan function.
     *
     * @var array
     */
    protected $arrScanFolder = array();

    /**
     * Holds the general blacklist for the filter iterator of the files.
     *
     * @var array
     */
    protected $arrGeneralBlacklist = array();

    /**
     * The iterator with all found files.
     *
     * @var \AppendIterator
     */
    protected $objAppendIt;

    /**
     * Containss the TL_ROOT path for pregs.
     *
     * @var string
     */
    protected $strPregRoot;

    /**
     * List of deleted files.
     *
     * @var array
     */
    protected $arrListOfDeletedFiles = array();

    /**
     * Name of the config array used on the $GLOBALS array.
     */
    const CONF_NAME = 'CLEAN_UP';

    /**
     * The length of a day in second. Don't change this.
     * If you change this the definition of the GENERAL_LIFETIME will be change.
     *
     * @var int
     */
    protected $intDayInSeconds = 86400;

    /**
     * Array for the replacement of the preg match.
     * @var array
     */
    protected $arrPregSearch = array("\\", ".", "^", "?", "*", "/");

    /**
     * Array for the replacement of the preg match.
     * @var array
     */
    protected $arrPregReplace = array("\\\\", "\\.", "\\^", ".?", ".*", "\\/");


    /**
     * Constructor
     */
    protected function __construct()
    {
        if (!isset($GLOBALS[self::CONF_NAME]))
        {
            return;
        }

        // The append iterator for the scanner.
        $this->objAppendIt = new \AppendIterator();

        // Flags for file scanning.
        $this->strRDIFlags = \RecursiveDirectoryIterator::FOLLOW_SYMLINKS | \RecursiveDirectoryIterator::SKIP_DOTS | \RecursiveDirectoryIterator::UNIX_PATHS;

        // Calculate the limit of the general lifetime.
        $intLifeTime = intval($GLOBALS[self::CONF_NAME]['GENERAL_LIFETIME']);
        if ($intLifeTime > 0)
        {
            $this->intTimeLimit = intval($GLOBALS[self::CONF_NAME]['GENERAL_LIFETIME']) * $this->intDayInSeconds;
        }

        // Check the folders for scanning.
        if (is_array($GLOBALS[self::CONF_NAME]['FOLDERS']) && count($GLOBALS[self::CONF_NAME]['FOLDERS']) > 0)
        {
            foreach ($GLOBALS[self::CONF_NAME]['FOLDERS'] as $arrFolderSettings)
            {
                // Check if we have a path;
                if (empty($arrFolderSettings['path']))
                {
                    continue;
                }

                $strFullPath = TL_ROOT . '/' . $GLOBALS['TL_CONFIG']['uploadPath'] . '/' . $arrFolderSettings['path'];
                if (file_exists($strFullPath))
                {
                    $this->arrScanFolder[] = $arrFolderSettings;
                }
            }
        }

        // Get the general blacklist and make it ready for a preg_match.
        if (is_array($GLOBALS[self::CONF_NAME]['GENERAL_BLACKLIST']) && count($GLOBALS[self::CONF_NAME]['GENERAL_BLACKLIST']) > 0)
        {
            foreach ($GLOBALS[self::CONF_NAME]['GENERAL_BLACKLIST'] as $strFilter)
            {
                $this->arrGeneralBlacklist[] = str_replace($this->arrPregSearch, $this->arrPregReplace, $strFilter);
            }
        }

        // Make the TL_ROOT rdy for a preg.
        $this->strPregRoot = str_replace(array('\\', '/'), array('\\\\', '\\/'), TL_ROOT);
    }

    /**
     * Get the instance.
     *
     * @return CleanUp
     */
    public static function getInstance()
    {
        if (self::$objInstance == null)
        {
            self::$objInstance = new self();
        }

        return self::$objInstance;
    }

    /**
     * Return the general blacklist.
     * Used for the FilterIteratorBase.
     *
     * @return array
     */
    public function getGeneralBlacklist()
    {
        return $this->arrGeneralBlacklist;
    }

    /**
     * Return a time. After this time all files are marked as old.
     * Used for the FilterIteratorBase.
     *
     * @return int
     */
    public function getTimeLimit()
    {
        return $this->intTimeLimit;
    }

    /**
     * Check if we have a dry run mode.
     *
     * @return boolean
     */
    public function isDryRun()
    {
        return ($GLOBALS[self::CONF_NAME]['DRY_RUN']) ? true : false;
    }

    /**
     * Run Run Run.
     */
    public function run()
    {
        // Check if we have a config.
        if (!isset($GLOBALS[self::CONF_NAME]))
        {
            return;
        }

        // Run.
        $this->scanFolders();
        $this->deleteFiles();
        $this->writeLogs();
    }

    /**
     * Write some nice things into the Contao log.
     */
    protected function writeLogs()
    {
        if (version_compare(VERSION, '3', '>'))
        {
            if (empty($this->arrListOfDeletedFiles))
            {
                \Backend::log('Nothing found to delete. ', __CLASS__ . '::run()', TL_CRON);
            }
            else
            {
                \Backend::log('Delete some old files: ' . implode(',', $this->arrListOfDeletedFiles), __CLASS__ . '::run()', TL_CRON);
            }
        }
    }

    /**
     * Delete old files.
     */
    protected function deleteFiles()
    {
        foreach ($this->objAppendIt as $strFullPath)
        {
            $strPathWORoot = preg_replace('/^' . $this->strPregRoot . '\//i', '', $strFullPath, 1);
            if (file_exists(TL_ROOT . '/' . $strPathWORoot) && is_file(TL_ROOT . '/' . $strPathWORoot))
            {
                // If not in dry run remove the file AND if in contao 3.2 remove the file from the dbafs.
                if (!$this->isDryRun())
                {
                    $objFile = new \File($strPathWORoot);
                    $objFile->delete();

                    if (version_compare(VERSION, '3.2', '>='))
                    {
                        \Dbafs::deleteResource($strPathWORoot);
                    }
                }

                // Add to the list of deleted files.
                $this->arrListOfDeletedFiles[] = $strPathWORoot;
            }
        }
    }

    /**
     * Scan all folders in the list and get the fitting files. Also these ones which are not on the
     * black list. We will filter the time diff later.
     */
    protected function scanFolders()
    {
        foreach ($this->arrScanFolder as $arrFolderSettings)
        {
            // Check some vars.
            if (isset($arrFolderSettings['recursive']) && $arrFolderSettings['recursive'] == true)
            {
                $binRecursive = true;
            }
            else
            {
                $binRecursive = false;
            }

            // Scan the folder and append the data to the overall container.
            $strFullPath = TL_ROOT . '/' . $GLOBALS['TL_CONFIG']['uploadPath'] . '/' . $arrFolderSettings['path'];
            $this->objAppendIt->append($this->scanSingleFolder($strFullPath, $binRecursive));
        }
    }

    /**
     * @param $strFullPathFolder
     *
     * @param $blnRecursive
     *
     * @return \RecursiveIteratorIterator
     */
    protected function scanSingleFolder($strFullPathFolder, $blnRecursive)
    {
        $objDirectoryIt = new \RecursiveDirectoryIterator($strFullPathFolder, $this->strRDIFlags);

        // Check which filter should be used.
        if ($blnRecursive)
        {
            $objFilterIt = new FilterIteratorRecursive($objDirectoryIt);
        }
        else
        {
            $objFilterIt = new FilterIteratorBase($objDirectoryIt);
        }

        $objRecursiveIt = new \RecursiveIteratorIterator($objFilterIt, \RecursiveIteratorIterator::SELF_FIRST);

        return $objRecursiveIt;
    }

}