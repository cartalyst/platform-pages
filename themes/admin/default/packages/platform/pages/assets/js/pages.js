jQuery(document).ready(function($) {

	$('#name').keyup(function() {
		$('#slug').val($(this).val().slugify());

		$('#uri').val($(this).val().slugify('/'));
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

	H5F.setup(document.getElementById('page-create-form'))

});
