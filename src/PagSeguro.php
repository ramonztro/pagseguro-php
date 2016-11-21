<?php
/*
+---------------------------------------------------------------------------+
| PagSeguroPhp                                                              |
| Copyright (c) 2013-2016, Ramon Kayo                                       |
+---------------------------------------------------------------------------+
| Author        : Ramon Kayo                                                |
| Email         : contato@ramonkayo.com                                     |
| License       : Distributed under the MIT License                         |
| Full license  : https://github.com/ramonztro/php-pagseguro/               |
+---------------------------------------------------------------------------+
| "Simplicity is the ultimate sophistication." - Leonardo Da Vinci          |
+---------------------------------------------------------------------------+
*/
namespace Ramonztro\PagSeguroPhp;

use \Exception;

class PagSeguro {
	
	const 
		UrlCheckout = 'https://ws.pagseguro.uol.com.br/v2/checkout',
		UrlTransactions = 'https://ws.pagseguro.uol.com.br/v3/transactions',
		UrlNotifications = 'https://ws.pagseguro.uol.com.br/v3/transactions/notifications';
	
	private
		$email = null,
		$token = null;
	
	public function __construct($email, $token) {
		if (!is_string($email)) throw new Exception('Email inválido.');
		$email =  strtolower(trim($email));
		$regex = '~^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$~';
		if (!preg_match($regex, $email)) throw new Exception('Email inválido.');
		
		$this->email = $email;
		
		if (!is_string($token)) throw new Exception('Token inválido.');
		$regex = '~^[a-zA-Z0-9]{32}$~';
		if (!preg_match($regex, $token)) throw new Exception('Token inválido.');
		
		$this->token = $token;
		
		libxml_use_internal_errors(true);
	}
	
	public function checkoutCode($parametros, $url = null) {
		$xml = simplexml_load_string($this->checkout($parametros, $url));
		if (!$xml)
			throw new Exception('Problema ao ler arquivo XML.');
		if (!is_null($xml->error[0]))
			throw new Exception("{$xml->error[0]->message[0]} ({$xml->error[0]->code[0]})");
		return $xml->code[0]->__toString();
	}
	
	
	public function checkout($parametros, $url = null) {
		$url = is_null($url) ? self::UrlCheckout : $url;
		
		$parametrosPadrao['email'] = $this->email;
		$parametrosPadrao['token'] = $this->token;
		$parametros = array_merge($parametrosPadrao, $parametros);
		
		$postFields = '';
		foreach($parametros as $chave => $valor) $postFields .= "$chave=$valor&";
		$postFields = rtrim($postFields, '&');
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded; charset=ISO-8859-1'));
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, count($parametros));
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
		$resposta = curl_exec($ch);
		curl_close($ch);
		return $resposta;
	}
	
	public function transactionStatus($transaction, $url = null) {
		$xml = simplexml_load_string($this->transaction($transaction, $url));
		if (!$xml)
			throw new Exception('Problema ao ler arquivo XML.');
		if (!is_null($xml->error[0]))
			throw new Exception("{$xml->error[0]->message[0]} ({$xml->error[0]->code[0]})");
		return $xml->status[0]->__toString();
	}
	
	public function transaction($transaction, $url = null) {
		$url = is_null($url) ? self::UrlTransactions : $url;
		$url .= '/' . $transaction;
		$url .= "?email={$this->email}";
		$url .= "&token={$this->token}";
	
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded; charset=ISO-8859-1'));
		curl_setopt($ch, CURLOPT_URL, $url);
		$resposta = curl_exec($ch);
		curl_close($ch);
	
		return $resposta;
	}

	public function notificationStatus($notification, $url = null) {
		$xml = simplexml_load_string($this->notification($notification, $url));
		if (!$xml)
			throw new Exception('Problema ao ler arquivo XML.');
		if (!is_null($xml->error[0]))
			throw new Exception("{$xml->error[0]->message[0]} ({$xml->error[0]->code[0]})");
		return $xml->status[0]->__toString();
	}
	
	public function notification($notification, $url = null) {
		$url = is_null($url) ? self::UrlNotifications : $url;
		$url .= $notification;
		$url .= "?email={$this->email}";
		$url .= "&token={$this->token}";
	
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded; charset=ISO-8859-1'));
		curl_setopt($ch, CURLOPT_URL, $url);
		$resposta = curl_exec($ch);
		curl_close($ch);
	
		return $resposta;
	}
	
	public function statusSignificado($codigo) {
		switch ($codigo) {
			case 1:	return "Aguardando pagamento: o comprador iniciou a transação, mas até o momento o PagSeguro não recebeu nenhuma informação sobre o pagamento.";
			case 2:	return "Em análise: o comprador optou por pagar com um cartão de crédito e o PagSeguro está analisando o risco da transação.";
			case 3:	return "Paga: a transação foi paga pelo comprador e o PagSeguro já recebeu uma confirmação da instituição financeira responsável pelo processamento.";
			case 4:	return "Disponível: a transação foi paga e chegou ao final de seu prazo de liberação sem ter sido retornada e sem que haja nenhuma disputa aberta.";
			case 5:	return "Em disputa: o comprador, dentro do prazo de liberação da transação, abriu uma disputa.";
			case 6:	return "Devolvida: o valor da transação foi devolvido para o comprador.";
			case 7:	return "Cancelada: a transação foi cancelada sem ter sido finalizada.";
			case 8:	return "Chargeback debitado: o valor da transação foi devolvido para o comprador.";
			case 9:	return "Em contestação: o comprador abriu uma solicitação de chargeback junto à operadora do cartão de crédito.";
		}
	}
	
}