<?php
use Lib\ACL;
?>

<!-- Sidebar -->
<aside class="sidebar sidebar-expand-lg">
    <header class="sidebar-header">
        <a class="logo-icon" href="#"><img src="/img/logo-icon-light.png" alt="logo icon"></a>
        <span class="logo">
          <a href="#">Pro Port</a>
        </span>
        <span class="sidebar-toggle-fold"></span>
    </header>

    <nav class="sidebar-navigation">
        <ul class="menu">

            <li class="menu-category">Welcome</li>

            <li class="menu-item active">
                <a class="menu-link" href="/user/dashboard">
                    <span class="icon fa fa-home"></span>
                    <span class="title">Dashboard</span>
                </a>
            </li>


            <li class="menu-category">Menu</li>


            <li class="menu-item <?php echo $vesselAndVoyages; ?>">
                <a class="menu-link" href="#">
                    <span class="icon ti-layout"></span>
                    <span class="title">Vessels &amp; Voyages</span>
                    <span class="arrow"></span>
                </a>

                <ul class="menu-submenu">
                    <?php if (ACL::canRead('voyage-records')) :?>
                    <li class="menu-item <?php echo $vesselVoyages; ?>">
                        <a class="menu-link" href="/user/voyage">
                            <span class="icon"></span>
                            <span class="title">Voyage Records</span>
                        </a>
                    </li>
                    <?php endif;?>
                    <?php if (ACL::canRead('vessel-records')) :?>
                    <li class="menu-item <?php echo $vesselMasterList; ?>">
                        <a class="menu-link" href="/user/vessel">
                            <span class="icon"></span>
                            <span class="title">Vessel Records</span>
                        </a>
                    </li>
                    <?php endif;?>
                    <?php if (ACL::canRead('container-records')) :?>
                    <li class="menu-item <?php echo $containerMasterList; ?>">
                        <a class="menu-link" href="/user/container">
                            <span class="icon"></span>
                            <span class="title">Container Records</span>
                        </a>
                    </li>
                    <?php endif;?>
                    <?php if (ACL::canRead('excel-upload')) :?>
                    <li class="menu-item <?php echo $excelReader; ?>">
                        <a class="menu-link" href="/user/import">
                            <span class="icon"></span>
                            <span class="title">Excel Upload</span>
                        </a>
                    </li>
                    <?php endif;?>
                </ul>
            </li>


            <li class="menu-item <?php echo $gateTransactions; ?>">
                <a class="menu-link" href="#">
                    <span class="icon fa fa-align-left"></span>
                    <span class="title">Gate Transactions</span>
                    <span class="arrow"></span>
                </a>

                <ul class="menu-submenu">
                    <?php if (ACL::canRead('gatein-records')) :?>
                    <li class="menu-item <?php echo $gateInRecords; ?>">
                        <a class="menu-link" href="/user/gate_in">
                            <span class="icon"></span>
                            <span class="title">Gate In Records</span>
                        </a>
                    </li>
                    <?php endif;?>
                    <?php if (ACL::canRead('gateout-records')) :?>
                    <li class="menu-item <?php echo $gateOutRecords; ?>">
                        <a class="menu-link" href="/user/gate_out">
                            <span class="icon"></span>
                            <span class="title">Gate Out Records</span>
                        </a>
                    </li>
                    <?php endif;?>
                    <?php if (ACL::canRead('let-pass-generation')) :?>
                        <li class="menu-item <?php echo $pass; ?>">
                            <a class="menu-link" href="/user/let_pass">
                                <span class="icon"></span>
                                <span class="title">Generate Let Pass</span>
                            </a>
                        </li>
                    <?php endif;?>
                    <?php if (ACL::canRead('let-pass-records')) :?>
                    <li class="menu-item <?php echo $let_pass; ?>">
                        <a class="menu-link" href="/user/let_pass_record">
                            <span class="icon"></span>
                            <span class="title">Let Pass Records</span>
                        </a>
                    </li>
                    <?php endif;?>
                    <?php if (ACL::canRead('truck-record')) :?>
                    <li class="menu-item <?php echo $truck; ?>">
                        <a class="menu-link" href="/user/bare_chasis">
                            <span class="icon"></span>
                            <span class="title">Bare Chassis</span>
                        </a>
                    </li>
                    <?php endif;?>
                </ul>
            </li>


            <li class="menu-item <?php echo $depotTransactions; ?>">
                <a class="menu-link" href="#">
                    <span class="icon fa fa-plus-circle"></span>
                    <span class="title">Depot Transactions</span>
                    <span class="arrow"></span>
                </a>

                <ul class="menu-submenu">
                    <?php if (ACL::canRead('depot-overview')) :?>
                    <li class="menu-item <?php echo $depotOverview; ?>">
                        <a class="menu-link" href="/user/depot_over">
                            <span class="icon"></span>
                            <span class="title">Depot Overview</span>
                        </a>
                    </li>
                    <?php endif;?>
                    <?php if (ACL::canRead('ucl-depot')) :?>
                        <li class="menu-item <?php echo $uclActive; ?>">
                            <a class="menu-link" href="/user/ucl_depot">
                                <span class="icon"></span>
                                <span class="title">UCL</span>
                            </a>
                        </li>
                    <?php endif;?>
                    <?php if (ACL::canRead('empty-bookings')) :?>
                        <li class="menu-item <?php echo $emptyBookings; ?>">
                            <a class="menu-link" href="/user/empty_bookings">
                                <span class="icon"></span>
                                <span class="title">Create Bookings</span>
                            </a>
                        </li>
                    <?php endif;?>
                    <?php if (ACL::canRead('bookings')) :?>
                        <li class="menu-item <?php echo $bookings; ?>">
                            <a class="menu-link" href="/user/bookings">
                                <span class="icon"></span>
                                <span class="title">Booking Records</span>
                            </a>
                        </li>
                    <?php endif;?>
                    <?php if (ACL::canRead('move-to-export')) :?>
                        <li class="menu-item <?php echo $moveToExport; ?>">
                            <a class="menu-link" href="/user/move_to_export">
                                <span class="icon"></span>
                                <span class="title">Stuffing (Export)</span>
                            </a>
                        </li>
                    <?php endif;?>
                    <?php if (ACL::canRead('yard-planning')) :?>
                        <li class="menu-item <?php echo $yard_plan; ?>">
                            <a class="menu-link" href="/user/yard_planning">
                                <span class="icon"></span>
                                <span class="title">Yard Planning</span>
                            </a>
                        </li>
                    <?php endif;?>
                    <?php if (ACL::canRead('operator-view')) :?>
                        <li class="menu-item <?php echo $operatorActive; ?>">
                            <a class="menu-link" href="/user/operator_view">
                                <span class="icon"></span>
                                <span class="title">Operator View</span>
                            </a>
                        </li>
                    <?php endif;?>
                    <?php if (ACL::canRead('examination-area')) :?>
                        <li class="menu-item <?php echo $examination_active; ?>">
                            <a class="menu-link" href="/user/examination_area">
                                <span class="icon"></span>
                                <span class="title">Examination Area</span>
                            </a>
                        </li>
                    <?php endif;?>
                </ul>
            </li>


            <li class="menu-item <?php echo $billingTransactions; ?>">
                <a class="menu-link" href="#">
                    <span class="icon ti-ruler-pencil"></span>
                    <span class="title">Billing Transactions</span>
                    <span class="arrow"></span>
                </a>

                <ul class="menu-submenu">
                    <?php if (ACL::canRead('invoicing')) :?>
                    <li class="menu-item <?php echo $invoicing; ?>">
                        <a class="menu-link" href="/user/invoicing">
                            <span class="icon"></span>
                            <span class="title">Invoicing</span>
                        </a>
                    </li>
                    <?php endif;?>
                    <?php if (ACL::canRead('invoice-records')) :?>
                        <li class="menu-item <?php echo $invoiceRecords; ?>">
                        <a class="menu-link" href="/user/invoice">
                            <span class="icon"></span>
                            <span class="title">Invoice Records</span>
                        </a>
                    </li>
                    <?php endif;?>
                    <?php if (ACL::canRead('invoice-approvals')) :?>
                        <li class="menu-item <?php echo $invoiceApprovals; ?>">
                            <a class="menu-link" href="/user/invoice_approvals">
                                <span class="icon"></span>
                                <span class="title">Invoice Approvals</span>
                            </a>
                        </li>
                    <?php endif;?>
                    <?php if (ACL::canRead('invoice-waivers')) :?>
                        <li class="menu-item <?php echo $invoiceWaivers; ?>">
                            <a class="menu-link" href="/user/invoice_waivers">
                                <span class="icon"></span>
                                <span class="title">Invoice Waivers</span>
                            </a>
                        </li>
                    <?php endif;?>
                    <?php if (ACL::canRead('invoice-deferrals')) :?>
                        <li class="menu-item <?php echo $invoiceDeferrals; ?>">
                            <a class="menu-link" href="/user/invoice_deferrals">
                                <span class="icon"></span>
                                <span class="title">Invoice Deferrals</span>
                            </a>
                        </li>
                    <?php endif;?>
                    <?php if (ACL::canRead('invoice-payments')) :?>
                        <li class="menu-item <?php echo $invoicePayment; ?>">
                            <a class="menu-link" href="/user/invoice_payment">
                                <span class="icon"></span>
                                <span class="title">Invoice Payment Records</span>
                            </a>
                        </li>
                    <?php endif;?>
                </ul>
            </li>
            <li class="menu-item <?php echo $suppBillingTransactions; ?>">
                <a class="menu-link" href="#">
                    <span class="icon ti-ruler-pencil"></span>
                    <span class="title">Supplementary Billing Transactions</span>
                    <span class="arrow"></span>
                </a>

                <ul class="menu-submenu">
                    <?php if (ACL::canRead('proforma-supplementary-invoicing')) :?>
                        <li class="menu-item <?php echo $proforma_supp_invoicing; ?>">
                            <a class="menu-link" href="/user/proforma_supplementary_invoicing">
                                <span class="icon"></span>
                                <span class="title">Pro forma Invoicing</span>
                            </a>
                        </li>
                    <?php endif;?>
                    <?php if (ACL::canRead('proforma-supplementary-invoice-records')) :?>
                        <li class="menu-item <?php echo $proforma_invoice_records; ?>">
                            <a class="menu-link" href="/user/proforma_supplementary_invoice">
                                <span class="icon"></span>
                                <span class="title">Pro forma Invoice Records</span>
                            </a>
                        </li>
                    <?php endif;?>
                    <?php if (ACL::canRead('supplementary-invoicing')) :?>
                        <li class="menu-item <?php echo $supp_invoicing; ?>">
                            <a class="menu-link" href="/user/supplementary_invoicing">
                                <span class="icon"></span>
                                <span class="title">Invoicing</span>
                            </a>
                        </li>
                    <?php endif;?>
                    <?php if (ACL::canRead('supplementary-invoice-records')) :?>
                        <li class="menu-item <?php echo $supp_invoice_records; ?>">
                            <a class="menu-link" href="/user/supplementary_invoice">
                                <span class="icon"></span>
                                <span class="title">Invoice Records</span>
                            </a>
                        </li>
                    <?php endif;?>
                    <?php if (ACL::canRead('supplementary-invoice-approvals')) :?>
                        <li class="menu-item <?php echo $supp_invoice_approvals; ?>">
                            <a class="menu-link" href="/user/supplementary_invoice_approvals">
                                <span class="icon"></span>
                                <span class="title">Invoice Approvals</span>
                            </a>
                        </li>
                    <?php endif;?>
                    <?php if (ACL::canRead('supplementary-invoice-waivers')) :?>
                        <li class="menu-item <?php echo $supp_invoice_waiver; ?>">
                            <a class="menu-link" href="/user/supplementary_invoice_waivers">
                                <span class="icon"></span>
                                <span class="title">Invoice Waivers</span>
                            </a>
                        </li>
                    <?php endif;?>
                    <?php if (ACL::canRead('supplementary-invoice-deferrals')) :?>
                        <li class="menu-item <?php echo $supp_invoice_deferrals; ?>">
                            <a class="menu-link" href="/user/supplementary_invoice_deferrals">
                                <span class="icon"></span>
                                <span class="title">Invoice Deferrals</span>
                            </a>
                        </li>
                    <?php endif;?>
                    <?php if (ACL::canRead('supplementary-invoice-payments')) :?>
                        <li class="menu-item <?php echo $supp_invoice_payment; ?>">
                            <a class="menu-link" href="/user/supplementary_invoice_payments">
                                <span class="icon"></span>
                                <span class="title">Invoice Payment Records</span>
                            </a>
                        </li>
                    <?php endif;?>
                </ul>
            </li>

            <li class="menu-item <?php echo $proforma; ?>">
                <a class="menu-link" href="#">
                    <span class="icon ti-ruler-pencil"></span>
                    <span class="title">Proforma</span>
                    <span class="arrow"></span>
                </a>

                <ul class="menu-submenu">
                    <?php if (ACL::canRead('proforma-depot-overview')) :?>
                        <li class="menu-item <?php echo $proforma_depot_overview; ?>">
                            <a class="menu-link" href="/user/proforma_depot_over">
                                <span class="icon"></span>
                                <span class="title">Depot Overview</span>
                            </a>
                        </li>
                    <?php endif;?>
                    <?php if (ACL::canRead('proforma-invoicing')) :?>
                        <li class="menu-item <?php echo $proforma_invoicing; ?>">
                            <a class="menu-link" href="/user/proforma_invoicing">
                                <span class="icon"></span>
                                <span class="title">Invoicing</span>
                            </a>
                        </li>
                    <?php endif;?>
                    <?php if (ACL::canRead('proforma-invoice-records')) :?>
                        <li class="menu-item <?php echo $proforma_invoice_records; ?>">
                            <a class="menu-link" href="/user/proforma_invoice">
                                <span class="icon"></span>
                                <span class="title">Invoice Records</span>
                            </a>
                        </li>
                    <?php endif;?>
                </ul>
            </li>
            <li class="menu-item">
                <a class="menu-link" href="#">
                    <span class="icon ti-layout-grid3-alt"></span>
                    <span class="title">Container Freight Station</span>

                </a>
            </li>



            <li class="menu-item <?php echo $reports; ?>">
                <a class="menu-link" href="#">
                    <span class="icon fa fa-file"></span>
                    <span class="title">Reports</span>
                    <span class="arrow"></span>
                </a>

                <ul class="menu-submenu">
                    <?php if (ACL::canRead('voyage-reports')) :?>
                    <li class="menu-item <?php echo $depotReports; ?>">
                        <a class="menu-link" href="/user/voyage_reports">
                            <span class="icon"></span>
                            <span class="title">Voyage Reports</span>
                        </a>
                    </li>
                    <?php endif;?>
                    <?php if (ACL::canRead('gate-reports')) :?>
                    <li class="menu-item <?php echo $gate_reports; ?>">
                        <a class="menu-link" href="/user/gate_reports">
                            <span class="icon"></span>
                            <span class="title">Gate Reports</span>
                        </a>
                    </li>
                    <?php endif;?>
                    <?php if (ACL::canRead('depot-reports')) :?>
                    <li class="menu-item <?php echo $stockReports; ?>">
                        <a class="menu-link" href="/user/stock_reports">
                            <span class="icon"></span>
                            <span class="title">Stock Reports</span>
                        </a>
                    </li>
                    <?php endif;?>
                    <?php if (ACL::canRead('invoice-reports')) :?>
                        <li class="menu-item <?php echo $invoice_reports; ?>">
                            <a class="menu-link" href="/user/invoice_reports">
                                <span class="icon"></span>
                                <span class="title">Invoice Reports</span>
                            </a>
                        </li>
                    <?php endif;?>
                    <?php if (ACL::canRead('payment-reports')) :?>
                    <li class="menu-item <?php echo $payment_report_active; ?>">
                        <a class="menu-link" href="/user/payment_reports">
                            <span class="icon"></span>
                            <span class="title">Payment Reports</span>
                        </a>
                    </li>
                    <?php endif;?>
                    <?php if (ACL::canRead('summary-remittances')) :?>
                    <li class="menu-item <?php echo $summary_remittances; ?>">
                        <a class="menu-link" href="/user/summary_remittances">
                            <span class="icon"></span>
                            <span class="title">Summary Remittances</span>
                        </a>
                    </li>
                    <?php endif;?>
                </ul>
            </li>

            <li class="menu-category">System</li>

            <li class="menu-item <?php echo $userDataManager; ?>">
                <a class="menu-link" href="#">
                    <span class="icon fa fa-files-o"></span>
                    <span class="title">User Data Manager</span>
                    <span class="arrow"></span>
                </a>

                <ul class="menu-submenu">
                    <?php if (ACL::canRead('udm-banks')) :?>
                        <li class="menu-item <?php echo $banks; ?>">
                            <a class="menu-link" href="/user/banks">
                                <span class="icon"></span>
                                <span class="title">Banks</span>
                            </a>
                        </li>
                    <?php endif;?>
                    <?php if (ACL::canRead('udm-ports')) :?>
                    <li class="menu-item <?php echo $ports; ?>">
                        <a class="menu-link" href="/user/port">
                            <span class="icon"></span>
                            <span class="title">Ports</span>
                        </a>
                    </li>
                    <?php endif;?>
                    <?php if (ACL::canRead('udm-shipping-line')) :?>
                    <li class="menu-item <?php echo $shipping_lines; ?>">
                        <a class="menu-link" href="/user/shipping_line">
                            <span class="icon"></span>
                            <span class="title">Shipping Lines</span>
                        </a>
                    </li>
                    <?php endif;?>
                    <?php if (ACL::canRead('udm-shipping-line-agents')) :?>
                    <li class="menu-item <?php echo $shippingLineAgents; ?>">
                        <a class="menu-link" href="/user/shipping_line_agent">
                            <span class="icon"></span>
                            <span class="title">Shipping Line Agents</span>
                        </a>
                    </li>
                    <?php endif;?>
                    <?php if (ACL::canRead('udm-trucking-companies')) :?>
                    <li class="menu-item <?php echo $truckingCompanies; ?>">
                        <a class="menu-link" href="/user/trucking_company">
                            <span class="icon"></span>
                            <span class="title">Trucking Companies</span>
                        </a>
                    </li>
                    <?php endif;?>
                    <?php if (ACL::canRead('udm-vehicle')) :?>
                    <li class="menu-item <?php echo $vehicleRegistration; ?>">
                        <a class="menu-link" href="/user/vehicle">
                            <span class="icon"></span>
                            <span class="title">Vehicle Registration</span>
                        </a>
                    </li>
                    <?php endif;?>
                    <?php if (ACL::canRead('udm-container-type-codes')) :?>
                    <li class="menu-item <?php echo $container_type_codes; ?>">
                        <a class="menu-link" href="/user/container_type_codes">
                            <span class="icon"></span>
                            <span class="title">Container ISO Type Code</span>
                        </a>
                    </li>
                    <?php endif;?>
                    <li class="menu-item">
                        <a class="menu-link" href="#">
                            <span class="icon"></span>
                            <span class="title">ICD Manager</span>
                        </a>
                    </li>
                    <?php if (ACL::canRead('udm-driver-registration')) :?>
                    <li class="menu-item <?php echo $driverRegistration; ?>">
                        <a class="menu-link" href="/user/vehicle_driver">
                            <span class="icon"></span>
                            <span class="title">Driver Registration</span>
                        </a>
                    </li>
                    <?php endif;?>
                    <?php if (ACL::canRead('udm-ucl-settings')) :?>
                        <li class="menu-item <?php echo $ucl; ?>">
                            <a class="menu-link" href="/user/ucl_settings">
                                <span class="icon"></span>
                                <span class="title">UCL Settings </span>
                            </a>
                        </li>
                    <?php endif;?>
                    <?php if (ACL::canRead('udm-reach-stacker')) :?>
                        <li class="menu-item <?php echo $reach_stacker; ?>">
                            <a class="menu-link" href="/user/reach_stacker">
                                <span class="icon"></span>
                                <span class="title">Reach Stacker </span>
                            </a>
                        </li>
                    <?php endif;?>
                    <?php if (ACL::canRead('udm-stack')) :?>
                        <li class="menu-item <?php echo $stack; ?>">
                            <a class="menu-link" href="/user/stack">
                                <span class="icon"></span>
                                <span class="title">Stack </span>
                            </a>
                        </li>
                    <?php endif;?>
                    <li class="menu-item">
                        <a class="menu-link" href="#">
                            <span class="icon"></span>
                            <span class="title">Yard Setup</span>
                        </a>
                    </li>
                    <?php if (ACL::canRead('udm-countries')) :?>
                    <li class="menu-item <?php echo $countries; ?>">
                        <a class="menu-link" href="/user/countries">
                            <span class="icon"></span>
                            <span class="title">Countries</span>
                        </a>
                    </li>
                    <?php endif;?>
                    <li class="menu-item">
                        <a class="menu-link" href="#">
                            <span class="icon"></span>
                            <span class="title">Yard Gates</span>
                        </a>
                    </li>

                    <li class="menu-item">
                        <a class="menu-link" href="#">
                            <span class="icon"></span>
                            <span class="title">Holidays</span>
                        </a>
                    </li>

                    <li class="menu-item">
                        <a class="menu-link" href="#">
                            <span class="icon"></span>
                            <span class="title">Stack Setup</span>
                        </a>
                    </li>

                    <li class="menu-item">
                        <a class="menu-link" href="#">
                            <span class="icon"></span>
                            <span class="title">Devanning Area Setup</span>
                        </a>
                    </li>
                    <?php if (ACL::canRead('udm-agency')) :?>
                    <li class="menu-item <?php echo $clearingAgents; ?>">
                        <a class="menu-link" href="/user/agency">
                            <span class="icon"></span>
                            <span class="title">Agency</span>
                        </a>
                    </li>
                    <?php endif;?>
                    <?php if (ACL::canRead('udm-customers')) :?>
                    <li class="menu-item <?php echo $customers; ?>">
                        <a class="menu-link" href="/user/customer">
                            <span class="icon"></span>
                            <span class="title">Customers</span>
                        </a>
                    </li>
                    <?php endif;?>
                    <?php if (ACL::canRead('udm-customer_billing_groups')) :?>
                    <li class="menu-item <?php echo $customer_billing?>">
                        <a class="menu-link" href="/user/customer_billing_groups">
                            <span class="icon"></span>
                            <span class="title">Customer Billing Groups</span>
                        </a>
                    </li>
                    <?php endif;?>
                    <?php if (ACL::canRead('udm-depot-activity')) :?>
                        <li class="menu-item <?php echo $depot_activity; ?>">
                            <a class="menu-link" href="/user/depot_activity">
                                <span class="icon"></span>
                                <span class="title">Depot Activity</span>
                            </a>
                        </li>
                    <?php endif;?>
                    <?php if (ACL::canRead('udm-depot-activity-charges')) :?>
                    <li class="menu-item <?php echo $depotActivityCharges; ?>">
                        <a class="menu-link" href="/user/depot_activity_charges">
                            <span class="icon"></span>
                            <span class="title">Depot Activity Charges</span>
                        </a>
                    </li>
                    <?php endif;?>
                    <?php if (ACL::canRead('udm-storage-rent-charges')) :?>
                    <li class="menu-item <?php echo $storageRentCharges; ?>">
                        <a class="menu-link" href="/user/storage_rent_charges">
                            <span class="icon"></span>
                            <span class="title">Storage Rent Charges</span>
                        </a>
                    </li>
                    <?php endif;?>
                    <?php if (ACL::canRead('udm-charges-container-monitoring')) :?>
                        <li class="menu-item <?php echo $chargesContainerMonitoring; ?>">
                            <a class="menu-link" href="/user/charges_container_monitoring">
                                <span class="icon"></span>
                                <span class="title">Monitoring Charges</span>
                            </a>
                        </li>
                    <?php endif;?>
                    <?php if (ACL::canRead('udm-exchange_rate')) :?>
                        <li class="menu-item <?php echo $exchange_rate; ?>">
                            <a class="menu-link" href="/user/exchange_rate">
                                <span class="icon"></span>
                                <span class="title">Exchange Rate</span>
                            </a>
                        </li>
                    <?php endif;?>
                    <?php if (ACL::canRead('udm-taxes')) :?>
                    <li class="menu-item <?php echo $taxes; ?>">
                        <a class="menu-link" href="/user/taxes">
                            <span class="icon"></span>
                            <span class="title">Taxes</span>
                        </a>
                    </li>
                    <?php endif;?>
                </ul>
            </li>


            <li class="menu-item <?php echo $systemSetup; ?>">
                <a class="menu-link" href="#">
                    <span class="icon fa fa-question-circle"></span>
                    <span class="title">System Setup</span>
                    <span class="arrow"></span>
                </a>

                <ul class="menu-submenu">
                    <?php if (ACL::canRead('label-editor')) :?>
                    <li class="menu-item <?php echo $label_editor; ?>">
                        <a class="menu-link" href="/user/label_editor">
                            <span class="icon"></span>
                            <span class="title">Label Editor</span>
                        </a>
                    </li>
                    <?php endif;?>
                    <?php if (ACL::canRead('groups')) :?>
                    <li class="menu-item <?php echo $userGroup; ?>">
                        <a class="menu-link" href="/user/user_group">
                            <span class="icon"></span>
                            <span class="title">User Group Manager</span>
                        </a>
                    </li>
                    <?php endif;?>
                    <?php if (ACL::canRead('group-permissions')) :?>
                    <li class="menu-item <?php echo $accessManager; ?>">
                        <a class="menu-link" href="/user/access_permission">
                            <span class="icon"></span>
                            <span class="title">Access Permission Manager</span>
                        </a>
                    </li>
                    <?php endif;?>
                    <?php if (ACL::canRead('user-account')) :?>
                    <li class="menu-item <?php echo $userAccount; ?>">
                        <a class="menu-link" href="/user/user_account">
                            <span class="icon"></span>
                            <span class="title">User Account Manager</span>
                        </a>
                    </li>
                    <?php endif;?>
                </ul>
            </li>


        </ul>
    </nav>

</aside>
<!-- END Sidebar -->
<!-- test -->