<?php
/*
* Payu Latam Payment Method
* @author Alexandre <alexandre@madeinweb.com.br>
*/
class ControllerPaymentPayU extends Controller
{
    private $error = array();

    //Config page
    public function index()
    {
        $this->load->language('payment/payu');
        $this->document->setTitle($this->language->get('heading_title'));
        $this->load->model('setting/setting');

        //new config
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->model_setting_setting->editSetting('openpayu', $this->request->post);
            $this->session->data['success'] = $this->language->get('text_success');
            $this->redirect($this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'));
        }

        //language data
        $this->data['heading_title'] = $this->language->get('heading_title');

        $this->data['text_enabled'] = $this->language->get('text_enabled');
        $this->data['text_disabled'] = $this->language->get('text_disabled');
        $this->data['text_all_zones'] = $this->language->get('text_all_zones');
        $this->data['text_yes'] = $this->language->get('text_yes');
        $this->data['text_no'] = $this->language->get('text_no');
        $this->data['entry_currency'] = $this->language->get('entry_currency');

        $this->data['entry_test'] = $this->language->get('entry_test');
        $this->data['entry_status'] = $this->language->get('entry_status');
        $this->data['entry_sort_order'] = $this->language->get('entry_sort_order');
        $this->data['entry_total'] = $this->language->get('entry_total');
        $this->data['entry_geo_zone'] = $this->language->get('entry_geo_zone');
        $this->data['entry_geo_zone_id'] = $this->language->get('entry_geo_zone_id');
        
        $this->data['entry_merchantId'] = $this->language->get('entry_merchantId');
        $this->data['entry_apiKey'] = $this->language->get('entry_apiKey');
        $this->data['entry_apiLogin'] = $this->language->get('entry_apiLogin');
        $this->data['entry_accountId'] = $this->language->get('entry_accountId');
        $this->data['entry_maxInstallments'] = $this->language->get('entry_maxInstallments');
        
        $this->data['entry_rejected_status'] = $this->language->get('entry_rejected_status');
        $this->data['entry_approved_status'] = $this->language->get('entry_approved_status');
        $this->data['entry_processed_status'] = $this->language->get('entry_processed_status');
        $this->data['entry_button'] = $this->language->get('entry_button');
        $this->data['button_save'] = $this->language->get('button_save');
        $this->data['button_cancel'] = $this->language->get('button_cancel');

        //error data
        if (isset($this->error['warning'])) {
            $this->data['error_warning'] = $this->error['warning'];
        } else {
            $this->data['error_warning'] = '';
        }
        if (isset($this->error['sort_order'])) {
            $this->data['error_sort_order'] = $this->error['sort_order'];
        } else {
            $this->data['error_sort_order'] = '';
        }
        if (isset($this->error['merchantId'])) {
            $this->data['error_merchantId'] = $this->error['merchantId'];
        } else {
            $this->data['error_merchantId'] = '';
        }
        if (isset($this->error['apiLogin'])) {
            $this->data['error_apiLogin'] = $this->error['apiLogin'];
        } else {
            $this->data['error_apiLogin'] = '';
        }
        if (isset($this->error['apiKey'])) {
            $this->data['error_apiKey'] = $this->error['apiKey'];
        } else {
            $this->data['error_apiKey'] = '';
        }
        if (isset($this->error['accountId'])) {
            $this->data['error_accountId'] = $this->error['accountId'];
        } else {
            $this->data['error_accountId'] = '';
        }
        if (isset($this->error['maxInstallments'])) {
            $this->data['error_maxInstallments'] = $this->error['maxInstallments'];
        } else {
            $this->data['error_maxInstallments'] = '';
        }

        //preloaded config
        if (isset($this->request->post['payu_test'])) {
            $this->data['payu_test'] = $this->request->post['payu_test'];
        } else {
            $this->data['payu_test'] = $this->config->get('payu_test');
        }
        if (isset($this->request->post['payu_status'])) {
            $this->data['payu_status'] = $this->request->post['payu_status'];
        } else {
            $this->data['payu_status'] = $this->config->get('payu_status');
        }
        if (isset($this->request->post['payu_merchantId'])) {
            $this->data['payu_merchantId'] = $this->request->post['payu_merchantId'];
        } else {
            $this->data['payu_merchantId'] = $this->config->get('payu_merchantId');
        }
        if (isset($this->request->post['payu_apiKey'])) {
            $this->data['payu_apiKey'] = $this->request->post['payu_apiKey'];
        } else {
            $this->data['payu_apiKey'] = $this->config->get('payu_apiKey');
        }
        if (isset($this->request->post['payu_apiLogin'])) {
            $this->data['payu_apiLogin'] = $this->request->post['payu_apiLogin'];
        } else {
            $this->data['payu_apiLogin'] = $this->config->get('payu_apiLogin');
        }
        if (isset($this->request->post['payu_accountId'])) {
            $this->data['payu_accountId'] = $this->request->post['payu_accountId'];
        } else {
            $this->data['payu_accountId'] = $this->config->get('payu_accountId');
        }
        if (isset($this->request->post['payu_maxInstallments'])) {
            $this->data['payu_maxInstallments'] = $this->request->post['payu_maxInstallments'];
        } else {
            $this->data['payu_maxInstallments'] = $this->config->get('payu_maxInstallments');
        }
        
        // Status
        // approved
        if (isset($this->request->post['payu_approved_status'])) {
            $this->data['payu_approved_status'] = $this->request->post['payu_approved_status'];
        } else {
            $this->data['payu_approved_status'] = $this->config->get('payu_approved_status');
        }
        // processed
        if (isset($this->request->post['payu_processed_status'])) {
            $this->data['payu_processed_status'] = $this->request->post['payu_processed_status'];
        } else {
            $this->data['payu_processed_status'] = $this->config->get('payu_processed_status');
        }
        // rejected
        if (isset($this->request->post['payu_rejected_status'])) {
            $this->data['payu_rejected_status'] = $this->request->post['payu_rejected_status'];
        } else {
            $this->data['payu_rejected_status'] = $this->config->get('payu_rejected_status');
        }

        if (isset($this->request->post['payu_sort_order'])) {
            $this->data['payu_sort_order'] = $this->request->post['payu_sort_order'];
        } else {
            $this->data['payu_sort_order'] = $this->config->get('payu_sort_order');
        }
        if (isset($this->request->post['payu_test'])) {
            $this->data['payu_test'] = $this->request->post['payu_test'];
        } else {
            $this->data['payu_test'] = $this->config->get('payu_test');
        }
        if (isset($this->request->post['payu_total'])) {
            $this->data['payu_total'] = $this->request->post['payu_total'];
        } else {
            $this->data['payu_total'] = $this->config->get('payu_total');
        }
        if (isset($this->request->post['payu_geo_zone_id'])) {
            $this->data['payu_geo_zone_id'] = $this->request->post['payu_geo_zone_id'];
        } else {
            $this->data['payu_geo_zone_id'] = $this->config->get('payu_geo_zone_id');
        }

        $this->data['breadcrumbs'] = array();
        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => false
        );
        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_payment'),
            'href' => $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => ' :: '
        );
        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('payment/payu', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => ' :: '
        );

        //links
        $this->data['action'] = $this->url->link('payment/payu', 'token=' . $this->session->data['token'], 'SSL');
        $this->data['cancel'] = $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL');

        $this->load->model('localisation/order_status');
        $this->data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();
        
        $this->load->model('localisation/geo_zone');
        $this->data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

        //template
        $this->template = 'payment/payu.tpl';
        $this->children = array(
            'common/header',
            'common/footer'
        );

        $this->response->setOutput($this->render());
    } //index


    //validate
    private function validate()
    {
        //permisions
        if (!$this->user->hasPermission('modify', 'payment/payu')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }
        //check for errors
        if (!$this->request->post['payu_merchantId']) {
            $this->error['merchantId'] = $this->language->get('error_merchantId');
        }
        if (!$this->request->post['payu_apiKey']) {
            $this->error['apiKey'] = $this->language->get('error_apiKey');
        }
        if (!$this->request->post['payu_apiLogin']) {
            $this->error['apiLogin'] = $this->language->get('error_apiLogin');
        }
        if (!$this->request->post['payu_accountId']) {
            $this->error['accountId'] = $this->language->get('error_accountId');
        }
        if ($this->request->post['payu_maxInstallments'] > 12 || $this->request->post['payu_maxInstallments'] < 1) {
            $this->error['maxInstallments'] = $this->language->get('error_maxInstallments');
        }
        if (!$this->request->post['payu_sort_order']) {
            $this->error['sort_order'] = $this->language->get('error_sort_order');
        }
        //if errors correct them
        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }

    public function install()
    {
        $this->load->model('payment/payu');
        $this->model_payment_payu->install();
    }

    public function uninstall()
    {
        $this->load->model('payment/payu');
        $this->model_payment_payu->uninstall();
    }


}