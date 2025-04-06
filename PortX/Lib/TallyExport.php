<?php

namespace Lib;
use XMLWriter;

class TallyExport {

    private $data;
    private $payments;
    private $start_date;
    private $end_date;
    private $date;
    private $type;
    private $status;
    private $to_file;

    public function __construct() {
        $this->date = date("Y-m-d H:i:s");
    }

    function export($start_date, $end_date, $type, $status) {
        $this->type = $type;
        $this->data = array();
        $this->start_date = $start_date;
        $this->end_date = $end_date;
        $this->status = $status;
        $this->payments = array();
        $this->getData();
        $this->writeData();
    }

    function exportFile($start_date, $end_date, $type, $status) {
        $this->to_file= true;
        $this->type = $type;
        $this->data = array();
        $this->start_date = $start_date;
        $this->end_date = $end_date;
        $this->status = $status;
        $this->payments = array();
        $this->getData();
        $this->writeData();
    }

    function getData() {
        $query = new MyTransactionQuery();

        $query->query("select id, name from trade_type");
        $result = $query->run();
        $trade_types = array();
        if ($result->num_rows() > 0) {
            while ($row = $result->fetch_assoc()) {
                $trade_types[$row["id"]] = $row["name"];
            }
        }

        $query->query("select id, name from tax_type");
        $result = $query->run();
        $tax_types = array();
        if ($result->num_rows() > 0) {
            while ($row = $result->fetch_assoc()) {
                $tax_types[$row["id"]] = $row["name"];
            }
        }

        $query->query("select id, code from currency");
        $result = $query->run();
        $currencies = array();
        if ($result->num_rows() > 0) {
            while ($row = $result->fetch_assoc()) {
                $currencies[$row["id"]] = $row["code"];
            }
        }

        if($this->type == "invoice") {
            $query->query("select * from invoice where cast(date as date) between ?  and  ? and status like ?");
            $query->bind = array('sss', &$this->start_date, &$this->end_date, &$this->status);
            $invoice_result = $query->run();
            if ($result->num_rows() > 0) {
                while ($row = $invoice_result->fetch_assoc()) {
                    $details = array();
                    $details["id"] = $row["id"];
                    $details["trade_type"] = $trade_types[$row["trade_type"]];
                    $details["number"] = $row["number"];
                    $details["bl_number"] = $row["bl_number"];
                    $details["book_number"] = $row["book_number"];
                    $details["do_number"] = $row["do_number"];
                    $details["bill_date"] = $row["bill_date"];
                    $details["due_date"] = $row["due_date"];
                    $details["waiver_amount"] = $row["waiver_amount"];
                    $details["waiver_percentage"] = $row["waiver_pct"];
                    $id = $row["id"];

                    $invoice_query = new MyTransactionQuery();
                    $invoice_query->query("select description, name,cost, qty, total_cost, product_key  from invoice_details left join depot_activity on depot_activity.id = invoice_details.product_key where invoice_id = $id");
                    $result = $invoice_query->run();
                    $invoice_items = array();

                    if ($result->num_rows() > 0) {
                        while ($row_details = $result->fetch_assoc()) {
                            $invoice_item = array();
                            $name = $row_details["name"];
                            $product = $row_details["product_key"];

                            if ($product == 'S') {
                                $name = "Storage";
                            } else if ($product == "M") {
                                $name = "Monitoring";
                            } else if ($product == "U") {
                                $name = "UCL";
                            }
                            if($name == "Additional Handling") {
                                $name = "AdditionalHandling";
                            }

                            $invoice_item["invoice_item_description"] = html_entity_decode($name);
                            $invoice_item["invoice_item_unit_cost"] = $row_details["cost"];
                            $invoice_item["invoice_item_qty"] = $row_details["qty"];
                            $invoice_item["invoice_item_total_cost"] = $row_details["total_cost"];

                            array_push($invoice_items, $invoice_item);
                        }
                    }

                    $details["invoice_items"] = $invoice_items;
                    $details["invoice_total_cost"] = $row["cost"];
                    $details["tax_type"] = $tax_types[$row["tax_type"]];

                    $invoice_query->query("select description,rate,cost from invoice_details_tax where invoice_id = $id");
                    $result = $invoice_query->run();
                    $tax_items = array();

                    if ($result->num_rows() > 0) {
                        while ($row_details = $result->fetch_assoc()) {
                            $tax_item = array();

                            $tax_item["tax_description"] = html_entity_decode($row_details["description"]);
                            $tax_item["tax_rate"] = $row_details["rate"];
                            $tax_item["tax_cost"] = $row_details["cost"];

                            array_push($tax_items, $tax_item);
                        }
                    }

                    $details["tax_items"] = $tax_items;
                    $details["tax_total_cost"] = $row["tax"];
                    $details["grand_total"] = $row["tax"] + $row["cost"];
                    $details["currency"] = $currencies[$row["currency"]];
                    $details["note"] = html_entity_decode($row["note"]);
                    $customer = $row["customer_id"];
                    $invoice_query->query("select code,name from customer where id = $customer");
                    $result = $invoice_query->run();

                    if ($result->num_rows() > 0) {
                        $customer_row = $result->fetch_assoc();
                        $details["customer_code"] = html_entity_decode($customer_row["code"]);
                        $details["customer_name"] = html_entity_decode($customer_row["name"]);
                    }

                    $details["date"] = $row["date"];
                    $details["status"] = $row["status"];

                    array_push($this->data, $details);

                    $invoice_query->commit();
                }
            }
        }
        else if($this->type == "payment") {
            $query->query("select payment.id,receipt_number,number,paid,outstanding,name,payment.date,pdate from payment left join invoice on invoice.id = payment.invoice_id LEFT JOIN customer on invoice.customer_id = customer.id where cast(payment.date as date) >= ?  and cast(payment.date as date) <= ?");
            $query->bind = array('ss', &$this->start_date, &$this->end_date);
            $result = $query->run();

            if ($result->num_rows() > 0) {
                while ($row_details = $result->fetch_assoc()) {
                    $payment = array();
                    $payment["id"] = $row_details["id"];
                    $payment["receipt_number"] = $row_details["receipt_number"];
                    $payment["invoice_number"] = $row_details["number"];
                    $payment["paid"] = $row_details["paid"];
                    $payment["outstanding"] = $row_details["outstanding"];
                    $payment["customer_name"] = html_entity_decode($row_details["name"]);
                    $payment["date"] = $row_details["date"];
                    $payment["pdate"] = $row_details["pdate"];

                    array_push($this->payments, $payment);
                }
            }
        }
    }

