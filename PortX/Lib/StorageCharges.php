<?php
namespace Lib;

class StorageCharges{
    public $actual_date;
    public $p_date;
    public $teu;
    public $due_cost;
    public $storage_cost;
    public $trade_type__id;
    public $iso_type;
    public $other_charges;
    public $total_charges;
    public $result2;
    public $total_amount;
    public $default_charges;
    public $main_activity;
    public $main_count;
    public $all_charges;
    public $main_charges;
    public $storage_charges;
    public $activity_array;
    public $first;
    public $due_date;
    public $eta_date;
    public $sup_storage_start_date;
    public $base_currency;
    public $rate;
    public $billing_group;
    public $extra_days;
    public $storage_calculated;
    public $days_charged;
    public $monitoring_days;
    public $total_days;
    public $total_free_days;
    public $standard_free_days;

    public function chargeStorage($container_no, $is_proforma = false){
        $query=new MyTransactionQuery();
        $date = new \DateTime($this->eta_date);
        $strip = $date->format('Y-m-d');

        $proforma_prefix = $is_proforma ? "proforma_" : "";

        $date1 = strtotime($strip);
        $date2 = strtotime($this->p_date);
        $diff = $date2 - $date1;
        $diffs = abs(round($diff / 86400));
        $date3 = $diffs + 1;
        $this->total_days = $date3;

        $this->storage_cost = 0;
        $this->storage_calculated = false;
        $days_billable = array();
        $billable_cost = array();


        $query->query("SELECT container.full_status, ".$proforma_prefix."container_depot_info.goods,container_isotype_code.length, trade_type.name as trade FROM container 
                              INNER JOIN container_isotype_code ON container_isotype_code.id = container.iso_type_code 
                              INNER JOIN ".$proforma_prefix."container_depot_info on ".$proforma_prefix."container_depot_info.container_id = container.id 
                              INNER JOIN trade_type on trade_type.code = container.trade_type_code  WHERE container.id = ?");
        $query->bind = array('i', &$container_no);
        $run = $query->run();
        $container_info = $run->fetch_assoc();
        $full_status = $container_info['full_status'];
        $container_length = $container_info['length'];
        $goods = $container_info['goods'];
        $trade = $container_info['trade'];


        $query->query("SELECT free_days, first_billable_days, first_billable_days_cost, second_billable_days, second_billable_days_cost, allother_billable_days_cost, code
                            FROM charges_storage_rent_teu 
                            INNER join currency on charges_storage_rent_teu.currency = currency.id 
                            WHERE trade_type = ? AND full_status = ?  and currency.code = ? and goods = ?");
        $query->bind = array('iiss', &$this->trade_type__id, &$full_status, &$this->base_currency, &$goods);
        $run = $query->run();
        $charge_res = $run->fetch_assoc();
        if(!$run->num_rows()) {
            $query->query("SELECT free_days, first_billable_days, first_billable_days_cost, second_billable_days, second_billable_days_cost, allother_billable_days_cost, code
                            FROM charges_storage_rent_teu 
                            INNER join currency on charges_storage_rent_teu.currency = currency.id 
                            WHERE trade_type = ? AND full_status = ? and currency.code != ? and goods = ?");
            $query->bind = array('iiss', &$this->trade_type__id, &$full_status, &$this->base_currency, &$goods);
            $run = $query->run();
            $charge_res = $run->fetch_assoc();
        }
        $this->standard_free_days = $charge_res['free_days'];
        $first_billable = $charge_res['first_billable_days'];
        $first_cost = $charge_res['first_billable_days_cost'];
        $second_billable = $charge_res['second_billable_days'];
        $second_cost = $charge_res['second_billable_days_cost'];
        $allother_cost = $charge_res['allother_billable_days_cost'];
        $quote_currency = $charge_res['code'];

// var_dump($quote_currency);die;
        if(!$quote_currency) {
            $query->commit();
            new Respond(1212, array('good' => $goods,"trade" => $trade, "fstat" => $full_status == 1 ? "YES" : "NO" ));
        }

        $this->total_free_days = $this->extra_days;
        $this->days_charged = $this->total_days - ($this->total_free_days+$this->standard_free_days);

        $remaining = $this->total_days - $this->standard_free_days - $first_billable - $second_billable;

        array_push($days_billable,$first_billable,$second_billable, $remaining );
        array_push($billable_cost,$first_cost,$second_cost,$allother_cost);

        if($this->days_charged <= 0) {
            return 0;
        }

        if ($this->total_days > $this->standard_free_days) {
            $this->total_days -= $this->standard_free_days;
            for ($i = 0; $i < count($days_billable); $i++) {
                $days = $days_billable[$i];
                $cost = $billable_cost[$i];

                if ($this->extra_days > 0){
                    if($this->extra_days <= $days) {
                        $days_diff = $days - $this->extra_days;
                        $this->storage_cost += $days_diff * $cost * $container_length / 20;
                    }
                }
                else {
                    if ($this->total_days >= $days) {
                        $this->storage_cost += $days * $cost * $container_length / 20;
                    } else {
                        $days = $this->total_days % $days;
                        $this->storage_cost += $this->total_days * $cost * $container_length / 20;
                    }
                }

                $this->total_days -= $days;
                $this->extra_days -= $days;
                $this->extra_days = $this->extra_days < 0 ? 0: $this->extra_days;

                if ($this->total_days <= 0) {
                    break;
                }
            }

        }
        else {
            return 0;
        }

        $this->extra_days = $this->total_free_days ;



        $this->rate = 0;
        if($this->base_currency != $quote_currency) {
            $query->query("select id, buying, selling from exchange_rate where base in (select id from currency where code =  ? ) and quote in (select id from currency where code =  ?) order by date DESC");
            $query->bind = array('ss', &$this->base_currency, &$quote_currency);
            $run = $query->run();

            $isBase = true;

            if (!$run->num_rows()) {
                $query->query("select id, buying, selling from exchange_rate where quote in (select id from currency where code =  ? ) and base in (select id from currency where code =  ?) order by date DESC");
                $query->bind = array('ss', &$this->base_currency, &$quote_currency);
                $run = $query->run();

                $isBase = false;
                if (!$run->num_rows()) {
                    $query->commit();
                    new Respond(1211, array('base' => $this->base_currency, 'quote' => $quote_currency));
                }
            }
            $rates = $run->fetch_assoc();
            if ($isBase) {
                $rate = $rates['buying'];

                $this->storage_cost = $this->storage_cost / $rate;
            } else {
                $rate = $rates['selling'];

                $this->storage_cost = $this->storage_cost * $rate;
            }

            $this->rate = $rates['id'];
        }
        $query->commit();
        $this->storage_calculated = true;
        return round($this->storage_cost, 2);
    }

    public function chargeEmptyStorage($container_no, $is_proforma = false){
        $query=new MyTransactionQuery();
        $date = new \DateTime($this->eta_date);
        $strip = $date->format('Y-m-d');

        $proforma_prefix = $is_proforma ? "proforma_" : "";

        $date1 = strtotime($strip);
        $date2 = strtotime($this->p_date);
        $diff = $date2 - $date1;
        $diffs = abs(round($diff / 86400));
        $date3 = $diffs + 1;
        $this->total_days = $date3;

        $this->storage_cost = 0;
        $this->storage_calculated = false;
        $days_billable = array();
        $billable_cost = array();


        $query->query("SELECT container.full_status,/*  ".$proforma_prefix."container_depot_info.goods, */container_isotype_code.length, trade_type.name as trade FROM container 
                              INNER JOIN container_isotype_code ON container_isotype_code.id = container.iso_type_code 
                              /* INNER JOIN ".$proforma_prefix."container_depot_info on ".$proforma_prefix."container_depot_info.container_id = container.id */ 
                              INNER JOIN trade_type on trade_type.code = container.trade_type_code  WHERE container.id = ?");
        $query->bind = array('i', &$container_no);
        $run = $query->run();
        $container_info = $run->fetch_assoc();
        $full_status = $container_info['full_status'];
        $container_length = $container_info['length'];
        // $goods = $container_info['goods'];
        $trade = $container_info['trade'];


        $query->query("SELECT free_days, first_billable_days, first_billable_days_cost, second_billable_days, second_billable_days_cost, allother_billable_days_cost, code
                            FROM charges_storage_rent_teu 
                            INNER join currency on charges_storage_rent_teu.currency = currency.id 
                            WHERE trade_type = ? AND full_status = ?  and currency.code = ? /* and goods = ? */");
        $query->bind = array('iiss', &$this->trade_type__id, &$full_status, &$this->base_currency/* , &$goods */);
        $run = $query->run();
        $charge_res = $run->fetch_assoc();
        if(!$run->num_rows()) {
            $query->query("SELECT free_days, first_billable_days, first_billable_days_cost, second_billable_days, second_billable_days_cost, allother_billable_days_cost, code
                            FROM charges_storage_rent_teu 
                            INNER join currency on charges_storage_rent_teu.currency = currency.id 
                            WHERE trade_type = ? AND full_status = ? and currency.code != ?/*  and goods = ? */");
            $query->bind = array('iiss', &$this->trade_type__id, &$full_status, &$this->base_currency/* , &$goods */);
            $run = $query->run();
            $charge_res = $run->fetch_assoc();
        }
        $this->standard_free_days = $charge_res['free_days'];
        $first_billable = $charge_res['first_billable_days'];
        $first_cost = $charge_res['first_billable_days_cost'];
        $second_billable = $charge_res['second_billable_days'];
        $second_cost = $charge_res['second_billable_days_cost'];
        $allother_cost = $charge_res['allother_billable_days_cost'];
        $quote_currency = $charge_res['code'];

        if(!$quote_currency) {
            $query->commit();
            new Respond(1212, array(/* 'good' => $goods, */"trade" => $trade, "fstat" => $full_status == 1 ? "YES" : "NO" ));
        }

        $this->total_free_days = $this->extra_days;
        $this->days_charged = $this->total_days - ($this->total_free_days+$this->standard_free_days);

        $remaining = $this->total_days - $this->standard_free_days - $first_billable - $second_billable;

        array_push($days_billable,$first_billable,$second_billable, $remaining );
        array_push($billable_cost,$first_cost,$second_cost,$allother_cost);

        if($this->days_charged <= 0) {
            return 0;
        }

        if ($this->total_days > $this->standard_free_days) {
            $this->total_days -= $this->standard_free_days;
            for ($i = 0; $i < count($days_billable); $i++) {
                $days = $days_billable[$i];
                $cost = $billable_cost[$i];

                if ($this->extra_days > 0){
                    if($this->extra_days <= $days) {
                        $days_diff = $days - $this->extra_days;
                        $this->storage_cost += $days_diff * $cost * $container_length / 20;
                    }
                }
                else {
                    if ($this->total_days >= $days) {
                        $this->storage_cost += $days * $cost * $container_length / 20;
                    } else {
                        $days = $this->total_days % $days;
                        $this->storage_cost += $this->total_days * $cost * $container_length / 20;
                    }
                }

                $this->total_days -= $days;
                $this->extra_days -= $days;
                $this->extra_days = $this->extra_days < 0 ? 0: $this->extra_days;

                if ($this->total_days <= 0) {
                    break;
                }
            }

        }
        else {
            return 0;
        }

        $this->extra_days = $this->total_free_days ;



        $this->rate = 0;
        if($this->base_currency != $quote_currency) {
            $query->query("select id, buying, selling from exchange_rate where base in (select id from currency where code =  ? ) and quote in (select id from currency where code =  ?) order by date DESC");
            $query->bind = array('ss', &$this->base_currency, &$quote_currency);
            $run = $query->run();

            $isBase = true;

            if (!$run->num_rows()) {
                $query->query("select id, buying, selling from exchange_rate where quote in (select id from currency where code =  ? ) and base in (select id from currency where code =  ?) order by date DESC");
                $query->bind = array('ss', &$this->base_currency, &$quote_currency);
                $run = $query->run();

                $isBase = false;
                if (!$run->num_rows()) {
                    $query->commit();
                    new Respond(1211, array('base' => $this->base_currency, 'quote' => $quote_currency));
                }
            }
            $rates = $run->fetch_assoc();
            if ($isBase) {
                $rate = $rates['buying'];

                $this->storage_cost = $this->storage_cost / $rate;
            } else {
                $rate = $rates['selling'];

                $this->storage_cost = $this->storage_cost * $rate;
            }

            $this->rate = $rates['id'];
        }
        $query->commit();
        $this->storage_calculated = true;
        return round($this->storage_cost, 2);
    }

}

?>