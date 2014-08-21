<?php
#####################################
# ShopCMS: ������ ��������-��������
# Copyright (c) by ADGroup
# http://shopcms.ru
#####################################

function auxpgGetAllPageAttributes()
{
        $q = db_query("select aux_page_ID, aux_page_name, aux_page_text_type from ".AUX_PAGES_TABLE);
        $data = array();
        while( $row = db_fetch_row( $q ) ) $data[] = $row;
        return $data;
}

function auxpgGetAuxPage( $aux_page_ID )
{
        $q = db_query("select aux_page_ID, aux_page_name, aux_page_text, aux_page_text_type, ".
                 " meta_keywords, meta_description, title from ".AUX_PAGES_TABLE." where aux_page_ID=".(int)$aux_page_ID);
        if  ( $row=db_fetch_row($q) )
        {
                if ( $row["aux_page_text_type"] !=1 ) $row["aux_page_text"] = ToText( $row["aux_page_text"] );
                $row["aux_page_title"] = $row["title"];
        }
        return $row;
}

function auxpgUpdateAuxPage(    $aux_page_ID, $aux_page_name,
                                $aux_page_text, $aux_page_text_type,
                                $meta_keywords, $meta_description, $aux_page_title  )
{
        db_query("update ".AUX_PAGES_TABLE.
                 " set     aux_page_name='".xToText($aux_page_name)."', ".
                 "         aux_page_text='".xEscSQL($aux_page_text)."', ".
                 "         aux_page_text_type=".(int)$aux_page_text_type.", ".
                 "         meta_keywords='".xToText($meta_keywords)."', ".
                 "         meta_description='".xToText($meta_description)."', ".
                 "         title='".xToText($aux_page_title)."' ".
                 " where aux_page_ID=".(int)$aux_page_ID);
}

function auxpgAddAuxPage(       $aux_page_name,
                                $aux_page_text, $aux_page_text_type,
                                $meta_keywords, $meta_description, $aux_page_title)
{
        db_query( "insert into ".AUX_PAGES_TABLE.
                " ( aux_page_name, aux_page_text, aux_page_text_type, meta_keywords, meta_description, title )  ".
                " values( '".xToText($aux_page_name)."', '".xEscSQL($aux_page_text)."', ".(int)$aux_page_text_type.", ".
                " '".xToText($meta_keywords)."', '".xToText($meta_description)."', '".xToText($aux_page_title)."' ) " );
}

function auxpgDeleteAuxPage( $aux_page_ID )
{
        db_query("delete from ".AUX_PAGES_TABLE." where aux_page_ID=".(int)$aux_page_ID);
}


?>