self.importScripts('/typo3conf/ext/reserve/Resources/Public/JavaScript/jsQR.js');
self.addEventListener("message", message => {
    const { width, height, data } = message.data;
    self.postMessage(jsQR(data, width, height));
});