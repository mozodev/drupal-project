<?php

namespace Drupal\bs_entity\Commands;

use Consolidation\OutputFormatters\StructuredData\RowsOfFields;
use Drupal\Component\Serialization\Yaml;
use Drupal\Component\Utility\Html;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Path\PathValidator;
use Drush\Commands\DrushCommands;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;

/**
 * A Drush commandfile.
 */
class BsEntityCommands extends DrushCommands {

  const MENU_YML_DIR = PROJECT_ROOT . '/config/entity';

  /**
   * Entity type manager service.
   *
   * @var entityTypeManager
   */
  protected $entityTypeManager;

  /**
   * Path validator service.
   *
   * @var pathValidator
   */
  protected $pathValidator;

  /**
   * EntityCommands constructor.
   */
  public function __construct(
    EntityTypeManagerInterface $entityTypeManager,
    PathValidator $pathValidator
  ) {
    $this->entityTypeManager = $entityTypeManager;
    $this->pathValidator = $pathValidator;
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
    $links = [];
    $menu_config = self::MENU_YML_DIR . '/menu.main.yml';
    if (is_readable($menu_config)) {
      $links = Yaml::decode(file_get_contents($menu_config));
      if (empty($links) || !(count($links) > 0)) {
        $this->logger()->error(dt('menu.main.yml file empty.'));
        return FALSE;
      }
    }
    else {
      $this->logger()->error(dt('menu.main.yml file not readable.'));
      return FALSE;
    }

    $menu_link_storage = \Drupal::entityTypeManager()->getStorage('menu_link_content');
    foreach ($links as $key => $link) {
      $item = $this->buildMenuItem($link);
      try {
        if (!empty($item)) {
          $link = $menu_link_storage->create($item)->save();
          $this->logger()->success(dt('@url', [
            '@url' => $item['link']['uri'],
          ]));
        }
        else {
          continue;
        }
      }
      catch (InvalidArgumentException $e) {
        $this->logger()->error(dt('@message', [
          '@message' => $e->getMessage(),
        ]));
        continue;
      }
    }

  }

  /**
   * Purge main menu_link entities.
   *
   * @command menu:purge
   * @usage menu:purge
   * @aliases mmp
   */
  public function mainMenuLinkPurge() {
    $menu_link_storage = \Drupal::entityTypeManager()->getStorage('menu_link_content');
    $menu_items = $menu_link_storage->loadByProperties();
    try {
      $menu_link_storage->delete($menu_items);
      $this->logger()->success(dt('Main menu link items purged: @mlids', [
        '@mlids' => implode(',', array_keys($menu_items)),
      ]));
    }
    catch (Exception $e) {
      $this->logger()->error(dt('error: @message', [
        '@message' => $e->getMessage(),
      ]));
    }
  }

  /**
   * Build menu item payloads from links (title, uri).
   */
  private function buildMenuItem(array $link) {
    if (empty($link)
      || empty($link['title'])
      || empty($link['url'])
      || !$this->pathValidator->isValid($link['url'])) {
      $flag = $this->pathValidator->isValid($link['url']) ? 1 : 0;
      $this->logger()->error(dt('empty - @flag - @link', [
        '@flag' => $flag,
        '@link' => print_r($link, TRUE),
      ]));
      return [];
    }
    // Build options.
    $slug = Html::cleanCssIdentifier(ltrim($link['url'], '/'));
    $options['attributes'] = [
      'id' => 'menu-item-hugo--' . $slug,
      'class' => 'menu-item-hugo',
    ];
    // Build url.
    if (!str_starts_with($link['url'], 'http')
      || !str_starts_with($link['url'], '<')) {
      $link['url'] = 'internal:' . $link['url'];
    }
    else {
      $options['attributes']['target'] = '_blank';
    }
    return [
      'title' => $link['title'],
      'link' => [
        'uri' => $link['url'],
        'title' => $link['title'],
        'options' => $options,
      ],
      'menu_name' => 'main',
    ];
  }

}
