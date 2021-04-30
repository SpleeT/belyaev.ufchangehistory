<?php
namespace belyaev\ufchangehistory\handler;

/**
* Статические методы для инициализации входящих изменений сущностей OnBefore{Entity}Update
* @param array $arFields - массив данных формирующийся в ядре обработчика сущности
*/

class Handlers
{

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
}

 ?>
