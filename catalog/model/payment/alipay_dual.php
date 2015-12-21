<?php 
class ModelPaymentAlipayDual extends Model {
  	public function getMethod($address) {
		$this->load->language('payment/alipay_dual');
		
		if ($this->config->get('alipay_dual_status')) {
      		$status = TRUE;
      	} else {
			$status = FALSE;
		}
		
		$method_data = array();
	
		if ($status) {  
      		$method_data = array( 
        		'code'         => 'alipay_dual',
        		'title'      => $this->language->get('text_title'),
				'sort_order' => $this->config->get('alipay_dual_sort_order')
      		);
    	}
	
    	return $method_data;
  	}
}
?>