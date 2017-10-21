<?php
class modules_bootstrap {

	/**
	 * @brief The base URL to the depselect module.  This will be correct whether it is in the 
	 * application modules directory or the xataface modules directory.
	 *
	 * @see getBaseURL()
	 */
	private $baseURL = null;
	private $pathsRegistered = false;
	
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
		$app = Dataface_Application::getInstance();
		
	}
	
	function registerPaths(){
		if ( !$this->pathsRegistered ){
			$this->pathsRegistered = true;
			df_register_skin('bootstrap', dirname(__FILE__).'/templates');
			
			$jt = Dataface_JavascriptTool::getInstance();
			//$jt->addPath(dirname(__FILE__).'/js', $this->getBaseURL().'/js');
			
			$ct = Dataface_CSSTool::getInstance();
			//$ct->addPath(dirname(__FILE__).'/css', $this->getBaseURL().'/css');
			
			$app = Dataface_Application::getInstance();
			$jsUrl = $this->getBaseUrl().'/js/bootstrap/bootstrap.min.js';
			$cssUrl = $this->getBaseUrl().'/css/bootstrap/bootstrap.min.css';
			$jqueryUrl = DATAFACE_URL.'/js/jquery.packed.js';
			
			$app->addHeadContent(sprintf('
				<link rel="stylesheet" 
					  type="text/css"
					  href="%s" 
				/>',
				htmlspecialchars($cssUrl)
			));
			
			if ( !$jt->isIgnored('jquery.packed.js') ){
			
				$app->addHeadContent(sprintf(
					'<script src="%s"></script>',
					htmlspecialchars($jqueryUrl)
				));
			}
			$app->addHeadContent(sprintf(
				'<script src="%s"></script>',
				htmlspecialchars($jsUrl)
			));
			
			$jt->ignore('jquery.packed.js');
			$jt->ignore('bootstrap/bootstrap.min.js');
			$ct->ignore('bootstrap/bootstrap.min.css');
			
		}
	}
}