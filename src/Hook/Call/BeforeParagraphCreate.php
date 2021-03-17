<?php

namespace miiimooo\BehatTools\Hook\Call;

use Drupal\DrupalExtension\Hook\Call\EntityHook;
use miiimooo\BehatTools\Hook\Scope\ParagraphScope;

/**
 * BeforeParagraphCreate hook class.
 */
class BeforeParagraphCreate extends EntityHook
{

  /**
   * Initializes hook.
   */
    public function __construct($filterString, $callable, $description = null)
    {
        parent::__construct(ParagraphScope::BEFORE, $filterString, $callable, $description);
    }

  /**
   * {@inheritdoc}
   */
    public function getName()
    {
        return 'BeforeParagraphCreate';
    }
}
