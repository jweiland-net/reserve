<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/reserve.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Reserve\Form\Element;

use TYPO3\CMS\Backend\Form\Element\AbstractFormElement;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * Form element to render a QR code preview in TCA using
 * type=none, renderType=reserveQrCodePreview
 */
class QrCodePreviewElement extends AbstractFormElement
{
    public function render(): array
    {
        $resultArray = $this->initializeResultArray();
        $resultArray['html'] = sprintf(
            '<div class="alert alert-info">%s</div><p><button class="btn btn-default generate-qr-code" data-facility="%d">%s</button><p class="qr-code-preview" data-facility="%d" ></p>',
            LocalizationUtility::translate('LLL:EXT:reserve/Resources/Private/Language/locallang_db.xlf:tx_reserve_domain_model_facility.qr_code_preview.notice'),
            $this->data['vanillaUid'],
            LocalizationUtility::translate('LLL:EXT:reserve/Resources/Private/Language/locallang_db.xlf:tx_reserve_domain_model_facility.qr_code_preview.button'),
            $this->data['vanillaUid']
        );
        $resultArray['additionalJavaScriptPost'][] = <<<JAVASCRIPT
var elements = document.getElementsByClassName('generate-qr-code');

function generateQrCode(event)
{
    event.preventDefault();
    var facilityUid = this.getAttribute('data-facility');
    var previewDiv = document.querySelector('.qr-code-preview[data-facility="' + facilityUid + '"]');
    var xhttp = new XMLHttpRequest();
    previewDiv.innerHTML = '<div class="progress" style="max-width: 200px;">' +
        '  <div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%">' +
        '    <span class="sr-only">Please wait</span>' +
        '  </div>' +
        '</div>';
    xhttp.onreadystatechange = function() {
        if (this.readyState === 4 && this.status === 200) {
            responseObject = JSON.parse(this.responseText);
            previewDiv.innerHTML = '<img src="' + responseObject.qrCode + '" />';
        }
    };
    xhttp.open('GET', TYPO3.settings.ajaxUrls['tx_reserve_qr_code_preview'] + '&facility=' + facilityUid, true);
    xhttp.send();
}

for (var i = 0; i < elements.length; i++) {
    elements[i].addEventListener('click', generateQrCode);
}
JAVASCRIPT;

        return $resultArray;
    }
}
