<?php
class actions_test_bootstrap_fluid {
	function handle($params){
		Dataface_ModuleTool::getInstance()
			->loadModule('modules_bootstrap')
			->registerPaths();
			
		df_display(array(), 'bootstrap/tests/fluid_test.html');
	}
}