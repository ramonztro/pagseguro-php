# PagSeguro-PHP *(API não oficial)*

O PHP PagSeguro permite que você utilize a API de pagamentos, a API de notificações e 
a API de transações do PagSeguro com facilidade. 

## Guia Rápido

### Instalando

Instale a última versão com o comando:

```bash
$ composer require ramonztro/pagseguro-php
```

### Utilização

```php
<?php

use Ramonztro\PhpPagSeguro\PagSeguro;

//Cria objeto de comunicação com a API
//Mais sobre o token: https://pagseguro.uol.com.br/v2/guia-de-integracao/como-comecar.html#!configure-seu-token
$pagseguro = new PagSeguro('seu-email', 'seu-token');

//Define os parâmetros da requisição de checkout
//Veja mais em: https://pagseguro.uol.com.br/v2/guia-de-integracao/api-de-pagamentos.html
$parametros = array(
	'currency' => 'BRL',
	'itemQuantity1' => '1',
	'itemId1' => "id do produto",
	'itemDescription1' => "título/descrição do produto",
	'itemAmount1' => "100.00"
);

//Requisita um código de checkout
$checkoutCode = $pagSeguro->checkoutCode($parametros);

```

## Sobre

### Requisitos

- PHP 5.3 ou mais recente.

### Licença (MIT License)

*Permission is hereby granted, free of charge, to any person obtaining a copy * 
of this software and associated documentation files (the "Software"), to    
deal in the Software without restriction, including without limitation the  
rights to use, copy, modify, merge, publish, distribute, sublicense, and/or 
sell copies of the Software, and to permit persons to whom the Software is  
furnished to do so, subject to the following conditions:*                    
                                                                            
*The above copyright notice and this permission notice shall be included in  
all copies or substantial portions of the Software.*                         
                                                                            
*THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR  
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,    
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE 
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER      
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING     
FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS * 
IN THE SOFTWARE.*

### Autor

Ramon Kayo - <contato@ramonk.com>
