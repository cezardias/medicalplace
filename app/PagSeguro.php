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

    public function charge($request,$valor,$cartao = null,$user = null) {

        $cvv = $request->get('cvv');
        $valor_cobranca = $valor * 100;
        $reference = str_pad($user,5,"0").date('Ymd')."_".time();
        if (!empty($cartao)) {
            $card = '
                "id" : "'.$cartao->token.'",
                "securyty_code" : "'.$cvv.'"
            ';
        } else {
            $cartao = \App\Helper\Funcoes::instance()->onlyNumbers($request->get('numero_cartao'));
            $exp_data = Carbon::createFromFormat('m/y',$request->get('validade'));
            $exp_month = $exp_data->format('m');
            $exp_year = $exp_data->format('Y');
            $store = 0;
            if (!empty($request->get('gravar_cartao')))
                $store = 1;
            $titular = $request->get('nome_titular');
            /*$card = '
                "number": "'.$cartao.'",
                "exp_month": "'.$exp_month.'",
                "exp_year": "'.$exp_year.'",
                "security_code": "'.$cvv.'",
		"store": '.$store.',
                "holder": {
                    "name": "'.$titular.'"
                }
            ';
	     */
            $card = '
                "number": "'.$cartao.'",
                "exp_month": "'.$exp_month.'",
                "exp_year": "'.$exp_year.'",
                "security_code": "'.$cvv.'",
                "holder": {
                    "name": "'.$titular.'"
                }
            ';
        }

        $params = '
            {
                "reference_id": "'.$reference.'",
                "description": "Aluguel de sala para atendimento",
                "amount": {
                    "value": '.$valor_cobranca.',
                    "currency": "BRL"
                },
                "payment_method": {
                    "type": "CREDIT_CARD",
                    "installments": 1,
                    "capture": true,
                    "card": {
                        '.$card.'
                    }
                }
            }
        ';

        Log::info($params);

        $curl = curl_init();
        curl_setopt_array($curl, array(

            CURLOPT_SSL_VERIFYPEER => false, // Somente localhost

            CURLOPT_URL => "{$this->url}/charges",
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

        Log::info($response);
	    Log::info($this->url."/charges");

        if (!empty($err)) {
		Log::debug($err);
            return array(
                'status' => false,
                'mensagem' => 'ERRO AO ENVIAR REQUISIÇÃO. ENTRE EM CONTATO COM O SUPORTE.'
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
                        'mensagem' => $retorno->payment_response->message." (".$retorno->status.")"
                    );
                }
            } else {
                $det_erro = null;
                if (!empty($retorno->error_messages)) {
                    foreach ($retorno->error_messages as $mess) {
                        $det_erro .= $mess->parameter_name." ";
                    }
                }
                return array(
                    'status' => false,
                    'mensagem' => 'VERIFIQUE OS DADOS PREENCHIDOS E TENTE NOVAMENTE. ('.$det_erro.')'
                );
            }
        } else {
            return array(
                'status' => false,
                'mensagem' => 'VERIFIQUE OS DADOS PREENCHIDOS'
            );
        }
    }

}
