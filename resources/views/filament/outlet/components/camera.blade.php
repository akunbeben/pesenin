<div
    class="w-full"
    wire:ignore
    x-data="{
        html5QrcodeScanner: null,
        resume: () => html5QrcodeScanner.resume(),
        clear: () => html5QrcodeScanner.clear(),
        reset: () => {
            lastResult = null
            countResults = 0
        },
        lastResult: null,
        countResults: 0,
        init: function () {
            // document.getElementById('scanner').style.width = window.screen.width - 20

            html5QrcodeScanner = new Html5QrcodeScanner('scanner', {
                fps: 10,
                qrbox: 250,
            });

            function onScanSuccess(decodedText, decodedResult) {
                if (decodedText !== this.lastResult) {
                    ++this.countResults;
                    this.lastResult = decodedText;

                    $wire.dispatch('scanned', { ref: decodedText });

                    html5QrcodeScanner.pause();
                }
            }

            html5QrcodeScanner.render(onScanSuccess);
        }
    }"
    x-on:resume.window="resume()"
    x-on:failed.window="reset()"
    x-on:close-modal.window="clear()"
>
    <div id="scanner" class="rounded-xl"></div>
</div>