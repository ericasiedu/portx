<?php
use Lib\ACL;
use Lib\Gate, Lib\BillTansaction;

// use Lib\Vessel;
$system_object="bookings";
ACl::verifyRead($system_object);
$title = 'Bookings';
$depotTransactions = 'active open';
$bookings = 'active';
?>
<?php require_once('includes/header.php'); ?>
<?php require_once('includes/preloaders.php'); ?>
<?php require_once('includes/aside.php'); ?>
<?php require_once('includes/top-header.php');
$gate = new Gate();
$invoice_transaction = new BillTansaction();
?>

<main>

    <div class="main-content">

        <div class="card">
            <h4 class="card-title"><strong>Booking</strong> Records</h4>
            <div class="card-body">

                <div id="customForm">
                    <editor-field name="booking.shipping_line_id"></editor-field>
                    <editor-field name="booking.size"></editor-field>
                    <editor-field name="booking.quantity"></editor-field>
                    <editor-field name="booking.booking_number"></editor-field>
                    <editor-field name="container"></editor-field>
                </div>

                <div class="row">

                    <div class="col-md-12 table-responsive">
                        <datalist id="containers"></datalist>

                        <datalist id="customers">
                            <?php
                                foreach ($invoice_transaction->get_customers() as $customer) { ?>
                                    <option><?= $customer[1] ?></option>
                                <?php }
                            ?>
                        </datalist>

                        <table id="booking" class="display table">
                            <thead>
                            <tr>
                                <th>Shipping Line</th>
                                <th>Booked by Party</th>
                                <th>Size</th>
                                <th>Quantity</th>
                                <th>ACT</th>
                                <th>Booking Number</th>
                                <th>Date</th>
                                <th>Containers</th>
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
        Booking.iniTable();
    });

</script>
