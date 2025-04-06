<?php
use Lib\ACL;
$system_object="supplementary-invoicing";
ACl::verifyRead($system_object);
$title = 'Supplementary Invoicing';
$suppBillingTransactions = 'active open';
$supp_invoicing = 'active';
require_once 'includes/header.php';
require_once 'includes/preloaders.php';
require_once 'includes/aside.php';
require_once 'includes/top-header.php';
?>

<main>
    <div class="main-content">
        <div class="card">
            <h4 class="card-title"><strong>Supplementary</strong> Invoicing</h4>
            <div class="card-body">


                <div class="nav-tabs-left">
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs nav-tabs-success">
                        <li class="nav-item"><a class="nav-link active" id="homes" data-toggle="tab" href="#home-left">Main Invoice</a></li>
                        <li class="nav-item"><a class="nav-link" id="profiles" data-toggle="tab">Container Selection</a></li>
                        <li class="nav-item"><a class="nav-link" id="charge-link" data-toggle="tab">Storage Charge</a></li>
                        <li class="nav-item"><a class="nav-link" id="preview-link" data-toggle="tab">Preview</a></li>
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
                        <div class="tab-pane fade" id="preview-left">
                            <H4>Invoice Preview</H4>
                            <br>
                            <h6 id="company-name"></h6>
                            <p id="company-address"></p>
                            <p id="company-contacts"></p>
                            <div class="container">
                                <h6>CASH DELIVERY SUPPLEMENTARY INVOICE</h6>
                                <hr>
                                <div class="row">
                                    <b class="col-md-2">Invoice Date: </b><span id="invoice-date" class="col-md-3"></span>
                                    <b class="col-md-2 col-md-offset-2">Invoice No.: </b><span id="invoice-no" class="col-md-3"></span>
                                </div>
                                <div class="row">
                                    <b class="col-md-2">Paid Up to: </b><span id="paid-up-to" class="col-md-3"></span>
                                    <b class="col-md-2 col-md-offset-2">TIN: </b><span id="tin" class="col-md-3"></span>
                                </div>
                                <hr>
                            </div>
                            <div class="container business">
                                <div class="row">
                                    <table class="col-md-12">
                                        <thead>
                                            <tr>
                                                <th>Charge Description</th>
                                                <th>Qty</th>
                                                <th class="table-money">Unit Rate</th>
                                                <th class="table-money">Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody id="main-table">
                                            <tr>
                                                <td></td>
                                                <th colspan="2">Subtotal</th>
                                                <td id="subtotal" class="table-money"></td>
                                            </tr>
                                            <tr>
                                                <td></td>
                                                <th colspan="2">Total Tax</th>
                                                <td id="total-tax" class="table-money"></td>
                                            </tr>
                                            <tr>
                                                <td></td>
                                                <th colspan="2">Total Amount GHS</th>
                                                <td id="total-amount" class="table-money"></td>
                                            </tr>
                                        </tbody>
                                        <!-- <tfoot> -->
                                        <!-- </tfoot> -->
                                    </table>
                                </div>
                            </div>
                            <div class="container">
                                <h6>INVOICE CONTAINER LIST:</h6>
                                <div class="row">
                                    <table class="col-md-12">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Container No</th>
                                                <th>ISO Type Code</th>
                                                <th>Container Type</th>
                                                <th>Goods Description</th>
                                            </tr>
                                        </thead>
                                        <tbody id="container-list">
                                        </tbody>
                                        <!-- <tfoot> -->
                                        <!-- </tfoot> -->
                                    </table>
                                </div>            
                            </div>
                            <div class="modal-footer">
                                <button type="button" id="invoice_button" class="btn btn-primary">GENERATE INVOICE</button>
                            </div>
                            <!-- <p>
                                <button type="button" id="preview_button" class="btn btn-primary">PREVIEW
                                </button>
                                <button type="button" id="storage_button" class="btn btn-primary">NEXT
                                </button>
                            </p> -->
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

        <div class="container mid-info import removable">
            <div class="row">
                <table class="col-md-12">
                    <thead>
                        <tr>
                            <th id="importer" class="col-md-2">Importer</th>
                            <th id="agency" class="col-md-2">Agency</th>
                            <th id="release-instructions" class="col-md-3">Release Instructions</th>
                            <th id="customer" class="col-md-3">Customer</th>
                            <th id="main-invoice" class="col-md-2">Main Invoice</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td id="importer-td" class="col-md-2"></td>
                            <td id="agency-td" class="col-md-2"></td>
                            <td id="release-instructions-td" class="col-md-3"></td>
                            <td id="customer-td" class="col-md-3"></td>
                            <td id="main-invoice-td" class="col-md-2"></td>
                        </tr>
                    </tbody>            
                </table>
            </div>
            <div class="row">
                <table class="col-md-12">
                    <thead>
                        <tr>
                            <th id="vessel" class="col-md-3">Vessel</th>
                            <th id="voyage-no" class="col-md-2">Voyage No</th>
                            <th id="arrival-date" class="col-md-2">Arrival Date</th>
                            <th id="departure-date" class="col-md-2">Departure Date</th>
                            <th id="rotation-number" class="col-md-3">Rotation number</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td id="vessel-td" class="col-md-3"></td>
                            <td id="voyage-no-td" class="col-md-2"></td>
                            <td id="arrival-date-td" class="col-md-2"></td>
                            <td id="departure-date-td" class="col-md-2"></td>
                            <td id="rotation-number-td" class="col-md-3"></td>
                        </tr>
                    </tbody>            
                </table>
            </div>
            <div class="row">
                <table class="col-md-12">
                    <thead>
                        <tr>
                            <th id="bl-number" class="col-md-3">BL Number</th>
                            <th id="boe-number" class="col-md-3">BoE Number</th>
                            <th id="do-number" class="col-md-2">DO Number</th>
                            <th id="release-date" class="col-md-2">Release Date</th>
                            <th id="containers" class="col-md-2">Container(s)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td id="bl-number-td" class="col-md-3"></td>
                            <td id="boe-number-td" class="col-md-3"></td>
                            <td id="do-number-td" class="col-md-2"></td>
                            <td id="release-date-td" class="col-md-2"></td>
                            <td id="containers-td" class="col-md-2"></td>
                        </tr>
                    </tbody>            
                </table>
            </div>
        </div>

        <div class="container mid-info export removable">
            <div class="row">
                <table class="col-md-12">
                    <thead>
                        <tr>
                            <th class="col-md-4">Shipper</th>
                            <th class="col-md-4">Shipping Line</th>
                            <th class="col-md-4">Customer</th>
                            <th id="main-invoice" class="col-md-2">Main Invoice</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td id="shipper-td" class="col-md-4"></td>
                            <td id="ship-line-td" class="col-md-4"></td>
                            <td id="exp-customer-td" class="col-md-4"></td>
                            <td id="exp-main-invoice-td" class="col-md-2"></td>
                        </tr>
                    </tbody>            
                </table>
            </div>
            <div class="row">
                <table class="col-md-12">
                    <thead>
                        <tr>
                            <th class="col-md-3">Vessel</th>
                            <th class="col-md-3">Booking Number</th>
                            <th class="col-md-3">Booking Date</th>
                            <th class="col-md-3">Activity</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td id="exp-vessel-td" class="col-md-3"></td>
                            <td id="booking-number-td" class="col-md-2"></td>
                            <td id="booking-date-td" class="col-md-2"></td>
                            <td id="exp-activity-td" class="col-md-2"></td>
                        </tr>
                    </tbody>            
                </table>
            </div>
            <div class="row">
                <table class="col-md-12">
                    <thead>
                        <tr>
                            <th class="col-md-2">Container(s)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td id="exp-containers-td" class="col-md-2"></td>
                        </tr>
                    </tbody>            
                </table>
            </div>
        </div>

        <div class="container mid-info empty removable">
            <div class="row">
                <table class="col-md-12">
                    <thead>
                        <tr>
                            <th class="col-md-4">Shipper</th>
                            <th class="col-md-4">Shipping Line</th>
                            <th class="col-md-4">Customer</th>
                            <th id="main-invoice" class="col-md-2">Main Invoice</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td id="emp-shipper-td" class="col-md-4"></td>
                            <td id="emp-ship-line-td" class="col-md-4"></td>
                            <td id="emp-customer-td" class="col-md-4"></td>
                            <td id="emp-main-invoice-td" class="col-md-2"></td>
                        </tr>
                    </tbody>            
                </table>
            </div>
            <div class="row">
                <table class="col-md-12">
                    <thead>
                        <tr>
                            <th class="col-md-3">Vessel</th>
                            <th class="col-md-3">Booking Number</th>
                            <th class="col-md-3">Booking Date</th>
                            <th class="col-md-3">Activity</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td id="emp-vessel-td" class="col-md-3"></td>
                            <td id="emp-booking-number-td" class="col-md-2"></td>
                            <td id="emp-booking-date-td" class="col-md-2"></td>
                            <td id="emp-activity-td" class="col-md-2"></td>
                        </tr>
                    </tbody>            
                </table>
            </div>
            <div class="row">
                <table class="col-md-12">
                    <thead>
                        <tr>
                            <th class="col-md-2">Container(s)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td id="emp-containers-td" class="col-md-2"></td>
                        </tr>
                    </tbody>            
                </table>
            </div>
        </div>

</main>

<?php

require_once 'includes/footer.php';

?>
<script>

    $(document).ready(function () {
        system_object='<?php echo $system_object?>';
        SupplementaryInvoice.iniTable();
    });

</script>
