jQuery(document).ready(function($)
{

	$('#type').change(function()
	{
		$('[class^="type"]').addClass('hide');
		$('.type-'+$(this).val()).removeClass('hide');
	});

});
