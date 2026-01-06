<?php

namespace Drupal\h5p_views\Plugin\views\filter;

use Drupal\Core\Database\Connection;
use Drupal\Core\Database\StatementInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\views\Attribute\ViewsFilter;
use Drupal\views\Plugin\views\display\DisplayPluginBase;
use Drupal\views\Plugin\views\filter\InOperator;
use Drupal\views\ViewExecutable;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Exposed filter that lists only H5P library titles which have content.
 *
 * @ingroup views_filter_handlers
 */
#[ViewsFilter('h5p_library_title')]
class H5pLibraryTitleFilter extends InOperator implements ContainerFactoryPluginInterface {
  use StringTranslationTrait;

  /**
   * Database connection.
   */
  protected Connection $connection;

  /**
   * {@inheritDoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): H5pLibraryTitleFilter {
    /** @var static $instance */
    $instance = parent::create($container, $configuration, $plugin_id, $plugin_definition);
    $instance->connection = $container->get('database');
    return $instance;
  }

  /**
   * Build the list of selectable options.
   *
   * Keys are the actual filter values, labels are shown in the dropdown.
   */
  public function getValueOptions(): array|null {
    $options = [];

    $connection = $this->connection;
    if ($connection instanceof Connection) {
      $query = $this->connection->select('h5p_libraries', 'l');

      // Join must be on its own line (it returns the alias).
      $query->innerJoin('h5p_content', 'c', 'c.library_id = l.library_id');

      // Select fields and other clauses on the query object.
      $query
        ->fields('l', ['title', 'library_id'])
        ->distinct()
        ->orderBy('l.title', 'ASC');

      $statement = $query->execute();
      if ($statement instanceof StatementInterface) {
        foreach ($statement->fetchAllAssoc('library_id') as $row) {
          $options[$row->title] = $row->title;
        }
      }

    }

    $this->valueOptions = $options;
    return $this->valueOptions;
  }

  /**
   * Force a dropdown instead of checkboxes as a sensible default.
   *
   * Editors can still change widget type in the Views UI.
   */
  public function init(ViewExecutable $view, DisplayPluginBase $display, ?array &$options = NULL): void {
    parent::init($view, $display, $options);
    $this->valueFormType = 'select';
  }

}
