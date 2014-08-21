<?
/**
 * @connect_module_class_name CInvoiceJur
 *
 */

// ������ ������������ ������ �� ������ ��� ����������� ���

// ���� ������ ��������� ����������� ���� ��� ������.
// � ���������� ����� �������
// �����:
//        1.                � ����� ����� �� ���������� ������� �������, ������� ������������ � �����������������
//                        �������� � ������� "���������" - "������".
//                        ���� ������ ��������� ���������� ��� �� ���������� ������ ���, � ����� ����������,
//                        ������� ����� � ��������� ��� ���
//                        ������ ��� ����������� ��� ����� ������ � ����� �� ������� � ��������, ������� ��
//                        ����������� � ����������������� (����� ������� ���������������).
//        2.                ����� ������������ ������ � ������

define('CINVOICEJUR_DB_TABLE', DB_PRFX.'_module_payment_invoice_jur');

class CInvoiceJur extends PaymentModule {

        function _initVars(){

                $this->title                 = "����������� ������";
                $this->description         = "������ ������������ ������ �� ������ ��� ����������� ���";
                $this->sort_order         = 3;

                $this->Settings = array(
                                "CONF_PAYMENTMODULE_INVOICE_JUR_CURRENCY",
                                "CONF_PAYMENTMODULE_INVOICE_JUR_NDS",
                                "CONF_PAYMENTMODULE_INVOICE_JUR_NDS_IS_INCLUDED_IN_PRICE",
                                "CONF_PAYMENTMODULE_INVOICE_JUR_EMAIL_HTML_INVOICE",
                                "CONF_PAYMENTMODULE_INVOICE_JUR_COMPANYNAME",
                                "CONF_PAYMENTMODULE_INVOICE_JUR_COMPANYADDRESS",
                                "CONF_PAYMENTMODULE_INVOICE_JUR_COMPANYPHONE",
                                "CONF_PAYMENTMODULE_INVOICE_JUR_BANK_ACCOUNT_NUMBER",
                                "CONF_PAYMENTMODULE_INVOICE_JUR_INN",
                                "CONF_PAYMENTMODULE_INVOICE_JUR_KPP",
                                "CONF_PAYMENTMODULE_INVOICE_JUR_BANKNAME",
                                "CONF_PAYMENTMODULE_INVOICE_JUR_BANK_KOR_NUMBER",
                                "CONF_PAYMENTMODULE_INVOICE_JUR_BIK"
                        );
        }

