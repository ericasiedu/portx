<?php
use Lib\ACL;
use Lib\Gate;
$system_object="udm-depot-activity-charges";
ACl::verifyRead($system_object);
$title = 'Depot Activity Charges';  ?>
<?php $userDataManager = 'active open'; ?>
<?php $depotActivityCharges = 'active'; ?>
<?php require_once('includes/header.php'); ?>
<?php require_once('includes/preloaders.php'); ?>
<?php require_once('includes/aside.php'); ?>
<?php require_once('includes/top-header.php');  ?>
<?php
$gate=new Gate();
?>
<main>

    <div class="main-content">

        <div class="card">
            <h4 class="card-title"><strong>Depot Activity</strong> Charges</h4>
            <div class="card-body">
                <datalist id="container_type">
                    <?php
                    foreach ($gate->getContainerType() as $type){?>
                        <option><?=$type[0]?></option>
                        <?php
                    }
                    ?>
                </datalist>

                <div class="row">
                    <div class="col-md-12 table-responsive">

                        <table id="depot_activity_charges" class="display table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Trade Type</th>
                                    <th>Container Length</th>
                                    <th>Load Status</th>
                                    <th>Goods</th>
                                    <th>OOG Status</th>
                                    <th>Activity</th>
                                    <th>Full Status</th>
                                    <th>Cost</th>
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
            DepotActivityCharges.iniTable();
        });

    </script>
