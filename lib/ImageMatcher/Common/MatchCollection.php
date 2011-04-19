<?php
/**
 * A class that holds a collection of MatchPairs
 * Author: Jud Stephenson
 */

namespace ImageMatcher\Common;
use ImageMatcher\Common\MatchPair as MatchPair;


class MatchCollection {
  public $collection = array();
  
  /**
   * Add a MatchPair to the collection. This does the "heavy" lifting of
   * determining if the pair already exists in its reverse form, which
   * makes sure that we don't duplicate the image matches.
   */
  public function addPair($newPair) {
    if($newPair instanceof MatchPair) {
      if(!$member = $this->pairExists($newPair)) {
        $this->collection[] = $newPair;
      } else {
        $member->addMatchTest($newPair);
      }
    } else {
      return false;
    }
  }
  
  /**
   * appendCollection takes another match collection and adds it to the 
   * end of the current collection. This is useful for each of the Comparators
   * which have their own internal MathCollection and return a MatchCollection
   * which is then appended to the global collection.
   */
  public function appendCollection($collection) {
    if($collection instanceof MatchCollection) {
      foreach($collection->collection as $pair) {
        $this->addPair($pair);
      }
      return true;
    } else {
      return false;
    }
  }
  
  /**
   * Test to see if a pair already exists in this collection.
   */
  public function pairExists($pair) {
    foreach($this->collection as $member) {
      if($member->id == $pair->id) {
        return $member;
      }
    }
    return false;
  }
  
  /**
   * Remove a pair from this collection. Useful for when filters determine that
   * a pair in this collection needs to be removed.
   */
  public function remove($item) {
    foreach($this->collection as $k => $el) {
      if($el->id == $item->id) {
        unset($this->collection[$k]);
      }
    }
  }
  
  /**
   * A toArray function to that we can easily loop through all of the matching
   * objects in this collection.
   */
  public function toArray() {
    return is_array($this->collection) ? $this->collection : array();
  }
}
?>