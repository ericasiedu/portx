<?php
namespace Api;

use Lib\MyQuery,
     Lib\MyTransactionQuery,
     Lib\Respond;

class InvoiceStatus {

    private $request,$response;

    public function  __construct($request,$response) {
        $this->request = $request;
        $this->response = $response;
    }

    public function cancel() {
        $number = $this->request->param('data');
        $is_proforma = $this->request->param('prof') == 1;
        $cancelled_msg = array();

        $user_id = $_SESSION['id'];

        $table_prefix = $is_proforma ? "proforma_" : "";

        $query = new MyTransactionQuery();

        $query->query("select id,status,note from  " . $table_prefix . "invoice where number = ?");
        $query->bind = array('s', &$number);
        $query->run();
        $invoice = $query->fetch_assoc();
        $invoice_id = $invoice['id'];

        $query->query("select id from ".$table_prefix."invoice_note where invoice_id=?");
        $query->bind = array('i', &$invoice_id);
        $query->run();
        $invoice_record = $query->fetch_assoc();

      

        if (!$invoice_record["id"]){
            new Respond(121);
        }
        if ($invoice['status'] == "DEFERRED") {
            $query->query("select container.id from container inner join  " . $table_prefix . "invoice_container 
            on  " . $table_prefix . "invoice_container.container_id = container.id inner join  " . $table_prefix . "invoice on  " . $table_prefix . "invoice.id =  " . $table_prefix . "invoice_container.invoice_id 
            where  " . $table_prefix . "invoice.id =? and  " . $table_prefix . "invoice.status = 'DEFERRED' and container.gate_status = 'GATED OUT'");
            $query->bind = array('i', &$invoice_id);
            $query->run();

            if ($query->num_rows()) {
                $query->commit();
                new Respond(120);
            }

            $query->query("UPDATE " . $table_prefix . "invoice SET status = 'UNPAID', deferral_note = '' WHERE id =?");
            $query->bind = array('i', &$invoice_id);
            $query->run();

            $query->query("insert into " . $table_prefix . "invoice_history_log(invoice_id, status, user_id) values (?,'CANCELLED',?)");
            $query->bind = array('ii', &$invoice_id,&$user_id);
            $query->run();

            new Respond(240, array('numb' => $number));
        } else if ($invoice['id']) {
            $query->query("UPDATE  " . $table_prefix . "invoice SET cancelled_by = ?, status = 'CANCELLED' WHERE id =?");
            $query->bind = array('ii', &$user_id, &$invoice_id);
            $query->run();

            $query->query("insert into " . $table_prefix . "invoice_history_log(invoice_id, status, user_id) values (?,'CANCELLED',?)");
            $query->bind = array('ii', &$invoice_id,&$user_id);
            $query->run();

            $this->update_invoiced_container($query, $invoice_id, $table_prefix);
            

            $this->insert_invoice_status($query,$invoice_id,$table_prefix);

        

            $query->query("delete from  " . $table_prefix . "invoice_container where invoice_id = ?");
            $query->bind = array('i', &$invoice_id);
            $query->run();
            $cancelled_msg['c_msg'] = $number;
            new Respond(241, array('numb' => $number));
        }

        $query->commit();
    }

    public function approve() {
        $number = $this->request->param('data');
        $user_id = $_SESSION['id'];

        $query = new MyTransactionQuery();
        $query->query("select id from invoice where number=?");
        $query->bind = array('s',&$number);
        $query->run();
        $result = $query->fetch_assoc();
        $invoice_id = $result['id'];

        $query->query("UPDATE invoice SET approved = 1 WHERE number = ? and approved = 0");
        $query->bind = array('s', &$number);
        $query->run();

        $query->query("insert into invoice_history_log(invoice_id, status, user_id) values (?,'APPROVED',?)");
        $query->bind = array('ii', &$invoice_id,&$user_id);
        $query->run();

        $query->commit();
        $approved_msg['number'] = $number;
        new Respond(242, $approved_msg);
    }


    public function update_invoiced_container($query, $invoice ,$table_prefix = "") {
        $query->query("select  " . $table_prefix . "invoice_details.container_id,  " . $table_prefix . "invoice_details.product_key
                  from  " . $table_prefix . "invoice inner join  " . $table_prefix . "invoice_details on  " . $table_prefix . "invoice_details.invoice_id =  " . $table_prefix . "invoice.id
                  where  " . $table_prefix . "invoice.id =? order by  " . $table_prefix . "invoice_details.id desc");
        $query->bind = array('i', &$invoice);
        $invoice_query = $query->run();

        $activities = $invoice_query->fetch_all(MYSQLI_ASSOC);

        foreach ($activities as $activity) {
            $container_id = $activity['container_id'];
            $activity_id = $activity['product_key'];

            $query->query("SELECT id FROM `" . $table_prefix . "container_log`
                      WHERE container_id=? and invoiced = '1' and activity_id =?
                      ORDER BY id DESC");
            $query->bind = array('ii', &$container_id, &$activity_id);
            $query->run();
            $log_id = $query->fetch_num()[0];

            if ($log_id) {
                $query->query("UPDATE  " . $table_prefix . "container_log SET invoiced='0' WHERE id=?");
                $query->bind = array('i', &$log_id);
                $query->run();
            }
        }
    }

    public function recall_invoice(){
        $invoice_number = $this->request->param('invn');
        $is_proforma = $this->request->param('prof') == 1;
        $table_prefix = $is_proforma ? "proforma_" : "";

        $query = new MyTransactionQuery();
        $query->query("select id from ".$table_prefix."invoice where number=? and status = 'CANCELLED'");
        $query->bind = array('s',&$invoice_number);
        $query->run();
        $result = $query->fetch_assoc();
        $invoice_id = $result['id'];


        $query->query("select id from ".$table_prefix."invoice_note where invoice_id=? and note_type='RECALLED'");
        $query->bind = array('i', &$invoice_id);
        $query->run();
        $invoice_record = $query->fetch_assoc();


        if (!$invoice_record["id"]){
            new Respond(1133);
        }
        

        $user_id = $_SESSION['id'];

        $query->query("select container_id from ".$table_prefix."invoice_details where invoice_id=? order by id desc");
        $query->bind = array('i',&$invoice_id);
        $query->run();

   

        while($result = $query->fetch_assoc()){
            $container_id = $result['container_id'];
            
            $query->query("select ".$table_prefix."invoice_container.id from ".$table_prefix."invoice_container left join gate_record on gate_record.container_id = ".$table_prefix."invoice_container.container_id 
                    where ".$table_prefix."invoice_container.container_id=? and gate_record.type='GATE IN'");
            $query->bind = array('i',&$container_id);
            $query->run();
            
            if($query->num_rows() > 0){
                $query->commit();
                new Respond(1134);
            }

            $query->query("insert into ".$table_prefix."invoice_container(invoice_id,container_id)values(?,?)");
            $query->bind = array('ii',&$invoice_id,&$container_id);
            $query->run();

            $query->query("update ".$table_prefix."container_log left join gate_record on gate_record.container_id = ".$table_prefix."container_log.container_id set invoiced=1 
                    where ".$table_prefix."container_log.container_id=? and gate_record.type='GATE IN'");
            $query->bind = array('i',&$container_id);
            $query->run();

            $query->query("update ".$table_prefix."invoice set status='RECALLED',recalled_by=? where id=?");
            $query->bind = array('ii',&$user_id,&$invoice_id);
            $query->run();

            $query->query("insert into " . $table_prefix . "invoice_history_log(invoice_id, status, user_id) values (?,'RECALLED',?)");
            $query->bind = array('ii', &$invoice_id,&$user_id);
            $query->run();
        }
        $query->commit();
        
        new Respond(260);
    }

    public function insert_invoice_status($query,$invoice_id,$table_prefix){
        $query->query("select container_id from ".$table_prefix."invoice_details where invoice_id=?");
        $query->bind = array('i',&$invoice_id);
        $query->run();
        while($result = $query->fetch_assoc()){
            $container_id = $result['container_id'];

                $query->query("update ".$table_prefix."invoice_status set cancelled=0 where invoice_id !=? and container_id=?");
                $query->bind = array('ii',&$invoice_id,&$container_id);
                $query->run();
       
                $query->query("insert into ".$table_prefix."invoice_status(invoice_id,container_id,cancelled)values(?,?,1)");
                $query->bind = array('ii',&$invoice_id,&$container_id);
                $query->run();
        }

        
    }


}

?>