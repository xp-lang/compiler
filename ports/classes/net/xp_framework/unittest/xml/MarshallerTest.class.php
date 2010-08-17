<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */
 
  uses(
    'unittest.TestCase',
    'net.xp_framework.unittest.xml.DialogType',
    'xml.meta.Marshaller'
  );

  /**
   * Test Marshaller API
   *
   * @see      xp://xml.meta.Marshaller
   * @purpose  Unit Test
   */
  class MarshallerTest extends TestCase {
    protected $fixture= NULL;
  
    /**
     * Creates fixture
     *
     */
    public function setUp() {
      $this->fixture= new Marshaller();
    }

    /**
     * Compares XML after stripping all whitespace between tags of both 
     * expected and actual strings.
     *
     * @see     xp://unittest.TestCase#assertEquals
     * @param   string expect
     * @param   xml.Node node
     * @return  bool
     */
    public function assertMarshalled($expect, $node) {
      return $this->assertEquals(
        preg_replace('#>[\s\r\n]+<#', '><', trim($expect)),
        preg_replace('#>[\s\r\n]+<#', '><', trim($node->getSource(INDENT_DEFAULT)))
      );
    }

    /**
     * Tests the dialog's id member gets serialized as an id attribute
     *
     */
    #[@test]
    public function idAttribute() {
      $dialog= new DialogType();
      $dialog->setId('file.open');
      
      $this->assertMarshalled('
        <dialogtype id="file.open">
          <caption/>
          <flags/>
          <options/>
        </dialogtype>', 
        $this->fixture->marshalTo(new Node('dialogtype'), $dialog)
      );
    }
    
    /**
     * Tests the dialog's caption member gets serialized as a node
     *
     */
    #[@test]
    public function captionNode() {
      $dialog= new DialogType();
      $dialog->setCaption('Open a file > Choose');
      
      $this->assertMarshalled('
        <dialogtype id="">
          <caption>Open a file &gt; Choose</caption>
          <flags/>
          <options/>
        </dialogtype>', 
        $this->fixture->marshalTo(new Node('dialogtype'), $dialog)
      );
    }

    /**
     * Tests the dialog's buttons member gets serialized as a nodeset
     *
     */
    #[@test]
    public function buttonsNodeSet() {
      $dialog= new DialogType();
      $dialog->setCaption('Really delete the file "�"?');

      with ($ok= $dialog->addButton(new ButtonType())); {
        $ok->setId('ok');
        $ok->setCaption('Yes, go ahead');
      }
      with ($cancel= $dialog->addButton(new ButtonType())); {
        $cancel->setId('cancel');
        $cancel->setCaption('No, please don\'t!');
      }

      $this->assertMarshalled('
        <dialogtype id="">
          <caption>Really delete the file &quot;�&quot;?</caption>
          <button id="ok">Yes, go ahead</button>
          <button id="cancel">No, please don\'t!</button>
          <flags/>
          <options/>
        </dialogtype>', 
        $this->fixture->marshalTo(new Node('dialogtype'), $dialog)
      );
    }
    
    /**
     * Tests for a new dialog without any members set
     *
     */
    #[@test]
    public function emptyMembers() {
      $dialog= new DialogType();
      $this->assertMarshalled('
        <dialogtype id="">
          <caption/>
          <flags/>
          <options/>
        </dialogtype>', 
        $this->fixture->marshalTo(new Node('dialogtype'), $dialog)
      );
    }

    /**
     * Tests the dialog's id member gets serialized as an id attribute
     *
     */
    #[@test]
    public function asTree() {
      $dialog= new DialogType();
      $dialog->setId('file.open');

      $node= $this->fixture->marshalTo(new Node('dialog'), $dialog);
      $this->assertInstanceOf('xml.Node', $node);
      $this->assertEquals('dialog', $node->getName());
      $this->assertEquals('file.open', $node->getAttribute('id'));
    }

    /**
     * Tests the deprecated usage
     *
     */
    #[@test]
    public function deprecatedUsage() {
      $dialog= new DialogType();
      $this->assertEquals(
        Marshaller::marshal($dialog),
        $this->fixture->marshalTo(new Node('dialogtype'), $dialog)->getSource(INDENT_DEFAULT)
      );
    }
  }
?>
