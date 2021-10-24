<?php

namespace Drupal\hugo_page\Commands;

use Drupal\path_alias\Entity\PathAlias;
use Drupal\hugo_page\Controller\HugoPageController;
use Drush\Commands\DrushCommands;

/**
 * A Drush commandfile.
 */
class HugoPageCommands extends DrushCommands {

  /**
   * Build route pathnames and path aliases for html, json built by hugo.
   *
   * @command hugo_page:build
   * @usage hugo_page:build
   * @aliases hpb
   */
  public function build() {
    $hugo_dir = PROJECT_ROOT . '/hugo';
    $hugo_build = "cd " . $hugo_dir . " && hugo";
    if (!is_readable($hugo_dir . '/content')) {
      $this->logger()->error(dt('Content directory not found.'));
      return FALSE;
    }
    elseif (!shell_exec($hugo_build)) {
      $this->logger()->error(dt('Hugo build failed.'));
      return FALSE;
    }

    $active = HugoPageController::getActiveState();
    $staged = \Drupal::state()->get('hugo_page.routes');

    // Active, stage all empty.
    if (empty($active) && empty($staged)) {
      $this->logger()->notice(dt('No content found. Add one and retry.'));
    }
    // Active empty, Stage not empty, then delete aliases from $staged.
    if (empty($active) && !empty($staged)) {
      $this->deleteAliases($staged);
      $this->logger()->notice(dt('@count Path aliases were deleted.', [
        '@count' => count($staged),
      ]));
    }
    // Active not empty, Stage empty, then create aliases from $active.
    if (!empty($active) && empty($staged)) {
      $this->createAliases($active);
    }
    // Active not empty, Stage not empty.
    if (!empty($active) && !empty($staged)) {
      if ($active == $staged) {
        $this->logger()->notice(dt('No differences found.'));
        return 0;
      }
      $this->deleteAliases($staged);
      $this->createAliases($active);
      $this->logger()->notice(dt('Path aliases were rebuilt.'));
    }
    \Drupal::state()->set('hugo_page.routes', $active);
    $this->logger()->notice(dt('Path aliases were synced.'));
  }

  /**
   * Get aliases from $staged.
   */
  private function getAliases($staged) {
    $alias_store = \Drupal::entityTypeManager()->getStorage('path_alias');
    $paths = array_map(function ($routeId) {
      return '/hugo-page/' . $routeId;
    }, $staged);
    $aliases = $alias_store->loadByProperties(['path' => $paths]);
    if (!empty($aliases)) {
      $ids = implode(', ', array_keys($aliases));
      $this->logger()->notice(dt('Path aliases: @ids', ['@ids' => $ids]));
    }
    return $aliases;
  }

  /**
   * Delete aliases.
   */
  private function deleteAliases($paths) {
    $alias_store = \Drupal::entityTypeManager()->getStorage('path_alias');
    $aliases = $this->getAliases($paths);
    $alias_store->delete($aliases);
    $this->logger()->notice(dt('@count Path aliases were deleted.', [
      '@count' => count($paths),
    ]));
  }

  /**
   * Create aliases.
   */
  private function createAliases($routeIds) {
    foreach ($routeIds as $routeId) {
      $path = str_replace('_', '/', $routeId);
      $alias = PathAlias::create([
        'path' => '/hugo-page/' . $routeId,
        'alias' => '/' . $path,
        'langcode' => 'ko',
      ]);
      $alias->save();
    }
    $this->logger()->success(dt('@count Path aliases were created.', [
      '@count' => count($routeIds),
    ]));
  }

  /**
   * Delete state and all aliases of markdown pages.
   *
   * @command hugo_page:purge
   * @usage hugo_page:purge
   * @aliases hpp
   */
  public function purge() {
    $staged = \Drupal::state()->get('hugo_page.routes');
    if (!empty($staged)) {
      $this->deleteAliases($staged);
    }
    \Drupal::state()->delete('hugo_page.routes');
    $this->logger()->notice(dt('Purged state and all aliases of markdown pages.'));
  }

}
