{{--
    Script polling station — SHARED Kitchen & Barista (Modul 6/7).
    Pemanggil: @include('station.partials.queue-script', ['pollUrl' => route('...'), 'containerId' => '...-queue'])
    POLL_INTERVAL hanya ada DI SINI — satu-satunya tempat mengubah interval untuk semua station.
--}}
<script>
    // ===== Konfigurasi polling station =====
    const POLL_INTERVAL = 3000; // ms
    const POLL_URL = @js($pollUrl);
    const QUEUE_CONTAINER_ID = @js($containerId);

    document.addEventListener('alpine:init', () => {
        /**
         * Komponen antrian station. Partial refresh:
         * - fetch JSON tiap POLL_INTERVAL, kirim signature milik klien;
         * - server hanya menyertakan `html` bila signature berbeda → tanpa perubahan
         *   tidak ada update DOM sama sekali (posisi scroll otomatis tetap);
         * - saat berubah: hanya innerHTML container antrian yang di-swap;
         * - lonceng berbunyi SATU KALI hanya saat last_id naik (ada item baru).
         */
        Alpine.data('stationQueue', ({ lastId, signature, unprintedCount }) => ({
            lastId,
            signature,
            unprintedCount,
            soundOn: localStorage.getItem('station_sound') !== 'off',
            clock: '--:--',
            polling: false,

            init() {
                this.tick();
                setInterval(() => this.tick(), 1000);
                setInterval(() => this.poll(), POLL_INTERVAL);
            },

            tick() {
                this.clock = new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
            },

            toggleSound() {
                this.soundOn = !this.soundOn;
                localStorage.setItem('station_sound', this.soundOn ? 'on' : 'off');
                if (this.soundOn) this.bell(); // gesture user → unlock audio context + tes bunyi
            },

            async poll() {
                if (this.polling) return; // jangan tumpang tindih bila respons lambat
                this.polling = true;
                try {
                    const url = `${POLL_URL}?signature=${encodeURIComponent(this.signature)}`;
                    const res = await fetch(url, { headers: { Accept: 'application/json' } });
                    if (!res.ok) return;
                    const data = await res.json();

                    // Notifikasi SATU KALI hanya untuk item baru (last_id naik).
                    if (data.last_id > this.lastId && this.soundOn) this.bell();
                    this.lastId = data.last_id;
                    this.unprintedCount = data.unprinted_count;

                    // Tanpa perubahan → server tidak mengirim html → tidak ada update DOM.
                    if (data.html !== undefined) {
                        document.getElementById(QUEUE_CONTAINER_ID).innerHTML = data.html;
                        this.signature = data.signature;
                    }
                } catch (e) {
                    // Gangguan jaringan lokal — abaikan, coba lagi di interval berikutnya.
                } finally {
                    this.polling = false;
                }
            },

            // Lonceng "ding-dong" via Web Audio API — tanpa file audio/package baru.
            bell() {
                const Ctx = window.AudioContext || window.webkitAudioContext;
                if (!Ctx) return;
                if (!this._ctx) this._ctx = new Ctx();
                const ctx = this._ctx;
                if (ctx.state === 'suspended') ctx.resume();

                const now = ctx.currentTime;
                [{ delay: 0, freq: 1319 }, { delay: 0.18, freq: 1568 }].forEach(({ delay, freq }) => {
                    const osc  = ctx.createOscillator();
                    const gain = ctx.createGain();
                    osc.type = 'sine';
                    osc.frequency.value = freq;
                    gain.gain.setValueAtTime(0.001, now + delay);
                    gain.gain.exponentialRampToValueAtTime(0.4, now + delay + 0.02);
                    gain.gain.exponentialRampToValueAtTime(0.001, now + delay + 0.9);
                    osc.connect(gain).connect(ctx.destination);
                    osc.start(now + delay);
                    osc.stop(now + delay + 1);
                });
            },
        }));
    });
</script>
