<?php
use Lib\ACL;
$system_object="depot-reports";
ACl::verifyRead($system_object);
$title = 'Stock Reports';
$reports = 'active open';
$stockReports = 'active';
require_once 'includes/header.php';
require_once 'includes/preloaders.php';
require_once 'includes/aside.php';
require_once 'includes/top-header.php';

?>
<main>
    <div class="main-content">
        <div class="card">
            <h4 class="card-title"><strong>Stock </strong><span> Reports</span>
                <span class="select-t">Trade Type</span> <select class="custom-select"
                            id="trade_type">
                        <option value="%">All</option>
                        <option value="11">Import</option>
                        <option value="21">Export</option>
                </select></h4>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12 table-responsive">
                        <table id="depot_report" class="display table">
                            <thead>
                            <tr>
                                <th>Shipping Line</th>
                                <th>22G1</th>
                                <th>22U1</th>
                                <th>22P1</th>
                                <th>45G1</th>
                                <th>42U1</th>
                                <th>45R1</th>
                                <th>42P1</th>
                                <th>Other</th>
                                <th>Tot. Units</th>
                                <th>Tot. TEU</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tfoot>
                            <tr>
                                <th colspan="4"> </th>
                                <th colspan="4"></th>
                                <th colspan="4"></th>
                            </tr>
                            </tfoot>
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
                    StockReport.iniTable();
                });
            </script>