        function _initSettingFields(){

                $this->SettingsFields['CONF_PAYMENTMODULE_INVOICE_JUR_CURRENCY'] = array(
                        'settings_value'                 => '0',
                        'settings_title'                         => '������ - �����',
                        'settings_description'         => '����� �� ������ ������������ � ������. �������� �� ������ ����� �������� �����. ��� ������������ ����� ����� �������������� �������� ����� �����. ���� ������ �� ����������, ����� ����������� ���� ��������� ������������� ������',
                        'settings_html_function'         => 'setting_CURRENCY_SELECT(',
                        'sort_order'                         => 1,
                );
                $this->SettingsFields['CONF_PAYMENTMODULE_INVOICE_JUR_NDS'] = array(
                        'settings_value'                 => '0',
                        'settings_title'                         => '������ ��� (%)',
                        'settings_description'         => '������� ������ ��� � ���������. ���� �� ��������� �� ���������� ������� ���������������, ������� 0',
                        'settings_html_function'         => 'setting_TEXT_BOX(0,',
                        'sort_order'                         => 1,
                );
                $this->SettingsFields['CONF_PAYMENTMODULE_INVOICE_JUR_NDS_IS_INCLUDED_IN_PRICE'] = array(
                        'settings_value'                 => '1',
                        'settings_title'                         => '��� ��� ������� � ��������� �������',
                        'settings_description'         => '�������� ��� �����, ���� ����� ��� ������� � ��������� ������� � ����� ��������. ���� �� ��� �� ������� � ��������� � ������ ������������ �������������, ��������� ��� �����',
                        'settings_html_function'         => 'setting_CHECK_BOX(',
                        'sort_order'                         => 1,
                );
                $this->SettingsFields['CONF_PAYMENTMODULE_INVOICE_JUR_EMAIL_HTML_INVOICE'] = array(
                        'settings_value'                 => '1',
                        'settings_title'                         => '���������� ���������� HTML-����',
                        'settings_description'         => '�������� ��� �����, ���� ������, ����� ���������� ������������� ����������� ���� � HTML-�������. ���� ����� ���������, �� ���������� ����� ���������� ������ �� ���� �� ����� ��������',
                        'settings_html_function'         => 'setting_CHECK_BOX(',
                        'sort_order'                         => 1,
                );
                $this->SettingsFields['CONF_PAYMENTMODULE_INVOICE_JUR_COMPANYNAME'] = array(
                        'settings_value'                 => '',
                        'settings_title'                         => '�������� ��������',
                        'settings_description'         => '������� �������� �����������, �� ����� ������� ������������ ����',
                        'settings_html_function'         => 'setting_TEXT_BOX(0,',
                        'sort_order'                         => 1,
                );
                $this->SettingsFields['CONF_PAYMENTMODULE_INVOICE_JUR_COMPANYADDRESS'] = array(
                        'settings_value'                 => '',
                        'settings_title'                         => '����� ��������',
                        'settings_description'         => '������� ����� �����������, �� ����� ������� ������������ ����',
                        'settings_html_function'         => 'setting_TEXT_BOX(0,',
                        'sort_order'                         => 1,
                );
                $this->SettingsFields['CONF_PAYMENTMODULE_INVOICE_JUR_COMPANYPHONE'] = array(
                        'settings_value'                 => '',
                        'settings_title'                         => '������� ��������',
                        'settings_description'         => '������� ������� �����������',
                        'settings_html_function'         => 'setting_TEXT_BOX(0,',
                        'sort_order'                         => 1,
                );
                $this->SettingsFields['CONF_PAYMENTMODULE_INVOICE_JUR_BANK_ACCOUNT_NUMBER'] = array(
                        'settings_value'                 => '',
                        'settings_title'                         => '��������� ����',
                        'settings_description'         => '����� ���������� ����� �����������',
                        'settings_html_function'         => 'setting_TEXT_BOX(0,',
                        'sort_order'                         => 1,
                );
                $this->SettingsFields['CONF_PAYMENTMODULE_INVOICE_JUR_INN'] = array(
                        'settings_value'                 => '',
                        'settings_title'                         => '���',
                        'settings_description'         => '��� �����������',
                        'settings_html_function'         => 'setting_TEXT_BOX(0,',
                        'sort_order'                         => 1,
                );
                $this->SettingsFields['CONF_PAYMENTMODULE_INVOICE_JUR_KPP'] = array(
                        'settings_value'                 => '',
                        'settings_title'                         => '���',
                        'settings_description'         => '',
                        'settings_html_function'         => 'setting_TEXT_BOX(0,',
                        'sort_order'                         => 1,
                );
                $this->SettingsFields['CONF_PAYMENTMODULE_INVOICE_JUR_BANKNAME'] = array(
                        'settings_value'                 => '',
                        'settings_title'                         => '������������ �����',
                        'settings_description'         => '',
                        'settings_html_function'         => 'setting_TEXT_BOX(0,',
                        'sort_order'                         => 1,
                );
                $this->SettingsFields['CONF_PAYMENTMODULE_INVOICE_JUR_BANK_KOR_NUMBER'] = array(
                        'settings_value'                 => '',
                        'settings_title'                         => '����������������� ����',
                        'settings_description'         => '',
                        'settings_html_function'         => 'setting_TEXT_BOX(0,',
                        'sort_order'                         => 1,
                );
                $this->SettingsFields['CONF_PAYMENTMODULE_INVOICE_JUR_BIK'] = array(
                        'settings_value'                 => '',
                        'settings_title'                         => '���',
                        'settings_description'         => '',
                        'settings_html_function'         => 'setting_TEXT_BOX(0,',
                        'sort_order'                         => 1,
                );


                //������� �������, � ������� ����� ���������� ���������� ��� �����
                // - ����� � ������ � ��������� ������

                if(!in_array(strtolower(CINVOICEJUR_DB_TABLE), db_get_all_tables())){

                        $sql = '
                                CREATE TABLE '.CINVOICEJUR_DB_TABLE.'
                                (module_id INT UNSIGNED, orderID INT, company_name varchar(64), company_inn varchar(64), nds_included int default 0, nds_rate float default 0, RUR_rate float default 1)
                        ';
                        db_query($sql);
                }
        }

