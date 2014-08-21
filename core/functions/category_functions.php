<?php
#####################################
# ShopCMS: Скрипт интернет-магазина
# Copyright (c) by ADGroup
# http://shopcms.ru
#####################################

function catInstall()
{
        db_query("insert into ".CATEGORIES_TABLE." ( name, parent, categoryID ) values ( '".ADMIN_CATEGORY_ROOT."', NULL, 1 )");
}

function getcontentcat($categoryID){
         $out = array();
         $cnt = 0;
         $q = db_query("select Owner from ".RELATED_CONTENT_CAT_TABLE." where categoryID=".(int)$categoryID);
         while ($row = db_fetch_row($q))
         {
                $qd = db_query("select aux_page_name from ".AUX_PAGES_TABLE." where aux_page_ID=".(int)$row["Owner"]);
                $rowd = db_fetch_row($qd);
                $out[$cnt][0] = $row["Owner"];
                $out[$cnt][1] = $rowd["aux_page_name"];
                $cnt++;
         }
         return $out;
}

function processCategories($level, $path, $sel)
{
        //returns an array of categories, that will be presented by the category_navigation.tpl template

        //$categories[] - categories array
        //$level - current level: 0 for main categories, 1 for it's subcategories, etc.
        //$path - path from root to the selected category (calculated by calculatePath())
        //$sel -- categoryID of a selected category

        //returns an array of (categoryID, name, level)

        //category tree is being rolled out "by the path", not fully

        $out = array();
        $cnt = 0;

        $parent = $path[$level]["parent"];
        if ( $parent == "" || $parent == null ) $parent = "NULL";

        $q = db_query("select categoryID, name from ".CATEGORIES_TABLE.
                " where parent=".(int)$path[$level]["parent"]." order by sort_order, name");
        $c_path = count($path);
        while ($row = db_fetch_row($q))
        {
                $out[$cnt][0] = $row["categoryID"];
                $out[$cnt][1] = $row["name"];
                $out[$cnt][2] = $level;
                $cnt++;

                //process subcategories?
                if ($level+1<$c_path && $row["categoryID"] == $path[$level+1])
                {
                        $sub_out = processCategories($level+1,$path,$sel);
                        //add $sub_out to the end of $out
                        for ($j=0; $j<count($sub_out); $j++)
                        {
                                $out[] = $sub_out[$j];
                                $cnt++;
                        }
                }
        }
        return $out;
} //processCategories

function fillTheCList($parent,$level) //completely expand category tree
{

        $q = db_query("select categoryID, name, products_count, products_count_admin, parent FROM ".
                CATEGORIES_TABLE." WHERE parent=".(int)$parent." ORDER BY sort_order, name");
        $a = array(); //parents
        while ($row = db_fetch_row($q))
        {
                $row["level"] = $level;
                $a[] = $row;
                //process subcategories
                $b = fillTheCList($row[0],$level+1);
                //add $b[] to the end of $a[]
                $cc_b = count($b);
                for ($j=0; $j<$cc_b; $j++) $a[] = $b[$j];
        }
        return $a;

} //fillTheCList

function _recursiveGetCategoryCompactCList( $path, $level )
{
        $q = db_query( "select categoryID, parent, name, products_count from ".CATEGORIES_TABLE.
                                " where parent=".(int)$path[$level-1]["categoryID"]." order by sort_order, name " );
        $res = array();
        $selectedCategoryID = null;
        $c_path = count($path);
        while( $row=db_fetch_row($q) )
        {

                $row["level"] = $level;
                $res[] = $row;
                if ( $level <= $c_path-1 )
                {
                        if ( (int)$row["categoryID"] == (int)$path[$level]["categoryID"] )
                        {
                                $selectedCategoryID = $row["categoryID"];
                                $arres = _recursiveGetCategoryCompactCList( $path, $level+1 );

                                $c_arres = count($arres);
                                for ($i=0; $i<$c_arres; $i++) $res[] = $arres[$i];
                        }
                }
        }

        return $res;
}

