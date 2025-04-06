<?php
use Lib\ACL,
    Lib\BillTansaction;
$system_object="udm-exchange_rate";
ACl::verifyRead($system_object);
$title = 'Exchange Rate';
$userDataManager = 'active open';
$exchange_rate = 'active';
require_once 'includes/header.php';
require_once 'includes/preloaders.php';
require_once 'includes/aside.php';
require_once 'includes/top-header.php';
$exchange_rate = new BillTansaction();
?>
<main>
    <div class="main-content">
        <div class="card">
            <h4 class="card-title"><strong>Exchange </strong>Rate</h4>
            <div class="card-body">
                <div class="row">

                    <datalist id="base_list">
                        <?php
                        foreach ($exchange_rate->getExchange() as $currency_code){?>
                            <option><?=$currency_code[0]?></option>
                            <?php
                        }
                        ?>
                    </datalist>

                    <datalist id="quote_list">
                        <?php
                        foreach ($exchange_rate->getExchange() as $currency_code){?>
                            <option><?=$currency_code[0]?></option>
                            <?php
                        }
                        ?>
                    </datalist>

                    <div class="col-md-12 table-responsive">
                        <table id="exchange_rate" class="display table">
                            <thead>
                            <tr>
                                <th>Base</th>
                                <th>Quote</th>
                                <th>Buying</th>
                                <th>Selling</th>
                                <th>User</th>
                                <th>Date</th>
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
                    system_object='<?php echo $system_object?>';
                    ExchangeRate.iniTable();
                });
            </script>