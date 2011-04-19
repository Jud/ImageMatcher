<?php
/*
 * Compare the images using the md5 crypto hash, which isn't ideal for images
 * but can stand to show that files are exactly alike.
 * Author: Jud Stephenson
 */
 
namespace ImageMatcher\Comparators;

use ImageMatcher\Common\Image as Image;
use ImageMatcher\Common\MatchCollection as MatchCollection;
use ImageMatcher\Common\MatchPair as MatchPair;

class md5 {
  
  /**
   * The static `compare` method is implimented by all of the Comparators.
   * This method takes an array of images and performs the needed calculations
   * on them and outputs a MatchCollection object, which will then be appended
   * to the global collection.
   */
  public static function compare($images) {
  
    $hashes = array();
    $matches = new MatchCollection;
        
    foreach($images->images as $k => $group) {
      foreach($group as $image) {
        if($image instanceof Image) {
          $image->hashes['md5'] = !$image->hashes['md5'] ? md5($image->data['raw']) : $image->hashes['md5'];
          if($img = self::in_array($image, $images->images, $k))
          {            
            $matches->addPair(new MatchPair(array(
                                                  $img,
                                                  $image
                                            ), 'md5', 1));
          }
        }
      }
    }
    
    return $matches;
    
  }
  
  private static function in_array($image, $array, $key) {
    foreach($array as $k => $v) {
      if($k != $key) {
        foreach($v as $img) {
          if(@$img->hashes['md5'] == $image->hashes['md5']) {
            return $img;
          }
        }
      }
    }
    
    return false;
  }
}
 
?>