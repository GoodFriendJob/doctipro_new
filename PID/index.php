<?php
// echo __DIR__; exit;
require_once 'config.php';
require_once 'simulation.php'; 
require_once 'validation.php'; 
require_once 'contestation.php'; 

$doctor_id = 1;
if (isset($_POST['doctor_id'])) $doctor_id = $_POST['doctor_id'];

$psEHealthID = '2854201475';
if (isset($_POST['psEHealthID'])) $psEHealthID = $_POST['psEHealthID'];

$pshealthid_p12 = $p12_path . '/' . 'MIPIT.p12';
if (isset($_POST['pshealthid_p12'])) $pshealthid_p12 = $p12_path . '/' . $_POST['pshealthid_p12'];

$medical_code = 'C1';
if (isset($_POST['medical_code'])) $medical_code = $_POST['medical_code'];

$biller_id = '90812100';
if (isset($_POST['biller_id'])) $biller_id = $_POST['biller_id'];

$service_place = '01';
if (isset($_POST['service_place'])) $service_place = $_POST['service_place'];

$patient_number = '1900123456712';
if (isset($_POST['patient_number'])) $patient_number = $_POST['patient_number'];

$act_code="90813319";
if (isset($_POST['act_code'])) $act_code = $_POST['act_code'];

$act_number = '1';
if (isset($_POST['act_number'])) $act_number = $_POST['act_number'];

$p12_password = '7v4DfRK,G0Y0=?Cc';
if (isset($_POST['pshealthid_p12_pass'])) $p12_password = $_POST['pshealthid_p12_pass'];

// Set the content type to application/json
header('Content-Type: application/json');

$return_values = simulation($pshealthid_p12, $p12_password, $doctor_id, $psEHealthID, $act_code, $medical_code, $service_place, $patient_number, $act_number, $biller_id);
// validation($pshealthid_p12, $p12_password, $doctor_id, $psEHealthID, $return_values['identifiantReponseSimulation'], $return_values['CCss'], $return_values['WsuID']);
// contestation($pshealthid_p12, $p12_password, $doctor_id, $psEHealthID, $return_values['identifiantReponseSimulation'], $return_values['CCss'], $return_values['WsuID'], $return_values['varIdMemoireHonoraire']);

// var_dump($a);

?>