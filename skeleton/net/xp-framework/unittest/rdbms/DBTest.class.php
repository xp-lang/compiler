<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */
 
  uses(
    'rdbms.DriverManager', 
    'util.profiling.unittest.TestCase'
  );

  /**
   * Test rdbms API
   *
   * @purpose  Unit Test
   */
  class DBTest extends TestCase {
    var
      $conn = NULL,
      $dsn  = '';
      
    /**
     * Constructor
     *
     * @access  publuc
     * @param   string name
     * @param   string dsn
     */
    function __construct($name, $dsn) {
      $this->dsn= $dsn;
      parent::__construct($name);
    }
      
    /**
     * Setup function
     *
     * @access  public
     * @throws  rdbms.DriverNotSupportedException
     */
    function setUp() {
      try(); {
        $this->conn= &DriverManager::getConnection($this->dsn);
      } if (catch('DriverNotSupportedException', $e)) {
        return throw($e);
      }
    }
    
    /**
     * Tear down function
     *
     * @access  public
     */
    function tearDown() {
      $this->conn->close();
    }
    
    /**
     * Test database connect
     *
     * @access  public
     */
    function testConnect() {
      $result= $this->conn->connect();
      $this->assertTrue($result);
    }
    
    /**
     * Test database select
     *
     * @access  public
     */
    function testSelect() {
      $r= &$this->conn->query('select %s as version', '$Version$');
      $this->assertSubclass($r, 'rdbms.ResultSet');
      $r && $this->assertNotEmpty($r->next('version'));
    }
  }
?>
