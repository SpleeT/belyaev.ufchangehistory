<?php
  namespace belyaev\ufchangehistory;
  defined('B_PROLOG_INCLUDED') || die;
  use Bitrix\Main\Config\Option;
  use Bitrix\Main\Localization\Loc;
  use Bitrix\Main\Loader;
  use Bitrix\Main\LoaderException;

  if (!Loader::includeModule('belyaev.ufchangehistory')) {
      throw new LoaderException(Loc::getMessage('CANT_INCLUDE_MODULE'));
  }

  $moduleID = 'belyaev.ufchangehistory';

  $validator = new Validator();

  $aTabs = array(
    array(
        'DIV' => 'belyaev_ufchangehistory_options_ch1',
        'TAB' => Loc::getMessage("CHANNEL_1"),
        'OPTIONS' => $validator->generateChannelOptions("ch1", Loc::getMessage("CHANNEL_1"))
      ),
    array(
        'DIV' => 'belyaev_ufchangehistory_options_ch2',
        'TAB' => Loc::getMessage("CHANNEL_2"),
        'OPTIONS' => $validator->generateChannelOptions("ch2", Loc::getMessage("CHANNEL_2"))
      ),
    array(
        'DIV' => 'belyaev_ufchangehistory_options_ch3',
        'TAB' => Loc::getMessage("CHANNEL_3"),
        'OPTIONS' => $validator->generateChannelOptions("ch3", Loc::getMessage("CHANNEL_3"))
      ),
    array(
        'DIV' => 'belyaev_ufchangehistory_options_l_status',
        'TAB' => Loc::getMessage("LEAD_STATUS"),
        'OPTIONS' => $validator->generateChannelOptions("l_status", Loc::getMessage("LEAD_STATUS"), "statusList")
      )
  );
  if ($USER->IsAdmin()) {
      if (check_bitrix_sessid() && strlen($_POST['save']) > 0) {
          foreach ($aTabs as $aTab) {
              __AdmSettingsSaveOptions($moduleID, $aTab['OPTIONS']);
          }
          LocalRedirect($APPLICATION->GetCurPageParam());
      }
  }
  $tabControl = new \CAdminTabControl('tabControl', $aTabs);
?>
<form method="POST" action="">
    <? $tabControl->Begin();

    foreach ($aTabs as $aTab) {
        $tabControl->BeginNextTab();
        __AdmSettingsDrawList($moduleID, $aTab['OPTIONS']);
    }

    $tabControl->Buttons(array('btnApply' => false, 'btnCancel' => false, 'btnSaveAndAdd' => false)); ?>

    <?= bitrix_sessid_post(); ?>
    <? $tabControl->End(); ?>
</form>
