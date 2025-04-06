<?php
use Lib\ACL;
$system_object="udm-container-type-codes";
ACl::verifyRead($system_object);
$title = 'Container Type Codes';
$userDataManager = 'active open';
$container_type_codes = 'active';
require_once 'includes/header.php';
require_once 'includes/preloaders.php';
require_once 'includes/aside.php';
require_once 'includes/top-header.php';

?>
    <main>
        <div class="main-content">
            <div class="card">
                <h4 class="card-title"><strong>Container Type</strong> Codes</h4>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12 table-responsive">
                            <table id="container_codes" class="display table">
                                <thead>
                                <tr>
                                    <th>Code</th>
                                    <th>Description</th>
                                    <th>Length</th>
                                    <th>Height</th>
                                    <th>Group</th>
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

                    $(document).ready(function () {
                        system_object='<?php echo $system_object?>';
                        ContainerCodes.iniTable();
                    });

                </script>