        function payment_form_html()
        {
                $text = "";

                $text.= "
                        <table>
                        <tr><td>�������� �����������:</td><td><input type=text name=minvoicejur_company_name value=\"\"></td></tr>
                        <tr><td>���:</td><td><input type=text name=minvoicejur_inn value=\"\"></td></tr>
                        </table>
                ";

                return $text;
        }

        function payment_process($order)
        {
                //��������� ������������ �����

                if (!isset($_POST["minvoicejur_company_name"]) || strlen( trim($_POST["minvoicejur_company_name"]) ) == 0)
                {
                        return "����������, ������� �������� �����������, �� ��� ������� ����� ������������ ����";
                }

                if (!isset($_POST["minvoicejur_inn"]) || strlen( trim($_POST["minvoicejur_inn"]) ) == 0)
                {
                        return "����������, ������� ��� �����������, �� ��� ������� ����� ������������ ����";
                }

                return 1;
        }

        function after_processing_php( $orderID )
        {
                //��������� ����� �����
                $orderID = (int) $orderID;
                $order = ordGetOrder( $orderID );
                if ($order)
                {
                        $q = db_query("select count(*) from ".CINVOICEJUR_DB_TABLE."  where orderID=".(int)$orderID." AND module_id=".(int)$this->ModuleConfigID);
                        $row = db_fetch_row($q);
                        if ($row[0] > 0) //������� ��� ������ ������
                        {
                                db_query("delete from ".CINVOICEJUR_DB_TABLE." where orderID=".(int)$orderID." AND module_id=".(int)$this->ModuleConfigID);
                        }

                        $q = db_query("select currency_value from ".CURRENCY_TYPES_TABLE." where CID=".( (int)$this->_getSettingValue('CONF_PAYMENTMODULE_INVOICE_JUR_CURRENCY') ) );
                        $row = db_fetch_row($q);
                        $RUR_rate = $row ? (float)$row[0] : 1;

                        //�������� ����� ������
                        $sql = "insert into ".CINVOICEJUR_DB_TABLE." (module_id, orderID, company_name, company_inn, nds_included, nds_rate, RUR_rate) values (".(int)$this->ModuleConfigID.", ".(int)$orderID.", '".xToText(trim($_POST["minvoicejur_company_name"]))."', '".xToText(trim($_POST["minvoicejur_inn"]))."', '".$this->_getSettingValue('CONF_PAYMENTMODULE_INVOICE_JUR_NDS_IS_INCLUDED_IN_PRICE')."', ".(float)$this->_getSettingValue('CONF_PAYMENTMODULE_INVOICE_JUR_NDS').", ".$RUR_rate." )";
                        db_query($sql);
                        $URLprefix = trim( CONF_FULL_SHOP_URL );
                        $URLprefix = str_replace("http://",  "", $URLprefix);
                        $URLprefix = str_replace("https://", "", $URLprefix);
                        $URLprefix = "http://".$URLprefix;
                        if ($URLprefix[ strlen($URLprefix)-1 ] != '/')
                        {
                                $URLprefix .= "/";
                        }

                        //��������� ���� ���������� �� ����������� �����
                        if ($this->_getSettingValue('CONF_PAYMENTMODULE_INVOICE_JUR_EMAIL_HTML_INVOICE') == 1) //html
                        {

                                $mySmarty = new Smarty; //core smarty object
                                $mySmarty->template_dir = "core/modules/templates/";
                                //define smarty vars
                                $mySmarty->assign( "billing_lastname", $order["billing_lastname"] );
                                $mySmarty->assign( "billing_firstname", $order["billing_firstname"] );
                                $mySmarty->assign( "billing_city", $order["billing_city"] );
                                $mySmarty->assign( "billing_address", $order["billing_address"] );
                                $mySmarty->assign( "orderID", $orderID );
                                $mySmarty->assign( "order_time", $order["order_time_mysql"] );

                                //����� �����
                                $q = db_query("select company_name, company_inn, nds_included, nds_rate, RUR_rate from ".CINVOICEJUR_DB_TABLE." where orderID=".(int)$orderID." AND module_id=".(int)$this->ModuleConfigID);
                                $row = db_fetch_row($q);
                                if ($row) //����� ������� � ����� � ��������� �����
                                {
                                        $mySmarty->assign( "customer_companyname", $row["company_name"] );
                                        $mySmarty->assign( "customer_inn",  $row["company_inn"] );
                                        $nds_rate = (float) $row["nds_rate"];
                                        $RUR_rate = (float) $row["RUR_rate"];
                                        $nds_included = !strcmp((string)$row["nds_included"],"1") ? 1 : 0;
                                }
                                else //���������� � ���� �� �������
                                {
                                        die ("���� �� ������ � ���� ������");
                                }

                                //���������� ������
                                $order_content = ordGetOrderContent( $orderID );
                                $amount = 0;
                                foreach( $order_content as $key => $val)
                                {
                                        $order_content[$key]["Price"] = $this->_my_formatprice ( $order_content[$key]["Price"] * $RUR_rate );
                                        $order_content[$key]["Price_x_Quantity"] = $this->_my_formatprice ( $val["Quantity"] * $val["Price"] * $RUR_rate );
                                        $amount += (float) str_replace(",","",$order_content[$key]["Price_x_Quantity"]);
                                }

                                $shipping_rate = $order["shipping_cost"]*$RUR_rate;

                                $order["discount_value"] = round((float)$order["order_discount"] * $amount)/100;

                                $mySmarty->assign( "order_discount", $order["order_discount"] );
                                $mySmarty->assign( "order_discount_value", $this->_my_formatprice($order["discount_value"]) );

                                $amount += $shipping_rate; //+��������� ��������

                                $mySmarty->assign( "order_content", $order_content);
                                $mySmarty->assign( "order_content_items_count", count($order_content) + 1 );
                                $mySmarty->assign( "order_subtotal", $this->_my_formatprice($amount) );

                                $amount -= $order["discount_value"];

                                if ($nds_rate <= 0) //�������� ���
                                {
                                        $mySmarty->assign( "order_tax_amount", "���" );
                                        $mySmarty->assign( "order_tax_amount_string", "���" );
                                }
                                else
                                {
                                        //����� �� ������������� �� ��������� ��������
                                        //���� �� ������, ����� ����� ������������ � �� ��������� �������� �������� ����
                                        // '($amount-$shipping_rate)' �� '$amount'

                                        if (!$nds_included) //����� �������
                                        {
                                                $tax_amount = round ( ($amount-$shipping_rate) * $nds_rate ) / 100;

                                                $amount += $tax_amount;
                                        }
                                        else //��������� �����
                                        {
                                                $tax_amount = round ( 100 * ($amount-$shipping_rate) * $nds_rate / ($nds_rate+100) ) / 100;
                                        }
                                        $mySmarty->assign( "order_tax_amount", $this->_my_formatprice($tax_amount) );
                                        $mySmarty->assign( "order_tax_amount_string", $this->create_string_representation_of_a_number($tax_amount) );

                                }

                                $mySmarty->assign( "order_total", $this->_my_formatprice($amount) );
                                $mySmarty->assign( "order_total_string", $this->create_string_representation_of_a_number($amount) );

                                //��������
                                if ($shipping_rate > 0)
                                {
                                        $mySmarty->assign( "shipping_type", $order["shipping_type"] );
                                        $mySmarty->assign( "shipping_rate", $this->_my_formatprice($shipping_rate) );
                                }

                                $mySmarty->assign("shopping_cart_url", $URLprefix); //���� � ����� ��������
                                $mySmarty->assign('InvoiceModule', $this);

                                $invoice = $mySmarty->fetch("invoice_jur.tpl.html");

                                //��������� ���� ����������
                                xMailTxtHTMLDATA($order["customer_email"], "���� �� ������", $invoice);

                                // xMailTxtHTMLDATA(CONF_GENERAL_EMAIL, "���� �� ������ - ����� #".$orderID, $invoice);

                        }
                        else //������ �� ����
                        {

                                $invoice_url = $URLprefix . "index.php?do=invoice_jur&moduleID=".(int)$this->ModuleConfigID."&orderID=".(int)$orderID."&order_time=".base64_encode( $order["order_time_mysql"] ) . "&customer_email=" . base64_encode( $order["customer_email"] );

                                xMailTxtHTMLDATA($order["customer_email"], "���� �� ������", "������������!<br><br>������� �� ��� �����.<br>���� �� ������ �� ������ ���������� � ����������� �� ������:<br><a href=\"" . $invoice_url . "\">" . $invoice_url . "</a><br><br>� ���������,<br>".CONF_SHOP_NAME);

                        }

                }

                return "";
        }

