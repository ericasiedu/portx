<?php
use Lib\ACL;
use Lib\Voyage;
$system_object="voyage-records";
ACl::verifyRead($system_object);
$title = 'Vessel Voyages';
$vesselAndVoyages = 'active open';
$vesselVoyages = 'active';
require_once 'includes/header.php';
require_once 'includes/preloaders.php';
require_once 'includes/aside.php';
require_once 'includes/top-header.php';
$voyage=new Voyage();
?>
<main>
    <div class="main-content">
        <div class="card">
            <h4 class="card-title"><strong>Voyages</strong></h4>
            <div class="card-body">
                <datalist id="vessels">
                    <?php
                    foreach ($voyage->getVessels() as $vessel){?>
                        <option><?=$vessel[0]?></option>
                        <?php
                    }
                    ?>
                </datalist>
                <datalist id="agents">
                    <?php
                    foreach ($voyage->getShipping() as $shipping){?>
                        <option><?=$shipping[0]?></option>
                        <?php
                    }
                    ?>
                </datalist>
                <datalist id="ports">
                    <?php
                    foreach ($voyage->getPort() as $port){?>
                        <option><?=$port[0]?></option>
                        <?php
                    }
                    ?>
                </datalist>
        <div class="row">
            <div class="col-md-12 table-responsive">
<table id="voyage" class="display table">
    <thead>
        <tr>
            <th>Reference</th>
            <th>Rotation Number</th>
            <th>Vessel</th>
            <th>Shipping Line</th>
            <th>Arrival Draft</th>
            <th>Gross Tonnage</th>
            <th>Voyage Status</th>
            <th>Estimated Arrival</th>
            <th>Actual Arrival</th>
            <th>Estimated Departure</th>
            <th>Actual Departure</th>
            <th>Previous Port</th>
            <th>Next Port</th>
            <th>Entry Status</th>
            <th>Entry Date</th>
            <th>Job Number</th>
            <th>Gate Open</th>
            <th>Gate Close</th>
            <th>Discharge From</th>
            <th>Discharge To</th>
        </tr>
    </thead>
</table>
</div>
        </div><!-- end of card-body -->
            </div><!-- end of card -->
<?php

require_once 'includes/footer.php';

?>
            <script>
                system_object='<?php echo $system_object?>';
                $(document).ready(function() {
                    Voyage.iniTable();
                });
            </script>