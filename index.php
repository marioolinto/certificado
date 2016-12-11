<?php
	require_once('certificado.php');	
	header('Content-type: text/xml;');

	$path = "certificado/16825779000114.pfx";
	$pass = "1234";
	$privateKey = '';
	$x509Certificate = '';
	$message = '';
	
	//libxml_use_internal_errors(true);

	$root = new DOMDocument('1.0', 'UTF-8');

	$enviar = $root->createElementNS('http://www.ginfes.com.br/servico_enviar_lote_rps_envio_v03.xsd', 'ns1:EnviarLoteRpsEnvio');

	$enviar->setAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
	$enviar->setAttribute('xmlns:ns2', 'http://www.ginfes.com.br/tipos_v03.xsd');
	$enviar->setAttribute('xsi:schemaLocation', 'http://www.ginfes.com.br/tipos_v03.xsd schemas_v301/tipos_v03.xsd
   http://www.ginfes.com.br/servico_enviar_lote_rps_envio_v03.xsd schemas_v301/servico_enviar_lote_rps_envio_v03.xsd');
	
	$lote = $root->createElement('ns1:LoteRps');	

	$node = $root->createElement('ns2:NumeroLote', '8895');	
	$lote->appendChild($node);

	$node = $root->createElement('ns2:Cnpj', '16825779000114');
	$lote->appendChild($node);

	$node = $root->createElement('ns2:InscricaoMunicipal','20361301');
	$lote->appendChild($node);

	$node = $root->createElement('ns2:QuantidadeRps','1');
	$lote->appendChild($node);

	$lista = $root->createElement('ns2:ListaRps');
	
	$rps = $root->createElement('ns2:Rps');

	$infRps = $root->createElement('ns2:InfRps');

	$idRps = $root->createElement('ns2:IdentificacaoRps');

	$node = $root->createElement('ns2:Numero', '8895');
	$idRps->appendChild($node);	

	$node = $root->createElement('ns2:Serie', '0000');
	$idRps->appendChild($node);

	$node = $root->createElement('ns2:Tipo', '1');
	$idRps->appendChild($node);	

	$infRps->appendChild($idRps);

	$node = $root->createElement('ns2:DataEmissao', '2016-12-02T20:03:51');
	$infRps->appendChild($node);

	$node = $root->createElement('ns2:NaturezaOperacao', '1');
	$infRps->appendChild($node);

	$node = $root->createElement('ns2:OptanteSimplesNacional', '2');
	$infRps->appendChild($node);

	$node = $root->createElement('ns2:IncentivadorCultural', '2');
	$infRps->appendChild($node);
	
	$node = $root->createElement('ns2:Status', '1');
	$infRps->appendChild($node);

	$servico = $root->createElement('ns2:Servico');

	$valores = $root->createElement('ns2:Valores');
	
	$node = $root->createElement('ns2:ValorServicos', '939.99');
	$valores->appendChild($node);

	$node = $root->createElement('ns2:IssRetido', '2');
	$valores->appendChild($node);	

	$servico->appendChild($valores);

	$node = $root->createElement('ns2:ItemListaServico', '7.13');
	$servico->appendChild($node);

	$node = $root->createElement('ns2:CodigoTributacaoMunicipio', '812220000');
	$servico->appendChild($node);

	$node = $root->createElement('ns2:Discriminacao', 'C.Bancaria  29/12/2016 R$ 313.33  28/01/2017 R$ 313.33  27/02/2017 R$ 313.33  TRIBUTOS APROXIMADOS R$ 159,7983');
	$servico->appendChild($node);

	$node = $root->createElement('ns2:CodigoMunicipio', '1302603');
	$servico->appendChild($node);

	$infRps->appendChild($servico);

	$prestador = $root->createElement('ns2:Prestador');

	$node = $root->createElement('ns2:Cnpj','16825779000114');
	$prestador->appendChild($node);
	$infRps->appendChild($prestador);

	$tomador = $root->createElement('ns2:Tomador');

	$idTomador = $root->createElement('ns2:IdentificacaoTomador');
	
	$cpfcnpj = $root->createElement('ns2:CpfCnpj');

	$node = $root->createElement('ns2:Cnpj', '08675607000183');
	$cpfcnpj->appendChild($node);
	$idTomador->appendChild($cpfcnpj);	
	$tomador->appendChild($idTomador);

	$infRps->appendChild($tomador);

	$rps->appendChild($infRps);
	
	$lista->appendChild($rps);

	$lote->appendChild($lista);	

	$enviar->appendChild($lote);	

	$signature = $root->createElement('Signature');
	$signature->setAttribute('xmlns','http://www.w3.org/2000/09/xmldsig#');

	$signedInfo = $root->createElement('SignedInfo');

	$canonMethod = $root->createElement('CanonicalizationMethod');
	$canonMethod->setAttribute('Algorithm', 'http://www.w3.org/TR/2001/REC-xml-c14n-20010315');
	$signedInfo->appendChild($canonMethod);

	$signatureMethod = $root->createElement('SignatureMethod');
	$signatureMethod->setAttribute('Algorithm', 'http://www.w3.org/2000/09/xmldsig#rsa-sha1');
	$signedInfo->appendChild($signatureMethod);

	$reference = $root->createElement('Reference');
	$transforms = $root->createElement('Transforms');
	$transform1 = $root->createElement('Transform');
	$transform1->setAttribute('Algorithm', 'http://www.w3.org/TR/2001/REC-xml-c14n-20010315');
	$transform2 = $root->createElement('Transform');
	$transform2->setAttribute('Algorithm', 'http://www.w3.org/2000/09/xmldsig#enveloped-signature');

	$transforms->appendChild($transform1);
	$transforms->appendChild($transform2);

	$reference->appendChild($transforms);

	$digestMethod = $root->createElement('DigestMethod');
	$digestMethod->setAttribute('Algorithm', 'http://www.w3.org/2000/09/xmldsig#sha1');		

	$reference->appendChild($digestMethod);

	$digestValue = base64_encode(hash('sha1', $root->C14N(false, false, null, null), true));

	$digestValueNode = $root->createElement('DigestValue', $digestValue);
	$reference->appendChild($digestValueNode);

	$signedInfo->appendChild($reference);
	$dataSinedInfo = $signedInfo->C14N(false, false, null, null);

	if (! $cert_store = file_get_contents($path)){
		echo "Error: Não foi possível carregar o arquivo do certificado \n";				
		$message = "Erro ao carregar o certificado digital ";	
		return false;
	}		
		
	openssl_pkcs12_read($cert_store, $cert_info, $pass);
	
	//echo 'Lendo Dados do certificado para extrair a chave primaria <br>';			
	$privateKey = $cert_info['pkey'];
			

	$x509Certificate = preg_replace( "/[\n]/", '', 
		preg_replace( '/\-\-\-\-\-[A-Z]+ CERTIFICATE\-\-\-\-\-/', '', 
		    $cert_info['cert'] ) );					

	$signature->appendChild($signedInfo);
	
	$signatureValue = '';
	$pkeyId = openssl_get_privatekey(file_get_contents('certificado/privatekey.pem'));
	openssl_sign($dataSinedInfo, $signatureValue, $pkeyId);	
	$signatureValueNode = $root->createElement('SignatureValue', base64_encode($signatureValue));

	$signature->appendChild($signatureValueNode);

	$keyInfo = $root->createElement('KeyInfo');

	$X509Data = $root->createElement('X509Data');

	$X509CertificateNode = $root->createElement('X509Certificate', $x509Certificate);

	$X509Data->appendChild($X509CertificateNode);

	$keyInfo->appendChild($X509Data);

	$signature->appendChild($keyInfo);

	$enviar->appendChild($signature);

	// We insert the new element as root (child of the document)
	$root->appendChild($enviar);	

	echo $root->saveXML();
	
	file_put_contents('c:/Users/Mario Olinto/Desktop/nfse8895.xml', $root->saveXML());

	//$root->save('nf8895.xml');

	
	
	/*function validate($x509certdata){		
		$cert = openssl_x509_parse(openssl_x509_read($x509certdata));

		//echo 'Datas no formato Brasileiro <br>';
		$certsValidTo = date('Y-m-d', $cert['validTo_time_t']);
		$currentDate = date('Y-m-d');

		//echo 'Teste comparando datas <br>';
		$datetime1 = new DateTime($currentDate);
		$datetime2 = new DateTime($certsValidTo);
		$interval = $datetime1->diff($datetime2);
		$daysToExpire = $interval->format('%r%a') . '<br>';

		if ($daysToExpire > 0){
			//echo 'Certificado com a validade Ok!';
		} else {
			//echo 'Certificado vencido em '.date('d/m/Y', $cert['validTo_time_t']).' <br>'; 
		}

		return true;
	}*/