<?php
/* Diese Klasse ist Teil des XP-Frameworks
 * 
 * $Id$
 */
 
  /**
   * Kapselt SQL-Exceptions
   * 
   * Besonderes:
   * - in e->sql findet sich - falls vorhanden - der Query-String, in e->code der SQL-Returncode
   * - SQL-Returncode ist bspw. bei einer Sybase 1205 f�r Deadlock
   *
   * @see Exception
   */
  class SQLException extends Exception {
    var 
      $sql,
      $code;
  }
?>
