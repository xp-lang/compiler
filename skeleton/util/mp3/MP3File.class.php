<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('io.File', 'util.mp3.ID3Tag');
  
  /**
   * MP3 file
   *
   *
   */
  class MP3File extends Object {
  
    /**
     * Constructor
     *
     * @access  public
     * @param   &io.File file
     */
    function __construct(&$file) {
      $this->file= &$file;
      parent::__construct();
    }
    
    /**
     * Extract ID3 Tags
     *
     * @access  public
     * @param   int version default ID3_VERSION_UNKNOWN ID3 Version
     * @see     http://www.id3.org/
     */
    function getID3Tag($version= ID3_VERSION_UNKNOWN) {
      try(); {
        $this->file->open(FILE_MODE_READ);
        
        do {
          switch ($version) {
            case ID3_VERSION_UNKNOWN:
            
              // Check version 2
              $this->file->rewind();
              $buf= $this->file->read(10);
              if ('ID3' == substr($buf, 0, 3)) {
                $version= ID3_VERSION_2;
                break;
              }
              
              $this->file->seek(-128, SEEK_END);
              $buf= $this->file->read(128);
              $version= ID3_VERSION_1;
              break;

            case ID3_VERSION_1:
            case ID3_VERSION_1_1:
              $buf= $this->file->read(128);
              $version= ("\0" == $buf{125} && "\0" != $buf{126}) ? ID3_VERSION_1_1 : ID3_VERSION_1;
              break;

            case ID3_VERSION_2:
              $buf= $this->file->read(10);
              break;

            default:
              $version= FALSE;
              throw(new IllegalArgumentException('Version '.$version.' not supported'));
          }
        } while (ID3_VERSION_UNKNOWN === $version);
      } if (catch('Exception', $e)) {
        $this->file->close();
        return throw($e);
      }
      $this->file->close();
      
      return ID3Tag::fromString($buf, $version);
    }
  }
?>