function getcontentcatresc( $catID )
{
        $q = db_query( "select categoryID, name, products_count, description, picture  from ".CATEGORIES_TABLE.
                                " where parent=".(int)$catID." order by sort_order, name " );
        $res = array();
        while( $row=db_fetch_row($q) ) $res[] = $row;
        return $res;
}

function catExpandCategory( $categoryID, $sessionArrayName )
{
        $existFlag = false;
        foreach( $_SESSION[$sessionArrayName] as $key => $value )
                if ( $value == $categoryID )
                {
                        $existFlag = true;
                        break;
                }
        if ( !$existFlag ) $_SESSION[$sessionArrayName][] = $categoryID;

}

function catShrinkCategory( $categoryID, $sessionArrayName )
{
        foreach( $_SESSION[$sessionArrayName] as $key => $value )
        {
                if ( $value == $categoryID ) unset( $_SESSION[$sessionArrayName][$key] );
        }
}

function catExpandCategoryp( $sessionArrayName )
{
        $categoryID = 0;
        $cats = array();
        $q = db_query("select categoryID FROM ".CATEGORIES_TABLE." ORDER BY sort_order, name");
        while ($row = db_fetch_row($q)) $_SESSION[$sessionArrayName][] = $row[0];
}

function catShrinkCategorym( $sessionArrayName )
{
        unset( $_SESSION[$sessionArrayName]);
        $_SESSION["expcat"] = array(1);
}

function catGetCategoryCompactCList( $selectedCategoryID )
{
        $path = catCalculatePathToCategory( $selectedCategoryID );
        $res = array();
        $res[] = array( "categoryID" => 1, "parent" => null,
                                        "name" => ADMIN_CATEGORY_ROOT, "level" => 0 );
        $q = db_query( "select categoryID, parent, name, products_count from ".CATEGORIES_TABLE.
                                " where parent=1 ".
                                " order by sort_order, name " );
        $c_path = count($path);
        while( $row = db_fetch_row($q) )
        {
                $row["level"] = 1;
                $res[] = $row;
                if ( $c_path > 1 )
                {
                        if ( $row["categoryID"] == $path[1]["categoryID"] )
                        {
                                $arres = _recursiveGetCategoryCompactCList( $path, 2 );
                                $c_arres = count($arres);
                                for ($i=0; $i<$c_arres; $i++) $res[] = $arres[$i];

                        }
                }
        }
        return $res;
}



// *****************************************************************************
// Purpose        gets category tree to render it on HTML page
// Inputs
//                        $parent - must be 0
//                        $level        - must be 0
//                        $expcat - array of category ID that expanded
// Remarks
//                        array of item
//                                for each item
//                                        "products_count"                        -                count product in category including
//                                                                                                                        subcategories excluding enabled product
//                                        "products_count_admin"                -                count product in category
//                                                                                                                        without count product subcategory
//                                        "products_count_category"        -
// Returns        nothing
function _recursiveGetCategoryCList( $parent, $level, $expcat, $_indexType = 'NUM', $cprod = false, $ccat = true)
{
global $fc, $mc;

        $rcat  = array_keys ($mc, (int)$parent);
        $result = array(); //parents

        $crcat = count($rcat);
        for ($i=0; $i<$crcat; $i++) {

        $row = $fc[(int)$rcat[$i]];
                if (!file_exists("data/category/".$row["picture"])) $row["picture"] = "";
                $row["level"] = $level;
                $row["ExpandedCategory"] = false;
                if ( $expcat != null )
                {
                        foreach( $expcat as $categoryID )
                        {
                                if ( (int)$categoryID == (int)$row["categoryID"] )
                                {
                                        $row["ExpandedCategory"] = true;
                                        break;
                                }
                        }
                }
                else
                        $row["ExpandedCategory"] = true;

                if ($ccat) {$row["products_count_category"] = catGetCategoryProductCount( $row["categoryID"], $cprod );}

                $row["ExistSubCategories"] = ( $row["subcount"] != 0 );

                if($_indexType=='NUM')
                        $result[] = $row;
                elseif ($_indexType=='ASSOC')
                        $result[$row['categoryID']] = $row;


                if ( $row["ExpandedCategory"] )
                {
                        //process subcategories
                        $subcategories = _recursiveGetCategoryCList( $row["categoryID"],
                                $level+1, $expcat, $_indexType, $cprod, $ccat);

                        if($_indexType=='NUM'){

                                //add $subcategories[] to the end of $result[]
                                for ($j=0; $j<count($subcategories); $j++)
                                        $result[] = $subcategories[$j];
                        }
                        elseif ($_indexType=='ASSOC'){

                                //add $subcategories[] to the end of $result[]
                                foreach ($subcategories as $_sub){

                                        $result[$_sub['categoryID']] = $_sub;
                                }
                        }

                }
        }
        return $result;
}


