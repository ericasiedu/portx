<?php
use Lib\ACL,
    Lib\BillTansaction;
$system_object="summary-remittances";
ACl::verifyRead($system_object);
$title = 'Summary Remittances';
$reports = 'active open';
$summary_remittances = 'active';
require_once 'includes/header.php';
require_once 'includes/preloaders.php';
require_once 'includes/aside.php';
require_once 'includes/top-header.php';
$invoice_transaction = new BillTansaction();

?>
<main>
    <div class="main-content">
        <div class="card">
            <h4 class="card-title invo_filter"><strong>Summary </strong><span class="title_card">Remittances</span><span class="invoice_filter">Start Date <input type="date" value="<?php echo date('Y-m-d'); ?>" id="start_date" />
                </span> <span class="invoice_filter">End Date <input type="date" value="<?php echo date('Y-m-d'); ?>" id="end_date" /></span>

                <!-- Invoice Status
            <select id="invoice_status"  class="custom-select">
                <option value="ALL">ALL</option>
                <option value="CANCELLED">CANCELLED</option>
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
                </span> -->

                <!-- <span class="invoice_filter">
                    Trade Type
                    <select id="trade_type" class="custom-select">
                        <option value="ALL">ALL</option>
                        <option value="1">IMPORT</option>
                        <option value="4">EXPORT</option>
                    </select>
                </span> -->
                
               <!-- Tax Type
               <select id="tax_type" class="custom-select">
                   <option value="ALL">ALL</option>
                   <?php

                       foreach ($invoice_transaction->getTaxList() as $tax_list) { ?>
                           <option value="<?= $tax_list[0] ?>"><?= $tax_list[1]; ?></option>
                       <?php }

                       ?>
               </select> -->
            </h4>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12 table-responsive">

                        <table id="summary_remittance" class="display table">
                            <thead>
                            <th>Payment Date</th>
                            <th>Invoice Number</th>
                            <th>TEU</th>
                            <th>Stripping</th>
                            <th>D/D</th>
                            <th>Handling</th>
                            <th>Storage</th>
                            <th>Transport</th>
                            <th>Amt Waived</th>
                            <th>Amount</th> 
                            <th>VAT</th><!--10-->
                            <th>COVID-19</th>
                            <th>WHT</th>
                            <th>GETFUND</th>
                            <th>Cash/Cheque</th>
                            <th>Amt Paid</th>
                            <th>Balance</th>
                            <th>Shipping Line</th>
                            <th>Vessel Name</th>
                            <th>Consignee</th>
                            </thead>
                            <tfoot>
                                <tr>
                                    <th colspan="1"></th>
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
                    SummaryRemittance.iniTable();
                });
            </script>