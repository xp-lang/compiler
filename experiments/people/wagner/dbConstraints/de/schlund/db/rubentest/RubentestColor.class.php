<?php
/* This class is part of the XP framework
 *
 * $Id: xp5.php.xsl 52481 2007-01-16 11:26:17Z rdoebele $
 */
 
  uses('rdbms.DataSet');

  /**
   * Class wrapper for table color, database Ruben_Test_PS
   * (Auto-generated on Thu, 22 Mar 2007 18:02:07 +0100 by ruben)
   *
   * @purpose  Datasource accessor
   */
  class RubentestColor extends DataSet {
    public
      $color_id           = 0,
      $name               = '',
      $colortype          = '';

    static function __static() { 
      with ($peer= self::getPeer()); {
        $peer->setTable('Ruben_Test_PS.color');
        $peer->setConnection('localhost');
        $peer->setIdentity('color_id');
        $peer->setPrimary(array('color_id'));
        $peer->setTypes(array(
          'color_id'            => array('%d', FieldType::INT, FALSE),
          'name'                => array('%s', FieldType::VARCHAR, FALSE),
          'colortype'           => array('%s', FieldType::VARCHAR, FALSE)
        ));
      }
    }  
  
    /**
     * Retrieve associated peer
     *
     * @return  rdbms.Peer
     */
    public static function getPeer() {
      return Peer::forName(__CLASS__);
    }
  
    /**
     * Gets an instance of this object by index "PRIMARY"
     * 
     * @param   int color_id
     * @return  de.schlund.db.rubentest.RubentestColor entitiy object
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByColor_id($color_id) {
      $r= self::getPeer()->doSelect(new Criteria(array('color_id', $color_id, EQUAL)));
      return $r ? $r[0] : NULL;
    }

    /**
     * Gets an instance of this object by index "colortype"
     * 
     * @param   string colortype
     * @return  de.schlund.db.rubentest.RubentestColor[] entities object
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByColortype($colortype) {
      return self::getPeer()->doSelect(new Criteria(array('colortype', $colortype, EQUAL)));
    }

    /**
     * Retrieves color_id
     *
     * @return  int
     */
    public function getColor_id() {
      return $this->color_id;
    }
      
    /**
     * Sets color_id
     *
     * @param   int color_id
     * @return  int the previous value
     */
    public function setColor_id($color_id) {
      return $this->_change('color_id', $color_id);
    }

    /**
     * Retrieves name
     *
     * @return  string
     */
    public function getName() {
      return $this->name;
    }
      
    /**
     * Sets name
     *
     * @param   string name
     * @return  string the previous value
     */
    public function setName($name) {
      return $this->_change('name', $name);
    }

    /**
     * Retrieves colortype
     *
     * @return  string
     */
    public function getColortype() {
      return $this->colortype;
    }
      
    /**
     * Sets colortype
     *
     * @param   string colortype
     * @return  string the previous value
     */
    public function setColortype($colortype) {
      return $this->_change('colortype', $colortype);
    }

    /**
     * Retrieves an array of all Texture entities referencing
     * this entity by colortype=>colortype
     *
     * @return  de.schlund.db.rubentest.RubentestTexture[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getTextureColortypeList() {
      return XPClass::forName('de.schlund.db.rubentest.RubentestTexture')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('colortype', $this->getColortype(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Texture entities referencing
     * this entity by colortype=>colortype
     *
     * @return  rdbms.ResultIterator<de.schlund.db.rubentest.RubentestTexture>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getTextureColortypeIterator() {
      return XPClass::forName('de.schlund.db.rubentest.RubentestTexture')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('colortype', $this->getColortype(), EQUAL)
      ));
    }
  }
?>