<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Observer interface
   *
   * @see      &util.Observable
   * @purpose  Interface
   */
  class LogObserver extends Object {
    var
      $cat=   NULL;

    /**
     * Retrieve instance bound to log category.
     *
     * @access  public
     * @param   string arg
     * @return  &util.log.LogObserver
     */
    function &instanceFor($arg) {
      static $inst= array();
      
      if (!isset ($inst[$arg])) {
        $log= &Logger::getInstance();
        $inst[$arg]= &new LogObserver();
        $inst[$arg]->cat= &$log->getCategory($arg);
      }
      
      return $inst[$arg];
    }
  
    /**
     * Update method
     *
     * @access  public
     * @param   &util.Observable obs
     * @param   mixed arg default NULL
     */
    function update(&$obs, $arg= NULL) {
      $this->cat->debug($arg);
    }

  } implements (__FILE__, 'rdbms.DBObserver');
?>
