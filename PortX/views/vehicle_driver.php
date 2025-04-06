<?php
use Lib\ACL;
use Lib\Truck;
$system_object="udm-driver-registration";
ACl::verifyRead($system_object);
$title = ' Driver Registration';
$userDataManager = 'active open';
$driverRegistration = 'active';
?>
<?php require_once('includes/header.php'); ?>
<?php require_once('includes/preloaders.php'); ?>
<?php require_once('includes/aside.php'); ?>
<?php require_once('includes/top-header.php');
$truck = new Truck();
?>

<main>

    <div class="main-content">

        <div class="card">
            <h4 class="card-title"><strong>Driver</strong> Registration</h4>
            <div class="card-body">

               <div id="customForm">
                   <editor-field name="vehicle_driver.license"></editor-field>
                   <editor-field name="vehicle_driver.name"></editor-field>
                   <editor-field name="vehicle_driver.trucking_company_id"></editor-field>
               </div>
                <datalist id="trucking">
                    <?php

                         foreach ($truck->getTruckCompany() as $trucking_company){ ?>
                             <option><?=$trucking_company[0]?></option>
                        <?php }

                    ?>
                </datalist>

                <div class="row">
                    <div class="col-md-12 table-responsive">

                        <table id="vehicle_driver" class="display table">
                            <thead>
                            <tr>
                                <th>License</th>
                                <th>Name</th>
                                <th>Trucking Company</th>
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
        VehicleDriver.iniTable();
    });

</script>