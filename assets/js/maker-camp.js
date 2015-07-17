/*! Maker Camp - v0.1.0
 * http://quorstudio.com
 * Copyright (c) 2015; * Licensed GPLv2+ */
(function (window, undefined) {
  'use strict';

  jQuery(window).load(function () {

    var startAtSlide = jQuery('.taxonomy-weeks li[data-is-current="1"]').attr('data-index');

    /**
     * Helper function: update day content
     */
    var updateDayContent = function (new_camp_day, slideIndex) {

      slideIndex = typeof slideIndex !== 'undefined' ? slideIndex : new_camp_day.closest('.taxonomy-week').attr('data-index');

      /**
       * Remove opened-day class from current opened day
       */
      mainSlider.find('.camp_day-number').removeClass('opened-day');

      /**
       * Figure out new opened day ID, title, description and assign needed class to it
       */
      new_camp_day.addClass('opened-day');
      var new_camp_day_id = new_camp_day.attr('data-id');
      var new_camp_day_title = new_camp_day.attr('data-title');
      var new_camp_day_description = new_camp_day.attr('data-description');

      /**
       * Do same with class for calendar
       */
      jQuery('.calendar .camp_day-number').removeClass('opened-day').filter('[data-id="' + new_camp_day_id + '"]').addClass('opened-day');

      /**
       * Update current day title and description inside current slide (which is week)
       */
      mainSlider.find('.taxonomy-week[data-index="' + slideIndex + '"] .day-title').html('<span class="label">' + vars.day_label + ' ' + new_camp_day.find('a').html() + '</span>: ' + new_camp_day_title);
      mainSlider.find('.taxonomy-week[data-index="' + slideIndex + '"] .day-description').html(new_camp_day_description);

      /**
       * Make and ajax call
       */
      jQuery.ajax({
        type   : "post",
        url    : vars.ajax_url,
        data   : {action: 'refresh_camp_day_content', camp_day_id: new_camp_day_id, _ajax_nonce: vars.ajax_nonce},
        success: function (html) { //so, if data is retrieved, store it in html

          /**
           * Update url according to day
           */
          history.pushState({}, '', new_camp_day.find('a').attr('href'));

          /**
           * Update title according new day
           */
          $('title').html(new_camp_day_title);

          $('.makercamp .day-wrapper').fadeOut('slow', function () {
            $(this).html($(html));
            $(this).fadeIn('fast');
          });
        }
      }); //close jQuery.ajax
    };

    /**
     * Initialize flexslider
     */
    var mainSlider = jQuery('.makercamp .flexslider').flexslider({
      animation    : "slide",
      animationLoop: false,
      controlNav   : false,
      slideshow    : false,
      keyboard     : false,
      startAt      : (startAtSlide ? startAtSlide : 0),

      /**
       * Setup ajax content reload on week change
       */
      before: function (slider) {

        /**
         * Get our new camp day and call update day content func
         */
        var nextSlideIndex = slider.animatingTo;
        var new_camp_day = mainSlider.find('.taxonomy-week[data-index="' + nextSlideIndex + '"] .camp_days .camp_day-number').first();
        updateDayContent(new_camp_day, nextSlideIndex);
      }
    });

    /**
     * Setup ajax content reload on day change
     */
    jQuery(document).on('click', '.makercamp .flexslider .camp_day-number a', function (e) {
      e.preventDefault();

      /**
       * Don't allow locked weeks to be available
       */
      if ($(this).attr('href') == "#") {
        return false;
      }

      /**
       * Get our new camp day and call update day content func
       */
      var new_camp_day = $(this).parent();
      updateDayContent(new_camp_day);
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
    jQuery(document).on('click', '.calendar-wrapper .camp_day-number a', function (e) {
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
        title  : '',
        content: function () {
          return jQuery(this).attr('data-title');
        }
      }).popover('show');
    }, function () {
      var element = $(this).parent();

      // Popover off
      element.popover('hide');
    });

    /**
     * Trigger first video modal on page load
     */
    setTimeout(
        function () {
          jQuery('.dayly-camp-videos a').eq(0).trigger("click");
        }, 2000);

  });

})(this);