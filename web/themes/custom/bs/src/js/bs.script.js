import './_bootstrap.js';

(function ($, Drupal) {

  'use strict';

  // // Enable Bootstrap Popover sitewide.
  // // Popovers are opt-in for performance reasons.
  // Drupal.behaviors.bsPopover = {
  //   attach: function (context) {
  //     $('[data-bs-toggle="popover"]').popover();
  //   }
  // };

  // // Enable Bootstrap Toast sitewide.
  // // Toasts are opt-in for performance reasons.
  // Drupal.behaviors.bsToast = {
  //   attach: function (context) {
  //     $('.toast').toast('show');
  //   }
  // };

  Drupal.behaviors.bsDarkMode = {
    attach: context => {
      // Hello darkness, my old friend.
      if (window.matchMedia &&
        window.matchMedia('(prefers-color-scheme)').media == 'not all' &&
        !$('body').hasClass('bs--dark-mode')) {
        $('body').addClass('bs--dark-mode')
      }
    }
  }

})(jQuery, Drupal);
