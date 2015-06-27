<?php
class ModelPaymentPayu extends Model {

    public function install() {
        $this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "payu_order_reference_code` (
			  `payu_order_reference_code_id` int(11) NOT NULL AUTO_INCREMENT,
			  `order_id` int(11) NOT NULL,
			  `reference_code` VARCHAR(255) NOT NULL,
			  `created` DATETIME NOT NULL,
			  PRIMARY KEY (`payu_order_reference_code_id`)
			);");
    }

    public function uninstall() {
//        $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "payu_order_reference_code`;");
    }
}