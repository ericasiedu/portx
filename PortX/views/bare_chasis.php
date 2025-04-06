<?php
use Lib\ACL;
$system_object="truck-record";
ACl::verifyRead($system_object);
$title = 'Bare Chasis';
$gateTransactions = 'active open';
$truck = 'active';
require_once 'includes/header.php';
require_once 'includes/preloaders.php';
require_once 'includes/aside.php';
require_once 'includes/top-header.php';

?>
<main>
    <div class="main-content">
        <div class="card">
            <h4 class="card-title"><strong>Bare</strong> Chasis <span class="bare_chasis_date">Start Date <input type="date" value="<?php echo date('Y-m-d'); ?>" id="start_date" />
                </span>End Date <input type="date" value="<?php echo date('Y-m-d'); ?>" id="end_date" /></h4>
            <div class="card-body">

            <datalist id="vehicle_list"></datalist>
            <datalist id="driver_list"></datalist>
            <datalist id="container_list"></datalist>

                <table id="truck" class="display table">
                    <thead>
                    <tr>
                        <th>Vehicle Number</th>
                        <th>Driver Name</th>
                        <th>Container Number</th>
                        <th>Letpass Number</th>
                        <th>Truck without Container(Time Spent)</th>
                        <th>Truck with Container(Time Spent)</th>
                        <th>Gate Status</th>
                        <th>Date</th>
                        <th>Action</th>
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
                    BareChasis.iniTable();
                });
            </script>