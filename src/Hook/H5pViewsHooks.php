<?php

namespace Drupal\h5p_views\Hook;

use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Implements hooks for H5P Views module.
 */
class H5pViewsHooks {

  use StringTranslationTrait;

  /**
   * Adds fields for views.
   */
  #[Hook('views_data')]
  public function addH5pViewData(): array {
    // Finding the data through a join to another table.
    $data['h5p_libraries']['table']['join'] = [
      'h5p_content' => [
        'left_field' => 'library_id',
        'field' => 'library_id',
      ],
    ];

    // Definition of the type of data.
    $data['h5p_libraries']['title'] = [
      'group' => $this->t('H5P Content'),
      'title' => $this->t('H5P Library Type'),
      'help' => $this->t('The H5P library of this content'),
      'field' => [
        'id' => 'standard',
      ],
      'filter' => [
        'id' => 'h5p_library_title',
      ],
      'argument' => [
        'id' => 'standard',
      ],
      'sort' => [
        'id' => 'standard',
      ],
    ];

    return $data;
  }

}
