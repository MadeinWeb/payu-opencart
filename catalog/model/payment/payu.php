<?php
/*
* Payu Latam Payment Method
* @author Alexandre <alexandre@madeinweb.com.br>
*/
class ModelPaymentPayu extends Model
{
    public function getMethod($address, $total)
    {
        $this->load->language('payment/payu');
        $status = true;

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$this->config->get('payu_geo_zone_id') . "' AND country_id = '" . (int)$address['country_id'] . "' AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')");

        if ($this->config->get('payu_total') > $total) {
            $status = false;
        } elseif (!$this->config->get('payu_geo_zone_id')) {
            $status = true;
        } elseif ($query->num_rows) {
            $status = true;
        } else {
            $status = false;
        }

        $method_data = array();
        if ($status) {
            $method_data = array(
                'code' => 'payu',
                'title' => $this->language->get('text_title'),
                'sort_order' => $this->config->get('payu_sort_order')
            );
        }

        return $method_data;
    }

    public function addOrder($order_data) {
        /**
         * 1 to 1 relationship with order table (extends order info)
         */

        $this->db->query("INSERT INTO `" . DB_PREFIX . "payu_order_reference_code` SET
			`order_id` = '".(int)$order_data['order_id']."',
			`reference_code` = '".$order_data['reference_code']."',
			`created` = NOW()
        ;");

        return $this->db->getLastId();
    }

    public function setReferenceId() {
        $this->db->query("INSERT payu_order_reference_code");

    }
}