<?php
use Lib\ACL;
$system_object="udm-countries";
ACl::verifyRead($system_object);
$title = 'Countries';  ?>
<?php $userDataManager = 'active open'; ?>
<?php $countries = 'active'; ?>
<?php require_once('includes/header.php'); ?>
<?php require_once('includes/preloaders.php'); ?>
<?php require_once('includes/aside.php'); ?>
<?php require_once('includes/top-header.php');  ?>

<main>

    <div class="main-content">

        <div class="card">
            <h4 class="card-title"><strong>Countries</strong></h4>
            <div class="card-body">

                <div id="customForm">
                    <editor-field name="code"></editor-field>
                    <editor-field name="name"></editor-field>
                </div>

                <div class="row">
                    <div class="col-md-12 table-responsive">

                        <table id="country" class="display table">
                            <thead>
                            <th>Code</th>
                            <th>Name</th>
                            </thead>
                        </table>

                    </div>
                </div>

            </div><!-- end of card body -->
        </div><!-- end of card -->
    </div>


    <?php require_once('includes/footer.php') ?>
    <script>

        $(document).ready(function () {
            system_object='<?php echo $system_object?>';
            Country.iniTable();
        });

    </script>
