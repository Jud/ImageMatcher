<?php
/*
 * PHP Image Image Matching Class
 * Author: Jud Stephenson - http://JudStephenson.com
 */

namespace ImageMatcher\Core;
use ImageMatcher\Common\ImageFactory;
use ImageMatcher\Common\MatchCollection;
class ImageMatcher {
  
  public $urls;
  
  /**
   * Create a new ImageMatcher object. You may pass in either two arguments
   * or an array of URLs. The latter is the preferred method.
   */
  public function __construct($url = '', $url2 = '') {
    if((is_array($url) && count($url) > 1) || (!empty($url) && !empty($url2))) {
      return $this->setURLs($url, $url2);
    }
  }
  
  /**
   * Set the urls to query. If $url is an array, then we don't
   * even worry about $url2. Consider passing an array of URLs to be the
   * preferred way to interact.
   */
  public function setURLs($url='', $url2='') {
    if((is_array($url) && count($url) > 1) || (!empty($url) && !empty($url2))) {
      $urls = (is_array($url)) ? $url : array($url, $url2);
      foreach($urls as $url) {
        if($this->isURLValid($url)) {
          $this->urls[] = $url;
        }
      }
      return $this;
    } else {
      return false;
    }
  }
  
  /**
   * `CompareImagesWith` compares the images at the urls stored in the `$urls`
   * property. There are three different "components" that are available to 
   * interchange, namely, `comparators`, `filters`, and `parsers`.
   */
  public function compareImagesWith($comparators = array('md5'), $filters = array(), $parser = 'html') {
    if(count($this->urls) < 2) {
      return false;
    }
    
    /**
     * Parsers are what take an html page and return a set of image URLs.
     * For now, we have implimented a simple HTML parser based of of 
     * PHPs built in DOMdocument. But it wouldn't be a far stretch to 
     * drop in another module that takes into account JS images, using
     * something like PHPJS or P2P5.
     */
    $images = array();
    foreach($this->urls as $url) {
      $name = '\ImageMatcher\Parsers\\' . $parser;
      $imgs = ImageFactory::imagesFromLocationArray($name::parse($url));
      foreach($imgs as $img) {
        $images[] = $img;
      }
    }
    
    $images = $this->runFilters('before_filters', $filters, $images);
    
    /**
     * Comparators compare images and create MatchCollection's of MatchPairs, 
     * which hold the two image objects, as well as a score for their similarity.
     */
    $matches = new MatchCollection;
    foreach($comparators as $comparator) {
      $name = '\ImageMatcher\Comparators\\' . $comparator;
      $matches->appendCollection($name::compare($images));
    }
    
    $matches = $this->runFilters('after_filters', $filters, $matches);

    return $matches;

  }
  
  /**
   * `runFilters` takes a filter type, an array of filters and a collection
   * and applies the selected filters to the collection (an array or object).
   * For our purposes, the only filter built in is a simple size filter,
   * which weeds out images that are too small to be products so we don't 
   * waste time comparing them to other images.
   */
  private function runFilters($type, $filters, $collection) {
    // if the filter type (before_filters, after_filters) we are after isn't
    // empty and it has an array of filters to perform.
    if(!empty($filters[$type]) && is_array($filters[$type])) {
      foreach($filters[$type] as $filter => $options) {
        foreach($collection as $k => $value) {
          
          // dynamically setup our filter
          $f = '\ImageMatcher\Filters\\' . $filter;
          
          // all of the filters impliment a static `filter` method that returns
          // false if the item should be deleted.
          if(!$f::filter($value, $options)) {
            if(is_array($collection)) { 
              unset($collection[$k]);
            } else if(is_object($collection)) {
              $collection->remove($value);
            }
          }
        }
      }
    }
    
    // return the modified collection
    return $collection;
  }
  
  /**
   * Check to see if the URL is valid. PHP5 makes this easy with the filter_var
   * function and the FILTER_VALIDATE_URL type.
   */
  private function isURLValid ($url) {
    return filter_var($url, FILTER_VALIDATE_URL);
  }
}

?>