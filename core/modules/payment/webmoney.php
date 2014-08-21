<?php
/**
 * @connect_module_class_name CWebMoney
 *
 */
// WebMoney method implementation
// see also
//                http://www.webmoney.ru
//                https://merchant.webmoney.ru/conf/guide.asp#properties

class CWebMoney extends PaymentModule {

        function _initVars(){

                $this->title                 = "WebMoney";
                $this->description         = "WebMoney Merchant Interface (www.webmoney.ru). ������ �������� � ������ �������������� ������. ���� ������ ����� ������������ ��� �������������� ������� �������� �������. ���������:<br>Result Url - http(s)://�����_��������/index.php?webmoney=yes (POST method)<br>Success Url - http(s)://�����_��������/index.php?transaction_result=success (POST method)<br>Fail Url - http(s)://�����_��������/index.php?transaction_result=failure (POST method)<br>������ ���������: ���������� ��������� � ��������������� �������, �� �������� Secret Key ���� Result URL ������������ ������������, �� ��������� ������������ URL ������������ � �����, ����� ������������ ����������� ������� - MD5.";
                $this->sort_order         = 0;
                $this->Settings = array(
                                "CONF_PAYMENTMODULE_WEBMONEY_MERCHANT_PURSE",
                                "CONF_PAYMENTMODULE_WEBMONEY_TESTMODE",
                                "CONF_PAYMENTMODULE_WEBMONEY_PAYMENTS_DESC",
                                "CONF_PAYMENTMODULE_WEBMONEY_SECRET_KEY",
                                "CONF_PAYMENTMODULE_WEBMONEY_MERCHANT_EXCHANGERATE",
                                "CONF_PAYMENTMODULE_WEBMONEY_STATUS_AFTER_PAY"
                        );
        }

        function _initSettingFields(){

                $this->SettingsFields['CONF_PAYMENTMODULE_WEBMONEY_MERCHANT_PURSE'] = array(
                        'settings_value'                 => '',
                        'settings_title'                         => '����� ��������, �� ������� ����� ����������� ������ � ����� ��������',
                        'settings_description'         => '������ - ����� � 12 ����',
                        'settings_html_function'         => 'setting_TEXT_BOX(0,',
                        'sort_order'                         => 1,
                );
                $this->SettingsFields['CONF_PAYMENTMODULE_WEBMONEY_TESTMODE'] = array(
                        'settings_value'                 => '',
                        'settings_title'                         => '�������� �����',
                        'settings_description'         => '����������� �������� ����� ��� �������� ������',
                        'settings_html_function'         => 'setting_CHECK_BOX(',
                        'sort_order'                         => 1,
                );
                $this->SettingsFields['CONF_PAYMENTMODULE_WEBMONEY_PAYMENTS_DESC'] = array(
                        'settings_value'                 => '������ ������ #[orderID]',
                        'settings_title'                         => '���������� ��������',
                        'settings_description'         => '������� �������� ��������. �� ������ ������������ ������ [orderID] - ��� ������������� ����� �������� �� ����� ������',
                        'settings_html_function'         => 'setting_TEXT_BOX(0,',
                        'sort_order'                         => 1,
                );
                $this->SettingsFields['CONF_PAYMENTMODULE_WEBMONEY_SECRET_KEY'] = array(
                        'settings_value'                 => '',
                        'settings_title'                         => '�������� Secret Key',
                        'settings_description'         => '������� ��������� ��� ������� ���������� � ����� Web Merchant Interface',
                        'settings_html_function'         => 'setting_TEXT_BOX(0,',
                        'sort_order'                         => 1,
                );
                $this->SettingsFields['CONF_PAYMENTMODULE_WEBMONEY_MERCHANT_EXCHANGERATE'] = array(
                        'settings_value'                 => '',
                        'settings_title'                         => '�������� ������ ��������, � ������� ����� ����������� ������ ��������',
                        'settings_description'         => '�������� �� ������ ����� ������ ��������-�������� ������, ������� ������������� ��������� ���� ������ �������� Webmoney.',
                        'settings_html_function'         => 'setting_CURRENCY_SELECT(',
                        'sort_order'                         => 1,
                );
                $this->SettingsFields['CONF_PAYMENTMODULE_WEBMONEY_STATUS_AFTER_PAY'] = array(
                        'settings_value'                 => '',
                        'settings_title'                         => '������ ������ ����� ������',
                        'settings_description'         => '�������, ����� ������ ����������� ������ ����� ���������� �������. ������������� ���������� ��� �� ������, ��� ���������� � ���������� �������� � �������� ������� ������������ ������. ��� �������� �������� ���������� �������� ��������� ������.',
                        'settings_html_function'         => 'setting_ORDER_STATUS_SELECT(',
                        'sort_order'                         => 1,
                );
        }

