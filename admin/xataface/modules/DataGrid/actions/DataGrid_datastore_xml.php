<?php
class actions_DataGrid_datastore_xml {
	
	function handle(&$params){
		$app =& Dataface_Application::getInstance();
		$query =& $app->getQuery();
		
		$mt =& Dataface_ModuleTool::getInstance();
		$mod =& $mt->loadModule('modules_DataGrid');
		if ( PEAR::isError($mod) ) return $mod;
		
		$dataGrid = $mod->getDataGrid($query['-gridid']);
		if ( !$dataGrid ) return PEAR::raiseError("Error, the specified data grid could not be found");
		
		import('Dataface/XMLTool.php');
		$xmlTool = new Dataface_XMLTool();
		
		$records = df_get_records_array($query['-table'], $query, null, null, false);
		
		$rows = array();
		$fieldDefs = $dataGrid->getFieldDefs();
		foreach ($records as $record){
			if ( !$record->checkPermission('view') ){
				continue;
			}

			$row = array();
			$row['__recordID__'] = $record->getId();

			foreach ($fieldDefs as $colName => $fieldDef ){
				$permView = false;
				$permEdit = false;
				
				
				
				if ( strpos($colName,'#') === false ){
					// No index was provided so index is 0
					$index = 0;
					$fieldName = $colName;
				} else {
					list($fieldName, $index) = explode('#', $colName);
				}
				
				if ( strpos($fieldName, '.') === false ){
					$permView = $record->checkPermission('view', array('field'=>$fieldName));
					$permEdit = $record->checkPermission('edit', array('field'=>$fieldName));
					$grafted = $record->table()->graftedFields();
					if ( isset($grafted[$fieldName]) ){
						$permEdit = false;
					}
				
				} else {
					list($relname, $fname) = explode('.', $fieldName);
					$rrec = $record->getRelatedRecord($relname, $index);
					if ( $rrec ){
						$permView = $rrec->checkPermission('view', array('field'=>$fname));
						$permEdit = $rrec->checkPermission('edit', array('field'=>$fname));
						$grafted = $rrec->toRecord()->table()->graftedFields();
						if ( isset($grafted[$fname]) ){
							$permEdit = false;
						}
					}
					unset($rrec);
				}
				
				$encColName = str_replace('.','-',$colName);
				$colVal = $xmlTool->xmlentities($record->strval( $fieldName, $index));
				if ( !$permView ){
					$colVal = '';
				}
				$row[ $encColName ] = $colVal;
				$row[ $encColName.'__view' ] = $permView;
				$row[ $encColName.'__edit' ] = $permEdit;
			}
			$rows[] = $row;
			unset($record);
			
			
		}
		if ( @$_GET['--format'] == 'csv' ){
			import('actions/export_csv.php');
			$temp = tmpfile();
			$headings = array();
			if ( $rows ){
				foreach ($fieldDefs as $key=>$val){
					$headings[] = $val['widget']['label'];
					
				}
			}
			fputcsv($temp, $headings,",",'"');
			foreach ($rows as $row){
				$thisrow = array();
				foreach ($row as $key=>$val){
					if ( $key != '__recordID__' ){
						$thisrow[] = $val;
					}
				}
				fputcsv($temp, $thisrow,",",'"');
			}
			
			fseek($temp,0);
			header("Content-type: text/csv; charset=".$app->_conf['oe']);
			header('Content-disposition: attachment; filename="'.$query['-table'].'_'.$dataGrid->name.'_export_'.date('Y_m_d_H_i_s').'.csv"');
		
			$fstats = fstat($temp);
		
			echo fread($temp, $fstats['size']);
			fclose($temp);
			
		} else {
			header("Content-type: application/xml; charset=".$app->_conf['oe']);
			//df_register_skin('DataGrid', DATAFACE_PATH.'/modules/DataGrid/templates');
			$mod->registerSkin();
			df_display(array('rows'=>&$rows), 'DataGrid/datastore.xml');
		}
		exit;
		
	}
}