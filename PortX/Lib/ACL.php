<?php

namespace Lib;

class ACL {
    static function canCreate($system_object) {
        return $_SESSION['acl'][$system_object]['create'];
    }
    static function canRead($system_object) {
        return $_SESSION['acl'][$system_object]['read'];
    }
    static function canUpdate($system_object) {
        return $_SESSION['acl'][$system_object]['update'];
    }
    static function canDelete($system_object) {
        return $_SESSION['acl'][$system_object]['delete'];
    }
    static function verifyCreate($system_object) {
        if (ACL::canCreate($system_object) == 0) {
            exit;
        }
    }
    static function verifyRead($system_object) {
        if (ACL::canRead($system_object) == 0) {
            exit;
        }
    }
    static function verifyUpdate($system_object) {
        if (ACL::canUpdate($system_object) == 0) {
            exit;
        }
    }
    static function verifyDelete($system_object) {
        if (ACL::canDelete($system_object) == 0) {
            exit;
        }
    }
    function getUserPermissions($user_id){
        $query=new MyQuery();
        $query->query("SELECT system_object.name as object,c,r,u,d FROM user_group_acl inner join system_object on user_group_acl.system_object_id=system_object.id WHERE group_id=$user_id");
        $query->run();
        $permission = array();
        while($perm=$query->fetch_assoc()) {
            $perm_entry = array($perm['object'] => ['create' => $perm['c'], 'read' => $perm['r'], 'update' => $perm['u'], 'delete' => $perm['d']]);
            if (!$perm_entry){
                $permission=$perm_entry;
            }
            else{
                $permission=array_merge($permission,$perm_entry);
            }
        }
        return $permission;
    }

     function getGroups(){
        $query = new MyQuery();
        $query->query("select id, name from user_group");
        $query->run();
        return $query->fetch_all();
    }
}