        function after_processing_html( $orderID )
        {
                //������� ���� �� ������
                $order = ordGetOrder( $orderID );

                if(!$this->ModuleConfigID){

                        $sql = '
                                SELECT module_id FROM '.MODULES_TABLE.' WHERE module_name="'.xEscSQL($this->title).'"
                        ';
                        @list($this->ModuleConfigID) = db_fetch_row(db_query($sql));
                }

                $res = "";

                $res .=
                        "<script>\n".
                        "        open_window('index.php?do=invoice_jur&moduleID=".(int)$this->ModuleConfigID."&orderID=".(int)$orderID."&order_time=".base64_encode( $order["order_time_mysql"] )."&customer_email=".base64_encode( $order["customer_email"] )."',700,600);\n".
                        "</script>\n";

                return $res;
        }

        function uninstall($_ModuleConfigID = 0){

                PaymentModule::uninstall($_ModuleConfigID);

                if(!count(modGetModuleConfigs(get_class($this)))){

                        //������� ������� � ����������� � ������
                        db_query("DROP TABLE IF EXISTS ".CINVOICEJUR_DB_TABLE);
                }else {

                        $sql = 'DELETE FROM '.CINVOICEJUR_DB_TABLE.' WHERE module_id='.(int)$this->ModuleConfigID;
                }
        }

