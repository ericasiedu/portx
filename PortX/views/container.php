<?php
use Lib\ACL,
    Lib\Container;
$system_object="container-records";
ACl::verifyRead($system_object);
$title = 'Container Master List';
$vesselAndVoyages = 'active open';
$containerMasterList = 'active';
require_once 'includes/header.php';
require_once 'includes/preloaders.php';
require_once 'includes/aside.php';
require_once 'includes/top-header.php';
$container = new Container();
?>
    <main>
        <div class="main-content">
            <div class="card">
                <h4 class="card-title"><strong>Container</strong> Master List</h4>
                <div class="card-body">

                        <div class="modal modal-center fade" id="modal-small" tabindex="-1">
                            <div class="modal-dialog modal-sm">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h4 class="modal-title" id="statusHeader">Modal title</h4>
                                        <button type="button" class="close" data-dismiss="modal">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <p id="containerStatus">Your content comes here</p>
                                    </div>
                                    <div class="modal-footer">
                                    </div>
                                </div>
                            </div>
                        </div>

                    <datalist id="agents">
                        <?php
                        foreach ($container->getAgents() as $agent){?>
                            <option><?=$agent[0]?></option>
                            <?php
                        }
                        ?>
                    </datalist>
                    <datalist id="lines">
                        <?php
                        foreach ($container->getLines() as $line){?>
                            <option><?=$line[0]?></option>
                            <?php
                        }
                        ?>
                    </datalist>
                    <datalist id="ports">
                        <?php
                        foreach ($container->getPorts() as $port){?>
                            <option><?=$port[0]?></option>
                            <?php
                        }
                        ?>
                    </datalist>
                    <datalist id="types">
                        <?php
                        foreach ($container->getTypes() as $type){?>
                            <option><?=$type[0]?></option>
                            <?php
                        }
                        ?>
                    </datalist>
                    <datalist id="imdg">
                        <?php
                        foreach ($container->getImdg() as $imdg){?>
                            <option><?=$imdg[0]?></option>
                            <?php
                        }
                        ?>
                    </datalist>
                    <datalist id="voyages">
                        <?php
                        foreach ($container->getVoyage() as $voyage){?>
                            <option><?=$voyage[0]?></option>
                            <?php
                        }
                        ?>
                    </datalist>
            <div class="row">
                <div class="col-md-12 table-responsive">
    <table id="container" class="display table">
        <thead>
        <tr>
            <th>Number</th>
            <th>BL Number</th>
            <th>Booking Number</th>
            <th>Voyage</th>
            <th>Seal Number 1</th>
            <th>Seal Number 2</th>
            <th>ICL Seal Number 1</th>
            <th>ICL Seal Number 2</th>
            <th> ISO Type Code</th>
            <th>Soc Status</th>
            <th>Shipping Line</th>
            <th>Agent</th>
            <th>POL</th>
            <th>POD</th>
            <th>FPOD</th>
            <th>Tonnage Weight Metric</th>
            <th>Tonnage Freight</th>
            <th>Trade Type</th>
            <th>Content of Goods</th>
            <th>Importer Address</th>
            <th>IMDG Code</th>
            <th>OOG Status</th>
            <th>Full Status</th>
            <th>Gate Status</th>
            <th>Action</th>
        </tr>
        </thead>
    </table>
                </div>
                </div><!-- end of card-body -->
            </div><!-- end of card -->
</div>
<?php

require_once 'includes/footer.php';

?>

                <script>
                    $(document).ready(function() {
                        system_object='<?php echo $system_object?>';
                        Container.iniTable();
                    });
                </script>