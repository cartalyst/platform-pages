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
 * @version    1.0.7
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
		Extension.Form
			.listeners()
			.selectize()
		;
	};

	// Add Listeners
	Extension.Form.listeners = function()
	{
		Platform.Cache.$body
			.on('change', '#type', Extension.Form.storage)
			.on('change', '#menu', Extension.Form.navigation)
			.on('change', '#visibility', Extension.Form.visibility)
		;

		return this;
	};

	// Initialize Selectize
	Extension.Form.selectize = function ()
	{
		$('select:not(#tags)').selectize({
 			create: false
 		});

		$('#tags').selectize({
			create: true, sortField: 'text',
		});

		return this;
	};

	// Navigation
	Extension.Form.navigation = function()
	{
		$('[data-menu-parent]').addClass('hide');

		$('[data-menu-parent="' + $(this).val() + '"]').removeClass('hide');
	};

	// Storage Type
	Extension.Form.storage = function()
	{
		var value = $(this).val();

		$('[data-type]').addClass('hide');

		$('[data-type="' + value + '"]').removeClass('hide');

		$((value == 'filesystem' ? '#file' : '#value')).attr('required', true);

		$((value == 'filesystem' ? '#value' : '#file')).removeAttr('required');
	};

	// Visibility
	Extension.Form.visibility = function()
	{
		if ($(this).val() === 'logged_in')
		{
			$('#roles')[0].selectize.enable();
		}
		else
		{
			$('#roles')[0].selectize.disable();
		}
	};

	// Job done, lets run.
	Extension.Form.init();

})(window, document, jQuery);
