<?php

/*
 * Payu Latam Payment Method
 * @author Alexandre <alexandre@madeinweb.com.br>
 * 
 * http://docs.payulatam.com/pt-br/integracao-com-api/o-que-voce-precisa-saber-a-integracao-via-api/
 * http://docs.payulatam.com/pt-br/integracao-com-api/se-voce-utiliza-outra-linguagem-2/brasil/
 * http://docs.payulatam.com/pt-br/integracao-com-api/informacao-adicional/tabela-de-variaveis-complementares/
 * 
 */

class ControllerPaymentPayU extends Controller {

    private $error = array();

    public function index() {
        $this->language->load('payment/payu');

        $this->data['text_credit_card'] = $this->language->get('text_credit_card');
        $this->data['text_ticket'] = $this->language->get('text_ticket');

        $this->data['required_field'] = $this->language->get('required_field');
        $this->data['invalid_number'] = $this->language->get('invalid_number');
        $this->data['invalid_doc'] = $this->language->get('invalid_doc');
        $this->data['invalid_cvv'] = $this->language->get('invalid_cvv');
        $this->data['required_field_month_year_format'] = $this->language->get('required_field_month_year_format');

        $this->data['entry_cc_flag'] = $this->language->get('entry_cc_flag');
        $this->data['entry_cc_name'] = $this->language->get('entry_cc_name');
        $this->data['entry_cc_doc'] = $this->language->get('entry_cc_doc');
        $this->data['entry_cc_number'] = $this->language->get('entry_cc_number');
        $this->data['entry_cc_expirationDate'] = $this->language->get('entry_cc_expirationDate');
        $this->data['entry_cc_securityCode'] = $this->language->get('entry_cc_securityCode');
        $this->data['entry_cc_installments'] = $this->language->get('entry_cc_installments');
        $this->data['securityCode_tooltip_link'] = $this->language->get('securityCode_tooltip_link');
        $this->data['securityCode_tooltip_text'] = $this->language->get('securityCode_tooltip_text');

        $this->data['empty_flag'] = $this->language->get('empty_flag');
        $this->data['payu_title'] = $this->language->get('payu_title');
        $this->data['waiting_payment_return'] = $this->language->get('waiting_payment_return');

        $this->data['button_confirm'] = $this->language->get('button_confirm');

        $this->data['cc_maxInstallments'] = $this->config->get('payu_maxInstallments');;
        $this->data['flags'] = array(
            array('text' => 'Visa', 'value' => 'VISA'),
            array('text' => 'MasterCard', 'value' => 'MASTERCARD'),
            array('text' => 'American Express', 'value' => 'AMEX'),
            array('text' => 'Diners Club', 'value' => 'DINERS'),
            array('text' => 'Elo', 'value' => 'ELO')
        );

        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/payu.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/payment/payu.tpl';
        } else {
            $this->template = 'lexus_superstore/template/payment/payu.tpl';
        }

