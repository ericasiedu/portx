<?php

namespace Api;

use
    Lib\ACL,
    Lib\MyQuery,
    Lib\MyTransactionQuery,
    DataTables\Bootstrap,
    DataTables\Editor,
    DataTables\Editor\Field,
    DataTables\Editor\Validate,
    DataTables\Editor\ValidateOptions,
    Lib\Respond;

$system_object='container-records';
class Container{
    private $request;
    private $container_data_id;
    private $container_field_id;
    private $user;

    function __construct($request)
    {
        $this->request = $request;
    }


    public function table(){
        $db=new Bootstrap();
        $db=$db->database();

        $query = new MyTransactionQuery();
        Editor::inst( $db, 'container' )
            ->fields(
                Field::inst('container.trade_type_code')
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('Empty Field')
                    )),
                Field::inst('container.number')
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('Empty Field')
                    ))
                    ->validator(function ( $val, $data, $field, $host ) use ($query) {
                        $val = preg_replace("/\s+/", "", $val);
                        if(!ctype_alnum($val)) {
                            return "Container number must not contain symbols.";
                        }
                        if (strlen($val)!= 11){
                            return 'Container Number must be 11 characters long, excluding spaces';
                        }

                        if ($host['action'] == 'create'){
                            $query->query("select id from container where gate_status != 'GATED OUT' and number = ?");
                            $query->bind = array('s', &$val);
                            $run = $query->run();
                            $result = $run->fetch_assoc();
                            if ($result['id']){
                                return 'Container already exist';
                            }
                            else{
                                return true;
                            }
                        }
                        else{
                            return true;
                        }
                      })
                    -> setFormatter(function($val, $data, $field) {
                        return preg_replace("/\s+/", "",$val);
                    }),
                Field::inst('container.bl_number')
                   ->validator(function ($val, $data, $field, $host){
                       if ($val == '' && $data['container']['trade_type_code'] == '11'){
                           return "Bl Number field cannot be empty";
                       }
                       $val = preg_replace("/\s+/", "", $val);
                       $container_number = $data['container']['number'];
                       $query = new MyQuery();
                       $query->query("select id from container where number=? and bl_number=?");
                       $query->bind = array('ss',&$container_number,&$val);
                       $query->run();

                       if($val != "" && !ctype_alnum($val)) {
                           return "BL number must not contain symbols.";
                       }
                       elseif($host['action'] == 'create'){
                        if($query->num_rows() > 0){
                            return "BL number exist already for $container_number";
                        }
                        else{
                            return true;
                        }
                       }
                       else{
                           return true;
                       }
                      
                   }),
                Field::inst('container.book_number')
                ->validator(function ($val, $data, $field, $host){
                    if ($val == '' && $data['container']['trade_type_code'] == '21'){
                        return "Booking Number field cannot be empty";
                    }
                    $val = preg_replace("/\s+/", "", $val);
                    if($val != "" && !ctype_alnum($val)) {
                        return "Booking number must not contain symbols.";
                    }
                    else{
                        return true;
                    }
                }),
                Field::inst('container.seal_number_1')
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('Empty Field')
                    ))
                    ->validator(function($val){
                        $val = preg_replace("/\s+/", "", $val);
                        if($val != "" && !ctype_alnum($val)) {
                            return "Seal number 1 must not contain symbols.";
                        }
                        else{
                            return true;
                        }
                    }),
                Field::inst('container.seal_number_2')
                ->validator(function ($val, $data, $field, $host){
                    if ($val == '' && $data['container']['trade_type_code'] == '21'){
                        return "Empty Field";
                    }
                    $val = preg_replace("/\s+/", "", $val);
                    if($val != "" && !ctype_alnum($val)) {
                        return "Seal number 2 must not contain symbols.";
                    }
                    else{
                        return true;
                    }
                }),
                Field::inst('container.iso_type_code')
                    ->setFormatter(function($val) use ($query) {
                        $query->query("select id from container_isotype_code where code = ?");
                        $query->bind = array('s', &$val);
                        $run=$query->run();
                        return $run->fetch_num()[0] ?? '';
                    })
                    ->getFormatter(function($val) use ($query) {
                        $query->query("select code from container_isotype_code where id  = ?");
                        $query->bind = array('i', &$val);
                        $run=$query->run();
                        return $run->fetch_all()[0] ?? '';
                    })
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('Empty Field')
                    ))
                    ->validator(function ( $val, $data, $field, $host ) use ($query) {
                        $query->query("select id from container_isotype_code where code  = ?");
                        $query->bind = array('s', &$val);
                        $run=$query->run();
                        $result = $run->fetch_assoc();
                        if(!$result['id']){
                            return 'Container Type Does Not Exist';
                        }

                        if ($host['action'] == 'create') {
                            $container_number = $data['container']['number'];
                            $query->query("select id,iso_type_code from container where number=? limit 1");
                            $query->bind = array('s',&$container_number);
                            $query->run();
                            $result_query = $query->fetch_assoc();
                            if ($result_query['id']) {
                                $iso_code = $this->container_iso_type($query,$result_query['iso_type_code']);

                                if ($val != $iso_code) {
                                    return "Container ISO Code is ".$iso_code."";
                                }
                                else {
                                    return true;
                                } 
                            }
                            else{
                              return true;
                            }

                            
                        }

                        if ($host['action'] == 'edit') {
                            $container_number = $data['container']['number'];
                            $query->query("select id,iso_type_code from container where number=?");
                            $query->bind = array('s',&$container_number);
                            $query->run();

                            if ($query->num_rows() > 1) {
                                $result_query = $query->fetch_assoc();
                                $iso_code = $this->container_iso_type($query,$result_query['iso_type_code']);
                                if ($val != $iso_code) {
                                    return "Container ISO Code is ".$iso_code."";
                                }
                                else {
                                    return true;
                                } 
                            }
                            else{
                                $query->query("select id from invoice_container where container_id=?");
                                $query->bind = array('i',&$data['container']['id']);
                                $invoice_query = $query->run();
                                $invoice_result = $invoice_query->fetch_assoc();
    
                                if ($invoice_result['id']) {
                                    return "Cannot edit ISO type Container because container has been invoiced";
                                }
                                else{
                                    return true;
                                }
                            }
                        }
                        return true;
                   
                    }),
                Field::inst('container.soc_status')
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('Empty Field')
                    )),
                Field::inst('container.voyage')
                    ->setFormatter(function($val) use ($query) {
                        $val = html_entity_decode($val);
                        $query->query("select id from voyage where reference = ?");
                        $query->bind = array('s', &$val);
                        $run=$query->run();
                        if(!$run->num_rows()){
                            $val = htmlspecialchars($val);
                            $query->query("select id from voyage where reference = ?");
                            $query->bind = array('s', &$val);
                            $run=$query->run();
                        }
                        return $run->fetch_num()[0] ?? '';
                    })
                    ->getFormatter(function($val) use ($query) {
                        $query->query("select reference from voyage where id = ?");
                        $query->bind = array('i', &$val);
                        $run=$query->run();
                        return $run->fetch_num()[0] ?? '';
                    })
                    ->validator(function ($val, $data, $field, $host) use ($query){
                        if ($data['container']['trade_type_code'] == '21'){
                            return true;
                        }
                        if(empty($val))
                            return "Empty Field";

                        $val = html_entity_decode($val);
                        $query->query("SELECT voyage.reference FROM voyage INNER JOIN vessel ON 
                        vessel.id = voyage.vessel_id WHERE vessel.name = 'Export Vessel'");
                        $query->bind = array();
                        $run=$query->run();
                        $result = $run->fetch_assoc();
                        $voyage_id = $result['reference'];
                        $trade_type = $data['container']['trade_type_code'];
                        if ($val != $voyage_id && $trade_type == 21){
                            return "There is no voyage for export";
                        }
                        else{
                            $query->query("SELECT id FROM voyage WHERE reference = ?");
                            $query->bind = array('s', &$val);
                            $run=$query->run();

                            if($run->num_rows() == 0){
                                $val = htmlspecialchars($val);
                                $query->query("SELECT id FROM voyage WHERE reference = ?");
                                $query->bind = array('s', &$val);
                                $run=$query->run();

                                if($run->num_rows() == 0){
                                    return 'Voyage does not exist';
                                }
                                else {
                                    return true;
                                }                            }
                            else {
                                return true;
                            }
                        }
                    }),
                Field::inst('container.shipping_line_id')
                    ->setFormatter(function($val) use ($query) {
                        $val = html_entity_decode($val);
                        $query->query("select id from shipping_line where name = ?");
                        $query->bind = array('s', &$val);
                        $run=$query->run();
                        if(!$run->num_rows()) {
                            $val = htmlspecialchars($val);
                            $query->query("select id from shipping_line where name = ?");
                            $query->bind = array('s', &$val);
                            $run=$query->run();
                        }
                        return $run->fetch_num()[0] ?? '';
                    })
                    ->getFormatter(function($val) use ($query) {
                        $query->query("select name from shipping_line where id  = ?");
                        $query->bind = array('i', &$val);
                        $run=$query->run();
                        return $run->fetch_all()[0] ?? '';
                    })
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('Empty Field')
                    ))
                    ->validator(function ( $val, $data, $field, $host ) use ($query) {
                        $val = html_entity_decode($val);
                        $query->query("select id from shipping_line where name  = ?");
                        $query->bind = array('s', &$val);
                        $run=$query->run();
                        if(!$run->num_rows()){
                            $val = htmlspecialchars($val);
                            $query->query("select id from shipping_line where name  = ?");
                            $query->bind = array('s', &$val);
                            $run=$query->run();
                        }
                        return ($run->fetch_num()[0]) ? true: 'Shipping Line Not Registered';
                    }),
                Field::inst('container.agency_id')
                    ->setFormatter(function($val) use ($query) {
                        $val = html_entity_decode($val);
                        $query->query("select id from agency where name = ?");
                        $query->bind = array('s', &$val);
                        $run=$query->run();
                        if(!$run->num_rows()){
                            $val = htmlspecialchars($val);
                            $query->query("select id from agency where name = ?");
                            $query->bind = array('s', &$val);
                            $run=$query->run();
                        }
                        return $run->fetch_num()[0] ?? '';
                    })
                    ->getFormatter(function($val) use ($query) {
                        $query->query("select name from agency where id  = ?");
                        $query->bind = array('i', &$val);
                        $run=$query->run();
                        return $run->fetch_all()[0] ?? '';
                    })
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('Empty Field')
                    ))
                    ->validator(function ( $val, $data, $field, $host ) use ($query) {
                        $val = html_entity_decode($val);
                        $query->query("select id from agency where name  = ?");
                        $query->bind = array('s', &$val);
                        $run=$query->run();
                        if(!$run->num_rows()){
                            $val = htmlspecialchars($val);
                            $query->query("select id from agency where name  = ?");
                            $query->bind = array('s', &$val);
                            $run=$query->run();
                        }
                        return ($run->fetch_num()[0]) ? true: 'Agent Not Registered';
                    }),
                Field::inst('container.pol')
                    ->setFormatter(function($val) use ($query) {
                        $query->query("select id from port where code = ?");
                        $query->bind = array('s', &$val);
                        $run=$query->run();
                        return $run->fetch_num()[0] ?? '';
                    })
                    ->getFormatter(function($val) use ($query) {
                        $query->query("select code from port where id  = ?");
                        $query->bind = array('i', &$val);
                        $run=$query->run();
                        return $run->fetch_all()[0] ?? '';
                    })
                    ->validator(function ( $val, $data, $field, $host ) use ($query) {
                        $query->query("select id from port where code  = ?");
                        $query->bind = array('s', &$val);
                        $run=$query->run();
                        if ($val == '' || $run->fetch_num()[0]){
                            return true;
                        }
                        else{
                            return 'POL Does Not Exist';
                        }
                    }),
                Field::inst('container.pod')
                    ->setFormatter(function($val) use ($query) {
                        $query->query("select id from port where code = ?");
                        $query->bind = array('s', &$val);
                        $run=$query->run();
                        return $run->fetch_num()[0] ?? '';
                    })
                    ->getFormatter(function($val) use ($query) {
                        $query->query("select code from port where id  = ?");
                        $query->bind = array('i', &$val);
                        $run=$query->run();
                        return $run->fetch_all()[0] ?? '';
                    })
                    ->validator(function ( $val, $data, $field, $host ) use ($query) {
                        $query->query("select id from port where code  = ?");
                        $query->bind = array('s', &$val);
                        $run=$query->run();
                        if ($val == '' || $run->fetch_num()[0]){
                            return true;
                        }
                        else{
                            return 'POD Does Not Exist';
                        }

                    }),
                Field::inst('container.fpod'),
                Field::inst('container.tonnage_weight_metric'),
                Field::inst('container.tonnage_freight'),
                Field::inst('container.content_of_goods')
                    ->setFormatter(function($val){
                        return trim($val,"=");
                    })
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('Empty Field')
                    )),
                Field::inst('container.importer_address')
                    ->setFormatter(function($val){
                        return trim($val,"=");
                    })
                ->validator(function ($val, $data, $field, $host){
                    if ($val == '' && $data['container']['trade_type_code'] == '11'){
                        return "Importer address cannot be empty";
                    }
                    else{
                        return true;
                    }
                }),
                Field::inst('container.imdg_code_id')
                    ->setFormatter(function($val) use ($query) {
                        $query->query("select id from imdg where name = ?");
                        $query->bind = array('s', &$val);
                        $run=$query->run();
                        return $run->fetch_num()[0] ?? '';
                    })
                    ->getFormatter(function($val) use ($query) {
                        $query->query("select name from imdg where id  = ?");
                        $query->bind = array('i', &$val);
                        $run=$query->run();
                        return $run->fetch_all()[0] ?? '';
                    })
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('Empty Field')
                    ))
                    ->validator(function ( $val, $data, $field, $host ) use ($query) {
                        $query->query("select id from imdg where name  = ?");
                        $query->bind = array('s', &$val);
                        $run=$query->run();
                        return ($run->fetch_num()[0]) ? true: 'IMDG Code Does Not Exist';
                    }),
                Field::inst('container.oog_status')
                    ->setFormatter(function($val) {
                        return $val == "NO" ? 0 : 1;
                    })
                    ->getFormatter(function($val) {
                        return $val == 1 ? "YES" : "NO";
                    })
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('Empty Field')
                    )),
                Field::inst('container.full_status')
                    ->setFormatter(function($val) {
                        return $val == "NO" ? 0 : 1;
                    })
                    ->getFormatter(function($val) {
                        return $val == 1 ? "YES" : "NO";
                    })
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('Empty Field')
                    )),
                Field::inst('container.date')
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('Empty Field')
                    )),
                Field::inst('container.gate_status'),
                Field::inst('container.icl_seal_number_1')
                    ->validator(function($val){
                        $val = preg_replace("/\s+/", "", $val);
                        if($val != "" && !ctype_alnum($val)) {
                            return "ICL Seal number 1 must not contain symbols.";
                        }
                        else{
                            return true;
                        }
                    }),
                Field::inst('container.icl_seal_number_2')
                    ->validator(function($val){
                        $val = preg_replace("/\s+/", "", $val);
                        if($val != "" && !ctype_alnum($val)) {
                            return "ICL Seal number 2 must not contain symbols.";
                        }
                        else{
                            return true;
                        }
                    }),

                Field::inst('container.status')
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('Empty Field')
                    )),
                Field::inst('container.id'),
                Field::inst('trade_type.name'),
                Field::inst('container.deleted')
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('Empty Field')
                    ))
            )
            ->on('preCreate', function ($editor,$values,$system_object='container-records'){
                ACl::verifyCreate($system_object);
            })
            ->on('preGet', function ($editor,$id,$system_object='container-records'){
                ACl::verifyRead($system_object);
            })
            ->on('preEdit', function ($editor,$id,$values,$system_object='container-records'){
                ACl::verifyUpdate($system_object);
            })
            ->on('preRemove', function ($editor,$id,$values,$system_object='container-records'){
                ACl::verifyDelete($system_object);
               $query = new MyTransactionQuery();

               $query->query("select id from invoice_container where container_id=?");
               $query->bind = array('i',&$id);
               $query->run();
               $result_query = $query->fetch_assoc();
               if ($result_query['id']) {
                    $query->commit();
                    return false;
               }
               else{
                    $query->query("delete from gate_record where container_id=?");
                    $query->bind = array('i',&$id);
                    $query->run();

                    $query->query("delete from container_log where container_id=?");
                    $query->bind = array('i',&$id);
                    $query->run();

                    $query->query("delete from proforma_container_log where container_id=?");
                    $query->bind = array('i',&$id);
                    $query->run();
                    $query->commit();
               }
            })
            ->leftJoin('trade_type', 'container.trade_type_code', '=', 'trade_type.code')
            ->where("container.gate_status","GATED OUT","!=")
            ->process($_POST)
            ->json();
            $query->commit();
    }

    public function type_codes_table()
    {
        $db = new Bootstrap();
        $db = $db->database();
        $query=new MyTransactionQuery();

        Editor::inst($db, 'container_isotype_code')
            ->fields(
                Field::inst('code')
                    ->setFormatter(function($val) {
                        $val = html_entity_decode($val);
                        return mb_convert_case($val, MB_CASE_UPPER);
                    })
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('A reference is required')
                    ))
                    ->validator(Validate::maxLen( 30,ValidateOptions::inst()
                        ->message('Maximum length for field exceeded')
                    ))
                    ->validator(function ( $val, $data, $field, $host ) use ($query) {
                        $val = html_entity_decode($val);
                        $query->query("select id from container_isotype_code where code  = ?");
                        $query->bind = array('s', &$val);
                        $run=$query->run();
                        if ($run->num_rows()){
                            $id = $run->fetch_num()[0];
                            if($id == $host['id']){
                                return true;
                            }
                            else {
                                return "ISO Type Code already exists";
                            }
                        }
                        else{
                            $val = htmlspecialchars($val);
                            $query->query("select id from container_isotype_code where code  = ?");
                            $query->bind = array('s', &$val);
                            $run=$query->run();
                            if ($run->num_rows()){
                                $id = $run->fetch_num()[0];
                                if($id == $host['id']){
                                    return true;
                                }
                                else {
                                    return "ISO Type Code already exists";
                                }
                            }
                            else{
                                return true;
                            }
                        }
                    }),
                Field::inst('description')
                    ->setFormatter(function ($val){
                        $val = html_entity_decode($val);
                        return ucwords($val);
                    })
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('A reference is required')
                    ))
                    ->validator(Validate::maxLen( 30,ValidateOptions::inst()
                        ->message('Maximum length for field exceeded')
                    )),
                Field::inst('length')
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('A reference is required')
                    ))
                    ->validator(Validate::maxLen( 30,ValidateOptions::inst()
                        ->message('Maximum length for field exceeded')
                    ))
                    ->validator(Validate::numeric())
                    ->validator(function ( $val, $data, $field, $host ) {
                        return $val <= 53 && $val > 0 ?  true : "Invalid Length";
                    }),
                Field::inst('height')
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('A reference is required')
                    ))
                    ->validator(Validate::maxLen( 30,ValidateOptions::inst()
                        ->message('Maximum length for field exceeded')
                    ))
                    ->validator(Validate::numeric())
                    ->validator(function ( $val, $data, $field, $host ) {
                        return $val <= 10 && $val > 0 ?  true : "Invalid Height";
                    }),
                Field::inst('grp')
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('A reference is required')
                    ))
                    ->validator(Validate::maxLen( 30,ValidateOptions::inst()
                        ->message('Maximum length for field exceeded')
                    ))
            )
            ->on('preCreate', function ($editor,$values,$system_object='udm-container-type-codes'){
                ACl::verifyCreate($system_object);
            })
            ->on('preGet', function ($editor,$id,$system_object='udm-container-type-codes'){
                ACl::verifyRead($system_object);
            })
            ->on('preEdit', function ($editor,$id,$values,$system_object='udm-container-type-codes'){
                ACl::verifyUpdate($system_object);
            })
            ->on('preRemove', function ($editor,$id,$values,$system_object='udm-container-type-codes') use ($query){
                ACl::verifyDelete($system_object);
                $query->query("select id from container where iso_type_code = ?");
                $query->bind=array('i',&$id);
                $run = $query->run();
                if($run->num_rows() > 0){
                    return false;
                }
            })
            ->process($_POST)
            ->json();
    }

    public function get_container_info(){
        $gate_container = $this->request->param('ctid');
        $trade_type = $this->request->param('trty');

        $query=new MyQuery();
        $query->query("SELECT container.bl_number,container.book_number,container.seal_number_1, container.seal_number_2, container.soc_status, 
              voyage.reference, container.full_status, shipping_line.name as name, imdg.name 
              AS iname, container_isotype_code.code,container.gate_status, container.oog_status,trade_type.name as trade FROM container LEFT JOIN shipping_line 
              ON container.shipping_line_id = shipping_line.id LEFT JOIN imdg ON container.imdg_code_id = imdg.id 
              LEFT JOIN container_isotype_code ON container.iso_type_code = container_isotype_code.id LEFT JOIN voyage 
              ON voyage.id = container.voyage LEFT JOIN trade_type ON trade_type.code = container.trade_type_code WHERE container.number = ? AND container.gate_status != 'GATED OUT' ORDER BY container.id DESC");
        $query->bind = array('s', &$gate_container);
        $run=$query->run();
        $result = $run->fetch_assoc();
        echo json_encode($result);
    }

    public function get_container_edit_info(){
        $gate_record = $this->request->param('gid');
        $gate_record_id = (int)$gate_record;

        $query = new MyTransactionQuery();
        $query->query("select container_id from gate_record where id=?");
        $query->bind = array('i',&$gate_record_id);
        $query->run();
        $result = $query->fetch_assoc();
        $container_id = $result['container_id'];

        $query->query("SELECT container.book_number,container.seal_number_1, container.seal_number_2, container.soc_status, 
              voyage.reference, container.full_status, shipping_line.name as name, imdg.name 
              AS iname, container_isotype_code.code,container.gate_status, container.oog_status FROM container LEFT JOIN shipping_line 
              ON container.shipping_line_id = shipping_line.id LEFT JOIN imdg ON container.imdg_code_id = imdg.id 
              LEFT JOIN container_isotype_code ON container.iso_type_code = container_isotype_code.id LEFT JOIN voyage 
              ON voyage.id = container.voyage WHERE container.id = ? ORDER BY container.id DESC");
        $query->bind = array('i', &$container_id);
        $query->run();
        $result = $query->fetch_assoc();
        $query->commit();
        echo json_encode($result);
    }

    public function get_trade_type_info(){
        $gate_record = $this->request->param('gid');
        $gate_record_id = (int)$gate_record;
        $query = new MyTransactionQuery();
        $query->query("select container_id from gate_record where id=?");
        $query->bind = array('i',&$gate_record_id);
        $query->run();
        $query_result = $query->fetch_assoc();

        $query->query("select trade_type_code from container where id=?");
        $query->bind = array('i',&$query_result['container_id']);
        $query->run();
        $result = $query->fetch_assoc();
        echo json_encode($result);
    }

    public function get_invoiced_container(){
        $gate_record = $this->request->param('gid');
        $gate_record_id = (int)$gate_record;
        $query = new MyTransactionQuery();
        $query->query("select container_id from gate_record where id=?");
        $query->bind = array('i',&$gate_record_id);
        $query->run();
        $number_result = $query->fetch_assoc();
        $container_id = $number_result['container_id'];

        $query->query("select id from invoice_container where container_id=?");
        $query->bind = array('i',&$container_id);
        $query->run();
        $result = $query->fetch_assoc();

        $error = array();
        if($result['id']){
            $error['iner'] = 1;
        }
        $query->commit();
        echo json_encode($error);
    }

    public function get_trade_containers(){
        $type = $this->request->param('type');
        $query=new MyQuery();
        $query->query("SELECT number FROM container WHERE status = 0 AND gate_status = ''
                          AND trade_type_code =  ?");
        $query->bind = array('i', &$type);
        $run=$query->run();
        $result = $run->fetch_all();
        echo json_encode($result);
    }

    public function flag() {
        $container_id = $this->request->param('ctid');
        $user_id = $_SESSION['id'];
        $query=new MyTransactionQuery();
        $query->query("UPDATE container SET status = '1' WHERE id = ?");
        $query->bind = array('i', &$container_id);
        $query->run();
        $query->query("INSERT INTO container_log (container_id, activity_id, note, user_id, date)
                          VALUES (?, '3', 'Container Flagged', ?, '" . date('Y-m-d H:i:s') . "')");
        $query->bind = array('ii', &$container_id, &$user_id);
        $query->run();
        $query->commit();
    }

    public function unflag(){
        $container_id = $this->request->param('ctid');
        $user_id = $_SESSION['id'];
        $query=new MyTransactionQuery();
        $query->query("UPDATE container SET status = '0' WHERE id = ?");
        $query->bind = array('i', &$container_id);
        $query->run();
        $query->query("INSERT INTO container_log (container_id, activity_id, note, user_id, date)
                          VALUES (?, '4', 'Container Unflagged', ?, '" . date('Y-m-d H:i:s') . "')");
        $query->bind = array('ii', &$container_id, &$user_id);
        $query->run();
        $query->commit();
    }

    public function get_export_voyage(){
        $query=new MyQuery();
        $query->query("SELECT reference as ref FROM voyage WHERE id = 1");
        $run=$query->run();
        $result = $run->fetch_assoc();
        echo json_encode($result);
    }

    public function get_export_shipping_line(){
        $query=new MyQuery();
        $query->query("SELECT name FROM shipping_line WHERE id = 1");
        $run=$query->run();
        $result = $run->fetch_assoc();
        echo json_encode($result);
    }

    public  function add_info(){
        $container_id = (int)$this->request->param('ctid');

        $query = new MyTransactionQuery();
        $query->query("select id, imdg_code_id from container where trade_type_code = 21 and id = ?");
        $query->bind = array('i', &$container_id);
        $run = $query->run();

        if($run->num_rows()) {
            $run = $run->fetch_assoc();
            $cat = $run['imdg_code_id'];
            $user_id = $_SESSION['id'];
            $info_category = '';
            switch ($cat) {
                case 2:
                case 4:
                case 10:
                    $info_category = 'DG I';
                    break;
                case 3:
                case 5:
                case 6:
                case 7:
                case 8:
                case 9:
                    $info_category = 'DG II';
                    break;
                default:
                    $info_category = 'General Goods';
            }

            $query->query("insert into container_depot_info(container_id,load_status,goods,user_id)values(?,'FCL',?,?)");
            $query->bind = array('isi', &$container_id, &$info_category, &$user_id);
            $query->run();

            $query->query("insert into proforma_container_depot_info(container_id,load_status,goods,user_id)values(?,'FCL',?,?)");
            $query->bind = array('isi', &$container_id, &$info_category, &$user_id);
            $query->run();

            $query->commit();
        }
    }

    public function update_container_seals() {
        $container_number = $this->request->param('ctno');
        $seal_number1 = $this->request->param('seal1');
        $seal_number2 = $this->request->param('seal2');

        $query = new MyTransactionQuery();

        if ($seal_number1 == '' && $seal_number2 == '') {
            $query->query("select icl_seal_number_1, icl_seal_number_2 from container where number = ? and gate_status = ''");
            $query->bind = array('s', &$container_number);
            $run = $query->run();
            $result = $run->fetch_assoc();
            $icl_seal_no1 = $result['icl_seal_number_1'];
            $icl_seal_no2 = $result['icl_seal_number_2'];

            if ($icl_seal_no1 || $icl_seal_no2) {
                $query->query("update container set seal_number_1 = ?, seal_number_2 = ? where number like ? and gate_status = ''");
                $query->bind = array('sss', &$icl_seal_no1, &$icl_seal_no2, &$container_number);
            }
            else {
                return;
            }
        } else {
            $query->query("update container set seal_number_1 = ?, seal_number_2 = ? where number like ? and gate_status = ''");
            $query->bind = array('sss', &$seal_number1, &$seal_number2, &$container_number);
        }
        $query->run();
        $query->commit();
    }

    public function check_container(){
        $number = $this->request->param('number');
        $query = new MyQuery();
        $query->query("select gate_status from container where number = ?");
        $query->bind = array('s', &$number);
        $run = $query->run();
        $result = $run->fetch_assoc();
        echo json_encode($result);
    }

    public function check_number(){
        $container_number = $this->request->param('number');
        $query = new MyQuery();
        $query->query("select id from container where gate_status != 'GATED OUT' and number = ?");
        $query->bind = array('s', &$container_number);
        $res3 = $query->run();
        $result1 = $res3->fetch_assoc();
        if ($result1['id']){
            echo "Container already exist";
        }
    }

    public function add_export_container(){
        $trade_type = $this->request->param('trty');
        $container_number = $this->request->param('ctno');
        $agent = $this->request->param('agnt');
        $content = $this->request->param('ctnt');
        $imdg = $this->request->param('imdg');
        $book_number = $this->request->param('bkno');
        $full_status = $this->request->param('fstat');
        $iso = $this->request->param('iso');
        $oog = $this->request->param('oog');
        $soc = $this->request->param('soc');
        $seal_no1 = $this->request->param('seal1');
        $seal_no2 = $this->request->param('seal2');
        $consignee = htmlentities($this->request->param('cons'));
        $shipping_line = $this->request->param('shid');
        $special_seal = $this->request->param('spel');

        $error = array();

        $query = new MyTransactionQuery();
        $query->query("select id from container_isotype_code where code = ?");
        $query->bind = array('s', &$iso);
        $run = $query->run();
        $result = $run->fetch_assoc();
        $iso_id = $result['id'];

      
            $query->query("select consignee from gate_record left join container on gate_record.container_id = container.id where book_number = ? and consignee != ? and gate_record.type = 'GATE IN'");
            $query->bind = array('ss', &$book_number, &$consignee);
            $run = $query->run();
            if($run->num_rows() > 0) {
                $book_consignee = $run->fetch_assoc()['consignee'];
                $error['cser'] = "A different Shipper has been assigned for another container in this booking number. Consignee : $book_consignee";
            }
        

        $query->query("select id from imdg where name = ?");
        $query->bind = array('s', &$imdg);
        $res1 = $query->run();
        $result1 = $res1->fetch_assoc();
        $imdg_id = $result1['id'];

        $query->query("select id from agency where name = ?");
        $query->bind = array('s', &$agent);
        $res2 = $query->run();
        $result2 = $res2->fetch_assoc();
        $agent_id = $result2['id'];
        if (!$agent_id) {
            $agent = htmlspecialchars($agent);
            $query->query("select id from agency where name = ?");
            $query->bind = array('s', &$agent);
            $res2 = $query->run();
            $result2 = $res2->fetch_assoc();
            $agent_id = $result2['id'];
        }

        $query->query("select id from shipping_line where name = ?");
        $query->bind = array('s', &$shipping_line);
        $res2 = $query->run();
        $result2 = $res2->fetch_assoc();
        $shipping_line_id = $result2['id'];
        if (!$shipping_line_id) {
            $shipping_line = htmlspecialchars($shipping_line);
            $query->query("select id from shipping_line where name = ?");
            $query->bind = array('s', &$shipping_line);
            $res2 = $query->run();
            $result2 = $res2->fetch_assoc();
            $shipping_line_id = $result2['id'];
        }

        $query->query("select id,trade_type_code,status from container where gate_status != 'GATED OUT' and number = ?");
        $query->bind = array('s', &$container_number);
        $res3 = $query->run();
        $result1 = $res3->fetch_assoc();

        $query->query("select id,iso_type_code from container where number=? limit 1");
        $query->bind = array('s', &$container_number);
        $query->run();
        $result_iso_code = $query->fetch_assoc();

        $container_number = preg_replace("/\s+/", "", $container_number);
        $book_number = preg_replace("/\s+/", "", $book_number);
        $seal_no1 = preg_replace("/\s+/", "", $seal_no1);
        $seal_no2 = preg_replace("/\s+/", "", $seal_no2);
        $special_seal = preg_replace("/\s+/", "", $special_seal);

        if ($container_number == '') {
            $error['cnum'] = "empty";
        }
        if (!ctype_alnum($container_number)) {
            $error['cnum'] = "sym";
        } elseif (strlen($container_number) < 11) {
            $error['clens'] = "len";
        } elseif ($result1['id'] != NULL && $result1['trade_type_code'] == '21') {
            $error['cnum1'] = "ex";
        }
        else
        if (!$trade_type) {
            $error['trty'] = 1;
        }
        if (!$agent_id) {
            $error['agnt'] = 1;
        }
        if ($content == '') {
            $error['cnt'] = 1;
        }
        if (!$imdg_id) {
            $error['img'] = 1;
        }
        if ($book_number == '') {
            $error['bnum'] = 1;
        }
        elseif (!ctype_alnum($book_number)) {
            $error['bkn'] = 'bokn';
        }
        if ($full_status == '') {
            $error['fstat'] = 1;
        }
        if (!$iso_id) {
            $error['iso'] = 1;
        }
        if ($oog == '') {
            $error['oog'] = 1;
        }
        if ($soc == '') {
            $error['soc'] = 1;
        }
        if (!$shipping_line_id) {
            $error['sline'] = 1;
        }
        if ($seal_no1 == '') {
            $error['seal1'] = 1;
        }
        elseif (!ctype_alnum($seal_no1)) {
            $error['sea1'] = 'senu1';
        }
        if ($seal_no2 == '') {
            $error['seal2'] = 1;
        }
        elseif (!ctype_alnum($seal_no2)) {
            $error['sea2'] = 'senu2';
        }
        if ($consignee == '') {
            $error['cons'] = 1;
        }
        if($special_seal != '' && !ctype_alnum($special_seal)){
            $error['spesc'] = "spelss";
        }
        if($result1['id']){
            // if($result1['trade_type_code'] != '21'){
            //     $error['trte'] = 'trer';
            // }
           if($result1['status'] == 1){
                $error['flag'] = 'flagged';
           }
        }
        if($result_iso_code['id'] && $iso_id != $result_iso_code['iso_type_code']){
            $query->query("select code from container_isotype_code where id = ?");
            $query->bind = array('i',&$result_iso_code['iso_type_code']);
            $query->run();
            $result3 = $query->fetch_assoc();
            $error['isoer'] = 'iso_er';
            $error['isod'] = $result3['code'];
        }
        
        if(count($error)) {
            new Respond(160,  array("err"=> $error));
        }

        

        $number = preg_replace("/\s+/", "", $container_number);
        $consignee2 = trim($consignee,"=");
        $goods = trim($content,'=');

        if ($result1['id']) {
            if($result1['trade_type_code'] == '11'){
                $query->query("update container set bl_number='', book_number=?,seal_number_1=?,seal_number_2=?,iso_type_code=?,soc_status=?,voyage=1,shipping_line_id=?,agency_id=?,trade_type_code=?,content_of_goods=?,importer_address=?,imdg_code_id=?,oog_status=?,full_status=? where id=?");
                $query->bind = array('ssssisiisssiii',&$book_number, &$seal_no1, &$seal_no2, &$iso_id, &$soc, &$shipping_line_id, &$agent_id, &$trade_type, &$goods, &$consignee2,&$imdg_id, &$oog, &$full_status,&$result1['id']);
                $query->run();
                $query->commit();
                new Respond(260);
            }
        }
        else{
            $query->query("insert into container(number, book_number ,seal_number_1,seal_number_2,iso_type_code,soc_status,voyage,shipping_line_id,agency_id,trade_type_code,content_of_goods,importer_address,imdg_code_id,oog_status,full_status)
            values( ? ,?, ?, ?, ?,?,'1',?,?,?,?, ?,?,?,?)");
            $query->bind = array('ssssisiisssiii', &$number, &$book_number, &$seal_no1, &$seal_no2, &$iso_id, &$soc, &$shipping_line_id, &$agent_id, &$trade_type, &$goods, &$consignee2,
            &$imdg_id, &$oog, &$full_status);
            $res3 = $query->run();

            $last_id = $res3->get_last_id();

            if ($last_id) {
                $user_id = $_SESSION['id'];
                $info_category = '';
                switch ($imdg_id) {
                    case 2:
                    case 4:
                    case 10:
                        $info_category = 'DG I';
                        break;
                    case 3:
                    case 5:
                    case 6:
                    case 7:
                    case 8:
                    case 9:
                        $info_category = 'DG II';
                        break;
                    default:
                        $info_category = 'General Goods';
                    }

                $query->query("insert into container_depot_info(container_id,load_status,goods,user_id)values(?,'FCL',?,?)");
                $query->bind = array('isi', &$last_id, &$info_category, &$user_id);
                $query->run();
                $query->query("insert into proforma_container_depot_info(container_id,load_status,goods,user_id)values(?,'FCL',?,?)");
                $query->bind = array('isi', &$last_id, &$info_category, &$user_id);
                $query->run();
                $query->commit();
                new Respond(260);
            }
        }
       
       
    
        
    }

    public function add_empty_container()
    {
        $trade_type = $this->request->param('trty');
        $container_number = $this->request->param('ctno');
        $agent = $this->request->param('agnt');
        $full_status = $this->request->param('fstat');
        $iso = $this->request->param('iso');
        $oog = $this->request->param('oog');
        $soc = $this->request->param('soc');
        $seal_no1 = $this->request->param('seal1');
        $seal_no2 = $this->request->param('seal2');
        $activity_type = $this->request->param('activity');
        $consignee = htmlentities($this->request->param('cons'));
        $shipping_line = $this->request->param('shid');
        $special_seal = $this->request->param('spel');

        $error = array();

        $query = new MyTransactionQuery();
        $query->query("select id from container_isotype_code where code = ?");
        $query->bind = array('s', &$iso);
        $run = $query->run();
        $result = $run->fetch_assoc();
        $iso_id = $result['id'];

      
            $query->query("SELECT container.number FROM gate_record LEFT JOIN container ON gate_record.container_id = container.id WHERE container.trade_type_code = ? AND container.number = ? AND gate_record.type = 'GATE IN'");
            $query->bind = array('ss', &$trade_type, &$container_number);
            $run = $query->run();
            if($run->num_rows() > 0) {
                $number = $run->fetch_assoc()['number'];
                $error['cser'] = "A container with number [$number] has already been gated in as EMPTY";
            }
        

        $query->query("select id from agency where name = ?");
        $query->bind = array('s', &$agent);
        $res2 = $query->run();
        $result2 = $res2->fetch_assoc();
        $agent_id = $result2['id'];
        if (!$agent_id) {
            $agent = htmlspecialchars($agent);
            $query->query("select id from agency where name = ?");
            $query->bind = array('s', &$agent);
            $res2 = $query->run();
            $result2 = $res2->fetch_assoc();
            $agent_id = $result2['id'];
        }

        $query->query("select id from shipping_line where name = ?");
        $query->bind = array('s', &$shipping_line);
        $res2 = $query->run();
        $result2 = $res2->fetch_assoc();
        $shipping_line_id = $result2['id'];
        if (!$shipping_line_id) {
            $shipping_line = htmlspecialchars($shipping_line);
            $query->query("select id from shipping_line where name = ?");
            $query->bind = array('s', &$shipping_line);
            $res2 = $query->run();
            $result2 = $res2->fetch_assoc();
            $shipping_line_id = $result2['id'];
        }

        $query->query("select id from container where gate_status != 'GATED OUT' and number = ?");
        $query->bind = array('s', &$container_number);
        $res3 = $query->run();
        $result1 = $res3->fetch_assoc();

        $container_number = preg_replace("/\s+/", "", $container_number);
        $seal_no1 = preg_replace("/\s+/", "", $seal_no1);
        $seal_no2 = preg_replace("/\s+/", "", $seal_no2);
        $special_seal = preg_replace("/\s+/", "", $special_seal);

        if ($container_number == '') {
            $error['cnum'] = "empty";
        }
        if (!ctype_alnum($container_number)) {
            $error['cnum'] = "sym";
        } elseif (strlen($container_number) < 11) {
            $error['clens'] = "len";
        } elseif ($result1['id']) {
            $error['cnum1'] = "ex";
        }
        if (!$trade_type) {
            $error['trty'] = 1;
        }
        if (!$agent_id) {
            $error['agnt'] = 1;
        }
       
        if ($full_status == '') {
            $error['fstat'] = 1;
        }
        if (!$iso_id) {
            $error['iso'] = 1;
        }
        if ($oog == '') {
            $error['oog'] = 1;
        }
        if ($soc == '') {
            $error['soc'] = 1;
        }
        if (!$shipping_line_id) {
            $error['sline'] = 1;
        }
        if ($seal_no1 == '') {
            $error['seal1'] = 1;
        }
        elseif (!ctype_alnum($seal_no1)) {
            $error['sea1'] = 'senu1';
        }
        if ($seal_no2 == '') {
            $error['seal2'] = 1;
        }
        elseif (!ctype_alnum($seal_no2)) {
            $error['sea2'] = 'senu2';
        }
        if ($consignee == '') {
            $error['cons'] = 1;
        }
        if($special_seal != '' && !ctype_alnum($special_seal)){
            $error['spesc'] = "spelss";
        }

        if(count($error)) {
            new Respond(160,  array("err"=> $error));
        }
        $number = preg_replace("/\s+/", "", $container_number);
        $consignee2 = trim($consignee,"=");
  
        $user_id = $_SESSION['id'];

        $query->query("SELECT id FROM voyage WHERE reference = 'EMP'");
        $res3 = $query->run();
        $result1 = $res3->fetch_assoc();
        $voyageId = $result1['id'];

        $query->query("INSERT INTO container(number, seal_number_1,seal_number_2,iso_type_code,soc_status, voyage, shipping_line_id,agency_id,trade_type_code,importer_address, oog_status,full_status)
              VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $query->bind = array('sssisiiissii', &$number, &$seal_no1, &$seal_no2, &$iso_id, &$soc, &$voyageId, &$shipping_line_id, &$agent_id, &$trade_type, &$consignee2,
            &$oog, &$full_status);
        $res3 = $query->run();

 
        $last_id = $res3->get_last_id();

    
            $info_category = 'General Goods';

            $query->query("insert into container_depot_info(container_id,load_status,goods,user_id)values(?,'FCL',?,?)");
            $query->bind = array('isi', &$last_id, &$info_category, &$user_id);
            $query->run();
            $query->query("insert into proforma_container_depot_info(container_id,load_status,goods,user_id)values(?,'FCL',?,?)");
            $query->bind = array('isi', &$last_id, &$info_category, &$user_id);
            $query->run();

     

        new Respond(260);
        $query->commit();
    }

    public function add_empty_activity() {
        $container_number = $this->request->param('ctno');
        $activity_type = $this->request->param('activity');
        $user_id = $_SESSION['id'];

        $query = new MyTransactionQuery();

        $query->query("select id from container where trade_type_code = '70' and number = ?");
        $query->bind = array('s', &$container_number);
        $res3 = $query->run();
        $result1 = $res3->fetch_assoc();

        $query->query("select id from depot_activity where name = ?");
        $query->bind = array('s', &$activity_type);
        $query->run();
        $query_res = $query->fetch_assoc();
        $activity_id = $query_res['id'];

        $query->query("INSERT INTO container_log(container_id, activity_id, user_id, date)VALUES(?,?,?, now())");
        $query->bind = array('iii', &$result1['id'], &$activity_id, &$user_id);
        $query->run();
        $query->query("INSERT INTO proforma_container_log(container_id, activity_id, user_id, date)VALUES(?,?,?, now())");
        $query->bind = array('iii', &$result1['id'], &$activity_id, &$user_id);
        $query->run();

        $query->commit();
        new Respond(360);
    }

    public function edit_export_container(){
        $imdg = $this->request->param('imdg');
        $full_status = $this->request->param('fstat') == "1" ? 1 : 0;
        $oog = $this->request->param('oog') == "1" ? 1 : 0;
        $soc = $this->request->param('soc');
        $seal_no1 = $this->request->param('seal1');
        $seal_no2 = $this->request->param('seal2');
        $shipping_line = $this->request->param('shid');
        $container_number = $this->request->param('ctno');
        $row_container = $this->request->param('rctno');
        $booking_number = $this->request->param('bkno');
        $gate_record = $this->request->param('gid');
        $gate_record_id = (int)$gate_record;
        $trade_code = $this->request->param('trad');

        $error = array();

        $query = new MyTransactionQuery();
        $query->query("select id from shipping_line where name = ?");
        $query->bind = array('s', &$shipping_line);
        $res2 = $query->run();
        $result2 = $res2->fetch_assoc();
        $shipping_line_id = $result2['id'];
        if (!$shipping_line_id) {
            $shipping_line = htmlspecialchars($shipping_line);
            $query->query("select id from shipping_line where name = ?");
            $query->bind = array('s', &$shipping_line);
            $res2 = $query->run();
            $result2 = $res2->fetch_assoc();
            $shipping_line_id = $result2['id'];
        }

        if($trade_code == 70){
            $query->query("select id from imdg where name = ?");
            $query->bind = array('s', &$imdg);
            $res1 = $query->run();
            $result1 = $res1->fetch_assoc();
            $imdg_id = $result1['id'];
        }
  
        $query->query("select id,trade_type_code from container where number=? and gate_status != 'GATED OUT'");
        $query->bind = array('s',&$row_container);
        $query->run();
        $container_result = $query->fetch_assoc();
        $container_id = $container_result['id'];
   

        $query->query("select id,trade_type_code,status from container where number=? and gate_status != 'GATED OUT'");
        $query->bind = array('s',&$container_number);
        $query->run();
        $container_result1 = $query->fetch_assoc();
        $trade_type = $container_result1['trade_type_code'];
        $container_field_id = $container_result1['id'];

        $query->query("select id from container where number=? limit 1");
        $query->bind = array('s',&$container_number);
        $query->run();
        $number_result = $query->fetch_assoc();

        $query->query("select id from imdg where name=?");
        $query->bind = array('s',&$imdg);
        $query->run();
        $imdg_result = $query->fetch_assoc();
        $imdg_id = $imdg_result['id'];

       
        $seal_no1 = preg_replace("/\s+/", "", $seal_no1);
        $seal_no2 = preg_replace("/\s+/", "", $seal_no2);
        $booking_number = preg_replace("/\s+/", "", $booking_number);
        $container_number = preg_replace("/\s+/", "", $container_number);
       
        if($imdg == '' && $trade_code == 21){
            $error['img'] = 1;
        }
        elseif (!$imdg_id && $trade_code == 21) {
            $error['img'] = "imdg_err";
        }
        if($shipping_line == ''){
            $error['sline'] = 1;
        }
        else if (!$shipping_line_id) {
            $error['sline'] = 'shpping_line_err';
        }
        if ($seal_no1 == '') {
            $error['seal1'] = 1;
        }
        elseif (!ctype_alnum($seal_no1)) {
            $error['sea1'] = 'senu1';
        }
        if ($seal_no2 == '') {
            $error['seal2'] = 1;
        }
        elseif (!ctype_alnum($seal_no2)) {
            $error['sea2'] = 'senu2';
        }
        if ($booking_number == '' && $trade_code == 21) {
            $error['bknu'] = 1;
        }
        elseif (!ctype_alnum($booking_number) && $trade_code == 21) {
            $error['bkerr'] = 'bkn_err';
        }
        if ($container_number == '') {
            $error['cnerr'] = 1;
        }
        elseif (!ctype_alnum($container_number)) {
            $error['cner'] = 'ctn_err';
        }
        elseif(strlen($container_number) < 11){
            $error['clen'] = 1;
        }
        if($number_result['id']){
            $query->query("select container_id from gate_record where id=?");
            $query->bind = array('i',&$gate_record_id);
            $query->run();
            $gate_result = $query->fetch_assoc();
    
            $query->query("select id from invoice_container where container_id=?");
            $query->bind = array('i',&$gate_result['container_id']);
            $query->run();
            $invoiced = $query->fetch_assoc();
    
            if(($trade_type == '11') && !$invoiced['id']){
                $error['trter'] = 1;
            }
            elseif($container_result1['status'] == 1){
                $error['flag'] = 1;
            }
            else{
                $container_id = $container_field_id;
            }
        }
     
      
        if(count($error) > 0) {
            $query->commit();
            new Respond(161,  array("err"=> $error));
        }
        if ($trade_code == 70) {
            $query->query("update container set number=?,seal_number_1=?,seal_number_2=?, soc_status =?,shipping_line_id=?,oog_status=?,full_status=? where id=?");
            $query->bind = array('ssssiiii',&$container_number,&$seal_no1,&$seal_no2,&$soc,&$shipping_line_id,&$oog,&$full_status,&$container_id);
        }
        else{
            $query->query("update container set number=?,book_number=?,seal_number_1=?,seal_number_2=?, soc_status =?,shipping_line_id=?,imdg_code_id=?,oog_status=?,full_status=? where id=?");
            $query->bind = array('sssssiiiii',&$container_number,&$booking_number,&$seal_no1,&$seal_no2,&$soc,&$shipping_line_id,&$imdg_id,&$oog,&$full_status,&$container_id);
        }

        $query->run();
        $query->commit();
        new Respond(261);
        
    }

    public function update_container_gate_status(){
        $container_number = $this->request->param('num');
        $container_field_value = $this->request->param('cnum');
        
        $this->user = $_SESSION['id'];

        $query = new MyTransactionQuery();
        $query->query("select id from container where number=? and gate_status != 'GATED OUT'");
        $query->bind = array('s',&$container_number);
        $query->run();
        $container_result1 = $query->fetch_assoc();
        $this->container_data_id = $container_result1['id'];

        $query->query("select id from container where number=? and gate_status != 'GATED OUT'");
        $query->bind = array('s',&$container_field_value);
        $query->run();
        $container_result2 = $query->fetch_assoc();
        $this->container_field_id = $container_result2['id'];

        $query->query("update container set gate_status='GATED IN' where id=?");
        $query->bind = array('i',&$this->container_field_id);
        $query->run();

        $query->query("update container set gate_status='' where id=?");
        $query->bind = array('i',&$this->container_data_id);
        $query->run();

        $this->update_container_gate_edit($query);
        $this->update_container_gate_edit($query,$proforma="proforma_");
        $query->commit();
    }

    public function update_container_gate_edit($query,$proforma=""){
        $query->query("select activity_id,date from ".$proforma."container_log where container_id=?");
        $query->bind = array('i',&$this->container_data_id);
        $query->run();

        while($result2 = $query->fetch_assoc()){
            $activity_id = $result2['activity_id'];
            $date = $result2['date'];
            $insert_query = new MyQuery();
            $insert_query->query("insert into ".$proforma."container_log(container_id,activity_id,user_id, date,pdate)values(?,?,?,?,now())");
            $insert_query->bind = array('iiis',&$this->container_field_id,&$activity_id,&$this->user,&$date);
            $insert_query->run();

            $insert_query2 = new MyQuery();
            $insert_query2->query("insert into ".$proforma."container_log_history(container_id,activity_id,user_id,status)values(?,?,?,'CREATED');
            ");
            $insert_query2->bind = array('iii',&$this->container_field_id,&$activity_id,&$this->user);
            $insert_query2->run();

            $check_query = new MyQuery();
            $check_query->query("select id from ".$proforma."container_depot_info where container_id=?");
            $check_query->bind = array('i',&$this->container_field_id);
            $check_query->run();
            $check_id = $check_query->fetch_assoc();
            if(!$check_id['id']){
                $insert_query3 = new MyQuery();
                $insert_query3->query("insert into ".$proforma."container_depot_info(container_id,load_status,goods,user_id)values(?,'FCL','General Goods',?)");
                $insert_query3->bind = array('ii',&$this->container_field_id,&$this->user);
                $insert_query3->run();
            }
        }
        $query->query("delete from ".$proforma."container_log where container_id=?");
        $query->bind = array('i',&$this->container_data_id);
        $query->run();
    }

    public function update_trade_type(){
        $id = $this->request->param('id');
        $query = new MyTransactionQuery();
                $query->query("select container_id from gate_record where id =?");
                $query->bind = array('i', &$id);
                $query->run();
                $result6 = $query->fetch_assoc();
                $container_id = $result6['container_id'];

            
                $query->query("SELECT invoice.trade_type FROM invoice INNER JOIN invoice_container 
                    ON invoice.id = invoice_container.invoice_id 
                    INNER JOIN container ON container.id = invoice_container.container_id 
                    WHERE container.id = ?");
                $query->bind = array('i', &$container_id);
                $run = $query->run();
                $result = $run->fetch_assoc();

                $tradeTypeId = $result['trade_type'];

                if ($tradeTypeId == 3) {
                    $query->query("UPDATE container SET trade_type_code = '13' WHERE id = ?");
                    $query->bind = array('i', &$container_id);
                    $query->run();
                    $query->commit();
                }
                $query->commit();
    }

    public function container_iso_type($query,$container_id){
        $query->query("select code from container_isotype_code where id = ?");
        $query->bind = array('i', &$container_id);
        $query->run();
        $result1 = $query->fetch_assoc();
        return $result1['code'];
    }

    public function update_export_seal(){
        $seal_no1 = $this->request->param('sno1');
        $seal_no2 = $this->request->param('sno2');
        $booking_number = $this->request->param('bkno');
        $trade_type = $this->request->param('trad');
        $container_number = $this->request->param('ctno');
        $consignee = htmlentities($this->request->param('cons'));
        $shipping_line = $this->request->param('shid');
        $vehicle = $this->request->param('vech');
        $driver = $this->request->param('drvr');
        $trucking = $this->request->param('trk');

        $query = new MyTransactionQuery();
        $query->query("select id from shipping_line where name = ?");
        $query->bind = array('s', &$shipping_line);
        $res2 = $query->run();
        $result2 = $res2->fetch_assoc();

        $error = array();

        if ($consignee == "") {
            $error['conss'] = 1;
        }
        if ($shipping_line == "") {
            $error['ship'] = 1;
        }
        elseif (!$result2['id']) {
            $error['eship'] = 'ersh';
        }
        if ($vehicle == "") {
            $error['vch'] = 1;
        }
        if ($driver == "") {
            $error['drv'] = 1;
        }
        if ($trucking == "") {
            $error['truk'] = 1;
        }
        if ($seal_no1 == '') {
            $error['seal1'] = 1;
        }
        elseif ($seal_no1 == "NA") {
            $error['sea3'] = 'senu0';
        }
        elseif (!ctype_alnum($seal_no1)) {
            $error['sea1'] = 'senu1';
        }
        if ($seal_no2 == '') {
            $error['seal2'] = 1;
        }
        elseif ($seal_no2  == 'NA') {
            $error['seal4'] = 'senum';
        }
        elseif (!ctype_alnum($seal_no2)) {
            $error['sea2'] = 'senu2';
        }
        if ($booking_number == '' && $trade_type == "21") {
            $error['bknu'] = 1;
        }
        elseif (!ctype_alnum($booking_number) && $trade_type == "21") {
            $error['bkerr'] = 'bkn_err';
        }

        if(count($error) > 0) {
            $query->commit();
            new Respond(164,  array("err"=> $error));
        }
        
       
        $query->query("select id,trade_type_code from container where number=? and gate_status != 'GATED OUT'");
        $query->bind = array('s',&$container_number);
        $query->run();
        $trade_query = $query->fetch_assoc();
        if (($trade_type == '21') && ($trade_query['trade_type_code'] == '11')) {

            $query->query("update container set shipping_line_id=?, seal_number_1=?, seal_number_2=?,bl_number='',book_number=?,voyage=1, trade_type_code = '21' where id=? and gate_status != 'GATED OUT'");
            $query->bind = array('isssi',&$result2['id'],&$seal_no1,&$seal_no2,&$booking_number,&$trade_query['id']);
            $query->run();
            $query->commit();

            new Respond(265);
        }
       
        
        
    }

}
?>
