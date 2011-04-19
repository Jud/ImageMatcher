<?php
/**
 * Similarity Group: Abstract away a matching pair, for useful things like
 * similarity score per comparator, etc.
 * Author: Jud Stephenson
 */

namespace ImageMatcher\Common;
use ImageMatcher\Common\Image as Image;
class MatchPair {
  
  public $id, $members, $matches;
  
  /**
   * Create a new matchpair object. It is important that the array of $ids
   * is sorted before the MatchPair id is generated, since md5 DOES take
   * into account the order of a string, we need to make sure two md5 strings
   * will always be placed in the same order, and then the md5 taken of that
   * to get the id for the match.
   */
  public function __construct($imgs = '', $type, $score) {
    if(is_array($imgs)) {
      foreach($imgs as $img) {
        if($img instanceof Image) {
          $this->members[] = $img;
          $ids[] = (!empty($img->hashes['md5'])) ? $img->hashes['md5'] : md5($img->data['raw']);
        }
      }
      
      asort($ids);
      $output = '';
      foreach($ids as $id) {
        $output .= $id;
      }
      
      $this->id = md5($output);
      $this->matches[$type] = $score;
    }
  }
  
  /**
   * Probably a bad name, but this function, when given another MatchPair, will
   * copy the Comparator tests from one to the other. This is useful when an
   * image that already exists in on MatchCollection is trying to be added again.
   * Instead of just rejecting it, we will add the comparator results if they are
   * not included in the MatchPair already.
   */
  public function addMatchTest($pair) {
    foreach($pair->matches as $k => $v) {
      if(empty($this->matches[$k])) {
        $this->matches[$k] = $v;
      }
    }
  }
}
?>