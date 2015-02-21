<?php
class modules_g2responsive_installer {

    function update_1(){
    
        $settings = parse_ini_file("settings.ini");
        
        if ($settings["dashboard"] == 1){
            $sql[] = "create table if not exists dashboard (
                dashboard_id int(11) not null auto_increment primary key
            )";                            
            df_q($sql);
            copy(dirname(__FILE__).'/install/dashboard/actions.ini', dirname(__FILE__).'/actions.ini');
            copy(dirname(__FILE__).'/install/dashboard/settings.ini', dirname(__FILE__).'/settings.ini');
            
            $filename = $ENV.DATAFACE_SITE_PATH.'/conf/ApplicationDelegate.php';
            $foldername = $ENV.DATAFACE_SITE_PATH.'/conf';
            
            if (!file_exists($foldername)){
                mkdir($foldername, 0777, true);
            }

            if (!file_exists($filename)){
            copy(dirname(__FILE__).'/install/conf/ApplicationDelegate.php', $filename);
            }
            else{}
        }
        else{
            copy(dirname(__FILE__).'/install/nodashboard/actions.ini', dirname(__FILE__).'/actions.ini');
            copy(dirname(__FILE__).'/install/nodashboard/settings.ini', dirname(__FILE__).'/settings.ini');
        }
    }
}