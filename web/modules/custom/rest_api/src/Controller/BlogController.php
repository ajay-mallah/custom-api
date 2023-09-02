<?php

namespace Drupal\rest_api\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Entity\EntityTypeManager;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Returns responses for Rest api routes.
 */
final class BlogController extends ControllerBase {

  /**
   * Manages entity type plugin definitions.
   *
   * @var \Drupal\Core\Entity\EntityTypeManager
   */
  protected $entityTypeManager;

  /**
   * Sets class variables.
   *
   * @param \Drupal\Core\Entity\EntityTypeManager $entityTypeManager
   *   Manages entity type plugin definitions.
   */
  public function __construct(EntityTypeManager $entityTypeManager) {
    $this->entityTypeManager = $entityTypeManager;
  }

  /**
   * Injects dependencies to the class.
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager'),
    );
  }

  /**
   * Builds the response.
   */
  public function __invoke(Request $request): JsonResponse {
    // Fetching condition parameters from the request Url.
    $params = $this->setConditions($request);
    // Fetching node ids.
    $nids = $this->fetchNodeIds($params);
    // Fetching node object by node id.
    $nodes = $this->entityTypeManager->getStorage('node')->loadMultiple($nids);
    $nodes = $this->preProcessNode($nodes);
    return new JsonResponse($nodes);
  }

  /**
   * Sets conditional parameters.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   Request represents an HTTP request.
   */
  public function setConditions(Request $request) {
    $params = [];

    if ($tags = $request->query->get('tags')) {
      // Fetching the term id from term name.
      $terms = $this->entityTypeManager->getStorage('taxonomy_term')
        ->loadByProperties(['name' => explode(" ", $tags)]);
      // Setting up the taxonomy term condition parameters.
      if (!empty($terms)) {
        $term_ids = [];
        foreach ($terms as $term) {
          array_push($term_ids, $term->id());
        }
        $params['tags'] = [
          'key' => 'field__blog_tags.target_id',
          'value' => $term_ids,
          'expression' => 'IN',
        ];
      }
    }

    if ($authors = $request->query->get('authors')) {
      $params['authors'] = [
        'key' => 'uid.entity.name',
        'value' => explode(" ", $authors),
        'expression' => 'IN',
      ];
    }

    if ($start = $request->query->get('start')) {
      $start .= 'T00:00:00';
      $start = new DrupalDateTime($start);
      $params['range'] = [
        'key' => 'created',
        'value' => $start->format('U'),
        'expression' => '>=',
      ];
    }

    if ($end = $request->query->get('end')) {
      $end = new DrupalDateTime($end . 'T23:59:59');
      $end = $end->format('U');
      $params[] = [
        'key' => 'created',
        'value' => $end,
        'expression' => '<=',
      ];
    }

    return $params;
  }

  /**
   * Fetches node ids based on query parameters.
   *
   * @param array $params
   *   Contains request parameters.
   */
  private function fetchNodeIds(array $params): array {
    try {
      $storage = $this->entityTypeManager->getStorage('node');
      $nids = $storage
        ->getQuery()
        ->accessCheck(TRUE)
        ->condition('type', 'blog');

      foreach ($params as $condition) {
        $nids = $nids->condition(
          $condition['key'],
          $condition['value'],
          $condition['expression'],
        );
      }
      $nids = $nids->execute();
    }
    catch (\Exception $e) {
      $this->loggerFactory->get('rest_api')->warning($e->getMessage());
      return [];
    }

    return array_values($nids);
  }

  /**
   * Pre-processes the node fields.
   *
   * @param array $nodes
   *   Array of nodes.
   *
   * @return array
   *   Returns associative array with field key and value.
   */
  private function preProcessNode(array $nodes) {
    $tags = $this->entityTypeManager->getStorage('taxonomy_term')->loadTree('tags');

    // Mapping tags with target id.
    $processedTags = [];
    foreach ($tags as $tag) {
      $processedTags[$tag->tid] = $tag;
    }

    // Fetching term data.
    $data = [];
    foreach ($nodes as $nid => $node) {
      $posted_at = DrupalDateTime::createFromTimestamp($node->get('created')->value);
      $posted_at = $posted_at->format('Y-m-d H:i:s');
      $data[$nid] = [
        'title' => $node->get('title')->value,
        'field_description' => $node->get('field_description')->value,
        'posted_on' => $posted_at,
        'author' => $node->uid->entity->name->value,
        'tags' => $this->getTagsName($processedTags, $node->get('field__blog_tags')->getValue()),
      ];
    }

    return $data;
  }

  /**
   * Returns tags information for a give target id of the taxonomy_term.
   *
   * @param array $taxonomy_terms
   *   Objects of taxonomy terms.
   * @param array $target_ids
   *   Target id of the taxonomy.
   *
   * @return array
   *   Returns associative array with tags name and url link.
   */
  private function getTagsWithLink(array $taxonomy_terms, array $target_ids) :array {
    $tags = [];
    foreach ($target_ids as $tid) {
      $tid = $tid['target_id'];
      $url = Url::fromRoute('entity.taxonomy_term.canonical', [
        'taxonomy_term' => $tid,
      ]);
      $link = Link::fromTextAndUrl($taxonomy_terms[$tid]->name, $url);
      $link = $link->toRenderable();
      $rendered = \Drupal::service('renderer')->render($link);
      $tags[$tid] = [
        'name' => $taxonomy_terms[$tid]->name,
        'link' => $rendered,
      ];
    }
    return $tags;
  }

  /**
   * Returns tags information for a give target id of the taxonomy_term.
   *
   * @param array $taxonomy_terms
   *   Objects of taxonomy terms.
   * @param array $target_ids
   *   Target id of the taxonomy.
   *
   * @return array
   *   Returns associative array with tag names.
   */
  private function getTagsName($taxonomy_terms, $target_ids) :array {
    $tags = [];
    foreach ($target_ids as $tid) {
      $tid = $tid['target_id'];
      $tags[] = $taxonomy_terms[$tid]->name;
    }
    return $tags;
  }

}