    function writeData() {
        $xw = new XMLWriter();
        header("Content-type: text/xml");
        if($this->to_file) {
            header("Content-disposition: attachment; filename=\"$this->type-$this->date.xml\"");
        }

        $xw->openUri("php://output");

        $xw->setIndent(true);
        $xw->startDocument("1.0");
        $xw->startElement("database");
        $xw->startAttribute("name");
        $xw->text("portx");
        $xw->endAttribute();
        $xw->startElement("date_generated");
        $xw->text($this->date);
        $xw->endElement();
        $xw->startElement("start_date");
        $xw->text($this->start_date);
        $xw->endElement();
        $xw->startElement("end_date");
        $xw->text($this->end_date);
        $xw->endElement();

        if ($this->type == "invoice") {
            foreach ($this->data as $record) {
                $xw->startElement("table");
                $xw->startAttribute("name");
                $xw->text("invoice");
                $xw->endAttribute();
                foreach ($record as $key => $value) {
                    if ($key === "invoice_items") {
                        foreach ($value as $item_array) {
                            foreach ($item_array as $invoice_item_key => $invoice_item_value) {
                                $xw->startElement("column");
                                $xw->startAttribute("name");
                                $xw->text($invoice_item_key);
                                $xw->endAttribute();
                                $xw->text($invoice_item_value);
                                $xw->endElement();
                            }
                        }
                    } elseif ($key === "tax_items") {
                        foreach ($value as $item_array) {
                            foreach ($item_array as $tax_item_key => $tax_item_value) {
                                $xw->startElement("column");
                                $xw->startAttribute("name");
                                $xw->text($tax_item_key);
                                $xw->endAttribute();
                                $xw->text($tax_item_value);
                                $xw->endElement();
                            }
                        }
                    } else {
                        $xw->startElement("column");
                        $xw->startAttribute("name");
                        $xw->text($key);
                        $xw->endAttribute();
                        $xw->text($value);
                        $xw->endElement();
                    }
                }
                $xw->endElement();
            }
        }
        else if ($this->type == "payment") {
            foreach ($this->payments as $payment) {
                $xw->startElement("table");
                $xw->startAttribute("name");
                $xw->text("payment");
                $xw->endAttribute();
                foreach ($payment as $key => $value) {
                    $xw->startElement("column");
                    $xw->startAttribute("name");
                    $xw->text($key);
                    $xw->endAttribute();
                    $xw->text($value);
                    $xw->endElement();
                }
                $xw->endElement();
            }
        }

        $xw->endElement();
        $xw->endDocument();
        $xw->flush();
    }
}

?>