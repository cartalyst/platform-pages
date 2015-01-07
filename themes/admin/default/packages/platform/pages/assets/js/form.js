/**
 * Part of the Platform Pages extension.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the Cartalyst PSL License.
 *
 * This source file is subject to the Cartalyst PSL License that is
 * bundled with this package in the LICENSE file.
 *
 * @package    Platform Pages extension
 * @version    1.0.0
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2015, Cartalyst LLC
 * @link       http://cartalyst.com
 */

var Extension;

;(function(window, document, $, undefined)
{

	'use strict';

	Extension = Extension || {
		Form: {},
	};

	// Initialize functions
	Extension.Form.init = function()
	{
		Extension.Form.selectize();
		Extension.Form.listeners();
	};

	// Add Listeners
	Extension.Form.listeners = function()
	{
		Platform.Cache.$body
			.on('keyup', '#name', Extension.Form.Slug)
			.on('change', '#type', Extension.Form.Storage)
			.on('change', '#file', Extension.Form.Previewer)
			.on('change', '#visibility', Extension.Form.Visibility)
			.on('change', '#menu', Extension.Form.Navigation)
		;

		Extension.Form.Previewer();
	};

	// Slugify
	Extension.Form.Slug = function()
	{
		$('#slug').val(
			$(this).val().slugify()
		);
	};

	// Visibility
	Extension.Form.Visibility = function()
	{
		var status = $(this).val() !== 'logged_in';

		$('#roles').prop('disabled', status);
	};

	// Navigation
	Extension.Form.Navigation = function()
	{
		var menuId = $(this).val();

		$('[data-menu-parent]').addClass('hide');

		$('[data-menu-parent="' + menuId + '"]').removeClass('hide');
	};

	// Storage Type
	Extension.Form.Storage = function()
	{
		var value = $(this).val();

		$('[data-type]').addClass('hide');

		$('[data-type="' + value + '"]').removeClass('hide');

		$((value == 'filesystem' ? '#file' : '#value')).attr('required', true);

		$((value == 'filesystem' ? '#value' : '#file')).removeAttr('required');
	};

	// Initialize Bootstrap Popovers
	Extension.Form.selectize = function ()
	{
		$('select').selectize({
			create: false,
			sortField: 'text'
		});
	};

	// Job done, lets run.
	Extension.Form.init();

})(window, document, jQuery);
