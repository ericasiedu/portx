<?php
use Lib\ACL,
    Lib\BillTansaction;
$system_object="invoice-reports";
ACl::verifyRead($system_object);
$title = 'Invoice Reports';
$reports = 'active open';
$invoice_reports = 'active';
require_once 'includes/header.php';
require_once 'includes/preloaders.php';
require_once 'includes/aside.php';
require_once 'includes/top-header.php';
$invoice_transaction = new BillTansaction();
date_default_timezone_set('UTC');

?>
<main>
    <div class="main-content">
        <div class="card">
            <h4 class="card-title invo_filter"><strong>Invoice </strong><span class="title_card">Reports</span><span class="invoice_filter">Start Date <input type="date" value="<?php echo date('Y-m-d'); ?>" id="start_date" />
                </span> <span class="invoice_filter">End Date <input type="date" value="<?php echo date('Y-m-d'); ?>" id="end_date" /></span>

                Invoice Status
            <select id="invoice_status"  class="custom-select">
                <option value="ALL">ALL</option>
                <option value="CANCELLED">CANCELLED</option>
                <option value="RECALLED">RECALLED</option>
                <option value="DEFERRED">DEFERRED</option>
                <option value="EXPIRED">EXPIRED</option>
                <option value="WAIVED">WAIVED</option>
            </select>
            </h4>
            <h4 class="card-title invo_filt">
                <span class="invoice_filter">
                    Payment Status
                    <select id="payment_status" class="custom-select">
                        <option value="ALL">ALL</option>
                        <option value="PAID">PAID</option>
                        <option value="UNPAID">UNPAID</option>
                    </select>
                </span>

                <span class="invoice_filter">
                    Trade Type
                    <select id="trade_type" class="custom-select">
                        <option value="ALL">ALL</option>
                        <option value="1">IMPORT</option>
                        <option value="4">EXPORT</option>
                        <option value="8">EMPTY</option>
                        <option value="3">TRANSIT</option>
                    </select>
                </span>
                
               Tax Type
               <select id="tax_type" class="custom-select">
                   <option value="ALL">ALL</option>
                   <?php

                       foreach ($invoice_transaction->getTaxList() as $tax_list) { ?>
                           <option value="<?= $tax_list[0] ?>"><?= $tax_list[1]; ?></option>
                       <?php }

                       ?>
               </select>
            </h4>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12 table-responsive">

                        <table id="invoice_reports" class="display table">
                            <thead>
                            <th>Trade Type</th>
                            <th>Number</th>
                            <th>BL Number</th>
                            <th>DO Number</th>
                            <th>Bill Date</th>
                            <th>Due Date</th>
                            <th>Handling Cost</th>
                            <th>Transfer Cost</th>
                            <th>Partial Unstuffing Cost</th>
                            <th>Unstuffing Cost</th>
                            <th>Ancillary Charges</th>
                            <th>Storage Cost</th>
                            <th>GETFund</th>
                            <th>Covid-19 Levy</th>
                            <th>NHIL</th>
                            <th>VAT</th>
                            <th>Number of Container(s)</th>
                            <th>Total</th>
                            <th>Waiver Pct</th>
                            <th>Waiver Amount</th>
                            <th>Tax Type</th>
                            <th>Note(s)</th>
                            <th>Customer</th>
                            <th>Waiver By</th>
                            <th>Cancelled By</th>
                            <th>Deferred By</th>
                            <th>Created By</th>
                            <th>Date</th>
                            <th>Status</th>
                            </thead>
                            <tfoot>
                            <tr>
                                <th colspan="1"> </th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                            </tr>
                            </tfoot>
                        </table>

                    </div>
                </div>
            </div>
            <?php

            require_once 'includes/footer.php';

            ?>

            <script>
                $(document).ready(function() {
                    system_object='<?php echo $system_object?>';
                    InvoiceReports.iniTable();
                });
            </script>