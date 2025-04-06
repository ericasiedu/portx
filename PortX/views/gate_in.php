<?php
use Lib\ACL,
    Lib\Gate;
    use Lib\BillTansaction;
$system_object="gatein-records";
ACl::verifyRead($system_object);
$title = 'Gate In Records';
$gateTransactions = 'active open';
$gateInRecords = 'active';
require_once 'includes/header.php';
require_once 'includes/preloaders.php';
require_once 'includes/aside.php';
require_once 'includes/top-header.php';
$invoice_transaction = new BillTansaction();
$gate=new Gate();
?>

<main>
    <div class="main-content">
        <div class="card">
            <h4 class="card-title"><strong>Gate In</strong> Records</h4>
            <div class="card-body">
                <div id="customForm">
                    <editor-field name="trade"></editor-field>
                    <editor-field name="gate_record.container_id" ></editor-field>
                    <editor-field name="soc"></editor-field>
                    <editor-field name="gate_record.type"></editor-field>
                    <span id="book_numberID" style="display:none"><editor-field name="book_number"></editor-field></span>
                    <editor-field name="voyage"></editor-field>
                    <editor-field name="shipping_line"></editor-field>
                    <editor-field name="gate_record.depot_id"></editor-field>
                    <editor-field name="gate_record.gate_id"></editor-field>
                    <editor-field name="iso_code"></editor-field>
                    <span id="imdgID" style="display: block;"><editor-field name="imdg"></editor-field></span>
                    <editor-field name="full_status"></editor-field>
                    <editor-field name="oog"></editor-field>
                    <editor-field name="seal_number_1"></editor-field>
                    <editor-field name="seal_number_2"></editor-field>
                    <editor-field name="gate_record.special_seal"></editor-field>
                    <editor-field name="gate_record.vehicle_id"></editor-field>
                    <editor-field name="gate_record.driver_id"></editor-field>
                    <editor-field name="gate_record.trucking_company_id"></editor-field>
                    <span id="consignee" style="display:none"><editor-field name="gate_record.consignee"></editor-field></span>
                    <span id="external_ref"><editor-field name="gate_record.external_reference"></editor-field></span>
                    <editor-field name="gate_record.cond"></editor-field>
                    <editor-field name="gate_record.note"></editor-field>
                    <editor-field name="gate_record.waybill"></editor-field>
                    <editor-field name="gate_record.date"></editor-field>
                    <editor-field name="gate_record.pdate"></editor-field>
                    <editor-field name="gate_record.staff_id"></editor-field>
                </div>
                <div id="customForm">
                    <div data-editor-template="gate_record_container_condition.gate_record"></div>
                    <div data-editor-template="container"></div>
                    <div data-editor-template="gate_record_container_condition.container_section"></div>
                    <div data-editor-template="gate_record_container_condition.damage_type"></div>
                    <div data-editor-template="gate_record_container_condition.damage_severity"></div>
                    <div data-editor-template="gate_record_container_condition.note"></div>
                </div>
                <div class="modal fade" id="DescModal" role="dialog">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-body">
                            <p>dfgbdgbd</p>
                            </div>
                        </div>
                    </div>
                </div>

                <datalist id="containers">

                </datalist>

                <datalist id="customers_name">
                                        <?php

                                        foreach ($invoice_transaction->get_customers() as $customer) { ?>
                                            <option><?= $customer[1] ?></option>
                                        <?php }

                                        ?>
                </datalist>

                <datalist id="iso_code">
                    <?php
                    foreach ($gate->getContainerType() as $iso){ ?>
                        <option><?=$iso[0]?></option>
                    <?php
                    }
                    ?>
                </datalist>
                <datalist id="agents">
                    <?php
                    foreach ($gate->getAgents() as $agency){?>
                        <option><?=$agency[0]?></option>
                        <?php
                    }
                    ?>
                </datalist>

                <datalist id="gcontainers">
                    <?php
                    foreach ($gate->getGatedContainers() as $gcontainer){?>
                        <option><?=$gcontainer[0]?></option>
                        <?php
                    }
                    ?>
                </datalist>
                <datalist id="vehicles">
                    <?php
                    foreach ($gate->getVehicles() as $vehicle){?>
                        <option><?=$vehicle[0]?></option>
                        <?php
                    }
                    ?>
                </datalist>
                <datalist id="drivers">
                    <?php
                    foreach ($gate->getDrivers() as $driver){?>
                        <option><?=$driver[0]?></option>
                        <?php
                    }
                    ?>
                </datalist>
                <datalist id="companies">
                    <?php
                    foreach ($gate->getCompanies() as $company){?>
                        <option><?=$company[0]?></option>
                        <?php
                    }
                    ?>
                </datalist>
                <datalist id="lines">
                    <?php
                    foreach ($gate->getLines() as $line){?>
                        <option><?=$line[0]?></option>
                        <?php
                    }
                    ?>
                </datalist>
                <datalist id="voyages">
                    <?php
                    foreach ($gate->getVoyages() as $voyage){?>
                        <option><?=$voyage[0]?></option>
                        <?php
                    }
                    ?>
                </datalist>
                <datalist id="sections">
                    <?php
                    foreach ($gate->getSections() as $section){?>
                        <option><?=$section[0]?></option>
                        <?php
                    }
                    ?>
                </datalist>
                <datalist id="imdgs">
                    <?php
                    foreach ($gate->getImdg() as $imdg){?>
                        <option><?=$imdg[0]?></option>
                        <?php
                    }
                    ?>
                </datalist>



                <div class="row">
                    <div class="col-md-12 table-responsive">
                        <table id="gate_in" class="display table">
                            <thead>
                            <tr>
                                <th>ID</th>
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
                    GateIn.iniTable();
                });
            </script>