<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  /**
   * Represents a Uniform Resource Locator 
   *
   * Warning:
   * This class does not validate the URL, it simply tries its best
   * in parsing it.
   *
   * @see	php-doc://parse_url
   */
  class URL extends Object {
	var
	  $_info;

    /**
     * Constructor
     *
     * @access  public
     * @param   string str
     */
    function __construct($str) {
	  $this->setURL($str);
	  parent::__construct();
	}

	/**
	 * Retreive scheme
	 *
	 * @access	public
	 * @return	string scheme or NULL if none is set
	 */
	function getScheme() {
	  return isset($this->_info['scheme']) ? $this->_info['scheme'] : NULL;
	}

	/**
	 * Retreive host
	 *
	 * @access	public
	 * @return	string host or NULL if none is set
	 */
	function getHost() {
	  return isset($this->_info['host']) ? $this->_info['host'] : NULL;
	}

	/**
	 * Retreive path
	 *
	 * @access	public
	 * @return	string path or NULL if none is set
	 */
	function getPath() {
	  return isset($this->_info['path']) ? $this->_info['path'] : NULL;
	}

	/**
	 * Retreive user
	 *
	 * @access	public
	 * @return	string user or NULL if none is set
	 */
	function getUser() {
	  return isset($this->_info['user']) ? $this->_info['user'] : NULL;
	}

	/**
	 * Retreive password
	 *
	 * @access	public
	 * @return	string password or NULL if none is set
	 */
	function getPassword() {
	  return isset($this->_info['password']) ? $this->_info['password'] : NULL;
	}

	/**
	 * Retreive query
	 *
	 * @access	public
	 * @return	string query or NULL if none is set
	 */
	function getQuery() {
	  return isset($this->_info['query']) ? $this->_info['query'] : NULL;
	}

	/**
	 * Retreive fragment
	 *
	 * @access	public
	 * @return	string fragment or NULL if none is set
	 */
	function getFragment() {
	  return isset($this->_info['fragment']) ? $this->_info['fragment'] : NULL;
	}

	/**
	 * Retreive port
	 *
	 * @access	public
	 * @return	int port or NULL if none is set
	 */
	function getPort() {
	  return isset($this->_info['port']) ? $this->_info['port'] : NULL;
	}
	
    /**
     * Get full URL
     *
     * @access  public
     * @return  string
     */
	function getURL() {
	  return $this->_info['url'];
	}
	
    /**
     * Set full URL
     *
     * @access  public
     * @param   string str URL
     */
	function setURL($str) {
	  $this->_info= parse_url($str);
	  $this->_info['url']= $str;
    }
  }
?>
