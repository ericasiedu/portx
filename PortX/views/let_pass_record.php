<?php
use Lib\ACL;
$system_object="let-pass-records";
ACl::verifyRead($system_object);
$title = 'Let Pass Records';
$gateTransactions = 'active open';
$let_pass = 'active';
require_once 'includes/header.php';
require_once 'includes/preloaders.php';
require_once 'includes/aside.php';
require_once 'includes/top-header.php';

?>
<main>
    <div class="main-content">
        <div class="card">
            <h4 class="card-title"><strong>Let Pass </strong>Records</h4>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12 table-responsive">
                        <table id="let_pass_record" class="display table">
                            <thead>
                            <tr>
                                <th>Number</th>
                                <th>Invoice NUmber</th>
                                <th>Date </th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                        </table>
                    </div><!-- end of card-body -->
                </div>
            </div>
            <?php

            require_once 'includes/footer.php';

            ?>

            <script>
                $(document).ready(function() {
                    system_object = '<?php echo $system_object?>';
                    LetPass.iniTable();
                });
            </script>