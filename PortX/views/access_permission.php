<?php
use Lib\ACL;
$system_object="group-permissions";
ACl::verifyRead($system_object);
$title = 'Access Permission Manager';
$accessManager = 'active open';
$ports = 'active';
require_once 'includes/header.php';
require_once 'includes/preloaders.php';
require_once 'includes/aside.php';
require_once 'includes/top-header.php';
$groups = new ACL();
?>
<main>
    <div class="main-content">
        <div class="card">
            <h4 class="card-title"><strong>Access</strong> Permission Manager</h4>
            <div class="card-body">

                <div class="row">
                    <div class="col-md-12" style="text-align:center">
                        <select id="user_group" class="custom-select">
                            <option value="">Select Group</option>
                            <?php
                            foreach ($groups->getGroups() as $user_groups){ ?>
                                <option value="<?=$user_groups[0]?>"><?=$user_groups[1]?></option>
                            <?php }
                            ?>
                        </select>
                    </div>
                </div><!-- end of row -->


                <div class="row">
                    <div class="col-md-12">
                        <table id="access_groups" class="display table dataTable" style="width:100%">
                            <thead>
                            <tr>
                                <th>Menu</th>
                                <th>Access Feature</th>
                                <th>Add Data</th>
                                <th>Update Data</th>
                                <th>Delete Data</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr><td><a href="/user/voyage"> Voyage Records</a></td><td><input id="tos" type="checkbox"></td><td><input id="tos1" type="checkbox"></td><td><input id="tos2" type="checkbox"></td><td><input id="tos3" type="checkbox"></td></tr>
                            <tr><td><a href="/user/vessel">Vessel Records</a></td><td><input type="checkbox"></td><td><input type="checkbox"></td><td><input type="checkbox"></td><td><input type="checkbox"></td></tr>
                            <tr><td><a href="/user/container">Container Records</a></td><td><input type="checkbox"></td><td><input type="checkbox"></td><td><input type="checkbox"></td><td><input type="checkbox"></td></tr>
                            <tr><td><a href="/user/import">Excel Uploads</a></td><td><input type="checkbox"></td><td><input type="checkbox"></td><td><input type="checkbox"></td><td><input type="checkbox"></td></tr>
                            <tr><td><a href="/user/gate_in">Gate In Records</a></td><td><input type="checkbox"></td><td><input type="checkbox"></td><td><input type="checkbox"></td><td><input type="checkbox"></td></tr>
                            <tr><td><a href="/user/gate_out">Gate Out Records</a></td><td><input type="checkbox"></td><td><input type="checkbox"></td><td><input type="checkbox"></td><td><input type="checkbox"></td></tr>
                            <tr><td><a href="/user/depot_over">Depot Overview</a></td><td><input type="checkbox"></td><td><input type="checkbox"></td><td><input type="checkbox"></td><td><input type="checkbox"></td></tr>
                            <tr><td><a href="/user/invoicing">Invoicing</a></td><td><input type="checkbox"></td><td><input type="checkbox"></td><td><input type="checkbox"></td><td><input type="checkbox"></td></tr>
                            <tr><td><a href="/user/supplementary_invoicing">Supplementary Invoicing</a></td><td><input type="checkbox"></td><td><input type="checkbox"></td><td><input type="checkbox"></td><td><input type="checkbox"></td></tr>
                            <tr><td><a href="/user/invoice">Invoice Records</a></td><td><input type="checkbox"></td><td><input type="checkbox"></td><td><input type="checkbox"></td><td><input type="checkbox"></td></tr>
                            <tr><td><a href="/user/supplementary_invoice">Supplementary Invoice Record</a></td><td><input type="checkbox"></td><td><input type="checkbox"></td><td><input type="checkbox"></td><td><input type="checkbox"></td></tr>
                            <tr><td><a href="/user/stock_reports">Stock Report</a></td><td><input type="checkbox"></td><td><input type="checkbox"></td><td><input type="checkbox"></td><td><input type="checkbox"></td></tr>
                            <tr><td><a href="/user/gate_reports">Gate Report</a></td><td><input type="checkbox"></td><td><input type="checkbox"></td><td><input type="checkbox"></td><td><input type="checkbox"></td></tr>
                            <tr><td><a href="/user/port">Ports</a></td><td><input type="checkbox"></td><td><input type="checkbox"></td><td><input type="checkbox"></td><td><input type="checkbox"></td></tr>
                            <tr><td><a href="/user/shipping_line">Shipping Line</a></td><td><input type="checkbox"></td><td><input type="checkbox"></td><td><input type="checkbox"></td><td><input type="checkbox"></td></tr>
                            <tr><td><a href="/user/shipping_line_agent">Shipping Line Agents</a></td><td><input type="checkbox"></td><td><input type="checkbox"></td><td><input type="checkbox"></td><td><input type="checkbox"></td></tr>
                            <tr><td><a href="/user/trucking_company">Trucking Companies</a></td><td><input type="checkbox"></td><td><input type="checkbox"></td><td><input type="checkbox"></td><td><input type="checkbox"></td></tr>
                            <tr><td><a href="/user/vehicle">Vehicle Registration</a></td><td><input type="checkbox"></td><td><input type="checkbox"></td><td><input type="checkbox"></td><td><input type="checkbox"></td></tr>
                            <tr><td><a href="/user/container_type_codes">Container Type Codes</a></td><td><input type="checkbox"></td><td><input type="checkbox"></td><td><input type="checkbox"></td><td><input type="checkbox"></td></tr>
                            <tr><td><a href="/user/vehicle_driver">Driver Registration</a></td><td><input type="checkbox"></td><td><input type="checkbox"></td><td><input type="checkbox"></td><td><input type="checkbox"></td></tr>
                            <tr><td><a href="/user/countries">Countries</a></td><td><input type="checkbox"></td><td><input type="checkbox"></td><td><input type="checkbox"></td><td><input type="checkbox"></td></tr>
                            <tr><td><a href="/user/agency">Agency</a></td><td><input type="checkbox"></td><td><input type="checkbox"></td><td><input type="checkbox"></td><td><input type="checkbox"></td></tr>
                            <tr><td><a href="/user/customer">Customers</a></td><td><input type="checkbox"></td><td><input type="checkbox"></td><td><input type="checkbox"></td><td><input type="checkbox"></td></tr>
                            <tr><td><a href="/user/depot_activity_charges">Depot Activity Charges</a></td><td><input type="checkbox"></td><td><input type="checkbox"></td><td><input type="checkbox"></td><td><input type="checkbox"></td></tr>
                            <tr><td><a href="/user/storage_rent_charges">Storage Rent Charges</a></td><td><input type="checkbox"></td><td><input type="checkbox"></td><td><input type="checkbox"></td><td><input type="checkbox"></td></tr>
                            <tr><td><a href="/user/taxes">Taxes</a></td><td><input type="checkbox"></td><td><input type="checkbox"></td><td><input type="checkbox"></td><td><input type="checkbox"></td></tr>
                            <tr><a href="/user/invoice_payment"></a><td>Invoice Payment</td><td><input type="checkbox"></td><td><input type="checkbox"></td><td><input type="checkbox"></td><td><input type="checkbox"></td></tr>
                            <tr><td>Supplementary Invoice Payement</td><td><input type="checkbox"></td><td><input type="checkbox"></td><td><input type="checkbox"></td><td><input type="checkbox"></td></tr>
                            <tr><td><a href="/user/user_account">User Account Manager</a></td><td><input type="checkbox"></td><td><input type="checkbox"></td><td><input type="checkbox"></td><td><input type="checkbox"></td></tr>
                            <tr><td><a href="/user/voyage_reports">Voyage Reports</a></td><td><input type="checkbox"></td><td><input type="checkbox"></td><td><input type="checkbox"></td><td><input type="checkbox"></td></tr>
                            <tr><td><a href="/user/depot_activity">Depot Activity</a></td><td><input type="checkbox"></td><td><input type="checkbox"></td><td><input type="checkbox"></td><td><input type="checkbox"></td></tr>
                            <tr><td><a href="/user/let_pass_record">Let Pass Records</a></td><td><input type="checkbox"></td><td><input type="checkbox"></td><td><input type="checkbox"></td><td><input type="checkbox"></td></tr>
                            <tr><td><a href="/user/payment_report">Payment Report</a></td><td><input type="checkbox"></td><td><input type="checkbox"></td><td><input type="checkbox"></td><td><input type="checkbox"></td></tr>
                            <tr><td><a href="/user/invoice_reports">Invoice Reports</a></td><td><input type="checkbox"></td><td><input type="checkbox"></td><td><input type="checkbox"></td><td><input type="checkbox"></td></tr>
                            <tr><td><a href="/user/let_pass">Let Pass Generation</a></td><td><input type="checkbox"></td><td><input type="checkbox"></td><td><input type="checkbox"></td><td><input type="checkbox"></td></tr>
                            <tr><td><a href="/user/banks">Banks</a></td><td><input type="checkbox"></td><td><input type="checkbox"></td><td><input type="checkbox"></td><td><input type="checkbox"></td></tr>
                            <tr><td><a href="/user/exchange_rate">Exchange Rate</a></td><td><input type="checkbox"></td><td><input type="checkbox"></td><td><input type="checkbox"></td><td><input type="checkbox"></td></tr>
                            <tr><td><a href="/user/invoice_approvals">Invoice Approvals</a></td><td><input type="checkbox"></td><td><input type="checkbox"></td><td><input type="checkbox"></td><td><input type="checkbox"></td></tr>
                            <tr><td><a href="/user/user_group">Groups</a></td><td><input type="checkbox"></td><td><input type="checkbox"></td><td><input type="checkbox"></td><td><input type="checkbox"></td></tr>
                            <tr><td><a href="/user/access_permission">Group Permissions</a></td><td><input type="checkbox"></td><td><input type="checkbox"></td><td><input type="checkbox"></td><td><input type="checkbox"></td></tr>
                            <tr><td><a href="/user/label_editor">Label Editor</a></td><td><input type="checkbox"></td><td><input type="checkbox"></td><td><input type="checkbox"></td><td><input type="checkbox"></td></tr>
                            <tr><td><a href="/user/customer_billing_groups">Customer Billing Groups</a></td><td><input type="checkbox"></td><td><input type="checkbox"></td><td><input type="checkbox"></td><td><input type="checkbox"></td></tr>
                            <tr><td><a href="/user/charges_container_monitoring">Monitoring Charges</a></td><td><input type="checkbox"></td><td><input type="checkbox"></td><td><input type="checkbox"></td><td><input type="checkbox"></td></tr>
                            <tr><td><a href="/user/supplementary_invoice_approvals">Supplementary Invoice Approvals</a></td><td><input type="checkbox"></td><td><input type="checkbox"></td><td><input type="checkbox"></td><td><input type="checkbox"></td></tr>
                            <tr><td><a href="/user/invoice_deferrals">Invoice Deferrals</a></td><td><input type="checkbox"></td><td><input type="checkbox"></td><td><input type="checkbox"></td><td><input type="checkbox"></td></tr>
                            <tr><td><a href="/user/invoice_waivers">Invoice Waivers</a></td><td><input type="checkbox"></td><td><input type="checkbox"></td><td><input type="checkbox"></td><td><input type="checkbox"></td></tr>
                            <tr><td><a href="/user/supplementary_invoice_waiver">Supplementary Invoice Waiver</a></td><td><input type="checkbox"></td><td><input type="checkbox"></td><td><input type="checkbox"></td><td><input type="checkbox"></td></tr>
                            <tr><td><a href="/user/supplementary_invoice_deferrals">Supplementary Invoice Deferrals</a></td><td><input type="checkbox"></td><td><input type="checkbox"></td><td><input type="checkbox"></td><td><input type="checkbox"></td></tr>
                            <tr><td><a href="/user/proforma_invoicing">Pro forma Invoicing</a></td><td><input type="checkbox"></td><td><input type="checkbox"></td><td><input type="checkbox"></td><td><input type="checkbox"></td></tr>
                            <tr><td><a href="/user/proforma_invoice">Pro forma Invoice Records</a></td><td><input type="checkbox"></td><td><input type="checkbox"></td><td><input type="checkbox"></td><td><input type="checkbox"></td></tr>
                            <tr><td><a href="/user/proforma_supplementary_invoicing">Pro froma Supplementary Invoicing</a></td><td><input type="checkbox"></td><td><input type="checkbox"></td><td><input type="checkbox"></td><td><input type="checkbox"></td></tr>
                            <tr><td><a href="/user/proforma_supplementary_invoice">Pro forma Supplementary Invoice Records</a></td><td><input type="checkbox"></td><td><input type="checkbox"></td><td><input type="checkbox"></td><td><input type="checkbox"></td></tr>
                            <tr><td><a href="/user/ucl_depot">UCL Depot</a></td><td><input type="checkbox"></td><td><input type="checkbox"></td><td><input type="checkbox"></td><td><input type="checkbox"></td></tr>
                            <tr><td><a href="/user/ucl_settings">UCL Settings</a></td><td><input type="checkbox"></td><td><input type="checkbox"></td><td><input type="checkbox"></td><td><input type="checkbox"></td></tr>
                            <tr><td><a href="/user/proforma_depot_over">Pro forma Depot Overview</a></td><td><input type="checkbox"></td><td><input type="checkbox"></td><td><input type="checkbox"></td><td><input type="checkbox"></td></tr>
                            <tr><td><a href="/user/summary_remittances">Summary Remittances</a></td><td><input type="checkbox"></td><td><input type="checkbox"></td><td><input type="checkbox"></td><td><input type="checkbox"></td></tr>
                            <tr><td><a href="/user/bookings">Bookings</a></td><td><input type="checkbox"></td><td><input type="checkbox"></td><td><input type="checkbox"></td><td><input type="checkbox"></td></tr>
                            <tr><td><a href="/user/empty_bookings">Empty Bookings</a></td><td><input type="checkbox"></td><td><input type="checkbox"></td><td><input type="checkbox"></td><td><input type="checkbox"></td></tr>
                            <tr><td><a href="/user/move_to_export">Move To Export</a></td><td><input type="checkbox"></td><td><input type="checkbox"></td><td><input type="checkbox"></td><td><input type="checkbox"></td></tr>
                            <tr><td><a href="/user/yard_planning">Yard Planning</a></td><td><input type="checkbox"></td><td><input type="checkbox"></td><td><input type="checkbox"></td><td><input type="checkbox"></td></tr>
                            <tr><td><a href="/user/reach_stacker">Reach Stacker</a></td><td><input type="checkbox"></td><td><input type="checkbox"></td><td><input type="checkbox"></td><td><input type="checkbox"></td></tr>
                            <tr><td><a href="/user/operator_view">Operator View</a></td><td><input type="checkbox"></td><td><input type="checkbox"></td><td><input type="checkbox"></td><td><input type="checkbox"></td></tr>
                            <tr><td><a href="/user/examination_area">Examination Area</a></td><td><input type="checkbox"></td><td><input type="checkbox"></td><td><input type="checkbox"></td><td><input type="checkbox"></td></tr>
                            <tr><td><a href="/user/stack">Stack</a></td><td><input type="checkbox"></td><td><input type="checkbox"></td><td><input type="checkbox"></td><td><input type="checkbox"></td></tr>
                            <tr><td><a href="/user/bare_chasis">Bare Chasis</a></td><td><input type="checkbox"></td><td><input type="checkbox"></td><td><input type="checkbox"></td><td><input type="checkbox"></td></tr>
                        </tbody>
                        </table>
                    </div>
                </div><!-- end of row -->

                <div class="row">
                    <div class="col-md-12" style="text-align:center; margin-top:20px">
                        <button type="button" id="add_permission" class="btn btn-primary">Submit</button>
                    </div>
                </div>
            </div>
            <?php

            require_once 'includes/footer.php';

            ?>
            <script>

                $(document).ready(function () {
                    GroupAccess.GroupsOverview();
                });

            </script>
