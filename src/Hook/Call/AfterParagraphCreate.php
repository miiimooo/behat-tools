<?php

namespace miiimooo\BehatTools\Hook\Call;

use Drupal\DrupalExtension\Hook\Call\EntityHook;
use miiimooo\BehatTools\Hook\Scope\ParagraphScope;

/**
 * AfterParagraphCreate hook class.
 */
class AfterParagraphCreate extends EntityHook
{

  /**
   * Initializes hook.
   */
    public function __construct($filterString, $callable, $description = null)
    {
        parent::__construct(ParagraphScope::AFTER, $filterString, $callable, $description);
    }

  /**
   * {@inheritdoc}
   */
    public function getName()
    {
        return 'AfterParagraphCreate';
    }
}
