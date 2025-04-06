<?php
use Lib\ACL;
$system_object="udm-charges-container-monitoring";
ACl::verifyRead($system_object);
$title = 'Container Monitoring Charges';  ?>
<?php $userDataManager = 'active open'; ?>
<?php $chargesContainerMonitoring = 'active'; ?>
<?php require_once('includes/header.php'); ?>
<?php require_once('includes/preloaders.php'); ?>
<?php require_once('includes/aside.php'); ?>
<?php require_once('includes/top-header.php');  ?>
<main>

    <div class="main-content">

        <div class="card">
            <h4 class="card-title"><strong>Monitoring</strong> Charges</h4>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12 table-responsive">
                        <table id="container_monitoring_charges" class="display table">
                            <thead>
                                <tr>
                                    <th>Trade Type</th>
                                    <th>Goods</th>
                                    <th>Cost Per Day</th>
                                    <th>Currency</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>

            </div><!-- end of card body -->
        </div><!-- end of card -->
    </div>


    <?php require_once('includes/footer.php') ?>
    <script>

        $(document).ready(function () {
            system_object='<?php echo $system_object?>';
            ContainerMonitoringCharges.iniTable();
        });

    </script>
