<?php
/*
 * Compare the images using the md5 crypto hash, which isn't ideal for images
 * but can stand to show that files are exactly alike.
 * Author: Jud Stephenson
 */
 
namespace ImageMatcher\Comparators;

use ImageMatcher\Common\Image;
class md5 {
  
  public static function compare($images) {
    // we operate on arrays of image objects
    if(!is_array($images)) {
      return false;
    }
    
    $hashes = array();
    $matches = array();
    foreach($images as $image) {
      if($image instanceof \ImageMatcher\Common\Image) {
        $image->hashes['md5'] = !$image->hashes['md5'] ? md5($image->data['raw']) : $image->hashes['md5'];
        if(!empty($hashes[$image->hashes['md5']])) {
          if(empty($matches[$image->hashes['md5']])) {
            $matches[$image->hashes['md5']][] = $hashes[$image->hashes['md5']];
          }
          $matches[$image->hashes['md5']][] = $image;
        } else {
          $hashes[$image->hashes['md5']] = $image;
        }
      }
    }
        
    // This way we can return an array with keys like 0, 1, 2 - instead of
    // the md5 signature of the image as the key.
    $m = array();
    foreach($matches as $match) {
      $m[] = $match;
    }
    
    return $m;
  }
}



/* TODO:
 * - Matching Pair object, which would give access to some "matching_score" type system
 * - Impliment another comparator
 * - Dominate?
 */