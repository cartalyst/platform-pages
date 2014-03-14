jQuery(document).ready(function($)
{
	// When the page name changes, we generate the slug
	$(document).on('keyup', '#name', function()
	{
		$('#slug').val($(this).val().slugify());
	});

	// When the storage type changes
	$(document).on('change', '#type', function()
	{
		var val = $(this).val();

		$('[data-type]').addClass('hide');

		$('[data-type="' + val + '"]').removeClass('hide');

		$((val == 'filesystem' ? '#file' : '#value')).attr('required', true);

		$((val == 'filesystem' ? '#value' : '#file')).removeAttr('required');
	});

	// When the page visibility changes
	$(document).on('change', '#visibility', function()
	{
		var status = $(this).val() === 'always';

		$('#groups').prop('disabled', status);
	});

	// When the user selects a menu
	$('#menu').on('change', function()
	{
		var menuId = $(this).val();

		$('[data-menu-parent]').addClass('hide');

		$('[data-menu-parent="' + menuId + '"]').removeClass('hide');
	});

	// Instantiate the editor
	$('.redactor').redactor({
		toolbarFixed: true,
		minHeight: 200,
	});

	// Validate the form
	H5F.setup(document.getElementById('pages-form'));
});