// *****************************************************************************
// Purpose        gets category tree to render it on HTML page
// Inputs
// Remarks
// Returns        nothing
function catGetCategoryCList( $expcat = null, $_indexType='NUM', $cprod = false, $ccat = true  )
{
        return _recursiveGetCategoryCList( 1, 0, $expcat, $_indexType, $cprod, $ccat);
}

function catGetCategoryCListMin()
{
        return _recursiveGetCategoryCList( 1, 0, null, 'NUM', false, false);
}

// *****************************************************************************
// Purpose        gets product count in category
// Inputs
// Remarks  this function does not keep in mind subcategories
// Returns        nothing
function catGetCategoryProductCount( $categoryID, $cprod = false )
{
        if (!$categoryID) return 0;

        $res = 0;
        $sql = "
                select count(*) FROM ".PRODUCTS_TABLE."
                WHERE categoryID=".(int)$categoryID."".($cprod?" AND enabled>0":"");
        $q = db_query($sql);
        $t = db_fetch_row($q);
        $res += $t[0];
        if($cprod)
                $sql = "
                        select COUNT(*) FROM ".PRODUCTS_TABLE." AS prot
                        LEFT JOIN ".CATEGORIY_PRODUCT_TABLE." AS catprot
                        ON prot.productID=catprot.productID
                        WHERE catprot.categoryID=".(int)$categoryID." AND prot.enabled>0
                ";
        else
                $sql = "
                        select count(*) from ".CATEGORIY_PRODUCT_TABLE.
                        " where categoryID=".(int)$categoryID
                ;
        $q1 = db_query($sql);
        $row = db_fetch_row($q1);
        $res += $row[0];
        return $res;
}

function update_sCount($parent)
{
global $fc, $mc;

        $rcat = array_keys ($mc, (int)$parent);
        $crcat = count($rcat);
        for ($i=0; $i<$crcat; $i++) {

        $rowsub = $fc[(int)$rcat[$i]];
        $countsub  = count(array_keys ($mc, (int)$rowsub["categoryID"]));

        db_query("UPDATE ".CATEGORIES_TABLE.
                        " SET subcount=".(int)$countsub." ".
                        " WHERE categoryID=".(int)$rcat[$i]);

        $rowsubExist = ( $countsub != 0 );
        if ( $rowsubExist ) update_sCount($rowsub["categoryID"]);
        }
}

