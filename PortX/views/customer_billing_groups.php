<?php
use Lib\ACL;
$system_object="udm-customer_billing_groups";
ACl::verifyRead($system_object);
$title = 'Customer Billing Groups';
$userDataManager = 'active open';
$customer_billing = 'active';
require_once 'includes/header.php';
require_once 'includes/preloaders.php';
require_once 'includes/aside.php';
require_once 'includes/top-header.php';

?>
<main>
    <div class="main-content">
        <div class="card">
            <h4 class="card-title"><strong>Customer </strong>Billing Groups</h4>
            <div class="card-body">
                <div id="customForm">
                    <editor-field name="customer_billing_group.name"></editor-field>
                    <editor-field name="customer_billing_group.extra_free_rent_days"></editor-field>
                    <editor-field name="customer_billing_group.trade_type"></editor-field>
                    <editor-field name="customer_billing_group.tax_type"></editor-field>
                    <editor-field name="waiver_type"></editor-field>
                    <editor-field name="customer_billing_group.waiver_pct"></editor-field>
                    <editor-field name="customer_billing_group.waiver_amount"></editor-field>
                </div>
                <div class="row">
                    <div class="col-md-12 table-responsive">
                        <table id="customer_billing_group" class="display table">
                            <thead>
                            <tr>
                                <th>Name</th>
                                <th>Trade Type</th>
                                <th>Extra Free Rent Days</th>
                                <th>Tax Type</th>
                                <th>Waiver (%)</th>
                                <th>Waiver (Amount)</th>
                            </tr>
                            </thead>
                        </table>
                    </div><!-- end of card-body -->
                </div><!-- end of card -->
            </div>
            <?php

            require_once 'includes/footer.php';

            ?>

            <script>
                $(document).ready(function() {
                    system_object='<?php echo $system_object?>';
                    CustomerBillingGroups.iniTable();
                });
            </script>