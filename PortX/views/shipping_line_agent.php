<?php
use Lib\ACL;
use Lib\Ships;
$system_object="udm-shipping-line-agents";
ACl::verifyRead($system_object);
$title = ' Shipping Line Agents';
$userDataManager = 'active open';
$shippingLineAgents = 'active';
?>
<?php require_once('includes/header.php'); ?>
<?php require_once('includes/preloaders.php'); ?>
<?php require_once('includes/aside.php'); ?>
<?php require_once('includes/top-header.php');
$shippers = new Ships();
?>

<main>

    <div class="main-content">

        <div class="card">
            <h4 class="card-title"><strong>Shipping Line</strong> Agents</h4>
            <div class="card-body">

                <div id="customForm">
                    <editor-field name="shipping_line_agent.line_id"></editor-field>
                    <editor-field name="shipping_line_agent.code"></editor-field>
                    <editor-field name="shipping_line_agent.name"></editor-field>
                    <editor-field name="shipping_line_agent.date"></editor-field>
                </div>
                <datalist id="ship_agents">
                    <?php

                          foreach ($shippers->getShipAgent() as $ship_agent){ ?>
                              <option><?=$ship_agent[0]?></option>
                         <?php }

                    ?>
                </datalist>

                <div class="row">
                    <div class="col-md-12 table-responsive">

                        <table id="ship_agent" class="display table">
                            <thead>
                            <tr>
                                <th>Shipping Line</th>
                                <th>Code</th>
                                <th>Name</th>
                                <th>Date</th>
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
            ShippingLineAgents.iniTable();
        });

    </script>
