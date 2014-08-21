<?php
#####################################
# ShopCMS: Скрипт интернет-магазина
# Copyright (c) by ADGroup
# http://shopcms.ru
#####################################

//this file indicates listing of all available languages

class Language
{
        var $description; //language name
        var $filename; //language PHP constants file
        var $template; //template filename
}

        //a list of languages
        $lang_list = array();

        //to add new languages add similiar structures

        $lang_list[0] = new Language();
        $lang_list[0]->description = "Русский";
        $lang_list[0]->filename = "russian.php";
        $lang_list[0]->iso2 = "ru";
?>