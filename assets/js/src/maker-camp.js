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
      animation : "slide",
      animationLoop: false,
      controlNav: false,
      slideshow : false,
      keyboard: false,
      startAt: (startAtSlide ? startAtSlide : 0)
    });

    /**
     * 1. Add text for prev/next buttons on hover
     * 2. Handle unhover/hover when clicking on arrows
     */
    jQuery('.flex-direction-nav a').hover(function() {
      var label = '';
      if (jQuery(this).hasClass('flex-next')) {
        label = mainSlider.find('.flex-active-slide').next().find('.week-title').attr('data-title');
      } else if(jQuery(this).hasClass('flex-prev')) {
        label = mainSlider.find('.flex-active-slide').prev().find('.week-title').attr('data-title');
      }

      jQuery(this).attr('data-content', label);
    }, function() {
      jQuery(this).attr('data-content', '');
    })
        .click(function() {
      jQuery(this).trigger('mouseout');
      jQuery(this).trigger('mouseenter');
    });

    /**
     * Initialize fancybox for first video on button click
     */
    jQuery(document).on('click', '.play-first-video-button', function(e) {
      e.preventDefault();

      jQuery('.dayly-camp-videos a').eq(0).trigger("click");
    });

    /**
     * Initialize fancybox for videos
     */
    jQuery(".makercamp .fancybox").attr('rel', 'gallery').fancybox({
      loop: false
    }); // fancybox

  });

})(this);
