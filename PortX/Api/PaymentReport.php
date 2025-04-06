<?php

namespace Api;
// ini_set("display_errors",1);

use
    Lib\ACL,
    DataTables\Bootstrap,
    DataTables\Editor,
    DataTables\Editor\Field,
    DataTables\Editor\Validate,
    DataTables\Editor\ValidateOptions,
    Lib\MyQuery,
    Lib\ReportGenerator,
    Lib\MyTransactionQuery,
    Lib\Respond;


class PaymentReport{
    private $request;

    private $end_date;

    private $start_date;

    private $payment_mode;

    private $trade_type;

    private $tax_type;

    private $param;

    function __construct($request){
        $this->request = $request;
        $date = date('Y-m-d');

        $this->start_date = $this->request->param('stdt');
        $this->end_date = $this->request->param('endt');


        $this->payment_mode = $this->request->param('pymd');
        $this->trade_type = $this->request->param('trty');
        $this->tax_type = $this->request->param('txty');

        $this->start_date = $this->start_date != '' ? $this->start_date : $date;
        $this->end_date = $this->end_date != '' ? $this->end_date : $date;
    }

    public function table()
    {
        $date = date('Y-m-d');

        $order = $this->request->param('order');
        $columns = $this->request->param('columns');
        $start_date = $this->request->param('stdt');
        $end_date = $this->request->param('endt');
        $order_key = $order[0]['column'];
        $order_dir = $order[0]['dir'];
        $order_col = $columns[$order_key]["data"];
        $start_offset = $this->request->param('start');
        $draw = $this->request->param('draw');
        $length = $this->request->param('length');
        $search_query = $this->request->param('search')['value'];
        $search_query = "%$search_query%";

        $id = 1;

        $query = new MyQuery();
        $query->query("select *  from (select receipt_number as rcpn, invoice.number as inv, invoice.date as idate, 
        waiver_pct as wpct, waiver_amount as wamt,tax_type.name as tax_type,
(select ifnull(sum(cost),0.00) from invoice_details where product_key = 1 and invoice_id=invoice.id) as hand,
(select sum(cost) from invoice_details where product_key = 2 and invoice_id=invoice.id) as transfer,
(select ifnull(sum(invoice_details.cost),0.00) as cost from invoice_details left join depot_activity on depot_activity.id = invoice_details.product_key  where depot_activity.name like 'Partial Unstu%' and invoice_id=invoice.id) as p_unstuff,
(select ifnull(sum(cost),0.00) from invoice_details where product_key = 7 and invoice_id=invoice.id) as unstuff,
(select ifnull(sum(cost),0.00) from invoice_details where product_key = 'S' and invoice_id=invoice.id) as stor,
(select ifnull(sum(cost),0.00) from invoice_details where product_key not in (1,2,7,62,63,64,65,67,68,86,'S') and invoice_id=invoice.id) as ancilar,
(select count(distinct(container_id)) FROM invoice_details WHERE invoice_id = invoice.id) as qty,
       (select cost AS getF from invoice_details_tax where description like \"GET%\" and invoice_id = pay.invoice_id) as getF, 
       (select cost AS getF from invoice_details_tax where description like \"VAT%\" and invoice_id = pay.invoice_id) as vat, 
       (select cost AS getF from invoice_details_tax where description like \"NHIL%\" and invoice_id = pay.invoice_id) as nhil,
       (select cost AS getF from invoice_details_tax where description like \"Covid-19%\" and invoice_id = pay.invoice_id) as covid, tax, invoice.cost as cost,
       paid,pay.bank_cheque_number as cheq, bank.name as bank, trade_type.name as trade_type,customer.name as cust, 'I', payment_mode.name as mode, pay.date as date, 
       (select SUM(cast(container_isotype_code.length / 20  as decimal(4,0))) as TEU from invoice
left join invoice_container on invoice_container.invoice_id = invoice.id
left join container on invoice_container.container_id = container.id
left join container_isotype_code on container.iso_type_code = container_isotype_code.id
where invoice.id = pay.invoice_id) as TEU, concat(user.first_name, \" \", user.last_name) as user
       from payment pay
       left join invoice on invoice.id = pay.invoice_id
       left join customer on invoice.customer_id =  customer.id
       left join trade_type on invoice.trade_type = trade_type.id
       left join invoice_container on invoice_container.invoice_id = invoice.id
       left join container on container.id = invoice_container.container_id
       left join payment_mode on payment_mode.id = mode
       left join bank on bank.id = pay.bank_name
       left join user on pay.user_id = user.id
        left join tax_type on tax_type.id = invoice.tax_type
       where invoice.tax_type like ? and trade_type.code like ? and cast(pay.date as date) between ? and ? and payment_mode.name like ? and 
       (
            receipt_number like ? OR
            invoice.number like ? OR
            pay.bank_cheque_number like ? OR
            bank.name like ? OR
            customer.name like ? OR
            payment_mode.name like ? OR
            pay.date like ? OR
            user.first_name like ? OR
            user.last_name like ?
       )
UNION
       select receipt_number as rcpn, supplementary_invoice.number as inv,  supplementary_invoice.date as idate, supplementary_invoice.waiver_pct as wpct, 
       supplementary_invoice.waiver_amount as wamt, tax_type.name as tax_type,
(select sum(cost) from supplementary_invoice_details where product_key = 1 and supplementary_invoice_details.invoice_id=supplementary_invoice.id) as hand,
(select sum(cost) from supplementary_invoice_details where product_key = 2 and supplementary_invoice_details.invoice_id=supplementary_invoice.id) as transfer,
(select ifnull(sum(supplementary_invoice_details.cost),0.00) from supplementary_invoice_details left join depot_activity on depot_activity.id = supplementary_invoice_details.product_key  where depot_activity.name like 'Partial Unstu%' and supplementary_invoice_details.invoice_id=supplementary_invoice.id) as p_unstuff,
(select ifnull(sum(cost),0.00) from supplementary_invoice_details where product_key = 7 and supplementary_invoice_details.invoice_id=supplementary_invoice.id) as unstuff,
(select ifnull(sum(cost),0.00) from supplementary_invoice_details where product_key = 'S' and supplementary_invoice_details.invoice_id=supplementary_invoice.id) as stor,
(select ifnull(sum(cost),0.00) from supplementary_invoice_details where product_key not in (1,2,7,62,63,64,65,67,68,86,'S') and supplementary_invoice_details.invoice_id=supplementary_invoice.id) as ancilar,
(select count(distinct(container_id)) FROM supplementary_invoice_details WHERE supplementary_invoice_details.invoice_id = supplementary_invoice.id) as qty,
       (select cost AS getF from supplementary_invoice_details_tax where description like \"GET%\" and invoice_id = sup_pay.invoice_id) as getF, 
       (select cost AS getF from supplementary_invoice_details_tax where description like \"VAT%\" and invoice_id = sup_pay.invoice_id) as vat, 
       (select cost AS getF from supplementary_invoice_details_tax where description like \"NHIL%\" and invoice_id = sup_pay.invoice_id) as nhil,
       (select cost AS getF from supplementary_invoice_details_tax where description like \"Covid-19%\" and invoice_id = sup_pay.invoice_id) as covid,supplementary_invoice.tax, supplementary_invoice.cost as cost, 
       paid, sup_pay.bank_cheque_number as cheq, bank.name as bank, trade_type.name as trade_type, customer.name as cust, 'S', payment_mode.name as mode, sup_pay.date as date, 
       (select SUM(cast(container_isotype_code.length / 20  as decimal(4,0))) as TEU from supplementary_invoice
left join supplementary_invoice_container on supplementary_invoice_container.invoice_id = supplementary_invoice.id
left join container on supplementary_invoice_container.container_id = container.id
left join container_isotype_code on container.iso_type_code = container_isotype_code.id
where supplementary_invoice.id = sup_pay.invoice_id) as TEU, concat(user.first_name, \" \", user.last_name) as user
       from supplementary_payment sup_pay
       left join supplementary_invoice on supplementary_invoice.id = sup_pay.invoice_id
       left join invoice on supplementary_invoice.invoice_id = invoice.id
       left join customer on invoice.customer_id =  customer.id    
       left join trade_type on invoice.trade_type = trade_type.id
       left join supplementary_invoice_container on supplementary_invoice_container.invoice_id = supplementary_invoice.invoice_id
       left join container on container.id = supplementary_invoice_container.container_id
       left join payment_mode on payment_mode.id = mode
       left join bank on bank.id = sup_pay.bank_name                            
       left join user on sup_pay.user_id = user.id
       left join tax_type on tax_type.id = invoice.tax_type
       where invoice.tax_type like ? and trade_type.code like ? and cast(sup_pay.date as date) between ?  and ? and payment_mode.name like ? and 
       (
            receipt_number like ? OR
            supplementary_invoice.number like ? OR
            sup_pay.bank_cheque_number like ? OR
            bank.name like ? OR
            customer.name like ? OR
            payment_mode.name like ? OR
            sup_pay.date like ? OR
            user.first_name like ? OR
            user.last_name like ?
       ))  as payments ORDER by $order_col $order_dir");
        $query->bind = array("ssssssssssssssssssssssssssss",&$this->tax_type,
            &$this->trade_type, &$start_date, &$end_date, &$this->payment_mode,
            &$search_query, &$search_query, &$search_query, &$search_query, &$search_query, &$search_query, &$search_query, &$search_query, &$search_query,
            &$this->tax_type, &$this->trade_type, &$this->start_date, &$this->end_date, &$this->payment_mode,
            &$search_query, &$search_query, &$search_query, &$search_query, &$search_query, &$search_query, &$search_query, &$search_query, &$search_query);
        $query->run();

        $added = 0;
        $index = 0;

        $filtered = array();

        while($row = $query->fetch_assoc()) {
            if($added >= $length){
                break;
            }

            if($index >= $start_offset) {
                array_push($filtered, $row);
                $added++;
            }

            $index++;
        }

        $total = $query->num_rows();

        $rows = ['data' => $filtered, "recordsTotal" => $total, "recordsFiltered" => $total, "draw" => $draw];

        new Respond(241,$rows);
    }

    public function report()
    {
        $date = date('Y-m-d');
        $start_date = $this->start_date != '' ? $this->start_date : $date;
        $end_date = $this->end_date != '' ? $this->end_date : $date;
        $type = $this->request->param('type');
        $columns = json_decode($this->request->param('src'));
        $headers = json_decode($this->request->param('head'));

        $id = 1;

        $query = new MyQuery();
        $query->query("select invoice.id as id, receipt_number as rcpn, invoice.number as inv, invoice.date, invoice.cost, 
        waiver_pct as wpct,waiver_amount as wamt,tax,invoice.tax_type as tax_type, pay.bank_cheque_number as cheq, bank.name as bank,
        (select ifnull(sum(cost),0.00) from invoice_details where product_key = 1 and invoice_id=invoice.id) as hand,
(select sum(cost) from invoice_details where product_key = 2 and invoice_id=invoice.id) as transfer,
(select ifnull(sum(invoice_details.cost),0.00) as cost from invoice_details left join depot_activity on depot_activity.id = invoice_details.product_key  where depot_activity.name like 'Partial Unstu%' and invoice_id=invoice.id) as p_unstuff,
(select ifnull(sum(cost),0.00) as cost from invoice_details where product_key = 7 and invoice_id=invoice.id) as unstuff,
(select ifnull(sum(cost),0.00) from invoice_details where product_key = 'S' and invoice_id=invoice.id) as stor,
(select ifnull(sum(cost),0.00) from invoice_details where product_key not in (1,2,7,62,63,64,65,67,68,86,'S') and invoice_id=invoice.id) as ancilar,
(select count(distinct(container_id)) FROM invoice_details WHERE invoice_id = invoice.id) as qty,
        (select cost AS getF from invoice_details_tax where description like \"GET%\" and invoice_id = pay.invoice_id) as getF, 
        (select cost AS getF from invoice_details_tax where description like \"VAT%\" and invoice_id = pay.invoice_id) as vat, 
        (select cost AS getF from invoice_details_tax where description like \"NHIL%\" and invoice_id = pay.invoice_id) as nhil,
        (select cost AS getF from invoice_details_tax where description like \"Covid-19%\" and invoice_id = pay.invoice_id) as covid,
        paid, trade_type.code as trade_type,customer.name as cust, 'I', payment_mode.name as mode, pay.date as date, 
        (select SUM(cast(container_isotype_code.length / 20  as decimal(4,0))) as TEU from invoice
        left join invoice_container on invoice_container.invoice_id = invoice.id
        left join container on invoice_container.container_id = container.id
        left join container_isotype_code on container.iso_type_code = container_isotype_code.id
        where invoice.id = pay.invoice_id) as TEU, concat(user.first_name, \" \", user.last_name) as user
        from payment pay
        left join invoice on invoice.id = pay.invoice_id
        left join customer on invoice.customer_id =  customer.id
        left join trade_type on invoice.trade_type = trade_type.id
        left join invoice_container on invoice_container.invoice_id = invoice.id
        left join container on container.id = invoice_container.container_id
        left join payment_mode on payment_mode.id = mode
        left join bank on bank.id = pay.bank_name
        left join user on pay.user_id = user.id
        where invoice.tax_type like ? and trade_type.code like ? and cast(pay.date as date) between ?  and ? and payment_mode.name like ?
        UNION
        select  supplementary_invoice.id as id, receipt_number as rcpn, supplementary_invoice.number as inv,  supplementary_invoice.date, 
        supplementary_invoice.cost as cost,supplementary_invoice.waiver_pct as wpct,  supplementary_invoice.waiver_amount as wamt,supplementary_invoice.tax, invoice.tax_type as tax_type,sup_pay.bank_cheque_number as cheq, bank.name as bank,
        (select sum(cost) from supplementary_invoice_details where product_key = 1 and supplementary_invoice_details.invoice_id=supplementary_invoice.id) as hand,
(select sum(cost) from supplementary_invoice_details where product_key = 2 and supplementary_invoice_details.invoice_id=supplementary_invoice.id) as transfer,
(select ifnull(sum(supplementary_invoice_details.cost),0.00) from supplementary_invoice_details left join depot_activity on depot_activity.id = supplementary_invoice_details.product_key  where depot_activity.name like 'Partial Unstu%' and supplementary_invoice_details.invoice_id=supplementary_invoice.id) as p_unstuff,
(select ifnull(sum(cost),0.00) from supplementary_invoice_details where product_key = 7 and supplementary_invoice_details.invoice_id=supplementary_invoice.id) as unstuff,
(select ifnull(sum(cost),0.00) from supplementary_invoice_details where product_key = 'S' and supplementary_invoice_details.invoice_id=supplementary_invoice.id) as stor,
(select ifnull(sum(cost),0.00) from supplementary_invoice_details where product_key not in (1,2,7,62,63,64,65,67,68,86,'S') and supplementary_invoice_details.invoice_id=supplementary_invoice.id) as ancilar,
(select count(distinct(container_id)) FROM supplementary_invoice_details WHERE supplementary_invoice_details.invoice_id = supplementary_invoice.id) as qty,
        (select cost AS getF from supplementary_invoice_details_tax where description like \"GET%\" and invoice_id = sup_pay.invoice_id) as getF, 
        (select cost AS getF from supplementary_invoice_details_tax where description like \"VAT%\" and invoice_id = sup_pay.invoice_id) as vat, 
        (select cost AS getF from supplementary_invoice_details_tax where description like \"NHIL%\" and invoice_id = sup_pay.invoice_id) as nhil,
        (select cost AS getF from supplementary_invoice_details_tax where description like \"Covid-19%\" and invoice_id = sup_pay.invoice_id) as covid, 
        paid, trade_type.code as trade_type, customer.name as cust, 'S', payment_mode.name as mode, sup_pay.date as date, 
        (select SUM(cast(container_isotype_code.length / 20  as decimal(4,0))) as TEU from supplementary_invoice
        left join supplementary_invoice_container on supplementary_invoice_container.invoice_id = supplementary_invoice.id
        left join container on supplementary_invoice_container.container_id = container.id
        left join container_isotype_code on container.iso_type_code = container_isotype_code.id
        where supplementary_invoice.id = sup_pay.invoice_id) as TEU, concat(user.first_name, \" \", user.last_name) as user
        from supplementary_payment sup_pay
        left join supplementary_invoice on supplementary_invoice.id = sup_pay.invoice_id
        left join invoice on supplementary_invoice.invoice_id = invoice.id
        left join customer on invoice.customer_id =  customer.id    
        left join trade_type on invoice.trade_type = trade_type.id
        left join supplementary_invoice_container on supplementary_invoice_container.invoice_id = supplementary_invoice.invoice_id
        left join container on container.id = supplementary_invoice_container.container_id
        left join payment_mode on payment_mode.id = mode
        left join bank on bank.id = sup_pay.bank_name                            
        left join user on sup_pay.user_id = user.id
        where invoice.tax_type like ? and trade_type.code like ? and cast(sup_pay.date as date) between ? and ? and payment_mode.name like ?");
        $query->bind = array("ssssssssss",&$this->tax_type,&$this->trade_type, &$start_date, &$end_date, &$this->payment_mode,
            &$this->tax_type, &$this->trade_type, &$start_date, &$end_date, &$this->payment_mode);
        $run = $query->run();

        $data = array();

        while ($row = $run->fetch_assoc()) {
            $row_data = array();

            foreach ($columns as $col){

                if($col == "bank"){
                    if($row['bank'] == "" || $row['bank'] == null){
                        $row[$col] = "NA";
                    }
                }
                if($col == "cheq"){
                    if($row['cheq'] == "" || $row['cheq'] == null){
                        $row[$col] = "NA";
                    }
                }

                array_push($row_data,$row[$col]);
            }
            array_push($data, $row_data);
        }

        $totals = $this->get_totals_array();

        $visible_totals = array();

        foreach ($columns as $column) {
            array_push($visible_totals, is_null($totals[$column]) ? "" : $totals[$column]);
        }

     

        $length = count($headers);
        $width = 190 / $length;
        $widths = array_fill(0, $length, $width);

        $report_generator = new ReportGenerator($start_date, $end_date, "Payment Report", $headers, $data, $widths, $visible_totals);

        if($type == "pdf") {
            $report_generator->printPdf();
        }
        else {
            $report_generator->printExcel();
        }
    }

    public function total(){
        $totals = $this->get_totals_array();
        new Respond(230, array('wpct' => $totals['wpct'],'wamt' => $totals['wamt'],'hand' => $totals['hand'],'trans' => $totals['transfer'],'partst' => $totals['partst'],'unstu' => $totals['unstu'],'ancilar' => $totals['ancilar'],'stor' => $totals['stor'],'qty' => $totals['qty'],'getf' => $totals['getF'], 'vat' =>$totals['vat'], 'nhil' => $totals['nhil'],'covid' => $totals['covid'],'tax' => $totals['tax'], 'cost' => $totals['cost'],'paid' => $totals['paid']));
    }

    private function get_totals_array(){
        $query = new MyTransactionQuery();
        $query->query("select sum(paid) from 
		(select paid, tax_type, payment_mode.name as mode, payment.date as date, code from payment
	            inner join invoice on invoice.id = payment.invoice_id
                inner join trade_type on trade_type.id = invoice.trade_type                            
                inner join payment_mode on payment_mode.id = payment.mode
         UNION
         select paid, tax_type, payment_mode.name as mode, supplementary_payment.date as date, code from supplementary_payment
	            inner join supplementary_invoice on supplementary_invoice.id = supplementary_payment.invoice_id    
         		inner join invoice on invoice.id = supplementary_invoice.invoice_id
                inner join trade_type on trade_type.id = invoice.trade_type  
                inner join payment_mode on payment_mode.id = supplementary_payment.mode
                ) as view 
                where tax_type like ? and code like ? and cast(date as date) between ? and ? and mode like ?");
        $query->bind =  array('sssss', &$this->tax_type, &$this->trade_type, &$this->start_date, &$this->end_date, &$this->payment_mode);
        $run =  $query->run();
        $sum = $run->fetch_num()[0] ?? "0.00";

        $query->query("select sum(cost) from 
                  (select invoice_details_tax.cost as cost, tax_type, payment_mode.name as mode, payment.date as date, code, invoice_details_tax.description as description
                        from payment 
                        inner join invoice on invoice.id = payment.invoice_id
                        inner join invoice_details_tax on invoice.id  = invoice_details_tax.invoice_id
                        inner join trade_type on trade_type.id = invoice.trade_type                
                        inner join payment_mode on payment_mode.id = payment.mode
                    UNION
                    select supplementary_invoice_details_tax.cost as cost, tax_type, payment_mode.name as mode, supplementary_payment.date as date, code, supplementary_invoice_details_tax.description
                        from supplementary_payment 
                    inner join supplementary_invoice on supplementary_invoice.id = supplementary_payment.invoice_id    
                        inner join invoice on invoice.id = supplementary_invoice.invoice_id
                        inner join trade_type on trade_type.id = invoice.trade_type  
                        inner join supplementary_invoice_details_tax on supplementary_invoice.id  = supplementary_invoice_details_tax.invoice_id
                        inner join payment_mode on payment_mode.id = supplementary_payment.mode
                                    ) as view 
                        where tax_type like ? and code like ? and cast(date as date) between ? and ? and mode like ?
                        and description like \"GET Fund%\" ");
        $query->bind =  array('sssss', &$this->tax_type, &$this->trade_type, &$this->start_date, &$this->end_date, &$this->payment_mode);
        $run =  $query->run();
        $get_fund = $run->fetch_num()[0] ?? "0.00";

        $query->query("select sum(cost) from 
                  (select invoice_details_tax.cost as cost, tax_type, payment_mode.name as mode, payment.date as date, code, invoice_details_tax.description as description
                        from payment 
                        inner join invoice on invoice.id = payment.invoice_id
                        inner join invoice_details_tax on invoice.id  = invoice_details_tax.invoice_id
                        inner join trade_type on trade_type.id = invoice.trade_type                
                        inner join payment_mode on payment_mode.id = payment.mode
                    UNION
                    select supplementary_invoice_details_tax.cost as cost, tax_type, payment_mode.name as mode, supplementary_payment.date as date, code, supplementary_invoice_details_tax.description
                        from supplementary_payment 
                    inner join supplementary_invoice on supplementary_invoice.id = supplementary_payment.invoice_id    
                        inner join invoice on invoice.id = supplementary_invoice.invoice_id
                        inner join trade_type on trade_type.id = invoice.trade_type  
                        inner join supplementary_invoice_details_tax on supplementary_invoice.id  = supplementary_invoice_details_tax.invoice_id
                        inner join payment_mode on payment_mode.id = supplementary_payment.mode
                                    ) as view 
                        where tax_type like ? and code like ? and cast(date as date) between ? and ? and mode like ?
    and description like \"VAT%\"");
        $query->bind =  array('sssss', &$this->tax_type, &$this->trade_type, &$this->start_date, &$this->end_date, &$this->payment_mode);
        $run =  $query->run();
        $vat = $run->fetch_num()[0] ?? "0.00";

        $query->query("select sum(cost) from 
                  (select invoice_details_tax.cost as cost, tax_type, payment_mode.name as mode, payment.date as date, code, invoice_details_tax.description as description
                        from payment 
                        inner join invoice on invoice.id = payment.invoice_id
                        inner join invoice_details_tax on invoice.id  = invoice_details_tax.invoice_id
                        inner join trade_type on trade_type.id = invoice.trade_type                
                        inner join payment_mode on payment_mode.id = payment.mode
                    UNION
                    select supplementary_invoice_details_tax.cost as cost, tax_type, payment_mode.name as mode, supplementary_payment.date as date, code, supplementary_invoice_details_tax.description
                        from supplementary_payment 
                    inner join supplementary_invoice on supplementary_invoice.id = supplementary_payment.invoice_id    
                        inner join invoice on invoice.id = supplementary_invoice.invoice_id
                        inner join trade_type on trade_type.id = invoice.trade_type  
                        inner join supplementary_invoice_details_tax on supplementary_invoice.id  = supplementary_invoice_details_tax.invoice_id
                        inner join payment_mode on payment_mode.id = supplementary_payment.mode
                                    ) as view 
                        where tax_type like ? and code like ? and cast(date as date) between ? and ? and mode like ?
    and description like \"NHIL%\"");
        $query->bind =  array('sssss', &$this->tax_type, &$this->trade_type, &$this->start_date, &$this->end_date, &$this->payment_mode);
        $run =  $query->run();
        $nhil = $run->fetch_num()[0] ?? "0.00";

        $query->query("select sum(cost) from 
                  (select invoice_details_tax.cost as cost, tax_type, payment_mode.name as mode, payment.date as date, code, invoice_details_tax.description as description
                        from payment 
                        inner join invoice on invoice.id = payment.invoice_id
                        inner join invoice_details_tax on invoice.id  = invoice_details_tax.invoice_id
                        inner join trade_type on trade_type.id = invoice.trade_type                
                        inner join payment_mode on payment_mode.id = payment.mode
                    UNION
                    select supplementary_invoice_details_tax.cost as cost, tax_type, payment_mode.name as mode, supplementary_payment.date as date, code, supplementary_invoice_details_tax.description
                        from supplementary_payment 
                    inner join supplementary_invoice on supplementary_invoice.id = supplementary_payment.invoice_id    
                        inner join invoice on invoice.id = supplementary_invoice.invoice_id
                        inner join trade_type on trade_type.id = invoice.trade_type  
                        inner join supplementary_invoice_details_tax on supplementary_invoice.id  = supplementary_invoice_details_tax.invoice_id
                        inner join payment_mode on payment_mode.id = supplementary_payment.mode
                                    ) as view 
                        where tax_type like ? and code like ? and cast(date as date) between ? and ? and mode like ?
    and description like \"Covid-19%\"");
        $query->bind =  array('sssss', &$this->tax_type, &$this->trade_type, &$this->start_date, &$this->end_date, &$this->payment_mode);
        $run =  $query->run();
        $covid = $run->fetch_num()[0] ?? "0.00";

        $query->query("select sum(waiver_amount) from 
                (select invoice.waiver_amount as waiver_amount, tax_type, payment_mode.name as mode, payment.date as date, code from payment 
                left join invoice on invoice.id = payment.invoice_id left join tax_type on tax_type.id = invoice.tax_type 
                left join trade_type on trade_type.id = invoice.trade_type left join payment_mode on payment_mode.id = payment.mode
                UNION
                select supplementary_invoice.waiver_amount as waiver_amount, tax_type, payment_mode.name as mode, supplementary_payment.date as date, code from supplementary_payment 
                left join supplementary_invoice on supplementary_invoice.id = supplementary_payment.invoice_id 
                left join invoice on invoice.id = supplementary_invoice.invoice_id left join tax_type on tax_type.id = invoice.tax_type 
                left join trade_type on trade_type.id = invoice.trade_type left join payment_mode on payment_mode.id = supplementary_payment.mode
                ) as view 
                where tax_type like ? and code like ? and cast(date as date) between ? and ? and mode like ?");
        $query->bind =  array('sssss', &$this->tax_type, &$this->trade_type, &$this->start_date, &$this->end_date, &$this->payment_mode);
        $run =  $query->run();
        $waiver_amount_total = $run->fetch_num()[0] ?? "0.00";

        $query->query("select sum(waiver_percent) from (select invoice.waiver_pct as waiver_percent, tax_type, payment_mode.name as mode, payment.date as date, code from payment 
                left join invoice on invoice.id = payment.invoice_id 
                left join tax_type on tax_type.id = invoice.tax_type 
                left join trade_type on trade_type.id = invoice.trade_type 
                left join payment_mode on payment_mode.id = payment.mode
                UNION
                select supplementary_invoice.waiver_pct as waiver_percent, tax_type, payment_mode.name as mode, supplementary_payment.date as date, code from supplementary_payment 
                left join supplementary_invoice on supplementary_invoice.id = supplementary_payment.invoice_id 
                left join invoice on invoice.id = supplementary_invoice.invoice_id 
                left join tax_type on tax_type.id = invoice.tax_type 
                left join trade_type on trade_type.id = invoice.trade_type 
                left join payment_mode on payment_mode.id = supplementary_payment.mode
                ) as view 
                where tax_type like ? and code like ? and cast(date as date) between ? and ? and mode like ?");
        $query->bind =  array('sssss', &$this->tax_type, &$this->trade_type, &$this->start_date, &$this->end_date, &$this->payment_mode);
        $run =  $query->run();
        $waiver_percent = $run->fetch_num()[0] ?? "0.00";

        $query->query("select sum(cost) from 
        (select invoice_details_tax.cost as cost, tax_type, payment_mode.name as mode, payment.date as date, code, invoice_details_tax.description as description
              from payment 
              left join invoice on invoice.id = payment.invoice_id
              left join invoice_details_tax on invoice.id  = invoice_details_tax.invoice_id
              left join trade_type on trade_type.id = invoice.trade_type                
              left join payment_mode on payment_mode.id = payment.mode
          UNION
          select supplementary_invoice_details_tax.cost as cost, tax_type, payment_mode.name as mode, supplementary_payment.date as date, code, supplementary_invoice_details_tax.description
              from supplementary_payment 
          inner join supplementary_invoice on supplementary_invoice.id = supplementary_payment.invoice_id    
              left join invoice on invoice.id = supplementary_invoice.invoice_id
              left join trade_type on trade_type.id = invoice.trade_type  
              left join supplementary_invoice_details_tax on supplementary_invoice.id  = supplementary_invoice_details_tax.invoice_id
              left join payment_mode on payment_mode.id = supplementary_payment.mode
                          ) as view 
              where tax_type like ? and code like ? and cast(date as date) between ? and ? and mode like ?");
              $query->bind =  array('sssss', &$this->tax_type, &$this->trade_type, &$this->start_date, &$this->end_date, &$this->payment_mode);
              $run =  $query->run();
              $total_tax_cost = $run->fetch_num()[0] ?? "0.00";

        $query->query("select sum(sub_total) from (select invoice.cost as sub_total, tax_type, payment_mode.name as mode, payment.date as date, code from payment 
        left join invoice on invoice.id = payment.invoice_id 
        left join tax_type on tax_type.id = invoice.tax_type 
        left join trade_type on trade_type.id = invoice.trade_type 
        left join payment_mode on payment_mode.id = payment.mode
        UNION
        select supplementary_invoice.cost as sub_total, tax_type, payment_mode.name as mode, supplementary_payment.date as date, code from supplementary_payment 
        left join supplementary_invoice on supplementary_invoice.id = supplementary_payment.invoice_id 
        left join invoice on invoice.id = supplementary_invoice.invoice_id 
        left join tax_type on tax_type.id = invoice.tax_type 
        left join trade_type on trade_type.id = invoice.trade_type 
        left join payment_mode on payment_mode.id = supplementary_payment.mode
        ) as view 
        where tax_type like ? and code like ? and cast(date as date) between ? and ? and mode like ?");
        $query->bind =  array('sssss', &$this->tax_type, &$this->trade_type, &$this->start_date, &$this->end_date, &$this->payment_mode);
        $run =  $query->run();
        $sub_total_cost = $run->fetch_num()[0] ?? "0.00";

        $query->query("select sum(cost) from 
        (select invoice_details.cost as cost, tax_type, payment_mode.name as mode, payment.date as date, code, invoice_details.product_key as description
              from payment 
              inner join invoice on invoice.id = payment.invoice_id
              inner join invoice_details on invoice.id  = invoice_details.invoice_id
              inner join trade_type on trade_type.id = invoice.trade_type                
              inner join payment_mode on payment_mode.id = payment.mode
          UNION
          select supplementary_invoice_details.cost as cost, tax_type, payment_mode.name as mode, supplementary_payment.date as date, code, supplementary_invoice_details.product_key
              from supplementary_payment 
          inner join supplementary_invoice on supplementary_invoice.id = supplementary_payment.invoice_id    
              inner join invoice on invoice.id = supplementary_invoice.invoice_id
              inner join trade_type on trade_type.id = invoice.trade_type  
              inner join supplementary_invoice_details on supplementary_invoice.id  = supplementary_invoice_details.invoice_id
              inner join payment_mode on payment_mode.id = supplementary_payment.mode
                          ) as view 
              where tax_type like ? and code like ? and cast(date as date) between ? and ? and mode like ?
            and description = 1");
            $query->bind =  array('sssss', &$this->tax_type, &$this->trade_type, &$this->start_date, &$this->end_date, &$this->payment_mode);
            $run =  $query->run();
        $handling = $run->fetch_num()[0] ?? "0.00";

        $query->query("select sum(cost) from 
        (select invoice_details.cost as cost, tax_type, payment_mode.name as mode, payment.date as date, code, invoice_details.product_key as description
              from payment 
              inner join invoice on invoice.id = payment.invoice_id
              inner join invoice_details on invoice.id  = invoice_details.invoice_id
              inner join trade_type on trade_type.id = invoice.trade_type                
              inner join payment_mode on payment_mode.id = payment.mode
          UNION
          select supplementary_invoice_details.cost as cost, tax_type, payment_mode.name as mode, supplementary_payment.date as date, code, supplementary_invoice_details.product_key
              from supplementary_payment 
          inner join supplementary_invoice on supplementary_invoice.id = supplementary_payment.invoice_id    
              inner join invoice on invoice.id = supplementary_invoice.invoice_id
              inner join trade_type on trade_type.id = invoice.trade_type  
              inner join supplementary_invoice_details on supplementary_invoice.id  = supplementary_invoice_details.invoice_id
              inner join payment_mode on payment_mode.id = supplementary_payment.mode
                          ) as view 
              where tax_type like ? and code like ? and cast(date as date) between ? and ? and mode like ?
            and description = 2");
            $query->bind =  array('sssss', &$this->tax_type, &$this->trade_type, &$this->start_date, &$this->end_date, &$this->payment_mode);
            $run =  $query->run();
        $transfer = $run->fetch_num()[0] ?? "0.00";

        $query->query("select sum(cost) from 
        (select invoice_details.cost as cost, tax_type, payment_mode.name as mode, payment.date as date, code, depot_activity.name as description
              from payment 
              inner join invoice on invoice.id = payment.invoice_id
              inner join invoice_details on invoice.id  = invoice_details.invoice_id
              inner join trade_type on trade_type.id = invoice.trade_type                
              inner join payment_mode on payment_mode.id = payment.mode
                 inner join depot_activity on depot_activity.id = invoice_details.product_key
          UNION
          select supplementary_invoice_details.cost as cost, tax_type, payment_mode.name as mode, supplementary_payment.date as date, code, depot_activity.name as product_key
              from supplementary_payment 
          inner join supplementary_invoice on supplementary_invoice.id = supplementary_payment.invoice_id    
              inner join invoice on invoice.id = supplementary_invoice.invoice_id
              inner join trade_type on trade_type.id = invoice.trade_type  
              inner join supplementary_invoice_details on supplementary_invoice.id  = supplementary_invoice_details.invoice_id
              inner join payment_mode on payment_mode.id = supplementary_payment.mode
                 inner join depot_activity on depot_activity.id = supplementary_invoice_details.product_key
                          ) as view 
              where tax_type like ? and code like ? and cast(date as date) between ? and ? and mode like ?
            and description like 'Partial Unstu%'");
            $query->bind =  array('sssss', &$this->tax_type, &$this->trade_type, &$this->start_date, &$this->end_date, &$this->payment_mode);
            $run =  $query->run();
        $partial_unstuff = $run->fetch_num()[0] ?? "0.00";

        $query->query("select sum(cost) from 
        (select invoice_details.cost as cost, tax_type, payment_mode.name as mode, payment.date as date, code, invoice_details.product_key as description
              from payment 
              inner join invoice on invoice.id = payment.invoice_id
              inner join invoice_details on invoice.id  = invoice_details.invoice_id
              inner join trade_type on trade_type.id = invoice.trade_type                
              inner join payment_mode on payment_mode.id = payment.mode
          UNION
          select supplementary_invoice_details.cost as cost, tax_type, payment_mode.name as mode, supplementary_payment.date as date, code, supplementary_invoice_details.product_key
              from supplementary_payment 
          inner join supplementary_invoice on supplementary_invoice.id = supplementary_payment.invoice_id    
              inner join invoice on invoice.id = supplementary_invoice.invoice_id
              inner join trade_type on trade_type.id = invoice.trade_type  
              inner join supplementary_invoice_details on supplementary_invoice.id  = supplementary_invoice_details.invoice_id
              inner join payment_mode on payment_mode.id = supplementary_payment.mode
                          ) as view 
              where tax_type like ? and code like ? and cast(date as date) between ? and ? and mode like ?
            and description = 7");
            $query->bind =  array('sssss', &$this->tax_type, &$this->trade_type, &$this->start_date, &$this->end_date, &$this->payment_mode);
            $run =  $query->run();
        $unstuff = $run->fetch_num()[0] ?? "0.00";

        $query->query("select sum(cost) from 
        (select invoice_details.cost as cost, tax_type, payment_mode.name as mode, payment.date as date, code, invoice_details.product_key as description
              from payment 
              inner join invoice on invoice.id = payment.invoice_id
              inner join invoice_details on invoice.id  = invoice_details.invoice_id
              inner join trade_type on trade_type.id = invoice.trade_type                
              inner join payment_mode on payment_mode.id = payment.mode
          UNION
          select supplementary_invoice_details.cost as cost, tax_type, payment_mode.name as mode, supplementary_payment.date as date, code, supplementary_invoice_details.product_key
              from supplementary_payment 
          inner join supplementary_invoice on supplementary_invoice.id = supplementary_payment.invoice_id    
              inner join invoice on invoice.id = supplementary_invoice.invoice_id
              inner join trade_type on trade_type.id = invoice.trade_type  
              inner join supplementary_invoice_details on supplementary_invoice.id  = supplementary_invoice_details.invoice_id
              inner join payment_mode on payment_mode.id = supplementary_payment.mode
                          ) as view 
              where tax_type like ? and code like ? and cast(date as date) between ? and ? and mode like ?
            and description not in (1,2,7,62,63,64,65,67,68,86,'S')");
            $query->bind =  array('sssss', &$this->tax_type, &$this->trade_type, &$this->start_date, &$this->end_date, &$this->payment_mode);
            $run =  $query->run();
        $ancillary_cost = $run->fetch_num()[0] ?? "0.00";

        $query->query("select sum(cost) from 
        (select invoice_details.cost as cost, tax_type, payment_mode.name as mode, payment.date as date, code, invoice_details.product_key as description
              from payment 
              inner join invoice on invoice.id = payment.invoice_id
              inner join invoice_details on invoice.id  = invoice_details.invoice_id
              inner join trade_type on trade_type.id = invoice.trade_type                
              inner join payment_mode on payment_mode.id = payment.mode
          UNION
          select supplementary_invoice_details.cost as cost, tax_type, payment_mode.name as mode, supplementary_payment.date as date, code, supplementary_invoice_details.product_key
              from supplementary_payment 
          inner join supplementary_invoice on supplementary_invoice.id = supplementary_payment.invoice_id    
              inner join invoice on invoice.id = supplementary_invoice.invoice_id
              inner join trade_type on trade_type.id = invoice.trade_type  
              inner join supplementary_invoice_details on supplementary_invoice.id  = supplementary_invoice_details.invoice_id
              inner join payment_mode on payment_mode.id = supplementary_payment.mode
                          ) as view 
              where tax_type like ? and code like ? and cast(date as date) between ? and ? and mode like ?
            and description = 'S'");
            $query->bind =  array('sssss', &$this->tax_type, &$this->trade_type, &$this->start_date, &$this->end_date, &$this->payment_mode);
            $run =  $query->run();
        $storage_cost = $run->fetch_num()[0] ?? "0.00";

        $query->query("select count(distinct(qty)) from 
        (select invoice_details.container_id as qty, tax_type, payment_mode.name as mode, payment.date as date, code
              from payment 
              inner join invoice on invoice.id = payment.invoice_id
              inner join invoice_details on invoice.id  = invoice_details.invoice_id
              inner join trade_type on trade_type.id = invoice.trade_type                
              inner join payment_mode on payment_mode.id = payment.mode
                 inner join depot_activity on depot_activity.id = invoice_details.product_key
          UNION
          select supplementary_invoice_details.container_id as qty, tax_type, payment_mode.name as mode, supplementary_payment.date as date, code
              from supplementary_payment 
          inner join supplementary_invoice on supplementary_invoice.id = supplementary_payment.invoice_id    
              inner join invoice on invoice.id = supplementary_invoice.invoice_id
              inner join trade_type on trade_type.id = invoice.trade_type  
              inner join supplementary_invoice_details on supplementary_invoice.id  = supplementary_invoice_details.invoice_id
              inner join payment_mode on payment_mode.id = supplementary_payment.mode
                 inner join depot_activity on depot_activity.id = supplementary_invoice_details.product_key
                          ) as view 
              where tax_type like ? and code like ? and cast(date as date) between ? and ? and mode like ?");
            $query->bind =  array('sssss', &$this->tax_type, &$this->trade_type, &$this->start_date, &$this->end_date, &$this->payment_mode);
            $run =  $query->run();
        $container_qty = $run->fetch_num()[0] ?? "0";

        $totals_array = array();
        $totals_array['paid'] = number_format($sum,2);
        $totals_array['getF'] = number_format($get_fund,2);
        $totals_array['vat'] = number_format($vat,2);
        $totals_array['nhil'] = number_format($nhil,2);
        $totals_array['covid'] = number_format($covid,2);
        $totals_array['wamt'] = number_format($waiver_amount_total,2);
        $totals_array['wpct'] = number_format($waiver_percent,2);
        $totals_array['tax'] = number_format($total_tax_cost,2);
        $totals_array['cost'] = number_format($sub_total_cost,2);
        $totals_array['hand'] = number_format($handling,2);
        $totals_array['transfer'] = number_format($transfer,2);
        $totals_array['partst'] = number_format($partial_unstuff,2);
        $totals_array['unstu'] = number_format($unstuff,2);
        $totals_array['ancilar'] = number_format($ancillary_cost,2);
        $totals_array['stor'] = number_format($storage_cost,2);
        $totals_array['qty'] = $container_qty;
        return $totals_array;
    }
}
?>