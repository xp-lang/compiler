<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('demo.Help');

  /**
   * Application
   *
   */
  class Application extends Object {
    
    /**
     * Entry point
     *
     * @param   string[] args
     */
    public static function main($args) {
      $begin= new Date();
      $end= Date::now();
      $help= new Help();
    }
  }
?>
