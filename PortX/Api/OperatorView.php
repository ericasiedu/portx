<?php
namespace Api;
session_start();

use DateTime;
use
    Lib\ACL,
    Lib\MyQuery,
    Lib\Respond,
    DataTables\Bootstrap,
    DataTables\Editor,
    DataTables\Editor\Field,
    DataTables\Editor\Validate,
    DataTables\Editor\ValidateOptions,
    Lib\MyTransactionQuery,
    PhpOffice\PhpSpreadsheet\Shared\Date;

class OperatorView{
    private $request;

    function __construct($request){
        $this->request = $request;
    }

    function table(){
        $db = new Bootstrap();
        $db = $db->database();
        $query =new MyTransactionQuery();
        Editor::inst($db, 'yard_log')
            ->fields(
                Field::inst('yard_log.container_id as cnum')
                  ->getFormatter(function($val) use($query){
                    $query->query("select number from container where id=?");
                    $query->bind = array('i',&$val);
                    $query->run();
                    $result = $query->fetch_assoc();
                    return $result['number'];
                  }),
                Field::inst('container.trade_type_code as trty')
                    ->getFormatter(function($val){
                        $trade_type = "";
                        switch ($val) {
                            case '11':
                                $trade_type = "IMPORT";
                                break;
                            case '21':
                                $trade_type = "EXPORT";
                                break;
                            case '13':
                                $trade_type = "TRANSIT";
                                break;
                            case '70':
                                $trade_type = "EMPTY";
                                break;
                            default:
                              # co
                                break;
                        }
                        return $trade_type;
                    }),
                Field::inst('yard_log.container_id as cid'),
                Field::inst('yard_log.stack as stk'),
                Field::inst('yard_log.bay as bay'),
                Field::inst('yard_log.row as row'),
                Field::inst('yard_log.tier as tier'),
                Field::inst('yard_log.stack as pos')
                    ->getFormatter(function($val){
                        return  $val;
                    }),
                Field::inst('yard_log.yard_activity as activity'),
                Field::inst('yard_log.id as yard_id'),
                Field::inst('yard_log.date as date'),
                Field::inst('container_isotype_code.length as size')
                    ->getFormatter(function($val){
                        return $val." ft";
                    }),
                Field::inst('container.trade_type_code as emx')
                        ->getFormatter(function($val){
                            $trade_type = "";
                            switch ($val) {
                                case '11':
                                    $trade_type = "IMP";
                                    break;
                                case '21':
                                    $trade_type = "EXP";
                                    break;
                                case '13':
                                    $trade_type = "TRA";
                                    break;
                                case '70':
                                    $trade_type = "EMP";
                                    break;
                                default:
                                    # co
                                    break;
                            }
                            return $trade_type;
                        }),
                Field::inst('yard_log.id as rfs')
                    ->getFormatter(function($val) use ($query){
                        return $val == 1 ? "YES" : "NO";
                    }),
                Field::inst('container.shipping_line_id as opr')
                    ->getFormatter(function($val) use ($query) {
                        $query->query("select code from shipping_line where id  = ?");
                        $query->bind = array('i', &$val);
                        $query->run();
                        $result = $query->fetch_assoc();
                        return substr($result['code'],0,3);
                    }),
                Field::inst('container.shipping_line_id as owr')
                    ->getFormatter(function($val) use ($query) {
                        $query->query("select code from shipping_line where id  = ?");
                        $query->bind = array('i', &$val);
                        $query->run();
                        $result = $query->fetch_assoc();
                        return substr($result['code'],0,3);
                    }),
            )
            ->on('preCreate', function ($editor, $values, $system_object = 'operator-view') {
                ACl::verifyCreate($system_object);
            })
            ->on('preGet', function ($editor, $id, $system_object = 'operator-view') {
                ACl::verifyRead($system_object);
            })
            ->on('preEdit', function ($editor, $id, $values, $system_object = 'operator-view') {
                return false;
            })
            ->on('preRemove', function ($editor, $id, $values, $system_object = 'operator-view') {
                return false;
            })
            ->leftJoin('container', 'container.id', '=', 'yard_log.container_id')
            ->leftJoin('container_isotype_code', 'container_isotype_code.id', '=', 'container.iso_type_code')
            ->where('yard_log.positioned', 0,'=')
            ->process($_POST)
            ->json();
            $query->commit();
    }

    function position_container(){
        $id = $this->request->param('id');
        $position_by = $_SESSION['id'];

        $query = new MyTransactionQuery();
        $query->query("update yard_log set positioned=1,position_by=? where id=?");
        $query->bind = array('ii',&$position_by,&$id);
        $query->run();

        $query->query("select container_id,stack,concat(stack,bay,row,tier) as position from yard_log where id=?");
        $query->bind = array('i',&$id);
        $query->run();
        $result = $query->fetch_assoc();

        $query->query("insert into yard_log_history(container_id,stack,position,yard_activity,user_id)values(?,?,?,'POSITION',?)");
        $query->bind = array('issi',&$result['container_id'],&$result['stack'],&$result['position'],&$position_by);
        $query->run();
        $query->commit();

        new Respond(260);
    }

    function remove_container(){
        $id = $this->request->param('id');
        $position_by = $_SESSION['id'];
        $action_type = $this->request->param('ctyp'); 

        $query = new MyTransactionQuery();
        $query->query("update yard_log set positioned=1,position_by=? where id=?");
        $query->bind = array('ii',&$position_by,&$id);
        $query->run();

        $query->query("select container_id,stack,concat(stack,bay,row,tier) as position from yard_log where id=?");
        $query->bind = array('i',&$id);
        $query->run();
        $result = $query->fetch_assoc();

       

        if ($action_type == "true") {
            $query->query("insert into yard_log_history(container_id,stack,position,yard_activity,user_id)values(?,?,?,'REMOVE',?)");
            $query->bind = array('issi',&$result['container_id'],&$result['stack'],&$result['position'],&$position_by);
            $query->run();
            $query->commit();
        }
        elseif($action_type == "false"){
            $query->query("insert into yard_log_history(container_id,stack,position,yard_activity,user_id)values(?,?,?,'EXAMINATION MOVE',?)");
            $query->bind = array('issi',&$result['container_id'],&$result['stack'],&$result['position'],&$position_by);
            $query->run();
            $query->commit();
        }
        
        new Respond(264);
    }

    function move_truck(){
        $id = $this->request->param('id');
        $move_by = $_SESSION['id'];

        $query = new MyTransactionQuery();
        $query->query("update yard_log set positioned=1,position_by=? where id=?");
        $query->bind = array('ii',&$move_by,&$id);
        $query->run();

        $query->query("select container_id from yard_log where id=?");
        $query->bind = array('i',&$id);
        $query->run();
        $result = $query->fetch_assoc();

        $query->query("insert into yard_log_history(container_id,yard_activity,user_id)values(?,'MOVE',?)");
        $query->bind = array('ii',&$result['container_id'],&$move_by);
        $query->run();
        $query->commit();

        new Respond(265);
    }

  
}

?>