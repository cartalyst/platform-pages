<section class="panel panel-default panel-help">

	<header class="panel-heading">

		<h4>
			<i class="fa fa-life-ring" data-toggle="popover" data-content="{{{ trans('platform/content::common.help.setting') }}}"></i> {{{ trans('platform/content::common.help.title') }}}

			<a class="panel-close small pull-right collapsed tip" data-original-title="{{{ trans('action.collapse') }}}" data-toggle="collapse" href="#help-body" aria-expanded="false" aria-controls="help-body"></a>

			<a class="manual small pull-right" href="{{URL::to('https://cartalyst.com/manual/platform-pages')}}" target="_blank">
				<i class="fa fa-file-text-o fa-sm"></i>
				<span>{{{ trans('platform/content::common.help.documentation') }}}</span>
			</a>

		</h4>

	</header>

	<div class="panel-body collapse" id="help-body">

		<div class="row">

			<div class="col-md-10 col-md-offset-1 help">

				@content('platform-pages-help', 'platform/pages::content/help.md')

			</div>


		</div>

	</div>

</section>
