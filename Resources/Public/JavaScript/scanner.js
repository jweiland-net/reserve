$(document).ready(function() {
    let reservations = $('#reservations').DataTable();
    let video = document.createElement('video');
    let canvasElement = document.getElementById('canvas');
    let canvas = canvasElement.getContext('2d');
    let loadingMessage = document.getElementById('loadingMessage');

    function drawLine(begin, end, color) {
        canvas.beginPath();
        canvas.moveTo(begin.x, begin.y);
        canvas.lineTo(end.x, end.y);
        canvas.lineWidth = 4;
        canvas.strokeStyle = color;
        canvas.stroke();
    }

    navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } }).then(function(stream) {
        video.srcObject = stream;
        video.setAttribute('playsinline', true); // required to tell iOS safari we don't want fullscreen
        video.play();
        requestAnimationFrame(tick);
    });

    function tick() {
        if (video.readyState === video.HAVE_ENOUGH_DATA) {
            loadingMessage.hidden = true;
            canvasElement.hidden = false;

            canvasElement.height = video.videoHeight;
            canvasElement.width = video.videoWidth;
            canvas.drawImage(video, 0, 0, canvasElement.width, canvasElement.height);
            let imageData = canvas.getImageData(0, 0, canvasElement.width, canvasElement.height);
            let code = jsQR(imageData.data, imageData.width, imageData.height, {
                inversionAttempts: 'dontInvert',
            });
            if (code) {
                drawLine(code.location.topLeftCorner, code.location.topRightCorner, '#3BFF58');
                drawLine(code.location.topRightCorner, code.location.bottomRightCorner, '#3BFF58');
                drawLine(code.location.bottomRightCorner, code.location.bottomLeftCorner, '#3BFF58');
                drawLine(code.location.bottomLeftCorner, code.location.topLeftCorner, '#3BFF58');
                reservations.search(code.data).draw();
                let $scan = $('tr[data-code="'+ code.data + '"]').find('a[data-action="scan"]');
                $scan.trigger('click');
            }
        } else {
            loadingMessage.innerText = '⌛ Loading video...';
        }
        requestAnimationFrame(tick);
    }

    let activeScan = false;
    $('a[data-action="scan"]').on('click', function(event) {
        if (activeScan) {
            return;
        }

        activeScan = true;
        event.preventDefault();
        let request = $.ajax({
            url: this.href
        });

        request.done((response) => {
            alert(response.status.message);
        }).complete(() => {
            activeScan = false;
        });
    });
});