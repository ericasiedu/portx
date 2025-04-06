<?php
use Lib\ACL;
use Lib\Vehicle;
$system_object="udm-vehicle";
ACl::verifyRead($system_object);
$title = 'Vehicle Registration';
$userDataManager = 'active open';
$vehicleRegistration = 'active';
require_once 'includes/header.php';
require_once 'includes/preloaders.php';
require_once 'includes/aside.php';
require_once 'includes/top-header.php';
$vehicle=new Vehicle();
?>
<main>
    <div class="main-content">
        <div class="card">
            <h4 class="card-title"><strong>Vehicle</strong> Registration</h4>
            <div class="card-body">
                <datalist id="types">
                    <?php
                    foreach ($vehicle->getTypes() as $type){?>
                        <option><?=$type[0]?></option>
                        <?php
                    }
                    ?>
                </datalist>
                <datalist id="companies">
                    <?php
                    foreach ($vehicle->getCompanies() as $company){?>
                        <option><?=$company[0]?></option>
                        <?php
                    }
                    ?>
                </datalist>
                <div id="customForm">
                    <editor-field name="vehicle.number"></editor-field>
                    <editor-field name="vehicle.description"></editor-field>
                    <editor-field name="vehicle.type_id"></editor-field>
                    <editor-field name="vehicle.trucking_company_id"></editor-field>
                </div>
                <div class="row">
                    <div class="col-md-12 table-responsive">
                        <table id="vehicle" class="display table">
                            <thead>
                            <tr>
                                <th>Number</th>
                                <th>Description</th>
                                <th>Type</th>
                                <th>Trucking Company</th>
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
                    Vehicle.iniTable();
                });
            </script>