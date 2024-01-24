<?php

function generateTimestamps()
{
    date_default_timezone_set('Europe/Luxembourg');

    $currentDateTime = new DateTime();
    $currentDateTime->sub(new DateInterval('PT1H'));
    $created = $currentDateTime->format('Y-m-d\TH:i:s.v\Z');
    $expires = new DateTime($created);
    $expires->add(new DateInterval('PT10M'));
    $expiresFormatted = $expires->format('Y-m-d\TH:i:s.v\Z');

    return [$created, $expiresFormatted];
}
function getCurrentDateTimeInISO8601() {
    $currentDateTime = new DateTime('now', new DateTimeZone('Europe/London'));
    $formattedDateTime = $currentDateTime->format('Y-m-d\TH:i:s');
    return $formattedDateTime;
}
function getCurrentDateTimeInISO8601Z() {
    $currentDateTime = new DateTime('now', new DateTimeZone('Europe/London'));
    $formattedDateTime = $currentDateTime->format('Y-m-d\TH:i:s\Z');
    return $formattedDateTime;
}
function ConnexionBdd($var1,$var2,$var3,$var4)
{
	$PARAM_hote=$var1;
	$PARAM_nom_bd=$var2;
	$PARAM_utilisateur=$var3;
	$PARAM_mot_passe=$var4;

	$OPC = new PDO('mysql:host='.$PARAM_hote.';dbname='.$PARAM_nom_bd, $PARAM_utilisateur, $PARAM_mot_passe);

	return $OPC;
}
function getCertificatGuichet($pshealthid_p12, $p12_password)
{
	$path_certificat_p12 = $pshealthid_p12;
	if (!$cert_store = file_get_contents($path_certificat_p12)) {
		echo "Error: Unable to read the cert file\n";
		exit;
	}

	if (openssl_pkcs12_read($cert_store, $cert_info, $p12_password)) {
		$privateKey = $cert_info['pkey']; // Clé privée
		$publicCert = $cert_info['cert']; // Certificat public
		$publicCertWithoutTitle = str_replace("-----BEGIN CERTIFICATE-----",'',$publicCert);
		$publicCertWithoutTitle = str_replace("-----END CERTIFICATE-----",'',$publicCertWithoutTitle);
		$publicCertWithoutTitle = trim($publicCertWithoutTitle);
	} else {
		echo "Error: Unable to read the cert store.\n";
		exit;
	}
	
	 $tableau = array(
        "publicCertWithoutTitle" => $publicCertWithoutTitle,
        "privateKey" => $privateKey
    );
	
	return $tableau;
}

?>