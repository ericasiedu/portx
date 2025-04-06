<?php
use Lib\ACL,
    Lib\Yard;
$system_object="yard-planning";
ACl::verifyRead($system_object);
$title = 'Yard Planning';
$depotTransactions = 'active open';
$yard_plan = 'active';
require_once 'includes/header.php';
require_once 'includes/preloaders.php';
require_once 'includes/aside.php';
require_once 'includes/top-header.php';
$yard_list = new Yard();
?>
<main>
    <div class="main-content">
        <div class="card">
            <h4 class="card-title"><strong>Yard </strong>Planning</h4>
            <div class="card-body">

            <datalist id="bays"></datalist>

            <datalist id="stack_list">
                <?php 
                    foreach ($yard_list->getStackList() as $stack_list) {?>
                        <option><?=$stack_list[0]?></option>
                  <?php }
                ?>
            </datalist>

            <datalist id="vehicle_list">
              
            </datalist>

            <datalist id="equipments">
                <?php 
                    foreach ($yard_list->getEquipment() as $equipment_list) {?>
                        <option><?=$equipment_list[0]?></option>
                  <?php }
                ?>
            </datalist>

                <table id="yard_plan" class="display table">
                    <thead>
                    <tr>
                        <th>Container ID</th>
                        <th>Act-Type</th>
                        <th>EMX</th>
                        <th>Trade Type</th>
                        <th>3PV Type</th>
                        <th>Stack</th>
                        <th>Position</th>
                        <th>Container Number</th>
                        <th>Size</th>
                        <th>Opr Code</th>
                        <th>Owr Code</th>
                        <th>LVRF De-Plug</th>
                        <th>Time Spent</th>
                        <th>Vehicle or Train Number</th>
                        <th>Equipment Number</th>
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
                    YardPlan.iniTable();
                });
            </script>