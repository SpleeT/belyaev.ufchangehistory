# belyaev.ufchangehistory [Для коробочной версии]
# История изменения пользовательских полей для сущностей CRM


## Особенности:
- Добавлет изменение пользовательских полей в ТАБ истории сущностей CRM Bitrix
- Поддерживает работу с множественными полями, инфоблоками и HL блоками.
- Создает событие перед внесением изменений в пользовательские поля (позволяет реализовать валидацию)

## Установка

- Сохраните репозиторий и перенесите папку belyaev.ufchangehistory в {Ваша директория Bitrix}/local/modules.
- Установите модуль в Адмимистративной панели -> Marketplace -> Установленные решения -> История изменений пользовательских полей (belyaev.ufchangehistory)

## Настройка валидаций и работа с событиями
Для работы с событиями в данном примере используется init.php [.../local/php_interface/].

#### Доступные события:
- OnLEADentityUfUpdate
- OnDEALentityUfUpdate
- OnCONTACTentityUfUpdate
- OnCOMPANYentityUfUpdate

Подключение события
```php
$eventManager->AddEventHandler("belyaev.ufchangehistory", "OnLEADentityUfUpdate", "ufLeadValidator");
function ufLeadValidator(\Bitrix\Main\Event $event) {
	$params = $event->getParameters();
	$needFieldID = "UF_CRM_1585892822";
	$throwAjaxError = function ($errorMsg) {
		$GLOBALS['APPLICATION']->RestartBuffer();
		Header('Content-Type: application/x-javascript; charset=' . LANG_CHARSET);
		echo CUtil::PhpToJSObject(array('ERROR' => $errorMsg));			
		if (!defined('PUBLIC_AJAX_MODE')) define('PUBLIC_AJAX_MODE', true);
		require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/epilog_after.php');
		die();
	};
	if(!$params[$needFieldID]) return true;
	$textField = $params[$needFieldID];
	if ($textField['changerUser'] == 640 && $textField['newValue'] == "Тест") {
		$throwAjaxError("Ой, сработала валидация для поля {$needFieldID}");
	}
}
```

Пример параметров события
```js
{
"UF_CRM_1585892822":{
"entityID":172930,
"entityType":"LEAD",
"changerUser":640,
"fieldName":"UF_CRM_1585892822",
"fieldTitle":"Комментарий клиента",
"fieldType":"string",
"newValCode":"Тест",
"oldValCode":null,
"newValue":"Тест",
"oldValue":" "
},
"UF_CRM_1594377433":{
"entityID":172930,
"entityType":"LEAD",
"changerUser":640,
"fieldName":"UF_CRM_1594377433",
"fieldTitle":"Диагноз",
"fieldType":"iblock_element",
"newValCode":"2261",
"oldValCode":"2278",
"newValue":"ЖКТ",
"oldValue":"Другое"
},
"UF_CRM_1599544321":{
"entityID":172930,
"entityType":"LEAD",
"changerUser":640,
"fieldName":"UF_CRM_1599544321",
"fieldTitle":"Тип прозвона",
"fieldType":"enumeration",
"newValCode":"488",
"oldValCode":"487",
"newValue":"Исходящий",
"oldValue":"Входящий"
},
```

Работа валидации
![Screenshot](http://i.piccy.info/i9/226c26053c10ef41ff79de54ec0c279e/1619822375/18914/1427303/Valydatsyia.jpg)
Пример истории пользовательских полей
![Screenshot](http://i.piccy.info/i9/99d94eaee2d542da3a1f7aece901b12c/1619822695/38638/1427303/43878Ystoryia.jpg)
## Контакты
TG: @VladimirBelyaev73
