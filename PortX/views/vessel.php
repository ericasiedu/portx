<?php
use Lib\ACL;
use Lib\Vessel;
$system_object="vessel-records";
ACl::verifyRead($system_object);
$title = ' Vessel Master List';
$vesselAndVoyages = 'active open';
$vesselMasterList = 'active';
?>
<?php require_once('includes/header.php'); ?>
<?php require_once('includes/preloaders.php'); ?>
<?php require_once('includes/aside.php'); ?>
<?php require_once('includes/top-header.php');
$country = new Vessel();
$port = new Vessel();
$vessel_type = new Vessel();
?>

<main>

    <div class="main-content">

        <div class="card">
            <h4 class="card-title"><strong>Vessels</strong></h4>
            <div class="card-body">

                <div id="customForm">
                    <editor-field name="vessel.code"></editor-field>
                    <editor-field name="vessel.name"></editor-field>
                    <editor-field name="vessel.length_over_all"></editor-field>
                    <editor-field name="vessel.net_tonnage"></editor-field>
                    <editor-field name="vessel.gross_tonnage"></editor-field>
                    <editor-field name="vessel.dead_weight_tonnage"></editor-field>
                    <editor-field name="vessel.teu_capacity"></editor-field>
                    <editor-field name="vessel.imo_number"></editor-field>
                    <editor-field name="vessel.type_id"></editor-field>
                    <editor-field name="vessel.registry_port_id"></editor-field>
                    <editor-field name="vessel.country_id"></editor-field>
                    <editor-field name="vessel.year_built"></editor-field>
                </div>
                <datalist id="vessel_type">
                    <?php

                        foreach ($vessel_type->vesselTyoe() as $vessels){ ?>
                            <option><?=$vessels[0]?></option>
                       <?php }

                    ?>
                </datalist>
                <datalist id="ports">
                    <?php
                          foreach ($port->getPort() as $ports){ ?>
                              <option><?=$ports[0]?></option>
                          <?php }
                    ?>
                </datalist>
                <datalist id="country">
                    <?php

                       foreach ($country->getCountry() as $countries){ ?>
                           <option><?=$countries[0]?></option>
                      <?php }

                    ?>
                </datalist>

                <div class="row">

                    <div class="col-md-12 table-responsive">

                        <table id="vessel" class="display table">
                            <thead>
                            <tr>
                                <th>Code</th>
                                <th>Name</th>
                                <th>Length Over All</th>
                                <th>Net Tonnage</th>
                                <th>Gross Tonnage</th>
                                <th>Dead Weight Tonnage</th>
                                <th>Teu Capacity</th>
                                <th>Imo Number</th>
                                <th>Vessel Type</th>
                                <th>Registry Port</th>
                                <th>Country</th>
                                <th>Year Built</th>
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
        Vessel.iniTable();
    });

</script>
