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
    // Set the text based on the content of the button state.
    if ($buttonState === 1) {
      $buttonText = $this->t('Button was clicked');
      $additionalText = $this->t('Button state is 1');
    }
    else {
      $buttonText = $this->t('Click the button');
      $additionalText = $this->t('Button state is 0');
    }

    // Initialize the output render array.
    $build = [];

    // Add a prefix and suffix to the button. The lazy-button-wrapper
    // class will be used to replace the button in the ajax callback. The
    // weight of these elements is set to -50 and +50 so that all other
    // elements will appear between them.
    $build['dynamic_lazy_button_prefix'] = [
      '#markup' => '<div class="lazy-button-wrapper">',
      '#weight' => -50,
    ];
    $build['dynamic_lazy_button_suffix'] = [
      '#markup' => '</div>',
      '#weight' => 50,
    ];

    // Add a user greeting to the beginning of the output.
    $build['user_greeting'] = [
      '#markup' => '<p>' . $this->t('Hi @username!', ['@username' => $user->getDisplayName()]) . '</p>',
    ];

    // Add the button itself. This must have the class of 'use-ajax' to
    // enable the ajax callback.
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

    // Add the additional text element.
    $build['additional'] = [
      '#markup' => '<p>' . $additionalText . '</p>',
    ];

    // Setting the correct cache contexts is important to ensure that the
    // button is cached correctly.
    $build['#cache'] = [
      'contexts' => [
        'user',
        'route',
      ],
    ];

    // We need to ensure that the drupal.ajax library is included in the page.
    $build['#attached']['library'][] = 'core/drupal.ajax';

    return $build;
  }

}
