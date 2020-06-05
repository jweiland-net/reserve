define(['TYPO3/CMS/Backend/Modal'], function(Modal) {
    if (typeof TYPO3.settings.reserve.showModal !== 'undefined') {
        var configuration = {
            title: TYPO3.settings.reserve.showModal.title,
            content: TYPO3.settings.reserve.showModal.message,
            buttons: [
                {
                    text: TYPO3.lang['reserve.modal.periodAskForMail.button.writeMail'],
                    name: 'write-mail',
                    icon: 'content-elements-mailform',
                    active: true,
                    btnClass: 'btn-primary',
                    trigger: function() {
                        // Open new model after current model is fully destroyed!
                        // Otherwise the modal buttons won't work
                        Modal.currentModal.on('modal-destroyed', function (event) {
                            showMailModal(TYPO3.settings.reserve.showModal.uri);
                        });
                        Modal.currentModal.trigger('modal-dismiss');
                    }
                }
            ]
        };
        Modal.advanced(configuration);
    }

    function showMailModal(uri)
    {
        var configuration = {
            type: Modal.types.iframe,
            content: uri,
            size: Modal.sizes.large,
        };
        Modal.advanced(configuration);
        Modal.currentModal.find('.modal-iframe').on('load', function (event) {
            if (event.target.contentWindow.location.href.includes('#txReserveCloseModal')) {
                Modal.currentModal.trigger('modal-dismiss');
            }
        });
    }
});
