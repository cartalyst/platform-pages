jQuery(document).ready(function($) {

	// When the page name changes, we generate the slug
	$(document).on('keyup', '#name', function() {

		$('#slug').val($(this).val().slugify());

	});

	// When the storage type changes
	$(document).on('change', '#type', function() {

		$('[class^="type"]').addClass('hide');
		$('.type-'+$(this).val()).removeClass('hide');

		if ($(this).val() === 'filesystem')
		{
			$('#file').attr('required', true);
			$('#value').removeAttr('required');
		}
		else if ($(this).val() === 'database')
		{
			$('#value').attr('required');
			$('#file').removeAttr('required', true);
		}

	});

	// When the page visibility changes
	$(document).on('change', '#visibility', function() {

		if ($(this).val() === 'always')
		{
			$('#groups').parent().parent().addClass('hide');
		}
		else
		{
			$('#groups').parent().parent().removeClass('hide');
		}

	});

	// Instantiate the editor
	$('textarea#value').fseditor({
		transition: 'fade',
		overlay: true
	});

});
