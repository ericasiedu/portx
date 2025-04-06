<?php
namespace Api;

use Lib\MyTransactionQuery,
    Lib\Excel;

session_abort();

class ExcelUpload{

    private $request,$response;

    public function  __construct($request,$response){
        $this->request = $request;
        $this->response = $response;
    }

    public function upload(){
        $voyage_list = $this->request->param('voyage_list') ?? '';

        $inputFileName = $_FILES['file_upload']['tmp_name'];
        $excel_upload = new Excel();

        if (empty($inputFileName)){
            $this->response->header('Location','/user/import');
        }
        else{

            $inputFileType = \PhpOffice\PhpSpreadsheet\IOFactory::identify($inputFileName);
            $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($inputFileType);
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($inputFileName);
            $worksheet = $spreadsheet->getActiveSheet();


            if ($voyage_list == 'container'){

                $column = 'D';
                $highestrow = $worksheet->getHighestRow();
                $cols = array();
                for ($row = 3; $row <= $highestrow; $row++){
                    $cell = $worksheet->getCell($column.$row);
                    array_push($cols,$cell);
                }

                $vessel_name = $cols[3];
                $prev = $cols[8];
                $next = $cols[9];
                $vessel = $cols[3];
                $vessel_code = $cols[4];
                $agency = $cols[0];
                $agenc_code = $cols[1];
                $liner = $cols[2];
                $ref = $cols[5];



                $query = new MyTransactionQuery();

                if ($excel_upload->getAgentId($query,$agenc_code) == ''){
                    $query->query("insert into agency(code,name)values(?,?)");
                    $query->bind = array('ss',&$agenc_code,&$agency);
                    $query->run();
                }


                if ($excel_upload->getVesselId($query,$vessel_name) == ''){
                    $query->query("insert into vessel(code,name,status)values(?,?,?)");
                    $query->bind = array('ssi',&$vessel_code,&$vessel,3);
                    $query->run();
                }


                if ($excel_upload->getShipId($query,$liner) == ''){
                    $query->query("insert into shipping_line(code,status)values(?,1)");
                    $query->bind = array('s',&$liner);
                    $query->run();

                    $query->query("insert into shipping_line_agent(line_id,code,name,status)select id,?,?,1 from shipping_line where  code like ? ");
                    $query->bind = array('sss',&$agenc_code,&$agency,&$liner);
                    $query->run();
                }



                if ($excel_upload->getPortId($query,$next) == ""){
                    $query->query("insert into port(name)values(?)");
                    $query->bind = array('s',&$next);
                    $query->run();
                }
                if ($excel_upload->getPortId($query,$prev) ==""){
                    $query->query("insert into port(name)values(?)");
                    $query->bind = array('s',&$next);
                    $query->run();
                }

                $value = $worksheet->getCell('D9')->getValue();
                $date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp($value);
                $voyage_eta = date('Y-m-d H:i:s',$date);


                $value1 = $worksheet->getCell('D10')->getValue();
                $date1 = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp($value1);
                $voyage_etd = date('Y-m-d H:i:s',$date1);



                $query->query("SELECT id FROM voyage WHERE reference = ?");
                $query->bind = array('s',&$ref);
                $query->run();
                $reference = $query->fetch_assoc();


                if (!$reference['id']){

                    $entry_date = date('Y-m-d h:m:s');

                    $vessel_id = $excel_upload->getVesselId($query,$vessel_name);
                    $shipping_line = $excel_upload->getShipId($query,$liner);
                    $prev_port = $excel_upload->getPortId($query,$prev);
                    $next_port = $excel_upload->getPortId($query,$next);


                    $query->query("INSERT INTO voyage(reference,vessel_id,shipping_line_id,estimated_arrival,estimated_departure,prev_port_id,next_port_id,entry_date)
                              VALUES(?,?,?,?,?,?,?,?)");
                    $query->bind = array('siissiis',&$ref,&$vessel_id,&$shipping_line,&$voyage_eta,&$voyage_etd,&$prev_port,&$next_port,&$entry_date);
                    $query->run();

                    $query->query("SELECT id AS voyage_id FROM voyage ORDER BY id DESC");
                    $query->bind = array('');
                    $query->run();
                    $voyage_id = $query->fetch_assoc();


                    $lastRow = $worksheet->getHighestRow();
                    for ($row = 21; $row <= $lastRow; ++$row){
                        $container_no = $worksheet->getCellByColumnAndRow(3,$row)->getValue();
                        $bl_number = $worksheet->getCellByColumnAndRow(14,$row)->getValue();
                        $seal_number = $worksheet->getCellByColumnAndRow(4,$row)->getValue();
                        $iso_type = $worksheet->getCellByColumnAndRow(5,$row)->getValue();
                        $soc = $worksheet->getCellByColumnAndRow(6,$row)->getValue();
                        $tonnage_metric = $worksheet->getCellByColumnAndRow(10,$row)->getValue();
                        $fpod = $worksheet->getCellByColumnAndRow(17,$row)->getValue();
                        $trade_type = $worksheet->getCellByColumnAndRow(18,$row)->getValue();
                        $goods = $worksheet->getCellByColumnAndRow(22,$row)->getValue();
                        $importer = $worksheet->getCellByColumnAndRow(23,$row)->getValue();
                        $imdg_code = $worksheet->getCellByColumnAndRow(16,$row)->getValue();
                        $oog_status = $worksheet->getCellByColumnAndRow(24,$row)->getValue();
                        $status = $worksheet->getCellByColumnAndRow(7,$row)->getValue();

                        $datas = $worksheet->getCellByColumnAndRow(1,$row)->getValue();
                        $datas2 = $worksheet->getCellByColumnAndRow(3,$row)->getValue();


                        $soc_status = $soc == 'Y' ? 'YES' : 'No';
                        $oog_stat = $oog_status == 'Y' ? 1 : 0;

                        $cat = substr($imdg_code,0,-2);
                        switch ($cat){
                            case 1:
                            case 7:
                                $category =4;
                                break;
                            case 2:
                            case 5:
                                $category = 2;
                                break;
                            case 3:
                            case 4:
                            case 6:
                            case 8:
                            case 9:
                                $category = 3;
                                break;
                            default:
                                $category = 1;
                        }


                        $query->query("SELECT code FROM trade_type WHERE name = ?");
                        $query->bind = array('s',&$trade_type);
                        $query = $query->run();
                        $result3 =$query->fetch_assoc();
                        $trade = $result3['code'];

                        $full = substr($status,0,1);

                        $full_status = $full == 'F' ? 1 : 0;

                        $query->query("SELECT id FROM container_isotype_code WHERE code = ?");
                        $query->bind = array('s',&$iso_type);
                        $query->run();
                        $result1 = $query->fetch_assoc();
                        $iso = $result1['id'];

                        $query->query("SELECT number FROM container WHERE number =?");
                        $query->bind = array('s',&$container_no);
                        $query->run();
                        $container_number = $query->fetch_assoc();

                        if (!empty($datas) && !empty($datas2)){
                            if ($container_no == $container_number['number']){
                                $this->response->header('Location','/user/import');
                            }
                            else{
                                $trade_code = $trade == '' ? 11 : $trade;
                                $agenc_id = $excel_upload->getAgentId($query,$agenc_code);
                                $query->query("INSERT INTO container(number,bl_number,seal_number_1,icl_seal_number_1,iso_type_code,soc_status,voyage,shipping_line_id,agency_id,pol,pod,fpod,tonnage_weight_metric,trade_type_code,content_of_goods,importer_address, imdg_code_id, oog_status,full_status)
	                                      VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
                                $query->bind = array('ssssisiiisssdsssiii',&$container_no,&$bl_number,&$seal_number,&$seal_number,&$iso,&$soc_status,&$voyage_id['voyage_id'],&$shipping_line,&$agenc_id,&$prev_port,&$next_port,&$fpod,&$tonnage_metric,&$trade_code,&$goods,&$importer,&$category,&$oog_stat,&$full_status);
                                $query->run();

                                $user_id = $_SESSION['id'];

                                switch ($cat){
                                    case 1:
                                    case 2:
                                    case 5:
                                    case 7:
                                        $info_category = 'DG I';
                                        break;
                                    case 3:
                                    case 4:
                                    case 6:
                                    case 8:
                                    case 9:
                                        $info_category = 'DG II';
                                        break;
                                    default:
                                        $info_category = 'General Goods';
                                }


                                $container_id = $query->get_last_id();
                                $query->query("insert into container_depot_info(container_id,load_status,goods,user_id)values(?,'FCL',?,?)");
                                $query->bind = array('isi',&$container_id,&$info_category,&$user_id);
                                $query->run();
                                $query->query("insert into proforma_container_depot_info(container_id,load_status,goods,user_id)values(?,'FCL',?,?)");
                                $query->bind = array('isi',&$container_id,&$info_category,&$user_id);
                                $query->run();

                            }
                        }
                    }
                    $query->commit();
                    $this->response->header('Location','/user/import');

                }
                else{
                    $this->response->header('Location','/user/import');
                }



            }

        }
    }


}




?>