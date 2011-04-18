<?php
include_once('lib/SplClassLoader.php');
$mapper = new SplClassLoader('ImageMatcher');
$mapper->register();

use \ImageMatcher\Core\ImageMatcher as ImageMatcher;

$matcher = new ImageMatcher(array(
                              'http://shop.obeyclothing.com/c-76-new-arrivals.aspx',
                              'http://shop.obeyclothing.com/c-183-shoes.aspx'
                            ));
$matcher->compareImagesWith(array('md5'));


print_r($matcher);
?>