<section class="panel panel-default panel-help">

	<header class="panel-heading collapsed"  data-toggle="collapse" data-target="#help-body" aria-expanded="false" aria-controls="help-body">

		<h4>

			<i class="fa fa-life-ring" data-toggle="popover" data-content="{{{ trans('common.help.setting') }}}"></i> {{{ trans('common.help.title') }}}

			<span class="panel-close small pull-right" data-toggle="tip" data-original-title="{{{ trans('action.collapse') }}}"></span>

		</h4>

	</header>

	<div class="panel-body collapse" id="help-body">

		<div class="row">

			<div class="col-md-10 col-md-offset-1 help">

				<h2>{{{ trans('platform/pages::common.title') }}}
					<small>
						<a class="manual" href="{{ url('https://cartalyst.com/manual/platform-pages') }}" target="_blank">
							<i class="fa fa-file-text-o fa-sm"></i>
							{{{ trans('common.help.documentation') }}}
						</a>
					</small>
				</h2>

				@content('platform-pages-help', 'platform/pages::content/help.md')

			</div>

		</div>

	</div>

</section>
