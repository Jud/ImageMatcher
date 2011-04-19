<?php
include_once('../lib/SplClassLoader.php');
$mapper = new SplClassLoader('ImageMatcher');
$mapper->register();

use \ImageMatcher\Core\ImageMatcher as ImageMatcher;

// get the URLs to check for matches
$urls = array($_POST['url1'], $_POST['url2']);

// this filter keeps the really tiny images from being matched
// I was going to make it an editable option on the page, but 
// it kind of cluttered up the demo page.
$filters = array(
                'before_filters' => 
                    array(
                      'size' => array('min-height' => 50, 'omit-empty' => false)
                    )
                );
try {
  $error = false;
  $matcher = new ImageMatcher($urls);
} catch(Exception $e) {
  $error = true;
}

$matches = $matcher->compareImagesWith(array('md5'), $filters);

// we could do this w/ json and js on the client side, but here works also.
if(!$error) {
  if(count($matches->toArray())) {
    foreach($matches->toArray() as $match) {
      echo '<div class="imagematch">';
      foreach($match->members as $img) {
        echo '<img src="' . $img->location . '"/>';
      }
      echo '</div>';
    }
  } else {
    echo '<div class="noMatch"> We didn\'t find any matches. </div>';
  }
} else {
  echo '<div class="error"> There was an error. </div>';
}
?>