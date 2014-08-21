<?php
  #####################################
  # ShopCMS: ������ ��������-��������
  # Copyright (c) by ADGroup
  # http://shopcms.ru
  #####################################

  define('ERROR_DB_INIT', 'Database connection problem!');

  function gmts()
  {
      list($usec, $sec) = explode(" ", microtime());
      return ((float)$usec + (float)$sec);
  }

  $sc_1 = gmts(); $sc_4 = 0; $sc_8 = 0; $gmc = 1;

  include("core/config/init.php");
  include("core/includes/database/mysql.php");

  $far_1 = array("core/config/connect.inc.php"
  ,"core/config/language_list.php"
  ,"core/classes/class.ajax.php"
  ,"core/classes/class.kcaptcha.php"
  ,"core/classes/class.virtual.paymentmodule.php"
  ,"core/classes/class.virtual.shippingratecalculator.php"
  ,"core/classes/class.xml2array.php");
  
  $far_2 = glob("core/functions/*.php");  
  $far = array_merge($far_1,$far_2);
 
  $cfar = count($far);
  if(file_exists("core/cache/fcache.php")) include ("core/cache/fcache.php");
  else for ($n=0; $n<$cfar; $n++) include ($far[$n]);

  define('PATH_DELIMITER', isWindows()?';':':');
  
  $_POST = xStripSlashesGPC($_POST);
  $_GET = xStripSlashesGPC($_GET);
  $_COOKIE = xStripSlashesGPC($_COOKIE);
  
  db_connect(DB_HOST, DB_USER, DB_PASS) or die(ERROR_DB_INIT);
  db_select_db(DB_NAME) or die(db_error());

  settingDefineConstants();
  
  include ("core/config/headers.php");
  include ("core/config/error_handler.php");
  
  define("SECURITY_EXPIRE", 60 * 60 * CONF_SECURITY_EXPIRE);
  session_set_save_handler("sess_open", "sess_close", "sess_read", "sess_write", "sess_destroy", "sess_gc");
  session_set_cookie_params(SECURITY_EXPIRE);
  session_start();
  if (isset($_COOKIE["PHPSESSID"])) setcookie("PHPSESSID", $_COOKIE["PHPSESSID"], time() + SECURITY_EXPIRE);
 
  //select a new language?
  if (isset($_POST["lang"])) $_SESSION["current_language"] = $_POST["lang"];

  //current language session variable
  if (!isset($_SESSION["current_language"]) || $_SESSION["current_language"] < 0 || $_SESSION["current_language"] >
      count($lang_list)) $_SESSION["current_language"] = 0; //set default language
  //include a language file
  if (isset($lang_list[$_SESSION["current_language"]]) && file_exists("core/languages/".$lang_list[$_SESSION["current_language"]]->
      filename))
  {
      //include current language file
      include ("core/languages/".$lang_list[$_SESSION["current_language"]]->filename);
  }
  else
  {
      die("<font color=red><b>ERROR: Couldn't find language file!</b></font>");
  }  

  if ( isset ( $_GET["do"] )) {
    if ( in_array($_GET["do"], array( "captcha", "cart", "rss", "compare", "yandex", "invoice_jur", "invoice_phys", "stat", "get_file" ))) {
        include ( "core/includes/processor/".$_GET["do"].".php" );
    }
    else {
        header("HTTP/1.0 404 Not Found");
        header("HTTP/1.1 404 Not Found");
        header("Status: 404 Not Found");
        die(ERROR_404_HTML);
    }
  } else {
  
  //init Smarty
  require ("core/smarty/smarty.class.php");
  $smarty = new Smarty; //core smarty object
  $smarty_mail = new Smarty; //for e-mails

  if ((int)CONF_SMARTY_FORCE_COMPILE) //this forces Smarty to recompile design each time someone runs index.php

  {
      $smarty->force_compile = true;
      $smarty_mail->force_compile = true;
  }

  $relaccess = checklogin();

  //# of selected currency
  $current_currency = isset($_SESSION["current_currency"]) ? $_SESSION["current_currency"] : CONF_DEFAULT_CURRENCY;
  $smarty->assign("current_currency", $current_currency);
  $q = db_query("select code, currency_value, where2show, currency_iso_3, Name, roundval from ".CURRENCY_TYPES_TABLE." where CID=".(int)$current_currency);
  if ($row = db_fetch_row($q))
  {
      $smarty->assign("currency_name", $row[0]);
      $selected_currency_details = $row; //for show_price() function
  }
  else //no currency found. In this case check is there any currency type in the database

  {
      $q = db_query("select code, currency_value, where2show, roundval from ".CURRENCY_TYPES_TABLE);
      if ($row = db_fetch_row($q))
      {
          $smarty->assign("currency_name", $row[0]);
          $selected_currency_details = $row; //for show_price() function
      }
  }
  $smarty->assign("currency_roundval", $selected_currency_details["roundval"]);

  //set $categoryID
  if (isset($_GET["categoryID"]) || isset($_POST["categoryID"]))
  {
      $categoryID = isset($_GET["categoryID"]) ? $_GET["categoryID"] : $_POST["categoryID"];
      $categoryID = (int)$categoryID;
  }
  // else $categoryID = 1;
  // set $productID
  if (isset($_GET["productID"]) || isset($_POST["productID"]))
  {
      $productID = isset($_GET["productID"]) ? $_GET["productID"] : $_POST["productID"];
      $productID = (int)$productID;
  }

  //and different vars...
  if (isset($_GET["register"]) || isset($_POST["register"]))
  $register = isset($_GET["register"]) ? $_GET["register"] : $_POST["register"];

  if (isset($_GET["update_details"]) || isset($_POST["update_details"]))
  $update_details = isset($_GET["update_details"]) ? $_GET["update_details"] : $_POST["update_details"];

  if (isset($_GET["order"]) || isset($_POST["order"]))
  $order = isset($_GET["order"]) ? $_GET["order"] : $_POST["order"];

  if (isset($_GET["order_without_billing_address"]) || isset($_POST["order_without_billing_address"]))
  $order_without_billing_address = isset($_GET["order_without_billing_address"]) ? $_GET["order_without_billing_address"] : $_POST["order_without_billing_address"];

  if (isset($_GET["check_order"]) || isset($_POST["check_order"]))
  $check_order = isset($_GET["check_order"]) ? $_GET["check_order"] : $_POST["check_order"];

  if (isset($_GET["proceed_ordering"]) || isset($_POST["proceed_ordering"]))
  $proceed_ordering = isset($_GET["proceed_ordering"]) ? $_GET["proceed_ordering"] : $_POST["proceed_ordering"];

  if (isset($_GET["update_customer_info"]) || isset($_POST["update_customer_info"]))
  $update_customer_info = isset($_GET["update_customer_info"]) ? $_GET["update_customer_info"] : $_POST["update_customer_info"];

  if (isset($_GET["show_aux_page"]) || isset($_POST["show_aux_page"]))
  $show_aux_page = isset($_GET["show_aux_page"]) ? $_GET["show_aux_page"] : $_POST["show_aux_page"];

  if (isset($_GET["visit_history"]) || isset($_POST["visit_history"])) $visit_history = 1;
  if (isset($_GET["order_history"]) || isset($_POST["order_history"])) $order_history = 1;
  if (isset($_GET["address_book"]) || isset($_POST["address_book"])) $address_book = 1;

  if (isset($_GET["address_editor"]) || isset($_POST["address_editor"]))
  $address_editor = isset($_GET["address_editor"]) ? $_GET["address_editor"] : $_POST["address_editor"];

  if (isset($_GET["add_new_address"]) || isset($_POST["add_new_address"]))
  $add_new_address = isset($_GET["add_new_address"]) ? $_GET["add_new_address"] : $_POST["add_new_address"];

  if (isset($_GET["contact_info"]) || isset($_POST["contact_info"]))
  $contact_info = isset($_GET["contact_info"]) ? $_GET["contact_info"] : $_POST["contact_info"];

  if (isset($_GET["comparison_products"]) || isset($_POST["comparison_products"])) $comparison_products = 1;
  if (isset($_GET["register_authorization"]) || isset($_POST["register_authorization"])) $register_authorization = 1;
  if (isset($_GET["page_not_found"]) || isset($_POST["page_not_found"])) $page_not_found = 1;
  if (isset($_GET["news"]) || isset($_POST["news"])) $news = 1;
  if (isset($_GET["quick_register"])) $quick_register = 1;
  if (isset($_GET["order2_shipping_quick"])) $order2_shipping_quick = 1;
  if (isset($_GET["order3_billing_quick"])) $order3_billing_quick = 1;
  if (isset($_GET["order2_shipping"]) || isset($_POST["order2_shipping"])) $order2_shipping = 1;
  if (isset($_GET["order3_billing"]) || isset($_POST["order3_billing"])) $order3_billing = 1;
  if (isset($_GET["change_address"]) || isset($_POST["change_address"])) $change_address = 1;
  if (isset($_GET["order4_confirmation"]) || isset($_POST["order4_confirmation"])) $order4_confirmation = 1;
  if (isset($_GET["order4_confirmation_quick"]) || isset($_POST["order4_confirmation_quick"])) $order4_confirmation_quick = 1;

  if (isset($_GET["order_detailed"]) || isset($_POST["order_detailed"]))
  $order_detailed = isset($_GET["order_detailed"]) ? $_GET["order_detailed"] : $_POST["order_detailed"];

  if (isset($_GET["p_order_detailed"]) || isset($_POST["p_order_detailed"]))
  $p_order_detailed = isset($_GET["p_order_detailed"]) ? $_GET["p_order_detailed"] : $_POST["p_order_detailed"];

  if (!isset($_SESSION["vote_completed"])) $_SESSION["vote_completed"] = array();

  //checking for proper $offset init
  $offset = isset($_GET["offset"]) ? $_GET["offset"] : 0;
  if ($offset < 0 || $offset % CONF_PRODUCTS_PER_PAGE) $offset = 0;

  if (isset($productID)) //to rollout categories navigation table

  {
      $q = db_query("select categoryID FROM ".PRODUCTS_TABLE." WHERE productID=".(int)$productID);
      $r = db_fetch_row($q);
      if ($r) $categoryID = $r[0];
  }

  if (isset($_POST["change_design"])) $_SESSION['CUSTOM_DESIGN'] = $_POST["change_design"];

  if (isset($_SESSION["CUSTOM_DESIGN"]))
  {
      $smarty->template_dir = "core/tpl/user/".$_SESSION["CUSTOM_DESIGN"];
      define('TPL', $_SESSION["CUSTOM_DESIGN"]);
  }
  else
  {
      $smarty->template_dir = "core/tpl/user/".CONF_DEFAULT_TEMPLATE;
      define('TPL', CONF_DEFAULT_TEMPLATE);
  }

  $smarty_mail->template_dir = "core/tpl/email";

  //fetch currency types from database
  $q = db_query("select CID, Name, code, currency_value, where2show, roundval, currency_iso_3 from ".CURRENCY_TYPES_TABLE." order by sort_order");
  $currencies = array();
  while ($row = db_fetch_row($q)) $currencies[] = $row;

  $smarty->assign("currencies", $currencies);
  $smarty->assign("currencies_count", count($currencies));

  $smarty->assign("lang_list", $lang_list);

  if (isset($_SESSION["current_language"])) $smarty->assign("current_language", $_SESSION["current_language"]);
  if (isset($_SESSION["log"])) $smarty->assign("log", $_SESSION["log"]);
  // - following vars are used as hidden in the customer survey form
  if (isset($categoryID)) $smarty->assign("categoryID", $categoryID);
  if (isset($productID)) $smarty->assign("productID", $productID);
  if (isset($_GET["currency"])) $smarty->assign("currency", $_GET["currency"]);
  if (isset($_GET["user_details"])) $smarty->assign("user_details", $_GET["user_details"]);
  if (isset($_GET["aux_page"])) $smarty->assign("aux_page", $_GET["aux_page"]);
  if (isset($_GET["show_price"])) $smarty->assign("show_price", $_GET["show_price"]);
  if (isset($_GET["searchstring"])) $smarty->hassign("searchstring", $_GET["searchstring"]);
  if (isset($register)) $smarty->assign("register", $register);
  if (isset($order)) $smarty->assign("order", $order);
  if (isset($check_order)) $smarty->assign("check_order", $check_order);
  //set defualt main_content template to homepage
  $smarty->assign("main_content_template", "home.tpl.html");

  //catalog

  $q = db_query("select categoryID, name, products_count, products_count_admin, parent, picture, subcount FROM ".CATEGORIES_TABLE." ORDER BY sort_order, name");
  $fc = array(); //parents
  $mc = array(); //parents
  while ($row = db_fetch_row($q)) {
                $fc[(int)$row["categoryID"]] = $row;
                $mc[(int)$row["categoryID"]] = (int)$row["parent"];
  }

  $cats = catGetCategoryCListMin();

  //include all .php files from core/includes/ dir or from cache
  if ((int)CONF_SMARTY_FORCE_COMPILE)
  {
      if(file_exists("core/cache/incache.php")) unlink ("core/cache/incache.php");
      if(file_exists("core/cache/fcache.php")) unlink ("core/cache/fcache.php");
      $fls = glob("core/includes/*.php");
      $cfls = count($fls);
      for ($zc=0; $zc<$cfls; $zc++) include ($fls[$zc]);
  }else{
      if(file_exists("core/cache/incache.php")) include ("core/cache/incache.php");
      else{
          ob_start();
          for ($n=0; $n<$cfar; $n++) readfile ($far[$n]);
          $_res = ob_get_contents();
          ob_end_clean();
          $fh = fopen("core/cache/fcache.php", 'w');
          fwrite($fh, $_res);
          fclose($fh);
          unset($_res);

          $fls = glob("core/includes/*.php");
          $cfls = count($fls);
          ob_start();
          for ($i=0; $i<$cfls; $i++) readfile ($fls[$i]);
          $_res = ob_get_contents();
          ob_end_clean();
          $fh = fopen("core/cache/incache.php", 'w');
          fwrite($fh, $_res);
          fclose($fh);
          unset($_res);
          include ("core/cache/incache.php");
      }
  }

  //show admin a administrative mode link
  if (isset($_SESSION["log"]) && in_array(100, $relaccess))
  {
      $smarty->assign("isadmin", "yes");
      $adminislog = true;
  }
  else
  {
      $adminislog = false;
  }

  $exploerrors = "";

  if (file_exists("install.php")) $exploerrors .= WARNING_DELETE_INSTALL_PHP;

  if (!is_writable("core/cache")) die(WARNING_WRONG_CHMOD);

  $RGLBLS = @ini_get('register_globals');
  if (strtolower($RGLBLS)=="on" || (int)$RGLBLS==1) die(WARNING_REGISTER_GLOBALS);

  $smarty->assign("exploerrors", $exploerrors);

  $tmpb = array();
  foreach ($leftb as $keylb => $vallb)
  {
        if ($vallb["which"] == 1) {
            if (!in_array($smarty -> get_template_vars("main_content_template"), $vallb["pages"]) && !in_array($aux_page["aux_page_ID"], $vallb["dpages"]) && !in_array($categoryID, $vallb["categories"]) && !in_array($productID, $vallb["products"])) $vallb["state"] = false;
            if (in_array($categoryID, $vallb["categories"]) && !in_array($productID, $vallb["products"]) && $smarty -> get_template_vars("main_content_template") == "product_detailed.tpl.html") $vallb["state"] = false;
        } else {
            if ($vallb["which"] == 2)
                if (in_array($smarty -> get_template_vars("main_content_template"), $vallb["pages"]) || in_array($aux_page["aux_page_ID"], $vallb["dpages"]) || in_array($categoryID, $vallb["categories"]) || in_array($productID, $vallb["products"])) $vallb["state"] = false;
                if (in_array($categoryID, $vallb["categories"]) && !in_array($productID, $vallb["products"]) && $smarty -> get_template_vars("main_content_template") == "product_detailed.tpl.html") $vallb["state"] = true;
        }
        if ($vallb["admin"] == 1) if (!$adminislog) $vallb["state"] = false;
            if ($vallb["state"] == true) {
                if ($vallb["url"] == "filter.tpl.html") {
                    if ($smarty -> get_template_vars("main_content_template") == ("category.tpl.html" || "category_search_result.tpl.html")) {
                        if ($smarty -> get_template_vars("categories_to_select")) $vallb["state"] = false;
                        if (!$categoryID) $vallb["state"] = false;
                        if (!$smarty -> get_template_vars("allow_products_search")) $vallb["state"] = false;
                        } else {
                        $vallb["state"] = false;
                        }
                }
            }

        if ($vallb["state"] == true) $tmpb[] = $vallb;
  }

  $smarty->assign("left_blocks",$tmpb);
  $smarty->assign("countlb",count($tmpb));

  $tmpb = array();
  foreach ($rightb as $keylb => $vallb)
  {
        if ($vallb["which"] == 1) {
            if (!in_array($smarty -> get_template_vars("main_content_template"), $vallb["pages"]) && !in_array($aux_page["aux_page_ID"], $vallb["dpages"]) && !in_array($categoryID, $vallb["categories"]) && !in_array($productID, $vallb["products"])) $vallb["state"] = false;
            if (in_array($categoryID, $vallb["categories"]) && !in_array($productID, $vallb["products"]) && $smarty -> get_template_vars("main_content_template") == "product_detailed.tpl.html") $vallb["state"] = false;
        } else {
            if ($vallb["which"] == 2)
                if (in_array($smarty -> get_template_vars("main_content_template"), $vallb["pages"]) || in_array($aux_page["aux_page_ID"], $vallb["dpages"]) || in_array($categoryID, $vallb["categories"]) || in_array($productID, $vallb["products"])) $vallb["state"] = false;
                if (in_array($categoryID, $vallb["categories"]) && !in_array($productID, $vallb["products"]) && $smarty -> get_template_vars("main_content_template") == "product_detailed.tpl.html") $vallb["state"] = true;
        }
        if ($vallb["admin"] == 1) if (!$adminislog) $vallb["state"] = false;
            if ($vallb["state"] == true) {
                if ($vallb["url"] == "filter.tpl.html") {
                    if ($smarty -> get_template_vars("main_content_template") == ("category.tpl.html" || "category_search_result.tpl.html")) {
                        if ($smarty -> get_template_vars("categories_to_select")) $vallb["state"] = false;
                        if (!$categoryID) $vallb["state"] = false;
                        if (!$smarty -> get_template_vars("allow_products_search")) $vallb["state"] = false;
                        } else {
                        $vallb["state"] = false;
                        }
                }
            }

        if ($vallb["state"] == true) $tmpb[] = $vallb;
  }
  $smarty->assign("right_blocks",$tmpb);
  $smarty->assign("countrb",count($tmpb));

  $tmpb = array();
  foreach ($topb as $keylb => $vallb)
  {
        if ($vallb["which"] == 1) {
            if (!in_array($smarty -> get_template_vars("main_content_template"), $vallb["pages"]) && !in_array($aux_page["aux_page_ID"], $vallb["dpages"]) && !in_array($categoryID, $vallb["categories"]) && !in_array($productID, $vallb["products"])) $vallb["state"] = false;
            if (in_array($categoryID, $vallb["categories"]) && !in_array($productID, $vallb["products"]) && $smarty -> get_template_vars("main_content_template") == "product_detailed.tpl.html") $vallb["state"] = false;
        } else {
            if ($vallb["which"] == 2)
                if (in_array($smarty -> get_template_vars("main_content_template"), $vallb["pages"]) || in_array($aux_page["aux_page_ID"], $vallb["dpages"]) || in_array($categoryID, $vallb["categories"]) || in_array($productID, $vallb["products"])) $vallb["state"] = false;
                if (in_array($categoryID, $vallb["categories"]) && !in_array($productID, $vallb["products"]) && $smarty -> get_template_vars("main_content_template") == "product_detailed.tpl.html") $vallb["state"] = true;
        }
        if ($vallb["admin"] == 1) if (!$adminislog) $vallb["state"] = false;
            if ($vallb["state"] == true) {
                if ($vallb["url"] == "filter.tpl.html") {
                    if ($smarty -> get_template_vars("main_content_template") == ("category.tpl.html" || "category_search_result.tpl.html")) {
                        if ($smarty -> get_template_vars("categories_to_select")) $vallb["state"] = false;
                        if (!$categoryID) $vallb["state"] = false;
                        if (!$smarty -> get_template_vars("allow_products_search")) $vallb["state"] = false;
                        } else {
                        $vallb["state"] = false;
                        }
                }
            }

        if ($vallb["state"] == true) $tmpb[] = $vallb;
  }
  $smarty->assign("top_blocks",$tmpb);
  $smarty->assign("counttb",count($tmpb));

  $tmpb = array();
  foreach ($bottomb as $keylb => $vallb)
  {
        if ($vallb["which"] == 1) {
            if (!in_array($smarty -> get_template_vars("main_content_template"), $vallb["pages"]) && !in_array($aux_page["aux_page_ID"], $vallb["dpages"]) && !in_array($categoryID, $vallb["categories"]) && !in_array($productID, $vallb["products"])) $vallb["state"] = false;
            if (in_array($categoryID, $vallb["categories"]) && !in_array($productID, $vallb["products"]) && $smarty -> get_template_vars("main_content_template") == "product_detailed.tpl.html") $vallb["state"] = false;
        } else {
            if ($vallb["which"] == 2)
                if (in_array($smarty -> get_template_vars("main_content_template"), $vallb["pages"]) || in_array($aux_page["aux_page_ID"], $vallb["dpages"]) || in_array($categoryID, $vallb["categories"]) || in_array($productID, $vallb["products"])) $vallb["state"] = false;
                if (in_array($categoryID, $vallb["categories"]) && !in_array($productID, $vallb["products"]) && $smarty -> get_template_vars("main_content_template") == "product_detailed.tpl.html") $vallb["state"] = true;
        }
        if ($vallb["admin"] == 1) if (!$adminislog) $vallb["state"] = false;
            if ($vallb["state"] == true) {
                if ($vallb["url"] == "filter.tpl.html") {
                    if ($smarty -> get_template_vars("main_content_template") == ("category.tpl.html" || "category_search_result.tpl.html")) {
                        if ($smarty -> get_template_vars("categories_to_select")) $vallb["state"] = false;
                        if (!$categoryID) $vallb["state"] = false;
                        if (!$smarty -> get_template_vars("allow_products_search")) $vallb["state"] = false;
                        } else {
                        $vallb["state"] = false;
                        }
                }
            }

        if ($vallb["state"] == true) $tmpb[] = $vallb;
  }
  $smarty->assign("bottom_blocks",$tmpb);
  $smarty->assign("countbb",count($tmpb));

  $sc_2 = getmicrotime();
  $sr_1 = $sc_2 - $sc_1 - $sc_8;

  //show Smarty output
  $smarty->display("index.tpl.html");

  if ($adminislog && CONF_DISPLAY_INFO == 1)
  {
      $sr3  = getmicrotime();
      $sr_2 = $sr3 - $sc_2;
      $sr_3 = $sr3 - $sc_1;
      $sr_1 = number_format(round($sr_1, 3), 3, '.', '');
      $sr_2 = number_format(round($sr_2, 3), 3, '.', '');
      $sr_3 = number_format(round($sr_3, 3), 3, '.', '');
      $sc_8 = number_format(round($sc_8, 3), 3, '.', '');

      $_SESSION["tgenexe"]     = $sr_1;
      $_SESSION["tgencompile"] = $sr_2;
      $_SESSION["tgendb"]      = $sc_8;
      $_SESSION["tgenall"]     = $sr_3;
      $_SESSION["tgensql"]     = $sc_4;
  }

  }
?>