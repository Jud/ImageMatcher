<?php
/*
 * PHP Image Image Matching Class
 * Author: Jud Stephenson - http://JudStephenson.com
 */

namespace ImageMatcher\Core;

use ImageMatcher\Parsers;
use ImageMatcher\Comparators;
use ImageMatcher\Common;

class ImageMatcher {
  
  public $urls;
  
  public function __construct($url = '', $url2 = '') {
    if((is_array($url) && count($url) > 1) || (!empty($url) && !empty($url2))) {
      $this->urls = (is_array($url)) ? $url : array($url, $url2);
      foreach($this->urls as $url) {
        if(!$this->isURLValid($url)) {
          throw new Exception('All URLs must be Valid.');
          return false;
        }
      }
      return $this;
    } else {
      throw new Exception('Must have at least two URLs to compare.');
      return false;
    }
  }
  
  public function compareImagesWith($comparators = array('md5'), $parser = 'html') {
    if(count($this->urls) < 2) {
      return false;
    }
    
    $images = array();
    foreach($this->urls as $url) {
      $name = '\ImageMatcher\Parsers\\' . $parser;
      foreach($name::parse($url) as $image) {
        $images[] = new Common\Image($image);
      }
    }
            
    $matches = array();
    foreach($comparators as $comparator) {
      $name = '\ImageMatcher\Comparators\\' . $comparator;
      foreach($name::compare($images) as $match) {
        $matches[] = $match;
      }
    }
    
    var_dump($matches);

  }
  
  public function sayHello() { echo 'hello'; }
  private function isURLValid ($url) {
    // returns bool value if it is a valid url
    return filter_var($url, FILTER_VALIDATE_URL);
  }
}

?>