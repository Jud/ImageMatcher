<?php
/**
 * Filter an image based on some discernable characteristics
 * This filter tests out *size*
 */

namespace ImageMatcher\Filters;
class size {
  
  /**
   * Each filter impliments a static function called `Filter`, which is run by
   * the main object on a collecton of objects. If the object should be filtered
   * then the function returns false.
   */
  public static function filter($img, $params = array()) {
    foreach($params as $key => $value) {
      switch(substr($key, 0, 3)) {
        // params should be in the form min-height, max-height, min-width, etc.
        case 'min':
          // Should return false if: 1) Needed param is not found in Image object AND 'omit-empty' is true
          // 2) if the image parameter is LESS than the minimum value of the parameter
          if((empty($img->data[substr($key, 4)]) && $params['omit-empty'] == true) || (@$img->data[substr($key, 4)] < $value)) {
            return false;
          }
        break;
        
        case 'max':
          if((empty($img->data[substr($key, 4)]) && $params['omit-empty'] == true) || (@$img->data[substr($key, 4)] > $value)) {
            return false;
          }
        break;
      }
    }
    
    return true;
    
  } 
}
?>