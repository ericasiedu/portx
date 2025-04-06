<?php

use Lib\ACL;
use Lib\BillTansaction;

$system_object = "let-pass-generation";
ACl::verifyRead($system_object);
$title = 'Generate Let Pass';
$gateTransactions = 'active open';
$pass = 'active';
require_once 'includes/header.php';
require_once 'includes/preloaders.php';
require_once 'includes/aside.php';
require_once 'includes/top-header.php';

?>

<main>
    <div class="main-content">
        <div class="card">
            <h4 class="card-title"><strong><?php echo $title?></strong></h4>
            <div class="card-body">


                <div class="nav-tabs-left">
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs nav-tabs-success">
                        <li class="nav-item"><a class="nav-link active" id="invoice-tab" data-toggle="tab" href="#invoice-view">Invoice</a>
                        </li>
                        <li class="nav-item"><a class="nav-link" id="container-tab" data-toggle="tab">Container</a>
                        </li>
                        <li class="nav-item"><a class="nav-link" id="driver-tab" data-toggle="tab">Driver</a>
                        </li>
                        <li class="nav-item"><a class="nav-link" id="letpass-tab" data-toggle="tab">Let Pass</a></li>
                    </ul>
                    <!-- Tab panes -->
                    <div class="tab-content">
                        <div class="tab-pane fade active show" id="invoice-view">
                            <div style="margin-bottom: 10px">
                                <label>Invoice Number</label>
                                <input type="text"  class="form-control" id="invoice">
                                <span id="invoice-error" style="color:red;"></span>
                            </div>
                            <button type="button" id="invoice-button" class="btn btn-primary">NEXT</button>
                        </div>
                        <div class="tab-pane fade" id="container-view">
                            <h4>Container List</h4>
                            <div class="media-list-body scrollable ps-container ps-theme-default">
                                <ul id="container" style="list-style-type: none">
                                </ul>
                                <span id="selection_error" style="color:red;"></span>
                            </div>
                            <button type="button" id="container-button" class="btn btn-primary">NEXT</button>
                        </div>
                        <div class="tab-pane fade" id="driver-view">
                            <div class="driver_heading" style="width:100%"><h4>Drivers</h4></div>
                            <div style="margin: 10px">
                                <form id="drivers" action="" onsubmit="">
                                   <div id="wrapper_input">
                                        <div id="letpass_wrapper" class="row letpass_form"></div>
                                   </div>
                                   
                                        <!-- <p class="driver">
                                        <input style="margin-right: 10px" placeholder="Vehicle License Plate" maxlength="18"> 
                                        <span class="driver_error" style="color:red;"></span>
                                        <input placeholder="Driver Name" maxlength="200"> <br>
                                        <span class="driver_error" style="color:red;"></span></p> -->
                                   
                                

                                    <!-- <p class="driver"><input style="margin-right: 10px" placeholder="Vehicle License Plate" maxlength="18"> <input placeholder="Driver Name" maxlength="200"> <br>
                                        <span class="driver_error" style="color:red;"></span></p>
                                    <p class="driver"><input style="margin-right: 10px" placeholder="Vehicle License Plate" maxlength="18"> <input placeholder="Driver Name" maxlength="200"><br>
                                        <span class="driver_error" style="color:red;"></span></p>
                                    <p class="driver"><input style="margin-right: 10px" placeholder="Vehicle License Plate" maxlength="18"> <input placeholder="Driver Name" maxlength="200"><br>
                                        <span class="driver_error" style="color:red;"></span></p>
                                    <p class="driver"><input style="margin-right: 10px" placeholder="Vehicle License Plate" maxlength="18"> <input placeholder="Driver Name" maxlength="200"><br>
                                        <span class="driver_error" style="color:red;"></span></p>
                                    <p class="driver"><input style="margin-right: 10px" placeholder="Vehicle License Plate" maxlength="18"> <input placeholder="Driver Name" maxlength="200"><br>
                                        <span class="driver_error" style="color:red;"></span></p>
                                    <p class="driver"><input style="margin-right: 10px" placeholder="Vehicle License Plate" maxlength="18"> <input placeholder="Driver Name" maxlength="200"><br>
                                        <span class="driver_error" style="color:red;"></span></p>
                                    <p class="driver"><input style="margin-right: 10px" placeholder="Vehicle License Plate" maxlength="18"> <input placeholder="Driver Name" maxlength="200"><br>
                                        <span class="driver_error" style="color:red;"></span></p>
                                    <p class="driver"><input style="margin-right: 10px" placeholder="Vehicle License Plate" maxlength="18"> <input placeholder="Driver Name" maxlength="200"><br>
                                        <span class="driver_error" style="color:red;"></span></p>
                                    <p class="driver"><input style="margin-right: 10px" placeholder="Vehicle License Plate" maxlength="18"> <input placeholder="Driver Name" maxlength="200"><br>
                                        <span class="driver_error" style="color:red;"></span></p>
                                    <p class="driver"><input style="margin-right: 10px" placeholder="Vehicle License Plate" maxlength="18"> <input placeholder="Driver Name" maxlength="200"><br>
                                        <span class="driver_error" style="color:red;"></span></p> -->
                                </form>
                                <span id="drivers_error" style="color:red;"></span>
                            </div>

                            <button type="button" id="driver-button" class="btn btn-primary">NEXT</button>
                        </div>
                        <div class="tab-pane fade" id="letpass-view">
                            <h4>Let Pass</h4>
                            <span id="letpass_link"></span>
                        </div>
                    </div>


                </div>

            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-4">

        <div class="modal modal-center fade" id="modal-small" tabindex="-1">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="manualHeader">Modal title</h4>
                        <button type="button" class="close" data-dismiss="modal">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p id="manualAlert">Your content comes here</p>
                    </div>
                    <div class="modal-footer">

                    </div>
                </div>
            </div>
        </div>
    </div>

</main>

<?php

require_once 'includes/footer.php';

?>
<script>

    $(document).ready(function () {
        system_object = '<?php //echo $system_object?>//';
         LetPass.iniTable();
    });

</script>
