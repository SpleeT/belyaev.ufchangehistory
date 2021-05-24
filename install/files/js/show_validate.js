BX.ready(function () {
  BX.addCustomEvent('onAjaxSuccess', BX.delegate(init, this));

  function init(arParams)
  {
    if(typeof arParams.STATUS_VALIDATER == "undefined") return;
    var popup = BX.PopupWindowManager.create("status-validate-message", null, {
      content: "<h2>"+ arParams.STATUS_VALIDATER +"</h2><h4>Изменения не сохранены</h4>",
      titleBar: "Валидация",
      darkMode: false,
      autoHide: true,
      lightShadow : true,
      closeIcon : true,
      closeByEsc : true,
      lightShadow: true,
      angle: true,
      buttons: [
        new BX.PopupWindowButton({
            text: "Больше так не буду!",
            id: 'save-btn',
            className: 'ui-btn ui-btn-success',
            events:
                {
                    click: function() {  this.popupWindow.close();}
                }
        })
      ]
    });

    popup.show();
  }

})
