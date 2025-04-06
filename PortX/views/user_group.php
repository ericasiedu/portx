<?php
use Lib\ACL;
$system_object="groups";
ACl::verifyRead($system_object);
$title = 'User Group Manager';
$systemSetup = 'active open';
$userGroup = 'active';
require_once 'includes/header.php';
require_once 'includes/preloaders.php';
require_once 'includes/aside.php';
require_once 'includes/top-header.php';
?>
<main>
    <div class="main-content">
        <div class="card">
            <h4 class="card-title"><strong>User Group </strong>Manager</h4>
            <div class="card-body">
                <div id="customForm">
                    <editor-field name="name"></editor-field>
                    <editor-field name="status"></editor-field>
                    <editor-field name="deleted"></editor-field>
                </div>
                <div class="row">
                    <div class="col-md-12 table-responsive">
                        <table id="user_group" class="display table">
                            <thead>
                            <tr>
                                <th>Name</th>
                                <th>Status</th>
                                <th>Deleted</th>
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

                $(document).ready(function () {
                    UserGroups.iniTable();
                });

            </script>
