<?php
use Lib\ACL,
    Lib\DepotActivity;
$system_object="depot-overview";
ACl::verifyRead($system_object);
$ucl = ACl::canRead("ucl-depot");
$title = 'Depot Overview';
$depotTransactions = 'active open';
$depotOverview = 'active';
require_once 'includes/header.php';
require_once 'includes/preloaders.php';
require_once 'includes/aside.php';
require_once 'includes/top-header.php';
$activity = new DepotActivity();
?>
<main>
    <div class="main-content">
        <div class="card">
            <h4 class="card-title"><strong>Depot</strong> Overview <span class="trade-filter">Trade Type
                    <select id="trade_type" class="custom-select">
                        <option value="ALL">ALL</option>
                        <option value="1">IMPORT</option>
                        <option value="4">EXPORT</option>
                    </select>
                </span></h4>
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
                <div class="row">
                    <div class="col-md-12 table-responsive">
                        <table id="depot_over" class="display table">
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



                        <table id="depot_container" class="display table" style="display:none">
                            <tr>
                                <th>Container</th>
                                <th>Activity</th>
                                <th>Note</th>
                                <th>Staff</th>
                                <th>Date</th>
                                <th>Pdate</th>
                            </tr>
                        </table>
<!---->
<!--                        <table id="container_info" class="display table" style="display:none">-->
<!--                            <tr>-->
<!--                                <th>Container</th>-->
<!--                                <th>Activity</th>-->
<!--                                <th>Note</th>-->
<!--                                <th>Staff</th>-->
<!--                                <th>Date</th>-->
<!--                                <th>Pdate</th>-->
<!--                            </tr>-->
<!--                        </table>-->

                        <div class="col-md-6 col-lg-4">
                            <div class="modal modal-center fade" id="modal-small" tabindex="-1">
                                <div class="modal-dialog modal-sm">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h4 class="modal-title" id="depotHeader">Modal title</h4>
                                            <button type="button" class="close" data-dismiss="modal">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <p id="depotAlert">Your content comes here</p>
                                        </div>
                                        <div class="modal-footer">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="modal modal-center fade" id="modal-center" tabindex="-1">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 id="container_number" class="modal-title"></h5>
                                        <button type="button" class="close" data-dismiss="modal">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="col-md-12 table-responsive">
                                            <div id="container_depot"></div>
                                        <div class="modal-footer">
<!--                                            <button type="button" class="btn btn-bold btn-pure btn-secondary" data-dismiss="modal">Close</button>-->
<!--                                            <button type="button" class="btn btn-bold btn-pure btn-primary">Save changes</button>-->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    </div><!-- end of card-body -->
                </div><!-- end of card -->
            </div>
            <?php

            require_once 'includes/footer.php';

            ?>

            <script>
                $(document).ready(function() {
                    system_object='<?php echo $system_object?>';
                    var can_move_ucl = <?php echo $ucl; ?>;
                    DepotOver.iniTable(can_move_ucl);
                });
            </script>