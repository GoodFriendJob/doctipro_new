<?php

require_once 'functions.php';

function simulation($pshealthid_p12, $p12_password, $doctor_id, $psEHealthID, $code_prestataire, $codeMedical, $lieuPrestation,$varMatricule,$NombreActeMedical, $biller_id)
{
  $lastInsertId = 0;
  $res = array(
    'status' => 0, 'message' => 'Error is happened',
    'soap' => array(
        'request' => [],
        'response' => []
    )
  );

	global $db_host, $db_name, $db_user, $db_pass;
  $OPC = ConnexionBdd($db_host, $db_name, $db_user, $db_pass);

  $info = getCertificatGuichet($pshealthid_p12, $p12_password);
  $privateKey = $info['privateKey'];
  $publicCertWithoutTitle = $info['publicCertWithoutTitle'];

  $ch = curl_init();
  $service_url = 'https://www-integration.esante.lu/SAML2Server/AuthenticationService';

  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
  curl_setopt($ch, CURLOPT_URL, $service_url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_SSLCERT, 'certificat.pem');
  // curl_setopt($ch, CURLOPT_SSLKEYPASSWD, $p12_password);
  curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_2TLS);
  // curl_setopt($ch, CURLOPT_VERBOSE, true);
  curl_setopt($ch, CURLOPT_POST, true);
  curl_setopt($ch, CURLOPT_HTTPHEADER, array(
          'Content-Type: text/xml;charset=UTF-8',
          'SOAPAction: "http://www.oasis-open.org/committees/securityhttp://www.oasis-open.org/committees/security"',
      )
  );

$wsuTimestampId = 'TS-8A64C6552EAFBF716616951123185611';
$wsuBinarySecurityTokenId = 'X509-8A64C6552EAFBF716616951123185992';
$wsuBodyId = 'id-8A64C6552EAFBF716616951123186195';
// $sampleID = 'saml-dea5cdaee319ff3662a81ae1fea6936f';

list($created, $expires) = generateTimestamps();
$dateIssueInstant = getCurrentDateTimeInISO8601();

//Soap BodyContent
$doc = new DOMDocument('1.0', 'UTF-8');

// Enveloppe SOAP
$envelope = $doc->createElement('soapenv:Envelope');
$envelope->setAttribute('xmlns:soapenv', 'http://schemas.xmlsoap.org/soap/envelope/');
$envelope->setAttribute('xmlns:saml2', 'urn:oasis:names:tc:SAML:2.0:assertion');
$doc->appendChild($envelope);

// En-tête SOAP
$header = $doc->createElement('soapenv:Header');
$header->setAttribute('xmlns:soapenv', 'http://schemas.xmlsoap.org/soap/envelope/');
$envelope->appendChild($header);

