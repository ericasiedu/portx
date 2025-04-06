<?php
use Lib\ACL,
    Lib\BillTansaction;

$system_object="invoice-deferrals";
ACl::verifyRead($system_object);
$title = 'Invoice Deferrals';  ?>
<?php $billingTransactions = 'active open'; ?>
<?php $invoiceDeferrals = 'active'; ?>
<?php require_once('includes/header.php'); ?>
<?php require_once('includes/preloaders.php'); ?>
<?php require_once('includes/aside.php'); ?>
<?php require_once('includes/top-header.php');  ?>
<?php $bank_list = new BillTansaction(); ?>

<main>

    <div class="main-content">

        <div class="card">
            <h4 class="card-title"><strong>Invoice</strong> Deferrals</h4>
            <div class="card-body">

                <div class="col-md-6 col-lg-4">
                    <div class="modal modal-center fade" id="modal-small" tabindex="-1">
                        <div class="modal-dialog modal-sm">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h4 class="modal-title" id="myModalLabel">Modal title</h4>
                                    <button type="button" class="close" data-dismiss="modal">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <p id="conditionTable">Your content comes here</p>
                                </div>
                                <div class="modal-footer">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4">

                    <div class="modal modal-center fade" id="modal-large" tabindex="-1">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h4 class="modal-title" id="paymentHeader">Modal title</h4>
                                    <button type="button" class="close" data-dismiss="modal">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <p id="paymentTable">Your content comes here</p>
                                </div>
                                <div class="modal-footer">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 table-responsive">

                        <table id="invoice_deferrals" class="display table">
                            <thead>
                            <th>Trade Type</th>
                            <th>Number</th>
                            <th>BL Number</th>
                            <th>Booking Number</th>
                            <th>DO Number</th>
                            <th>Bill Date</th>
                            <th>Due Date</th>
                            <th>Cost</th>
                            <th>Tax</th>
                            <th>Outstanding Payment</th>
                            <th>Tax Type</th>
                            <th>Note</th>
                            <th>Customer</th>
                            <th>Approved</th>
                            <th>User</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Action</th>
                            </thead>
                        </table>

                    </div>
                </div>

            </div><!-- end of card body -->
        </div><!-- end of card -->
    </div>


    <?php require_once('includes/footer.php') ?>
    <script>

        $(document).ready(function () {
            InvoiceDeferrals.iniTable();
        });

    </script>
