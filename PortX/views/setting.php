<?php
$title = 'Settings';
require_once 'includes/header.php';
require_once 'includes/preloaders.php';
require_once 'includes/aside.php';
require_once 'includes/top-header.php';
?>

<main>
    <div class="main-content">
        <div class="card">
            <h4 class="card-title"><strong>Settings</strong></h4>
            <div class="card-body">
                <div class="col-lg-6" style="float: left">
                    <form class="card">
                        <h4 class="card-title"><strong>Personal</strong> Details</h4>

                        <div class="card-body">
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">First Name</label>
                                <div class="col-sm-8">
                                    <input id="firstName" class="form-control" type="text" maxlength="50">
                                    <span class="first_name_error"></span>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">Last Name</label>
                                <div class="col-sm-8">
                                    <input id="lastName" class="form-control" type="text" maxlength="50">
                                    <span class="last_name_error"></span>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">Phone</label>
                                <div class="col-sm-8">
                                    <input id="phone" class="form-control" type="text" maxlength="20">
                                    <span class="phone_error"></span>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">Email</label>
                                <div class="col-sm-8">
                                    <input id="email" class="form-control" type="text" disabled>
                                </div>
                            </div>
                        </div>

                        <footer class="card-footer text-right">
                            <button onclick="Setting.update()" class="btn btn-primary" type="button">Update</button>
                        </footer>
                    </form>
                </div>

                <div class="col-lg-6" style="float: left">
                    <form class="card">
                        <h4 class="card-title"><strong>Change</strong> Password</h4>

                        <div class="card-body">
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">Old Password</label>
                                <div class="col-sm-8">
                                    <input id="oldPword" onblur="Setting.check()" class="form-control" type="password" maxlength="255">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">New Password</label>
                                <div class="col-sm-8">
                                    <input id="newPword" class="form-control" type="password" maxlength="255">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">Confirm New Password</label>
                                <div class="col-sm-8">
                                    <input id="confPword" class="form-control" type="password" maxlength="255">
                                </div>
                            </div>
                        </div>

                        <footer class="card-footer text-right">
                            <button onclick="Setting.change()" class="btn btn-primary" type="button">Change</button>
                        </footer>
                    </form>
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
        Setting.iniTable();
    });

</script>
