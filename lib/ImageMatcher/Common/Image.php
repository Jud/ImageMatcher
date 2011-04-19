<?php
/*
 * Class to encapsulate image information
 * Author: Jud Stephenson
 */

namespace ImageMatcher\Common;

class Image {
  public $hashes, $location, $data;
  
  public function __construct($location = '') {
    if($location) {
      return $this->loadImageAt($location);
    } else {
      return $this;
    }
  }
  
  /**
   * `loadImageAt` loads an image from a URI location. 
   * this method has lost favor (about an hour after I wrote it) and instead
   * I would encourage you to use an ImageFactory, which utilizes CURL's multi
   * methods to speed up image retrieval.
   */
  public function loadImageAt($location) {
    // We are creating a temporary file and using CURL to save the image
    // in the temporary file so that GD can store metadata such as
    // mime type, height, width, etc for further analysis.
    if(empty($this->data['raw'])) {
      $tmp = tmpfile();
      $ch = curl_init($location);
      curl_setopt($ch, CURLOPT_FILE, $tmp);
      curl_setopt($ch, CURLOPT_HEADER, 0);
      curl_exec($ch);
      curl_close($ch);
      fseek($tmp, 0);

      $this->location = $location;
      
      $this->setPropertiesFromFileHandle($tmp);
    }
    
    return $this;
  }
  
  /**
   * setPropertiesFromFileHandle, (verbose, I know) takes an fopen compatible
   * file handle and uses it to get information about an image. Some of the 
   * information set is 'height', 'width', and 'raw'.
   */ 
  public function setPropertiesFromFileHandle($handle) { 
    $stream = stream_get_meta_data($handle);
    
    if($raw = @fread($handle, filesize($stream['uri']))) {
      $this->data['raw'] = base64_encode($raw);
          
      if($info = getimagesize($stream['uri'])) {
        @$this->data['mime'] = $info['mime'];
        @$this->data['type'] = $info[2];
        @$this->data['width'] = $info[0];
        @$this->data['height'] = $info[1];
        @$this->data['bits'] = $info['bits'];
        @$this->data['channels'] = $info['channels'];
      }
    } else {
      return false;
    }
    
    return $this;
  }
}
?>