<?php
use Lib\ACL;
$system_object="udm-ucl-settings";
ACl::verifyRead($system_object);
$title = 'UCL Settings';
$userDataManager = 'active open';
$ucl = 'active';
require_once 'includes/header.php';
require_once 'includes/preloaders.php';
require_once 'includes/aside.php';
require_once 'includes/top-header.php';

?>
<main>
    <div class="main-content">
        <div class="card">
            <h4 class="card-title"><strong>UCL </strong>Settings</h4>
            <div id="ucl_set" class="card-body">
                <?php

                if (!ACL::canUpdate("udm-ucl-settings")){ ?>
                <div class="row">
                    <div class="col-md-3">
                        <p><label for="uCL Days">UCL Days</label></p>
                        <p><input type="text" id="ucl_days" readonly></p>
                    </div>
                    <div class="col-md-3">
                        <p><label for="20 FT FIxed Charge">20 FT FIxed Charge</label></p>
                        <p><input type="text" id="20_ft_charge" readonly></p>
                    </div>
                    <div class="col-md-3">
                        <p><label for="40 FT FIxed Charge">40 FT FIxed Charge</label></p>
                        <p><input type="text" id="40_ft_charge" readonly></p>
                    </div>
                    <div class="col-md-3">
                        <p><label for="45 FT FIxed Charge">45 FT FIxed Charge</label></p>
                        <p><input type="text" id="45_ft_charge" readonly></p>
                    </div>
                </div> <!-- end row -->
                <?php }
                    else{ ?>
                <div class="row">
                    <div class="col-md-3">
                            <p><label for="uCL Days">UCL Days</label></p>
                            <p><input type="text" id="ucl_days"></p>
                        </div>
                    <div class="col-md-3">
                            <p><label for="20 FT FIxed Charge">20 FT FIxed Charge</label></p>
                            <p><input type="text" id="20_ft_charge"></p>
                        </div>
                    <div class="col-md-3">
                            <p><label for="40 FT FIxed Charge">40 FT FIxed Charge</label></p>
                            <p><input type="text" id="40_ft_charge"></p>
                        </div>
                    <div class="col-md-3">
                            <p><label for="45 FT FIxed Charge">45 FT FIxed Charge</label></p>
                            <p><input type="text" id="45_ft_charge"></p>
                        </div>
                </div><!-- end of row -->
                        <div class="row">
                            <div class="col-md-12">
                                <p><button id="ucl_setting" class="btn btn-primary">Update</button></p>
                            </div>
                        </div><!-- end of row -->

                <?php }?>
            </div><!-- end of card-body -->
        </div><!-- end of card -->
    </div>

    <?php
    require_once 'includes/footer.php';
    ?>

    <script>

        $(document).ready(function () {
            system_object = '<?php echo $system_object?>';
            UCL.iniTable();
        });

    </script>

