# Drupal Lazy Button Module

This module is a demonstration module for Drupal 10 that shows how to create a
dynamic user interaction element that uses lazy builders and ajax.

Some important parts:
- LazyButtonBlock - The block that adds the lazy builder element to
the page.
- LazyButtonService - A service that generates a button based on
different parameters.
- LazyButtonController - The ajax callback is posted to this
controller, which then responds with an AjaxResponse object.

Feel free to use this model in your own code.

## Usage:

Install the module and then add the block lazy_button_block to a page of
your choice.

This will initially show a block with the text "Click the button" and secondary
text that shows what the button state value is.

Clicking on this button will change the value of the block to "Button
was clicked".

## Unit Tests

Unit tests exist to run through the different permutations of the button state.
This allows us to be sure that the button states work correctly.
