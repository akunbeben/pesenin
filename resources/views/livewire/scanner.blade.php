<div
    class="flex flex-col gap-2.5 items-center justify-center h-screen max-w-xl px-4 mx-auto"
    wire:ignore
    x-data="{
        html5QrcodeScanner: null,
        clear: () => html5QrcodeScanner.clear(),
        lastResult: null,
        countResults: 0,
        init: function () {
            html5QrcodeScanner = new Html5QrcodeScanner('scanner', {
                fps: 10,
                qrbox: 250,
            });

            function onScanSuccess(decodedText, decodedResult) {
                if (decodedText !== this.lastResult) {
                    ++this.countResults;
                    this.lastResult = decodedText;

                    $wire.dispatch('check-payload', { payload: decodedText });
                }
            }

            html5QrcodeScanner.render(onScanSuccess);
        }
    }"
    x-on:livewire:navigating.window="clear()"
>
    <div id="scanner" class="rounded-xl"></div>
    <div x-ref="result"></div>
</div>