        //��������� ������� ��������� ������� �� invoice_jur.php, �������� � ����� core/includes/processor/. ������� ����������, ���� ������� ������� �� �������� �� � ��������� ����.

        function _my_formatPrice($price)
        {
                return _formatPrice(roundf($price));
        }

        function number2string($n,$rod) //��������� ����� $n � ������. ����� ����������� ������ ���� 0 < $n < 1000. $rod ��������� �� ��� �������� (0 - �������, 1 - �������; ��������, "�����" - 1, "������" - 0).
        {
                $a = floor($n / 100);
                $b = floor(($n - $a*100) / 10);
                $c = $n % 10;

                $s = "";
                switch($a)
                {
                        case 1: $s = "���";
                        break;
                        case 2: $s = "������";
                        break;
                        case 3: $s = "������";
                        break;
                        case 4: $s = "���������";
                        break;
                        case 5: $s = "�������";
                        break;
                        case 6: $s = "��������";
                        break;
                        case 7: $s = "�������";
                        break;
                        case 8: $s = "���������";
                        break;
                        case 9: $s = "���������";
                        break;
                }
                $s .= " ";
                if ($b != 1)
                {
                   switch($b)
                   {
                        case 1: $s .= "������";
                        break;
                        case 2: $s .= "��������";
                        break;
                        case 3: $s .= "��������";
                        break;
                        case 4: $s .= "�����";
                        break;
                        case 5: $s .= "���������";
                        break;
                        case 6: $s .= "����������";
                        break;
                        case 7: $s .= "���������";
                        break;
                        case 8: $s .= "�����������";
                        break;
                        case 9: $s .= "���������";
                        break;
                   }
                   $s .= " ";
                   switch($c)
                   {
                        case 1: $s .= $rod ? "����" : "����";
                        break;
                        case 2: $s .= $rod ? "���" : "���";
                        break;
                        case 3: $s .= "���";
                        break;
                        case 4: $s .= "������";
                        break;
                        case 5: $s .= "����";
                        break;
                        case 6: $s .= "�����";
                        break;
                        case 7: $s .= "����";
                        break;
                        case 8: $s .= "������";
                        break;
                        case 9: $s .= "������";
                        break;
                   }
                }
                else //...�����
                {
                   switch($c)
                   {
                        case 0: $s .= "������";
                        break;
                        case 1: $s .= "�����������";
                        break;
                        case 2: $s .= "����������";
                        break;
                        case 3: $s .= "����������";
                        break;
                        case 4: $s .= "������������";
                        break;
                        case 5: $s .= "�����������";
                        break;
                        case 6: $s .= "������������";
                        break;
                        case 7: $s .= "�����������";
                        break;
                        case 8: $s .= "�������������";
                        break;
                        case 9: $s .= "�������������";
                        break;
                   }
                }
                return $s;
        }

