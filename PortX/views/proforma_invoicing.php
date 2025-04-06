<?php

use Lib\ACL,
    Lib\BillTansaction;

$system_object = "proforma-invoicing";
ACl::verifyRead($system_object);
$title = 'Pro forma Invoicing';
$proforma = 'active open';
$proforma_invoicing = 'active';
require_once 'includes/header.php';
require_once 'includes/preloaders.php';
require_once 'includes/aside.php';
require_once 'includes/top-header.php';
$invoice_transaction = new BillTansaction();

?>

<main>
    <div class="main-content">
        <div class="card">
            <h4 class="card-title"><strong>Pro forma </strong>Invoicing</h4>
            <div class="card-body">


                <div class="nav-tabs-left">
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs nav-tabs-success">
                        <li class="nav-item"><a class="nav-link active" id="homes" data-toggle="tab" href="#home-left">Trade
                                Type</a>
                        </li>
                        <li class="nav-item"><a class="nav-link" id="profiles" data-toggle="tab">Container Selection</a>
                        </li>
                        <li class="nav-item"><a class="nav-link" id="charge-link" data-toggle="tab">Storage Charge</a>
                        </li>
                        <li class="nav-item"><a class="nav-link" id="preview-link" data-toggle="tab">Preview</a>
                        </li>
                        <li class="nav-item"><a class="nav-link" id="invoice-link" data-toggle="tab">Invoice</a></li>
                    </ul>
                    <!-- Tab panes -->
                    <div class="tab-content">
                        <div class="tab-pane fade active show" id="home-left">

                            <div class="row">
                                <div class="select_type">
                                    <label>Trade Select</label>
                                    <p><select name="trade_type" id="trade_type" class="form-control">
                                            <option value="11">IMPORT</option>
                                            <option value="21">EXPORT</option>
                                            <option value="70">EMPTY</option>
                                            <option value="13">TRANSIT</option>
                                        </select>
                                    </p>
                                </div>

                                <div class="number">
                                    <label id="label_number">BL Number</label>
                                    <input type="text" id="b_number" class="form-control" maxlength="20"/>
                                    <span id="error_label" style="color:red;"></span>
                                </div>

                                <div class="boe_number">
                                    <label>BoE Number</label>
                                    <input type="text" id="boe_number" class="form-control"/>
                                    <span id="error_boe_label" style="color:red;"></span>
                                </div>
                                <div class="do_number">
                                    <label>DO Number</label>
                                    <input type="text" id="do_number" class="form-control"/>
                                    <span id="error_do_label" style="color:red;"></span>
                                </div>

                                <div class="tax">
                                    <label>Tax Type</label>
                                    <select id="tax_type" class="form-control">
                                        <?php

                                        foreach ($invoice_transaction->getTaxList() as $tax_list) { ?>
                                            <option <?php if ($tax_list[1] == 'Compound') {
                                                echo 'selected="selected"';
                                            } ?> value="<?= $tax_list[0] ?>"><?= $tax_list[1]; ?></option>
                                        <?php }

                                        ?>
                                    </select>
                                    <span id="error_tax_label" style="color:red;"></span>
                                </div>

                                <div class="currency">
                                    <label>Currency</label>
                                    <select id="currency" class="form-control">
                                        <datalist id="currencies">
                                            <?php

                                            foreach ($invoice_transaction->getCurrency() as $currency) { ?>
                                                <option><?= $currency[1] ?></option>
                                            <?php }

                                            ?>
                                        </datalist>
                                    </select>
                                </div>

                            </div><!-- end row-1 -->

                            <div class="row mt-3">
                                <div class="customer">
                                    <label>Customer</label>
                                    <input type="text" id="customer_id" placeholder="Customer" list="customers_name"
                                           class="form-control"/>
                                    <datalist id="customers_name">
                                        <?php

                                        foreach ($invoice_transaction->get_customers() as $customer) { ?>
                                            <option><?= $customer[1] ?></option>
                                        <?php }

                                        ?>
                                    </datalist>
                                    <span id="customer_error" style="color:red;"></span>
                                    <div class="billing">
                                        <label>Billing Group</label>
                                        <div><span id="billing_group"></span></div>
                                    </div>
                                    <button class="btn btn-primary detail-submit" type="button" id="next">NEXT</button>
                                </div>


                                <div class="voyage">
                                    <label>Voyage</label>
                                    <input type="text" id="voyage_id" placeholder="Voyage" list="voyages"
                                           class="form-control"/>
                                    <span id="v_error_label" style="color:red;"></span>
                                    <datalist id="voyages">
                                        <?php

                                        foreach ($invoice_transaction->getVoyage() as $voyage) { ?>
                                            <option><?= $voyage[1] ?></option>
                                        <?php }

                                        ?>
                                    </datalist>
                                </div>


                                <div class="release">
                                    <label>Release Instructions</label>
                                    <select type="text" id="release"
                                            class="form-control">
                                        <option selected value=""></option>
                                        <option value="H/H">H/H</option>
                                        <option value="Unstuffing">Unstuffing</option>
                                    </select>
                                </div>
                                <div class="note">
                                    <label>Note</label>
                                    <textarea id="note" placeholder="Note" class="form-control"></textarea>
                                    <p style="margin-top: 14px">
                                </div>

                            </div><!-- end row-2 -->

                        </div>
                        <div class="tab-pane fade" id="profile-left">
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
                            <button type="button" id="container-invoice" class="btn btn-primary">NEXT</button>
                        </div>
                        <div class="tab-pane fade" id="messages-left">
                            <div class="paid">
                                <H4>Paid up to Date</H4>
                                <input type="date" id="upto_date" class="form-control"/>
                            </div>
                            <p>
                                <button type="button" id="preview_button" class="btn btn-primary">NEXT
                                </button>
                                <!-- <button type="button" id="storage_button" class="btn btn-primary">NEXT
                                </button> -->
                            </p>
                        </div>
                        <div class="tab-pane fade" id="preview-left">
                            <H4>Invoice Preview</H4>
                            <br>
                            <h6 id="company-name"></h6>
                            <p id="company-address"></p>
                            <p id="company-contacts"></p>
                            <div class="container">
                                <h6>CASH DELIVERY INVOICE</h6>
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
                        <div class="tab-pane fade" id="invoice-left">
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
                        <h4 class="modal-title" id="manualHeader">Modal title</h4>
                        <button type="button" class="close" data-dismiss="modal">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p id="manualAlert">Your content comes here</p>
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
                            <th id="importer" class="col-md-3">Importer</th>
                            <th id="agency" class="col-md-3">Agency</th>
                            <th id="release-instructions" class="col-md-3">Release Instructions</th>
                            <th id="customer" class="col-md-3">Customer</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td id="importer-td" class="col-md-3"></td>
                            <td id="agency-td" class="col-md-3"></td>
                            <td id="release-instructions-td" class="col-md-3"></td>
                            <td id="customer-td" class="col-md-3"></td>
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
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td id="shipper-td" class="col-md-4"></td>
                            <td id="ship-line-td" class="col-md-4"></td>
                            <td id="exp-customer-td" class="col-md-4"></td>
                        </tr>
                    </tbody>            
                </table>
            </div>
            <div class="row">
                <table class="col-md-12">
                    <thead>
                        <tr>
                            <th class="col-md-3">Vessel</th>
                            <th class="col-md-3">Voyage No</th>
                            <th class="col-md-3">Booking Number</th>
                            <th class="col-md-3">Booking Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td id="exp-vessel-td" class="col-md-3"></td>
                            <td id="exp-voyage-no-td" class="col-md-2"></td>
                            <td id="booking-number-td" class="col-md-2"></td>
                            <td id="booking-date-td" class="col-md-2"></td>
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
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td id="emp-shipper-td" class="col-md-4"></td>
                            <td id="emp-ship-line-td" class="col-md-4"></td>
                            <td id="emp-customer-td" class="col-md-4"></td>
                        </tr>
                    </tbody>            
                </table>
            </div>
            <div class="row">
                <table class="col-md-12">
                    <thead>
                        <tr>
                            <th class="col-md-3">Vessel</th>
                            <th class="col-md-3">Voyage No</th>
                            <th class="col-md-3">Booking Number</th>
                            <th class="col-md-3">Booking Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td id="emp-vessel-td" class="col-md-3"></td>
                            <td id="emp-voyage-no-td" class="col-md-2"></td>
                            <td id="emp-booking-number-td" class="col-md-2"></td>
                            <td id="emp-booking-date-td" class="col-md-2"></td>
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
        system_object = '<?php echo $system_object?>';
        Invoicing.is_proforma = 1;
        Invoicing.initialize();
    });

</script>
