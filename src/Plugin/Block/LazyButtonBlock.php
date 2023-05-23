<?php

namespace Drupal\lazy_button\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Security\TrustedCallbackInterface;

/**
 * Provides a block to display the lazy button.
 *
 * @Block(
 *   id = "lazy_button_block",
 *   admin_label = @Translation("Creates a block with a lazy builder button."),
 *   category = "custom",
 *   context_definitions = {
 *     "current_user" = @ContextDefinition("entity:user", required = TRUE, label = @Translation("User"))
 *   }
 * )
 */
class LazyButtonBlock extends BlockBase implements TrustedCallbackInterface {

  /**
   * {@inheritDoc}
   */
  public function build() {
    $currentUser = $this->getContextValue('current_user');

    $build = [];

    $build['lazy_button'] = [
      '#lazy_builder' => [LazyButtonBlock::class . '::lazyButton', [
        $currentUser->id(),
      ],
      ],
      '#create_placeholder' => TRUE,
    ];

    return $build;
  }

  /**
   * Lazy callback for the lazy button.
   *
   * @param int $userId
   *   The user ID.
   *
   * @return array
   *   The lazy button render array.
   */
  public static function lazyButton($userId) {
    $entityTypeManager = \Drupal::service('entity_type.manager');
    $state = \Drupal::service('state');

    $user = $entityTypeManager->getStorage('user')->load($userId);

    $buttonState = $state->get('lazy_button.state');

    // Generate button.
    return \Drupal::service('lazy_button.button_service')->generateButton($user, $buttonState);
  }

  /**
   * {@inheritDoc}
   */
  public static function trustedCallbacks() {
    return [
      'lazyButton',
    ];
  }

}
