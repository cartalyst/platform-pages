jQuery(document).ready(function($) {

	// When the page name changes, we generate the slug
	$(document).on('keyup', '#name', function() {

		$('#slug').val($(this).val().slugify());

	});

	// When the storage type changes
	$(document).on('change', '#type', function() {

		var selectedType = $(this).val();

		$('[data-storage]').addClass('hide');

		$('[data-storage="' + selectedType + '"]').removeClass('hide');

		if (selectedType === 'filesystem')
		{
			$('#file').attr('required', true);
			$('#value').removeAttr('required');
		}
		else if (selectedType === 'database')
		{
			$('#value').attr('required');
			$('#file').removeAttr('required', true);
		}

	});

	// When the page visibility changes
	$(document).on('change', '#visibility', function() {

		if ($(this).val() === 'always')
		{
			$('#groups').prop('disabled', 'disabled');
		}
		else
		{
			$('#groups').prop('disabled', false);
		}

	});

	// When the user selects a menu
	$('#menu').on('change', function() {

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
