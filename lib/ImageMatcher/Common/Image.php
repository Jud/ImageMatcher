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
  
  public function loadImageAt($location) {
    // We are creating a temporary file and using CURL to save the image
    // in the temporary file so that GD can store metadata such as
    // mime type, height, width, etc for further analysis.
    $tmp = tmpfile();
    $ch = curl_init($location);
    curl_setopt($ch, CURLOPT_FILE, $tmp);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_exec($ch);
    curl_close($ch);
    fseek($tmp, 0);
    
    $this->location = $location;
    
    $stream = stream_get_meta_data($tmp);
    $this->data['raw'] = fread($tmp, filesize($stream['uri']));
    
    if($info = getimagesize($stream['uri'])) {
      $this->data['mime'] = $info['mime'];
      $this->data['type'] = $info[2];
      $this->data['width'] = $info[0];
      $this->data['height'] = $info[1];
      $this->data['bits'] = $info['bits'];
      $this->data['channels'] = $info['channels'];
    }
  
    return $this;
  }
}
?>