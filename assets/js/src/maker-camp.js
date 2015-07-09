/**
 * Maker Camp
 * http://quorstudio.com
 *
 * Copyright (c) 2015 Alex T (Quorstudio)
 * Licensed under the GPLv2+ license.
 */

(function (window, undefined) {
  'use strict';

  jQuery(window).load(function () {

    var startAtSlide = jQuery('.taxonomy-weeks li[data-is-current="1"]').attr('data-index');

    /**
     * Initialize flexslider
     */
    var mainSlider = jQuery('.makercamp .flexslider').flexslider({
      animation    : "slide",
      animationLoop: false,
      controlNav   : false,
      slideshow    : false,
      keyboard     : false,
      startAt      : (startAtSlide ? startAtSlide : 0)
    });

    /**
     * 1. Add text for prev/next buttons on hover
     * 2. Handle unhover/hover when clicking on arrows
     */
    jQuery('.flex-direction-nav a').hover(function () {
      var label = '';
      if (jQuery(this).hasClass('flex-next')) {
        label = mainSlider.find('.flex-active-slide').next().find('.week-title').attr('data-title');
      } else if (jQuery(this).hasClass('flex-prev')) {
        label = mainSlider.find('.flex-active-slide').prev().find('.week-title').attr('data-title');
      }

      jQuery(this).attr('data-content', label);
    }, function () {
      jQuery(this).attr('data-content', '');
    })
        .click(function () {
          jQuery(this).trigger('mouseout');
          jQuery(this).trigger('mouseenter');
        });

    /**
     * Initialize fancybox for first video on button click
     */
    jQuery(document).on('click', '.play-first-video-button', function (e) {
      e.preventDefault();

      jQuery('.dayly-camp-videos a').eq(0).trigger("click");
    });

    /**
     * Initialize fancybox for videos
     */
    jQuery(".makercamp .fancybox").attr('rel', 'gallery').fancybox({
      loop: false
    }); // fancybox

    /**
     * Open calendar handler
     */
    jQuery(document).on('click', '.calendar-button', function (e) {
      e.preventDefault();
      var calWrapper = jQuery('.calendar-wrapper');

      jQuery('#container').css({
        'height'  : calWrapper.outerHeight(),
        'overflow': 'hidden'
      });

      jQuery('#footer').hide();

      window.scrollTo(0, 0);

      calWrapper.show();

    });

    /**
     * Close calendar handler (upon clicking on overlay)
     */
    jQuery(document).on('click', '.calendar-wrapper', function (e) {
      if ($(e.target).hasClass('calendar-wrapper')) {
        var calWrapper = jQuery('.calendar-wrapper');

        jQuery('#container').css({
          'height'  : 'auto',
          'overflow': 'visible'
        });

        jQuery('#footer').show();

        calWrapper.hide();
      }
    });

    /**
     * Close calendar handler (upon clicking on go back link)
     */
    jQuery(document).on('click', '.calendar .go-back', function (e) {
      var calWrapper = jQuery('.calendar-wrapper');

      jQuery('#container').css({
        'height'  : 'auto',
        'overflow': 'visible'
      });

      jQuery('#footer').show();

      calWrapper.hide();
    });

    /**
     * Don't handle locked days in calendar
     */
    jQuery(document).on('click', '.calendar-wrapper .camp_day-number a', function(e) {
      if (jQuery(this).attr('href') == '#') {
        e.preventDefault();
      }
    });

    /**
     * Initialize popover on calendar days hover
     */
    jQuery('.calendar-wrapper .camp_day-number a').hover(function () {
      var element = $(this).parent();

      // Popover on
      element.popover({
        title: '',
        content: function() {
          return jQuery(this).attr('data-title');
        }
      }).popover('show');
    }, function() {
      var element = $(this).parent();

      // Popover off
      element.popover('hide');
    });

  });

})(this);

/**
 * Trigger first video modal on page load
 */
jQuery(document).ready(function () {
  setTimeout(
      function () {
        jQuery('.dayly-camp-videos a').eq(0).trigger("click");
      }, 2000);
});