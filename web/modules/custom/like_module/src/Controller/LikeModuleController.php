<?php

namespace Drupal\like_module\Controller;

use Drupal\Core\Cache\Cache;
use Drupal\Core\Controller\ControllerBase;
use Drupal\node\Entity\Node;
use Drupal\node\NodeInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Returns responses for like_module routes.
 */
final class LikeModuleController extends ControllerBase {

  /**
   * Updates like count for a blog.
   * 
   * @param string $nid
   *  node id of the blog.
   * 
   * @return Symfony\Component\HttpFoundation\JsonResponse
   *  returns a message and status in Json format.
   */
  public function updateLike(string $nid): JsonResponse {

    $node = Node::load((int)$nid);

    if ($node instanceof NodeInterface) {
      try {
        $likes = $node->get('field_likes')->value;
        $currentLike = $likes ? $likes + 1 : 1;
        $node->set('field_likes', $currentLike);
        $node->save();

        // Invalidating cache tag
        $cache_tags = [
          'node:' . $nid,
        ];
        Cache::invalidateTags($cache_tags);
        return new JsonResponse(array('message' => 'Like count updated successfully.'));
      }
      catch (\Exception $e) {
        watchdog_exception('myerrorid', $e);
      }
    }
    else {
      return new JsonResponse(array('message' => 'Node not found.'), 404);
    }
  }

}
