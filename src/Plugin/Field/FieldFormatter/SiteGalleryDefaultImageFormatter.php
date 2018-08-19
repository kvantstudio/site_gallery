<?php

namespace Drupal\site_gallery\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\image\Plugin\Field\FieldFormatter\ImageFormatter;

/**
 * Plugin implementation of the 'site_gallery_default_image' formatter.
 *
 * @FieldFormatter(
 *   id = "site_gallery_default_image",
 *   label = @Translation("Image of media gallery"),
 *   field_types = {
 *     "image"
 *   }
 * )
 */
class SiteGalleryDefaultImageFormatter extends ImageFormatter {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = parent::viewElements($items, $langcode);
    foreach ($elements as &$element) {
      $element['#theme'] = 'site_gallery_default_image_formatter';
    }
    return $elements;
  }
}