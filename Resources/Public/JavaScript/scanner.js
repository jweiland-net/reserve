$(() => {
    if (!document.getElementsByClassName('tx-reserve-management').length) {
       return;
    }

    let reserveConf = document.getElementById('reserve-conf');

    let config = {};

    if (reserveConf) {
        config = JSON.parse(reserveConf.getAttribute('data-conf'));
    }
    let $datatable = $('#datatable');
    let reservations = $datatable.DataTable({
        ...config.datatables,
        ...{"lengthChange": false},
        ...$datatable.data('config')
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

    $datatable.on('click', 'a[data-action="scan"]', function(event) {
        event.preventDefault();

        if (activeScan) {
            return;
        }
        activeScan = true;

        let url = '';
        if ($('#entireOrder').is(':checked')) {
            url = $(this).siblings('a[data-scan="all"]')[0].href;
        } else {
            url = this.href;
        }

        let request = $.ajax({
            url: url
        });

        request.done((response) => {
            let classes = ''

            if (response.status.error) {
                classes += 'error';
            }

            let $additionalElements = null;

            if (response.codes.length) {
                $additionalElements =
                    $('<div>').append(
                        $('<p style="font-size: 1.5rem">').text(response.codes.length + ' ' + config.language.reservations)
                    );
            }

            createModal(
                response.status.title,
                response.status.message,
                classes,
                $additionalElements
            );
        });
    });

    function createModal(title, message, classes = '', $additionalElement = null)
    {
        let $modal = $('<div class="reserve modal">');

        let $title = $('<h3>').text(title);
        let $message = $('<p>').text(message);
        let $close = $('<button>').css('float', 'right').width(200).height(40).text('close').on('click', () => {
            $.modal.close();
        });

        $modal.addClass(classes);

        $modal.append($title).append($message).append($additionalElement).append($close);

        $modal.appendTo('body').modal();
    }

    function initializeScanner(video) {
        if (!navigator.mediaDevices) {
            return;
        }

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
})
