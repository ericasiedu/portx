<?php
require_once 'includes/header.php';
require_once 'includes/preloaders.php';
require_once 'includes/aside.php';
require_once 'includes/top-header.php';
$title = "Container History";

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>
<main>
    <div class="main-content">
        <div class="card">
            <h4 class="card-title"><strong>Container History</strong></h4>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12 table-responsive">
                        <table id="history" class="display table">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Number</th>
                                <th>BL Number</th>
                                <th>Booking Number</th>
                                <th>Trade Type</th>
                                <th>Length</th>
                                <th>Gate In</th>
                                <th>Gate Out</th>
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
                    search = localStorage.getItem('search');
                    if(search) {
                        localStorage.setItem('search', '');
                        ContainerHistory.iniTable();
                    }
                    else {
                        $('#history').remove();
                        Modaler.dModal("Empty search Query", "Please enter a search query");
                    }
                });
            </script>