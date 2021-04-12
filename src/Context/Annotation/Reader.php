<?php

namespace miiimooo\BehatTools\Context\Annotation;

use Behat\Behat\Context\Annotation\AnnotationReader;
use Drupal\DrupalExtension\Hook\Dispatcher;
use ReflectionMethod;

/**
 * Annotated contexts reader.
 *
 * @see \Behat\Behat\Context\Loader\AnnotatedLoader
 */
class Reader implements AnnotationReader
{

  /**
   * @var string
   */
    private static $regex = '/^\@(beforeparagraphcreate|afterparagraphcreate)(?:\s+(.+))?$/i';

  /**
   * @var string[]
   */
    private static $classes = array(
      'beforeparagraphcreate' => 'miiimooo\BehatTools\Hook\Call\BeforeParagraphCreate',
      'afterparagraphcreate' => 'miiimooo\BehatTools\Hook\Call\AfterParagraphCreate',
    );

  /**
   * {@inheritDoc}
   */
    public function readCallee($contextClass, ReflectionMethod $method, $docLine, $description)
    {

        if (!preg_match(self::$regex, $docLine, $match)) {
            return null;
        }

        $type = strtolower($match[1]);
        $class = self::$classes[$type];
        $pattern = isset($match[2]) ? $match[2] : null;
        $callable = array($contextClass, $method->getName());

        return new $class($pattern, $callable, $description);
    }
}
