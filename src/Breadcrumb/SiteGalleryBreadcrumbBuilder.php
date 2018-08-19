<?php

/**
 * @file
 * Contains \Drupal\site_gallery\Breadcrumb\SiteGalleryBreadcrumbBuilder.
 */

namespace Drupal\site_gallery\Breadcrumb;

use Drupal\Core\Breadcrumb\Breadcrumb;
use Drupal\Core\Breadcrumb\BreadcrumbBuilderInterface;
use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\Core\Link;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Class to define the site_gallery breadcrumb builder.
 */
class SiteGalleryBreadcrumbBuilder implements BreadcrumbBuilderInterface {
  use StringTranslationTrait;

  /**
   * The entity manager.
   *
   * @var \Drupal\Core\Entity\EntityManagerInterface
   */
  protected $entityManager;

  /**
   * The taxonomy storage.
   *
   * @var \Drupal\Taxonomy\TermStorageInterface
   */
  protected $termStorage;

  /**
   * Constructs the TermBreadcrumbBuilder.
   *
   * @param \Drupal\Core\Entity\EntityManagerInterface $entityManager
   *   The entity manager.
   */
  public function __construct(EntityManagerInterface $entityManager) {
    $this->entityManager = $entityManager;
    $this->termStorage = $entityManager->getStorage('taxonomy_term');
  }

  /**
   * @inheritdoc
   */
  public function applies(RouteMatchInterface $route_match) {
    if ($route_match->getRouteName() == 'entity.node.canonical') {
      $node = $route_match->getParameter('node');
    }
    return (!empty($node) ? $node->getType() == 'site_gallery' : FALSE);
  }

  /**
   * @inheritdoc
   */
  public function build(RouteMatchInterface $route_match) {
    $breadcrumb = new Breadcrumb();
    $breadcrumb->addLink(Link::createFromRoute($this->t('Home'), '<front>'));
    $breadcrumb->addLink(Link::createFromRoute($this->t('Media gallery'), 'view.site_gallery.page'));

    $node = $route_match->getParameter('node');
    $node = $this->entityManager->getTranslationFromContext($node);
    $tid = $node->field_gallery_category->target_id;
    $term = \Drupal\taxonomy\Entity\Term::load($tid);
    $term = $this->entityManager->getTranslationFromContext($term);

    // Breadcrumb needs to have terms cacheable metadata as a cacheable
    // dependency even though it is not shown in the breadcrumb because e.g. its
    // parent might have changed.
    $breadcrumb->addCacheableDependency($term);
    // @todo This overrides any other possible breadcrumb and is a pure
    //   hard-coded presumption. Make this behavior configurable per
    //   vocabulary or term.
    $parents = $this->termStorage->loadAllParents($term->id());
    // Remove current term being accessed.
    array_shift($parents);
    foreach (array_reverse($parents) as $parents_term) {
      $parents_term = $this->entityManager->getTranslationFromContext($parents_term);
      $breadcrumb->addCacheableDependency($parents_term);
      $breadcrumb->addLink(Link::createFromRoute($parents_term->getName(), 'entity.taxonomy_term.canonical', array('taxonomy_term' => $parents_term->id())));
    }

    $breadcrumb->addLink(Link::createFromRoute($term->getName(), 'entity.taxonomy_term.canonical', array('taxonomy_term' => $term->id())));

    // This breadcrumb builder is based on a route parameter, and hence it
    // depends on the 'route' cache context.
    $breadcrumb->addCacheContexts(['route']);

    return $breadcrumb;
  }

}