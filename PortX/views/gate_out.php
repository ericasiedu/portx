<?php
use Lib\ACL;
use Lib\Gate;
$system_object="gateout-records";
ACl::verifyRead($system_object);
$title = 'Gate Out Records';
$gateTransactions = 'active open';
$gateOutRecords = 'active';
require_once 'includes/header.php';
require_once 'includes/aside.php';
require_once 'includes/top-header.php';
$gateOut=new Gate();
?>
<main>
    <div class="main-content">
        <div class="card">
            <h4 class="card-title"><strong>Gate Out</strong> Records</h4>
            <div class="card-body">

                <div id="customForm">
                    <editor-field name="gate_record.container_id"></editor-field>
                    <editor-field name="gate_record.type"></editor-field>
                    <editor-field name="gate_record.user_id"></editor-field>
                    <editor-field name="gate_record.depot_id"></editor-field>
                    <editor-field name="gate_record.gate_id"></editor-field>
                    <editor-field name="gate_record.vehicle_id"></editor-field>
                    <editor-field name="gate_record.driver_id"></editor-field>
                    <editor-field name="gate_record.trucking_company_id"></editor-field>
                    <editor-field name="booking.act"></editor-field>
                    <editor-field name="gate_record.cond"></editor-field>
                    <editor-field name="gate_record.date"></editor-field>
                </div>

                <div id="customForm">
<!--                    <div id="conta">dfxgb</div>-->
                    <div data-editor-template="gate_record_container_condition.gate_record"></div>
                    <div data-editor-template="container_number"></div>
                    <div data-editor-template="gate_record_container_condition.container_section"></div>
                    <div data-editor-template="gate_record_container_condition.damage_type"></div>
                    <div data-editor-template="gate_record_container_condition.damage_severity"></div>
                    <div data-editor-template="gate_record_container_condition.note"></div>
                </div>

                <datalist id="container_out">
                    <?php
                    foreach ($gateOut->getContainerOut() as $container){?>
                        <option><?=$container[0]?></option>
                        <?php
                    }
                    ?>
                </datalist>
                <datalist id="gcontainers">
                    <?php
                    foreach ($gateOut->gateContainerOut() as $containerOut){?>
                        <option><?=$containerOut[0]?></option>
                        <?php
                    }
                    ?>
                </datalist>
                <datalist id="companies">
                    <?php
                    foreach ($gateOut->getCompanies() as $company){?>
                        <option><?=$company[0]?></option>
                        <?php
                    }
                    ?>
                </datalist>
                <datalist id="vehicle_out">
                    <?php
                    foreach ($gateOut->vehicleOut() as $vehicle){?>
                        <option><?=$vehicle[0]?></option>
                        <?php
                    }
                    ?>
                </datalist>
                <datalist id="driver_out">
                    <?php
                    foreach ($gateOut->gateOutDrivers() as $driver){?>
                        <option><?=$driver[0]?></option>
                        <?php
                    }
                    ?>
                </datalist>
                <datalist id="lines">
                    <?php
                    foreach ($gateOut->getLines() as $line){?>
                        <option><?=$line[0]?></option>
                        <?php
                    }
                    ?>
                </datalist>
                <datalist id="voyages">
                    <?php
                    foreach ($gateOut->getVoyages() as $voyage){?>
                        <option><?=$voyage[0]?></option>
                        <?php
                    }
                    ?>
                </datalist>
                <datalist id="sections">
                    <?php
                    foreach ($gateOut->getSections() as $section){?>
                        <option><?=$section[0]?></option>
                        <?php
                    }
                    ?>
                </datalist>


        <div class="row">
            <div class="col-md-12 table-responsive">
                <table id="gate_out" class="display table">
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
                        <th>Action</th>
                        <th>Consignee </th>
                        <th>External Reference</th>
                        <th>P Date</th>
                        <th>User</th>
                    </tr>
                    </thead>
                </table>
            </div>

            </div>
        </div><!-- end of card-body -->
            </div><!-- end of card -->
<?php
    require_once 'includes/footer.php';
?>
            <script>
                $(document).ready(function() {
                    system_object='<?php echo $system_object?>';
                    GateOut.iniTable();
                });
            </script>