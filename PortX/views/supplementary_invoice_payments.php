<?php
use Lib;
use Lib\BillTansaction;
use Lib\ACL;
$system_object="supplementary-invoice-payments";
ACl::verifyRead($system_object);
$title = 'Supplementary Invoice Payment Records';  ?>
<?php $suppBillingTransactions = 'active open'; ?>
<?php $supp_invoice_payment = 'active'; ?>
<?php require_once('includes/header.php'); ?>
<?php require_once('includes/preloaders.php'); ?>
<?php require_once('includes/aside.php'); ?>
<?php require_once('includes/top-header.php');  ?>
<?php $bank_list = new BillTansaction(); ?>

<main>

    <div class="main-content">

        <div class="card">
            <h4 class="card-title"><strong>Supplementary Invoice</strong> Payment Records</h4>
            <div class="card-body">

                <div class="col-md-6 col-lg-4">
                    <div class="modal modal-center fade" id="modal-large" tabindex="-1">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h4 class="modal-title" id="sup_paymentHeader">Modal title</h4>
                                    <button type="button" class="close" data-dismiss="modal">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <p id="sup_paymentTable">Your content comes here</p>
                                </div>
                                <div class="modal-footer">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <datalist id="sup_banks">
                    <?php
                    foreach ($bank_list->getBankList() as $bank_name){?>
                        <option><?=$bank_name[0]?></option>
                        <?php
                    }
                    ?>
                </datalist>

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

                        <table id="supp_invoice_payment" class="display table">
                            <thead>
                            <th>Trade Type</th>
                            <th>Number</th>
                            <th>BL Number</th>
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
            SupplementaryInvoicePayment.iniTable();
        });

    </script>
