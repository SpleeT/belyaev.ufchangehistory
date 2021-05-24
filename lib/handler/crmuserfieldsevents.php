<?php
namespace belyaev\ufchangehistory\handler;

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Loader;
use Bitrix\Main\LoaderException;

class CrmUserFieldsEvents
{

  /**
  *
  * @param int $entityID идентификатор сущности Crm
  * @param int $id идентификатор лида/сделки/компании/контакта
  * @param array $arFields массив данных передаваемый событием изменения
  */
  public static function initChanges(int $entityID, int $id, array &$arFields)
  {
    global $USER_FIELD_MANAGER;
    if (!Loader::includeModule('belyaev.ufchangehistory') || !Loader::includeModule('crm')) {
        throw new LoaderException(Loc::getMessage('CANT_INCLUDE_MODULE'));
    }

    $changedBy = null; // Вводим переменную для текущего пользователя
    $changedBy = (is_null($changedBy)) ? \CCrmSecurityHelper::GetCurrentUserID() : $changedBy;

    if (!\CCrmOwnerType::IsDefined($entityID)) {
      throw new \Exception(Loc::getMessage('CANT_INCLUDE_OWNER_TYPE'));
    }

    $entityTypeName = \CCrmOwnerType::ResolveName($entityID);

    $ufEntiy = \CCrmOwnerType::resolveUserFieldEntityID($entityID);
    if(!$ufEntiy) throw new \Exception(Loc::getMessage('ERROR_PARSE_UF_ENTITY'));

    $ufData = $USER_FIELD_MANAGER->GetUserFields($ufEntiy, $id, LANGUAGE_ID);
    if(!$ufData) return true;

    $ufDataValues = self::getUserFieldsValues($arFields, $ufData, $entityTypeName, $changedBy);

    $event = new \Bitrix\Main\Event("belyaev.ufchangehistory", "On{$entityTypeName}entityUfUpdate", $ufDataValues);
    $event->send();

    self::writeChanges($ufDataValues); // Отправляем изменения

    return true;
  }

  private static function getUserFieldsValues($arFields, $ufData, $entityTypeName, $changedBy)
  {
    if(empty($arFields)) return false;
    $arResult = [];
    foreach ($arFields as $key => $value) {
      $currentField = $ufData[$key];
      if (empty($currentField)) continue;
      $fieldID = $currentField['ID'];
      $fieldType = $currentField['USER_TYPE_ID'];
      $fieldName = $currentField['FIELD_NAME'];
      $entityID = $currentField['ENTITY_VALUE_ID'];
      $fieldTitle = ($currentField['EDIT_FORM_LABEL']) ? $currentField['EDIT_FORM_LABEL'] : $fieldName;
      // Массив со всеми основными данными для передачи событий
      $newValue = self::checkValuesByType($value, $currentField, $fieldType);
      $oldValue = self::checkValuesByType($currentField['VALUE'], $currentField, $fieldType);
      if ($newValue == $oldValue) continue;
      $arResult[$key] = [
        "entityID" => $entityID,
        "entityType" => $entityTypeName,
        "changerUser" => $changedBy,
        "fieldName" => $fieldName,
        "fieldTitle" => $fieldTitle,
        "fieldType" => $fieldType,
        "newValCode" => $value,
        "oldValCode" => $currentField['VALUE'],
        "newValue" => $newValue,
        "oldValue" => $oldValue
      ];
    }
    return $arResult;
  }

  public static function checkValuesByType($value, $currentField, $fieldType)
  {
    $result;
    $fieldID = $currentField['ID'];
    $class = $currentField['USER_TYPE']['CLASS_NAME'];
    switch ($fieldType) {
      case 'boolean':
        if(is_array($value)) {
          $result = (in_array(1, $value)) ? Loc::getMessage('BOOLEAN_TRUE') : Loc::getMessage('BOOLEAN_FALSE');
        } else {
          $result = (!empty($value)) ? Loc::getMessage('BOOLEAN_TRUE') : Loc::getMessage('BOOLEAN_FALSE');
        }
        break;
      case 'enumeration':
        $result = self::getResultByClass($class, $currentField, $value);
        break;
      case 'iblock_section':
        $result = self::getResultByClass($class, $currentField, $value);
        break;
      case 'iblock_element':
        $result = self::getResultByClass($class, $currentField, $value);
        break;
      case 'hlblock':
        $result = self::getResultByClass($class, $currentField, $value);
        break;
      default:
        $result = $value;
        break;
    }
    if(is_array($result)) $result = implode(', ', $result);
    if(empty($result)) $result = " ";
    return $result;
  }

  private static function getNameFromArray($arLists, $value)
  {
    $result;
    if(!is_array($value)) {
      $result = $arLists[$value];
    } else {
      foreach ($value as $val) {
        if(empty($val)) continue;
        $result[] = $arLists[$val];
      }
    }
    return $result;
  }

  private static function getResultByClass($class, $currentField, $value)
  {
    if (!method_exists($class, 'GetList')) return $value;
    $arResult = [];
    $lists = $class::getList($currentField);
    while ($row = $lists->Fetch()) {
      $arResult[$row['ID']] = ($row['NAME'])? $row['NAME'] : $row['VALUE'];
    }
    $result = self::getNameFromArray($arResult, $value);
    return $result;
  }

  public static function writeChanges($ufDataValues)
  {
    $eventClass = new \CCrmEvent();
    foreach ($ufDataValues as $value) {
      if(empty($value)) continue;
      if($value['newValue'] == $value['oldValue']) continue;
      $arMessages = [
        'ENTITY_FIELD' => $value['fieldName'],
        'EVENT_NAME' => $value['fieldTitle'],
        'EVENT_TEXT_1' => htmlspecialcharsbx($value['oldValue']),
        'EVENT_TEXT_2' => htmlspecialcharsbx($value['newValue']),
        'ENTITY_TYPE' => $value['entityType'],
        'ENTITY_ID' => $value['entityID'],
        'USER_ID' => $value['changerUser'],
        'EVENT_TYPE' => $value['changerUser']
      ];
      $sender = $eventClass->Add($arMessages);
    }
  }
}

?>
