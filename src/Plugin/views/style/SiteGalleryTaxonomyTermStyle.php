<?php

namespace Drupal\site_gallery\Plugin\views\style;

use Drupal\views\Plugin\views\style\StylePluginBase;

/**
 * Style plugin for the cards view.
 *
 * @ViewsStyle(
 *   id = "site_gallery_taxonomy_term_style",
 *   title = @Translation("Media gallery"),
 *   help = @Translation(""),
 *   theme = "site_gallery_taxonomy_term",
 *   display_types = {"normal"}
 * )
 */
class SiteGalleryTaxonomyTermStyle extends StylePluginBase {

  /**
   * Does this Style plugin allow Row plugins?
   *
   * @var bool
   */
  protected $usesRowPlugin = TRUE;

  /**
   * Does the Style plugin support grouping of rows?
   *
   * @var bool
   */
  protected $usesGrouping = FALSE;

}