<?php
class modules_g2responsive {
  /**
   * @brief The base URL to the depselect module.  This will be correct whether it is in the
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

  public function __construct(){
    $s = DIRECTORY_SEPARATOR;
    $app = Dataface_Application::getInstance();
    Dataface_Application::getInstance()->registerEventListener('filterTemplateContext', array($this, 'filterTemplateContext'));

    $jt = Dataface_JavascriptTool::getInstance();
    $jt->ignore('bootstrap/bootstrap.min.js');
    $jt->ignore('jquery.packed.js');
    $app->addHeadContent('<script src="'.htmlspecialchars(DATAFACE_URL.'/js/jquery.packed.js').'"></script>');
    $app->addHeadContent('<script src="'.htmlspecialchars($this->getBaseURL().'/js/bootstrap/bootstrap.min.js').'"></script>');

    $app->addHeadContent('<link rel="stylesheet" type="text/css" href="'.htmlspecialchars($this->getBaseURL().'/css/bootstrap/bootstrap.min.css').'"/>');
    $app->addHeadContent('<link rel="stylesheet" type="text/css" href="'.htmlspecialchars($this->getBaseURL().'/css/g2ResponsiveStyles.css').'"/>');

    /*Detect mobile browsers and load separate css for each*/
    /*$useragent=$_SERVER['HTTP_USER_AGENT'];
    if(preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i',$useragent)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',substr($useragent,0,4))){
        $app->addHeadContent('<link rel="stylesheet" type="text/css" href="'.htmlspecialchars($this->getBaseURL().'/css/g2ResponsiveStylesMobile.css').'"/>');
    }
    else{
        $app->addHeadContent('<link rel="stylesheet" type="text/css" href="'.htmlspecialchars($this->getBaseURL().'/css/g2ResponsiveStylesDesktop.css').'"/>');
    }*/

    $jt->addPath(dirname(__FILE__).$s.'js', $this->getBaseURL().'/js');
    $ct = Dataface_CSSTool::getInstance();
    $ct->addPath(dirname(__FILE__).$s.'css', $this->getBaseURL().'/css');

    df_register_skin('g2responsive', dirname(__FILE__).'/templates');

    $settings = parse_ini_file("settings.ini");

    if ($settings["installed"] == 0){
      $sql[] = "drop table if exists dataface__modules";
      df_q($sql);
    }

  }

  	public function filterTemplateContext($event){

		$event->context['G2'] = $this;
	}

  function block__head() {
    echo "<meta name='viewport' content='width=640, initial-scale=1.0'>";
  }

  function block__before_search(){
    df_display(array(), 'mobile_buttons.html');
  }

  function block__before_fineprint(){
    df_display(array(), 'navbar_bottom.html');
  }


}