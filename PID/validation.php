<?php
function validation($pshealthid_p12, $p12_password, $doctor_id, $pshealthid, $id_response_simulation, $CCss, $WsuID)
{
	global $db_host, $db_name, $db_user, $db_pass, $cert_path;

	$OPC = ConnexionBdd($db_host, $db_name, $db_user, $db_pass);

	$info = getCertificatGuichet($pshealthid_p12, $p12_password);
	$privateKey = $info['privateKey'];
	$publicCertWithoutTitle = $info['publicCertWithoutTitle'];

	// $wsuBodyId ='id-8A64C6552EAFBF716616951123186195';
	// $sampleID = 'saml-dea5cdaee319ff3662a81ae1fea6936f';

	list($created, $expires) = generateTimestamps();
	$dateIssueInstant = getCurrentDateTimeInISO8601();


	$doc = new DomDocument('1.0', 'UTF-8');

	// Création de l'élément racine <soapenv:Envelope>
	$envelope = $doc->createElement('soapenv:Envelope');
	$envelope->setAttribute('xmlns:soapenv', 'http://schemas.xmlsoap.org/soap/envelope/');
	$envelope->setAttribute('xmlns:sync', 'http://ws.mysecu.lu/generic/sync');
	$doc->appendChild($envelope);

	// Création de l'élément <soapenv:Header>
	$header = $doc->createElement('soapenv:Header');
	$header->setAttribute('xmlns:wsse', 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd');
	$header->setAttribute('xmlns:wsu', 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd');
	$envelope->appendChild($header);

	// Création de l'élément <wsse:Security>
	$security = $doc->createElement('wsse:Security');

	// Création de l'élément <wsse:BinarySecurityToken>
	$binarySecurityToken1 = $doc->createElement('wsse:BinarySecurityToken', $CCss);
	$binarySecurityToken1->setAttribute('EncodingType', 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-soap-message-security-1.0#Base64Binary');
	$binarySecurityToken1->setAttribute('ValueType', 'http://ws.mySecu.lu/trust/token/bst');
	$binarySecurityToken1->setAttribute('wsu:Id', $WsuID);
	$binarySecurityToken1->setAttribute('xmlns:soap', 'http://schemas.xmlsoap.org/soap/envelope/');
	$binarySecurityToken1->setAttribute('xmlns:wst', 'http://docs.oasis-open.org/ws-sx/ws-trust/200512');
	$security->appendChild($binarySecurityToken1);

	// Création de l'élément <wsse:BinarySecurityToken>
	$binarySecurityToken2 = $doc->createElement('wsse:BinarySecurityToken', $publicCertWithoutTitle);
	$binarySecurityToken2->setAttribute('EncodingType', 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-soap-message-security-1.0#Base64Binary');
	$binarySecurityToken2->setAttribute('ValueType', 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-x509-token-profile-1.0#X509v3');
	$binarySecurityToken2->setAttribute('wsu:Id', 'X509-8A64C6552EAFBF7166169511421308223');
	$security->appendChild($binarySecurityToken2);

	// Création de l'élément <ds:Signature>
	$signature = $doc->createElement('ds:Signature');
	$signature->setAttribute('Id', 'SIG-8A64C6552EAFBF7166169511421308327');
	$signature->setAttribute('xmlns:ds', 'http://www.w3.org/2000/09/xmldsig#');

	// Création de l'élément <ds:SignedInfo> et ses sous-éléments
	$signedInfo = $doc->createElement('ds:SignedInfo');
	// Création de l'élément <ds:Signature> avec son attribut xmlns:ds
	$canonicalizationMethod = $doc->createElement('ds:CanonicalizationMethod');
	$canonicalizationMethod->setAttribute('Algorithm', 'http://www.w3.org/2001/10/xml-exc-c14n#');
	$inclusiveNamespaces = $doc->createElement('ec:InclusiveNamespaces');
	$inclusiveNamespaces->setAttribute('PrefixList', 'soapenv sync');
	$inclusiveNamespaces->setAttribute('xmlns:ec', 'http://www.w3.org/2001/10/xml-exc-c14n#');
	$canonicalizationMethod->appendChild($inclusiveNamespaces);

	$signatureMethod = $doc->createElement('ds:SignatureMethod');
	$signatureMethod->setAttribute('Algorithm', 'http://www.w3.org/2001/04/xmldsig-more#rsa-sha256');

	$reference = $doc->createElement('ds:Reference');
	$reference->setAttribute('URI', '#id-36854EF7B992756406157054655293757');

	$transforms = $doc->createElement('ds:Transforms');
	$transform = $doc->createElement('ds:Transform');
	$transform->setAttribute('Algorithm', 'http://www.w3.org/2001/10/xml-exc-c14n#');
	$inclusiveNamespaces = $doc->createElement('ec:InclusiveNamespaces');
	$inclusiveNamespaces->setAttribute('PrefixList', 'sync');
	$inclusiveNamespaces->setAttribute('xmlns:ec', 'http://www.w3.org/2001/10/xml-exc-c14n#');
	$transform->appendChild($inclusiveNamespaces);
	$transforms->appendChild($transform);

	$reference->appendChild($transforms);
	$digestMethod = $doc->createElement('ds:DigestMethod');
	$digestMethod->setAttribute('Algorithm', 'http://www.w3.org/2001/04/xmlenc#sha256');
	$digestValue = $doc->createElement('ds:DigestValue', 'tempBodyDigest');
	$reference->appendChild($digestMethod);
	$reference->appendChild($digestValue);
	$signedInfo->appendChild($canonicalizationMethod);
	$signedInfo->appendChild($signatureMethod);
	$signedInfo->appendChild($reference);

	$signatureValue = $doc->createElement('ds:SignatureValue', 'tempSignature');
	$keyInfo = $doc->createElement('ds:KeyInfo');
	$keyInfo->setAttribute('Id', 'KI-8A64C6552EAFBF7166169511421308224');
	$securityTokenReference = $doc->createElement('wsse:SecurityTokenReference');
	$securityTokenReference->setAttribute('wsu:Id', 'STR-8A64C6552EAFBF7166169511421308225');
	$reference = $doc->createElement('wsse:Reference');
	$reference->setAttribute('URI', '#X509-8A64C6552EAFBF7166169511421308223');
	$reference->setAttribute('ValueType', 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-x509-token-profile-1.0#X509v3');
	$securityTokenReference->appendChild($reference);
	$keyInfo->appendChild($securityTokenReference);
	$signature->appendChild($signedInfo);
	$signature->appendChild($signatureValue);
	$signature->appendChild($keyInfo);

	$security->appendChild($signature);
	$header->appendChild($security);

	// Création de l'élément <soapenv:Body>
	$body = $doc->createElement('soapenv:Body');
	$body->setAttribute('wsu:Id', 'id-36854EF7B992756406157054655293757');
	$body->setAttribute('xmlns:wsu', 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd');

	$envelope->appendChild($body);

	// Création de l'élément <sync:RequestInfo>
	$requestInfo = $doc->createElement('sync:RequestInfo');
	$requestInfo->setAttribute('modelUID', '2023-CNS-PID-VAL-V1');
	$body->appendChild($requestInfo);

	$paiementMedecin = $doc->createElement('cns:paiementMedecin');
	$paiementMedecin->setAttribute('xmlns:cns', 'http://www.secu.lu/ciss/cns');
	$paiementMedecin->setAttribute('xmlns:com', 'http://www.secu.lu/ciss/cns/commonTypes');
	$doc->appendChild($paiementMedecin);

	// Création des éléments enfants avec leurs valeurs
	$idReponseSimulation = $doc->createElement('cns:idReponseSimulation');
	$idReponseSimulation->nodeValue = $id_response_simulation;
	$paiementMedecin->appendChild($idReponseSimulation);

	$indicateurValidationMedecin = $doc->createElement('cns:indicateurValidationMedecin', 'true');
	$paiementMedecin->appendChild($indicateurValidationMedecin);

	$indicateurValidationPersonneProtegee = $doc->createElement('cns:indicateurValidationPersonneProtegee', 'true');
	$paiementMedecin->appendChild($indicateurValidationPersonneProtegee);

	$indicateurAcquittementParticipationPersonnelle = $doc->createElement('cns:indicateurAcquittementParticipationPersonnelle', 'true');
	$paiementMedecin->appendChild($indicateurAcquittementParticipationPersonnelle);

	$requestInfo->appendChild($paiementMedecin);

	$bodyNodeCanonized = CanoniseBodyValidation($id_response_simulation);
	$digestBody = openssl_digest($bodyNodeCanonized, 'sha256', true);//ok


	$digestValue = $doc->getElementsByTagName('ds:DigestValue')->item(0);
	$digestValue->nodeValue = base64_encode($digestBody);

	$canonizedSignature = CanoniseSignedInfoValidation($digestBody);
	openssl_sign($canonizedSignature, $signature1, $privateKey, OPENSSL_ALGO_SHA256 );

	$SignatureValue = $doc->getElementsByTagName('ds:SignatureValue')->item(0);
	$SignatureValue->nodeValue = base64_encode($signature1);
	$doc->formatOutput = true;
	$a = $doc->saveXML();


	file_put_contents('logs/RequestBusinessValidate.xml',$a);

	$ch = curl_init();
	$soapActionHeaderValue = 'exchange';
	$service_url = 'https://ws.mysecu.lu:7443/ws/soap/espinst/syncexchange';
	// Configurez les options cURL
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, TRUE);
	// curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, TRUE);
	curl_setopt($ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_3);
	curl_setopt($ch, CURLOPT_URL, $service_url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSLCERT, 'certificat.pem');//ok
	curl_setopt($ch, CURLOPT_CAINFO,  'certificats.pem');
	curl_setopt($ch, CURLOPT_SSLKEYTYPE, 'pem');
	curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
	curl_setopt($ch, CURLOPT_VERBOSE, true);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Content-Type: text/xml;charset=UTF-8'
		)
	);

	curl_setopt($ch, CURLOPT_POSTFIELDS, $a);
	$response = curl_exec($ch);

	if (curl_errno($ch))
	{// Une erreur cURL s'est produite. Vous pouvez récupérer les informations sur l'erreur.
		echo '======= Erreur cURL : ' . curl_error($ch);
	} else {
		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

		if ($httpCode == 500) {
			echo '============= Erreur 500 : ' . $response;
		} else {
			echo '=============  Réponse du serveur : ' . $response;
		}
	}
	file_put_contents('logs/responseCNSBusinessCallValidate.xml', $response);

	$dom = new DOMDocument();
	$dom->loadXML($response);
	$xpath = new DOMXPath($dom);
	$xpath->registerNamespace('cns', 'http://www.secu.lu/ciss/cns');

	// Récupération des éléments contenant les valeurs spécifiques
	$montantPartStatutaire = $xpath->query('//cns:montantPartStatutaire');
	$montantRecouvrement = $xpath->query('//cns:montantRecouvrement');
	$montantPaye = $xpath->query('//cns:montantPaye');
	
	echo '<br>';
	echo '<br>';
	echo '<br>';
	echo $id_response_simulation;
		 
	$req2 = $OPC->prepare(" UPDATE doctor_pid 
		SET validation_xml = :validation_xml,
			validation_response_xml =:validation_response_xml, 
			part_statutaire=:part_statutaire, 
			recouvrement=:recouvrement, 
			paye=:paye,
			validation_date = NOW() 
		WHERE pshealthid = :pshealthid AND id_response_simulation = :id_response_simulation
	");

	$req2->execute([
		'validation_xml' => $a,
		'validation_response_xml' => $response,
		'pshealthid' => $pshealthid,
		'recouvrement' => $montantRecouvrement[0]->nodeValue,
		'part_statutaire' => $montantPartStatutaire[0]->nodeValue,
		'paye' => $montantPaye[0]->nodeValue,
		'id_response_simulation' => $id_response_simulation
	]);
	
	$queryString = $req2->queryString;
	
}
function CanoniseBodyValidation($id)
{  
  $CanonizedBody = '<soapenv:Body xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:sync="http://ws.mysecu.lu/generic/sync" xmlns:wsu="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd" wsu:Id="id-36854EF7B992756406157054655293757">
    <sync:RequestInfo modelUID="2023-CNS-PID-VAL-V1">
      <cns:paiementMedecin xmlns:cns="http://www.secu.lu/ciss/cns">
        <cns:idReponseSimulation>'.$id.'</cns:idReponseSimulation>
        <cns:indicateurValidationMedecin>true</cns:indicateurValidationMedecin>
        <cns:indicateurValidationPersonneProtegee>true</cns:indicateurValidationPersonneProtegee>
        <cns:indicateurAcquittementParticipationPersonnelle>true</cns:indicateurAcquittementParticipationPersonnelle>
      </cns:paiementMedecin>
    </sync:RequestInfo>
  </soapenv:Body>';
 
     
     file_put_contents("logs/BodyCNSBusinessValidate.xml", $CanonizedBody);
	
	 return $CanonizedBody;
}
function CanoniseSignedInfoValidation($digestBody)
{
	$signedinfo = '<ds:SignedInfo xmlns:ds="http://www.w3.org/2000/09/xmldsig#" xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:sync="http://ws.mysecu.lu/generic/sync">
          <ds:CanonicalizationMethod Algorithm="http://www.w3.org/2001/10/xml-exc-c14n#">
            <ec:InclusiveNamespaces xmlns:ec="http://www.w3.org/2001/10/xml-exc-c14n#" PrefixList="soapenv sync"></ec:InclusiveNamespaces>
          </ds:CanonicalizationMethod>
          <ds:SignatureMethod Algorithm="http://www.w3.org/2001/04/xmldsig-more#rsa-sha256"></ds:SignatureMethod>
          <ds:Reference URI="#id-36854EF7B992756406157054655293757">
            <ds:Transforms>
              <ds:Transform Algorithm="http://www.w3.org/2001/10/xml-exc-c14n#">
                <ec:InclusiveNamespaces xmlns:ec="http://www.w3.org/2001/10/xml-exc-c14n#" PrefixList="sync"></ec:InclusiveNamespaces>
              </ds:Transform>
            </ds:Transforms>
            <ds:DigestMethod Algorithm="http://www.w3.org/2001/04/xmlenc#sha256"></ds:DigestMethod>
            <ds:DigestValue>'.base64_encode($digestBody).'</ds:DigestValue>
          </ds:Reference>
        </ds:SignedInfo>';
				
	  file_put_contents("logs/SignedInfoBusinessCallValidate.xml", $signedinfo);
	  
	  return $signedinfo;
}


