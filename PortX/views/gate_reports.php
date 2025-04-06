<?php
use Lib\ACL;
$system_object="gate-reports";
ACl::verifyRead($system_object);
$title = 'Gate Reports';
$reports = 'active open';
$gate_reports = 'active';
require_once 'includes/header.php';
require_once 'includes/preloaders.php';
require_once 'includes/aside.php';
require_once 'includes/top-header.php';

?>
<main>
    <div class="main-content">
        <div class="card">
            <h4 class="card-title"><strong>Gate</strong> <span>Reports</span> Start Date <input type="date" value="<?php echo date('Y-m-d'); ?>"  id="start_date" />
                End Date <input type="date" value="<?php echo date('Y-m-d'); ?>"  id="end_date" />
                Gate Status
                <select id="gate_status" class="custom-select">
                    <option value="*">ALL</option>
                    <option value="GATE IN">GATE IN</option>
                    <option value="GATE OUT">GATE OUT</option>
                </select>
                <span>
                    Trade Type
                    <select id="trade_type" class="custom-select">
                        <option value="*">ALL</option>
                        <option value="11">IMPORT</option>
                        <option value="21">EXPORT</option>
                        <option value="13">TRANSIT</option>
                        <option value="70">EMPTY</option>
                    </select>
                </span>
            </h4>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12 table-responsive">
                        <table id="gate_report" class="display table">
                            <thead>
                                <tr>
                                    <th>Container</th>
                                    <th>ISO Type Code</th>
                                    <th>Voyage</th>
                                    <th>Trade Type</th>
                                    <th>Gate</th>
                                    <th>Depot</th>
                                    <th>Vehicle</th>
                                    <th>Driver</th>
                                    <th>Trucking Company</th>
                                    <th>Shipping Line</th>
                                    <th>Date</th>
                                    <th>ICL Seal Number 1</th>
                                    <th>ICL Seal Number 2</th>
                                    <th>Seal Number 1</th>
                                    <th>Seal Number 2</th>
                                    <th>Special Seal</th>
                                    <th>Content of goods</th>
                                    <th>ACT</th>
                                    <th>Condition</th>
                                    <th>Note</th>
                                    <th>Consignee </th>
                                    <th>External Reference</th>
                                    <th>P Date</th>
                                    <th>User</th>
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
                    GateReport.iniTable();
                });
            </script>