        function create_string_representation_of_a_number( $n )
                // ������� ��������� ������������� �����. �������� $n = 123.
                // ��������� ����� "��� �������� ��� ����� 00 ������"
        {
                //��������� ����� �� �������: �������, ������, ��������, ��������� (������ ���������� �� ��������� :) )

                $billions = floor($n / 1000000000);
                $millions = floor( ($n-$billions*1000000000) / 1000000);
                $grands = floor( ($n-$billions*1000000000-$millions*1000000) / 1000);
                $roubles = floor( ($n-$billions*1000000000-$millions*1000000-$grands*1000) );//$n % 1000;

                //�������
                $kop = round ( $n*100 - round( floor($n)*100 ) );
                if ($kop < 10) $kop = "0".(string)$kop;

                $s = "";
                if ($billions > 0)
                {
                        $t = "��";
                        $temp = $billions % 10;
                        if (floor(($billions % 100)/10) != 1)
                        {
                                if ($temp == 1) $t = "";
                                else if ($temp >=2 && $temp <= 4) $t = "�";
                        }
                        $s .= $this->number2string($billions,1)." ��������$t ";
                }
                if ($millions > 0)
                {
                        $t = "��";
                        $temp = $millions % 10;
                        if (floor(($millions % 100)/10) != 1)
                        {
                                if ($temp == 1) $t = "";
                                else if ($temp >=2 && $temp <= 4) $t = "�";
                        }
                        $s .= $this->number2string($millions,1)." �������$t ";
                }
                if ($grands > 0)
                {
                        $t = "";
                        $temp = $grands % 10;
                        if (floor(($grands % 100)/10) != 1)
                        {
                                if ($temp == 1) $t = "�";
                                else if ($temp >=2 && $temp <= 4) $t = "�";
                        }
                        $s .= $this->number2string($grands,0)." �����$t ";
                }
                if ($roubles > 0)
                {
                        $rub = "��";
                        $temp = $roubles % 10;
                        if (floor(($roubles % 100)/10) != 1)
                        {
                                if ($temp == 1) $rub = "�";
                                else if ($temp >=2 && $temp <= 4) $rub = "�";
                        }
                        $s .=  $this->number2string($roubles,1)." ����$rub ";
                }

                {
                        $kp = "��";
                        $temp = $kop % 10;
                        if (floor(($kop % 100)/10) != 1)
                        {
                                if ($temp == 1) $kp = "���";
                                else if ($temp >=2 && $temp <= 4) $kp = "���";
                        }

                        $s .= "$kop ����$kp";
                }

                //������ ������� ������ ����� ���������
                if ($roubles>0 || $grands>0 || $millions>0 || $billions>0)
                {
                        $cnt=0; while($s[$cnt]==" ") $cnt++;
                        $s[$cnt] = chr( ord($s[$cnt])- 32 );
                }

                return $s;
        }
}
?>