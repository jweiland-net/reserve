$(document).ready(function() {
    let config = JSON.parse(document.getElementById('reserve-conf').getAttribute('data-conf'));

    let reservations = $('#datatable').DataTable({...config.datatables, ...{"lengthChange": false}});
    let canvasElement = document.getElementById('canvas');
    let activeScan = false;
    let video = null;

    if (canvasElement) {
        video = document.createElement('video');
        initializeScanner(video);
    }

    $('body').on($.modal.BEFORE_CLOSE, function() {
        activeScan = false;
        video.play();
    });

    function createModal(title, message, classes = '')
    {
        let $modal = $('<div class="modal">');

        let $title = $('<h3>').text(title);
        let $message = $('<p>').text(message);

        $modal.addClass(classes);

        $modal.append($title).append($message);

        $modal.appendTo('body').modal();
    }

    $('a[data-action="scan"]').on('click', function(event) {
        event.preventDefault();

        if (activeScan) {
            return;
        }
        activeScan = true;

        let request = $.ajax({
            url: this.href
        });

        request.done((response) => {
            let classes = ''

            if (response.status.error) {
                classes += 'error';
            }
            createModal(
                response.status.title,
                response.status.message,
                classes
            );
        });
    });

    function initializeScanner(video) {
        let canvas = canvasElement.getContext('2d');
        let loadingMessage = document.getElementById('loadingMessage');

        let startTime = 0;
        let codeInImage = null;

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

        function tick(timestamp) {
            if (!startTime) {
                startTime = timestamp;
            }

            let delta = timestamp - startTime;

            if (video.readyState === video.HAVE_ENOUGH_DATA) {
                loadingMessage.hidden = true;
                canvasElement.hidden = false;

                canvasElement.height = video.videoHeight;
                canvasElement.width = video.videoWidth;
                canvas.drawImage(video, 0, 0, canvasElement.width, canvasElement.height);

                if (codeInImage) {
                    drawLine(codeInImage.location.topLeftCorner, codeInImage.location.topRightCorner, '#3BFF58');
                    drawLine(codeInImage.location.topRightCorner, codeInImage.location.bottomRightCorner, '#3BFF58');
                    drawLine(codeInImage.location.bottomRightCorner, codeInImage.location.bottomLeftCorner, '#3BFF58');
                    drawLine(codeInImage.location.bottomLeftCorner, codeInImage.location.topLeftCorner, '#3BFF58');
                }

                if (!video.paused && delta > 500) {
                    startTime = timestamp;
                    let imageData = canvas.getImageData(0, 0, canvasElement.width, canvasElement.height);
                    codeInImage = jsQR(imageData.data, imageData.width, imageData.height, {
                        inversionAttempts: 'dontInvert',
                    });
                    if (codeInImage) {
                        video.pause();

                        reservations.search(codeInImage.data).draw();

                        let $scan = $('tr[data-code="'+ codeInImage.data + '"]').find('a[data-action="scan"]');

                        if ($scan.length) {
                            $scan.trigger('click');
                        } else {
                            createModal(
                                config.language.status.code_not_found.title,
                                config.language.status.code_not_found.message,
                                'error'
                            );
                        }
                    }
                }
            } else {
                loadingMessage.innerText = '⌛ ' + config.language.loading_video;
            }

            requestAnimationFrame(tick);
        }
    }
});