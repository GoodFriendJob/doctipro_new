<?php
// echo __DIR__; exit;
require_once 'config.php';
require_once 'functions.php';

$psEHealthID = '2854201475';
if (isset($_POST['pshealthid'])) $psEHealthID = $_POST['pshealthid'];

$pid = 1;
if (isset($_POST['pid'])) $pid = $_POST['pid'];

$pid_step = 1;
if (isset($_POST['pid_step'])) $pid_step = $_POST['pid_step'];

$res = array(
    'status' => 0, 'message' => 'Error is happened',
    'soap' => array(
        'request' => '',
        'response' => ''
    )
);

$OPC = ConnexionBdd($db_host, $db_name, $db_user, $db_pass);
$stmt = $OPC->prepare(" SELECT * FROM doctor_pid where pid_id=".$pid.";");
try{
    $stmt->execute();
}catch(PDOException $err){
    $res['message'] = 'DB connection Error!';
    echo json_encode($res);
    exit;
}
$row = [];
while($result=$stmt->fetch(PDO::FETCH_ASSOC)){
    //select column by key and use
    $row = $result;
} 
$message = '';
if ($pid_step==1) {
    //simulation
    $req_xml = ''; $res_xml='';
    if (file_exists('logs/'. $psEHealthID . '_' . $pid.'_RequestGuichet.xml')) {
        $req_xml = file_get_contents('logs/'. $psEHealthID . '_' . $pid.'_RequestGuichet.xml');
        $req_xml = beautify_xml($req_xml);
    }
    if (file_exists('logs/'. $psEHealthID . '_' . $pid.'_ResponseGuichet.xml')) {
        $res_xml = file_get_contents('logs/'. $psEHealthID . '_' . $pid.'_ResponseGuichet.xml');
        $res_xml = beautify_xml($res_xml);
    }
    $message .='<div class="pid_request_params"><h1>Guichet</h1><h3>Request</h3><p>Params: '
    .' pshealthid: <span>'.$row['pshealthid'].'</span>'
    .' codeMedical: <span>'.$row['medical_code'].'</span>'
    .' Lieu De Prestation: <span>'.$row['service_place'].'</span>'
    .' Matricule Du Patient: <span>'.$row['patient_number'].'</span>'
    .' Identifiant Facturier: <span>'.$row['biller_id'].'</span>'
    .' Type De Consultation (Code Acte): <span>'.$row['act_code'].'</span>'
    .' Type De Consultation (Code Number): <span>'.$row['act_number'].'</span>'
    .'</p></div>'
    .'<div class="pid_request_xml">'.$req_xml.'</div>'
    .'<div class="pid_response_params"><h3>Response</h3><p>Params: '
    .' Guichet date: <span>'.$row['guichet_date'].'</span>'
    .'</p></div>'
    .'<div class="pid_response_xml">'.$res_xml.'</div>';

    $req_xml = ''; $res_xml='';
    if (file_exists('logs/'. $psEHealthID . '_' . $pid.'_RequestCNS.xml')) {
        $req_xml = file_get_contents('logs/'. $psEHealthID . '_' . $pid.'_RequestCNS.xml');
        $req_xml = beautify_xml($req_xml);
    }
    if (file_exists('logs/'. $psEHealthID . '_' . $pid.'_ResponseCNS.xml')) {
        $res_xml = file_get_contents('logs/'. $psEHealthID . '_' . $pid.'_ResponseCNS.xml');
        $res_xml = beautify_xml($res_xml);
    }
    $message .='<div class="pid_request_params"><h1>CNS Request</h1><h3>Request</h3><p>Params: '
    .' pshealthid: <span>'.$row['pshealthid'].'</span>'
    .' codeMedical: <span>'.$row['medical_code'].'</span>'
    .' Lieu De Prestation: <span>'.$row['service_place'].'</span>'
    .' Matricule Du Patient: <span>'.$row['patient_number'].'</span>'
    .' Identifiant Facturier: <span>'.$row['biller_id'].'</span>'
    .' Type De Consultation (Code Acte): <span>'.$row['act_code'].'</span>'
    .' Type De Consultation (Code Number): <span>'.$row['act_number'].'</span>'
    .'</p></div>'
    .'<div class="pid_request_xml">'.$req_xml.'</div>'
    .'<div class="pid_response_params"><h3>Response</h3><p>Params: '
    .' ccss_token_date: <span>'.$row['ccss_token_date'].'</span>'
    .'</p></div>'
    .'<div class="pid_response_xml">'.$res_xml.'</div>';


    $req_xml = ''; $res_xml='';
    if (file_exists('logs/'. $psEHealthID . '_' . $pid.'_RequestBusiness.xml')) {
        $req_xml = file_get_contents('logs/'. $psEHealthID . '_' . $pid.'_RequestBusiness.xml');
        $req_xml = beautify_xml($req_xml);
    }
    if (file_exists('logs/'. $psEHealthID . '_' . $pid.'_ResponseBusiness.xml')) {
        $res_xml = file_get_contents('logs/'. $psEHealthID . '_' . $pid.'_ResponseBusiness.xml');
        $res_xml = beautify_xml($res_xml);
    }
    $message .='<div class="pid_request_params"><h1>Business Request</h1><h3>Request</h3><p>Params: '
    .' pshealthid: <span>'.$row['pshealthid'].'</span>'
    .' codeMedical: <span>'.$row['medical_code'].'</span>'
    .' Lieu De Prestation: <span>'.$row['service_place'].'</span>'
    .' Matricule Du Patient: <span>'.$row['patient_number'].'</span>'
    .' Identifiant Facturier: <span>'.$row['biller_id'].'</span>'
    .' Type De Consultation (Code Acte): <span>'.$row['act_code'].'</span>'
    .' Type De Consultation (Code Number): <span>'.$row['act_number'].'</span>'
    .'</p></div>'
    .'<div class="pid_request_xml">'.$req_xml.'</div>'
    .'<div class="pid_response_params"><h3>Response</h3><p>Params: '
    .' id_response_date: <span>'.$row['id_response_date'].'</span>'
    .' id_memoire_honoraire: <span>'.$row['id_memoire_honoraire'].'</span>'
    .' id_response_simulation: <span>'.$row['id_response_simulation'].'</span>'
    .' id_externe_prestation: <span>'.$row['id_externe_prestation'].'</span>'
    .' wsu_id: <span>'.$row['wsu_id'].'</span>'
    .'</p></div>'
    .'<div class="pid_response_xml">'.$res_xml.'</div>';
}
else if ($pid_step==2) {
    //validation
    $req_xml = ''; $res_xml='';
    if (file_exists('logs/'. $psEHealthID . '_' . $pid.'_RequestBusinessValidate.xml')) {
        $req_xml = file_get_contents('logs/'. $psEHealthID . '_' . $pid.'_RequestBusinessValidate.xml');
        $req_xml = beautify_xml($req_xml);
    }
    if (file_exists('logs/'. $psEHealthID . '_' . $pid.'_ResponseBusinessValidate.xml')) {
        $res_xml = file_get_contents('logs/'. $psEHealthID . '_' . $pid.'_ResponseBusinessValidate.xml');
        $res_xml = beautify_xml($res_xml);
    }
    $message .='<div class="pid_request_params"><h1>Validation</h1><h3>Request</h3><p>Params: '
    .' pshealthid: <span>'.$row['pshealthid'].'</span>'
    .' id_response_simulation: <span>'.$row['id_response_simulation'].'</span>'
    .' WsuID: <span>'.$row['wsu_id'].'</span>'
    .'</p></div>'
    .'<div class="pid_request_xml">'.$req_xml.'</div>'
    .'<div class="pid_response_params"><h3>Response</h3><p>Params: '
    .' validation_date: <span>'.$row['validation_date'].'</span>'
    .' part_statutaire: <span>'.$row['part_statutaire'].'</span>'
    .' recouvrement: <span>'.$row['recouvrement'].'</span>'
    .' paye: <span>'.$row['paye'].'</span>'
    .'</p></div>'
    .'<div class="pid_response_xml">'.$res_xml.'</div>';
}
else if ($pid_step==3) {
    //Contraint
    $req_xml = ''; $res_xml='';
    if (file_exists('logs/'. $psEHealthID . '_' . $pid.'_RequestBusinessContestation.xml')) {
        $req_xml = file_get_contents('logs/'. $psEHealthID . '_' . $pid.'_RequestBusinessContestation.xml');
        $req_xml = beautify_xml($req_xml);
    }
    if (file_exists('logs/'. $psEHealthID . '_' . $pid.'_ResponseBusinessContestation.xml')) {
        $res_xml = file_get_contents('logs/'. $psEHealthID . '_' . $pid.'_ResponseBusinessContestation.xml');
        $res_xml = beautify_xml($res_xml);
    }
    $message .='<div class="pid_request_params"><h1>Validation</h1><h3>Request</h3><p>Params: '
    .' pshealthid: <span>'.$row['pshealthid'].'</span>'
    .' id_response_simulation: <span>'.$row['id_response_simulation'].'</span>'
    .' WsuID: <span>'.$row['wsu_id'].'</span>'
    .'</p></div>'
    .'<div class="pid_request_xml">'.$req_xml.'</div>'
    .'<div class="pid_response_params"><h3>Response</h3><p>Params: '
    .' validation_date: <span>'.$row['validation_date'].'</span>'
    .' part_statutaire: <span>'.$row['part_statutaire'].'</span>'
    .' recouvrement: <span>'.$row['recouvrement'].'</span>'
    .' paye: <span>'.$row['paye'].'</span>'
    .'</p></div>'
    .'<div class="pid_response_xml">'.$res_xml.'</div>';
}
$res['message'] = $message;
$res['status'] = 1;
echo json_encode($res);

function beautify_xml($xml) {
    // $xml = str_replace('>', '><br>', $xml);
    // $xml = str_replace('<br><br>', '<br>', $xml);
    // $xml = preg_replace('/\<(\S+) /i', '<<strong>${1}</strong> ', $xml);
    $xml = str_replace('<', '&lt;', $xml);
    $xml = str_replace('>', '&gt;<br>', $xml);
    return $xml;
}
?>