<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses('rdbms.DataSet', 'org.bugzilla.BugConstants');
 
  /**
   * Class wrapper for table bugs, database bugs
   * (Auto-generated on Wed, 12 Nov 2003 14:18:29 +0100 by thekid)
   *
   * @purpose  Datasource accessor
   */
  class Bug extends DataSet {
    var
      $bug_id               = 0,
      $groupset             = 0,
      $assigned_to          = 0,
      $bug_file_loc         = '',
      $bug_severity         = '',
      $bug_status           = '',
      $creation_ts          = NULL,
      $delta_ts             = NULL,
      $short_desc           = '',
      $op_sys               = '',
      $priority             = '',
      $product              = '',
      $rep_platform         = '',
      $reporter             = 0,
      $version              = '',
      $component            = '',
      $resolution           = '',
      $target_milestone     = '',
      $qa_contact           = 0,
      $status_whiteboard    = '',
      $votes                = 0,
      $keywords             = '',
      $lastdiffed           = NULL,
      $everconfirmed        = 0,
      $reporter_accessible  = 0,
      $cclist_accessible    = 0;

    /**
     * Gets an instance of this object by index "PRIMARY"
     *
     * @access  static
     * @param   int bug_id
     * @return  &org.bugzilla.db.Bug object
     * @throws  rdbms.SQLException in case an error occurs
     */
    function &getByBug_id($bug_id) {
      $cm= &ConnectionManager::getInstance();  
      try(); {
        $db= &$cm->getByHost('bugzilla', 0);
        $q= &$db->query('
          select
            bug_id,
            groupset,
            assigned_to,
            bug_file_loc,
            bug_severity,
            bug_status,
            creation_ts,
            delta_ts,
            short_desc,
            op_sys,
            priority,
            product,
            rep_platform,
            reporter,
            version,
            component,
            resolution,
            target_milestone,
            qa_contact,
            status_whiteboard,
            votes,
            keywords,
            lastdiffed,
            everconfirmed,
            reporter_accessible,
            cclist_accessible
          from
            bugs..bugs 
          where
            bug_id = %d
          ', 
          $bug_id
        );
        if ($q && $r= $q->next()) $data= &new Bug($r); else $data= NULL;
      } if (catch('SQLException', $e)) {
        return throw($e);
      }

      return $data;
    }

    /**
     * Gets an instance of this object by index "assigned_to"
     *
     * @access  static
     * @param   int assigned_to
     * @return  &org.bugzilla.db.Bug[] objects
     * @throws  rdbms.SQLException in case an error occurs
     */
    function &getByAssigned_to($assigned_to) {
      $cm= &ConnectionManager::getInstance();  
      try(); {
        $db= &$cm->getByHost('bugzilla', 0);
        $q= &$db->query('
          select
            bug_id,
            groupset,
            assigned_to,
            bug_file_loc,
            bug_severity,
            bug_status,
            creation_ts,
            delta_ts,
            short_desc,
            op_sys,
            priority,
            product,
            rep_platform,
            reporter,
            version,
            component,
            resolution,
            target_milestone,
            qa_contact,
            status_whiteboard,
            votes,
            keywords,
            lastdiffed,
            everconfirmed,
            reporter_accessible,
            cclist_accessible
          from
            bugs..bugs 
          where
            assigned_to = %d
          ', 
          $assigned_to
        );

        $data= array();
        if ($q) while ($r= $q->next()) {
          $data[]= &new Bug($r);
        }
      } if (catch('SQLException', $e)) {
        return throw($e);
      }

      return $data;
    }

    /**
     * Gets an instance of this object by index "creation_ts"
     *
     * @access  static
     * @param   util.Date creation_ts
     * @return  &org.bugzilla.db.Bug[] objects
     * @throws  rdbms.SQLException in case an error occurs
     * @throws  lang.IllegalAccessException in case there is no suitable database connection available
     */
    function &getByCreation_ts($creation_ts) {
      $cm= &ConnectionManager::getInstance();  

      try(); {
        $db= &$cm->getByHost('bugzilla', 0);
        $q= &$db->query('
          select
            bug_id,
            groupset,
            assigned_to,
            bug_file_loc,
            bug_severity,
            bug_status,
            creation_ts,
            delta_ts,
            short_desc,
            op_sys,
            priority,
            product,
            rep_platform,
            reporter,
            version,
            component,
            resolution,
            target_milestone,
            qa_contact,
            status_whiteboard,
            votes,
            keywords,
            lastdiffed,
            everconfirmed,
            reporter_accessible,
            cclist_accessible
          from
            bugs..bugs 
          where
            creation_ts = %s
          ', 
          $creation_ts
        );

        $data= array();
        if ($q) while ($r= $q->next()) {
          $data[]= &new Bug($r);
        }
      } if (catch('SQLException', $e)) {
        return throw($e);
      }

      return $data;
    }

    /**
     * Gets an instance of this object by index "delta_ts"
     *
     * @access  static
     * @param   util.Date delta_ts
     * @return  &org.bugzilla.db.Bug[] objects
     * @throws  rdbms.SQLException in case an error occurs
     * @throws  lang.IllegalAccessException in case there is no suitable database connection available
     */
    function &getByDelta_ts($delta_ts) {
      $cm= &ConnectionManager::getInstance();  

      try(); {
        $db= &$cm->getByHost('bugzilla', 0);
        $q= &$db->query('
          select
            bug_id,
            groupset,
            assigned_to,
            bug_file_loc,
            bug_severity,
            bug_status,
            creation_ts,
            delta_ts,
            short_desc,
            op_sys,
            priority,
            product,
            rep_platform,
            reporter,
            version,
            component,
            resolution,
            target_milestone,
            qa_contact,
            status_whiteboard,
            votes,
            keywords,
            lastdiffed,
            everconfirmed,
            reporter_accessible,
            cclist_accessible
          from
            bugs..bugs 
          where
            delta_ts = %s
          ', 
          $delta_ts
        );

        $data= array();
        if ($q) while ($r= $q->next()) {
          $data[]= &new Bug($r);
        }
      } if (catch('SQLException', $e)) {
        return throw($e);
      }

      return $data;
    }

    /**
     * Gets an instance of this object by index "bug_severity"
     *
     * @access  static
     * @param   string bug_severity
     * @return  &org.bugzilla.db.Bug[] objects
     * @throws  rdbms.SQLException in case an error occurs
     * @throws  lang.IllegalAccessException in case there is no suitable database connection available
     */
    function &getByBug_severity($bug_severity) {
      $cm= &ConnectionManager::getInstance();  

      try(); {
        $db= &$cm->getByHost('bugzilla', 0);
        $q= &$db->query('
          select
            bug_id,
            groupset,
            assigned_to,
            bug_file_loc,
            bug_severity,
            bug_status,
            creation_ts,
            delta_ts,
            short_desc,
            op_sys,
            priority,
            product,
            rep_platform,
            reporter,
            version,
            component,
            resolution,
            target_milestone,
            qa_contact,
            status_whiteboard,
            votes,
            keywords,
            lastdiffed,
            everconfirmed,
            reporter_accessible,
            cclist_accessible
          from
            bugs..bugs 
          where
            bug_severity = %s
          ', 
          $bug_severity
        );

        $data= array();
        if ($q) while ($r= $q->next()) {
          $data[]= &new Bug($r);
        }
      } if (catch('SQLException', $e)) {
        return throw($e);
      }

      return $data;
    }

    /**
     * Gets an instance of this object by index "bug_status"
     *
     * @access  static
     * @param   string bug_status
     * @return  &org.bugzilla.db.Bug[] objects
     * @throws  rdbms.SQLException in case an error occurs
     * @throws  lang.IllegalAccessException in case there is no suitable database connection available
     */
    function &getByBug_status($bug_status) {
      $cm= &ConnectionManager::getInstance();  

      try(); {
        $db= &$cm->getByHost('bugzilla', 0);
        $q= &$db->query('
          select
            bug_id,
            groupset,
            assigned_to,
            bug_file_loc,
            bug_severity,
            bug_status,
            creation_ts,
            delta_ts,
            short_desc,
            op_sys,
            priority,
            product,
            rep_platform,
            reporter,
            version,
            component,
            resolution,
            target_milestone,
            qa_contact,
            status_whiteboard,
            votes,
            keywords,
            lastdiffed,
            everconfirmed,
            reporter_accessible,
            cclist_accessible
          from
            bugs..bugs 
          where
            bug_status = %s
          ', 
          $bug_status
        );

        $data= array();
        if ($q) while ($r= $q->next()) {
          $data[]= &new Bug($r);
        }
      } if (catch('SQLException', $e)) {
        return throw($e);
      }

      return $data;
    }

    /**
     * Gets an instance of this object by index "op_sys"
     *
     * @access  static
     * @param   string op_sys
     * @return  &org.bugzilla.db.Bug[] objects
     * @throws  rdbms.SQLException in case an error occurs
     * @throws  lang.IllegalAccessException in case there is no suitable database connection available
     */
    function &getByOp_sys($op_sys) {
      $cm= &ConnectionManager::getInstance();  

      try(); {
        $db= &$cm->getByHost('bugzilla', 0);
        $q= &$db->query('
          select
            bug_id,
            groupset,
            assigned_to,
            bug_file_loc,
            bug_severity,
            bug_status,
            creation_ts,
            delta_ts,
            short_desc,
            op_sys,
            priority,
            product,
            rep_platform,
            reporter,
            version,
            component,
            resolution,
            target_milestone,
            qa_contact,
            status_whiteboard,
            votes,
            keywords,
            lastdiffed,
            everconfirmed,
            reporter_accessible,
            cclist_accessible
          from
            bugs..bugs 
          where
            op_sys = %s
          ', 
          $op_sys
        );

        $data= array();
        if ($q) while ($r= $q->next()) {
          $data[]= &new Bug($r);
        }
      } if (catch('SQLException', $e)) {
        return throw($e);
      }

      return $data;
    }

    /**
     * Gets an instance of this object by index "priority"
     *
     * @access  static
     * @param   string priority
     * @return  &org.bugzilla.db.Bug[] objects
     * @throws  rdbms.SQLException in case an error occurs
     * @throws  lang.IllegalAccessException in case there is no suitable database connection available
     */
    function &getByPriority($priority) {
      $cm= &ConnectionManager::getInstance();  

      try(); {
        $db= &$cm->getByHost('bugzilla', 0);
        $q= &$db->query('
          select
            bug_id,
            groupset,
            assigned_to,
            bug_file_loc,
            bug_severity,
            bug_status,
            creation_ts,
            delta_ts,
            short_desc,
            op_sys,
            priority,
            product,
            rep_platform,
            reporter,
            version,
            component,
            resolution,
            target_milestone,
            qa_contact,
            status_whiteboard,
            votes,
            keywords,
            lastdiffed,
            everconfirmed,
            reporter_accessible,
            cclist_accessible
          from
            bugs..bugs 
          where
            priority = %s
          ', 
          $priority
        );

        $data= array();
        if ($q) while ($r= $q->next()) {
          $data[]= &new Bug($r);
        }
      } if (catch('SQLException', $e)) {
        return throw($e);
      }

      return $data;
    }

    /**
     * Gets an instance of this object by index "product"
     *
     * @access  static
     * @param   string product
     * @return  &org.bugzilla.db.Bug[] objects
     * @throws  rdbms.SQLException in case an error occurs
     * @throws  lang.IllegalAccessException in case there is no suitable database connection available
     */
    function &getByProduct($product) {
      $cm= &ConnectionManager::getInstance();  

      try(); {
        $db= &$cm->getByHost('bugzilla', 0);
        $q= &$db->query('
          select
            bug_id,
            groupset,
            assigned_to,
            bug_file_loc,
            bug_severity,
            bug_status,
            creation_ts,
            delta_ts,
            short_desc,
            op_sys,
            priority,
            product,
            rep_platform,
            reporter,
            version,
            component,
            resolution,
            target_milestone,
            qa_contact,
            status_whiteboard,
            votes,
            keywords,
            lastdiffed,
            everconfirmed,
            reporter_accessible,
            cclist_accessible
          from
            bugs..bugs 
          where
            product = %s
          ', 
          $product
        );

        $data= array();
        if ($q) while ($r= $q->next()) {
          $data[]= &new Bug($r);
        }
      } if (catch('SQLException', $e)) {
        return throw($e);
      }

      return $data;
    }

    /**
     * Gets an instance of this object by index "reporter"
     *
     * @access  static
     * @param   int reporter
     * @return  &org.bugzilla.db.Bug[] objects
     * @throws  rdbms.SQLException in case an error occurs
     * @throws  lang.IllegalAccessException in case there is no suitable database connection available
     */
    function &getByReporter($reporter) {
      $cm= &ConnectionManager::getInstance();  

      try(); {
        $db= &$cm->getByHost('bugzilla', 0);
        $q= &$db->query('
          select
            bug_id,
            groupset,
            assigned_to,
            bug_file_loc,
            bug_severity,
            bug_status,
            creation_ts,
            delta_ts,
            short_desc,
            op_sys,
            priority,
            product,
            rep_platform,
            reporter,
            version,
            component,
            resolution,
            target_milestone,
            qa_contact,
            status_whiteboard,
            votes,
            keywords,
            lastdiffed,
            everconfirmed,
            reporter_accessible,
            cclist_accessible
          from
            bugs..bugs 
          where
            reporter = %d
          ', 
          $reporter
        );

        $data= array();
        if ($q) while ($r= $q->next()) {
          $data[]= &new Bug($r);
        }
      } if (catch('SQLException', $e)) {
        return throw($e);
      }

      return $data;
    }

    /**
     * Gets an instance of this object by index "version"
     *
     * @access  static
     * @param   string version
     * @return  &org.bugzilla.db.Bug[] objects
     * @throws  rdbms.SQLException in case an error occurs
     * @throws  lang.IllegalAccessException in case there is no suitable database connection available
     */
    function &getByVersion($version) {
      $cm= &ConnectionManager::getInstance();  

      try(); {
        $db= &$cm->getByHost('bugzilla', 0);
        $q= &$db->query('
          select
            bug_id,
            groupset,
            assigned_to,
            bug_file_loc,
            bug_severity,
            bug_status,
            creation_ts,
            delta_ts,
            short_desc,
            op_sys,
            priority,
            product,
            rep_platform,
            reporter,
            version,
            component,
            resolution,
            target_milestone,
            qa_contact,
            status_whiteboard,
            votes,
            keywords,
            lastdiffed,
            everconfirmed,
            reporter_accessible,
            cclist_accessible
          from
            bugs..bugs 
          where
            version = %s
          ', 
          $version
        );

        $data= array();
        if ($q) while ($r= $q->next()) {
          $data[]= &new Bug($r);
        }
      } if (catch('SQLException', $e)) {
        return throw($e);
      }

      return $data;
    }

    /**
     * Gets an instance of this object by index "component"
     *
     * @access  static
     * @param   string component
     * @return  &org.bugzilla.db.Bug[] objects
     * @throws  rdbms.SQLException in case an error occurs
     * @throws  lang.IllegalAccessException in case there is no suitable database connection available
     */
    function &getByComponent($component) {
      $cm= &ConnectionManager::getInstance();  

      try(); {
        $db= &$cm->getByHost('bugzilla', 0);
        $q= &$db->query('
          select
            bug_id,
            groupset,
            assigned_to,
            bug_file_loc,
            bug_severity,
            bug_status,
            creation_ts,
            delta_ts,
            short_desc,
            op_sys,
            priority,
            product,
            rep_platform,
            reporter,
            version,
            component,
            resolution,
            target_milestone,
            qa_contact,
            status_whiteboard,
            votes,
            keywords,
            lastdiffed,
            everconfirmed,
            reporter_accessible,
            cclist_accessible
          from
            bugs..bugs 
          where
            component = %s
          ', 
          $component
        );

        $data= array();
        if ($q) while ($r= $q->next()) {
          $data[]= &new Bug($r);
        }
      } if (catch('SQLException', $e)) {
        return throw($e);
      }

      return $data;
    }

    /**
     * Gets an instance of this object by index "resolution"
     *
     * @access  static
     * @param   string resolution
     * @return  &org.bugzilla.db.Bug[] objects
     * @throws  rdbms.SQLException in case an error occurs
     * @throws  lang.IllegalAccessException in case there is no suitable database connection available
     */
    function &getByResolution($resolution) {
      $cm= &ConnectionManager::getInstance();  

      try(); {
        $db= &$cm->getByHost('bugzilla', 0);
        $q= &$db->query('
          select
            bug_id,
            groupset,
            assigned_to,
            bug_file_loc,
            bug_severity,
            bug_status,
            creation_ts,
            delta_ts,
            short_desc,
            op_sys,
            priority,
            product,
            rep_platform,
            reporter,
            version,
            component,
            resolution,
            target_milestone,
            qa_contact,
            status_whiteboard,
            votes,
            keywords,
            lastdiffed,
            everconfirmed,
            reporter_accessible,
            cclist_accessible
          from
            bugs..bugs 
          where
            resolution = %s
          ', 
          $resolution
        );

        $data= array();
        if ($q) while ($r= $q->next()) {
          $data[]= &new Bug($r);
        }
      } if (catch('SQLException', $e)) {
        return throw($e);
      }

      return $data;
    }

    /**
     * Gets an instance of this object by index "votes"
     *
     * @access  static
     * @param   int votes
     * @return  &org.bugzilla.db.Bug[] objects
     * @throws  rdbms.SQLException in case an error occurs
     * @throws  lang.IllegalAccessException in case there is no suitable database connection available
     */
    function &getByVotes($votes) {
      $cm= &ConnectionManager::getInstance();  

      try(); {
        $db= &$cm->getByHost('bugzilla', 0);
        $q= &$db->query('
          select
            bug_id,
            groupset,
            assigned_to,
            bug_file_loc,
            bug_severity,
            bug_status,
            creation_ts,
            delta_ts,
            short_desc,
            op_sys,
            priority,
            product,
            rep_platform,
            reporter,
            version,
            component,
            resolution,
            target_milestone,
            qa_contact,
            status_whiteboard,
            votes,
            keywords,
            lastdiffed,
            everconfirmed,
            reporter_accessible,
            cclist_accessible
          from
            bugs..bugs 
          where
            votes = %d
          ', 
          $votes
        );

        $data= array();
        if ($q) while ($r= $q->next()) {
          $data[]= &new Bug($r);
        }
      } if (catch('SQLException', $e)) {
        return throw($e);
      }

      return $data;
    }

    /**
     * Retrieves bug_id
     *
     * @access  public
     * @return  int
     */
    function getBug_id() {
      return $this->bug_id;
    }
      
    /**
     * Sets bug_id
     *
     * @access  public
     * @param   int bug_id
     */
    function setBug_id($bug_id) {
      $this->bug_id= $bug_id;
    }
      
    /**
     * Retrieves groupset
     *
     * @access  public
     * @return  int
     */
    function getGroupset() {
      return $this->groupset;
    }
      
    /**
     * Sets groupset
     *
     * @access  public
     * @param   int groupset
     */
    function setGroupset($groupset) {
      $this->groupset= $groupset;
    }
      
    /**
     * Retrieves assigned_to
     *
     * @access  public
     * @return  int
     */
    function getAssigned_to() {
      return $this->assigned_to;
    }
      
    /**
     * Sets assigned_to
     *
     * @access  public
     * @param   int assigned_to
     */
    function setAssigned_to($assigned_to) {
      $this->assigned_to= $assigned_to;
    }
      
    /**
     * Retrieves bug_file_loc
     *
     * @access  public
     * @return  string
     */
    function getBug_file_loc() {
      return $this->bug_file_loc;
    }
      
    /**
     * Sets bug_file_loc
     *
     * @access  public
     * @param   string bug_file_loc
     */
    function setBug_file_loc($bug_file_loc) {
      $this->bug_file_loc= $bug_file_loc;
    }
      
    /**
     * Retrieves bug_severity
     *
     * @access  public
     * @return  string
     */
    function getBug_severity() {
      return $this->bug_severity;
    }
      
    /**
     * Sets bug_severity
     *
     * @access  public
     * @param   string bug_severity one of the BUG_SEVERITY_* constants
     * @see     xp://org.bugzilla.BugConstants
     */
    function setBug_severity($bug_severity) {
      $this->bug_severity= $bug_severity;
    }
      
    /**
     * Retrieves bug_status
     *
     * @access  public
     * @return  string
     */
    function getBug_status() {
      return $this->bug_status;
    }
      
    /**
     * Sets bug_status
     *
     * @access  public
     * @param   string bug_status one of the BUG_STATUS_* constants
     * @see     xp://org.bugzilla.BugConstants
     */
    function setBug_status($bug_status) {
      $this->bug_status= $bug_status;
    }
      
    /**
     * Retrieves creation_ts
     *
     * @access  public
     * @return  util.Date
     */
    function getCreation_ts() {
      return $this->creation_ts;
    }
      
    /**
     * Sets creation_ts
     *
     * @access  public
     * @param   util.Date creation_ts
     */
    function setCreation_ts($creation_ts) {
      $this->creation_ts= $creation_ts;
    }
      
    /**
     * Retrieves delta_ts
     *
     * @access  public
     * @return  util.Date
     */
    function getDelta_ts() {
      return $this->delta_ts;
    }
      
    /**
     * Sets delta_ts
     *
     * @access  public
     * @param   util.Date delta_ts
     */
    function setDelta_ts($delta_ts) {
      $this->delta_ts= $delta_ts;
    }
      
    /**
     * Retrieves short_desc
     *
     * @access  public
     * @return  string
     */
    function getShort_desc() {
      return $this->short_desc;
    }
      
    /**
     * Sets short_desc
     *
     * @access  public
     * @param   string short_desc
     */
    function setShort_desc($short_desc) {
      $this->short_desc= $short_desc;
    }
      
    /**
     * Retrieves op_sys
     *
     * @access  public
     * @return  string
     */
    function getOp_sys() {
      return $this->op_sys;
    }
      
    /**
     * Sets op_sys
     *
     * @access  public
     * @param   string op_sys
     */
    function setOp_sys($op_sys) {
      $this->op_sys= $op_sys;
    }
      
    /**
     * Retrieves priority
     *
     * @access  public
     * @return  string
     */
    function getPriority() {
      return $this->priority;
    }
      
    /**
     * Sets priority
     *
     * @access  public
     * @param   string priority one of the BUG_PRIORITY_* constants
     * @see     xp://org.bugzilla.BugConstants
     */
    function setPriority($priority) {
      $this->priority= $priority;
    }
      
    /**
     * Retrieves product
     *
     * @access  public
     * @return  string
     */
    function getProduct() {
      return $this->product;
    }
      
    /**
     * Sets product
     *
     * @access  public
     * @param   string product
     */
    function setProduct($product) {
      $this->product= $product;
    }
      
    /**
     * Retrieves rep_platform
     *
     * @access  public
     * @return  string
     */
    function getRep_platform() {
      return $this->rep_platform;
    }
      
    /**
     * Sets rep_platform
     *
     * @access  public
     * @param   string rep_platform
     */
    function setRep_platform($rep_platform) {
      $this->rep_platform= $rep_platform;
    }
      
    /**
     * Retrieves reporter
     *
     * @access  public
     * @return  int
     */
    function getReporter() {
      return $this->reporter;
    }
      
    /**
     * Sets reporter
     *
     * @access  public
     * @param   int reporter
     */
    function setReporter($reporter) {
      $this->reporter= $reporter;
    }
      
    /**
     * Retrieves version
     *
     * @access  public
     * @return  string
     */
    function getVersion() {
      return $this->version;
    }
      
    /**
     * Sets version
     *
     * @access  public
     * @param   string version
     */
    function setVersion($version) {
      $this->version= $version;
    }
      
    /**
     * Retrieves component
     *
     * @access  public
     * @return  string
     */
    function getComponent() {
      return $this->component;
    }
      
    /**
     * Sets component
     *
     * @access  public
     * @param   string component
     */
    function setComponent($component) {
      $this->component= $component;
    }
      
    /**
     * Retrieves resolution
     *
     * @access  public
     * @return  string
     */
    function getResolution() {
      return $this->resolution;
    }
      
    /**
     * Sets resolution
     *
     * @access  public
     * @param   string resolution one of the BUG_RESOLUTION_* constants
     * @see     xp://org.bugzilla.BugConstants
     */
    function setResolution($resolution) {
      $this->resolution= $resolution;
    }
      
    /**
     * Retrieves target_milestone
     *
     * @access  public
     * @return  string
     */
    function getTarget_milestone() {
      return $this->target_milestone;
    }
      
    /**
     * Sets target_milestone
     *
     * @access  public
     * @param   string target_milestone
     */
    function setTarget_milestone($target_milestone) {
      $this->target_milestone= $target_milestone;
    }
      
    /**
     * Retrieves qa_contact
     *
     * @access  public
     * @return  int
     */
    function getQa_contact() {
      return $this->qa_contact;
    }
      
    /**
     * Sets qa_contact
     *
     * @access  public
     * @param   int qa_contact
     */
    function setQa_contact($qa_contact) {
      $this->qa_contact= $qa_contact;
    }
      
    /**
     * Retrieves status_whiteboard
     *
     * @access  public
     * @return  string
     */
    function getStatus_whiteboard() {
      return $this->status_whiteboard;
    }
      
    /**
     * Sets status_whiteboard
     *
     * @access  public
     * @param   string status_whiteboard
     */
    function setStatus_whiteboard($status_whiteboard) {
      $this->status_whiteboard= $status_whiteboard;
    }
      
    /**
     * Retrieves votes
     *
     * @access  public
     * @return  int
     */
    function getVotes() {
      return $this->votes;
    }
      
    /**
     * Sets votes
     *
     * @access  public
     * @param   int votes
     */
    function setVotes($votes) {
      $this->votes= $votes;
    }
      
    /**
     * Retrieves keywords
     *
     * @access  public
     * @return  string
     */
    function getKeywords() {
      return $this->keywords;
    }
      
    /**
     * Sets keywords
     *
     * @access  public
     * @param   string keywords
     */
    function setKeywords($keywords) {
      $this->keywords= $keywords;
    }
      
    /**
     * Retrieves lastdiffed
     *
     * @access  public
     * @return  util.Date
     */
    function getLastdiffed() {
      return $this->lastdiffed;
    }
      
    /**
     * Sets lastdiffed
     *
     * @access  public
     * @param   util.Date lastdiffed
     */
    function setLastdiffed($lastdiffed) {
      $this->lastdiffed= $lastdiffed;
    }
      
    /**
     * Retrieves everconfirmed
     *
     * @access  public
     * @return  int
     */
    function getEverconfirmed() {
      return $this->everconfirmed;
    }
      
    /**
     * Sets everconfirmed
     *
     * @access  public
     * @param   int everconfirmed
     */
    function setEverconfirmed($everconfirmed) {
      $this->everconfirmed= $everconfirmed;
    }
      
    /**
     * Retrieves reporter_accessible
     *
     * @access  public
     * @return  int
     */
    function getReporter_accessible() {
      return $this->reporter_accessible;
    }
      
    /**
     * Sets reporter_accessible
     *
     * @access  public
     * @param   int reporter_accessible
     */
    function setReporter_accessible($reporter_accessible) {
      $this->reporter_accessible= $reporter_accessible;
    }
      
    /**
     * Retrieves cclist_accessible
     *
     * @access  public
     * @return  int
     */
    function getCclist_accessible() {
      return $this->cclist_accessible;
    }
      
    /**
     * Sets cclist_accessible
     *
     * @access  public
     * @param   int cclist_accessible
     */
    function setCclist_accessible($cclist_accessible) {
      $this->cclist_accessible= $cclist_accessible;
    }
      
    /**
     * Update this object in the database
     *
     * @access  public
     * @return  boolean success
     * @throws  rdbms.SQLException in case an error occurs
     * @throws  lang.IllegalAccessException in case there is no suitable database connection available
     */
    function update() {
      $cm= &ConnectionManager::getInstance();  

      try(); {
        $db= &$cm->getByHost('bugzilla', 0);
        $db->update('
          bugs..bugs set
            groupset = %d,
            assigned_to = %d,
            bug_file_loc = %s,
            bug_severity = %s,
            bug_status = %s,
            creation_ts = %s,
            delta_ts = %s,
            short_desc = %s,
            op_sys = %s,
            priority = %s,
            product = %s,
            rep_platform = %s,
            reporter = %d,
            version = %s,
            component = %s,
            resolution = %s,
            target_milestone = %s,
            qa_contact = %d,
            status_whiteboard = %s,
            votes = %d,
            keywords = %s,
            lastdiffed = %s,
            everconfirmed = %d,
            reporter_accessible = %d,
            cclist_accessible = %d
          where
            bug_id = %d
          ',
          $this->groupset,
          $this->assigned_to,
          $this->bug_file_loc,
          $this->bug_severity,
          $this->bug_status,
          $this->creation_ts,
          $this->delta_ts,
          $this->short_desc,
          $this->op_sys,
          $this->priority,
          $this->product,
          $this->rep_platform,
          $this->reporter,
          $this->version,
          $this->component,
          $this->resolution,
          $this->target_milestone,
          $this->qa_contact,
          $this->status_whiteboard,
          $this->votes,
          $this->keywords,
          $this->lastdiffed,
          $this->everconfirmed,
          $this->reporter_accessible,
          $this->cclist_accessible,
          $this->bug_id
        );
      } if (catch('SQLException', $e)) {
        return throw($e);
      }

      return TRUE;
    }
    
    /**
     * Write this object to the database
     *
     * @access  public
     * @return  boolean success
     * @throws  rdbms.SQLException in case an error occurs
     * @throws  lang.IllegalAccessException in case there is no suitable database connection available
     */
    function insert() {
      $cm= &ConnectionManager::getInstance(); 
      try(); {
        $db= &$cm->getByHost('bugzilla', 0);
        $db->insert('
          bugs..bugs (
            groupset,
            assigned_to,
            bug_file_loc,
            bug_severity,
            bug_status,
            creation_ts,
            delta_ts,
            short_desc,
            op_sys,
            priority,
            product,
            rep_platform,
            reporter,
            version,
            component,
            resolution,
            target_milestone,
            qa_contact,
            status_whiteboard,
            votes,
            keywords,
            lastdiffed,
            everconfirmed,
            reporter_accessible,
            cclist_accessible
          ) values (
            %d, %d, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %d, %s, %s, %s, %s, %d, %s, %d, %s, %s, %d, %d, %d
          )',
          $this->groupset,
          $this->assigned_to,
          $this->bug_file_loc,
          $this->bug_severity,
          $this->bug_status,
          $this->creation_ts,
          $this->delta_ts,
          $this->short_desc,
          $this->op_sys,
          $this->priority,
          $this->product,
          $this->rep_platform,
          $this->reporter,
          $this->version,
          $this->component,
          $this->resolution,
          $this->target_milestone,
          $this->qa_contact,
          $this->status_whiteboard,
          $this->votes,
          $this->keywords,
          $this->lastdiffed,
          $this->everconfirmed,
          $this->reporter_accessible,
          $this->cclist_accessible
        );

        // Fetch identity
        $this->bug_id= $db->identity();
      } if (catch('SQLException', $e)) {
        return throw($e);
      }

      return TRUE;
    }    
  }
?>
