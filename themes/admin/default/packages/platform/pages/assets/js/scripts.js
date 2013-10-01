jQuery(document).ready(function($) {

	$('#name').keyup(function() {
		$('#slug').val($(this).val().slugify());
	});

	$('#type').change(function() {
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

	$('#visibility').change(function() {

		if ($(this).val() === 'always')
		{
			$('#groups').parent().parent().addClass('hide');
		}
		else
		{
			$('#groups').parent().parent().removeClass('hide');
		}

	});

	$('textarea').fseditor({
		transition: 'fade',
		overlay: true
	});

});
