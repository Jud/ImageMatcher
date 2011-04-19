<?php
/**
 * Hold all of the image URLs
 */
 
namespace ImageMatcher\Common;

class ImageCollection {

  public $images = array();
  
  public function add($img, $page) {
    $this->images[md5($page)][] = $img;
  }
  
  public function remove($img, $key) {
    // make sure the image has an md5 hash
    $img->genmd5();
    
    foreach($this->images as $k => $imggroup) {
      foreach($imggroup as $j => $im) {
        $im->genmd5();
        if(($key == $k) && ($img->hashes['md5'] == $im->hashes['md5'])) {
          unset($this->images[$k][$j]);
        }
      }
    }
  }
  
  public function toArray() {
    $imgs = array();
    foreach($this->images as $k => $imggroup) {
      foreach($imggroup as $i) {
        $imgs[] = array($i, $k);
      }
    }
    return $imgs;
  }
}
?>