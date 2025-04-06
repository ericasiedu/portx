<?php
use Lib\ACL;
use Lib\Gate;
$system_object="udm-storage-rent-charges";
ACl::verifyRead($system_object);
$title = 'Storage Rent Charges';  ?>
<?php $userDataManager = 'active open'; ?>
<?php $storageRentCharges = 'active'; ?>
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
            <h4 class="card-title"><strong>Storage Rent</strong> Charges</h4>
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

                        <table id="storage_rent_charges" class="display table">
                            <thead>
                                <tr>
                                    <th>Trade Type</th>
                                    <th>Goods</th>
                                    <th>Full Status</th>
                                    <th>Free Days</th>
                                    <th>First Billable Days</th>
                                    <th>First Billable Days Cost</th>
                                    <th>Second Billable Days</th>
                                    <th>Second Billable Days Cost</th>
                                    <th>All Other Billable Days Cost</th>
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
            StorageRentCharges.iniTable();
        });

    </script>
