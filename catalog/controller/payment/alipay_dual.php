<?php
require_once("alipay_dual_service.class.php");
require_once("alipay_dual_notify.class.php");

/*  日志消息,把支付宝反馈的参数记录下来*/	
function  log_result($word) {
	
	$fp = fopen("../../../log_alipay_dual_" . strftime("%Y%m%d",time()) . ".txt","a");	
	flock($fp, LOCK_EX) ;
	fwrite($fp,$word."::Date：".strftime("%Y-%m-%d %H:%I:%S",time())."\t\n");
	flock($fp, LOCK_UN); 
	fclose($fp);
	
}

class ControllerPaymentAlipayDual extends Controller {
	protected function index() {
		// 为 alipay.tpl 准备数据
    	$this->data['button_confirm'] = $this->language->get('button_confirm');
		
		$this->load->library('encryption');
		
		$encryption = new Encryption($this->config->get('config_encryption'));
		
		$this->data['custom'] = $encryption->encrypt($this->session->data['order_id']);		

		// 获取订单数据
		$this->load->model('checkout/order');

		$order_id = $this->session->data['order_id'];
		
		$order_info = $this->model_checkout_order->getOrder($order_id);

		
		/*
		$this->data['business'] = $this->config->get('alipay_dual_seller_email');
		$this->data['item_name'] = html_entity_decode($this->config->get('config_store'), ENT_QUOTES, 'UTF-8');				
		$this->data['currency_code'] = $order_info['currency'];
		$this->data['tgw'] = $this->session->data['order_id'];
		$this->data['amount'] = $this->currency->format($order_info['total'], $order_info['currency'], $order_info['value'], FALSE);
		$this->data['total'] = $order_info['total'];
		$this->data['currency'] = $order_info['currency'];
		$this->data['value'] = $order_info['value'];
		$this->data['first_name'] = html_entity_decode($order_info['payment_firstname'], ENT_QUOTES, 'UTF-8');	
		$this->data['last_name'] = html_entity_decode($order_info['payment_lastname'], ENT_QUOTES, 'UTF-8');	
		$this->data['address1'] = html_entity_decode($order_info['payment_address_1'], ENT_QUOTES, 'UTF-8');	
		$this->data['address2'] = html_entity_decode($order_info['payment_address_2'], ENT_QUOTES, 'UTF-8');	
		$this->data['city'] = html_entity_decode($order_info['payment_city'], ENT_QUOTES, 'UTF-8');	
		$this->data['zip'] = html_entity_decode($order_info['payment_postcode'], ENT_QUOTES, 'UTF-8');	
		$this->data['country'] = $order_info['payment_iso_code_2'];
		$this->data['notify_url'] = $this->url->http('payment/alipay/callback');
		$this->data['email'] = $order_info['email'];
		$this->data['invoice'] = $this->session->data['order_id'] . ' - ' . html_entity_decode($order_info['payment_firstname'], ENT_QUOTES, 'UTF-8') . ' ' . html_entity_decode($order_info['payment_lastname'], ENT_QUOTES, 'UTF-8');
		$this->data['lc'] = $this->session->data['language'];
		*/

		// 计算提交地址
		$seller_email = $this->config->get('alipay_dual_seller_email');		// 商家邮箱
		$security_code = $this->config->get('alipay_dual_security_code');	//安全检验码
		$partner = $this->config->get('alipay_dual_partner');				//合作伙伴ID
		$currency_code = $this->config->get('alipay_dual_currency_code');				//人民币代号（CNY）
		$item_name = $this->config->get('config_store');
		$first_name = $order_info['payment_firstname'];	
		$last_name = $order_info['payment_lastname'];

		$total = $order_info['total'];
		if($currency_code == ''){
			$currency_code = 'CNY';
		}
		
		$currency_value = $this->currency->getValue($currency_code);
		$amount = $total * $currency_value;
		$amount = number_format($amount,2,'.','');
		
		$_input_charset = "utf-8";  //字符编码格式  目前支持 GBK 或 utf-8
		$sign_type      = "MD5";    //加密方式  系统默认(不要修改)
		$transport      = "http";  //访问模式,你可以根据自己的服务器是否支持ssl访问而选择http以及https访问模式(系统默认,不要修改)
		$notify_url     = HTTP_SERVER . 'catalog/controller/payment/alipay_dual_notify_url.php';
		$return_url		= HTTPS_SERVER . 'index.php?route=checkout/success';
		$show_url       = "";        //你网站商品的展示地址
		$out_trade_no = $order_id;
		$subject = $this->config->get('config_name') . ' - #' . $order_id;
		$body = $subject;       //商品描述，必填			
		$total_fee = $amount;

		//↓↓↓↓↓↓↓↓↓↓请在这里配置您的基本信息↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓
		//合作身份者id，以2088开头的16位纯数字
		$aliapy_config['partner']      = trim($partner);

		//安全检验码，以数字和字母组成的32位字符
		$aliapy_config['key']          = trim($security_code);

		//签约支付宝账号或卖家支付宝帐户
		$aliapy_config['seller_email'] = trim($seller_email);

		//页面跳转同步通知页面路径，要用 http://格式的完整路径，不允许加?id=123这类自定义参数
		//return_url的域名不能写成http://localhost/trade_create_by_buyer_php_utf8/return_url.php ，否则会导致return_url执行无效
		$aliapy_config['return_url']   = $return_url; //'http://127.0.0.1/trade_create_by_buyer_php_utf8/return_url.php';

		//服务器异步通知页面路径，要用 http://格式的完整路径，不允许加?id=123这类自定义参数
		$aliapy_config['notify_url']   = $notify_url; //'http://www.xxx.com/trade_create_by_buyer_php_utf8/notify_url.php';

		//↑↑↑↑↑↑↑↑↑↑请在这里配置您的基本信息↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑


		//签名方式 不需修改
		$aliapy_config['sign_type']    = 'MD5';

		//字符编码格式 目前支持 gbk 或 utf-8
		$aliapy_config['input_charset']= 'utf-8';

		//访问模式,根据自己的服务器是否支持ssl访问，若支持请选择https；若不支持请选择http
		$aliapy_config['transport']    = 'http';

		$logistics_fee		= "0.00";				//物流费用，即运费。
		$logistics_type		= "EXPRESS";			//物流类型，三个值可选：EXPRESS（快递）、POST（平邮）、EMS（EMS）
		$logistics_payment	= "SELLER_PAY";			//物流支付方式，两个值可选：SELLER_PAY（卖家承担运费）、BUYER_PAY（买家承担运费）

		$quantity			= "1";					//商品数量，建议默认为1，不改变值，把一次交易看成是一次下订单而非购买一件商品。

		//选填参数//

		//买家收货信息（推荐作为必填）
		//该功能作用在于买家已经在商户网站的下单流程中填过一次收货信息，而不需要买家在支付宝的付款流程中再次填写收货信息。
		//若要使用该功能，请至少保证receive_name、receive_address有值
		//收货信息格式请严格按照姓名、地址、邮编、电话、手机的格式填写
		$receive_name		= "收货人姓名";			//收货人姓名，如：张三
		$receive_address	= "收货人地址";			//收货人地址，如：XX省XXX市XXX区XXX路XXX小区XXX栋XXX单元XXX号
		$receive_zip		= "123456";				//收货人邮编，如：123456
		$receive_phone		= "0571-81234567";		//收货人电话号码，如：0571-81234567
		$receive_mobile		= "13312341234";		//收货人手机号码，如：13312341234

		//构造要请求的参数数组，无需改动
		$parameter = array(
				"service"		=> "trade_create_by_buyer",
				"payment_type"	=> "1",
				
				"partner"		=> trim($partner),
				"_input_charset"=> trim($_input_charset),
				"seller_email"	=> trim($seller_email),
				"return_url"	=> trim($return_url),
				"notify_url"	=> trim($notify_url),

				"out_trade_no"	=> $out_trade_no,
				"subject"		=> $subject,
				"body"			=> $body,
				"price"			=> $total_fee,
				"quantity"		=> $quantity,
				
				"logistics_fee"		=> $logistics_fee,
				"logistics_type"	=> $logistics_type,
				"logistics_payment"	=> $logistics_payment,
				
				"receive_name"		=> $receive_name,
				"receive_address"	=> $receive_address,
				"receive_zip"		=> $receive_zip,
				"receive_phone"		=> $receive_phone,
				"receive_mobile"	=> $receive_mobile,
				
				"show_url"		=> $show_url
		);				

		$alipayService = new AlipayDualService($aliapy_config);
		$html_text = $alipayService->trade_create_by_buyer($parameter);
		
		//生成表单提交HTML文本信息
		$alipaySubmit = new AlipaySubmit();
		//待请求参数数组
		$para = $alipaySubmit->buildRequestPara($parameter,$aliapy_config);

		$action="";

		$this->data['action'] = $action;
		$this->data['para'] = $para;
		$this->data['input_charset'] = $aliapy_config['input_charset'];
		$this->data['html_text'] = $html_text;
		$this->id = 'payment';

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/alipay_dual.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/payment/alipay_dual.tpl';
		} else {
			$this->template = 'default/template/payment/alipay_dual.tpl';
		}	
		
		
		$this->render();	
	}

	
	// 支付返回后的处理
	public function callback() {
		$oder_success = FALSE;

		// 获取商家信息
		$this->load->library('encryption');
		$seller_email = $this->config->get('alipay_dual_seller_email');		// 商家邮箱
		$security_code = $this->config->get('alipay_dual_security_code');	//安全检验码
		$partner = $this->config->get('alipay_dual_partner');				//合作伙伴ID
		$_input_charset = "utf-8"; //字符编码格式  目前支持 GBK 或 utf-8
		$sign_type = "MD5"; //加密方式  系统默认(不要修改)		
		$transport = 'http';//访问模式,你可以根据自己的服务器是否支持ssl访问而选择http以及https访问模式(系统默认,不要修改)
		log_result("callback start.");

		//↓↓↓↓↓↓↓↓↓↓请在这里配置您的基本信息↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓
		$notify_url     = HTTP_SERVER . 'catalog/controller/payment/alipay_dual_notify_url.php';
		$return_url		= HTTPS_SERVER . 'index.php?route=checkout/success';

		//合作身份者id，以2088开头的16位纯数字
		$aliapy_config['partner']      = trim($partner);

		//安全检验码，以数字和字母组成的32位字符
		$aliapy_config['key']          = trim($security_code);

		//签约支付宝账号或卖家支付宝帐户
		$aliapy_config['seller_email'] = trim($seller_email);

		//页面跳转同步通知页面路径，要用 http://格式的完整路径，不允许加?id=123这类自定义参数
		//return_url的域名不能写成http://localhost/trade_create_by_buyer_php_utf8/return_url.php ，否则会导致return_url执行无效
		$aliapy_config['return_url']   = $return_url; //'http://127.0.0.1/trade_create_by_buyer_php_utf8/return_url.php';

		//服务器异步通知页面路径，要用 http://格式的完整路径，不允许加?id=123这类自定义参数
		$aliapy_config['notify_url']   = $notify_url; //'http://www.xxx.com/trade_create_by_buyer_php_utf8/notify_url.php';

		//↑↑↑↑↑↑↑↑↑↑请在这里配置您的基本信息↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑


		//签名方式 不需修改
		$aliapy_config['sign_type']    = 'MD5';

		//字符编码格式 目前支持 gbk 或 utf-8
		$aliapy_config['input_charset']= 'utf-8';

		//访问模式,根据自己的服务器是否支持ssl访问，若支持请选择https；若不支持请选择http
		$aliapy_config['transport']    = 'http';

		//计算得出通知验证结果
		$alipayNotify = new AlipayDualNotify($aliapy_config);
		$verify_result = $alipayNotify->verifyNotify();

		if($verify_result) {//验证成功
			log_result('验证成功');
			/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			//请在这里加上商户的业务逻辑程序代
			
			//——请根据您的业务逻辑来编写程序（以下代码仅作参考）——
			//获取支付宝的通知返回参数，可参考技术文档中服务器异步通知参数列表
			$out_trade_no	= $_POST['out_trade_no'];	    //获取订单号
			$trade_no		= $_POST['trade_no'];	    	//获取支付宝交易号
			$total			= $_POST['price'];				//获取总价格

			//获取支付宝的反馈参数
			$order_id   = $out_trade_no;   //获取支付宝传递过来的订单号

			$this->load->model('checkout/order');
			
			// 获取订单ID
			$order_info = $this->model_checkout_order->getOrder($order_id);
		
			// 存储订单至系统数据库
			if ($order_info) {
				$order_status_id = $order_info["order_status_id"];

				$alipay_dual_order_status_id = $this->config->get('alipay_dual_order_status_id');
				$alipay_dual_wait_buyer_pay = $this->config->get('alipay_dual_wait_buyer_pay');
				$alipay_dual_wait_buyer_confirm = $this->config->get('alipay_dual_wait_buyer_confirm');
				$alipay_dual_trade_finished = $this->config->get('alipay_dual_trade_finished');
				$alipay_dual_wait_seller_send = $this->config->get('alipay_dual_wait_seller_send');

				if (1 > $order_status_id){
					log_result('order->confirm order_status_id=' . $order_status_id);
					$this->model_checkout_order->confirm($order_id, $alipay_dual_order_status_id);
				}

				log_result('order_id=' . $order_id . ' order_status_id=' . $order_status_id);

				// 避免处理已完成的订单
				if ($order_status_id != $alipay_dual_trade_finished) {

					if($_POST['trade_status'] == 'WAIT_BUYER_PAY') {
						log_result('==alipay_dual_wait_buyer_pay==');

						//该判断表示买家已在支付宝交易管理中产生了交易记录，但没有付款
					
						//判断该笔订单是否在商户网站中已经做过处理（可参考“集成教程”中“3.4返回数据处理”）
						//如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
						//如果有做过处理，不执行商户的业务程序
						if($order_status_id != $alipay_dual_trade_finished && $order_status_id != $alipay_dual_wait_buyer_confirm && $order_status_id != $alipay_dual_wait_seller_send){
							$this->model_checkout_order->update($order_id, $alipay_dual_wait_buyer_pay);							

							echo "success - alipay_dual_wait_buyer_pay";		//请不要修改或删除
							
							//调试用，写文本函数记录程序运行情况是否正常
							log_result('success - alipay_dual_wait_buyer_pay');
						}
					}
					else if($_POST['trade_status'] == 'WAIT_SELLER_SEND_GOODS') {
						log_result('==alipay_dual_wait_seller_send==');
						//该判断表示买家已在支付宝交易管理中产生了交易记录且付款成功，但卖家没有发货
					
						//判断该笔订单是否在商户网站中已经做过处理（可参考“集成教程”中“3.4返回数据处理”）
						//如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
						//如果有做过处理，不执行商户的业务程序

						if($order_status_id != $alipay_dual_trade_finished && $order_status_id != $alipay_dual_wait_buyer_confirm){
							$this->model_checkout_order->update($order_id, $alipay_dual_wait_seller_send);

							echo "success - alipay_dual_wait_seller_send";		//请不要修改或删除
						
							//调试用，写文本函数记录程序运行情况是否正常
							log_result('success - alipay_dual_wait_seller_send');
						}

						
					}
					else if($_POST['trade_status'] == 'WAIT_BUYER_CONFIRM_GOODS') {
						log_result('==alipay_dual_wait_buyer_confirm==');
						//该判断表示卖家已经发了货，但买家还没有做确认收货的操作
					
						//判断该笔订单是否在商户网站中已经做过处理（可参考“集成教程”中“3.4返回数据处理”）
						//如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
						//如果有做过处理，不执行商户的业务程序
							
						if($order_status_id != $alipay_dual_trade_finished){
							$this->model_checkout_order->update($order_id, $alipay_dual_wait_buyer_confirm);

							echo "success - alipay_dual_wait_buyer_confirm";		//请不要修改或删除
						
							//调试用，写文本函数记录程序运行情况是否正常
							log_result('success - alipay_dual_wait_buyer_confirm');
						}
						
					}
					else if($_POST['trade_status'] == 'TRADE_FINISHED') {
					//该判断表示买家已经确认收货，这笔交易完成
					
						//判断该笔订单是否在商户网站中已经做过处理（可参考“集成教程”中“3.4返回数据处理”）
							//如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
							//如果有做过处理，不执行商户的业务程序
							
						$this->model_checkout_order->update($order_id, $alipay_dual_trade_finished);

						echo "success - alipay_dual_trade_finished";		//请不要修改或删除
						
						//调试用，写文本函数记录程序运行情况是否正常
						log_result('success - alipay_dual_trade_finished');
					}
					else {
						//其他状态判断
						$this->model_checkout_order->update($order_id, $alipay_dual_order_status_id);

						echo "success";

						//调试用，写文本函数记录程序运行情况是否正常
						//logResult ("这里写入想要调试的代码变量值，或其他运行的结果记录");
					}
				}
			}
			//——请根据您的业务逻辑来编写程序（以上代码仅作参考）——
			
			/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		}
		else {
			//验证失败
			echo "fail";

			//调试用，写文本函数记录程序运行情况是否正常
			//logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
		}
	}
}
?>