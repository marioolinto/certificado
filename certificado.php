<?php

class Certificado{
	
	private $path;
	private $pass;
	private $privateKey;
	private $x509Certificate;
	private $Message;
	
	public function __construct($path, $pass){
		$this->path = $path;
		$this->pass = $pass;
	}


	public function load(){
		if (! $cert_store = file_get_contents($this->path)){
			echo "Error: Não foi possível carregar o arquivo do certificado \n";				
			$message = "Erro ao carregar o certificado digital ";	
			return false;
		}		
				
		if (openssl_pkcs12_read($cert_store, $cert_info, $this->pass)){						
			echo 'Lendo Dados do certificado para extrair a chave primaria <br>';			
			$this->privatekey = $cert_info['pkey'];
			if (!file_put_contents('certificado/privatekey.pem', $this->privatekey)){
				echo "Erro ao criar o arquivo privatekey.pem";
				return false;
			}

			if (!$this->validate($cert_info['cert'])){
				echo "Erro na validação do certificado";
				return false;
			}

			$this->x509Certificate = preg_replace( "/[\n]/", '', 
                preg_replace( '/\-\-\-\-\-[A-Z]+ CERTIFICATE\-\-\-\-\-/', '', 
                        $cert_info['cert'] ) );

			if (!file_put_contents('certificado/cert.pem', $this->x509Certificate)){
				echo "Erro ao criar o arquivo cert.pem";
			}			
		} else {
			echo "Não foi possível ler o certificado <br>";
			return false;
		}
	}

	public function validate($x509certdata){		
		$cert = openssl_x509_parse(openssl_x509_read($x509certdata));

		echo 'Datas no formato Brasileiro <br>';
		$certsValidTo = date('Y-m-d', $cert['validTo_time_t']);
		$currentDate = date('Y-m-d');

		echo 'Teste comparando datas <br>';
		$datetime1 = new DateTime($currentDate);
		$datetime2 = new DateTime($certsValidTo);
		$interval = $datetime1->diff($datetime2);
		$daysToExpire = $interval->format('%r%a') . '<br>';

		if ($daysToExpire > 0){
			echo 'Certificado com a validade Ok!';
		} else {
			echo 'Certificado vencido em '.date('d/m/Y', $cert['validTo_time_t']).' <br>'; 
		}

		return true;
	}

}


//$certificado = new Certificado("certificado/16825779000114.pfx", "1234");
//$certificado->load();
