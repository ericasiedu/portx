<?php
namespace Api;
use Lib\MyQuery,
    Lib\Respond,
    Lib\System;

class SystemInformation
{
    private $request, $response;

    public function __construct($request, $response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    public function show_system_info()
    {

    

        $qu = new MyQuery();
        $qu->query("select * from system");
        $res = $qu->run();
        $result = $res->fetch_assoc();
        $result_array = array('cnam'=>$result['company_name'],
            'ctin'=>$result['company_tin'],
            'cloc'=>$result['company_location'],
            'cph1'=>$result['company_phone1'],
            'cph2'=>$result['company_phone2'],
            'mail'=>$result['company_email'],
            'cweb'=>$result['company_web'],
            'logo'=>$result['company_logo'],
            'ttyp'=>$result['tax_type'],
            'prfx'=>$result['prefix'],
            'idns'=>$result['idn_separator'],
            'swvn'=>$result['sw_version']);

        echo json_encode($result_array);
    }

    public function update_system(){

        $update_system = new System();
        $update_system->name = $this->request->param('cnam');
        $update_system->name = ucwords(html_entity_decode($update_system->name));
        $update_system->tin = $this->request->param('ctin');
        $update_system->tin = mb_convert_case(html_entity_decode($update_system->tin), MB_CASE_UPPER);
        $update_system->location = $this->request->param('cloc');
        $update_system->phone_1 = $this->request->param('cph1');
        $update_system->phone_2 = $this->request->param('cph2');
        $update_system->mail = $this->request->param('mail');
        $update_system->web = $this->request->param('cweb');
        $update_system->tax = $this->request->param('ttyp');
        $update_system->prefix = $this->request->param('prfx');
        $update_system->idn_seperator = $this->request->param('idns');
        $update_system->sw_version = $this->request->param('swvn');
        $logo_value = $this->request->param('logo');

        $error = array();

        $update_system->sw_date = date('Y-m-d h:m:s');

        if ( $update_system->name == '') {
            $error[] = "Empty field";
        }
        if ($update_system->tin == '') {
            $error[] = "Empty field";
        }
        if ($update_system->location == '') {
            $error[] = "Empty field";
        }
        if ($update_system->phone_1 == '') {
            $error[] = "Empty field";
        }

        if ($update_system->idn_seperator == '') {
            $error[] = "Empty field";
        }
        if ($update_system->idn_seperator != '') {
            if (!preg_match("/[-]/",$update_system->idn_seperator)){
                $error[] = "Invalid character";
            }
        }





        if (empty($error)) {
            if (!empty($_FILES['file'])){

                $company_logo = $_FILES['file']['name'];

                $upload_dir = '/opt/rh/httpd24/root/var/www/html/portx/img/';
                $file_name = basename($_FILES['file']['name']);

                $target_path = $upload_dir;
                $file_size = $_FILES['file']['size'];
                $extension = array("png" => "image/png");
                $ext = pathinfo($file_name, PATHINFO_EXTENSION);
                $update_system->logo = "logo." . $ext;
                $logo_name = $target_path . $update_system->logo;

                if (!array_key_exists($ext, $extension)) {
                    new Respond(120);
                }
                if ($file_size > 256000) {
                    new Respond(121);
                }


                if (move_uploaded_file($_FILES['file']['tmp_name'], $logo_name)) {
                    $update_system->updateSystem();
                    new Respond(215);
                }
            }
            else{
                $update_system->logo = $logo_value;
                $update_system->updateSystem();
                new Respond(215);
            }

        } else {
            return false;
        }

    }

}

?>