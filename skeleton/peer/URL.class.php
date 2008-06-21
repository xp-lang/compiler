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
   * Usage example:
   * <code>
   *   $u= new URL('http://user:pass@foo.bar:8081/news/1,2,6100.html?a=a#frag');
   *   echo $u->toString();
   * </code>
   *
   * @test   xp://net.xp_framework.unittest.peer.URLTest
   * @see    php://parse_url
   */
  class URL extends Object {
    public
      $_info = array();
      
    /**
     * Constructor
     *
     * @param   string str
     */
    public function __construct($str= NULL) {
      if (NULL !== $str) $this->setURL($str);
    }

    /**
     * Create a nice string representation
     *
     * @return  string
     * @see     xp://lang.Object#toString
     */
    public function toString() {
      return sprintf(
        "%s@ {\n".
        "  [scheme]      %s\n".
        "  [host]        %s\n".
        "  [port]        %d\n".
        "  [user]        %s\n".
        "  [password]    %s\n".
        "  [path]        %s\n".
        "  [query]       %s\n".
        "  [fragment]    %s\n".
        "}",
        $this->getClassName(),
        $this->getScheme(),
        $this->getHost(),
        $this->getPort(),
        $this->getUser(),
        $this->getPassword(),
        $this->getPath(),
        $this->getQuery(),
        $this->getFragment()
      );
    }

    /**
     * Retrieve scheme
     *
     * @param   mixed default default NULL  
     * @return  string scheme or default if none is set
     */
    public function getScheme($default= NULL) {
      return isset($this->_info['scheme']) ? $this->_info['scheme'] : $default;
    }
    
    /**
     * Set scheme
     *
     * @param   string scheme
     * @return  peer.URL this object
     */
    public function setScheme($scheme) {
      $this->_info['scheme']= $scheme;
      unset($this->_info['url']);
      return $this;
    }

    /**
     * Retrieve host
     *
     * @param   mixed default default NULL  
     * @return  string host or default if none is set
     */
    public function getHost($default= NULL) {
      return isset($this->_info['host']) ? $this->_info['host'] : $default;
    }
    
    /**
     * Set host
     *
     * @param   string host 
     * @return  peer.URL this object
     */
    public function setHost($host) {
      $this->_info['host']= $host;
      unset($this->_info['url']);
      return $this;
    }

    /**
     * Retrieve path
     *
     * @param   mixed default default NULL  
     * @return  string path or default if none is set
     */
    public function getPath($default= NULL) {
      return isset($this->_info['path']) ? $this->_info['path'] : $default;
    }
    
    /**
     * Set path
     *
     * @param   string path 
     * @return  peer.URL this object
     */
    public function setPath($path) {
      $this->_info['path']= $path;
      unset($this->_info['url']);
      return $this;
    }    

    /**
     * Retrieve user
     *
     * @param   mixed default default NULL  
     * @return  string user or default if none is set
     */
    public function getUser($default= NULL) {
      return isset($this->_info['user']) ? $this->_info['user'] : $default;
    }
    
    /**
     * Set user
     *
     * @param   string user 
     * @return  peer.URL this object
     */
    public function setUser($user) {
      $this->_info['user']= $user;
      unset($this->_info['url']);
      return $this;
    }    

    /**
     * Retrieve password
     *
     * @param   mixed default default NULL  
     * @return  string password or default if none is set
     */
    public function getPassword($default= NULL) {
      return isset($this->_info['pass']) ? $this->_info['pass'] : $default;
    }

    /**
     * Set password
     *
     * @param   string password 
     * @return  peer.URL this object
     */
    public function setPassword($password) {
      $this->_info['pass']= $password;
      unset($this->_info['url']);
      return $this;
    }    

    /**
     * Retrieve query
     *
     * @param   mixed default default NULL  
     * @return  string query or default if none is set
     */
    public function getQuery($default= NULL) {
      return isset($this->_info['query']) ? $this->_info['query'] : $default;
    }

    /**
     * Set query
     *
     * @param   string query 
     * @return  peer.URL this object
     */
    public function setQuery($query) {
      $this->_info['query']= $query;
      parse_str($this->_info['query'], $this->_info['params']);
      unset($this->_info['url']);
      return $this;
    }

    /**
     * Retrieve fragment
     *
     * @param   mixed default default NULL  
     * @return  string fragment or default if none is set
     */
    public function getFragment($default= NULL) {
      return isset($this->_info['fragment']) ? $this->_info['fragment'] : $default;
    }

    /**
     * Set fragment
     *
     * @param   string fragment 
     * @return  peer.URL this object
     */
    public function setFragment($fragment) {
      $this->_info['fragment']= $fragment;
      unset($this->_info['url']);
      return $this;
    }

    /**
     * Retrieve port
     *
     * @param   mixed default default NULL  
     * @return  int port or default if none is set
     */
    public function getPort($default= NULL) {
      return isset($this->_info['port']) ? $this->_info['port'] : $default;
    }
    
    /**
     * Set port
     *
     * @param   int port 
     * @return  peer.URL this object
     */
    public function setPort($port) {
      $this->_info['port']= $port;
      unset($this->_info['url']);
      return $this;
    }

    /**
     * Retrieve parameter by a specified name
     *
     * @param   string name
     * @param   mixed default default NULL  
     * @return  string url-decoded parameter value or default if none is set
     */
    public function getParam($name, $default= NULL) {
      return isset($this->_info['params'][$name]) ? $this->_info['params'][$name] : $default;
    }

    /**
     * Retrieve parameters
     *
     * @return  array params
     */
    public function getParams() {
      return $this->_info['params'];
    }
    
    /**
     * Add a parameter
     *
     * @param   string key
     * @param   string value
     * @return  peer.URL this object
     */
    public function addParam($key, $value) {
      if (!isset($this->_info['query'])) {
        $this->_info['query']= sprintf(
          '%s=%s',
          urlencode($key),
          urlencode($value)
        );          
      } else {
        $this->_info['query'].= sprintf(
          '&%s=%s',
          urlencode($key),
          urlencode($value)
        );
      }
      parse_str($this->_info['query'], $this->_info['params']); 
      unset($this->_info['url']);   // Indicate recalculation is needed
      return $this;
    }

    /**
     * Add parameters from an associative array. The key is taken as
     * parameter name and the value as parameter value.
     *
     * @param   array hash
     */
    public function addParams($hash) {
      if (!isset($this->_info['query'])) {
        $this->_info['query']= '';
      } else {
        $this->_info['query'].= '&';
      }
      
      foreach (array_keys($hash) as $key) {
        $this->_info['query'].= sprintf(
          '%s=%s&',
          urlencode($key),
          urlencode($hash[$key])
        );
      }
      $this->_info['query']= substr($this->_info['query'], 0, -1);
      parse_str($this->_info['query'], $this->_info['params']); 
      unset($this->_info['url']);   // Indicate recalculation is needed
      return $this;
    }

    /**
     * Retrieve whether parameters exist
     *
     * @return  bool
     */
    public function hasParams() {
      return !empty($this->_info['params']);
    }
    
    /**
     * Get full URL
     *
     * @return  string
     */
    public function getURL() {
      if (!isset($this->_info['url'])) {
        $this->_info['url']= $this->_info['scheme'].'://';
        if (isset($this->_info['user'])) $this->_info['url'].= sprintf(
          '%s%s@',
          $this->_info['user'],
          (isset($this->_info['pass']) ? ':'.$this->_info['pass'] : '')
        );
        $this->_info['url'].= $this->_info['host'];
        isset($this->_info['port']) && $this->_info['url'].= ':'.$this->_info['port'];
        isset($this->_info['path']) && $this->_info['url'].= $this->_info['path'];
        isset($this->_info['query']) && $this->_info['url'].= '?'.$this->_info['query'];
        isset($this->_info['fragment']) && $this->_info['url'].= '#'.$this->_info['fragment'];
      }
      return $this->_info['url'];
    }
    
    /**
     * Set full URL
     *
     * @param   string str URL
     */
    public function setURL($str) {
      $this->_info= parse_url($str);
      if (isset($this->_info['user'])) $this->_info['user']= rawurldecode($this->_info['user']);
      if (isset($this->_info['pass'])) $this->_info['pass']= rawurldecode($this->_info['pass']);
      if (isset($this->_info['query'])) {
        parse_str($this->_info['query'], $this->_info['params']);
      } else {
        $this->_info['params']= array();
      }
      $this->_info['url']= $str;
    }

    /**
     * Returns a hashcode for this URL
     *
     * @return  string
     */
    public function hashCode() {
      return md5($this->_info['url']);
    }
    
    /**
     * Returns whether a given object is equal to this.
     *
     * @param   lang.Object cmp
     * @return  bool
     */
    public function equals($cmp) {
      return $cmp instanceof self && $this->getURL() == $cmp->getURL();
    }
  }
?>
