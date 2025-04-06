<?php
use Lib\ACL;
$system_object="proforma-supplementary-invoicing";
ACl::verifyRead($system_object);
$title = 'Pro forma Supplementary Invoicing';
$suppBillingTransactions = 'active open';
$proforma_supp_invoicing = 'active';
require_once 'includes/header.php';
require_once 'includes/preloaders.php';
require_once 'includes/aside.php';
require_once 'includes/top-header.php';
?>

<main>
    <div class="main-content">
        <div class="card">
            <h4 class="card-title"><strong>Pro forma Supplementary</strong> Invoicing</h4>
            <div class="card-body">


                <div class="nav-tabs-left">
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs nav-tabs-success">
                        <li class="nav-item"><a class="nav-link active" id="homes" data-toggle="tab" href="#home-left">Main Invoice</a></li>
                        <li class="nav-item"><a class="nav-link" id="profiles" data-toggle="tab">Container Selection</a></li>
                        <li class="nav-item"><a class="nav-link" id="charge-link" data-toggle="tab">Storage Charge</a></li>
                        <li class="nav-item"><a class="nav-link" id="invoice-link" data-toggle="tab">Invoice</a></li>
                    </ul>
                    <!-- Tab panes -->
                    <div class="tab-content">
                        <div class="tab-pane fade active show" id="home-left">

                            <div class="invo_number">
                                <label id="s_invoice_number">Invoice Number</label>
                                <input type="text" id="sup_invoice_number" class="form-control" />
                                <p></p><span id="error_label" style="color:red;"></span>
                                <p><button style="margin-top:20" class="btn btn-primary" type="button" id="supp_next" >NEXT</button></p>
                            </div>

                            <div class="supp_note">
                                <label>Note</label>
                                <textarea id="sup_note" placeholder="Note" class="form-control"></textarea>
                            </div>

                        </div>
                        <div class="tab-pane fade" id="profile-selection">
                            <h4>Container List</h4>
                            <div id="select-all-panel" class="media-list-body ps-container ps-theme-default">
                                <label class="custom-control custom-control-lg custom-checkbox">
                                    <input type="checkbox" id="select-all" class="custom-control-input" autocomplete="off" />
                                    <span class="custom-control-indicator"></span>
                                    <span class="custom-control-description"><b>SELECT ALL</b></span>
                                </label>
                                <hr>
                            </div>
                            <div class="media-list-body scrollable ps-container ps-theme-default">
                                <ul id="container" style="list-style-type: none">
                                </ul>
                                <span id="selection_error" style="color:red;"></span>
                            </div>
                            <button type="button" id="supp_invoice" class="btn btn-primary">NEXT</button>
                        </div>
                        <div class="tab-pane fade" id="messages-supp">
                            <div class="paid">
                                <H4>Paid up to Date</H4>
                                <input type="date" id="sup_upto_date" class="form-control" />
                            </div>
                            <p><button type="button" id="sup_storage_button" class="btn btn-primary">NEXT</button></p>
                        </div>
                        <div class="tab-pane fade" id="invoice-supp">
                            <h1>Invoice</h1>
                            <span id="invoice_link"></span>
                        </div>
                    </div>


                </div>

            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-4">
        <div class="modal modal-center fade" id="modal-small" tabindex="-1">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="SuppHeader">Modal title</h4>
                        <button type="button" class="close" data-dismiss="modal">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p id="SuppAlert">Your content comes here</p>
                    </div>
                    <div class="modal-footer">

                    </div>
                </div>
            </div>
        </div>
    </div>

</main>

<?php

require_once 'includes/footer.php';

?>
<script>

    $(document).ready(function () {
        system_object='<?php echo $system_object?>';
        SupplementaryInvoice.is_proforma = true;
        SupplementaryInvoice.iniTable();
    });

</script>
