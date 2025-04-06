<?php
use Lib\ACL;
$system_object="udm-taxes";
ACl::verifyRead($system_object);
$title = 'Taxes';
$userDataManager = 'active open';
$taxes = 'active';
require_once 'includes/header.php';
require_once 'includes/preloaders.php';
require_once 'includes/aside.php';
require_once 'includes/top-header.php';

?>
<main>
    <div class="main-content">
        <div class="card">
            <h4 class="card-title"><strong>Taxes</strong></h4>
            <div class="card-body">

                <table id="tax" class="display table">
                    <thead>
                    <tr>
                        <th>Type</th>
                        <th>Label</th>
                        <th>Rate</th>
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
                    Tax.iniTable();
                });
            </script>