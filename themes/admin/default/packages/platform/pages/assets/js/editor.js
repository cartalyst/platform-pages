(function($) {

	// Redactor buttons
	var buttons = [
		'html',
		'|', 'formatting',
		'|', 'bold', 'italic', 'deleted',
		'|', 'unorderedlist', 'orderedlist', 'outdent', 'indent',
		'|', 'video', 'file', 'table', 'link',
		'|', 'fontcolor', 'backcolor',
		'|', 'alignment',
		'|', 'horizontalrule'
	];

	// Instantiate redactor
	$('textarea#value').redactor({
		minHeight: 300,
		convertDivs: false,
		tabindex: 4,
		buttons: buttons
	});

})(jQuery);
