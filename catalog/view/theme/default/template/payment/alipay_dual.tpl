<?php
$alipay_gateway_new = 'https://mapi.alipay.com/gateway.do?';
$sHtml = "<form id='alipaysubmit' name='alipaysubmit' action='".$alipay_gateway_new."_input_charset=".trim(strtolower($input_charset))."' method='get'>";
while (list ($key, $val) = each ($para)) {
    $sHtml.= "<input type=\"hidden\" name=\"".$key."\" value=\"".$val."\"/>";
}
//submit按钮控件请不要含有name属性
$sHtml = $sHtml."<div class=\"buttons\"><div class=\"right\"><a onclick=\"$('#alipaysubmit').submit();\" class=\"button\"><span>" . $button_confirm . "</span></a></div></div></form>";

echo $sHtml;
?>