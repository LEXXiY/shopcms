<?php
/**
 * @connect_module_class_name CZpayment
 *
 */
// Zpayment method implementation

class CZpayment extends PaymentModule {

        function _initVars(){

                $this->title                 = "Z-payment";
                $this->description         = "Z-payment (www.z-payment.ru). ������ �������� � ������ �������������� ������. ���� ������ ����� ������������ ��� �������������� ������� �������� �������. ���������:<br>Result Url - http(s)://�����_��������/index.php?zpayment=yes (POST method)<br>Success Url - http(s)://�����_��������/index.php?transaction_result=success (POST method)<br>Fail Url - http(s)://�����_��������/index.php?transaction_result=failure (POST method)<br>������ ���������: �� �������� ��������������� ������ ����� ������� �� Result URL, �� �������� Merchant Key ���� Result URL ������������ ������������.<br><b>��������!</b> ������������� ������� �� Z-payment �������� � ������� �� ���������, � ������ 5 ����� ����� ������!";
                $this->sort_order         = 0;
                $this->Settings = array(
                                "CONF_ZP_LMI_PAYEE_PURSE",
                                "CONF_ZP_MERCHANT_KEY",
                                "CONF_ZP_PASSWORD",
                                "CONF_ZP_STATUS_AFTER_PAY",
                                "CONF_ZP_SHOPCURRENCY"
                        );
        }

        function _initSettingFields(){

                $this->SettingsFields['CONF_ZP_LMI_PAYEE_PURSE'] = array(
                        'settings_value'                 => '',
                        'settings_title'                         => '������������� ��������',
                        'settings_description'         => '����� ����� - ������������� �������� � ������� Z-PAYMENT Merchant. ����������� ������������� �������� ��� �������� ������ ��������.',
                        'settings_html_function'         => 'setting_TEXT_BOX(0,',
                        'sort_order'                         => 1,
                );
                $this->SettingsFields['CONF_ZP_MERCHANT_KEY'] = array(
                        'settings_value'                 => '',
                        'settings_title'                         => '��������� ���� Merchant Key',
                        'settings_description'         => '������ ��������, ����������� � ���������� �������, ���������� �������� ������ � �����������. ��� ������ ������������ ��� ��������� ���������� ������������� ����������� ����������. ���������� ������ �������� ������ ������� Z-PAYMENT Merchant � ��������!',
                        'settings_html_function'         => 'setting_TEXT_BOX(0,',
                        'sort_order'                         => 1,
                );
                $this->SettingsFields['CONF_ZP_PASSWORD'] = array(
                        'settings_value'                 => '',
                        'settings_title'                         => '������ ������������� ��������',
                        'settings_description'         => '',
                        'settings_html_function'         => 'setting_TEXT_BOX(0,',
                        'sort_order'                         => 1,
                );
                $this->SettingsFields['CONF_ZP_STATUS_AFTER_PAY'] = array(
                        'settings_value'                 => '',
                        'settings_title'                         => '������ ������ ����� ������',
                        'settings_description'         => '�������, ����� ������ ����������� ������ ����� ���������� �������. ������������� ���������� ��� �� ������, ��� ���������� � ���������� �������� � �������� ������� ������������ ������. ��� �������� �������� ���������� �������� ��������� ������.',
                        'settings_html_function'         => 'setting_ORDER_STATUS_SELECT(',
                        'sort_order'                         => 1,
                );
                $this->SettingsFields['CONF_ZP_SHOPCURRENCY'] = array(
                        'settings_value'                 => '',
                        'settings_title'                         => '������ ��������',
                        'settings_description'         => '�������� �� ������ ����� ������ ��������-�������� ������, ������� ������������� ��������� ���� ������ �������� Z-payment.',
                        'settings_html_function'         => 'setting_CURRENCY_SELECT(',
                        'sort_order'                         => 1,
                );
        }

        function after_processing_html( $orderID )
        {

                $order = ordGetOrder( $orderID );
		    
			if ( $this->_getSettingValue('CONF_ZP_SHOPCURRENCY') > 0 )
		    {
			    $exhange_curr = currGetCurrencyByID ( $this->_getSettingValue('CONF_ZP_SHOPCURRENCY') );
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
                $crc = md5($this->_getSettingValue('CONF_ZP_LMI_PAYEE_PURSE').$orderID.$order_amount.$this->_getSettingValue('CONF_ZP_PASSWORD'));
                $res = '<form id="pay_zpayment" name="pay_zpayment" method="post" action="https://z-payment.ru/merchant.php">'."\n".
                       '<input type="hidden" name="LMI_PAYEE_PURSE" value="'.$this->_getSettingValue('CONF_ZP_LMI_PAYEE_PURSE').'">'.
                       '<input type="hidden" name="LMI_PAYMENT_AMOUNT" value="'.$order_amount.'">'.
                       '<input type="hidden" name="LMI_PAYMENT_DESC" value="������ ����� #'.$orderID.'">'.
                       '<input type="hidden" name="LMI_PAYMENT_NO" value="'.$orderID.'">'.
                       '<input type="hidden" name="CLIENT_MAIL" value="'.$order["customer_email"].'">'.
                       '<input type="hidden" name="ZP_SIGN" value="'.$crc.'">'.
                       '</form><table cellspacing="0" cellpadding="0" class="fsttab"><tr><td><table cellspacing="0" cellpadding="0" class="sectb"><tr><td>'.
                       '<a href="#" onclick="document.getElementById(\'pay_zpayment\').submit(); return false">'.STRING_PAY_NOW.'</a></td></tr></table></td></tr></table>';

                return $res;

        }

        function after_payment_php( $orderID, $params){
                
				$res = '';
                $order = ordGetOrder( $orderID );
                $skey = $this->_getSettingValue('CONF_ZP_MERCHANT_KEY');
                $merch_bd = strtoupper($this->_getSettingValue('CONF_ZP_LMI_PAYEE_PURSE'));
		  
		    if ( $this->_getSettingValue('CONF_ZP_SHOPCURRENCY') > 0 )
		    {
			    $exhange_curr = currGetCurrencyByID ( $this->_getSettingValue('CONF_ZP_SHOPCURRENCY') );
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
                $crc = strtoupper(md5($merch_bd.$params["LMI_PAYMENT_AMOUNT"].$params["LMI_PAYMENT_NO"].$params["LMI_MODE"].
                $params["LMI_SYS_INVS_NO"].$params["LMI_SYS_TRANS_NO"].$params["LMI_SYS_TRANS_DATE"].$skey.$params["LMI_PAYER_PURSE"].$params["LMI_PAYER_WM"]));
                    if ($order_amount > 0 && $merch_bd == strtoupper($params["LMI_PAYEE_PURSE"]) && $OutSum_x == $order_amount && $crc == strtoupper($params["LMI_HASH"])) {
                        ostSetOrderStatusToOrder($order["orderID"],$this->_getSettingValue('CONF_ZP_STATUS_AFTER_PAY'));
                        $res = "YES";
                    }
                return $res;
        }


}
?>