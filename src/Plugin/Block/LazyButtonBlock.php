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
    // Get the current user from the cache context.
    $currentUser = $this->getContextValue('current_user');

    $build = [];

    // Add the lazy builder for the button.
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
    // Load the user.
    $entityTypeManager = \Drupal::service('entity_type.manager');
    $user = $entityTypeManager->getStorage('user')->load($userId);

    // Get the current state of the button.
    $state = \Drupal::service('state');
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
