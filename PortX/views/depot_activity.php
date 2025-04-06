<?php
use Lib\ACL;
$system_object="udm-depot-activity";
ACl::verifyRead($system_object);
$title = 'Depot Activity';
$depot_activity = 'active';
require_once 'includes/header.php';
require_once 'includes/preloaders.php';
require_once 'includes/aside.php';
require_once 'includes/top-header.php';

?>
<main>
    <div class="main-content">
        <div class="card">
            <h4 class="card-title"><strong>Depot Activity</strong></h4>
            <div class="card-body">
                <div id="depotForm">
                    <editor-field name="name"></editor-field>
                    <editor-field name="billable"></editor-field>
                    <editor-field name="is_default"></editor-field>
                </div>
                <div class="row">
                    <div class="col-md-12 table-responsive">
                        <table id="depotActivity" class="display table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Default</th>
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
                    DepotActivity.iniTable();
                });
            </script>