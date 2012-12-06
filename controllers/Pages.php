<?php namespace Platform\Pages\Controllers;

class Pages extends \Controller {

	public function getIndex()
	{
		echo "Listing Pages Publically";
	}

	public function getEdit($id)
	{
		echo "Editing Page [{$id}]";
		echo <<<FORM
<form method="POST">
	<input type="text" name="input">
	<button type="submit">Submit</button>
</form>
FORM;
	}

	public function postEdit($id)
	{
		echo "Saving Page [{$id}]";
	}

}