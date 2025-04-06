<?php
use Lib\ACL;
$system_object="udm-trucking-companies";
ACl::verifyRead($system_object);
$title = 'Trucking Companies';
$userDataManager = 'active open';
$truckingCompanies = 'active';
require_once 'includes/header.php';
require_once 'includes/preloaders.php';
require_once 'includes/aside.php';
require_once 'includes/top-header.php';
?>
<main>
    <div class="main-content">
        <div class="card">
            <h4 class="card-title"><strong>Trucking</strong> Companies</h4>
            <div class="card-body">
                <div id="customForm">
                    <editor-field name="name"></editor-field>
                </div>
                <div class="row">
                    <div class="col-md-12 table-responsive">
                        <table id="company" class="display table">
                            <thead>
                            <tr>
                                <th>Name</th>
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
                    TruckingCompany.iniTable();
                });
            </script>