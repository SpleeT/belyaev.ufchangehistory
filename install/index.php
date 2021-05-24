<?php
defined('B_PROLOG_INCLUDED') || die;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Loader;
use Bitrix\Main\ModuleManager;
use Bitrix\Main\EventManager;
use belyaev\ufchangehistory\handler\Handlers;
use belyaev\ufchangehistory\events\Update;

class belyaev_ufchangehistory extends CModule
{
  var $MODULE_ID = "belyaev.ufchangehistory";
  var $MODULE_VERSION;
  var $MODULE_VERSION_DATE;
  var $MODULE_NAME;
  var $MODULE_DESCRIPTION;

  function __construct()
  {
    $arModuleVersion = array();

    include(dirname(__FILE__) . '/version.php');

    if (is_array($arModuleVersion) && array_key_exists("VERSION", $arModuleVersion))
    {
      $this->MODULE_VERSION = $arModuleVersion["VERSION"];
      $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
    }
      $this->MODULE_NAME = Loc::getMessage('BELYAEV_UFCGHS.MODULE_NAME');
      $this->MODULE_DESCRIPTION = Loc::getMessage('BELYAEV_UFCGHS.MODULE_NAME');
      $this->PARTNER_NAME = Loc::getMessage('BELYAEV_UFCGHS.PARTNER_NAME');
      $this->PARTNER_URI = Loc::getMessage('BELYAEV_UFCGHS.PARTNER_URI');
    }

    function DoInstall()
    {
      ModuleManager::registerModule($this->MODULE_ID);
      Loader::includeModule($this->MODULE_ID);
      Option::set($this->MODULE_ID, 'VERSION_DB', $this->versionToInt());
      $this->InstallFiles();
      $this->InstallHandlers();
    }

    function DoUninstall()
    {
      Option::delete($this->MODULE_ID, ["name" => 'VERSION_DB']);
      ModuleManager::unRegisterModule($this->MODULE_ID);
      $this->UnInstallFiles();
      $this->UnInstallHandlers();
    }

    function InstallFiles()
    {
      CopyDirFiles(
          __DIR__ . '/files/js',
          $_SERVER["DOCUMENT_ROOT"] . '/local/js/' .$this->MODULE_ID,
          true,
          true
      );
    }

    function UnInstallFiles()
    {
        DeleteDirFilesEx('/local/js/' . $this->MODULE_ID);
    }

    function InstallHandlers()
    {
      $eventManager = EventManager::getInstance();
      $eventManager->registerEventHandlerCompatible(
          'main',
          'OnBeforeEndBufferContent',
          $this->MODULE_ID,
          Handlers::class,
          'addAssetOnPage'
      );
      $eventManager->registerEventHandlerCompatible(
          'crm',
          'OnBeforeCrmCompanyUpdate',
          $this->MODULE_ID,
          Handlers::class,
          'companyEvent'
      );
      $eventManager->registerEventHandlerCompatible(
          'crm',
          'OnBeforeCrmContactUpdate',
          $this->MODULE_ID,
          Handlers::class,
          'contactEvent'
      );
      $eventManager->registerEventHandlerCompatible(
          'crm',
          'OnBeforeCrmDealUpdate',
          $this->MODULE_ID,
          Handlers::class,
          'dealEvent'
      );
      $eventManager->registerEventHandlerCompatible(
          'crm',
          'OnBeforeCrmLeadUpdate',
          $this->MODULE_ID,
          Handlers::class,
          'leadEvent'
      );
    }

    function UnInstallHandlers()
    {
      $eventManager = EventManager::getInstance();
      $eventManager->unRegisterEventHandler(
          'main',
          'OnBeforeEndBufferContent',
          $this->MODULE_ID,
          Handlers::class,
          'addAssetOnPage'
      );
      $eventManager->unRegisterEventHandler(
          'crm',
          'OnBeforeCrmCompanyUpdate',
          $this->MODULE_ID,
          Handlers::class,
          'companyEvent'
      );
      $eventManager->unRegisterEventHandler(
          'crm',
          'OnBeforeCrmContactUpdate',
          $this->MODULE_ID,
          Handlers::class,
          'contactEvent'
      );
      $eventManager->unRegisterEventHandler(
          'crm',
          'OnBeforeCrmDealUpdate',
          $this->MODULE_ID,
          Handlers::class,
          'dealEvent'
      );
      $eventManager->unRegisterEventHandler(
          'crm',
          'OnBeforeCrmLeadUpdate',
          $this->MODULE_ID,
          Handlers::class,
          'leadEvent'
      );
    }

    private function versionToInt()
    {
        return intval(preg_replace('/[^0-9]+/i', '', $this->MODULE_VERSION_DATE));
    }
}

?>