        function after_processing_html( $orderID )
        {
            $order = ordGetOrder( $orderID );

		    if ( $this->_getSettingValue('CONF_PAYMENTMODULE_WEBMONEY_MERCHANT_EXCHANGERATE') > 0 )
		    {
			    $exhange_curr = currGetCurrencyByID ( $this->_getSettingValue('CONF_PAYMENTMODULE_WEBMONEY_MERCHANT_EXCHANGERATE') );
			    $exhange_rate = $exhange_curr["currency_value"];
			    $exhange_round = $exhange_curr["roundval"]; 
			
		    }
	     	else
            {		
		        $exhange_rate = 1;
			    $exhange_round = 2;
            }
		
		    if ( (float)$exhange_rate == 0 ) $exhange_rate = 1;
			
		    $order_amount = _formatPrice(roundf($order["order_amount"]*$exhange_rate), $exhange_round, ".", "");

                $res = "";
                $res .=
                        "<table width='100%'>\n".
                        "        <tr>\n".
                        "                <td align='center'>\n".
                        "<form method='POST' action='https://merchant.webmoney.ru/lmi/payment.asp' id='payform'>\n".
                        "        <input type='hidden' name='LMI_PAYMENT_AMOUNT' value='".$order_amount."'>\n".
                        "        <input type='hidden' name='LMI_PAYMENT_DESC' value='".str_replace("[orderID]",$orderID,$this->_getSettingValue('CONF_PAYMENTMODULE_WEBMONEY_PAYMENTS_DESC'))."'>\n".
                        "        <input type='hidden' name='LMI_PAYMENT_NO' value='".$orderID."'>\n".
                        "        <input type='hidden' name='LMI_PAYEE_PURSE' value=".strtoupper($this->_getSettingValue('CONF_PAYMENTMODULE_WEBMONEY_MERCHANT_PURSE')).">\n".
                        "        <input type='hidden' name='LMI_MODE' value=".$this->_getSettingValue('CONF_PAYMENTMODULE_WEBMONEY_TESTMODE').">\n".
                        "        <table cellspacing='0' cellpadding='0' class='fsttab'><tr><td><table cellspacing='0' cellpadding='0' class='sectb'><tr><td><a href='#' onclick='document.getElementById(\"payform\").submit(); return false'>".STRING_PAY_NOW."</a></td></tr></table></td></tr></table>\n".
                        "</form>\n".
                        "                </td>\n".
                        "        </tr>\n".
                        "</table>";
                return $res;
        }

        function before_payment_php( $orderID, $OutSum, $merch)
		{

                $res = '';
                $order = ordGetOrder( $orderID );
                $merch_bd = strtoupper($this->_getSettingValue('CONF_PAYMENTMODULE_WEBMONEY_MERCHANT_PURSE'));
		  
		    if ( $this->_getSettingValue('CONF_PAYMENTMODULE_WEBMONEY_MERCHANT_EXCHANGERATE') > 0 )
		    {
			    $exhange_curr = currGetCurrencyByID ( $this->_getSettingValue('CONF_PAYMENTMODULE_WEBMONEY_MERCHANT_EXCHANGERATE') );
			    $exhange_rate = $exhange_curr["currency_value"];
			    $exhange_round = $exhange_curr["roundval"]; 
			
		    }
	     	else
            {		
		        $exhange_rate = 1;
			    $exhange_round = 2;
            }
		
		    if ( (float)$exhange_rate == 0 ) $exhange_rate = 1;
			
		        $order_amount = _formatPrice(roundf($order["order_amount"]*$exhange_rate), $exhange_round, ".", "");
                $OutSum_x = _formatPrice($OutSum, $exhange_round, ".", "");

                if ($order_amount > 0 && $merch_bd == strtoupper($merch) && $OutSum_x == $order_amount) $res = "YES";
                return $res;
        }

        function after_payment_php( $orderID, $params){

                $order = ordGetOrder( $orderID );
                $skey = $this->_getSettingValue('CONF_PAYMENTMODULE_WEBMONEY_SECRET_KEY');
                $merch_bd = strtoupper($this->_getSettingValue('CONF_PAYMENTMODULE_WEBMONEY_MERCHANT_PURSE'));
		 
		    if ( $this->_getSettingValue('CONF_PAYMENTMODULE_WEBMONEY_MERCHANT_EXCHANGERATE') > 0 )
		    {
			    $exhange_curr = currGetCurrencyByID ( $this->_getSettingValue('CONF_PAYMENTMODULE_WEBMONEY_MERCHANT_EXCHANGERATE') );
			    $exhange_rate = $exhange_curr["currency_value"];
			    $exhange_round = $exhange_curr["roundval"]; 
			
		    }
	     	else
            {		
		        $exhange_rate = 1;
			    $exhange_round = 2;
            }
		
		    if ( (float)$exhange_rate == 0 ) $exhange_rate = 1;
			
		        $order_amount = _formatPrice(roundf($order["order_amount"]*$exhange_rate), $exhange_round, ".", "");
                $OutSum_x = _formatPrice($params["LMI_PAYMENT_AMOUNT"], $exhange_round, ".", "");
                $crc = strtoupper(md5($merch_bd.$params["LMI_PAYMENT_AMOUNT"].$orderID.$params["LMI_MODE"].$params["LMI_SYS_INVS_NO"].$params["LMI_SYS_TRANS_NO"].$params["LMI_SYS_TRANS_DATE"].$skey.$params["LMI_PAYER_PURSE"].$params["LMI_PAYER_WM"]));

				if ($order_amount > 0 && $merch_bd == strtoupper($params["LMI_PAYEE_PURSE"]) && $OutSum_x == $order_amount && $crc == strtoupper($params["LMI_HASH"]))
                ostSetOrderStatusToOrder($order["orderID"],$this->_getSettingValue('CONF_PAYMENTMODULE_WEBMONEY_STATUS_AFTER_PAY'));

        }
}
?>