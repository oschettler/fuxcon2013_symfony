<?php

namespace FUxCon2013\ProjectsBundle\Twig\Extension;

use Symfony\Bundle\TwigBundle\Extension\AssetsExtension;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\File\File;

class FUxCon2013Extension extends \Twig_Extension
{
  private $container;

  public function __construct(ContainerInterface $container)
  {
    $this->container = $container;
  }

  public function getFunctions()
  {
    return array(
      new \Twig_SimpleFunction('thumbnail', array($this, 'thumbnail')),
    );
  }

  public function getFilters()
  {
    return array(
      new \Twig_SimpleFilter('truncate', array($this, 'truncate')),
    );
  }

  private function createThumbnail($src_name, $dst_name, $new_w, $new_h)
  {
    $info = explode('.', $src_name);
    if (preg_match('/jpg|jpeg/', $info[1])) {
      $src_img = imagecreatefromjpeg($src_name);
    }
    if (preg_match('/png/', $info[1])) {
      $src_img = imagecreatefrompng($src_name);
    }

    $old_x = imageSX($src_img);
    $old_y = imageSY($src_img);
    if ($old_x > $old_y) {
      $thumb_w = $new_w;
      $thumb_h = $old_y * ($new_h / $old_x);
    }
    if ($old_x < $old_y) {
      $thumb_w = $old_x * ($new_w / $old_y);
      $thumb_h = $new_h;
    }
    if ($old_x == $old_y) {
      $thumb_w = $new_w;
      $thumb_h = $new_h;
    }
    $dst_img = imagecreatetruecolor($thumb_w, $thumb_h);
    imagecopyresampled(
      $dst_img,
      $src_img,
      0,0,0,0,
      $thumb_w, $thumb_h,
      $old_x, $old_y
    );
    if (preg_match("/png/", $info[1])) {
      imagepng($dst_img, $dst_name);
    } else {
      imagejpeg($dst_img, $dst_name);
    }
    imagedestroy($dst_img);
    imagedestroy($src_img);
  }


  public function thumbnail($path, $size = null)
  {
    $ext = new AssetsExtension($this->container);

    $url = $ext->getAssetUrl($path);

    if (strpos($url, 'http') === 0) {
      return $url;
    }

    $fname = $_SERVER['DOCUMENT_ROOT'] . $url;
    if (file_exists($fname)) {

      if ($size) {
        $thumb_name = preg_replace('#([^/]*)(\.\w+)$#', "\$1/\$1_{$size}\$2", $fname);
        $thumb_url = substr($thumb_name, strlen($_SERVER['DOCUMENT_ROOT']));

        if (file_exists($thumb_name)
          && filemtime($fname) < filemtime($thumb_name)) {

          return $thumb_url;
        }

        @mkdir(dirname($thumb_name), 0755, /*recursive*/TRUE);

        list($width, $height) = explode('x', $size);
        $this->createThumbnail($fname, $thumb_name, $width, $height);

        return $thumb_url;
      }

      return $url;
    }

    return '/images/ni.png';
  }

  public function truncate($text, $length = 150)
  {
    if (strlen($text) < $length) {
      return $text;
    }
    
    $text = substr($text, 0, $length);
    $blank = strrpos($text, ' ');
    if (FALSE === $blank) {
      $text = '';
    }
    else {
      $text = substr($text, 0, $blank);
    }
    return $text . ' ...';
  }


  public function getName()
  {
    return 'fuxcon2013_extension';
  }
}
