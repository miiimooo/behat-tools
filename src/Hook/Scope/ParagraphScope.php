<?php
/**
 * @file
 * Paragraph scope.
 */
namespace miiimooo\BehatTools\Hook\Scope;

use Drupal\DrupalExtension\Hook\Scope\BaseEntityScope;
use Behat\Behat\Context\Context;
use Behat\Testwork\Hook\Scope\HookScope;

/**
 * Represents an Entity hook scope.
 */
abstract class ParagraphScope extends BaseEntityScope
{

    const BEFORE = 'paragraph.create.before';
    const AFTER = 'paragraph.create.after';
}
