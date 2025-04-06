<?php
use Lib\ACL;
$system_object="move-to-export";
ACl::verifyRead($system_object);
$title = 'Move To Export';
$depotTransactions = 'active open';
$moveToExport = 'active';
require_once 'includes/header.php';
require_once 'includes/preloaders.php';
require_once 'includes/aside.php';
require_once 'includes/top-header.php';

?>
<main>
    <div class="main-content">
        <div class="card">
            <h4 class="card-title"><strong>Stuffing</strong> (Export)</h4>
            <div class="card-body">
                <div id="customForm">
                    <editor-field name="code"></editor-field>
                    <editor-field name="name"></editor-field>
                </div>
                <div class="row">
                    <div class="col-md-12 table-responsive">
                        <table id="move-to-export" class="display table">
                            <thead>
                            <tr>
                                <th>Container</th>
                                <th>Shipping Line</th>
                                <th>Empty Booking</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                        </table>
                    </div><!-- end of card-body -->
                </div><!-- end of card -->
            </div>

            <div class="move-title">
                <h5 class="card-title"><strong>Export</strong> Booking Number</h5>
            </div>
            <div class="move-form card">
                <div class="card-body">
                    <input type="text" class="form-control" placeholder="Booking Number"><br>
                    <textarea class="form-control" placeholder="Content of Goods"></textarea><br>
                    <p>
                        <button type="button" class="btn btn-primary">MOVE
                        </button>
                    </p>
                </div>
            </div>
            <?php

            require_once 'includes/footer.php';

            ?>

            <script>
                $(document).ready(function() {
                    system_object='<?php echo $system_object?>';
                    MoveToExport.iniTable();
                });
            </script>