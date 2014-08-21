<?php
  #####################################
  # ShopCMS: ������ ��������-��������
  # Copyright (c) by ADGroup
  # http://shopcms.ru
  #####################################

  function _my_formatPrice($price)
  {
      return _formatPrice(roundf($price));
  }

  function number2string($n, $rod) //��������� ����� $n � ������. ����� ����������� ������ ���� 0 < $n < 1000. $rod ��������� �� ��� �������� (0 - �������, 1 - �������; ��������, "�����" - 1, "������" - 0).

  {
      $n = round($n);
      $a = floor($n / 100);
      $b = floor(($n - $a * 100) / 10);
      $c = $n % 10;

      $s = "";
      switch ($a)
      {
          case 1:
              $s = "���";
              break;
          case 2:
              $s = "������";
              break;
          case 3:
              $s = "������";
              break;
          case 4:
              $s = "���������";
              break;
          case 5:
              $s = "�������";
              break;
          case 6:
              $s = "��������";
              break;
          case 7:
              $s = "�������";
              break;
          case 8:
              $s = "���������";
              break;
          case 9:
              $s = "���������";
              break;
      }
      $s .= " ";
      if ($b != 1)
      {
          switch ($b)
          {
              case 1:
                  $s .= "������";
                  break;
              case 2:
                  $s .= "��������";
                  break;
              case 3:
                  $s .= "��������";
                  break;
              case 4:
                  $s .= "�����";
                  break;
              case 5:
                  $s .= "���������";
                  break;
              case 6:
                  $s .= "����������";
                  break;
              case 7:
                  $s .= "���������";
                  break;
              case 8:
                  $s .= "�����������";
                  break;
              case 9:
                  $s .= "���������";
                  break;
          }
          $s .= " ";
          switch ($c)
          {
              case 1:
                  $s .= $rod ? "����":
                  "����";
                  break;
              case 2:
                  $s .= $rod ? "���":
                  "���";
                  break;
              case 3:
                  $s .= "���";
                  break;
              case 4:
                  $s .= "������";
                  break;
              case 5:
                  $s .= "����";
                  break;
              case 6:
                  $s .= "�����";
                  break;
              case 7:
                  $s .= "����";
                  break;
              case 8:
                  $s .= "������";
                  break;
              case 9:
                  $s .= "������";
                  break;
          }
      }
      else //...�����

      {
          switch ($c)
          {
              case 0:
                  $s .= "������";
                  break;
              case 1:
                  $s .= "�����������";
                  break;
              case 2:
                  $s .= "����������";
                  break;
              case 3:
                  $s .= "����������";
                  break;
              case 4:
                  $s .= "������������";
                  break;
              case 5:
                  $s .= "�����������";
                  break;
              case 6:
                  $s .= "������������";
                  break;
              case 7:
                  $s .= "�����������";
                  break;
              case 8:
                  $s .= "�������������";
                  break;
              case 9:
                  $s .= "�������������";
                  break;
          }
      }
      return $s;
  }

  function create_string_representation_of_a_number($n) // ������� ��������� ������������� �����. �������� $n = 123.
      // ��������� ����� "��� �������� ��� ����� 00 ������"

  {
      //��������� ����� �� �������: �������, ������, ��������, ��������� (������ ���������� �� ��������� :) )

      $billions = floor($n / 1000000000);
      $millions = floor(($n - $billions * 1000000000) / 1000000);
      $grands = floor(($n - $billions * 1000000000 - $millions * 1000000) / 1000);
      $roubles = floor(($n - $billions * 1000000000 - $millions * 1000000 - $grands * 1000)); //$n % 1000;

      //�������
      $kop = round($n * 100 - round(floor($n) * 100));
      if ($kop < 10) $kop = "0".(string )$kop;

      $s = "";
      if ($billions > 0)
      {
          $t = "��";
          $temp = $billions % 10;
          if (floor(($billions % 100) / 10) != 1)
          {
              if ($temp == 1) $t = "";
              else
                  if ($temp >= 2 && $temp <= 4) $t = "�";
          }
          $s .= number2string($billions, 1)." ��������".$t." ";
      }
      if ($millions > 0)
      {
          $t = "��";
          $temp = $millions % 10;
          if (floor(($millions % 100) / 10) != 1)
          {
              if ($temp == 1) $t = "";
              else
                  if ($temp >= 2 && $temp <= 4) $t = "�";
          }
          $s .= number2string($millions, 1)." �������".$t." ";
      }
      if ($grands > 0)
      {
          $t = "";
          $temp = $grands % 10;
          if (floor(($grands % 100) / 10) != 1)
          {
              if ($temp == 1) $t = "�";
              else
                  if ($temp >= 2 && $temp <= 4) $t = "�";
          }
          $s .= number2string($grands, 0)." �����".$t." ";
      }
      if ($roubles > 0)
      {
          $rub = "��";
          $temp = $roubles % 10;
          if (floor(($roubles % 100) / 10) != 1)
          {
              if ($temp == 1) $rub = "�";
              else
                  if ($temp >= 2 && $temp <= 4) $rub = "�";
          }
          $s .= number2string($roubles, 1)." ����".$rub." ";
      }
      {
          $kp = "��";
          $temp = $kop % 10;
          if (floor(($kop % 100) / 10) != 1)
          {
              if ($temp == 1) $kp = "���";
              else
                  if ($temp >= 2 && $temp <= 4) $kp = "���";
          }

          $s .= $kop." ����".$kp;
      }

      //������ ������� ������ ����� ���������
      if ($roubles > 0 || $grands > 0 || $millions > 0 || $billions > 0)
      {
          $cnt = 0;
          while ($s[$cnt] == " ") $cnt++;
          $s[$cnt] = chr(ord($s[$cnt]) - 32);
      }

      return $s;
  }

  //init Smarty
  require ("core/smarty/smarty.class.php");
  $smarty = new Smarty; //core smarty object
  $smarty_mail = new Smarty; //for e-mails

  if ((int)CONF_SMARTY_FORCE_COMPILE) //this forces Smarty to recompile design each time someone runs index.php

  {
      $smarty->force_compile = true;
      $smarty_mail->force_compile = true;
  }

  //set Smarty include files dir
  $smarty->template_dir = "core/modules/design/";

  //assign core Smarty variables
        if (!isset($_GET["orderID"]) || !isset($_GET["order_time"]) || !isset($_GET["customer_email"]) || !isset($_GET["moduleID"]))
        {
                die ("����� �� ������ � ���� ������");
        }


        $InvoiceModule = modGetModuleObj((int)$_GET['moduleID'], PAYMENT_MODULE);
        $smarty->assign('InvoiceModule', $InvoiceModule);
        $_GET["orderID"] = (int)$_GET["orderID"];

  $q = db_query("select count(*) from ".ORDERS_TABLE." where orderID=".$_GET["orderID"]." and order_time='".xEscSQL(
      base64_decode($_GET["order_time"]))."' and customer_email='".xEscSQL(base64_decode($_GET["customer_email"])).
      "'");
  $row = db_fetch_row($q);

  if ($row[0] == 1) //����� ������ � ���� ������

  {
      $order = ordGetOrder($_GET["orderID"]); //order details

      //define smarty vars
      $smarty->assign("billing_name", $order["billing_firstname"]);
      $smarty->assign("billing_city", $order["billing_city"]);
      $smarty->assign("billing_address", $order["billing_address"]);
      $smarty->assign("orderID", $_GET["orderID"]);
      $smarty->assign("order_time", $order["order_time"]);

                if (!$InvoiceModule->is_installed()) //������ �� ����������
                {
                        die ("������ ������� ������ �� ����������");
                }

                //����� �����
                $sql = "select company_name, company_inn, nds_included, nds_rate, RUR_rate from ".DB_PRFX."_module_payment_invoice_jur where orderID=".$_GET["orderID"]." AND module_id=".(int)$InvoiceModule->ModuleConfigID;

                $q = db_query($sql);
                $row = db_fetch_row($q);
                if ($row) //����� ������� � ����� � ��������� �����
                {
                        $smarty->assign( "customer_companyname", $row["company_name"] );
                        $smarty->assign( "customer_inn",  $row["company_inn"] );
                        $nds_rate = (float) $row["nds_rate"];
                        $RUR_rate = (float) $row["RUR_rate"];
                        $nds_included = !strcmp((string)$row["nds_included"],"1") ? 1 : 0;
                }
                else //���������� � ���� �� �������
                {
                        die ("���� �� ������ � ���� ������");
                }

      //���������� ������
      $order_content = ordGetOrderContent($_GET["orderID"]);
      $amount = 0;
      foreach ($order_content as $key => $val)
      {
          $order_content[$key]["Price"] = _my_formatPrice($order_content[$key]["Price"] * $RUR_rate);
          $order_content[$key]["Price_x_Quantity"] = _my_formatPrice($val["Quantity"] * $val["Price"] *
              $RUR_rate);
          $amount += (double)strtr($order_content[$key]["Price_x_Quantity"], array("," => "", " " => ""));
      }

      $shipping_rate = $order["shipping_cost"] * $RUR_rate;

      $order["discount_value"] = round((double)$order["order_discount"] * $amount) / 100;

      $smarty->assign("order_discount", $order["order_discount"]);
      $smarty->assign("order_discount_value", _my_formatPrice($order["discount_value"]));

      $amount += $shipping_rate; //+��������� ��������

      $smarty->assign("order_content", $order_content);
      $smarty->assign("order_content_items_count", count($order_content) + 1);
      $smarty->assign("order_subtotal", _my_formatPrice($amount));

      if ($nds_rate <= 0) //�������� ���

      {
          $smarty->assign("order_tax_amount", "���");
          $smarty->assign("order_tax_amount_string", "���");
      }
      else
      {
          //����� �� ������������� �� ��������� ��������
          //���� �� ������, ����� ����� ������������ � �� ��������� �������� �������� ����
          // '($amount-$shipping_rate)' �� '$amount'

          if (!$nds_included) //����� �������

          {
              $tax_amount = round(($amount - $shipping_rate - $order["discount_value"]) * $nds_rate) /
                  100;

              $amount += $tax_amount;
          }
          else //��������� �����

          {
              $tax_amount = round(100 * ($amount - $shipping_rate - $order["discount_value"]) * $nds_rate /
                  ($nds_rate + 100)) / 100;
          }
          $smarty->assign("order_tax_amount", _my_formatPrice($tax_amount));
          $smarty->assign("order_tax_amount_string", create_string_representation_of_a_number($tax_amount));

      }

      $smarty->assign("order_total", _my_formatPrice($amount)); //$amount
      $smarty->assign("order_total_string", create_string_representation_of_a_number($amount));

      //��������
      if ($shipping_rate > 0)
      {
          $smarty->assign("shipping_type", $order["shipping_type"]);
          $smarty->assign("shipping_rate", _my_formatPrice($shipping_rate));
      }
  }
  else
  {
      die("����� �� ������ � ���� ������");
  }

  //show Smarty output
  $smarty->display("invoice_jur.tpl.html");
?>