<?php

/**
* Fast, light and safe Cache Class
*
* Cache_Lite is a fast, light and safe cache system. It's optimized
* for file containers. It is fast and safe (because it uses file 
* locking and/or anti-corruption tests).
*
* There are some examples in the 'docs/examples' file
* Technical choices are described in the 'docs/technical' file
*
* @package Cache_Lite
* @version $Id$
* @author Fabien MARTY <fab@php.net>
*/

define('CACHE_LITE_ERROR_RETURN', 1);
define('CACHE_LITE_ERROR_DIE', 8);

class Cache_Lite
{

    // --- Private properties ---
    
    /**
    * Directory where to put the cache files
    * (make sure to add a trailing slash)
    *
    * @var string $_cacheDir
    */
    var $_cacheDir = '/tmp/';
    
    /**
    * Enable / disable caching
    *
    * (can be very usefull for the debug of cached scripts)
    *
    * @var boolean $_caching
    */
    var $_caching = true;
    
    /**
    * Cache lifetime (in seconds)
    *
    * @var int $_lifeTime
    */
    var $_lifeTime = 3600;
    
    /**
    * Enable / disable fileLocking
    *
    * (can avoid cache corruption under bad circumstances)  
    *
    * @var boolean $_fileLocking
    */
    var $_fileLocking = true;
    
    /**
    * Timestamp of the last valid cache
    *
    * @var int $_refreshTime
    */
    var $_refreshTime;
    
    /**
    * File name (with path)
    *
    * @var string $_file
    */
    var $_file;

    /**
    * Enable / disable write control (the cache is read just after writing to detect corrupt entries)
    *
    * Enable write control will lightly slow the cache writing but not the cache reading
    * Write control can detect some corrupt cache files but maybe it's not a perfect control 
    *
    * @var boolean $_writeControl
    */
    var $_writeControl = true;

    /**
    * Enable / disable read control 
    * 
    * If enabled, a control key is embeded in cache file and this key is compared with the one
    * calculated after the reading.
    *
    * @var boolean $_writeControl
    */
    var $_readControl = true;
    
    /**
    * Type of read control (only if read control is enabled)
    *
    * Available values are :
    * 'md5' for a md5 hash control (best but slowest)
    * 'crc32' for a crc32 hash control (lightly less safe but faster, better choice)
    * 'strlen' for a length only test (fastest)
    *
    * @var boolean $_readControlType
    */
    var $_readControlType = 'crc32';
    
    /**
    * Pear error mode (when raiseError is called) 
    * 
    * (see PEAR doc)
    *
    * @see setToDebug()
    * @var int $_pearErrorMode
    */
    var $_pearErrorMode = CACHE_LITE_ERROR_RETURN;
    
    /**
    * Current cache id
    *
    * @var string $_id
    */
    var $_id;
    
    /**
    * Current cache group
    *
    * @var string $_group
    */
    var $_group;
    
    // --- Public methods ---
    
    /**
    * Constructor
    *
    * $options is an assoc. Available options are :
    * $options = array(
    *     'cacheDir' => directory where to put the cache files (string),
    *     'caching' => enable / disable caching (boolean),
    *     'lifeTime' => cache lifetime in seconds (int),
    *     'fileLocking' => enable / disable fileLocking (boolean),
    *     'writeControl' => enable / disable write control (boolean),
    *     'readControl' => enable / disable read control (boolean),
    *     'readControlType' => type of read control 'crc32', 'md5', 'strlen' (string)
    *     'pearErrorMode' => pear error mode (when raiseError is called) (cf PEAR doc) (int)
    * );
    *
    * @param array $options options
    * @access public
    */
    function Cache_Lite($options = NULL)
    {
        $availableOptions = '{cacheDir}{caching}{lifeTime}{fileLocking}{writeControl}{readControl}{readControlType}{pearErrorMode}';
        while (list($key, $value) = each($options)) {
            if (strpos('>'.$availableOptions, '{'.$key.'}')) {
                $property = '_'.$key;
                $this->$property = $value;
            }
        }
        $this->_refreshTime = time() - $this->_lifeTime;
    }
    
    /**
    * Test if a cache is available and (if yes) return it
    *
    * @param string $id cache id
    * @param string $group name of the cache group
    * @param boolean $doNotTestCacheValidity if set to true, the cache validity won't be tested
    * @return string data of the cache (or false if no cache available)
    * @access public
    */
    function get($id, $group = 'default', $doNotTestCacheValidity = false)
    {
        $this->_id = $id;
        $this->_group = $group;
        if ($this->_caching) {
            $this->_setFileName($id, $group);
            if ($doNotTestCacheValidity) {
                if (file_exists($this->_file)) {
                    return (($data = $this->_read()));
                }
            } else {
                if (@filemtime($this->_file) > $this->_refreshTime) {
                    return (($data = $this->_read()));
                }
            }
        }
        return false;
    }
    
    /**
    * Save some data in a cache file
    *
    * @param string $data data to put in cache
    * @param string $id cache id
    * @param string $group name of the cache group
    * @return boolean true if no problem
    * @access public
    */
    function save($data, $id = NULL, $group = 'default') 
    {
        if ($this->_caching) {
            if (isset($id)) {
                $this->_setFileName($id, $group);
            }
            if ($this->_writeControl) {
                if (!$this->_writeAndControl($data)) {
                    @touch($this->_file, time() - 2*abs($this->_lifeTime));
                    return false;
                } else {
                    return true;
                }
            } else {
                return $this->_write($data);
            }
        }
        return false;
    }
    
