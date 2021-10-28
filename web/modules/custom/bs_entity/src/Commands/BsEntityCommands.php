<?php

namespace Drupal\bs_entity\Commands;

use Consolidation\OutputFormatters\StructuredData\RowsOfFields;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drush\Commands\DrushCommands;
use Drupal\Component\Serialization\Yaml;
use Drupal\menu_link_content\Entity\MenuLinkContent;

/**
 * A Drush commandfile.
 */
class BsEntityCommands extends DrushCommands {

  const MENU_YML_DIR = PROJECT_ROOT . '/config/entity';

  /**
   * Entity type manager.
   *
   * @var entityTypeManager
   */
  protected $entityTypeManager;

  /**
   * EntityCommands constructor.
   */
  public function __construct(EntityTypeManagerInterface $entityTypeManager) {
    $this->entityTypeManager = $entityTypeManager;
  }

  /**
   * Show list of entities.
   *
   * @command entity:list
   * @usage entity:list
   * @aliases etl
   */
  public function entityList($options = ['format' => 'table', 'fields' => '']) {
    $rows = [];
    $types = $this->entityTypeManager->getDefinitions();
    foreach ($types as $key => $config) {
      $rows[$key] = [
        'type id' => $key,
        'label' => (string) $config->getLabel(),
      ];
    }
    return new RowsOfFields($rows);
  }

  /**
   * Generate menu_link entities from yml file.
   *
   * @command menu:gen
   * @usage menu:gen
   * @aliases mmg
   */
  public function mainMenuLinkCreate() {
    $menu_items = [];
    $menu_config = self::MENU_YML_DIR . '/menu.main.yml';
    if (is_readable($menu_config)) {
      $menu_items = Yaml::decode(file_get_contents($menu_config));
    }
    else {
      $this->logger()->error(dt('menu.main.yml file not readable.'));
      return FALSE;
    }
    // https://api.drupal.org/api/drupal/core%21modules%21menu_link_content%21src%21Entity%21MenuLinkContent.php/class/MenuLinkContent/9.2.x
    $menu_items = entity_list('menu_link_content', []);
    print_r($menu_items[1]->uuid());
  }

}
