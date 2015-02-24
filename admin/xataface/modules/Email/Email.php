<?php
class modules_Email {

	/**
	 * @brief The base URL to the datepicker module.  This will be correct whether it is in the 
	 * application modules directory or the xataface modules directory.
	 *
	 * @see getBaseURL()
	 */
	private $baseURL = null;
	/**
	 * @brief Returns the base URL to this module's directory.  Useful for including
	 * Javascripts and CSS.
	 *
	 */
	public function getBaseURL(){
		if ( !isset($this->baseURL) ){
			$this->baseURL = Dataface_ModuleTool::getInstance()->getModuleURL(__FILE__);
		}
		return $this->baseURL;
	}


	function __construct(){
		$base = 'xataface__email_';
		$tables = array(
			$base.'templates',
			$base.'newsletters',
			$base.'log',
			$base.'attachments',
			'dataface__email_blacklist'
		);
		
		$dirpath = dirname(__FILE__);
		foreach ($tables as $table){
			Dataface_Table::setBasePath($table, $dirpath);
		}
		
		Dataface_Application::getInstance()->_conf['_allowed_tables']['email module'] = '/^xataface__email_/';
		Dataface_Application::getInstance()->_conf['_allowed_tables']['email blacklist'] = '/^dataface__email_blacklist$/';
		
	}
	
	
	private $pathsAdded = false;
	public function addPaths(){
		if ( !$this->pathsAdded ){
			$this->pathsAdded = true;
			Dataface_JavascriptTool::getInstance()->addPath(
				dirname(__FILE__).'/js',
				$this->getBaseURL().'/js'
			);
			Dataface_CSSTool::getInstance()->addPath(
				dirname(__FILE__).'/css',
				$this->getBaseURL().'/css'
			);
			df_register_skin('email', dirname(__FILE__).'/templates');
			
		}
		
	}
	
	
}