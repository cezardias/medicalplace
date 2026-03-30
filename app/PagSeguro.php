<?php

namespace App;
use Carbon\Carbon;
use Log;

class PagSeguro
{

    private $url;
    private $email;
    private $token;

    public function __construct() {
        if (config('app.env') == "prod") {
            $this->url = config('pagseguro.prod_url');
            $this->email = config('pagseguro.prod_email');
            $this->token = config('pagseguro.prod_token');            
        } else {
            $this->url = config('pagseguro.url');
            $this->email = config('pagseguro.email');
            $this->token = config('pagseguro.token');
        }
    }

    public function getEnv() {
        return [
            'env' => config('app.env'),
            'url' => $this->url
        ];
    }

    public function cancel($reserva) {

        $valor_cancelamento = $reserva->valor_periodo * 100;
        $params = '{
            "amount": {
              "value": '.$valor_cancelamento.'
            }
        }';

        $curl = curl_init();
        curl_setopt_array($curl, array(

            CURLOPT_SSL_VERIFYPEER => false, // Somente localhost

            CURLOPT_URL => "{$this->url}/charges/".$reserva->codigo_transacao."/cancel",
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_HTTPHEADER => array(
                "Content-type: application/json",
                "Authorization: Bearer ".$this->token,
                "X-api-version: 1.0",
                "X-idempotency-key: "
            ),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS => $params
            )
        );

        $response = curl_exec($curl);
        $curl_info = curl_getinfo($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if (!empty($err)) {
            return array(
                'status' => false,
                'mensagem' => 'ERRO AO ENVIAR REQUISIÇÃO. ENTRE EM CONTATO COM O SUPORTE.'
            );
        }

$retorno = json_decode($response);

if (!empty($retorno)) {
    if (isset($retorno->amount->summary->refunded)) {
        if ($retorno->amount->summary->refunded == $valor_cancelamento) {
            return true;
                } 
            }   
        }
        return false;
    }

    public function charge($request, $valor, $cartao = null, $user = null) {

        $cvv = $request->get('cvv');
        $valor_cobranca = intval(round($valor * 100));
        
        // Ensure $user is an object
        if (is_numeric($user)) {
            $user = User::find($user);
        }
        
        $user_id = $user ? $user->id : 0;
        $reference = str_pad($user_id, 5, "0") . date('Ymd') . "_" . time();

        $card_data = [];
        if (!empty($cartao)) {
            $card_data = [
                "id" => $cartao->token,
                "security_code" => $cvv
            ];
        } else {
            $numero_cartao = \App\Helper\Funcoes::instance()->onlyNumbers($request->get('numero_cartao'));
            $validade = str_replace(' ', '', $request->get('validade'));
            if (strlen($validade) == 5) {
                $exp_data = Carbon::createFromFormat('m/y', $validade);
            } else {
                $exp_data = Carbon::createFromFormat('m/Y', $validade);
            }
            
            $card_data = [
                "number" => $numero_cartao,
                "exp_month" => $exp_data->format('m'),
                "exp_year" => $exp_data->format('Y'),
                "security_code" => $cvv,
                "holder" => [
                    "name" => $request->get('nome_titular')
                ]
            ];
        }

        $params = [
            "reference_id" => $reference,
            "description" => "Aluguel de sala para atendimento",
            "amount" => [
                "value" => $valor_cobranca,
                "currency" => "BRL"
            ],
            "payment_method" => [
                "type" => "CREDIT_CARD",
                "installments" => 1,
                "capture" => true,
                "card" => $card_data
            ]
        ];

        if ($user) {
            $cpf = preg_replace('/\D/', '', $user->cpf);
            $telefone = preg_replace('/\D/', '', $user->telefone);
            
            // Extract area code (first 2 digits) and number
            if (strlen($telefone) >= 10) {
                $area = substr($telefone, 0, 2);
                $number = substr($telefone, 2);

                $params["customer"] = [
                    "name" => trim($user->name . " " . $user->sobrenome),
                    "email" => $user->email,
                    "tax_id" => $cpf,
                    "phones" => [
                        [
                            "country" => "55",
                            "area" => $area,
                            "number" => $number,
                            "type" => "MOBILE"
                        ]
                    ]
                ];
            } else {
                $params["customer"] = [
                    "name" => trim($user->name . " " . $user->sobrenome),
                    "email" => $user->email,
                    "tax_id" => $cpf
                ];
            }
        }

        // Add holder info to card data for better approval (Recommended by PagBank)
        if (!empty($cartao) && $user) {
            $card_data = [
                "id" => $cartao->token,
                "security_code" => $cvv,
                "holder" => [
                    "name" => trim($user->name . " " . $user->sobrenome),
                    "tax_id" => preg_replace('/\D/', '', $user->cpf)
                ]
            ];
            $params["payment_method"]["card"] = $card_data;
        }

        $json_params = json_encode($params);

        \Log::info("PagSeguro Payload: ". $json_params);

        $curl = curl_init();
        curl_setopt_array($curl, array(

            CURLOPT_SSL_VERIFYPEER => false, // Somente localhost

            CURLOPT_TIMEOUT => 30,
            CURLOPT_URL => rtrim($this->url, '/') . "/charges",
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_HTTPHEADER => array(
                "Content-type: application/json",
                "Authorization: Bearer ".$this->token,
                "X-idempotency-key: " . uniqid()
            ),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS => $json_params
            )
        );

        $response = curl_exec($curl);
        $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $err = curl_error($curl);
        curl_close($curl);

        \Log::info("PagSeguro HTTP Code: " . $http_code);
        \Log::info("PagSeguro Response: ". $response);

        if (!empty($err)) {
            \Log::error("PagSeguro cURL Error: " . $err);
            return array(
                'status' => false,
                'mensagem' => 'ERRO NA COMUNICAÇÃO (TIMEOUT OU REDE). TENTE NOVAMENTE.'
            );
        }

        $retorno = json_decode($response);

        if (!empty($retorno)) {
            if (!empty($retorno->payment_response)) {
                if ($retorno->payment_response->code == '20000') {
                    return array(
                        'status' => true,
                        'retorno' => $retorno
                    );
                } else {
                    return array(
                        'status' => false,
                        'mensagem' => $retorno->payment_response->message
                    );
                }
            } else {
                $det_erro = "";
                if (!empty($retorno->error_messages)) {
                    foreach ($retorno->error_messages as $mess) {
                        $det_erro .= $mess->description . " ";
                    }
                }
                return array(
                    'status' => false,
                    'mensagem' => 'DADOS INVÁLIDOS: '.$det_erro
                );
            }
        } else {
            \Log::error("PagSeguro Invalid JSON Response. HTTP Code: " . $http_code . " Raw Response: " . $response);
            return array(
                'status' => false,
                'mensagem' => 'RESPOSTA INVÁLIDA DO PAGSEGURO.'
            );
        }
    }
}
