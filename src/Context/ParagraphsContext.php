<?php

namespace miiimooo\BehatTools\Context;

/**
 * Behat Context for adding paragraphs with API calls
 */
use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Gherkin\Node\TableNode;
use Drupal\DrupalExtension\Hook\Scope\BeforeNodeCreateScope;

class ParagraphsContext implements Context {
  /**
   * @var \Drupal\DrupalExtension\Context\DrupalContext
   */
  protected $drupalContext;

  protected $entities = [];

  const ENTITY_TYPE_ID = 'paragraph';

  protected $paragraphNames = [];

  /**
   * @BeforeScenario @javascript
   * Wrap XMLHttpRequest
   */
  public function prepare(BeforeScenarioScope $scope) {
    $this->drupalContext = $scope->getEnvironment()->getContext('Drupal\DrupalExtension\Context\DrupalContext');
  }

  /**
   * Create a paragraph.
   *
   * @param $entity
   *
   * @return saved
   *   The created paragraph.
   * @throws \Exception
   */
  public function create($entity) {
    $this->drupalContext->parseEntityFields(self::ENTITY_TYPE_ID, $entity);
    $saved = $this->drupalContext->getDriver()->createEntity(self::ENTITY_TYPE_ID, $entity);
    $this->entities[] = $saved;
    return $saved;
  }

  /**
   * Remove any created products.
   *
   * @AfterScenario
   */
  public function cleanup() {
    // Remove any paragraphs that were created.
    foreach ($this->entities as $entity) {
      $this->drupalContext->getDriver()->entityDelete(self::ENTITY_TYPE_ID, $entity);
    }
    $this->entities = [];
    $this->paragraphNames = [];
  }

  /**
   * Creates paragraph of the given type, provided in the form:
   * | title     | My node        |
   * | Field One | My field value |
   * | author    | Joe Editor     |
   * | status    | 1              |
   * | ...       | ...            |
   *
   * @Given a/an :type paragraph named :name:
   */
  public function createParagraph($type, $name, TableNode $fields) {
    $entity = (object) [
      'type' => $type,
    ];
    foreach ($fields->getRowsHash() as $field => $value) {
      $entity->{$field} = $value;
    }
    $saved = $this->create($entity);
    $this->paragraphNames[$name] = $saved->id;
  }
  /**
   * @BeforeNodeCreate
   */
  public function beforeNodeCreateHook(BeforeNodeCreateScope $scope) {
    // This is missing in the Drupal driver
    /** @var \Drupal\field\Entity\FieldStorageConfig[] $fields */
    $fields = \Drupal::service('entity_field.manager')
      ->getFieldStorageDefinitions('node');
    $node = $scope->getEntity();
    foreach ((array)$node as $field_name => $value) {
      if (is_array($value) || !isset($fields[$field_name])
        || !$fields[$field_name]->getSettings()
        || !isset($fields[$field_name]->getSettings()['target_type'])
        || $fields[$field_name]->getSettings()['target_type'] !== 'paragraph') {
        continue;
      }
      $target_id = isset($this->paragraphNames[$value]) ? $this->paragraphNames[$value] : NULL;
      if (!$target_id) {
        sprintf('Referenced paragraph name "%s" not found.', $value);
        return;
      }
      $paragraph = \Drupal\paragraphs\Entity\Paragraph::load($target_id);
      $column_name = "$field_name:target_id";
      $node->$column_name = $target_id;
      $column_name = "$field_name:target_revision_id";
      $node->$column_name = $paragraph->getRevisionId();
    }
  }
}

