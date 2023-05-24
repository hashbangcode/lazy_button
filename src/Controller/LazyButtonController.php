<?php

namespace Drupal\lazy_button\Controller;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\InsertCommand;
use Drupal\Core\Controller\ControllerBase;
use Drupal\lazy_button\LazyButtonServiceInterface;
use Drupal\user\UserInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\State\StateInterface;

/**
 * Controller for responding to lazy button ajax calls.
 */
class LazyButtonController extends ControllerBase {

  /**
   * The lazy button service.
   *
   * @var \Drupal\lazy_button\LazyButtonServiceInterface
   */
  protected $lazyButtonService;

  /**
   * The state service.
   *
   * @var \Drupal\Core\State\StateInterface
   */
  protected $state;

  /**
   * {@inheritDoc}
   */
  public static function create(ContainerInterface $container) {
    $instance = new static();
    $instance->setLazyButtonService($container->get('lazy_button.button_service'));
    $instance->setState($container->get('state'));
    return $instance;
  }

  /**
   * Sets the lazy button service.
   *
   * @param \Drupal\lazy_button\LazyButtonServiceInterface $lazyButtonService
   *   The lazy button service.
   *
   * @return self
   *   The current object.
   */
  public function setLazyButtonService(LazyButtonServiceInterface $lazyButtonService): self {
    $this->lazyButtonService = $lazyButtonService;
    return $this;
  }

  /**
   * Set the state service.
   *
   * @param \Drupal\Core\State\StateInterface $state
   *   The state service.
   *
   * @return self
   *   The current object.
   */
  public function setState(StateInterface $state): self {
    $this->state = $state;
    return $this;
  }

  /**
   * Callback for the route lazy_button_callback.
   *
   * @return \Drupal\Core\Ajax\AjaxResponse
   *   The response.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function lazyButtonCallback() {
    $response = new AjaxResponse();
    // Do not cache the response.
    $response->setMaxAge(-1);

    // Load the current user entity.
    $user = $this->entityTypeManager()
      ->getStorage('user')
      ->load($this->currentUser()->id());

    if (!($user instanceof UserInterface)) {
      return $response;
    }

    // Get the current button state.
    $buttonState = $this->state->get('lazy_button.state');

    // Change the button state depending on the value.
    if ($buttonState === 1) {
      $buttonState = 0;
    }
    else {
      $buttonState = 1;
    }
    $this->state->set('lazy_button.state', $buttonState);

    // Generate the button based on the user and the new state.
    $button = $this->lazyButtonService->generateButton($user, $buttonState);

    // Add the button to the ajax response as an insert command.
    $response->addCommand(new InsertCommand('.lazy-button-wrapper', $button));
    return $response;
  }

}
