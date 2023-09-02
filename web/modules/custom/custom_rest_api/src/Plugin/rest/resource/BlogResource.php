<?php

namespace Drupal\custom_rest_api\Plugin\rest\resource;

use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ResourceResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Provides a resource for custom books.
 *
 * @RestResource(
 *   id = "custom_book_resource",
 *   label = @Translation("Custom Book Resource"),
 *   uri_paths = {
 *     "canonical" = "/custom-books"
 *   }
 * )
 */
class BlogResource extends ResourceBase {

  /**
   * Responds to GET requests.
   *
   * @return \Drupal\rest\ResourceResponse
   *   Returns Json response.
   */
  public function get() {
    $response = ['message' => 'Hello, this is a rest service'];
    return new ResourceResponse($response);
  }

}
