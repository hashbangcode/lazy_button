<?php

namespace Drupal\lazy_button;

use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\StringTranslation\TranslationInterface;
use Drupal\Core\Url;
use Drupal\user\UserInterface;

/**
 * Service class to generate a stateful button.
 */
class LazyButtonService implements LazyButtonServiceInterface {
  use StringTranslationTrait;

  /**
   * Constructs a new LazyButtonService.
   *
   * @param \Drupal\Core\StringTranslation\TranslationInterface $string_translation
   *   The string translation service.
   */
  public function __construct(TranslationInterface $string_translation) {
    $this->stringTranslation = $string_translation;
  }

  /**
   * {@inheritDoc}
   */
  public function generateButton(UserInterface $user, $buttonState):array {

    if ($buttonState === 1) {
      $buttonText = $this->t('Button was clicked');
      $additionalText = $this->t('Button state is 1');
    }
    else {
      $buttonText = $this->t('Click the button');
      $additionalText = $this->t('Button state is 0');
    }

    $build = [];

    $build['dynamic_lazy_button_prefix'] = [
      '#markup' => '<div class="lazy-button-wrapper">',
      '#weight' => -50,
    ];

    $build['dynamic_lazy_button_suffix'] = [
      '#markup' => '</div>',
      '#weight' => 50,
    ];

    $build['#attached']['library'][] = 'core/drupal.dialog.ajax';

    $build['#cache'] = [
      'contexts' => [
        'user',
        'route',
      ],
    ];

    $build['user_greeting'] = [
      '#markup' => '<p>' . $this->t('Hi @username!', ['@username' => $user->getDisplayName()]) . '</p>',
    ];

    $build['lazy_button'] = [
      '#type' => 'link',
      '#url' => Url::fromRoute('lazy_button_callback'),
      '#title' => $buttonText,
      '#attributes' => [
        'class' => [
          'use-ajax',
          'button',
        ],
      ],
    ];

    $build['additional'] = [
      '#markup' => '<p>' . $additionalText . '</p>',
    ];

    return $build;
  }

}
