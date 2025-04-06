<?php $title = ' Dashboard';
use Lib\DashboardReport;
?>
<?php require_once('includes/header.php'); ?>
<?php require_once('includes/preloaders.php'); ?>
<?php require_once('includes/aside.php'); ?>
<?php require_once('includes/top-header.php');
$laden = new DashboardReport();
$export = new DashboardReport();
$activity = new DashboardReport();
?>

<!-- Main container -->
<main>

    <div class="main-content">
        <div class="row">



            <div class="col-md-4">
                <div class="card card-body depot-info">
                    <div class="flexbox">
                        <div class="text-left">
                            <span class="fw-400">LADEN</span><br>
                            <span>
                    <span class="fs-18 ml-1" id="laden"></span>
                  </span>
                        </div>
                        <img class="text-right depot-info-img" src="/img/laden.png"/>
                    </div>
                </div>
            </div>


            <div class="col-md-4">
                <div class="card card-body depot-info">
                    <div class="flexbox">
                        <div class="text-left">
                            <span class="fw-400">EMPTY</span><br>
                            <span>
                    <span class="fs-18 ml-1" id="depot_empty"></span>
                  </span>
                        </div>
                        <img class="text-right depot-info-img" src="/img/empty.png"/>
                    </div>
                </div>
            </div>


            <div class="col-md-4">
                <div class="card card-body depot-info">
                    <div class="flexbox">
                        <div class="text-left">
                            <span class="fw-400">EXPORT</span><br>
                            <span>
                    <span class="fs-18 ml-1" id="export"></span>
                  </span>
                        </div>
                        <img class="text-right depot-info-img" src="/img/export.png"/>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title"><strong>Activity Report</strong></h5>

                    </div>

                    <div class="card-body">
                            <table id="dashboard_activity" class="display table dataTable" style="width:100%">
                                <thead>
                                <tr>
                                    <th class="text-center">Activity</th>
                                    <th class="text-center">20 FT</th>
                                    <th class="text-center">40 FT</th>
                                </tr>
                                </thead>
                            </table>
                        </div><!-- end of card-body -->

                </div>
            </div><!-- end of activity report -->

            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title"><strong>Paid Invoices/Containers</strong></h5>
                    </div><!-- end of header -->
                    <div class="card-body">

                        <p style="font-weight: bolder; font-size: 14px; padding-bottom: 10px"><span style="border-bottom: 2px solid red; padding-bottom:10px">LADEN</span> &nbsp;<span id="laden_amount"></span></p>
                        <p style="font-weight: bolder; font-size: 14px; padding-bottom: 10px"><span style="border-bottom: 2px solid red; padding-bottom:10px">EXPORT</span> &nbsp; <span id="export_amount"></span></p>

                    </div><!-- end of card body -->
        </div>



        </div><!--/.main-content -->
    <!-- END Main container -->

<?php require_once('includes/footer.php') ?>
    <script>

        $(document).ready(function () {
           Dashboard.Overview();
        });

    </script>
