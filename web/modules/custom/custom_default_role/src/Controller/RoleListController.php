<?php

namespace Drupal\custom_default_role\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class RoleListController
 *   Handles request for user role list.
 */
class RoleListController extends ControllerBase {

  /**
   * Returns the role list in json format.
   *
   * @return Symfony\Component\HttpFoundation\JsonResponse
   */
  public function getRoleList() {
    $roles = user_roles();
    $role_list = [];
    foreach ($roles as $role_id => $role) {
      if ($role_id !== "authenticated") {
        $role_list[$role_id] = $role->label();
      }
    }

    return new JsonResponse($role_list);
  }

}