    /**
    * Remove a cache file
    *
    * @param string $id cache id
    * @param string $group name of the cache group
    * @return boolean true if no problem
    * @access public
    */
    function remove($id, $group)
    {
        $this->_setFileName($id, $group);
        if (!@unlink($this->_file)) {
            Cache_Lite::raiseError('Cache_Lite : Unable to remove cache !', -3);   
            return false;
        }
        return true;
    }
    
    /**
    * Clean the cache
    *
    * if no group is specified all cache files will be destroyed
    * else only cache files of the specified group will be destroyed
    *
    * @param string $group name of the cache group
    * @return boolean true if no problem
    * @access public
    */
    function clean($group = false)     
    {
        $motif = ($group) ? "cache_$group_" : 'cache_';
        if (!($dh = opendir($this->_cacheDir))) {
            Cache_Lite::raiseError('Cache_Lite : Unable to open cache directory !', -4);   
            return false;
        }
        while ($file = readdir($dh)) {
            if (($file != '.') && ($file != '..')) {
                $file = $this->_cacheDir . $file;
                if (is_file($file)) {
                    if (strpos($file, $motif, 0)) {
                        if (!@unlink($file)) {
                            Cache_Lite::raiseError('Cache_Lite : Unable to remove cache !', -3);   
                            return false;
                        }
                    }
                }
            }
        }
        return true;
    }
    
    /**
    * Set to debug mode
    *
    * When an error is found, the script will stop and the message will be displayed
    * (in debug mode only).
    *
    * @access public
    */
    function setToDebug()
    {
        $this->_pearErrorMode = CACHE_LITE_ERROR_DIE;
    }

    /**
    * Trigger a PEAR error
    *
    * To improve performances, the PEAR.php file is included dynamically.
    * The file is so included only when an error is triggered. So, in most
    * cases, the file isn't included and perfs are much better. 
    *
    * @param string $msg error message
    * @param int $code error code
    * @access public
    */
    function raiseError($msg, $code)
    {
        include_once('PEAR.php');
        PEAR::raiseError($msg, $code, $this->_pearErrorMode);
    }

    // --- Private methods ---
    
    /**
    * Make a file name (with path)
    *
    * @param string $id cache id
    * @param string $group name of the group
    * @access private
    */
    function _setFileName($id, $group)
    {
        $this->_file = ($this->_cacheDir.'cache_'.$group.'_'.md5($id));
    }
    
    /**
    * Read the cache file and return the content
    *
    * @return string content of the cache file
    * @access private
    */
    function _read()
    {
        $fp = @fopen($this->_file, "r");
        if ($this->_fileLocking) @flock($fp, LOCK_SH);
        if ($fp) {
            $length = @filesize($this->_file);
            if ($this->_readControl) {
                $hashControl = @fread($fp, 32);
                $length = $length - 32;
            } 
            $data = @fread($fp, $length);
            if ($this->_fileLocking) @flock($fp, LOCK_UN);
            @fclose($fp);
            if ($this->_readControl) {
                $hashData = $this->_hash($data, $this->_readControlType);
                if ($hashData != $hashControl) {
                    @touch($this->_file, time() - 2*abs($this->_lifeTime)); 
                    return false;
                }
            }
            return $data;
        }
        Cache_Lite::raiseError('Cache_Lite : Unable to read cache !', -2);   
        return false;
    }
    
    /**
    * Write the given data in the cache file
    *
    * @param string $data data to put in cache
    * @return boolean true if ok
    * @access private
    */
    function _write($data)
    {
        $fp = @fopen($this->_file, "w");
        if ($fp) {
            if ($this->_fileLocking) @flock($fp, LOCK_EX);
            if ($this->_readControl) {
                @fwrite($fp, $this->_hash($data, $this->_readControlType), 32);
            }
            $len = strlen($data);
            @fwrite($fp, $data, $len);
            if ($this->_fileLocking) @flock($fp, LOCK_UN);
            @fclose($fp);
            return true;
        }
        Cache_Lite::raiseError('Cache_Lite : Unable to write cache !', -1);
        return false;
    }
    
    /**
    * Write the given data in the cache file and control it just after to avoir corrupted cache entries
    *
    * @param string $data data to put in cache
    * @return boolean true if the test is ok
    * @access private
    */
    function _writeAndControl($data)
    {
        $this->_write($data);
        $dataRead = $this->_read($data);
        return ($dataRead==$data);
    }
    
    /**
    * Make a control key with the string containing datas
    *
    * @param string $data data
    * @param string $controlType type of control 'md5', 'crc32' or 'strlen'
    * @return string control key
    * @access private
    */
    function _hash($data, $controlType)
    {
        switch ($controlType) {
        case 'md5':
            return md5($data);
        case 'crc32':
            return sprintf('% 32d', crc32($data));
        case 'strlen':
            return sprintf('% 32d', strlen($data));
        default:
            $this->raiseError('Unknown controlType ! (available values are only \'md5\', \'crc32\', \'strlen\')', -5);
        }
    }
    
} 

?>