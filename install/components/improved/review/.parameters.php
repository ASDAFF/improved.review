<?
/**
 * Copyright (c) 1/8/2020 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!CModule::IncludeModule("iblock") ||
   !CModule::IncludeModule("improved.review"))
                return;
                
function array_merge_recursive_distinct ( array &$array1, array &$array2 )
{
  $merged = $array1;
  foreach ( $array2 as $key => &$value )
  {
    if ( is_array ( $value ) && isset ( $merged [$key] ) && is_array ( $merged [$key] ) )
      $merged [$key] = array_merge_recursive_distinct ( $merged [$key], $value );
    else
      $merged [$key] = $value;
  }
return $merged;
}
//list
CComponentUtil::__IncludeLang("/bitrix/components/improved/review.list/",".parameters.php");
include($_SERVER["DOCUMENT_ROOT"]."/bitrix/components/improved/review.list/.parameters.php");
$arComponentParametersList = $arComponentParameters;
unset($arComponentParameters);
//add
CComponentUtil::__IncludeLang("/bitrix/components/improved/review.add/",".parameters.php");
include($_SERVER["DOCUMENT_ROOT"]."/bitrix/components/improved/review.add/.parameters.php");
$arComponentParametersAdd = $arComponentParameters;
unset($arComponentParameters);

$arComponentParameters = array_merge_recursive_distinct($arComponentParametersList,$arComponentParametersAdd);
?>