        $this->render();
    }

    /**
     * Função de preparação e validação dos dados para a requisição do PayU;
     */
    public function send() {

        $json = array();

        $this->language->load('payment/payu');
        $this->load->helper('payu');
        $this->load->model('checkout/order');
        $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
        $this->load->model('account/order');
        $this->load->model('payment/payu');
        $order_products = $this->model_account_order->getOrderProducts($this->session->data['order_id']);

//        print_r($order_info);
//        echo "\n";
//        print_r($order_products);
//        echo "\n\n";

//        print_r($this->request->post);
        // extrai do array $_POST as variaveis, criando-as de acordo com seus indices.
        extract($this->request->post, EXTR_SKIP);

        $fullname = $order_info['payment_firstname'].' '.$order_info['payment_lastname'];
        $languageCode = substr($order_info['language_code'], 0, 2);
        $orderId = $this->session->data['order_id'];
        $referenceCode = date('His') . '-' . $orderId;

        $accountId = $this->config->get('payu_accountId');
        $total = number_format($order_info['total'], 2, '.', '');
        $currency = $order_info['currency_code'];

        $cc_doc = preg_replace('/\D/', '', $cc_doc);

        $description = $this->config->get('config_name')." - ".$this->language->get('payu_order_on_description').": ".$referenceCode."\n";
        $description .= $this->language->get("model_qty_subtotal")."\n";
        foreach ($order_products as $product){
            $description .= $product['model']." - ".$product['quantity']." - ".number_format($product['total'], 2, '.', '')."\n";
        }
        $description .= $this->language->get("total").": ".$total;
        if(strlen($description) > 254){
            $description = substr($description, 0, 251)."...";
        };

        $signature = md5($this->config->get('payu_apiKey') . '~' . $this->config->get('payu_merchantId') . '~' . $referenceCode . '~' . (int) $total . '~' . $currency);

        // $method vem do extrac do POST
        switch ($method) {
            case 'boleto':
                // TODO Boleto
                break;

            case 'cc':
                $additionalData = array(
                    "transaction" => array(
                        "order" => array(
                            "accountId" => $accountId,
                            "referenceCode" => $referenceCode,
                            "description" => $description,
                            "language" => $languageCode,
                            "signature" => $signature,
                            "buyer" => array(
                                "fullName" => $fullname,
                                "emailAddress" => $order_info['email'],
                                "dniNumber" => isset($order_info['tax_id']) && $order_info['tax_id'] != '' ? $order_info['tax_id'] : $cc_doc,
                                "shippingAddress" => array(
                                    "street1" => $order_info['payment_address_1'],
                                    "city" => $order_info['payment_city'],
                                    "state" => $order_info['payment_zone_code'],
                                    "country" => $order_info['payment_iso_code_2'],
                                    "postalCode" => $order_info['payment_postcode'],
                                    "phone" => maskPhone($order_info['ddd'] . $order_info['telephone'])
                                )
                            ),
                            "additionalValues" => array(
                                "TX_VALUE" => array(
                                    "value" => (int) $total,
                                    "currency" => $currency
                                )
                            ),
                            "partnerId" => "509434" // id da MadeinWeb
                        ),
                        "creditCard" => array(
                            "number" => $cc_number,
                            "securityCode" => $cc_securityCode,
                            "expirationDate" => $cc_expirationYear.'/'.$cc_expirationMonth,
                            "name" => $cc_name
                        ),
                        "type" => "AUTHORIZATION_AND_CAPTURE",
                        "paymentMethod" => $cc_flag,
                        "paymentCountry" => $order_info['payment_iso_code_2'],
                        "payer" => array(
                            "fullName" => $fullname,
                            "dniNumber" => $cc_doc,
                            "contactPhone" => maskPhone($order_info['ddd'] . $order_info['telephone']),
                            "emailAddress" => $order_info['email']
                        ),
                        "ipAddress" => $_SERVER['REMOTE_ADDR'],
                        "extraParameters" => array(
                            "INSTALLMENTS_NUMBER" => $cc_installments
                        )
                    )
                );
                break;

            default:
                $json['error'] = $this->language->get('method_not_existent');
                $this->response->setOutput(json_encode($json));
                exit;
        }

//        print_r($additionalData);
//        exit;

        $response = $this->payu_request('SUBMIT_TRANSACTION', $additionalData);

        $json['response'] = $response;
        if ($response['error'] == null) {

            if($response['transactionResponse']['state'] !== 'APPROVED'){
                // então a transação falhou, 
                $json['error'] = $this->language->get('transaction_error').' : '.$response['transactionResponse']['state'];
//                $this->model_checkout_order->update($this->session->data['order_id'], $this->config->get('payu_rejected_status'), $message, false);

            }else{

                // confirma o pedido
                $this->model_checkout_order->confirm($orderId, $this->config->get('config_order_status_id'));

                // define o conteudo de retorno
//                $json['response'] = $response;

                // adiciona no bd o referenceCode do pedido
                $this->model_payment_payu->addOrder(array('order_id' => $orderId, 'reference_code' => $referenceCode));

                // da um update no pedido, incluindo alguns dados na mensagem
                $message = array();
                $message['referenceCode'] = $referenceCode;
                foreach($response['transactionResponse'] as $responseKey => $responseItem){
                    if($responseItem != null){
                        $message[$responseKey] = $responseItem;
                    }
                }
                $message = json_encode($message, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

                $this->model_checkout_order->update($orderId, $this->config->get('payu_processed_status'), $message, false);
                $this->model_checkout_order->update($orderId, $this->config->get('payu_approved_status'), '', true);

                // url a que será redirecionado ao completar o pedido com sucesso.
                $json['success'] = $this->url->link('checkout/success');
            }

        } else {
            // retorna um erro que será exibido na caixinha vermelha
            $json['error'] = $response['error'];
        }
//        print_r($json);
//        exit;
        $this->response->setOutput(json_encode($json));
    }

    /**
     * Função de requisição a api do payu.
     *
     * em caso de erro na requisição, retorna um array contendo:
     * 'error'  => 'mensagem de erro';
     * 'info'   => curl_getinfo();
     *
     * @param string $command
     * @param array $additionalData
     * @return array
     */
    protected function payu_request($command = 'PING', $additionalData = array())
    {
//        var_dump($this->session)."\n\n";

        $data = array(
            'test'      => (int) $this->config->get('payu_test'),
            'language'  => substr($this->session->data['language'], 0, 2),
            'command'   => $command,
            'merchant'  => array(
                'apiLogin'  => $this->config->get('payu_apiLogin'),
                'apiKey'    => $this->config->get('payu_apiKey')
            )
        );

        if($this->config->get('payu_test')){
            $signature = md5('676k86ks53la6tni6clgd30jf6~500365~' . $additionalData['transaction']['order']['referenceCode'] . '~' . $additionalData['transaction']['order']['additionalValues']['TX_VALUE']['value'] . '~' . $additionalData['transaction']['order']['additionalValues']['TX_VALUE']['currency']);
            $testData = array(
                "transaction" => array(
                    "order" => array(
                        "accountId" => '500719',
                        "signature" => $signature
                    )
                ),
                'merchant'  => array(
                    'apiLogin'  => '403ba744e9827f3',
                    'apiKey'    => '676k86ks53la6tni6clgd30jf6'
                )
            );
            $additionalData = array_replace_recursive($additionalData, $testData);
        }

        $data = array_replace_recursive($data, $additionalData);

        $jData = json_encode($data, JSON_UNESCAPED_SLASHES);
        //echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)."\n\n";

        $headers = array(
            'Content-Type: application/json',
            'Accept: application/json',
            'Content-Length: '.strlen($jData)
        );

//        exit;

        if(!$data['test']){
            $ch = curl_init('https://api.payulatam.com/payments-api/4.0/service.cgi');
        }else{
            $ch = curl_init('https://stg.api.payulatam.com/payments-api/4.0/service.cgi');
        }

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $result = curl_exec($ch);
        $info = curl_getinfo($ch);

        if($info['http_code'] != 200){
            return array('error' => $this->language->get('payu_request_error').$info['http_code'], 'info' => $info);
        }elseif($this->config->get('payu_test') && $additionalData['transaction']['creditCard']['securityCode'] == '123'){
            return json_decode('{
                "code": "SUCCESS",
                "error": null,
                "transactionResponse": {
                    "orderId": "'.$additionalData['transaction']['order']['referenceCode'].'",
                    "transactionId": "d9ecd200-05ee-4a36-a0e4-0475478b73ee",
                    "state": "APPROVED",
                    "paymentNetworkResponseCode": null,
                    "paymentNetworkResponseErrorMessage": null,
                    "trazabilityCode": null,
                    "authorizationCode": null,
                    "pendingReason": null,
                    "responseCode": "APPROVED",
                    "errorCode": null,
                    "responseMessage": null,
                    "transactionDate": null,
                    "transactionTime": null,
                    "operationDate": "1314012556",
                    "extraParameters": null
                }
            }', true);
        }else{
            return json_decode($result, true);
        }
    }
}

?>