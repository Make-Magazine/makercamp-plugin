/*! Maker Camp - v0.1.0
 * http://quorstudio.com
 * Copyright (c) 2015; * Licensed GPLv2+ */
(function (window, undefined) {
	'use strict';

	jQuery(document).ready(function ($) {

		/**
		 * File uploader
		 */
		var file_uploader_frame; // Instantiates the variable that holds the media library frame.

		// Runs when the image button is clicked.
		$('.makercamp-file-uploader').click(function (e) {

			var me = $(this);
			// Prevents the default action from occuring.
			e.preventDefault();

			var image = $(this).hasClass('makercamp-image') ? { type: 'image' } : '';

			// Sets up the media library frame
			file_uploader_frame = wp.media.frames.file_uploader_frame = wp.media({
				title : vars.uploader_title,
				button: {text: vars.uploader_button},
				library: image
			});

			// Runs when an image is selected.
			file_uploader_frame.on('select', function () {

				// Grabs the attachment selection and creates a JSON representation of the model.
				var media_attachment = file_uploader_frame.state().get('selection').first().toJSON();

				// Sends the attachment URL to our custom image input field.
				me.prev('.makercamp-file-uploaded').val(media_attachment.url);
			});

			// Opens the media library frame.
			file_uploader_frame.open();
		});

		/**
		 * Dynamically add project link
		 */
		$('.makercamp-add-project').click(function (e) {

			// Prevents the default action from occuring.
			e.preventDefault();

      var new_projects_element = $('.new-projects');

      var last_existing_index = $('.project-sources').find('li').last().attr('data-index');
      var last_new_index = new_projects_element.find('li').last().attr('data-index');

      var new_index = last_new_index ? parseInt(last_new_index) + 1 : (last_existing_index ? parseInt(last_existing_index) + 1 : 0);

			var tmpl = '<li data-index="' + new_index + '"><input type="text" name="makercamp_project_source[' + new_index + '][url]" placeholder="' + vars.url_placeholder + '" size="25" /><input type="text" name="makercamp_project_source[' + new_index + '][title]" placeholder="' + vars.title_placeholder + '" size="25" /><a class="makercamp-project-delete" href="#">' + vars.link_delete + '</a></li>';

      new_projects_element.append(tmpl);
		});

		/**
		 * Dynamically remove project link
		 */
		$(document).on('click', '.makercamp-project-delete', function (e) {

			// Prevents the default action from occuring.
			e.preventDefault();

			$(this).parent('li').remove();
		});


		/**
		 * Add ability create new terms for taxonomy
		 */
		var taxonomy = vars.custom_taxonomy_slug;

		$('#' + taxonomy + 'checklist li :radio, #' + taxonomy + 'checklist-pop :radio').live('click', function () {
			var t = $(this), c = t.is(':checked'), id = t.val();
			$('#' + taxonomy + 'checklist li :radio, #' + taxonomy + 'checklist-pop :radio').prop('checked', false);
			$('#in-' + taxonomy + '-' + id + ', #in-popular-' + taxonomy + '-' + id).prop('checked', c);
		});

		$('#' + taxonomy + '-add .radio-tax-add').on('click', function () {
			var term = $('#' + taxonomy + '-add #new' + taxonomy).val();
			var nonce = $('#' + taxonomy + '-add #_wpnonce_radio-add-tag').val();
			$.post(ajaxurl, {
				action                  : 'radio_tax_add_taxterm',
				term                    : term,
				'_wpnonce_radio-add-tag': nonce,
				taxonomy                : taxonomy
			}, function (r) {
				$('#' + taxonomy + 'checklist').append(r.html).find('li#' + taxonomy + '-' + r.term + ' :radio').attr('checked', true);
			}, 'json');
		});
	});

})(this);
