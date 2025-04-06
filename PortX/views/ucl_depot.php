<?php
use Lib\ACL,
    Lib\DepotActivity;
$system_object="ucl-depot";
ACl::verifyRead($system_object);
$title = 'UCL';
$depotTransactions = 'active open';
$uclActive = 'active';
require_once 'includes/header.php';
require_once 'includes/preloaders.php';
require_once 'includes/aside.php';
require_once 'includes/top-header.php';
$activity = new DepotActivity();
?>
<main>
    <div class="main-content">
        <div class="card">
            <div class="card-body">
                <div id="customForm">
                    <editor-field name="container_id"></editor-field>
                    <editor-field name="activity_id"></editor-field>
                    <editor-field name="note"></editor-field>
                    <editor-field name="user_id"></editor-field>
                    <editor-field name="pdate"></editor-field>
                </div>
                <datalist id="activity_list">
                    <?php
                    foreach ($activity->activity_list() as $activities){?>
                        <option><?=$activities[0]?></option>
                    <?php  }
                    ?>
                </datalist>
                <h4 class="card-title"><strong>UCL </strong>Depot</h4>
                <div class="row">
                    <div class="col-md-12 table-responsive">
                        <table id="ucl_depot" class="display table">
                            <thead>
                            <tr>
                                <th>Container</th>
                                <th>ISO Type Code</th>
                                <th>BL Number</th>
                                <th>Booking Number</th>
                                <th>Depot</th>
                                <th>Gate</th>
                                <th>Vehicle</th>
                                <th>Driver</th>
                                <th>Trucking Company</th>
                                <th>Consignee </th>
                                <th>External Reference</th>
                                <th>Condition</th>
                                <th>Note</th>
                                <th>Actions</th>
                                <th>EIR/Waybill No</th>
                                <th>Date</th>
                                <th>P Date</th>
                                <th>User</th>
                                <th>Special Seal</th>
                            </tr>
                            </thead>
                        </table>

                    </div><!-- end of card-body -->
                </div><!-- end of card -->
            </div><!-- end of card-body -->
        </div><!-- end of card -->
    </div>
    <?php

    require_once 'includes/footer.php';

    ?>
    <script>
        $(document).ready(function () {
            system_object = '<?php echo $system_object?>';
            UCL.iniTable();
        });
    </script>

