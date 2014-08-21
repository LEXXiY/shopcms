<?php
// ������ ���������� � ��������� �������� ������.������
// http://money.yandex.ru

/**
 * @connect_module_class_name CYandexMoney
 *
 */

class CYandexMoney extends PaymentModule {

        function _initVars(){

                $this->title                 = "������.������";
                $this->description         = "������ ���������� � ��������� �������� ������.������ (http://money.yandex.ru) �� ����� '������ ������ �� ����'";
                $this->sort_order         = 2;

                $this->Settings = array(
                                "CONF_PAYMENTMODULE_YANDEXMONEY_MERCHANT_ID",
                                "CONF_PAYMENTMODULE_YANDEXMONEY_MERCHANT_ADDRESS",
                                "CONF_PAYMENTMODULE_YANDEXMONEY_MERCHANT_EXCHANGERATE",
                                "CONF_PAYMENTMODULE_YANDEXMONEY_PAYMENTS_DESC"
                        );
        }

        function _initSettingFields(){

                $this->SettingsFields['CONF_PAYMENTMODULE_YANDEXMONEY_MERCHANT_ID'] = array(
                        'settings_value'                 => '',
                        'settings_title'                         => '����� ����� � ������.������',
                        'settings_description'         => '������� ����� ������ ����� � ������� ������.������',
                        'settings_html_function'         => 'setting_TEXT_BOX(0,',
                        'sort_order'                         => 1,
                );
                $this->SettingsFields['CONF_PAYMENTMODULE_YANDEXMONEY_MERCHANT_ADDRESS'] = array(
                        'settings_value'                 => '',
                        'settings_title'                         => '����� �������� / ��������',
                        'settings_description'         => '� �������� ������ ����� ���� ������ IP, �������� ��� e-mail ����� (������������ ��� �������� ����������� �� ������)',
                        'settings_html_function'         => 'setting_TEXT_BOX(0,',
                        'sort_order'                         => 2,
                );
                $this->SettingsFields['CONF_PAYMENTMODULE_YANDEXMONEY_MERCHANT_EXCHANGERATE'] = array(
                        'settings_value'                 => '1',
                        'settings_title'                         => '���� �.�. �������� �� ��������� � ������ � ������� ������.������',
                        'settings_description'         => '������ � ������� ������.������<br>�������������� <u>� ������</u>',
                        'settings_html_function'         => 'setting_TEXT_BOX(1,',
                        'sort_order'                         => 3,
                );
                $this->SettingsFields['CONF_PAYMENTMODULE_YANDEXMONEY_PAYMENTS_DESC'] = array(
                        'settings_value'                 => '������ ������ �[orderID]',
                        'settings_title'                         => '���������� �������',
                        'settings_description'         => '������� �������� ��������. �� ������ ������������ ������ [orderID] - ��� ������������� ����� �������� �� ����� ������',
                        'settings_html_function'         => 'setting_TEXT_BOX(0,',
                        'sort_order'                         => 4,
                );
        }

        function after_processing_html( $orderID )
        {
                $order = ordGetOrder( $orderID );
                $order_amount = $order["order_amount"];

                $exhange_rate = (float)$this->_getSettingValue('CONF_PAYMENTMODULE_YANDEXMONEY_MERCHANT_EXCHANGERATE');
                if ( (float)$exhange_rate == 0 )
                        $exhange_rate = 1;

                $order_amount = $order_amount*((float)$this->_getSettingValue('CONF_PAYMENTMODULE_YANDEXMONEY_MERCHANT_EXCHANGERATE'));

                $res = "";
                $res .=
                        "<table width='100%'>\n".
                        "        <tr>\n".
                        "                <td align='center'>\n".
                        "                <form method=\"POST\" action=\"http://money.yandex.ru/select-wallet.xml\" id='payform'>\n".
                        "                <input type=hidden name=\"wbp_Version\" value=\"2\">\n".
                        "                <input type=\"hidden\" name=\"wbp_MessageType\" value=\"DirectPaymentIntoAccountRequest\">\n".
                        "                <input type=\"hidden\" name=\"wbp_ShopAddress\" value=\"".$this->_getSettingValue('CONF_PAYMENTMODULE_YANDEXMONEY_MERCHANT_ADDRESS')."\">\n".
                        "                <input type=\"hidden\" name=\"wbp_accountid\" value=\"".$this->_getSettingValue('CONF_PAYMENTMODULE_YANDEXMONEY_MERCHANT_ID')."\">\n".
                        "                <input type=\"hidden\" name=\"wbp_currencyamount\" value=\"643;".$order_amount."\">\n".
                        "                <input type=\"hidden\" name=\"wbp_shortdescription\" value=\"".str_replace("[orderID]",$orderID,$this->_getSettingValue('CONF_PAYMENTMODULE_YANDEXMONEY_PAYMENTS_DESC'))."\">\n".
                        "                <input type=\"hidden\" name=\"wbp_ShopErrorInfo\" value=\"\">\n".
                        "                <input type=\"hidden\" name=\"wbp_template_1\" value=\"\">\n".
                        "                <input type=\"hidden\" name=\"wbp_template_2\" value=\"\">\n".
                        "                <table cellspacing='0' cellpadding='0' class='fsttab'><tr><td><table cellspacing='0' cellpadding='0' class='sectb'><tr><td><a href='#' onclick='document.getElementById(\"payform\").submit(); return false'>".STRING_PAY_NOW."</a></td></tr></table></td></tr></table>\n".
                        "                </form>\n".
                        "                </td>\n".
                        "        </tr>\n".
                        "</table>";

                return $res;




        }
}
?>