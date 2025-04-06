<?php
use Lib\ACL;
$system_object="udm-reach-stacker";
ACl::verifyRead($system_object);
$title = 'Reach Stacker';
$userDataManager = 'active open';
$reach_stacker = 'active';
require_once 'includes/header.php';
require_once 'includes/preloaders.php';
require_once 'includes/aside.php';
require_once 'includes/top-header.php';

?>
<main>
    <div class="main-content">
        <div class="card">
            <h4 class="card-title"><strong>Reach Stacker</strong></h4>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12 table-responsive">
                        <table id="reach_stack" class="display table">
                            <thead>
                            <tr>
                                <th>Equipment Number</th>
                                <th>Type</th>
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
                    ReachStack.iniTable();
                });
            </script>