<?php
use Lib\ACL,
    Lib\Yard;
$system_object="operator-view";
ACl::verifyRead($system_object);
$title = 'Operator View';
$depotTransactions = 'active open';
$operatorActive = 'active';
require_once 'includes/header.php';
require_once 'includes/preloaders.php';
require_once 'includes/aside.php';
require_once 'includes/top-header.php';
$yard_list = new Yard();
?>
<main>
    <div class="main-content">
        <div class="card">
            <h4 class="card-title"><strong>Operator</strong> View</h4>
            <div class="card-body">

            <datalist id="bays"></datalist>

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

                <div class="row">
                    <div class="col-md-12 table-responsive">
                        <table id="operator_view_tbl" class="display table">
                            <thead>
                            <tr>
                                <th>Container Number</th>
                                <th>EMX</th>
                                <th>Depot OPS Size Type</th>
                                <th>Opr Code</th>
                                <th>Owr Code</th>
                                <th>LVRF De-Plug</th>
                                <th>Stack</th>
                                <th>Position</th>
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
                    OperatorView.iniTable();
                });
            </script>