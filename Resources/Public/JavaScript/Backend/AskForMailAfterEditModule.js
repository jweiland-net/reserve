define(['TYPO3/CMS/Backend/Modal'], function(Modal) {
    var i = 0;
    for (i = 0; i < TYPO3.settings.reserve.modals.length; i++) {
        var configuration = {
            title: TYPO3.settings.reserve.modals[i].title,
            content: TYPO3.settings.reserve.modals[i].message,
            buttons: [
                {
                    text: 'Send mail',
                    name: 'compose-mail',
                    icon: 'content-elements-mailform',
                    active: true,
                    btnClass: 'btn-primary',
                    dataAttributes: {
                        uri: TYPO3.settings.reserve.modals[i].uri
                    },
                    trigger: function(event) {
                        Modal.currentModal.trigger('modal-dismiss');
                        showMailModal(event.target.parentElement.dataset['uri']);
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
            title: 'NOT TRANSLATED YET!: Compose mail for affected visitors...',
            content: uri,
            size: Modal.sizes.large,
        };
        Modal.advanced(configuration);
    }
});
