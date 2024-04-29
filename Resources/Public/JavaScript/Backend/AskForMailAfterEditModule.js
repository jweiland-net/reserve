define(['TYPO3/CMS/Backend/Modal'], function (Modal) {
  if (typeof TYPO3.settings.reserve.showModal !== 'undefined') {
    const modal = Modal.advanced({
      title: TYPO3.settings.reserve.showModal.title,
      content: TYPO3.settings.reserve.showModal.message,
      buttons: [
        {
          text: TYPO3.lang['reserve.modal.button.writeMail'],
          name: 'write-mail',
          icon: 'content-elements-mailform',
          active: true,
          btnClass: 'btn-primary',
          trigger: function(event, modal) {
            modal.hideModal();
            showMailModal(TYPO3.settings.reserve.showModal.uri);
          }
        }
      ]
    });
    modal.addEventListener('typo3-modal-hidden', function() {
      // showMailModal(TYPO3.settings.reserve.showModal.uri);
    });
  }

  function showMailModal (uri) {
    const modal = Modal.advanced({
      type: Modal.types.iframe,
      content: uri,
      size: Modal.sizes.large,
      callback: modal => {
        modal.querySelectorAll('.modal-iframe').forEach(iFrameWindow => {
          iFrameWindow.addEventListener('load', event => {
            if (event.target.contentWindow.location.href.includes('#txReserveCloseModal')) {
              modal.hideModal();
            }
          })
        });
      }
    });
  }
});
