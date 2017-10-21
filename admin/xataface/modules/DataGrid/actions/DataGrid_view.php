<?php
//import('Services/JSON.php');

class actions_DataGrid_view {
	function handle(&$params){
		$app =& Dataface_Application::getInstance();
		$query =& $app->getQuery();
	
		// We need to load the current data grid from the database.
		// Its id is provided in the -gridid request parameter.
		
		
		
		$mt =& Dataface_ModuleTool::getInstance();
		$mod =& $mt->loadModule('modules_DataGrid');
		if ( PEAR::isError($mod) ) return $mod;

		if ( !@$query['-gridid'] ){
			// No grid was specified.. so we will just take the first grid
			$grids = $mod->getDataGrids();
			if ( !$grids ){
				// No grids were found.  We need to create one
				$table =& Dataface_Table::loadTable($query['-table']);
				$grid = $mod->createDataGrid($query['-table'].' default grid', $query['-table'], array_keys($table->fields()));
				$res = $mod->saveDataGrid($grid);
				if ( PEAR::isError($res) ) return $res;
				$dataGrid =& $grid;
			} else {
				$dataGrid = $grids[0];
			}
		}
		
		if ( !@$dataGrid ) $dataGrid =& $mod->getDataGrid($query['-gridid']);
		if ( !$dataGrid ) return PEAR::raiseError("Error, the specified data grid could not be found");
		
		if ( PEAR::isError($dataGrid) ) return $dataGrid;

		//$json = new Services_JSON;
		$jsonFieldDefs = json_encode($dataGrid->getFieldDefs(true));
		$mod->registerSkin();
				
		//echo "here";exit;
		// Find other grids for this table
		$res = mysql_query(sprintf("select gridID, gridName from dataface__DataGrids where `tableName`='%s'", addslashes($query['-table'])), df_db());
		if ( !$res ){
			throw new Exception(mysql_query(df_db()));
		}
		
		$grids = array();
		while ( $row = mysql_fetch_assoc($res) ){
		
			$url = $app->url('');
			if ( preg_match('/-gridid=[^&]+/', $url) ){
				$url = preg_replace('/-gridid=[^&]+/', '-gridid='.$row['gridID'], $url);
			} else {
				$url .= '&-gridid='.$row['gridID'];
			}
			$row['url'] = $url;
			
			$row['selected'] = '';
			if ( $dataGrid->id == $row['gridID'] ){
				$row['selected'] = 'selected';
			}
			
			$grids[] = $row;
		}
		@mysql_free_result($res);
		
		$app->addHeadContent('<style type="text/css">'.file_get_contents(dirname(__FILE__).'/../css/DataGrid_view.css').'</style>');
		$app->addHeadContent('<script>'.file_get_contents(dirname(__FILE__).'/../js/DataGrid_view.js').'</script>');
		df_display(
			array(
				'DataGrid_base'=>$mod->getBaseURL(), 
				'grids'=>$grids, 
				'grid'=>$dataGrid, 
				'fieldDefs' => $jsonFieldDefs,
				'json' => $this
			), 
			'DataGrid/view.html'
		);
	}
	
	function encode($data){
		return json_encode($data);
	}

}