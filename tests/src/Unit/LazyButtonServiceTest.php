<?php

namespace Drupal\Test\lazy_button\Unit;

use Drupal\lazy_button\LazyButtonService;
use Drupal\Tests\UnitTestCase;
use Drupal\user\Entity\User;

/**
 * Test the different states of the lazy button service.
 */
class LazyButtonServiceTest extends UnitTestCase {

  /**
   * The test user.
   *
   * @var \Drupal\user\Entity\User\PHPUnit\Framework\MockObject\MockObject|\PHPUnit\Framework\MockObject\MockObject
   */
  protected $user;

  /**
   * {@inheritDoc}
   */
  public function setUp():void {
    parent::setUp();

    $this->user = $this->getMockBuilder(User::class)
      ->disableOriginalConstructor()
      ->getMock();

    $this->user->expects($this->any())
      ->method('getDisplayName')
      ->willReturn('user');

    $translate = $this->getStringTranslationStub();

    $this->lazyButtonService = $this->getMockBuilder(LazyButtonService::class)
      ->setConstructorArgs([$translate])
      ->enableProxyingToOriginalMethods()
      ->getMock();
  }

  /**
   * Test the buttons initial state.
   */
  public function testButtonInitialState() {
    $buttonArray = $this->lazyButtonService->generateButton($this->user, 0);
    $this->assertEquals('<p>Hi user!</p>', $buttonArray['user_greeting']['#markup']);
    $this->assertEquals('Click the button', $buttonArray['lazy_button']['#title']);
    $this->assertEquals('<p>Button state is 0</p>', $buttonArray['additional']['#markup']);
  }

  /**
   * Test the button clicked state.
   */
  public function testOffState() {
    $buttonArray = $this->lazyButtonService->generateButton($this->user, 1);
    $this->assertEquals('<p>Hi user!</p>', $buttonArray['user_greeting']['#markup']);
    $this->assertEquals('Button was clicked', $buttonArray['lazy_button']['#title']);
    $this->assertEquals('<p>Button state is 1</p>', $buttonArray['additional']['#markup']);
  }

}
