self.addEventListener("message", message => {
    if (message.data.basePath) {
      // Import jsQR dynamically using the base path
      self.importScripts(message.data.basePath + '/jsQR.js');

      // Setup the message handler to process QR code data
      self.addEventListener("message", function(message) {
        const { width, height, data } = message.data;
        self.postMessage(jsQR(data, width, height));
      });

      // Once the base path is set and script is imported, remove this listener
      self.removeEventListener('message', message.callee);
    }
});
