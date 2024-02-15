<?php
// echo __DIR__; exit;
require_once 'config.php';
require_once 'functions.php';

$pid = 1;
if (isset($_POST['pid'])) $pid = $_POST['pid'];

$pid_step = 1;
if (isset($_POST['pid_step'])) $pid_step = $_POST['pid_step'];

$psEHealthID = '';

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
    $psEHealthID = $row['pshealthid'];
} 
$message = '';
if ($pid_step==1) {
    //simulation
    $message .='<div class="pid_request_params"><h3><h1>Guichet</h1><h3>Request</h3><p><b>Params</b>: '
    .' Code Prestataire: <span>'.$row['biller_id'].'</span>'
    .' pshealthid: <span>'.$row['pshealthid'].'</span>'
    .' codeMedical: <span>'.$row['medical_code'].'</span>'
    .' Lieu De Prestation: <span>'.$row['service_place'].'</span>'
    .' Matricule Du Patient: <span>'.$row['patient_id'].'</span>'
    .' Type De Consultation (Code Number): <span>'.$row['act_number'].'</span>'
    .'</p></div>'
    .'<div class="pid_response_params"><h3>Response</h3><p><b>Params</b>: '
    .' Guichet date: <span>'.$row['guichet_date'].'</span>'
    .'</p></div>';

    if ($row['ccss_token_date']=='')
        $message .='<div class="pid_request_params"><h1>CNS Request <span>(Failed)</span></h1><h3>Request</h3><p><b>Params</b>: ';
    else
        $message .='<div class="pid_request_params"><h1>CNS Request</h1><h3>Request</h3><p><b>Params</b>: ';
    $message .=' Code Prestataire: <span>'.$row['biller_id'].'</span>'
    .' pshealthid: <span>'.$row['pshealthid'].'</span>'
    .' codeMedical: <span>'.$row['medical_code'].'</span>'
    .' Lieu De Prestation: <span>'.$row['service_place'].'</span>'
    .' Matricule Du Patient: <span>'.$row['patient_id'].'</span>'
    .' Type De Consultation (Code Number): <span>'.$row['act_number'].'</span>'
    .'</p></div>'
    .'<div class="pid_response_params"><h3>Response</h3><p><b>Params</b>: '
    .' ccss_token_date: <span>'.$row['ccss_token_date'].'</span>'
    .'</p></div>';

    if ($row['sommeTotale']=='')
        $message .='<div class="pid_request_params"><h1>Business Request <span>(Failed)</span></h1><h3>Request</h3><p><b>Params</b>: ';
    else
        $message .='<div class="pid_request_params"><h1>Business Request</h1><h3>Request</h3><p><b>Params</b>: ';
    $message .=' Code Prestataire: <span>'.$row['biller_id'].'</span>'
    .' Pshealthid: <span>'.$row['pshealthid'].'</span>'
    .' Code Medical: <span>'.$row['medical_code'].'</span>'
    .' Lieu De Prestation: <span>'.$row['service_place'].'</span>'
    .' Matricule Du Patient: <span>'.$row['patient_id'].'</span>'
    .' Type De Consultation (Code Number): <span>'.$row['act_number'].'</span>'
    .'</p></div>'
    .'<div class="pid_response_params"><h3>Response</h3><p><b>Params</b>: '
    .' id_response_date: <span>'.$row['id_response_date'].'</span>'
    .' totalPartStatutaire: <span>'.$row['totalPartStatutaire'].'</span>'
    .' totalParticipationPersonelle: <span>'.$row['totalParticipationPersonelle'].'</span>'
    .' sommeTotale: <span>'.$row['sommeTotale'].'</span>'
    .'</p></div>';
}
else if ($pid_step==2) {
    //validation
    if ($row['paye']=='')
        $message .='<div class="pid_request_params"><h1>Validation <span>(Failed)</span></h1><h3>Request</h3><p><b>Params</b>: ';
    else
        $message .='<div class="pid_request_params"><h1>Validation</h1><h3>Request</h3><p><b>Params</b>: ';
    $message .=' Code Prestataire: <span>'.$row['biller_id'].'</span>'
    .' pshealthid: <span>'.$row['pshealthid'].'</span>'
    .' codeMedical: <span>'.$row['medical_code'].'</span>'
    .' Lieu De Prestation: <span>'.$row['service_place'].'</span>'
    .' Matricule Du Patient: <span>'.$row['patient_id'].'</span>'
    .' Type De Consultation (Code Number): <span>'.$row['act_number'].'</span>'
    .'</p></div>'
    .'<div class="pid_response_params"><h3>Response</h3><p>'
    .' motif: <span>'.$row['motif'].'</span><br><b>Params</b>: '
    .' validation_date: <span>'.$row['validation_date'].'</span>'
    .' part_statutaire: <span>'.$row['part_statutaire'].'</span>'
    .' recouvrement: <span>'.$row['recouvrement'].'</span>'
    .' paye: <span>'.$row['paye'].'</span>'
    .'</p></div>';
}
else if ($pid_step==3) {
    //Contraint
    $message .='<div class="pid_request_params"><h1>Validation</h1><h3>Request</h3><p><b>Params</b>: '
    .' Code Prestataire: <span>'.$row['biller_id'].'</span>'
    .' pshealthid: <span>'.$row['pshealthid'].'</span>'
    .' codeMedical: <span>'.$row['medical_code'].'</span>'
    .' Lieu De Prestation: <span>'.$row['service_place'].'</span>'
    .' Matricule Du Patient: <span>'.$row['patient_id'].'</span>'
    .' Type De Consultation (Code Number): <span>'.$row['act_number'].'</span>'
    .'</p></div>'
    .'<div class="pid_response_params"><h3>Response</h3><p><b>Params</b>: '
    .' contestation_id: <span>'.$row['contestation_id'].'</span>'
    .' contestation_date: <span>'.$row['contestation_date'].'</span>'
    .' recouvrement: <span>'.$row['recouvrement'].'</span>'
    .' paye: <span>'.$row['paye'].'</span>'
    .'</p></div>';
}
$res['message'] = $message;
$res['status'] = 1;
echo json_encode($res);

?>