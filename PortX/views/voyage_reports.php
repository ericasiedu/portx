<?php
use Lib\ACL;
$system_object="voyage-reports";
ACl::verifyRead($system_object);
$title = 'Voyage Reports';
$reports = 'active open';
$depotReports = 'active';
require_once 'includes/header.php';
require_once 'includes/preloaders.php';
require_once 'includes/aside.php';
require_once 'includes/top-header.php';

?>

<main>
    <div class="main-content">
        <div class="card">
            <h4 class="card-title"><strong>Voyage</strong> <span class="v_report">Reports</span> <span class="v_date">Start Date <input type="date" value="<?php echo date('Y-m-d'); ?>" id="start_date" />
                </span>End Date <input type="date" value="<?php echo date('Y-m-d'); ?>" id="end_date" /></h4>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12 table-responsive">

                        <table id="voyage_report" class="display table">
                            <thead>
                            <tr>
                                <th>Voyage</th>
                                <th>Vessel</th>
                                <th>20FT</th>
                                <th>40FT</th>
                                <th>TEU</th>
                                <th>Arrival Date</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tfoot>
                                <tr>
                                    <th colspan="2"></th>
                                    <th colspan="2"></th>
                                    <th colspan="2"></th>
                                    <th colspan="1"></th>
                                </tr>
                            </tfoot>
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
        VoyageReports.iniTable();
    });
</script>