function update_pCount($parent)
{
        update_sCount($parent);

        $q = db_query("select categoryID FROM ".CATEGORIES_TABLE.
                " WHERE categoryID>1 AND parent=".(int)$parent);

        $cnt = array();
        $cnt["admin_count"] = 0;
        $cnt["customer_count"] = 0;

        // process subcategories
        while( $row=db_fetch_row($q) )
        {
                $t = update_pCount( $row["categoryID"] );
                $cnt["admin_count"]     += $t["admin_count"];
                $cnt["customer_count"]  += $t["customer_count"];
        }

        // to administrator
        $q = db_query("select count(*) FROM ".PRODUCTS_TABLE.
                        " WHERE categoryID=".(int)$parent);
        $t = db_fetch_row($q);
        $cnt["admin_count"] += $t[0];
        $q1 = db_query("select count(*) from ".CATEGORIY_PRODUCT_TABLE.
                        " where categoryID=".(int)$parent);
        $row = db_fetch_row($q1);
        $cnt["admin_count"] += $row[0];

        // to customer
        $q = db_query("select count(*) FROM ".PRODUCTS_TABLE.
                        " WHERE enabled=1 AND categoryID=".(int)$parent);
        $t = db_fetch_row($q);
        $cnt["customer_count"] += $t[0];
        $q1 = db_query("select productID, categoryID from ".CATEGORIY_PRODUCT_TABLE.
                        " where categoryID=".(int)$parent);
        while( $row = db_fetch_row($q1) )
        {
                $q2 = db_query("select productID from ".PRODUCTS_TABLE.
                                " where enabled=1 AND productID=".(int)$row["productID"]);
                if ( db_fetch_row($q2) )
                        $cnt["customer_count"] ++;
        }

        db_query("UPDATE ".CATEGORIES_TABLE.
                        " SET products_count=".(int)$cnt["customer_count"].", products_count_admin=".
                                (int)$cnt["admin_count"]." WHERE categoryID=".(int)$parent);
        return $cnt;
}

function update_psCount($parent)
{
global $fc, $mc;

          $q = db_query("select categoryID, name, products_count, ".
                        "products_count_admin, parent, picture, subcount FROM ".
                        CATEGORIES_TABLE. " ORDER BY sort_order, name");
          $fc = array(); //parents
          $mc = array(); //parents
          while ($row = db_fetch_row($q)) {
                $fc[(int)$row["categoryID"]] = $row;
                $mc[(int)$row["categoryID"]] = (int)$row["parent"];
          }
          update_pCount($parent);
}
// *****************************************************************************
// Purpose        get subcategories by category id
// Inputs   $categoryID
//                                parent category ID
// Remarks  get current category's subcategories IDs (of all levels!)
// Returns        array of category ID
function catGetSubCategories( $categoryID )
{
        $q = db_query("select categoryID from ".CATEGORIES_TABLE." where parent=".(int)$categoryID);
        $r = array();
        while ($row = db_fetch_row($q))
        {
                $a = catGetSubCategories($row[0]);
                $c_a = count($a);
                for ($i=0;$i<$c_a;$i++) $r[] = $a[$i];
                $r[] = $row[0];
        }
        return $r;
}


// *****************************************************************************
// Purpose        get subcategories by category id
// Inputs           $categoryID
//                                parent category ID
// Remarks          get current category's subcategories IDs (of all levels!)
// Returns        array of category ID
function catGetSubCategoriesSingleLayer( $categoryID )
{
        $q = db_query("select categoryID, name, products_count FROM ".
                        CATEGORIES_TABLE." WHERE parent=".(int)$categoryID." order by sort_order, name");
        $result = array();
        while ($row = db_fetch_row($q)) $result[] = $row;
        return $result;
}



// *****************************************************************************
// Purpose        get category by id
// Inputs   $categoryID
//                                - category ID
// Remarks
// Returns
function catGetCategoryById($categoryID)
{
        $q = db_query("select categoryID, name, parent, products_count, description, picture, ".
                " products_count_admin, sort_order, viewed_times, allow_products_comparison, allow_products_search, ".
                " show_subcategories_products, meta_description, meta_keywords, title ".
                " from ".CATEGORIES_TABLE." where categoryID=".(int)$categoryID);
        $catrow = db_fetch_row($q);
        return $catrow;
}

