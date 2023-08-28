<?php

namespace Drupal\custom_api\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a block for the home page.
 *
 * @Block(
 *   id = "custom_api_homepage",
 *   admin_label = @Translation("Custom block for home page."),
 *   category = @Translation("Basic page"),
 * )
 */
class HomePageBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    return [
      '#theme' => 'custom_api_homepage_theme',
      '#login_route' => 'user.login',
    ];
  }

}
