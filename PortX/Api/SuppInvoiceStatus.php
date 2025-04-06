<?php
namespace Api;


use Lib\MyQuery,
    Lib\MyTransactionQuery,
    Lib\Respond;

class SuppInvoiceStatus {

    private $request,$response;

    public function  __construct($request,$response){
        $this->request = $request;
        $this->response = $response;

    }

    public function approve(){
        $number = $this->request->param('data');
        $user_id = $_SESSION['id'];
       

        $query = new MyTransactionQuery();
        $query->query("select id from supplementary_invoice where number=?");
        $query->bind = array('s',&$number);
        $query->run();
        $result = $query->fetch_assoc();
        $invoice_id = $result['id'];

        $query->query("UPDATE supplementary_invoice SET approved = 1 WHERE number = ?");
        $query->bind = array('s', &$number);
        $query->run();

        $query->query("insert into supplementary_invoice_history_log(invoice_id, status, user_id) values (?,'APPROVED',?)");
        $query->bind = array('ii', &$invoice_id,&$user_id);
        $query->run();

        $query->commit();
        new Respond(2122);
    }

    public function cancel() {
        $number = $this->request->param('data');
        $user_id = $_SESSION['id'];
        $query=new MyTransactionQuery();

        $query->query("select id,status from supplementary_invoice where number =?");
        $query->bind = array('s',&$number);
        $query->run();
        $supp_result = $query->fetch_assoc();
        $supp_invoice_id = $supp_result['id'];

        $query->query("select id from supplementary_note where invoice_id=?");
        $query->bind = array('i', &$supp_invoice_id);
        $query->run();
        $invoice_record = $query->fetch_assoc();

        if (!$invoice_record["id"]){
            new Respond(123);
        }

        if ($supp_result['status'] == "DEFERRED"){
            $query->query("select DISTINCT(container.id) from container inner join supplementary_invoice_container 
            on supplementary_invoice_container.container_id = container.id inner join supplementary_invoice on supplementary_invoice.id = supplementary_invoice_container.invoice_id 
            where supplementary_invoice.invoice_id = ? and supplementary_invoice.status = 'DEFERRED' and container.gate_status = 'GATED OUT'");
            $query->bind = array('i',&$supp_invoice_id);
            $query->run();

            if ($query->num_rows()) {
                $query->commit();
                new Respond(121);
            }
            $query->query("UPDATE supplementary_invoice SET status = 'UNPAID', deferral_note = '' WHERE id =?");
            $query->bind = array('i',&$supp_invoice_id);
            $query->run();

            $query->query("insert into supplementary_invoice_history_log(invoice_id, status, user_id) values (?,'CANCELLED',?)");
            $query->bind = array('ii', &$supp_invoice_id,&$user_id);
            $query->run();

            $query->commit();
            new Respond(250,array('numb'=>$number));
        }
        elseif ($supp_invoice_id){
            $user_id = $_SESSION['id'];
            $query->query("UPDATE supplementary_invoice SET status = 'CANCELLED', cancelled_by = ? WHERE id =?");
            $query->bind = array('ii', &$user_id, &$supp_invoice_id);
            $query->run();

            $query->query("insert into supplementary_invoice_history_log(invoice_id, status, user_id) values (?,'CANCELLED',?)");
            $query->bind = array('ii', &$supp_invoice_id,&$user_id);
            $query->run();

            $this->update_supplementary($query,$supp_invoice_id);

            $this->insert_invoice_status($query,$supp_invoice_id);

            $query->query("delete from supplementary_invoice_container where invoice_id =?");
            $query->bind = array('i',&$supp_invoice_id);
            $query->run();
            $query->commit();
            new Respond(251,array('numb'=>$number));


        }

        $query->commit();
    }

    public function update_supplementary($query,$invoice_id){
        $query->query("select supplementary_invoice_details.container_id, supplementary_invoice_details.product_key
                  from supplementary_invoice inner join supplementary_invoice_details on supplementary_invoice_details.invoice_id = supplementary_invoice.id
                  where supplementary_invoice.id =? order by supplementary_invoice_details.id desc");
        $query->bind = array('i',&$invoice_id);
        $query->run();
        $acitivities = $query->fetch_all(MYSQLI_ASSOC);


        foreach ($acitivities as $activity_result){
            $container_id = $activity_result['container_id'];
            $activity_id = $activity_result['product_key'];

            $query->query("SELECT id FROM container_log
                      WHERE container_id=? and invoiced = '1' and activity_id =?
                      ORDER BY id DESC");
            $query->bind = array('ii',&$container_id,&$activity_id);
            $log_result = $query->run();
            $log = $log_result->fetch_num()[0];

            if ($log){
                $query->query("UPDATE container_log SET invoiced='0' WHERE id=?");
                $query->bind = array('i',&$log);
                $query->run();
            }
        }
    }

    public function recall_invoice(){
        $invoice_number = $this->request->param('invn');
        

        $query = new MyTransactionQuery();
        $query->query("select id from supplementary_invoice where number=? and status = 'CANCELLED'");
        $query->bind = array('s',&$invoice_number);
        $query->run();
        $result = $query->fetch_assoc();
        $invoice_id = $result['id'];

        $query->query("select id from supplementary_note where invoice_id=? and note_type='RECALLED'");
        $query->bind = array('i', &$invoice_id);
        $query->run();
        $invoice_record = $query->fetch_assoc();


        if (!$invoice_record["id"]){
            new Respond(1133);
        }

        $user_id = $_SESSION['id'];

        $query->query("select container_id from supplementary_invoice_details where invoice_id=?");
        $query->bind = array('i',&$invoice_id);
        $query->run();



        while($result = $query->fetch_assoc()){
            $container_id = $result['container_id'];

            $query->query("select supplementary_invoice_container.id from supplementary_invoice_container left join gate_record on gate_record.container_id = supplementary_invoice_container.container_id 
                        where supplementary_invoice_container.container_id=? and gate_record.type='GATE IN'");
            $query->bind = array('i',&$container_id);
            $query->run();

            if($query->num_rows() > 0){
                $query->commit();
                new Respond(1134);
            }

            $query->query("insert into supplementary_invoice_container(invoice_id,container_id)values(?,?)");
            $query->bind = array('ii',&$invoice_id,&$container_id);
            $query->run();
        }
    

        $query->query("update supplementary_invoice set status='RECALLED',recalled_by=? where id=?");
        $query->bind = array('ii',&$user_id,&$invoice_id);
        $query->run();

        $query->query("insert into supplementary_invoice_history_log(invoice_id, status, user_id) values (?,'RECALLED',?)");
        $query->bind = array('ii', &$invoice_id,&$user_id);
        $query->run();

        $query->commit();
        new Respond(260);
    }

    public function insert_invoice_status($query,$invoice_id){
        $query->query("select container_id from supplementary_invoice_details where invoice_id=?");
        $query->bind = array('i',&$invoice_id);
        $query->run();
        while($result = $query->fetch_assoc()){
            $container_id = $result['container_id'];

                $query->query("update supplementary_status set cancelled=0 where invoice_id !=? and container_id=?");
                $query->bind = array('ii',&$invoice_id,&$container_id);
                $query->run();
       
                $query->query("insert into supplementary_status(invoice_id,container_id,cancelled)values(?,?,1)");
                $query->bind = array('ii',&$invoice_id,&$container_id);
                $query->run();
        }
    }

}



?>