// Security Header
$security = $doc->createElement('wsse:Security');
$security->setAttribute('xmlns:wsse', 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd');
$security->setAttribute('xmlns:wsu',  'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd');

$header->appendChild($security);

// Binary Security Token
$binarySecurityToken = $doc->createElement('wsse:BinarySecurityToken', $publicCertWithoutTitle);
$binarySecurityToken->setAttribute('EncodingType', 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-soap-message-security-1.0#Base64Binary');
$binarySecurityToken->setAttribute('ValueType', 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-x509-tokenprofile-1.0#X509v3');
$binarySecurityToken->setAttribute('wsu:Id', $wsuBinarySecurityTokenId);
$security->appendChild($binarySecurityToken);

// Signature
$signature = $doc->createElement('ds:Signature');
$signature->setAttribute('Id', 'SIG-8A64C6552EAFBF716616951123186246');
$signature->setAttribute('xmlns:ds', 'http://www.w3.org/2000/09/xmldsig#');
$security->appendChild($signature);

// SignedInfo
$signedInfo = $doc->createElement('ds:SignedInfo');
$signature->appendChild($signedInfo);

// Canonicalization Method
$canonicalizationMethod = $doc->createElement('ds:CanonicalizationMethod');
$canonicalizationMethod->setAttribute('Algorithm', 'http://www.w3.org/2001/10/xml-exc-c14n#');
$signedInfo->appendChild($canonicalizationMethod);

$inclusiveNamespaces = $doc->createElement('ec:InclusiveNamespaces');
$inclusiveNamespaces->setAttribute('PrefixList', 'saml2 soapenv');
$inclusiveNamespaces->setAttribute('xmlns:ec', 'http://www.w3.org/2001/10/xml-exc-c14n#');

// Attachez l'élément InclusiveNamespaces à CanonicalizationMethod
$canonicalizationMethod->appendChild($inclusiveNamespaces);

// Attachez l'élément CanonicalizationMethod à SignedInfo
$signedInfo->appendChild($canonicalizationMethod);

// Signature Method
$signatureMethod = $doc->createElement('ds:SignatureMethod');
$signatureMethod->setAttribute('Algorithm', 'http://www.w3.org/2001/04/xmldsig-more#rsa-sha256');
$signedInfo->appendChild($signatureMethod);

// Reference to Timestamp
$referenceTimestamp = $doc->createElement('ds:Reference');
$referenceTimestamp->setAttribute('URI', '#' . $wsuTimestampId);
$signedInfo->appendChild($referenceTimestamp);

// Transform for Timestamp
$transformTimestamp = $doc->createElement('ds:Transforms');
$referenceTimestamp->appendChild($transformTimestamp);

$transformAlgorithmTimestamp = $doc->createElement('ds:Transform');
$transformAlgorithmTimestamp->setAttribute('Algorithm', 'http://www.w3.org/2001/10/xml-exc-c14n#');
$transformTimestamp->appendChild($transformAlgorithmTimestamp);

$inclusiveNamespacesTimestamp = $doc->createElement('ec:InclusiveNamespaces');
$inclusiveNamespacesTimestamp->setAttribute('PrefixList', 'wsse saml2 soapenv');
$inclusiveNamespacesTimestamp->setAttribute('xmlns:ec', 'http://www.w3.org/2001/10/xml-exc-c14n#');
$transformAlgorithmTimestamp->appendChild($inclusiveNamespacesTimestamp);

// Digest Method for Timestamp
$digestMethodTimestamp = $doc->createElement('ds:DigestMethod');
$digestMethodTimestamp->setAttribute('Algorithm', 'http://www.w3.org/2001/04/xmlenc#sha256');
$referenceTimestamp->appendChild($digestMethodTimestamp);

$digestValueTimestamp = $doc->createElement('ds:DigestValue', 'Digest_TimeStamp');
$referenceTimestamp->appendChild($digestValueTimestamp);

// Reference to Binary Security Token
$referenceBinarySecurityToken = $doc->createElement('ds:Reference');
$referenceBinarySecurityToken->setAttribute('URI', '#' . $wsuBinarySecurityTokenId);
$signedInfo->appendChild($referenceBinarySecurityToken);

// Transform for Binary Security Token
$transformBinarySecurityToken = $doc->createElement('ds:Transforms');
$referenceBinarySecurityToken->appendChild($transformBinarySecurityToken);

$transformAlgorithmBinarySecurityToken = $doc->createElement('ds:Transform');
$transformAlgorithmBinarySecurityToken->setAttribute('Algorithm', 'http://www.w3.org/2001/10/xml-exc-c14n#');
$transformBinarySecurityToken->appendChild($transformAlgorithmBinarySecurityToken);

$inclusiveNamespacesBinarySecurityToken = $doc->createElement('ec:InclusiveNamespaces');
$inclusiveNamespacesBinarySecurityToken->setAttribute('PrefixList', '');
$inclusiveNamespacesBinarySecurityToken->setAttribute('xmlns:ec', 'http://www.w3.org/2001/10/xml-exc-c14n#');
$transformAlgorithmBinarySecurityToken->appendChild($inclusiveNamespacesBinarySecurityToken);

// Digest Method for Binary Security Token
$digestMethodBinarySecurityToken = $doc->createElement('ds:DigestMethod');
$digestMethodBinarySecurityToken->setAttribute('Algorithm', 'http://www.w3.org/2001/04/xmlenc#sha256');
$referenceBinarySecurityToken->appendChild($digestMethodBinarySecurityToken);

$digestValueBinarySecurityToken = $doc->createElement('ds:DigestValue', 'Digest_Certificat');
$referenceBinarySecurityToken->appendChild($digestValueBinarySecurityToken);

// Reference to Body
$referenceBody = $doc->createElement('ds:Reference');
$referenceBody->setAttribute('URI', '#' . $wsuBodyId);
$signedInfo->appendChild($referenceBody);

// Transform for Body
$transformBody = $doc->createElement('ds:Transforms');
$referenceBody->appendChild($transformBody);

$transformAlgorithmBody = $doc->createElement('ds:Transform');
$transformAlgorithmBody->setAttribute('Algorithm', 'http://www.w3.org/2001/10/xml-exc-c14n#');
$transformBody->appendChild($transformAlgorithmBody);

$inclusiveNamespacesBody = $doc->createElement('ec:InclusiveNamespaces');
$inclusiveNamespacesBody->setAttribute('PrefixList', 'saml2');
$inclusiveNamespacesBody->setAttribute('xmlns:ec', 'http://www.w3.org/2001/10/xml-exc-c14n#');
$transformAlgorithmBody->appendChild($inclusiveNamespacesBody);

// Digest Method for Body
$digestMethodBody = $doc->createElement('ds:DigestMethod');
$digestMethodBody->setAttribute('Algorithm', 'http://www.w3.org/2001/04/xmlenc#sha256');
$referenceBody->appendChild($digestMethodBody);

$digestValueBody = $doc->createElement('ds:DigestValue', 'Digest_body');
$referenceBody->appendChild($digestValueBody);

// Key Info
$keyInfo = $doc->createElement('ds:KeyInfo');
$keyInfo->setAttribute('Id', 'KI-8A64C6552EAFBF716616951123186033');
$signature->appendChild($keyInfo);

// Security Token Reference
$securityTokenReference = $doc->createElement('wsse:SecurityTokenReference');
$securityTokenReference->setAttribute('wsu:Id', 'STR-8A64C6552EAFBF716616951123186054');
$keyInfo->appendChild($securityTokenReference);

// Reference to Binary Security Token
$referenceBinarySecurityTokenKey = $doc->createElement('wsse:Reference');
$referenceBinarySecurityTokenKey->setAttribute('URI', '#' . $wsuBinarySecurityTokenId);
$referenceBinarySecurityTokenKey->setAttribute('ValueType', 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-x509-token-profile-1.0#X509v3');
$securityTokenReference->appendChild($referenceBinarySecurityTokenKey);

// Timestamp
$timestamp = $doc->createElement('wsu:Timestamp');
$timestamp->setAttribute('wsu:Id', $wsuTimestampId);
$security->appendChild($timestamp);
$createdElement = $doc->createElement('wsu:Created', $created);
$timestamp->appendChild($createdElement);
$expiresElement = $doc->createElement('wsu:Expires', $expires);
$timestamp->appendChild($expiresElement);

// Créez le corps SOAP
$body = $doc->createElement('soapenv:Body');
$body->setAttribute('wsu:Id', $wsuBodyId);
$body->setAttribute('xmlns:wsu', 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd');
$envelope->appendChild($body);

// Contenu du corps SOAP (SAML Request)
$authnRequest = $doc->createElement('saml2p:AuthnRequest');
$authnRequest->setAttribute('AssertionConsumerServiceURL', 'https://ws.mysecu.lu:7443');
$authnRequest->setAttribute('Destination', 'https://www-integration.esante.lu/auth/realms/organization/ideosso/protocol/saml');
$authnRequest->setAttribute('ID', 'saml-3201bcaf15f82be8bf146387317f23fd');
$authnRequest->setAttribute('IssueInstant', $dateIssueInstant);
$authnRequest->setAttribute('ProtocolBinding', 'urn:oasis:names:tc:SAML:2.0:bindings:SOAP');
$authnRequest->setAttribute('Version', '2.0');
$authnRequest->setAttribute('xmlns:saml2p', 'urn:oasis:names:tc:SAML:2.0:protocol');
$body->appendChild($authnRequest);

// Créez le nœud de l'émetteur
$issuer = $doc->createElement('saml2:Issuer', 'https://ws.mysecu.lu:7443');
$authnRequest->appendChild($issuer);

// Créez les extensions
$extensions = $doc->createElement('saml2p:Extensions');
$authnRequest->appendChild($extensions);

// Créez l'attribut
$attribute = $doc->createElement('saml2:Attribute');
$attribute->setAttribute('Name', 'psEHealthID');
$attribute->setAttribute('NameFormat', 'urn:oasis:names:tc:SAML:2.0:attrname-format:unspecified');
$extensions->appendChild($attribute);

// Créez la valeur de l'attribut
$attributeValue = $doc->createElement('saml2:AttributeValue', $psEHealthID);
$attribute->appendChild($attributeValue);

// Créez le sujet
$subject = $doc->createElement('saml2:Subject');
$authnRequest->appendChild($subject);

// Créez la confirmation du sujet
$subjectConfirmation = $doc->createElement('saml2:SubjectConfirmation');
$subjectConfirmation->setAttribute('Method', 'urn:oasis:names:tc:SAML:2.0:cm:bearer');
$subject->appendChild($subjectConfirmation);

// Créez l'authnContext
$requestedAuthnContext = $doc->createElement('saml2p:RequestedAuthnContext');
$requestedAuthnContext->setAttribute('Comparison', 'minimum');
$authnRequest->appendChild($requestedAuthnContext);

// Créez la classe de contexte d'authentification
$authnContextClassRef = $doc->createElement('saml2:AuthnContextClassRef', 'urn:oasis:names:tc:SAML:2.0:ac:classes:X509');
$requestedAuthnContext->appendChild($authnContextClassRef);

/*XML FIN d'ETRE CONSTRUIT ON PASSE AU TRAITEMENT*/

$digestTimestampNode = $doc->getElementsByTagName('ds:DigestValue')->item(0);//digest TT
$digestCertificatNode = $doc->getElementsByTagName('ds:DigestValue')->item(1);//digest cert
$digestBodyNode = $doc->getElementsByTagName('ds:DigestValue')->item(2);//digestbody
$timestampNode = $doc->getElementsByTagName('wsu:Timestamp');//D
$wsseBinaryToken = $doc->getElementsByTagName('wsse:BinarySecurityToken')->item(0);

$TimeStampCanonized = CanoniseTT($expires,$created,"logs/TimeStimeCanonizedFromScrath.xml");
$wsseBinaryTokenCanonized = CanoniseCertificat($publicCertWithoutTitle,"CertificatCanonizedFromScratch.xml");
$bodyNodeCanonized = CanoniseBody($dateIssueInstant,$psEHealthID,"BodyCanonizedFromScratch.xml");

$digestTimestamp = openssl_digest($TimeStampCanonized, 'sha256', true);//ok
$digestCertificat = openssl_digest($wsseBinaryTokenCanonized, 'sha256', true);//ok
$digestBody = openssl_digest($bodyNodeCanonized, 'sha256', true);//ok

// Remplacer les valeurs de hachage digest
$digestTimestampNode->nodeValue = base64_encode($digestTimestamp);//ok
$digestCertificatNode->nodeValue = base64_encode($digestCertificat);//ok
$digestBodyNode->nodeValue = base64_encode($digestBody);//ok
$signedInfoToSign = CanoniseSignedInfo(base64_encode($digestTimestamp),base64_encode($digestCertificat),base64_encode($digestBody));

openssl_sign($signedInfoToSign, $signature1, $privateKey, OPENSSL_ALGO_SHA256 );

$SignatureOfSignedInfo = base64_encode($signature1);

$signedInfoElement = $doc->getElementsByTagName('ds:SignedInfo')->item(0);
if ($signedInfoElement) {

    // Créer un nouvel élément ds:SignatureValue
    $signatureValueElement = $doc->createElement('ds:SignatureValue');
    $signatureValueElement->nodeValue = $SignatureOfSignedInfo;
    $signedInfoElement->parentNode->insertBefore($signatureValueElement, $signedInfoElement->nextSibling);
}

/***********************************************************************************************************/
$doc->formatOutput = true;//laisser ça là !
curl_setopt($ch, CURLOPT_POSTFIELDS, $doc->saveXML());

// Exécutez la requête cURL
$response = curl_exec($ch);

if (curl_errno($ch))
{
    $res['message'] = 'Erreur cURL : ' . curl_error($ch);
    echo json_encode($res); 
} else {
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    if ($httpCode == 500) {
      $res['message'] = 'Erreur 500 : ' . $response; 
      echo json_encode($res); exit;
    } else {
      // echo '=============  Réponse du serveur : ' . $response;
      array_push($res['soap']['request'], $doc->saveXML());
      array_push($res['soap']['response'], $response);
    }
}

try {
  file_put_contents('logs/'. $psEHealthID . '_' . $OPC->lastInsertId().'_RequestGuichet.xml', $doc->saveXML());
  file_put_contents('logs/'. $psEHealthID . '_' . $OPC->lastInsertId().'_ResponseGuichet.xml', $response);
  $req1 = $OPC->prepare(" INSERT INTO doctor_pid 
  (doctor_id, pshealthid, medical_code, service_place, patient_number, biller_id, act_code, act_number, guichet_date, date_modified) 
VALUES (:doctor_id, :pshealthid, :medical_code, :service_place, :patient_number, :biller_id, :act_code, :act_number, NOW(), NOW())");
  $req1->execute([
    'doctor_id' => $doctor_id,	
    'pshealthid' => $psEHealthID,	
    'medical_code' => $codeMedical,	
    'service_place' => $lieuPrestation,	
    'patient_number' => $varMatricule,	
    'biller_id' => $biller_id,	
    'act_code' => $code_prestataire,	
    'act_number' => $NombreActeMedical,	
  ]);
} catch (\Exception $e) {
  $res['message'] = "Error: " . $e->getMessage(); 
  echo json_encode($res); exit;
}
	
if (empty($response)) exit;
$docResponseGuichet = new DOMDocument();
$docResponseGuichet->loadXML($response);
$xpath = new DOMXPath($docResponseGuichet);
$xpath->registerNamespace('SOAP-ENV', 'http://schemas.xmlsoap.org/soap/envelope/');
$xpath->registerNamespace('samlp', 'urn:oasis:names:tc:SAML:2.0:protocol');
$xpath->registerNamespace('saml', 'urn:oasis:names:tc:SAML:2.0:assertion');
$xpath->registerNamespace('dsig', 'http://www.w3.org/2000/09/xmldsig#');

// Recherche de l'élément saml:Assertion dans la réponse
$assertionNode = $xpath->query('//SOAP-ENV:Envelope/SOAP-ENV:Body/samlp:Response/saml:Assertion')->item(0);
$assertionText = $assertionNode->c14n(true);
$assertionText2 = $assertionNode->ownerDocument->saveXML($assertionNode);

$assertionText = str_replace(' xmlns:saml="urn:oasis:names:tc:SAML:2.0:assertion"','',$assertionText);
$signatureId = $docResponseGuichet->getElementsByTagNameNS('http://www.w3.org/2000/09/xmldsig#', 'Signature')[0]->getAttribute('Id');
$certificates = $xpath->query('/SOAP-ENV:Envelope/SOAP-ENV:Body/samlp:Response/saml:Assertion/dsig:Signature/dsig:KeyInfo/dsig:X509Data/dsig:X509Certificate');
$x509Certificate = $certificates->item(0)->nodeValue;
$digestValue = $xpath->query('/SOAP-ENV:Envelope/SOAP-ENV:Body/samlp:Response/saml:Assertion/dsig:Signature/dsig:SignedInfo/dsig:Reference/dsig:DigestValue');
$digestValueX = $digestValue->item(0)->nodeValue;

$notBefore = $xpath->query('/SOAP-ENV:Envelope/SOAP-ENV:Body/samlp:Response/saml:Assertion/saml:Conditions/@NotBefore');
$notOnAfter = $xpath->query('/SOAP-ENV:Envelope/SOAP-ENV:Body/samlp:Response/saml:Assertion/saml:Conditions/@NotOnOrAfter');
$notBefore = $notBefore->item(0)->nodeValue;
$notOnAfter = $notOnAfter->item(0)->nodeValue;
$signatureValue = $xpath->query('/SOAP-ENV:Envelope/SOAP-ENV:Body/samlp:Response/saml:Assertion/dsig:Signature/dsig:SignatureValue');
$signatureValueX = $signatureValue->item(0)->nodeValue;
$keyInfoId = $xpath->query('/SOAP-ENV:Envelope/SOAP-ENV:Body/samlp:Response/saml:Assertion/dsig:Signature/dsig:KeyInfo/@Id');
$keyInfoId = $keyInfoId->item(0)->nodeValue;


if ($assertionNode !== null) {
    // Récupération de la valeur de l'attribut ID de l'assertion SAML
    $assertionID = $assertionNode->getAttribute('ID');
    $datedIssueInstant = $assertionNode->getAttribute('IssueInstant');
    // Récupération de l'élément saml:Issuer
    $issuerNode = $xpath->query('./saml:Issuer', $assertionNode)->item(0);
    $issuer = ($issuerNode !== null) ? $issuerNode->textContent : '';

} else {
  $res['message'] = "Error: Assertion SAML non trouvée dans la réponse."; 
  echo json_encode($res); exit;
}

$doc = new DomDocument();

$envelope = $doc->createElement('soapenv:Envelope');
$envelope->setAttribute('xmlns:soapenv', 'http://schemas.xmlsoap.org/soap/envelope/');

$header = $doc->createElement('soapenv:Header');

// Création de l'élément wsse:Security
$security = $doc->createElement('wsse:Security');
$security->setAttribute('xmlns:wsse', 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd');
$security->setAttribute('xmlns:wsu', 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd');
$security->setAttribute('xmlns:auth', 'http://schemas.xmlsoap.org/ws/2006/12/authorization');
$security->setAttribute('xmlns:ns', 'http://docs.oasis-open.org/ws-sx/ws-trust/200512');
$security->setAttribute('xmlns:soapenv', 'http://schemas.xmlsoap.org/soap/envelope/');
$security->setAttribute('xmlns:wsa', 'http://www.w3.org/2005/08/addressing');
$security->setAttribute('xmlns:wsp', 'http://schemas.xmlsoap.org/ws/2004/09/policy');
$security->setAttribute('xmlns:saml', 'urn:oasis:names:tc:SAML:2.0:assertion');

// Création de l'élément wsse:BinarySecurityToken
$binarySecurityToken = $doc->createElement('wsse:BinarySecurityToken', $publicCertWithoutTitle);
$binarySecurityToken->setAttribute('EncodingType', 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-soap-message-security-1.0#Base64Binary');
$binarySecurityToken->setAttribute('ValueType', 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-x509-tokenprofile-1.0#X509v3');
$binarySecurityToken->setAttribute('wsu:Id', 'X509-8A64C6552EAFBF716616951123314897');

// Ajout de wsse:BinarySecurityToken à wsse:Security
$security->appendChild($binarySecurityToken);

// Création de l'élément ds:Signature
$signature = $doc->createElement('ds:Signature');
$signature->setAttribute('Id', 'SIG-8A64C6552EAFBF7166169511233149011');
$signature->setAttribute('xmlns:ds', 'http://www.w3.org/2000/09/xmldsig#');

// Création de l'élément ds:SignedInfo
$signedInfo = $doc->createElement('ds:SignedInfo');

// Création de l'élément ds:CanonicalizationMethod
$canonicalizationMethod = $doc->createElement('ds:CanonicalizationMethod');
$canonicalizationMethod->setAttribute('Algorithm', 'http://www.w3.org/2001/10/xml-exc-c14n#');

// Création de l'élément ec:InclusiveNamespaces
$inclusiveNamespaces = $doc->createElement('ec:InclusiveNamespaces');
$inclusiveNamespaces->setAttribute('xmlns:ec', 'http://www.w3.org/2001/10/xml-exc-c14n#');
$inclusiveNamespaces->setAttribute('PrefixList', 'auth ns soapenv wsa wsp');
$canonicalizationMethod->appendChild($inclusiveNamespaces);


$signedInfo->appendChild($canonicalizationMethod);

// Création de l'élément ds:SignatureMethod
$signatureMethod = $doc->createElement('ds:SignatureMethod');
$signatureMethod->setAttribute('Algorithm', 'http://www.w3.org/2001/04/xmldsig-more#rsa-sha256');
$signedInfo->appendChild($signatureMethod);

// Création de l'élément ds:Reference pour URI #Id-7f5c096519317582a1926d8f-2
$reference1 = $doc->createElement('ds:Reference');
$reference1->setAttribute('URI', '#'. $assertionID.'');

// Création de l'élément ds:Transforms pour ds:Reference #Id-7f5c096519317582a1926d8f-2
$transforms1 = $doc->createElement('ds:Transforms');

// Création de l'élément ds:Transform pour ds:Reference #Id-7f5c096519317582a1926d8f-2
$transform1 = $doc->createElement('ds:Transform');
$transform1->setAttribute('Algorithm', 'http://www.w3.org/2001/10/xml-exc-c14n#');

// Création de l'élément ec:InclusiveNamespaces pour ds:Transform
$ecInclusiveNamespaces = $doc->createElement('ec:InclusiveNamespaces');
$ecInclusiveNamespaces->setAttribute('xmlns:ec', 'http://www.w3.org/2001/10/xml-exc-c14n#');
$ecInclusiveNamespaces->setAttribute('PrefixList', 'wsse wsu auth ns soapenv wsa wsp');
$transform1->appendChild($ecInclusiveNamespaces);

$transforms1->appendChild($transform1);
$reference1->appendChild($transforms1);

// Création de l'élément ds:DigestMethod pour ds:Reference #Id-7f5c096519317582a1926d8f-2
$digestMethod1 = $doc->createElement('ds:DigestMethod');
$digestMethod1->setAttribute('Algorithm', 'http://www.w3.org/2001/04/xmlenc#sha256');
$reference1->appendChild($digestMethod1);

$dateIssueInstant = getCurrentDateTimeInISO8601Z();
$BodyCanonized = CanoniseBody2($code_prestataire);

$SamlCanonized = canoniserSAMLresponse($dateIssueInstant,$assertionID,$signatureValueX,$x509Certificate,$signatureId,$digestValueX,$notBefore,$notOnAfter,$keyInfoId);
$digestAssertion = openssl_digest($SamlCanonized, 'sha256', true);//ok
$digestBody = openssl_digest($BodyCanonized, 'sha256', true);// à charger ici la version canonisé

// Création de l'élément ds:DigestValue pour ds:Reference #Id-7f5c096519317582a1926d8f-2
$digestValue1 = $doc->createElement('ds:DigestValue', base64_encode($digestAssertion));
$reference1->appendChild($digestValue1);

// Ajout de ds:Reference #Id-7f5c096519317582a1926d8f-2 à ds:SignedInfo
$signedInfo->appendChild($reference1);

// Ajout de ce qu'il manque (Reference pour Digest_of_Body) dans SignedInfo
$referenceBody = $doc->createElement('ds:Reference');
$referenceBody->setAttribute('URI', '#id-36854EF7B992756406157054522149119');

// Création de l'élément ds:Transforms
$transformsBody = $doc->createElement('ds:Transforms');

// Création de l'élément ds:Transform
$transformBody = $doc->createElement('ds:Transform');
$transformBody->setAttribute('Algorithm', 'http://www.w3.org/2001/10/xml-exc-c14n#');
$transformsBody->appendChild($transformBody);

// Création de l'élément ec:InclusiveNamespaces pour ds:Transform
$inclusiveNamespacesBody = $doc->createElement('ec:InclusiveNamespaces');
$inclusiveNamespacesBody->setAttribute('xmlns:ec', 'http://www.w3.org/2001/10/xml-exc-c14n#');
$inclusiveNamespacesBody->setAttribute('PrefixList', 'auth ns wsa wsp');
$transformBody->appendChild($inclusiveNamespacesBody);

$referenceBody->appendChild($transformsBody);

// Création de l'élément ds:DigestMethod pour Digest_of_Body
$digestMethodBody = $doc->createElement('ds:DigestMethod');
$digestMethodBody->setAttribute('Algorithm', 'http://www.w3.org/2001/04/xmlenc#sha256');
$referenceBody->appendChild($digestMethodBody);

// Création de l'élément ds:DigestValue pour Digest_of_Body
$digestValueBody = $doc->createElement('ds:DigestValue', base64_encode($digestBody));
$referenceBody->appendChild($digestValueBody);

// Ajout de ds:Reference pour Digest_of_Body à ds:SignedInfo
$signedInfo->appendChild($referenceBody);

// Ajout de ds:SignedInfo à ds:Signature
$signature->appendChild($signedInfo);

// Ajout de ds:Signature à wsse:Security
$security->appendChild($signature);

// Création de l'élément ds:SignatureValue
$signatureValue = $doc->createElement('ds:SignatureValue', 'Signature_of_SignedInfo');
$signature->appendChild($signatureValue);

// Création de l'élément ds:KeyInfo
$keyInfo = $doc->createElement('ds:KeyInfo');
$keyInfo->setAttribute('Id', 'KI-8A64C6552EAFBF716616951123314898');

// Création de l'élément wsse:SecurityTokenReference
$securityTokenReference = $doc->createElement('wsse:SecurityTokenReference');
$securityTokenReference->setAttribute('wsu:Id', 'STR-8A64C6552EAFBF716616951123314899');

// Création de l'élément wsse:Reference
$referenceKeyInfo = $doc->createElement('wsse:Reference');
$referenceKeyInfo->setAttribute('URI', '#X509-8A64C6552EAFBF716616951123314897');$referenceKeyInfo->setAttribute('URI', '#X509-8A64C6552EAFBF716616951123314897');
$referenceKeyInfo->setAttribute('ValueType', 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-x509-token-profile-1.0#X509v3');

$securityTokenReference->appendChild($referenceKeyInfo);
$keyInfo->appendChild($securityTokenReference);

$signature->appendChild($keyInfo);

// Ajout de soapenv:Header au document
$doc->appendChild($header);

// Création de l'élément <saml:Assertion>
$samlAssertion = $doc->createElement('saml:Assertion');

$security->appendChild($samlAssertion);

// Ajout de wsse:Security à soapenv:Header
$header->appendChild($security);

// Définition des namespaces utilisés dans le document
$soapenvURI = 'http://schemas.xmlsoap.org/soap/envelope/';
$wsuURI = 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd';
$nsURI = 'http://docs.oasis-open.org/ws-sx/ws-trust/200512';
$wsaURI = 'http://www.w3.org/2005/08/addressing';
$wspURI = 'http://schemas.xmlsoap.org/ws/2004/09/policy';
$authURI = 'http://schemas.xmlsoap.org/ws/2006/12/authorization';

// Création de l'élément <soapenv:Body> avec ses namespaces
$soapenvBody = $doc->createElement('soapenv:Body');
// $soapenvBody->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:soapenv', $soapenvURI);
$soapenvBody->setAttribute('wsu:Id', 'id-36854EF7B992756406157054522149119');
$soapenvBody->setAttribute('xmlns:wsu', $wsuURI);
$soapenvBody->setAttribute('xmlns:ns', $nsURI);
$soapenvBody->setAttribute('xmlns:wsa', $wsaURI);
$soapenvBody->setAttribute('xmlns:wsp', $wspURI);
$soapenvBody->setAttribute('xmlns:auth', $authURI);

// Création des autres éléments avec leurs namespaces respectifs
$requestSecurityToken = $doc->createElement('ns:RequestSecurityToken');
$requestType = $doc->createElement('ns:RequestType', 'http://docs.oasis-open.org/ws-sx/ws-trust/200512/Issue');
$tokenType = $doc->createElement('ns:TokenType', 'http://ws.mySecu.lu/trust/token/bst');
$appliesTo = $doc->createElement('wsp:AppliesTo');
$endpointReference = $doc->createElement('wsa:EndpointReference');
$address = $doc->createElement('wsa:Address', 'https://ws.mysecu.lu:7443/ws/soap/espinst/syncexchange');
$claims = $doc->createElement('ns:Claims');
$claims->setAttribute('Dialect', 'http://schemas.xmlsoap.org/ws/2006/12/authorization/authclaims');
$claimType = $doc->createElement('auth:ClaimType');
$claimType->setAttribute('Uri', 'http://ws.mysecu.lu/trust/prodo/espaceid');
$value = $doc->createElement('auth:Value', $code_prestataire);

// Construction de la structure XML en ajoutant les éléments les uns aux autres
$claimType->appendChild($value);
$claims->appendChild($claimType);
$appliesTo->appendChild($endpointReference);
$endpointReference->appendChild($address);
$requestSecurityToken->appendChild($requestType);
$requestSecurityToken->appendChild($tokenType);
$requestSecurityToken->appendChild($appliesTo);
$requestSecurityToken->appendChild($claims);

$soapenvBody->appendChild($requestSecurityToken);
$doc->appendChild($soapenvBody);

$signedInfoToSign = CanoniseSignedInfoCNS(base64_encode($digestBody),base64_encode($digestAssertion),$assertionID);

openssl_sign($signedInfoToSign, $signature1, $privateKey, OPENSSL_ALGO_SHA256 );

$SignatureOfSignedInfo = base64_encode($signature1);

$signatureValue->nodeValue = $SignatureOfSignedInfo;
$signedInfoElement->nodeValue = $SignatureOfSignedInfo;

$envelope->appendChild($header);

$envelope->appendChild($soapenvBody);
$doc->appendChild($envelope);
$doc->formatOutput = true;

$dsSignatureElement = $doc->getElementsByTagName('ds:Signature')->item(0);

$a = $doc->saveXML();
$a = str_replace('
      <saml:Assertion/>
    ',$assertionText2,$a);

$ch = curl_init();
$service_url = 'https://ws.mysecu.lu:7443/ws/soap/trust';

curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, TRUE);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, TRUE);
curl_setopt($ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_3);
curl_setopt($ch, CURLOPT_URL, $service_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSLCERT, 'certificat.pem');//ok
curl_setopt($ch, CURLOPT_CAINFO,  'certificats.pem');
curl_setopt($ch, CURLOPT_SSLKEYTYPE, 'pem');

curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
// curl_setopt($ch, CURLOPT_VERBOSE, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: text/xml;charset=UTF-8'
    )
);

curl_setopt($ch, CURLOPT_POSTFIELDS, $a);

// Exécutez la requête cURL
$response = curl_exec($ch);

if (curl_errno($ch))
{
    $res['message'] = "Error cURL : ".curl_error($ch); 
    echo json_encode($res); exit;
} else {
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    if ($httpCode == 500) {
      $res['message'] = "Erreur 500 : ".$response; 
      echo json_encode($res); exit;
    } else {
        // echo '=============  Réponse du serveur : ' . $response;
        array_push($res['soap']['request'], $a);
        array_push($res['soap']['response'], $response);
    }
}

try {
  $lastInsertId = $OPC->lastInsertId();
  file_put_contents('logs/'. $psEHealthID . '_' . $lastInsertId.'_requestCNS.xml', $a);
  file_put_contents('logs/'. $psEHealthID . '_' . $lastInsertId.'_respondeCNS.xml', $response);
  if ($lastInsertId) {
    $req2 = $OPC->prepare(" UPDATE doctor_pid SET ccss_token_date=NOW() WHERE pid_id = :lastInsertId");
    $req2->execute([
      'lastInsertId' => $lastInsertId
    ]);
  }
} catch (\Exception $e) {
  $res['message'] = "Error: " . $e->getMessage(); 
  echo json_encode($res); exit;
}

$CCss  = extractBinarySecurityToken($response);
$WsuID = extractBinarySecurityTokenID($response);

$doc = new DomDocument('1.0', 'UTF-8');

// Création de l'élément racine <soapenv:Envelope>
$envelope = $doc->createElement('soapenv:Envelope');
$envelope->setAttribute('xmlns:soapenv', 'http://schemas.xmlsoap.org/soap/envelope/');
$envelope->setAttribute('xmlns:sync', 'http://ws.mysecu.lu/generic/sync');
// $body->setAttribute('xmlns:sync', '');
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

$body = $doc->createElement('soapenv:Body');
$body->setAttribute('wsu:Id', 'id-36854EF7B992756406157054655293757');
$body->setAttribute('xmlns:wsu', 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd');
$envelope->appendChild($body);

// Création de l'élément <sync:RequestInfo>
$requestInfo = $doc->createElement('sync:RequestInfo');
$requestInfo->setAttribute('modelUID', '2023-CNS-PID-SIM-V1');
$body->appendChild($requestInfo);

$simulationMedecin = $doc->createElement('cns:simulationMedecin');
$simulationMedecin->setAttribute('xmlns:cns', 'http://www.secu.lu/ciss/cns');
$simulationMedecin->setAttribute('xmlns:com', 'http://www.secu.lu/ciss/cns/commonTypes');

$requestInfo->appendChild($simulationMedecin);

// Création de l'élément <cns:UTA>
$uta = $doc->createElement('cns:UTA');
$simulationMedecin->appendChild($uta);

// Ajout des éléments à <cns:UTA>
$identifiantFacturier = $doc->createElement('cns:identifiantFacturier', $biller_id);
$uta->appendChild($identifiantFacturier);

$matricule = $doc->createElement('cns:matricule', $varMatricule);
$uta->appendChild($matricule);

$varIdMemoireHonoraire = format_uuidv4(random_bytes(16));
$idMemoireHonoraire = $doc->createElement('cns:idMemoireHonoraire',$varIdMemoireHonoraire);
$uta->appendChild($idMemoireHonoraire);

$varDateEtablissementMemoireHonoraire = date('Y-m-d');
$dateEtablissementMemoireHonoraire = $doc->createElement('cns:dateEtablissementMemoireHonoraire',$varDateEtablissementMemoireHonoraire);
$uta->appendChild($dateEtablissementMemoireHonoraire);

$lignes = $doc->createElement('cns:Lignes');
$uta->appendChild($lignes);

$ligneUTA = $doc->createElement('cns:LigneUTA');
$lignes->appendChild($ligneUTA);
$varIdentifiantExternePrestation = format_uuidv4(random_bytes(16));

$identifiantExternePrestation = $doc->createElement('cns:identifiantExternePrestation',$varIdentifiantExternePrestation);
$ligneUTA->appendChild($identifiantExternePrestation);
$varDateDebutPrestation = date('Y-m-d');
$dateDebutPrestation = $doc->createElement('cns:dateDebutPrestation',$varDateDebutPrestation);
$ligneUTA->appendChild($dateDebutPrestation);

$acte = $doc->createElement('cns:acte');
$ligneUTA->appendChild($acte);

$codeActe = $doc->createElement('com:codeActe', 'C1');
$acte->appendChild($codeActe);

$nombre = $doc->createElement('com:nombre', '1');
$acte->appendChild($nombre);

$lieuDePrestation = $doc->createElement('cns:lieuDePrestation', '01');
$ligneUTA->appendChild($lieuDePrestation);

$bodyNodeCanonized = CanoniseBody3($varIdMemoireHonoraire,$varDateEtablissementMemoireHonoraire,$varIdentifiantExternePrestation,$varDateDebutPrestation,$codeMedical,$lieuPrestation,$varMatricule,$NombreActeMedical);

$digestBody = openssl_digest($bodyNodeCanonized, 'sha256', true);//ok

$digestValue = $doc->getElementsByTagName('ds:DigestValue')->item(0);
$digestValue->nodeValue = base64_encode($digestBody);
$canonizedSignature = CanoniseSignedInfoBusiness($digestBody);
openssl_sign($canonizedSignature, $signature1, $privateKey, OPENSSL_ALGO_SHA256 );

$SignatureValue = $doc->getElementsByTagName('ds:SignatureValue')->item(0);
$SignatureValue->nodeValue = base64_encode($signature1);

  $doc->formatOutput = true;
  $a = $doc->saveXML();

  $ch = curl_init();
  $soapActionHeaderValue = 'exchange';
  $service_url = 'https://ws.mysecu.lu:7443/ws/soap/espinst/syncexchange';
  // Configurez les options cURL
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, TRUE);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, TRUE);
  curl_setopt($ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_3);
  curl_setopt($ch, CURLOPT_URL, $service_url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_SSLCERT, 'certificat.pem');
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

  // Exécutez la requête cURL
  $response = curl_exec($ch);

  if (curl_errno($ch))
  {
    $res['message'] = "Erreur Bussiness cURL : " . curl_error($ch); 
    echo json_encode($res); exit;
  } else {
      $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
      if ($httpCode == 500) {
        $res['message'] = "Erreur Bussiness 500 : " . $response; 
        echo json_encode($res); exit;
      } else {
        // echo '=============  Réponse du serveur : ' . $response;
        array_push($res['soap']['request'], $a);
        array_push($res['soap']['response'], $response);
      }
  }

  $docResponseBusinessCall = new DOMDocument();
  $docResponseBusinessCall->loadXML($response);
  $xpath = new DOMXPath($docResponseBusinessCall);
  $xpath->registerNamespace('cns', 'http://www.secu.lu/ciss/cns');
  $xpath->registerNamespace('soap', 'http://schemas.xmlsoap.org/soap/envelope/');
  $xpath->registerNamespace('mySecu', 'http://ws.mysecu.lu/generic/sync');
  $xpath->registerNamespace('cns', 'http://www.secu.lu/ciss/cns');
  $query = '//cns:identifiantReponseSimulation';
  $nodes = $xpath->query($query);
  if ($nodes->length > 0) {
      $id_response_simulation = $nodes->item(0)->nodeValue;
  }

  if ($lastInsertId) {
    file_put_contents('logs/'. $psEHealthID . '_' . $OPC->lastInsertId().'_RequestBusiness.xml', $a);
    file_put_contents('logs/'. $psEHealthID . '_' . $OPC->lastInsertId().'_ResponseBusiness.xml', $response);
    $req2 = $OPC->prepare(" UPDATE doctor_pid SET 
    ccss_token=:CCss, wsu_id=:WsuID, 
    id_memoire_honoraire=:id_memoire_honoraire, 
    id_externe_prestation=:id_externe_prestation, 
    id_response_simulation=:id_response_simulation, 
    id_response_date=NOW() WHERE pid_id = :lastInsertId");
    $req2->execute([
      'id_memoire_honoraire' => $varIdMemoireHonoraire,
      'id_response_simulation' => $id_response_simulation,
      'id_externe_prestation' => $varIdentifiantExternePrestation,
      "CCss" => $CCss,
      "WsuID" => $WsuID,
      'lastInsertId' => $lastInsertId
    ]);
  }
  $res['status'] = 1;
  $res['message'] = 'Simulation request is posted successfully';
  array_push($res['soap']['request'], $a);
  array_push($res['soap']['response'], $response);
   
  echo json_encode($res);
}
function extractBinarySecurityToken($xmlString) {
    $doc = new DOMDocument();
    $doc->loadXML($xmlString);

    $xpath = new DOMXPath($doc);
    $xpath->registerNamespace('wsse', 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd');

    $result = $xpath->evaluate('string(//wsse:BinarySecurityToken)');

    return $result;
}
function extractBinarySecurityTokenID($xmlString) {
    $doc = new DOMDocument();
    $doc->loadXML($xmlString);

    $xpath = new DOMXPath($doc);
    $xpath->registerNamespace('wsse', 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd');
    $xpath->registerNamespace('wsu', 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd');

    $result = $xpath->evaluate('string(//wsse:BinarySecurityToken/@wsu:Id)');

    return $result;
}
function CanoniseTT($expired,$created,$filename="") {
   $CanonisedTimeStamp ='<wsu:Timestamp xmlns:saml2="urn:oasis:names:tc:SAML:2.0:assertion" xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd" xmlns:wsu="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd" wsu:Id="TS-8A64C6552EAFBF716616951123185611">
        <wsu:Created>'.$created.'</wsu:Created>
        <wsu:Expires>'.$expired.'</wsu:Expires>
      </wsu:Timestamp>';
	if($filename != "")
	{
		file_put_contents($filename, $CanonisedTimeStamp);
	}

   return $CanonisedTimeStamp;
}
function CanoniseCertificat($publicCertWithoutTitle,$filename="") {
   $CanonisedCertificat = '<wsse:BinarySecurityToken xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd" xmlns:wsu="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd" EncodingType="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-soap-message-security-1.0#Base64Binary" ValueType="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-x509-tokenprofile-1.0#X509v3" wsu:Id="X509-8A64C6552EAFBF716616951123185992">'.$publicCertWithoutTitle.'</wsse:BinarySecurityToken>';
   
   return $CanonisedCertificat;
}
function CanoniseBody($dateIssueInstant,$psEHealthID,$filename="") {
	
$CanonizedBody = '<soapenv:Body xmlns:saml2="urn:oasis:names:tc:SAML:2.0:assertion" xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:wsu="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd" wsu:Id="id-8A64C6552EAFBF716616951123186195">
    <saml2p:AuthnRequest xmlns:saml2p="urn:oasis:names:tc:SAML:2.0:protocol" AssertionConsumerServiceURL="https://ws.mysecu.lu:7443" Destination="https://www-integration.esante.lu/auth/realms/organization/ideosso/protocol/saml" ID="saml-3201bcaf15f82be8bf146387317f23fd" IssueInstant="'.$dateIssueInstant.'" ProtocolBinding="urn:oasis:names:tc:SAML:2.0:bindings:SOAP" Version="2.0">
      <saml2:Issuer>https://ws.mysecu.lu:7443</saml2:Issuer>
      <saml2p:Extensions>
        <saml2:Attribute Name="psEHealthID" NameFormat="urn:oasis:names:tc:SAML:2.0:attrname-format:unspecified">
          <saml2:AttributeValue>'.$psEHealthID.'</saml2:AttributeValue>
        </saml2:Attribute>
      </saml2p:Extensions>
      <saml2:Subject>
        <saml2:SubjectConfirmation Method="urn:oasis:names:tc:SAML:2.0:cm:bearer"></saml2:SubjectConfirmation>
      </saml2:Subject>
      <saml2p:RequestedAuthnContext Comparison="minimum">
        <saml2:AuthnContextClassRef>urn:oasis:names:tc:SAML:2.0:ac:classes:X509</saml2:AuthnContextClassRef>
      </saml2p:RequestedAuthnContext>
    </saml2p:AuthnRequest>
  </soapenv:Body>';

   return $CanonizedBody;
}
function CanoniseBody2($code_prestataire)
{
	$CanonizedBody ='<soapenv:Body xmlns:auth="http://schemas.xmlsoap.org/ws/2006/12/authorization" xmlns:ns="http://docs.oasis-open.org/ws-sx/ws-trust/200512" xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:wsa="http://www.w3.org/2005/08/addressing" xmlns:wsp="http://schemas.xmlsoap.org/ws/2004/09/policy" xmlns:wsu="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd" wsu:Id="id-36854EF7B992756406157054522149119">
    <ns:RequestSecurityToken>
      <ns:RequestType>http://docs.oasis-open.org/ws-sx/ws-trust/200512/Issue</ns:RequestType>
      <ns:TokenType>http://ws.mySecu.lu/trust/token/bst</ns:TokenType>
      <wsp:AppliesTo>
        <wsa:EndpointReference>
          <wsa:Address>https://ws.mysecu.lu:7443/ws/soap/espinst/syncexchange</wsa:Address>
        </wsa:EndpointReference>
      </wsp:AppliesTo>
      <ns:Claims Dialect="http://schemas.xmlsoap.org/ws/2006/12/authorization/authclaims">
        <auth:ClaimType Uri="http://ws.mysecu.lu/trust/prodo/espaceid">
          <auth:Value>'.$code_prestataire.'</auth:Value>
        </auth:ClaimType>
      </ns:Claims>
    </ns:RequestSecurityToken>
  </soapenv:Body>';
   
     file_put_contents("logs/BodyCNSCanonized.xml", $CanonizedBody);
	
	 return $CanonizedBody;
}
function CanoniseBody3($varIdMemoireHonoraire,$varDateEtablissementMemoireHonoraire,$varIdentifiantExternePrestation,$varDateDebutPrestation,$codeMedical,$lieuPrestation,$varMatricule,$NombreActeMedical)
{  
  $CanonizedBody = '<soapenv:Body xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:sync="http://ws.mysecu.lu/generic/sync" xmlns:wsu="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd" wsu:Id="id-36854EF7B992756406157054655293757">
    <sync:RequestInfo modelUID="2023-CNS-PID-SIM-V1">
      <cns:simulationMedecin xmlns:cns="http://www.secu.lu/ciss/cns">
        <cns:UTA>
          <cns:identifiantFacturier>90812100</cns:identifiantFacturier>
          <cns:matricule>'.$varMatricule.'</cns:matricule>
          <cns:idMemoireHonoraire>'.$varIdMemoireHonoraire.'</cns:idMemoireHonoraire>
          <cns:dateEtablissementMemoireHonoraire>'.$varDateEtablissementMemoireHonoraire.'</cns:dateEtablissementMemoireHonoraire>
          <cns:Lignes>
            <cns:LigneUTA>
              <cns:identifiantExternePrestation>'.$varIdentifiantExternePrestation.'</cns:identifiantExternePrestation>
              <cns:dateDebutPrestation>'.$varDateDebutPrestation.'</cns:dateDebutPrestation>
              <cns:acte>
                <com:codeActe xmlns:com="http://www.secu.lu/ciss/cns/commonTypes">'.$codeMedical.'</com:codeActe>
                <com:nombre xmlns:com="http://www.secu.lu/ciss/cns/commonTypes">'.$NombreActeMedical.'</com:nombre>
              </cns:acte>
              <cns:lieuDePrestation>'.$lieuPrestation.'</cns:lieuDePrestation>
            </cns:LigneUTA>
          </cns:Lignes>
        </cns:UTA>
      </cns:simulationMedecin>
    </sync:RequestInfo>
  </soapenv:Body>';
   
  file_put_contents("logs/BodyCNSBusiness.xml", $CanonizedBody);
  
  return $CanonizedBody;
}
function format_uuidv4($data)
{
  assert(strlen($data) == 16);

  $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // set version to 0100
  $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // set bits 6-7 to 10
    
  return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}
function canoniserSAMLresponse($dateIssueInstant,$id,$signatureValue,$x509cert,$IdSignature,$digestValue,$notBefore,$notOnAfter,$keyInfoId)
{
	  $samlCanonized = '<saml:Assertion xmlns:auth="http://schemas.xmlsoap.org/ws/2006/12/authorization" xmlns:ns="http://docs.oasis-open.org/ws-sx/ws-trust/200512" xmlns:saml="urn:oasis:names:tc:SAML:2.0:assertion" xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:wsa="http://www.w3.org/2005/08/addressing" xmlns:wsp="http://schemas.xmlsoap.org/ws/2004/09/policy" xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd" xmlns:wsu="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd" ID="'.$id.'" IssueInstant="'.$dateIssueInstant.'" Version="2.0"><saml:Issuer Format="urn:oasis:names:tc:SAML:1.1:nameid-format:X509SubjectName">https://www-integration.esante.lu/auth/realms/organization</saml:Issuer><dsig:Signature xmlns:dsig="http://www.w3.org/2000/09/xmldsig#" Id="'.$IdSignature.'"><dsig:SignedInfo><dsig:CanonicalizationMethod Algorithm="http://www.w3.org/2001/10/xml-exc-c14n#"></dsig:CanonicalizationMethod><dsig:SignatureMethod Algorithm="http://www.w3.org/2001/04/xmldsig-more#rsa-sha256"></dsig:SignatureMethod><dsig:Reference URI="#'.$id.'"><dsig:Transforms><dsig:Transform Algorithm="http://www.w3.org/2000/09/xmldsig#enveloped-signature"></dsig:Transform><dsig:Transform Algorithm="http://www.w3.org/2001/10/xml-exc-c14n#"></dsig:Transform></dsig:Transforms><dsig:DigestMethod Algorithm="http://www.w3.org/2001/04/xmlenc#sha256"></dsig:DigestMethod><dsig:DigestValue>'.$digestValue.'</dsig:DigestValue></dsig:Reference></dsig:SignedInfo><dsig:SignatureValue>'.$signatureValue.'</dsig:SignatureValue><dsig:KeyInfo Id="'.$keyInfoId.'"><dsig:X509Data><dsig:X509Certificate>'.$x509cert.'</dsig:X509Certificate></dsig:X509Data></dsig:KeyInfo></dsig:Signature><saml:Subject><saml:NameID Format="urn:oasis:names:tc:SAML:1.1:nameid-format:X509SubjectName">C=LU,title=PS-001,SN=MIPIT,GN=TEST,serialNumber=2854201475,CN=TEST MIPIT,emailAddress=certificat@esante.lu</saml:NameID><saml:SubjectConfirmation Method="urn:oasis:names:tc:SAML:2.0:cm:bearer"></saml:SubjectConfirmation></saml:Subject><saml:Conditions NotBefore="'.$notBefore.'" NotOnOrAfter="'.$notOnAfter.'"></saml:Conditions><saml:AttributeStatement><saml:Attribute Name="userCertificate" NameFormat="urn:vordel:attribute:1.0"><saml:AttributeValue>MIIGqjCCBJKgAwIBAgIIVVH8QaoVhQYwDQYJKoZIhvcNAQELBQAwPTEWMBQGA1UEAwwNQWVTLUktQ0EtVGVzdDEWMBQGA1UECgwNQWdlbmNlIGVTYW50ZTELMAkGA1UEBhMCTFUwHhcNMjMxMTAyMTI0MjM5WhcNMjUxMTAxMTI0MjM4WjCBjDEjMCEGCSqGSIb3DQEJARYUY2VydGlmaWNhdEBlc2FudGUubHUxEzARBgNVBAMMClRFU1QgTUlQSVQxEzARBgNVBAUTCjI4NTQyMDE0NzUxDTALBgNVBCoMBFRFU1QxDjAMBgNVBAQMBU1JUElUMQ8wDQYDVQQMDAZQUy0wMDExCzAJBgNVBAYTAkxVMIICIjANBgkqhkiG9w0BAQEFAAOCAg8AMIICCgKCAgEAtLqgrFDKKDfKaxJzxF0PQO2yxpAHuQ/bNIuW3Jr23YWlZgvEqXl2031JDAt1Pr4sTr2vSRMy8PPjytY4VUffD9ahIRDf/F/m2Sit9ggE+6+J3E36naDsGGYmU7Mre0BEKIA5Ew6LQvpFKvW9Jx7CQxw6jfxGlr3JXpx9pJxlPohmgTvXIc6//Pffiu6LTEuKiK3fKYCpU1ZKIiOrX+IhhLjQf9NxKd7IQOnrWQTSvvrUJGAm3dYtAg/s/dKNNYwEb/gS6cGs4Glh0b8DH82ZfgEQRk3wj4C+m+mMsh0KDRb41Lwj+hJ4CY9xqrcXBpt+MOFvBW1lrHKVW6Ss7U+VUqS36IzhmpurJ0jjWeyR2BKjJaDte4kN6uwdhjXQDRCMLTnEHKpL6xLki74G1n0ZiIDIjgw72SBpKjL/qs0M6bR9uWKK6iXWiy1Iaj/5lNmmV840uKxbLqARgx/2MKvXsCsaUUq30gMHJzQMVb0ftb4ayZmox3PO8BATdovofmBYP66W5SDnCJhA9eLghKjaxSBKayNmbIxz+PwlbR7+k3afN9ZuqacWuJP5gsEft0yQRB9YgyB9gamkmmsI/T9MNwm54+JAx2C2CYoZ6svkbZ3Op456oV+W/Fl4q9OqyMCMrs7etpwIDuec27npcZpn5htClNrE6cV7WX2gYx/2uRMCAwEAAaOCAVwwggFYMB8GA1UdIwQYMBaAFHzNGwGIayHofP6XJ94Tk9m0PHpCMH0GCCsGAQUFBwEBBHEwbzA5BggrBgEFBQcwAoYtaHR0cDovL3BraS1pbnRlZ3JhdGlvbi5lc2FudGUubHUvQWVTLUktQ0EuY3J0MDIGCCsGAQUFBzABhiZodHRwOi8vcGtpLWludGVncmF0aW9uLmVzYW50ZS5sdS9PQ1NQLzBHBgNVHSAEQDA+MDwGBFUdIAAwNDAyBggrBgEFBQcCARYmaHR0cHM6Ly93d3ctaW50ZWdyYXRpb24uZXNhbnRlLmx1L0NQUy8wPgYDVR0fBDcwNTAzoDGgL4YtaHR0cDovL3BraS1pbnRlZ3JhdGlvbi5lc2FudGUubHUvQWVTLUktQ0EuY3JsMB0GA1UdDgQWBBT5RObXWvJuDPOmkhZJyFFVdhrD/jAOBgNVHQ8BAf8EBAMCBeAwDQYJKoZIhvcNAQELBQADggIBAAvAW/8Xn5tWZQ1Czjr+kzu+yxc3nhF9uLE9wZH4whj+ojeS6uuJQzPX4frRrrlde/k/+7jeIQ7e4RP3t+5/qus7D6lHchu/Enufl2Cb5SsZRFAUbWYuwQiTl27onMws8TpZCy1HfUowdlJRI9FaLw7Uleb6PfLtkv6qtP6jk7Z4ul0HUc1iLWoTlpfSSCEfNrjtLvHZ22aTxOvEgxID7pEx6qjrg6SimCMRHCv+h2hy1xvZ679y/ax8t7eFpxfaiqYylsPO/WoNKRfMqYe5XVu4jM3YWpiDNoHIZXaYfZgtHiLX+8KL0LLkJNL0ukqZjFDt+xLXOqAwPxkBOGL+p6PqkWlmQARACz5TFLLP0KlMnzgGeevlMJ9ytt74xxHimV1YxruK/bIxDqMASxVDTHxM0JG/XNPUBBr9JaT3MlGpKsXB5s2DWTQHjfmkW/MpcCMjL+VzlQwrziOnUsyvSqXmIYnqTUrGA8bM+w/PwNuIk8ZiwRfMEPII8OAika5FXDrVGl1d3lkRrogz8lVXo+bQeztqyQMwkVYCQFYW9O9479AyvcLE2IuuirrSWnyKPD8CnnCeV/2oTCmLm75lUTdUuwtg2J5Z5F426y6+jsWg9IZej9Kxe8wgSTcSMuiibfNszMl7eFZYcwhkyAksFh2pGgmuH8nfeoDoSjo++XTQ</saml:AttributeValue></saml:Attribute><saml:Attribute Name="psMatricule" NameFormat="urn:vordel:attribute:1.0"><saml:AttributeValue>1970050299902</saml:AttributeValue></saml:Attribute><saml:Attribute Name="psEHealthID" NameFormat="urn:vordel:attribute:1.0"><saml:AttributeValue>2854201475</saml:AttributeValue></saml:Attribute></saml:AttributeStatement></saml:Assertion>';
	   
	file_put_contents("logs/SamlCanonized.xml", $samlCanonized);
	
	return $samlCanonized;
}
function GetCcssToken($CCss,$wsuId)
{
	
	$ccssToken = '<wsse:BinarySecurityToken xmlns:wsu="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd" xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd" wsu:Id="'.$wsuId.'" ValueType="http://ws.mySecu.lu/trust/token/bst" EncodingType="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-soap-message-security-1.0#Base64Binary">'.$CCss.'</wsse:BinarySecurityToken>';
	
	file_put_contents("logs/ccssToken.xml", $ccssToken);
	
	return $ccssToken;
}
function CanoniseSignedInfo($digestTimestamp,$digestCertificat,$digestBody)
{
	$signedinfo = '<ds:SignedInfo xmlns:ds="http://www.w3.org/2000/09/xmldsig#" xmlns:saml2="urn:oasis:names:tc:SAML:2.0:assertion" xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/">
          <ds:CanonicalizationMethod Algorithm="http://www.w3.org/2001/10/xml-exc-c14n#">
            <ec:InclusiveNamespaces xmlns:ec="http://www.w3.org/2001/10/xml-exc-c14n#" PrefixList="saml2 soapenv"></ec:InclusiveNamespaces>
          </ds:CanonicalizationMethod>
          <ds:SignatureMethod Algorithm="http://www.w3.org/2001/04/xmldsig-more#rsa-sha256"></ds:SignatureMethod>
          <ds:Reference URI="#TS-8A64C6552EAFBF716616951123185611">
            <ds:Transforms>
              <ds:Transform Algorithm="http://www.w3.org/2001/10/xml-exc-c14n#">
                <ec:InclusiveNamespaces xmlns:ec="http://www.w3.org/2001/10/xml-exc-c14n#" PrefixList="wsse saml2 soapenv"></ec:InclusiveNamespaces>
              </ds:Transform>
            </ds:Transforms>
            <ds:DigestMethod Algorithm="http://www.w3.org/2001/04/xmlenc#sha256"></ds:DigestMethod>
            <ds:DigestValue>'.$digestTimestamp.'</ds:DigestValue>
          </ds:Reference>
          <ds:Reference URI="#X509-8A64C6552EAFBF716616951123185992">
            <ds:Transforms>
              <ds:Transform Algorithm="http://www.w3.org/2001/10/xml-exc-c14n#">
                <ec:InclusiveNamespaces xmlns:ec="http://www.w3.org/2001/10/xml-exc-c14n#" PrefixList=""></ec:InclusiveNamespaces>
              </ds:Transform>
            </ds:Transforms>
            <ds:DigestMethod Algorithm="http://www.w3.org/2001/04/xmlenc#sha256"></ds:DigestMethod>
            <ds:DigestValue>'.$digestCertificat.'</ds:DigestValue>
          </ds:Reference>
          <ds:Reference URI="#id-8A64C6552EAFBF716616951123186195">
            <ds:Transforms>
              <ds:Transform Algorithm="http://www.w3.org/2001/10/xml-exc-c14n#">
                <ec:InclusiveNamespaces xmlns:ec="http://www.w3.org/2001/10/xml-exc-c14n#" PrefixList="saml2"></ec:InclusiveNamespaces>
              </ds:Transform>
            </ds:Transforms>
            <ds:DigestMethod Algorithm="http://www.w3.org/2001/04/xmlenc#sha256"></ds:DigestMethod>
            <ds:DigestValue>'.$digestBody.'</ds:DigestValue>
          </ds:Reference>
        </ds:SignedInfo>';

	return $signedinfo;
}
function CanoniseSignedInfoCNS($digestbody,$digestAssertion,$uriAssertion,$uriBody="",$filename="signedinfoCns.xml")
{
	$signedinfo = '<ds:SignedInfo xmlns:auth="http://schemas.xmlsoap.org/ws/2006/12/authorization" xmlns:ds="http://www.w3.org/2000/09/xmldsig#" xmlns:ns="http://docs.oasis-open.org/ws-sx/ws-trust/200512" xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:wsa="http://www.w3.org/2005/08/addressing" xmlns:wsp="http://schemas.xmlsoap.org/ws/2004/09/policy">
          <ds:CanonicalizationMethod Algorithm="http://www.w3.org/2001/10/xml-exc-c14n#">
            <ec:InclusiveNamespaces xmlns:ec="http://www.w3.org/2001/10/xml-exc-c14n#" PrefixList="auth ns soapenv wsa wsp"></ec:InclusiveNamespaces>
          </ds:CanonicalizationMethod>
          <ds:SignatureMethod Algorithm="http://www.w3.org/2001/04/xmldsig-more#rsa-sha256"></ds:SignatureMethod>
          <ds:Reference URI="#'.$uriAssertion.'">
            <ds:Transforms>
              <ds:Transform Algorithm="http://www.w3.org/2001/10/xml-exc-c14n#">
                <ec:InclusiveNamespaces xmlns:ec="http://www.w3.org/2001/10/xml-exc-c14n#" PrefixList="wsse wsu auth ns soapenv wsa wsp"></ec:InclusiveNamespaces>
              </ds:Transform>
            </ds:Transforms>
            <ds:DigestMethod Algorithm="http://www.w3.org/2001/04/xmlenc#sha256"></ds:DigestMethod>
            <ds:DigestValue>'.$digestAssertion.'</ds:DigestValue>
          </ds:Reference>
          <ds:Reference URI="#id-36854EF7B992756406157054522149119">
            <ds:Transforms>
              <ds:Transform Algorithm="http://www.w3.org/2001/10/xml-exc-c14n#">
                <ec:InclusiveNamespaces xmlns:ec="http://www.w3.org/2001/10/xml-exc-c14n#" PrefixList="auth ns wsa wsp"></ec:InclusiveNamespaces>
              </ds:Transform>
            </ds:Transforms>
            <ds:DigestMethod Algorithm="http://www.w3.org/2001/04/xmlenc#sha256"></ds:DigestMethod>
            <ds:DigestValue>'.$digestbody.'</ds:DigestValue>
          </ds:Reference>
        </ds:SignedInfo>';

	if($filename != "")
	{
		file_put_contents($filename, $signedinfo);
	}
	return $signedinfo;
}
function CanoniseSignedInfoBusiness($digestBody)
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
		
	  file_put_contents("logs/SignedInfoBusinessCall.xml", $signedinfo);
	  
	  return $signedinfo;
}