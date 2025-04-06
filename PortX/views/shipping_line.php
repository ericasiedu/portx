<?php
use Lib\ACL;
$system_object="udm-shipping-line";
ACl::verifyRead($system_object);
?>
<?php $title = ' Shipping Lines'; ?>
<?php $userDataManager = 'active open'; ?>
<?php $shipping_lines = 'active'; ?>
<?php require_once('includes/header.php'); ?>
<?php require_once('includes/preloaders.php'); ?>
<?php require_once('includes/aside.php'); ?>
<?php require_once('includes/top-header.php'); ?>

<main>

    <div class="main-content">

        <div class="card">
            <h4 class="card-title"><strong>Shipping</strong> Lines</h4>
            <div class="card-body">

                <div id="customForm">
                    <editor-field name="code"></editor-field>
                    <editor-field name="name"></editor-field>
                </div>

                <div class="row">
                    <div class="col-md-12 table-responsive">

                        <table id="shipping_line" class="display table">
                            <thead>
                            <tr>
                                <th>Code</th>
                                <th>Name</th>
                            </tr>
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
            ShippingLine.iniTable();
        });

    </script>
