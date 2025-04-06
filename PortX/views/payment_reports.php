<?php

use Lib\ACL;

$system_object = "payment-reports";
ACl::verifyRead($system_object);
$title = 'Payment Reports';
$reports = 'active open';
$payment_report_active = 'active';
require_once 'includes/header.php';
require_once 'includes/preloaders.php';
require_once 'includes/aside.php';
require_once 'includes/top-header.php';
date_default_timezone_set('UTC');
?>
<main>
    <div class="main-content">
        <div class="card">
            <h4 class="card-title pay_line"><strong>Payment </strong> Reports
                <span class="payment">Start Date <input type="date" value="<?php echo date('Y-m-d'); ?>" id="start_date"/></span>
                <span class="payment">End Date <input type="date" value="<?php echo date('Y-m-d'); ?>" id="end_date"/></span>

            <span class="payment">Payment Mode <select class="custom-select"
                        id="payment_mode">
                    <option value="%">All</option>
                    <option value="Cash">Cash</option>
                    <option value="Cheque">Cheque</option>
                </select>
            </span>
            </h4>
            <h4 class="card-title">
            Trade Type <select class="custom-select"
                                            id="trade_type">
                    <option value="%">All</option>
                    <option value="11">Import</option>
                    <option value="21">Export</option>
                    <option value="70">Empty</option>
                </select>
                </select>Tax Type <select class="custom-select"
                                            id="tax_type">
                    <option value="%">All</option>
                    <option value="1">Simple</option>
                    <option value="2">Compound</option>
                    <option value="3">Total Exempt</option>
                    <option value="4">VAT Exempt</option>
                </select></h4>
            <div class="card-body">
                <div id="depotForm">
                    <editor-field name="name"></editor-field>
                    <editor-field name="billable"></editor-field>
                </div>
                <div class="row">
                    <div class="col-md-12 table-responsive">
                        <table id="payment_report" class="display table">
                            <thead>
                                <tr>
                                    <th>Receipt No.</th>
                                    <th>Invoice No.</th>
                                    <th>Invoice Date.</th>
                                    <th>Waiver Pct</th>
                                    <th>Waiver Amount</th>
                                    <th>Tax Type</th>
                                    <th>Handling Cost</th>
                                    <th>Transfer Cost</th>
                                    <th>Partial Unstuffing Cost</th>
                                    <th>Unstuffing Cost</th>
                                    <th>Storage Cost</th>
                                    <th>Ancillary Charges</th>
                                    <th>Number of Container(s)</th>
                                    <th>GETFUND</th>
                                    <th>VAT</th>
                                    <th>NHIL</th>
                                    <th>Covid-19 Levy</th>
                                    <th>Tax</th>
                                    <th>Total</th>
                                    <th>Amount Paid</th>
                                    <th>Cheque Number</th>
                                    <th>Bank</th>
                                    <th>Trade Type</th>
                                    <th>Customer</th>
                                    <th>Payment Mode</th>
                                    <th>Payment Date</th>
                                    <th>TEU</th>
                                    <th>User</th>
                                </tr>
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
                    </div><!-- end of card-body -->
                </div><!-- end of card -->
            </div>
            <?php

            require_once 'includes/footer.php';

            ?>

            <script>
                $(document).ready(function () {
                    system_object = '<?php echo $system_object?>';
                    PaymentReport.iniTable();
                });
            </script>