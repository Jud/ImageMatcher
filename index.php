<?php
include_once('lib/SplClassLoader.php');
$mapper = new SplClassLoader('ImageMatcher');
$mapper->register();

$start = microtime(true);
use \ImageMatcher\Core\ImageMatcher as ImageMatcher;

$matcher = new ImageMatcher(array(
                              'http://boutique.kulte.fr/categories-Kulte-accessoires-5.html',
                              'http://boutique.kulte.fr/shopdisplayproducts.asp'
                            ));

$filters = array(
                'before_filters' => 
                    array(
                      'size' => array('min-height' => 75, 'omit-empty' => false)
                    )
                );
                
$matches = $matcher->compareImagesWith(array('md5'), $filters);
                                       
echo (microtime(true)-$start) . "<br/>";
if($matches) {
  foreach($matches->toArray() as $match) {
    foreach($match->members as $img) {
      echo '<img src="' . $img->location . '"/><br />';
    }
    echo '<br /><br />';
  }
}
?>