// *****************************************************************************
// Purpose        gets category META information in HTML form
// Inputs   $categoryID
//                                - category ID
// Remarks
// Returns
function catGetMetaTags($categoryID)
{
        $q = db_query( "select meta_description, meta_keywords from ".
                CATEGORIES_TABLE." where categoryID=".(int)$categoryID );
        $row = db_fetch_row($q);

        $res = "";

        if  ( $row["meta_description"] != "" )
                $res .= "<meta name=\"Description\" content=\"".$row["meta_description"]."\">\n";
        if  ( $row["meta_keywords"] != "" )
                $res .= "<meta name=\"KeyWords\" content=\"".$row["meta_keywords"]."\" >\n";

        return $res;
}



// *****************************************************************************
// Purpose        adds product to appended category
// Inputs
// Remarks      this function uses CATEGORIY_PRODUCT_TABLE table in data base instead of
//                        PRODUCTS_TABLE.categoryID. In CATEGORIY_PRODUCT_TABLE saves appended
//                        categories
// Returns        array of item
//                        "categoryID"
//                        "category_name"
function catGetAppendedCategoriesToProduct( $productID )
{
         $q = db_query( "select ".CATEGORIES_TABLE.".categoryID as categoryID, name as category_name ".
                " from ".CATEGORIY_PRODUCT_TABLE.", ".CATEGORIES_TABLE." ".
                " where ".CATEGORIY_PRODUCT_TABLE.".categoryID = ".CATEGORIES_TABLE.".categoryID ".
                " AND productID = ".(int)$productID  );
        $data = array();
        while( $row = db_fetch_row( $q ) ){
                $wayadd = '';
                $way = catCalculatePathToCategoryA($row["categoryID"]);
                $cway = count($way);
                for ($i=$cway-1; $i>=0; $i--){ if($way[$i]['categoryID']!=1) $wayadd .= $way[$i]['name'].' / '; }
                $row["category_way"]=$wayadd."<b>".$row["category_name"]."</b>";
                $data[] = $row;
                }
        return $data;
}



// *****************************************************************************
// Purpose        adds product to appended category
// Inputs
// Remarks      this function uses CATEGORIY_PRODUCT_TABLE table in data base instead of
//                        PRODUCTS_TABLE.categoryID. In CATEGORIY_PRODUCT_TABLE saves appended
//                        categories
// Returns        true if success, false otherwise
function catAddProductIntoAppendedCategory($productID, $categoryID)
{
        $q = db_query("select count(*) from ".CATEGORIY_PRODUCT_TABLE.
                " where productID=".(int)$productID." AND categoryID=".(int)$categoryID);
        $row = db_fetch_row( $q );

        $qh = db_query( "select categoryID from ".PRODUCTS_TABLE.
                        " where productID=".(int)$productID);
        $rowh = db_fetch_row( $qh );
        $basic_categoryID = $rowh["categoryID"];

        if ( !$row[0] && $basic_categoryID != $categoryID )
        {
                db_query("insert into ".CATEGORIY_PRODUCT_TABLE.
                        "( productID, categoryID ) ".
                        "values( ".(int)$productID.", ".(int)$categoryID." )" );
                return true;
        }
        else
                return false;
}


// *****************************************************************************
// Purpose        removes product to appended category
// Inputs
// Remarks      this function uses CATEGORIY_PRODUCT_TABLE table in data base instead of
//                        PRODUCTS_TABLE.categoryID. In CATEGORIY_PRODUCT_TABLE saves appended
//                        categories
// Returns        nothing
function catRemoveProductFromAppendedCategory($productID, $categoryID)
{
        db_query("delete from ".CATEGORIY_PRODUCT_TABLE.
                " where productID = ".(int)$productID." AND categoryID = ".(int)$categoryID);

}


