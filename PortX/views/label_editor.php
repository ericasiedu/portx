<?php
use Lib\ACL;
use Lib\BillTansaction;
$system_object="label-editor";
ACl::verifyRead($system_object);
$title = 'Label Editor';
$userDataManager = 'active open';
$label_editor = 'active';
require_once 'includes/header.php';
require_once 'includes/preloaders.php';
require_once 'includes/aside.php';
require_once 'includes/top-header.php';
$tax_type = new BillTansaction();
?>
<main>
    <div class="main-content">
        <div class="card">
            <h4 class="card-title"><strong>Label</strong> Editor</h4>
            <div class="card-body">

                <div class="row">

                    <div class="col-lg-8" style="float: left">
                        <form class="card" onsubmit="System.updateSystem(event)">
                            <div class="card-body">
                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label">Company Name</label>
                                    <div class="col-sm-8">
                                        <input id="company_name" class="form-control" type="text" maxlength="50">
                                        <span class="text-danger name_error"></span>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label"> Company TIN</label>
                                    <div class="col-sm-8">
                                        <input id="company_tin" class="form-control" type="text" maxlength="20">
                                        <span class="text-danger tin_error"></span>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label">Company Location</label>
                                    <div class="col-sm-8">
                                        <textarea id="company_location" rows="3" class="form-control"></textarea>
                                        <span class="text-danger location_error"></span>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label">Company Phone 1</label>
                                    <div class="col-sm-8">
                                        <input id="company_phone_1" class="form-control" type="text" maxlength="20"/>
                                        <span class="text-danger phone1_error"></span>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label">Company Phone 2</label>
                                    <div class="col-sm-8">
                                        <input id="company_phone_2" class="form-control" type="text" maxlength="20"/>
                                        <span class="text-danger phone2_error"></span>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label">Company Email</label>
                                    <div class="col-sm-8">
                                        <input id="company_email" class="form-control" type="text" maxlength="80"/>
                                        <span class="text-danger email_error"></span>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label">Company Website</label>
                                    <div class="col-sm-8">
                                        <input id="company_web" class="form-control" type="text" maxlength="80"/>
                                        <span class="text-danger web_error"></span>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label">Company Logo</label>
                                    <div class="col-sm-8">
                                        <div class="input-group file-group">
                                            <input type="text" id="logo" class="form-control file-value"  placeholder="Choose file..." readonly>
                                            <input type="file" id="company_logo" name="file"  multiple>
                                            <span class="input-group-btn">
                    <button class="btn btn-light file-browser" type="button"><i class="fa fa-upload"></i></button>
                  </span> </div>
                                        <span class="text-danger logo_error"></span>

                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label">Tax Type</label>
                                    <div class="col-sm-8">
                                        <select id="tax_type" class="form-control">
                                            <?php

                                            foreach ($tax_type->getTaxList() as $tax_list){ ?>
                                                <option value="<?=$tax_list[0]?>" ><?=$tax_list[1];?></option>
                                            <?php  }

                                            ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label">Prefix</label>
                                    <div class="col-sm-8">
                                        <input id="prefix" class="form-control" type="text" maxlength="4"/>
                                        <span class="text-danger prefix_error"></span>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label">Seperator</label>
                                    <div class="col-sm-8">
                                        <input id="idn_seperator" class="form-control" type="text" maxlength="1"/>
                                        <span class="text-danger idn_error"></span>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label"> SW Version</label>
                                    <div class="col-sm-8">
                                        <input id="sw_version" class="form-control" type="text" maxlength="10"/>
                                        <span class="text-danger sw_error"></span>
                                    </div>
                                </div>

                                <footer class="form-group text-left">
                                    <button  class="btn btn-primary" type="submit">Update</button>
                                </footer>
                        </form>
                    </div>
                </div>

                <div class="col-md-4">
                </div><!-- end of label Information -->

            </div><!-- end of card -->
        </div>
        <?php

        require_once 'includes/footer.php';

        ?>

        <script>

            $(document).ready(function () {
                system_object='<?php echo $system_object?>';
                System.systemSetting();
            });

        </script>
