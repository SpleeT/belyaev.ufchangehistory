<?php
namespace belyaev\ufchangehistory\handler;
use Bitrix\Main\Page\Asset;
use Bitrix\Main\Loader;
use Bitrix\Main\LoaderException;
/**
* Статические методы для инициализации входящих изменений сущностей OnBefore{Entity}Update
* @param array $arFields - массив данных формирующийся в ядре обработчика сущности
*/

class Handlers
{
  const CRM_DETAIL_PAGE = [
    'crm/'
  ];

  public static function leadEvent($arFields)
  {
    if(!$arFields['ID']) return false;
    CrmUserFieldsEvents::initChanges(\CCrmOwnerType::Lead, $arFields['ID'], $arFields);
  }

  public static function dealEvent(array $arFields)
  {
    if(!$arFields['ID']) return false;
    CrmUserFieldsEvents::initChanges(\CCrmOwnerType::Deal, $arFields['ID'], $arFields);
  }

  public static function contactEvent(array $arFields)
  {
    if(!$arFields['ID']) return false;
    CrmUserFieldsEvents::initChanges(\CCrmOwnerType::Contact, $arFields['ID'], $arFields);
  }

  public static function companyEvent(array $arFields)
  {
    if(!$arFields['ID']) return false;
    CrmUserFieldsEvents::initChanges(\CCrmOwnerType::Lead, $arFields['ID'], $arFields);
  }

  public static function addAssetOnPage()
  {
    if (!Loader::includeModule('belyaev.ufchangehistory')) {
        throw new LoaderException("Ошибка подключения модуля belyaev.ufchangehistory");
    }
    try {
      if(self::detectCrmDetailPage()) {
        $asset = Asset::getInstance();
        $asset->addJs('/local/js/belyaev.ufchangehistory/show_validate.js');
      }
    } catch (\Throwable $e) {
    }
  }

  private static function detectCrmDetailPage()
  {
    $curPage = $GLOBALS['APPLICATION']->GetCurPage();
    $result = false;
    foreach (self::CRM_DETAIL_PAGE as $accesPage) {
      if (strpos($curPage, $accesPage) !== false) $result = true;
    }
    return $result;
  }
}

 ?>
