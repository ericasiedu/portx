<?php
namespace Api;
session_start();

use Lib\ACL,
    Lib\MyTransactionQuery,
    Lib\Respond,
    DataTables\Bootstrap,
    DataTables\Editor,
    DataTables\Editor\Field,
    DataTables\Editor\Validate,
    DataTables\Editor\ValidateOptions,
    Lib\MyQuery;

class UserGroup{

    private $request;

    public function  __construct($request)
    {
        $this->request = $request;

    }

    function get_objects(){
        ACl::verifyRead("group-permissions");
        $query = new MyTransactionQuery();
        $query->query("select name from system_object");
        $query->run();
        $system_object = $query->fetch_all();

        $user_id = $_SESSION['id'];
        $query->query("select user_group.id from user inner join user_group
                on user_group.id = user.grp where user.id =?");
        $query->bind = array('i',&$user_id);
        $query->run();
        $user_group = $query->fetch_assoc();
        $query->commit();

        $permission = new ACL();
        $perms = $permission->getUserPermissions($user_group['id']);

        $result = array('sobj'=>$system_object,'uid'=>$user_group['id'],'perms'=>$perms);

        new Respond(216,$result);
    }

    function get_users_group(){
        $user_id = $this->request->param('gpid');
        $permission = new ACL();
        $result = $permission->getUserPermissions($user_id);
        new Respond(216,$result);
    }

    function edit_permission() {
         ACl::verifyUpdate("group-permissions");
        $user_id = $this->request->param('gpid');
        $perms = json_decode($this->request->param('perms'));


        foreach ($perms as $checked_list) {
            $permissions = explode(",", $checked_list);

            $query = new MyTransactionQuery();
            $query->query("select id from system_object where name = ?");
            $query->bind = array('s', &$permissions[0]);
            $query->run();
            $result = $query->fetch_assoc();
            $menu_name = $result['id'];

            $query->query("select * from user_group_acl where group_id = ? and system_object_id = ?");
            $query->bind = array('ii', &$user_id, &$menu_name);
            $query->run();
            $group_acl = $query->fetch_num();

            if ($group_acl > 0) {
                $query->query("update user_group_acl set r = ? ,c = ?,u = ?,d = ? 
                  where group_id = ? and system_object_id = ?");
                $query->bind =  array('iiiiii', &$permissions[1], &$permissions[2],&$permissions[3], &$permissions[4], &$user_id, &$menu_name);
                $query->run();
            } else {
                $query->query("replace into user_group_acl(group_id,system_object_id,c,r,u,d)
             values(?,?,?,?,?,?)");
                $query->bind =  array('iiiiii', &$user_id, &$menu_name, &$permissions[1], &$permissions[2],&$permissions[3], &$permissions[4]);
                $query->run();
            }

            $query->commit();

        }
    }

    function table()
    {
        $db = new Bootstrap();
        $db = $db->database();

        Editor::inst($db, 'user_group')
            ->fields(
                Field::inst('name')
                    ->setFormatter(function($val) {
                        return ucwords($val);
                    })
                    ->validator(Validate::maxLen( 100,ValidateOptions::inst()
                        ->message('Maximum length for field exceeded')
                    ))
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('A reference is required')
                    ))
                    ->validator(function ( $val, $data, $field, $host ) {
                        $val = html_entity_decode($val);
                        $query=new MyQuery();
                        $query->query("select id from user_group where name = ?");
                        $query->bind = array('s', &$val);
                        $run=$query->run();
                        if ($run->num_rows()){
                            $id = $run->fetch_num()[0];
                            if($id == $host['id']){
                                return true;
                            }
                            else {
                                return "User Group already exists";
                            }
                        }
                        else{
                            $val = htmlspecialchars($val);
                            $query=new MyQuery();
                            $query->query("select id from user_group where name = ?");
                            $query->bind = array('s', &$val);
                            $run=$query->run();
                            if ($run->num_rows()){
                                $id = $run->fetch_num()[0];
                                if($id == $host['id']){
                                    return true;
                                }
                                else {
                                    return "User Group already exists";
                                }
                            }
                            else{
                                return true;
                            }
                        }
                    }),
                Field::inst('status')
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('A reference is required')
                    )),
                Field::inst('deleted')
            )
            ->on('preCreate', function ($editor,$values,$system_object='groups'){
                ACl::verifyCreate($system_object);
            })
            ->on('preGet', function ($editor,$id,$system_object='groups'){
                ACl::verifyRead($system_object);
            })
            ->on('preEdit', function ($editor,$id,$values,$system_object='groups'){
                ACl::verifyUpdate($system_object);
            })
            ->on('preRemove', function ($editor,$id,$values,$system_object='groups'){
                ACl::verifyDelete($system_object);
            })
            ->process($_POST)
            ->json();
    }

}

?>