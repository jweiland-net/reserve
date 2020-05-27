$(document).ready(function() {
    let config = JSON.parse(document.getElementById('reserve-conf').getAttribute('data-conf'));

    let reservations = $('#datatable').DataTable({
        ...config.datatables,
        ...{"lengthChange": false}
    });
    let canvasElement = document.getElementById('canvas');
    let activeScan = false;
    let video = null;
    let codeInImage = null;

    if (canvasElement) {
        video = document.createElement('video');
        initializeScanner(video);
    }

    $('body').on($.modal.BEFORE_CLOSE, function() {
        activeScan = false;
    });

    function createModal(title, message, classes = '')
    {
        let $modal = $('<div class="modal">');

        let $title = $('<h3>').text(title);
        let $message = $('<p>').text(message);
        let $close = $('<button>').css('float', 'right').width(200).height(40).text('close').on('click', () => {
            $.modal.close();
        });

        $modal.addClass(classes);

        $modal.append($title).append($message).append($close);

        $modal.appendTo('body').modal();
    }

    $('#datatable').on('click', 'a[data-action="scan"]', function(event) {
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

        function drawLine(begin, end, color) {
            canvas.beginPath();
            canvas.moveTo(begin.x, begin.y);
            canvas.lineTo(end.x, end.y);
            canvas.lineWidth = 4;
            canvas.strokeStyle = color;
            canvas.stroke();
        }

        navigator.mediaDevices.getUserMedia({
            video: { facingMode: 'environment' },
            width: screen.width
        }).then(function(stream) {
            video.srcObject = stream;
            video.setAttribute('autoplay', true);
            video.setAttribute('muted', true);
            video.setAttribute('playsinline', true);
            video.onloadedmetadata = function(e) {
                video.play();
            };
            requestAnimationFrame(tick);
        });

        let qrReader = new Worker(`/typo3conf/ext/reserve/Resources/Public/JavaScript/qrReader.js`);
        let qrReaderReady = true;

        function tick(timestamp) {
            if (!startTime) {
                startTime = timestamp;
            }

            let delta = timestamp - startTime;

            if (video.readyState === video.HAVE_ENOUGH_DATA) {
                loadingMessage.hidden = true;
                canvasElement.hidden = false;

                let scale = video.videoWidth / canvasElement.parentNode.parentElement.offsetWidth;
                canvasElement.width = canvasElement.parentNode.parentElement.offsetWidth;
                canvasElement.height = video.videoHeight / scale;
                canvas.drawImage(video, 0, 0, canvasElement.width, canvasElement.height);
                canvasElement.style.display = 'block';
                canvasElement.style.margin = 'auto';

                if (codeInImage) {
                    drawLine(codeInImage.location.topLeftCorner, codeInImage.location.topRightCorner, '#3BFF58');
                    drawLine(codeInImage.location.topRightCorner, codeInImage.location.bottomRightCorner, '#3BFF58');
                    drawLine(codeInImage.location.bottomRightCorner, codeInImage.location.bottomLeftCorner, '#3BFF58');
                    drawLine(codeInImage.location.bottomLeftCorner, codeInImage.location.topLeftCorner, '#3BFF58');
                }

                if (delta > 300) {
                    startTime = timestamp;

                    if (qrReaderReady && !activeScan) {
                        qrReaderReady = false;
                        let imageData = canvas.getImageData(0, 0, canvasElement.width, canvasElement.height);
                        qrReader.postMessage(imageData, [imageData.data.buffer]);
                    }
                }
            } else {
                loadingMessage.innerText = '⌛ ' + config.language.loading_video;
            }

            qrReader.onmessage = function(message) {
                codeInImage = message.data;

                if (codeInImage) {
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
                qrReaderReady = true;
            }

            requestAnimationFrame(tick);
        }
    }
});