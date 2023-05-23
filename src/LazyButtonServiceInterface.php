<?php

namespace Drupal\lazy_button;

use Drupal\user\UserInterface;

/**
 * Interface for the lazy button service.
 */
interface LazyButtonServiceInterface {

  /**
   * Generates a button based on the passed parameters.
   *
   * @param \Drupal\user\UserInterface $user
   *   The user.
   * @param string $buttonState
   *   The button state.
   *
   * @return array
   *   The button render array.
   */
  public function generateButton(UserInterface $user, $buttonState):array;

}
