<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('io.File');

  /**
   * Implements the StorageElement via filesystem
   *
   * @ext      posix
   * @purpose  StorageElement
   */
  class FilesystemStorageElement extends Object {
    var
      $f    = NULL,
      $st   = array();

    /**
     * Constructor
     *
     * @access  public
     * @return  string uri
     */
    function __construct($uri) { 
      $this->f= &new File($uri);
      $this->st= stat($this->f->getURI());
      $this->st['pwuid']= posix_getpwuid($this->st['uid']);
      $this->st['grgid']= posix_getgrgid($this->st['gid']);
    }

    /**
     * Deletes an entry
     *
     * @access  public
     * @return  bool TRUE to indicate success
     */
    function delete() { 
      return $this->f->unlink();
    }

    /**
     * Renames an entry
     *
     * @access  public
     * @param   string target
     * @return  bool TRUE to indicate success
     */
    function rename($target) { 
      return $this->f->move($target);
    }

    /**
     * Retrieves the (short) name of a storage entry
     *
     * @access  public
     * @return  string
     */  
    function getName() { 
      return basename($this->f->getURI());
    }
    
    /**
     * Retrieves the owner's username
     *
     * @access  public
     * @return  string
     */  
    function getOwner() { 
      return $this->st['pwuid']['name'];
    }

    /**
     * Retrieves the owner's group name
     *
     * @access  public
     * @return  string
     */  
    function getGroup() {
      return $this->st['grgid']['name'];
    }
    
    /**
     * Retrieves the size of this storage entry
     *
     * @access  public
     * @return  int bytes
     */  
    function getSize() { 
      return $this->st['size'];
    }

    /**
     * Retrieves the modified timestamp of this storage entry
     *
     * @access  public
     * @return  int unix timestamp
     */  
    function getModifiedStamp() { 
      return $this->st['mtime'];
    }
    
    /**
     * Retrieves the permissions of this storage entry expressed in a
     * unix-permission style integer
     *
     * @see     http://www.google.com/search?ie=UTF8&q=Unix%20permissions
     * @access  public
     * @return  int
     */  
    function getPermissions() { 
      return $this->st['mode'];
    }

    /**
     * Sets the permissions of this storage entry expressed in a
     * unix-permission style integer
     *
     * @access  public
     * @param   int permissions
     */  
    function setPermissions($permissions) { 
      chmod($this->f->getURI(), $permissions);
      $this->st['mode']= $permissions;
    }

    /**
     * Retrieves the number of links
     *
     * @access  protected
     * @return  string
     */
    function numLinks() {
      return $this->st['nlink'];
    }

    /**
     * Open this element with a specified mode
     *
     * @access  public
     * @param   string mode of of the SE_* constants
     */
    function open($mode) { 
      switch ($mode) {
        case SE_READ: return $this->f->open(FILE_MODE_READ);
        case SE_WRITE: return $this->f->open(FILE_MODE_WRITE);
      }
    }
    
    /**
     * Read a chunk of data from this element
     *
     * @access  public
     * @return  string
     */
    function read() { 
      return $this->f->read();
    }
    
    /**
     * Write a chunk of data to this element
     *
     * @access  public
     * @param   string buf
     */
    function write($buf) { 
      return $this->f->write($buf);
    }
    
    /**
     * Close this element
     *
     * @access  public
     */
    function close() { 
      return $this->f->close();
    }
    
  } implements(__FILE__, 'peer.ftp.server.storage.StorageElement');
?>
