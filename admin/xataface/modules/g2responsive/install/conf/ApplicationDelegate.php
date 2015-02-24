<?php

class conf_ApplicationDelegate {

    public function beforeHandleRequest() {

        //action to display dashboard...
        $app = & Dataface_Application::getInstance();
        $query = & $app->getQuery();
        if ($query['-table'] == 'dashboard' and ($query['-action'] == 'browse' or $query['-action'] == 'list')) {
            $query['-action'] = 'dashboard_action';
        }

       /**
        * Returns permissions array.  This method is called every time an action is
        * performed to make sure that the user has permission to perform the action.
        * @param record A Dataface_Record object (may be null) against which we check
        *               permissions.
        * @see Dataface_PermissionsTool
        * @see Dataface_AuthenticationTool
        
       function getPermissions(&$record) {
           $auth = & Dataface_AuthenticationTool::getInstance();
           $user = & $auth->getLoggedInUser();
           if (!isset($user))
               return Dataface_PermissionsTool::NO_ACCESS();
           // if the user is null then nobody is logged in... no access.
           // This will force a login prompt.
           $role = $user->val('Role');
           return Dataface_PermissionsTool::getRolePermissions($role);
           // Returns all of the permissions for the user's current role.
       }
       */
    
    //endof... beforehandlerequest
    }

//endof... class
}
