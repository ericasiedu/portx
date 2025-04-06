<?php
use Lib\ACL;
$system_object="user-account";
ACl::verifyRead($system_object);
$title = 'User Account Manager';
$systemSetup = 'active open';
$userAccount = 'active';
require_once 'includes/header.php';
require_once 'includes/preloaders.php';
require_once 'includes/aside.php';
require_once 'includes/top-header.php';

?>
<main>
    <div class="main-content">
        <div class="card">
            <h4 class="card-title"><strong>User</strong> Account Manager</h4>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12 table-responsive">
                        <table id="user_account" class="display table">
                            <thead>
                            <tr>
                                <th>First Name</th>
                                <th>Last Name</th>
                                <th>Phone</th>
                                <th>Email</th>
                                <th>Status</th>
                                <th>Group</th>
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
                    UserAccount.iniTable();
                });
            </script>