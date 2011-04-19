<?php
/*
 * Static parsing class to get images from html, doesn't get css or js images
 * Author: Jud Stephenson
 */

namespace ImageMatcher\Parsers;

class html {

  public static function parse($url) {
    $ch = curl_init($url);
          curl_setopt($ch, CURLOPT_HEADER, 0);
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $page = curl_exec($ch);
          curl_close($ch);

    @$dom = new \DOMDocument();
    @$dom->loadHTML($page);
    $img = $dom->getElementsByTagName('img');
    $imgLength = $img->length;
    
    $images = array();
    for($i=0; $i<$imgLength;$i++) {
      $src = false;
      $src = self::normalizeURL($url, $img->item($i)->attributes->getNamedItem('src')->nodeValue);
      
      if($src)
        $images[] = $src;
    }
    
    return $images;
  }
  
  private static function normalizeURL($page, $url) {
    $urlparts = parse_url($page);
    if(strpos($url, '/') === 0) {
      return $urlparts['scheme'] . '://' . $urlparts['host'] . $url;
    } else {
      $abs = strtolower(substr($url, 0, 7)) == 'http://';
      if($abs != 'http://' && $abs != 'https:/') {
        $imgpath = explode('/', $urlparts['path']);
        $imgpath[(count($imgpath)-1)] = $url;
        return $urlparts['scheme'] . '://' . $urlparts['host'] . implode('/', $imgpath);
      } else {
        return $url;
      }
    }
  }
}
?>