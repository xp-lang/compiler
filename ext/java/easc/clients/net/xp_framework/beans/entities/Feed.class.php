<?php
/* This file is part of the XP framework's EASC API
 *
 * $Id$
 */

  uses('net.xp_framework.beans.entities.FeedValue');

  /**
   * Feed remote interface
   *
   * @purpose  Demo class  
   */
  class Feed extends Interface {

    /**
     * Set Bz_id
     *
     * @access  public
     * @param   int bz_id
     */
    function setBz_id($bz_id) { }

    /**
     * Get Bz_id
     *
     * @access  public
     * @return  int
     */
    function getBz_id() { }

    /**
     * Gets value object
     *
     * @access  public
     * @return  &net.xp_framework.beans.entities.FeedValue
     */  
    function &getFeedValue() { }
    
    /**
     * Gets feed's id (primary key)
     *
     * @access  public
     * @return  java.lang.Long
     */
    function getFeed_id() { }

    /**
     * Sets the feed's id
     *
     * @access public
     * @param  java.lang.Long feed_id
     */
    function setFeed_id($feed_id) { }

    /**
     * Sets feed's title
     *
     * @access  public
     * @return  java.lang.String
     */
    function getTitle() { }

    /**
     * Gets feed's title
     *
     * @access public
     * @param  java.lang.String title
     */
    function setTitle($title) { }

    /**
     * Sets feed's url
     *
     * @access  public
     * @return  java.lang.String
     */
    function getUrl() { }

    /**
     * Gets feed's url
     *
     * @access public
     * @param  java.lang.String url
     */
    function setUrl($url) { }
  }
?>
