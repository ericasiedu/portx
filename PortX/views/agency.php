<?php
use Lib\ACL;
$system_object="udm-agency";
ACl::verifyRead($system_object);
$title = 'Agency';
$userDataManager = 'active open';
$clearingAgents = 'active';
require_once 'includes/header.php';
require_once 'includes/preloaders.php';
require_once 'includes/aside.php';
require_once 'includes/top-header.php';

?>
<main>
    <div class="main-content">
        <div class="card">
            <h4 class="card-title"><strong>Agency</strong> </h4>
            <div class="card-body">
                <div id="customForm">
                    <editor-field name="code"></editor-field>
                    <editor-field name="name"></editor-field>
                    <editor-field name="address_line_1"></editor-field>
                    <editor-field name="address_line_2"></editor-field>
                    <editor-field name="address_line_3"></editor-field>
                    <editor-field name="telephone"></editor-field>
                    <editor-field name="email"></editor-field>
                    <editor-field name="fax"></editor-field>
                </div>
                <div class="row">
                    <div class="col-md-12 table-responsive">
                        <table id="agency" class="display table">
                            <thead>
                            <tr>
                                <th>Code</th>
                                <th>Name</th>
                                <th>Address Line 1</th>
                                <th>Address Line 2</th>
                                <th>Address Line 3</th>
                                <th>Telephone</th>
                                <th>Email</th>
                                <th>Fax</th>
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
                    Agency.iniTable();
                });
            </script>