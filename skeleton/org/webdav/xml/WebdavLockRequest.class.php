<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'xml.Tree',
    'org.webdav.WebdavScriptletRequest',
    'org.webdav.WebdavLock'
  );

  define('WEBDAV_LOCKTYPE_READ',    'read');
  define('WEBDAV_LOCKTYPE_WRITE',   'write');
  define('WEBDAV_LOCKTYPE_UNKNOWN', 0x02);

  define('WEBDAV_LOCKSCOPE_EXCL',    'exclusive');
  define('WEBDAV_LOCKSCOPE_SHARED',  'shared');
  define('WEBDAV_LOCKSCOPE_UNKNOWN', 0x02);

  /**
   * Lock Request XML
   *
   * LOCK xml
   * <pre>
   *   <?xml version="1.0" encoding="utf-8" ?>
   *   <A:lockinfo xmlns:A="DAV:">
   *     <A:locktype>
   *       <A:write/>
   *     </A:locktype>
   *     <A:lockscope>
   *       <A:exclusive/>
   *     </A:lockscope>
   *    <A:owner>
   *        <A:href>DAV Explorer</A:href>
   *    </A:owner>
   * </A:lockinfo>
   * </pre>
   *
   * ANSWER
   * <pre>
   * </pre>
   *
   * @purpose  Encapsulate LOCK  XML request
   * @see      xp://org.webdav.WebdavScriptlet#doLock
   */
  class WebdavLockRequest extends WebdavScriptletRequest {
    var
      $properties=  array(),
      $namespaces=  array();
      
    
    /**
     * register an Lock-request
     *
     * @access  public
     * @param   string name
     */
    function registerLock(
      $owner,
      $lktype,
      $lkscope,
      $lktoken= NULL,
      $filename= NULL,
      $timeout,
      $depth) {

      with ($lockprop= &new WebdavLock($filename)); {
        $lockprop->setOwner($owner);
        $lockprop->setLockType($lktype);
        $lockprop->setLockScope($lkscope);
        $lockprop->setLockToken($lktoken);
        $lockprop->setTimeout($timeout);
        $lockprop->setDepth($depth);
      }
      
      $this->properties= $lockprop;
    }
    
    /**
     * Get all properties
     *
     * @access  public
     * @return  &string[] properties
     */
    function &getProperties() {
      return $this->properties;
    }

    /**
     * Get all namespaces
     *
     * @access  public
     * @return  &string[] properties
     */
    function &getNamespaces() {
      return $this->namespaces;
    }

    /**
     * Set data and parse for properties
     *
     * @access public
     * @param  string data The data
     */
    function setData(&$data) {    
      parent::setData($data);
      
      if (!isset($trans)) $trans= array_flip(get_html_translation_table(HTML_ENTITIES));

      $node= $this->getNode('/lockinfo/locktype');
      switch ($node->children[0]->name) {
        case 'read':
          $lktype= WEBDAV_LOCKTYPE_READ;
          break;
        case 'write': // set Propertie
          $lktype= WEBDAV_LOCKTYPE_WRITE;
          break;
        default:
          $lktype= WEBDAV_LOCKTYPE_UNKNOWN;
          break;
      }

      $node= $this->getNode('/lockinfo/lockscope');
      switch ($node->children[0]->name) {
        case 'exclusive': // READ-LOCK
          $lkscope= WEBDAV_LOCKSCOPE_EXCL;
          break;
        case 'shared': // set Property
          $lkscope= WEBDAV_LOCKSCOPE_SHARED;
          break;
        default:
          $lkscope= WEBDAV_LOCKSCOPE_UNKNOWN;
          break;
      }

      // owner
      if (($node= $this->getNode('/lockinfo/owner/href')) != '') {
        $owner= utf8_decode(preg_replace('/&#([0-9]+);/me', 
          'chr("\1")', 
          strtr(trim($node->children->content), $trans))
        );
      }

      // if we dont have a user in the request, take it from the authorization
      if (empty($owner) && $this->getUser() !== NULL) {
        $user= $this->getUser();
        $owner= $user->getUsername();
      }
      
      // Locktoken
      if (($node= $this->getNode('/lockinfo/token/href')) != '') {
        $lktoken= utf8_decode(preg_replace('/&#([0-9]+);/me', 
          'chr("\1")', 
          strtr(trim($node->children->content), $trans))
        );
      } else {
        $lktoken= $this->getHeader('Lock-Token');
      }
    
      switch ($this->getHeader('depth')) {
        case 0: $depth= 0x00000000; break;
        case 'infinity': 
        default: $depth= 'infinity'; break;
      }

      return $this->registerLock(
        $owner,
        $lktype,
        $lkscope,
        $lktoken,
        $this->getPath(),
        $this->getHeader('timeout'),
        $depth
      );
    }
  }
?>
