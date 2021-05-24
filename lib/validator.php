<?php
namespace belyaev\ufchangehistory;
use Bitrix\Main\GroupTable;
use Bitrix\Main\Config\Option;
/**
 *
 */
class Validator
{

  private static $moduleName = "belyaev.ufchangehistory";
  public $groupList = [];
  public $sourcesList = [];
  public $statusList = [];

  function __construct()
  {
    $getGroupList = GroupTable::getList(array(
      'select'  => array('ID', 'NAME'), // выберем название, идентификатор, символьный код, сортировку
      'filter'  => array('!ID' => '1'), // все группы, кроме основной группы администраторов
      'order'   => array('ID' => "DESC")
    ));
    $this->makeSelectorArray($getGroupList);
    $this->sourcesList = \CCrmStatus::GetStatusList("SOURCE");
    $this->statusList = \CCrmStatus::GetStatusList("STATUS");
    if(!is_array($this->groupList) || !is_array($this->sourcesList)) die();
  }

  function generateChannelOptions($prefix, $prefixName, $entity = "sourcesList")
  {
    $result[] = "Запрет на изменеие {$prefixName} для групп пользователей:";
    foreach ($this->groupList as $key => $value) {
      $result[] = [
        "belyaev_ufchangehistory_{$prefix}_group_{$key}",
        $value,
        null,
        array('multiselectbox', $this->$entity)
      ];
    }
    return $result;
  }

  function makeSelectorArray($object, $ID = "ID", $NAME = "NAME")
  {
    while ($arGroup = $object->Fetch()) {
      $this->groupList[$arGroup['ID']] = $arGroup['NAME'];
    }
  }

  public static function getBlockedValue()
  {
    $options = Option::getForModule(self::$moduleName);
    $result = [];
    foreach ($options as $key => $value) {
      if (is_null($value)) continue;
      if (strpos($key, "belyaev_ufchangehistory") === false) continue;
      $result[$key] = explode(",", $value);
    }
    return $result;
  }

  public static function patternName($channelNumber, int $groupNumber)
  {
    return "belyaev_ufchangehistory_{$channelNumber}_group_{$groupNumber}";
  }
}

?>
