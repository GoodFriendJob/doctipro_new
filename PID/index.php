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

$codeMedical = 'C1';
if (isset($_POST['medical_code'])) $codeMedical = $_POST['medical_code'];

$biller_id = '90812100';
if (isset($_POST['biller_id'])) $biller_id = $_POST['biller_id'];

$lieuPrestation = '01';
if (isset($_POST['service_place'])) $lieuPrestation = $_POST['service_place'];

$varMatricule = '1900123456712';
if (isset($_POST['patient_number'])) $varMatricule = $_POST['patient_number'];

$code_prestataire="90813319";
if (isset($_POST['act_code'])) $code_prestataire = $_POST['act_code'];

$NombreActeMedical = '1';
if (isset($_POST['NombreActeMedical'])) $NombreActeMedical = $_POST['NombreActeMedical'];

$return_values = simulation($pshealthid_p12, $doctor_id, $psEHealthID, $code_prestataire, $codeMedical, $lieuPrestation, $varMatricule, $NombreActeMedical, $biller_id);
validation($return_values['identifiantReponseSimulation'],$return_values['CCss'],$return_values['WsuID']);
// contestation($return_values['identifiantReponseSimulation'],$return_values['CCss'],$return_values['WsuID'],$pshealthid,$return_values['varIdMemoireHonoraire']);

// var_dump($a);

?>