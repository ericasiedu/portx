<?php
use Lib\ACL;
$system_object="udm-stack";
ACl::verifyRead($system_object);
$title = 'Stack';
$userDataManager = 'active open';
$stack = 'active';
require_once 'includes/header.php';
require_once 'includes/preloaders.php';
require_once 'includes/aside.php';
require_once 'includes/top-header.php';

?>
<main>
    <div class="main-content">
        <div class="card">
            <h4 class="card-title"><strong>Stack</strong></h4>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12 table-responsive">
                        <table id="stack_table" class="display table">
                            <thead>
                            <tr>
                                <th>Name</th>
                                <th>Stack Type</th>
                                <th>Date</th>
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
                    Stack.iniTable();
                });
            </script>