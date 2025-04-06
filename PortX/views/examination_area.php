<?php
use Lib\ACL,
    Lib\DepotActivity,
    Lib\Yard;
$system_object="examination-area";
ACl::verifyRead($system_object);
$title = 'Examination Area';
$depotTransactions = 'active open';
$examination_active = 'active';
require_once 'includes/header.php';
require_once 'includes/preloaders.php';
require_once 'includes/aside.php';
require_once 'includes/top-header.php';
$activity = new DepotActivity();
$yard_list = new Yard();
?>
<main>
    <div class="main-content">
        <div class="card">
            <h4 class="card-title"><strong>Examination</strong> Area</h4>
            <div class="card-body">
            <datalist id="activity_list">
                    <?php
                    foreach ($activity->activity_list() as $activities){?>
                        <option><?=$activities[0]?></option>
                    <?php  }
                    ?>
                </datalist>
                <datalist id="equipments">
                <?php 
                    foreach ($yard_list->getEquipment() as $equipment_list) {?>
                        <option><?=$equipment_list[0]?></option>
                  <?php }
                ?>
            </datalist>
            <datalist id="stack_list">
                <?php 
                    foreach ($yard_list->getStackList() as $stack_list) {?>
                        <option><?=$stack_list[0]?></option>
                  <?php }
                ?>
            </datalist>
                <table id="examination" class="display table">
                    <thead>
                        <tr>
                            <th>Container</th>
                            <th>ISO Type Code</th>
                            <th>BL Number</th>
                            <th>Vehicle</th>
                            <th>Driver</th>
                            <th>Consignee</th>
                            <th>Trucking Company</th>
                            <th>User</th>
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
                    ExaminationArea.iniTable();
                });
            </script>