// *****************************************************************************
// Purpose        calculate a path to the category ( $categoryID )
// Inputs
// Remarks
// Returns        path to category
function catCalculatePathToCategory( $categoryID )
{
        if (!$categoryID) return NULL;

        $path = array();

        $q = db_query("select count(*) from ".CATEGORIES_TABLE.
                        " where categoryID=".(int)$categoryID);
        $row = db_fetch_row($q);
        if ( $row[0] == 0 ) return $path;

        do
        {
                $q = db_query("select categoryID, parent, name FROM ".
                        CATEGORIES_TABLE." WHERE categoryID=".(int)$categoryID);
                $row = db_fetch_row($q);
                $path[] = $row;

                if ( $categoryID == 1 ) break;

                $categoryID = $row["parent"];
        }
        while ( 1 );
        //now reverse $path
        $path = array_reverse($path);
        return $path;
}

// *****************************************************************************
// Purpose        calculate a path to the category ( $categoryID )
// Inputs
// Remarks
// Returns        path to category
function catCalculatePathToCategoryA( $categoryID )
{
        if (!$categoryID) return NULL;

        $path = array();

        $q = db_query("select count(*) from ".CATEGORIES_TABLE.
                        " where categoryID=".(int)$categoryID);
        $row = db_fetch_row($q);
        if ( $row[0] == 0 ) return $path;
        $curr = $categoryID;
        do
        {
                $q = db_query("select categoryID, parent, name FROM ".
                        CATEGORIES_TABLE." WHERE categoryID=".(int)$categoryID);
                $row = db_fetch_row($q);
                if($categoryID != $curr) $path[] = $row;

                if ( $categoryID == 1 ) break;

                $categoryID = $row["parent"];
        }
        while ( 1 );
        //now reverse $path
        $path = array_reverse($path);
        return $path;
}

function _deleteSubCategories( $parent )
{

        $q1 = db_query("select picture FROM ".CATEGORIES_TABLE." WHERE categoryID=".(int)$parent);
        $r = db_fetch_row($q1);
        if ($r["picture"] && file_exists("data/category/".$r["picture"])) unlink("data/category/".$r["picture"]);


        $q = db_query("select categoryID FROM ".CATEGORIES_TABLE." WHERE parent=".(int)$parent);
        while ($row = db_fetch_row($q)){
        $qp = db_query("select productID FROM ".PRODUCTS_TABLE." where categoryID=".(int)$row["categoryID"] );
        while ( $picture = db_fetch_row($qp) )
        {
        DeleteThreePictures2($picture["productID"]);
        }
        db_query("delete FROM ".PRODUCTS_TABLE." WHERE categoryID=".(int)$row["categoryID"]);
        _deleteSubCategories( $row["categoryID"] );
        }
        db_query("delete FROM ".CATEGORIES_TABLE." WHERE parent=".(int)$parent);

}


// *****************************************************************************
// Purpose        deletes category
// Inputs
//                 $categoryID - ID of category to be deleted
// Remarks      delete also all subcategories, all prodoctes remove into root
// Returns        nothing
function catDeleteCategory( $categoryID )
{
        _deleteSubCategories( $categoryID );

        $q=db_query("select productID FROM ".PRODUCTS_TABLE." where categoryID=".(int)$categoryID );
        if ( $picture=db_fetch_row($q) )
        {
        DeleteThreePictures2($picture["productID"]);
        }

        db_query("delete FROM ".PRODUCTS_TABLE." WHERE categoryID=".(int)$categoryID);

        db_query("delete FROM ".CATEGORIES_TABLE." WHERE parent=".(int)$categoryID);
        $q = db_query("select picture FROM ".CATEGORIES_TABLE." WHERE categoryID=".(int)$categoryID);
        $r = db_fetch_row($q);
        if ($r["picture"] && file_exists("data/category/".$r["picture"])) unlink("data/category/".$r["picture"]);

        db_query("delete FROM ".CATEGORIES_TABLE." WHERE categoryID=".(int)$categoryID);
}

?>