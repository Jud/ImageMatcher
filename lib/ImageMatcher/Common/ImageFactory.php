<?php
/**
 * Class to utilize CURL Multi to spit out initialized Image objects.
 * Author: Jud Stephenson
 */
 
namespace ImageMatcher\Common;
use ImageMatcher\Common\Image;

class ImageFactory {
  
  /**
   * `ImagesFromLocationArray` takes an array of image locations, and curl_inits
   * each, then binds that to a curl_multi instance, which is then executed and
   * once all of the multi instances have completed, it sets up an array of
   * Image objects and outputs them.
   */
  public static function ImagesFromLocationArray($locations) {
    $images = array();
    $mh = curl_multi_init();
    foreach($locations as $location) {
      $ch = curl_init();
      
      // set URL and other appropriate options
      curl_setopt($ch, CURLOPT_URL, $location);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_HEADER, 0);
      
      //add the two handles
      curl_multi_add_handle($mh,$ch);
      $images[] = array('handle' => $ch, 'location' => $location);
    }
    
    $active = null;
    do {
      $mrc = curl_multi_exec($mh, $active);
    } while ($mrc == CURLM_CALL_MULTI_PERFORM);
    
    while ($active && $mrc == CURLM_OK) {
      if (curl_multi_select($mh) != -1) {
        do {
            $mrc = curl_multi_exec($mh, $active);
        } while ($mrc == CURLM_CALL_MULTI_PERFORM);
      }
    }
    
    // Now get the content from the CURL instances
    $output = array();
    foreach($images as $image) {
      // I really wanted to use CURLOPT_FILE to make managing the image
      // download cleaner, but max file descriptors was 256 on my iMac
      // so I had to kept them down by manually managing file operations.
      if($tmp = tmpfile()) {
        $content = curl_multi_getcontent($image['handle']);
        fwrite($tmp, $content);
        fseek($tmp, 0);
        
        // now create our image
        $img = new \ImageMatcher\Common\Image;
        $img->location = $image['location'];
        $img->setPropertiesFromFileHandle($tmp);
        
        $output[] = $img;
        
        fclose($tmp);    
        curl_close($image['handle']);
        curl_multi_remove_handle($mh, $image['handle']);
      }
    }
    
    curl_multi_close($mh);    
    return $output;
  }
}
?>