<?php 
class ModelExtensionPaymentPaycore extends Model {
    public function getMethod($address, $total) {
        $this->load->language('extension/payment/paycore');

    if (($this->config->get('paycore_status')) && ($total) &&
        (!$this->config->get('paycore_minimal_order') || ($total >= (float)$this->config->get('paycore_minimal_order'))) &&
        (!$this->config->get('paycore_maximal_order') || ($total <= (float)$this->config->get('paycore_maximal_order')))) {
            $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int) $this->config->get('paycore_geo_zone_id') . "' AND country_id = '" . (int) $address['country_id'] . "' AND (zone_id = '" . (int) $address['zone_id'] . "' OR zone_id = '0')");

            if (!$this->config->get('paycore_geo_zone_id')) {
                $status = true;
            } elseif ($query->num_rows) {
                $status = true;
            } else {
                $status = false;
            }
        } else {
            $status = false;
        }

        $method_data = array();

        if ($status) {
            $title = $this->config->get('paycore_langdata');
            $server = isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1')) ? $this->config->get('config_ssl') : $this->config->get('config_url');

            $method_data = array(
                'code' => 'paycore',
                'title' => $title[$this->config->get('config_language_id')]['title'],
                'description' => sprintf($this->language->get('text_description'), $server),
                'terms'       => '',
                'sort_order' => $this->config->get('paycore_sort_order')
            );
        }
        return $method_data;
    }
}
?>