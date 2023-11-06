<?php

namespace iutnc\touiteur\loader;

class Psr4ClassLoader
{

  //attributs
  private $prefix;
  private $baseDir;

  //constructeur
  public function __construct($prefix, $baseDir)
  {
    $this->prefix = $prefix;
    $this->baseDir = $baseDir;
  }

  //méthode loadClass
  public function loadClass($className)
  {
    if (strpos($className, $this->prefix) === 0) {
      $relativeClass = substr($className, strlen($this->prefix));
      $filePath = $this->baseDir . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, $relativeClass) . '.php';
      if (is_file($filePath)) {
        require_once $filePath;
      }
    }
  }

  //méthode register
  public function register()
  {
    spl_autoload_register([$this, 'loadClass']);
  }
}
