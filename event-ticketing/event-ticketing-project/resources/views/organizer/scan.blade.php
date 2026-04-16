<x-app-layout>
    <div class="main-content">
        <!-- Header -->
        <header class="app-header">
            <div class="header-container">
                <div>
                    <h2 class="lowercase"
                        style="font-size: 1.875rem; font-weight: 800; color: var(--text-dark); margin: 0;">
                        scan tickets.
                    </h2>
                    <p class="lowercase" style="color: var(--text-muted); font-size: 0.875rem; margin-top: 0.25rem;">
                        validate attendee tickets via QR code
                    </p>
                </div>
            </div>
        </header>

        <!-- Event Selector -->
        <section style="margin-bottom: 2rem;">
            <form method="GET" action="{{ route('organizer.scan') }}" id="event-selector-form">
                <div
                    style="background: white; border-radius: var(--radius-2xl); padding: 1.5rem 2rem; box-shadow: var(--shadow-sm); border: 1px solid var(--gray-border);">
                    <label class="uppercase tracking-widest font-bold"
                        style="font-size: 10px; color: var(--text-muted); display: block; margin-bottom: 0.75rem;">Select
                        Event</label>
                    <div style="display: flex; gap: 1rem; align-items: center; flex-wrap: wrap;">
                        <select name="event" id="event-select"
                            onchange="document.getElementById('event-selector-form').submit()"
                            style="flex: 1; min-width: 250px; padding: 0.75rem 1rem; border-radius: 0.75rem; border: 2px solid var(--gray-border); font-size: 0.875rem; font-weight: 600; color: var(--text-dark); background: white; outline: none; transition: border-color 0.2s;"
                            onfocus="this.style.borderColor='#111'"
                            onblur="this.style.borderColor='var(--gray-border)'">
                            <option value="">— choose an event —</option>
                            @foreach($events as $event)
                                <option value="{{ $event->id_event }}" {{ $selectedEventId == $event->id_event ? 'selected' : '' }}>
                                    {{ $event->title }} — {{ $event->date->format('d M Y') }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </form>
        </section>

        @if($selectedEvent)
            <!-- Stats Cards -->
            <section class="stats-grid" style="margin-bottom: 2rem;">
                <div class="stat-card" style="background: linear-gradient(135deg, #134e4a 0%, #115e59 100%); color: white;">
                    <span class="text-xs font-bold uppercase tracking-widest" style="color: #99f6e4;">Total Tickets</span>
                    <h4 style="color: white; font-size: 2rem;" id="stat-total">{{ number_format($stats['total']) }}</h4>
                    <p class="text-xs font-bold uppercase tracking-widest" style="color: #ccfbf1; margin: 0;">issued
                        tickets.</p>
                </div>

                <div class="stat-card">
                    <span class="text-xs font-bold uppercase tracking-widest text-muted"
                        style="color: var(--text-muted);">Checked In</span>
                    <h4 id="stat-scanned">{{ number_format($stats['scanned']) }}</h4>
                    <p class="text-xs font-bold uppercase tracking-widest" style="color: #16a34a; margin: 0;">scanned
                        tickets.</p>
                </div>

                <div class="stat-card">
                    <span class="text-xs font-bold uppercase tracking-widest text-muted"
                        style="color: var(--text-muted);">Remaining</span>
                    <h4 id="stat-remaining">{{ number_format($stats['remaining']) }}</h4>
                    <p class="text-xs font-bold uppercase tracking-widest" style="color: #ca8a04; margin: 0;">waiting to
                        check-in.</p>
                </div>
            </section>

            <!-- Scanner + Result Section -->
            <section style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 2rem;">
                <!-- QR Scanner -->
                <div
                    style="background: white; border-radius: var(--radius-2xl); padding: 1.75rem 2rem; box-shadow: var(--shadow-sm); border: 1px solid var(--gray-border);">
                    <h3 class="lowercase"
                        style="font-size: 1.125rem; font-weight: 700; color: var(--text-dark); margin: 0 0 1.25rem 0;">
                        <span style="display: inline-flex; align-items: center; gap: 0.5rem;">
                            <svg style="width: 1.25rem; height: 1.25rem;" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z">
                                </path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            scan QR code
                        </span>
                    </h3>

                    <!-- Camera Scanner Area -->
                    <div style="position: relative; margin-bottom: 1rem;">
                        <div id="qr-reader"
                            style="width: 100%; border-radius: 1rem; overflow: hidden; background: #f8f8f8; min-height: 280px;">
                        </div>
                        <!-- Cooldown Overlay -->
                        <div id="scan-cooldown-overlay"
                            style="display: none; position: absolute; inset: 0; border-radius: 1rem; background: rgba(0,0,0,0.6); flex-direction: column; align-items: center; justify-content: center; gap: 0.5rem; pointer-events: none; z-index: 10;">
                            <span id="scan-cooldown-msg" style="font-size: 1.5rem; font-weight: 800; color: white; text-align: center; margin-bottom: 0.5rem;"></span>
                            <span id="scan-cooldown-count"
                                style="font-size: 3.5rem; font-weight: 900; color: white; line-height: 1;"></span>
                            <span id="scan-cooldown-label"
                                style="font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em; color: rgba(255,255,255,0.75);">next
                                scan in...</span>
                        </div>
                    </div>

                    <div style="display: flex; gap: 0.5rem; margin-bottom: 1.25rem;">
                        <button type="button" id="btn-start-camera" onclick="startScanner()"
                            style="flex: 1; padding: 0.65rem 1rem; border-radius: 0.75rem; border: 2px solid #111; background: #111; color: white; font-size: 0.8rem; font-weight: 700; cursor: pointer; text-transform: lowercase; letter-spacing: 0.03em; transition: all 0.2s;">
                            start camera
                        </button>
                        <button type="button" id="btn-stop-camera" onclick="stopScanner()"
                            style="display: none; flex: 1; padding: 0.65rem 1rem; border-radius: 0.75rem; border: 2px solid var(--gray-border); background: white; color: var(--text-dark); font-size: 0.8rem; font-weight: 700; cursor: pointer; text-transform: lowercase; letter-spacing: 0.03em; transition: all 0.2s;">
                            stop camera
                        </button>
                    </div>

                    <!-- Divider -->
                    <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1.25rem;">
                        <div style="flex: 1; height: 1px; background: var(--gray-border);"></div>
                        <span class="uppercase tracking-widest font-bold"
                            style="font-size: 10px; color: var(--text-muted);">or enter code manually</span>
                        <div style="flex: 1; height: 1px; background: var(--gray-border);"></div>
                    </div>

                    <!-- Manual Input -->
                    <div style="display: flex; gap: 0.75rem;">
                        <input type="text" id="manual-code" placeholder="enter ticket code..."
                            style="flex: 1; padding: 0.75rem 1rem; border-radius: 0.75rem; border: 2px solid var(--gray-border); font-size: 0.875rem; font-weight: 600; color: var(--text-dark); outline: none; transition: border-color 0.2s;"
                            onfocus="this.style.borderColor='#111'" onblur="this.style.borderColor='var(--gray-border)'"
                            onkeypress="if(event.key === 'Enter') processManualScan()">
                        <button type="button" onclick="processManualScan()"
                            style="padding: 0.75rem 1.5rem; border-radius: 0.75rem; border: none; background: #111; color: white; font-size: 0.8rem; font-weight: 700; cursor: pointer; text-transform: lowercase; letter-spacing: 0.03em; transition: all 0.2s; white-space: nowrap;"
                            onmouseenter="this.style.background='#333'" onmouseleave="this.style.background='#111'">
                            check in
                        </button>
                    </div>
                </div>

                <!-- Scan Result -->
                <div
                    style="background: white; border-radius: var(--radius-2xl); padding: 1.75rem 2rem; box-shadow: var(--shadow-sm); border: 1px solid var(--gray-border); display: flex; flex-direction: column;">
                    <h3 class="lowercase"
                        style="font-size: 1.125rem; font-weight: 700; color: var(--text-dark); margin: 0 0 1.25rem 0;">
                        scan result
                    </h3>

                    <!-- Default State -->
                    <div id="result-empty"
                        style="flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; padding: 2rem;">
                        <div
                            style="width: 5rem; height: 5rem; border-radius: 50%; background: var(--gray-light); display: flex; align-items: center; justify-content: center; margin-bottom: 1.25rem;">
                            <svg style="width: 2.5rem; height: 2.5rem; color: #bbb;" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z">
                                </path>
                            </svg>
                        </div>
                        <p class="lowercase"
                            style="font-weight: 700; color: var(--text-muted); margin: 0; font-size: 0.875rem;">
                            scan a QR code or enter a ticket code to check in an attendee
                        </p>
                    </div>

                    <!-- Loading State -->
                    <div id="result-loading"
                        style="flex: 1; display: none; flex-direction: column; align-items: center; justify-content: center; text-align: center; padding: 2rem;">
                        <div
                            style="width: 3rem; height: 3rem; border: 3px solid var(--gray-light); border-top-color: #111; border-radius: 50%; animation: spin 0.8s linear infinite; margin-bottom: 1rem;">
                        </div>
                        <p class="lowercase"
                            style="font-weight: 700; color: var(--text-muted); margin: 0; font-size: 0.875rem;">
                            processing...</p>
                    </div>

                    <!-- Success State -->
                    <div id="result-success" style="flex: 1; display: none; flex-direction: column;">
                        <div
                            style="background: #f0fdf4; border: 2px solid #bbf7d0; border-radius: 1rem; padding: 1.25rem; margin-bottom: 1rem; text-align: center;">
                            <div
                                style="width: 3.5rem; height: 3.5rem; border-radius: 50%; background: #16a34a; display: flex; align-items: center; justify-content: center; margin: 0 auto 0.75rem;">
                                <svg style="width: 2rem; height: 2rem; color: white;" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                            <p class="lowercase font-bold" style="font-size: 1.1rem; color: #15803d; margin: 0;">check-in
                                successful!</p>
                        </div>
                        <div id="result-success-data" style="display: flex; flex-direction: column; gap: 0.75rem;"></div>
                    </div>

                    <!-- Error State -->
                    <div id="result-error" style="flex: 1; display: none; flex-direction: column;">
                        <div id="result-error-banner"
                            style="border-radius: 1rem; padding: 1.25rem; margin-bottom: 1rem; text-align: center;">
                            <div id="result-error-icon"
                                style="width: 3.5rem; height: 3.5rem; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 0.75rem;">
                            </div>
                            <p id="result-error-message" class="lowercase font-bold" style="font-size: 1rem; margin: 0;">
                            </p>
                        </div>
                        <div id="result-error-data" style="display: flex; flex-direction: column; gap: 0.75rem;"></div>
                    </div>
                </div>
            </section>

            <!-- Responsive override for mobile -->
            <style>
                @media (max-width: 900px) {
                    section[style*="grid-template-columns: 1fr 1fr"] {
                        grid-template-columns: 1fr !important;
                    }
                }
            </style>

            <!-- Recent Scans History -->
            <section style="margin-bottom: 2rem;">
                <div
                    style="background: white; border-radius: var(--radius-2xl); padding: 1.75rem 2rem; box-shadow: var(--shadow-sm); border: 1px solid var(--gray-border);">
                    <h3 class="lowercase"
                        style="font-size: 1.25rem; font-weight: 700; color: var(--text-dark); margin: 0 0 1.25rem 0;">
                        recent check-ins
                    </h3>

                    <div style="overflow-x: auto;">
                        <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.875rem;"
                            id="scan-history-table">
                            <thead>
                                <tr>
                                    <th
                                        style="padding: 0.6rem 1rem 0.6rem 0; color: var(--text-muted); font-weight: 700; text-transform: uppercase; font-size: 10px; letter-spacing: 0.08em; border-bottom: 2px solid var(--gray-light); white-space: nowrap;">
                                        #</th>
                                    <th
                                        style="padding: 0.6rem 1rem; color: var(--text-muted); font-weight: 700; text-transform: uppercase; font-size: 10px; letter-spacing: 0.08em; border-bottom: 2px solid var(--gray-light); white-space: nowrap;">
                                        Attendee</th>
                                    <th
                                        style="padding: 0.6rem 1rem; color: var(--text-muted); font-weight: 700; text-transform: uppercase; font-size: 10px; letter-spacing: 0.08em; border-bottom: 2px solid var(--gray-light); white-space: nowrap;">
                                        Email</th>
                                    <th
                                        style="padding: 0.6rem 1rem; color: var(--text-muted); font-weight: 700; text-transform: uppercase; font-size: 10px; letter-spacing: 0.08em; border-bottom: 2px solid var(--gray-light); white-space: nowrap;">
                                        Ticket Type</th>
                                    <th
                                        style="padding: 0.6rem 1rem; color: var(--text-muted); font-weight: 700; text-transform: uppercase; font-size: 10px; letter-spacing: 0.08em; border-bottom: 2px solid var(--gray-light); white-space: nowrap;">
                                        Code</th>
                                    <th
                                        style="padding: 0.6rem 0 0.6rem 1rem; color: var(--text-muted); font-weight: 700; text-transform: uppercase; font-size: 10px; letter-spacing: 0.08em; border-bottom: 2px solid var(--gray-light); text-align: right; white-space: nowrap;">
                                        Scanned At</th>
                                </tr>
                            </thead>
                            <tbody id="scan-history-body">
                                @forelse ($recentScans as $index => $scan)
                                    <tr>
                                        <td
                                            style="padding: 0.9rem 1rem 0.9rem 0; border-bottom: 1px solid var(--gray-light); font-weight: 600; color: var(--text-muted);">
                                            {{ $index + 1 }}</td>
                                        <td
                                            style="padding: 0.9rem 1rem; border-bottom: 1px solid var(--gray-light); font-weight: 700; color: var(--text-dark);">
                                            {{ $scan->attendee_name ?? $scan->user->name ?? '-' }}</td>
                                        <td
                                            style="padding: 0.9rem 1rem; border-bottom: 1px solid var(--gray-light); color: var(--text-muted); font-weight: 600;">
                                            {{ $scan->attendee_email ?? $scan->user->email ?? '-' }}</td>
                                        <td style="padding: 0.9rem 1rem; border-bottom: 1px solid var(--gray-light);">
                                            <span
                                                style="font-size: 10px; padding: 3px 8px; background: var(--gray-light); border-radius: 4px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-dark);">{{ $scan->eventTicketType->ticketType->name ?? '-' }}</span>
                                        </td>
                                        <td
                                            style="padding: 0.9rem 1rem; border-bottom: 1px solid var(--gray-light); font-family: monospace; font-size: 0.8rem; color: var(--text-muted);">
                                            {{ $scan->unique_code }}</td>
                                        <td
                                            style="padding: 0.9rem 0 0.9rem 1rem; border-bottom: 1px solid var(--gray-light); text-align: right; font-weight: 600; color: var(--text-dark);">
                                            {{ $scan->scanned_at->format('d M Y, H:i') }}</td>
                                    </tr>
                                @empty
                                    <tr id="empty-history-row">
                                        <td colspan="6"
                                            style="padding: 2.5rem 0; text-align: center; color: var(--text-muted); font-size: 12px; font-weight: 600;">
                                            no check-ins yet for this event.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        @elseif($events->isEmpty())
            <div
                style="background: white; padding: 4rem; border-radius: var(--radius-2xl); text-align: center; border: 1px solid var(--gray-border);">
                <div
                    style="width: 5rem; height: 5rem; border-radius: 50%; background: var(--gray-light); display: flex; align-items: center; justify-content: center; margin: 0 auto 1.25rem;">
                    <svg style="width: 2.5rem; height: 2.5rem; color: #bbb;" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                        </path>
                    </svg>
                </div>
                <p class="lowercase" style="font-weight: 700; color: var(--text-muted); margin: 0;">no published events
                    found. create an event first.</p>
            </div>
        @else
            <div
                style="background: white; padding: 4rem; border-radius: var(--radius-2xl); text-align: center; border: 1px solid var(--gray-border);">
                <div
                    style="width: 5rem; height: 5rem; border-radius: 50%; background: var(--gray-light); display: flex; align-items: center; justify-content: center; margin: 0 auto 1.25rem;">
                    <svg style="width: 2.5rem; height: 2.5rem; color: #bbb;" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122">
                        </path>
                    </svg>
                </div>
                <p class="lowercase" style="font-weight: 700; color: var(--text-muted); margin: 0;">select an event above to
                    start scanning tickets.</p>
            </div>
        @endif
    </div>

    <style>
        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .scan-result-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.65rem 0;
            border-bottom: 1px solid var(--gray-light);
            animation: fadeInUp 0.3s ease;
        }

        .scan-result-row:last-child {
            border-bottom: none;
        }

        .scan-result-label {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--text-muted);
        }

        .scan-result-value {
            font-size: 0.875rem;
            font-weight: 700;
            color: var(--text-dark);
            text-align: right;
        }

        #qr-reader video {
            border-radius: 0.75rem !important;
        }

        #qr-reader {
            border: none !important;
        }

        #qr-reader img[alt="Info icon"] {
            display: none !important;
        }

        #qr-reader__dashboard_section_swaplink {
            color: #111 !important;
            text-decoration: underline !important;
            font-weight: 600 !important;
        }
    </style>

    @push('scripts')
        <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
        <script>
            let html5QrCode = null;
            let isScanning = false;
            const eventId = {{ $selectedEventId ?? 'null' }};
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            // ── Cooldown State ───────────────────────────────
            let scanCooldownActive = false;
            let cooldownTimerInterval = null;

            // ── Web Audio Beep ───────────────────────────────
            const audioCtx = new (window.AudioContext || window.webkitAudioContext)();

            function playBeep(type) {
                // type: 'success' | 'warning' | 'error'
                const configs = {
                    success: [{ freq: 880, dur: 0.08 }, { freq: 1100, dur: 0.12 }],
                    warning: [{ freq: 660, dur: 0.15 }, { freq: 440, dur: 0.2 }],
                    error: [{ freq: 300, dur: 0.1 }, { freq: 220, dur: 0.18 }],
                };
                const notes = configs[type] || configs.success;
                let startTime = audioCtx.currentTime;
                notes.forEach(note => {
                    const osc = audioCtx.createOscillator();
                    const gain = audioCtx.createGain();
                    osc.connect(gain);
                    gain.connect(audioCtx.destination);
                    osc.type = 'sine';
                    osc.frequency.setValueAtTime(note.freq, startTime);
                    gain.gain.setValueAtTime(0.35, startTime);
                    gain.gain.exponentialRampToValueAtTime(0.001, startTime + note.dur);
                    osc.start(startTime);
                    osc.stop(startTime + note.dur);
                    startTime += note.dur + 0.02;
                });
            }

            function startProcessingOverlay() {
                scanCooldownActive = true;
                const overlay = document.getElementById('scan-cooldown-overlay');
                document.getElementById('scan-cooldown-msg').textContent = 'Processing...';
                document.getElementById('scan-cooldown-msg').style.color = '#ccc';
                document.getElementById('scan-cooldown-count').style.display = 'none';
                document.getElementById('scan-cooldown-label').style.display = 'none';
                if (overlay) {
                    overlay.style.display = 'flex';
                    overlay.style.background = 'rgba(0,0,0,0.4)';
                }
            }

            // ── Cooldown Overlay ─────────────────────────────
            function startCooldown(statusText, statusColor) {
                scanCooldownActive = true;
                const overlay = document.getElementById('scan-cooldown-overlay');
                const msgEl = document.getElementById('scan-cooldown-msg');
                const countEl = document.getElementById('scan-cooldown-count');
                const labelEl = document.getElementById('scan-cooldown-label');
                
                if (overlay) {
                    overlay.style.display = 'flex';
                    overlay.style.background = 'rgba(0,0,0,0.8)';
                    msgEl.textContent = statusText;
                    msgEl.style.color = statusColor || '#fff';
                    countEl.style.display = 'none';
                    labelEl.style.display = 'none';
                }

                // Show status message for 1.5 seconds, then show countdown
                setTimeout(() => {
                    if (overlay) {
                        msgEl.textContent = '';
                        countEl.style.display = 'block';
                        labelEl.style.display = 'block';
                    }
                    let remaining = 3;
                    if (countEl) countEl.textContent = remaining;

                    cooldownTimerInterval = setInterval(() => {
                        remaining--;
                        if (countEl) countEl.textContent = remaining;
                        if (remaining <= 0) {
                            clearInterval(cooldownTimerInterval);
                            scanCooldownActive = false;
                            if (overlay) overlay.style.display = 'none';
                        }
                    }, 1000);
                }, 1500);
            }

            // ── QR Scanner ─────────────────────────────────
            function startScanner() {
                if (!eventId) return;

                html5QrCode = new Html5Qrcode("qr-reader");
                html5QrCode.start(
                    { facingMode: "environment" },
                    { fps: 10, qrbox: { width: 250, height: 250 } },
                    onScanSuccess,
                    onScanError
                ).then(() => {
                    isScanning = true;
                    document.getElementById('btn-start-camera').style.display = 'none';
                    document.getElementById('btn-stop-camera').style.display = 'block';
                }).catch(err => {
                    console.error('Camera error:', err);
                    Swal.fire({
                        icon: 'error',
                        title: 'camera error.',
                        text: 'Could not access camera. Please check permissions or use manual input.',
                        confirmButtonColor: '#111',
                        customClass: {
                            popup: 'rounded-[2rem] p-6',
                            title: 'text-2xl font-bold lowercase tracking-tight text-[#444444]',
                            htmlContainer: 'text-[#777777] text-sm font-medium lowercase'
                        }
                    });
                });
            }

            function stopScanner() {
                if (html5QrCode && isScanning) {
                    html5QrCode.stop().then(() => {
                        isScanning = false;
                        document.getElementById('btn-start-camera').style.display = 'block';
                        document.getElementById('btn-stop-camera').style.display = 'none';
                        document.getElementById('qr-reader').innerHTML = '';
                    });
                }
            }

            let lastScannedCode = '';
            let lastScanTime = 0;

            function onScanSuccess(decodedText) {
                // Debounce — hindari scan double
                const now = Date.now();
                if (decodedText === lastScannedCode && (now - lastScanTime) < 3000) return;
                lastScannedCode = decodedText;
                lastScanTime = now;

                processScan(decodedText);
            }

            function onScanError(errorMessage) {
                // Silent — ignore continuous scan errors
            }

            // ── Manual Input ─────────────────────────────────
            function processManualScan() {
                const code = document.getElementById('manual-code').value.trim();
                if (!code) return;
                processScan(code);
                document.getElementById('manual-code').value = '';
            }

            // ── Process Scan (AJAX) ─────────────────────────
            function processScan(uniqueCode) {
                if (!eventId) return;
                if (scanCooldownActive) return; // blok jika masih dalam cooldown

                showState('loading');
                startProcessingOverlay();

                fetch('{{ route("organizer.scan.process") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        unique_code: uniqueCode,
                        event_id: eventId,
                    }),
                })
                    .then(res => res.json().then(data => ({ status: res.status, data })))
                    .then(({ status, data }) => {
                        if (data.status === 'success') {
                            showSuccess(data.attendee);
                            updateStats(data.stats);
                            refreshHistory();
                            startCooldown('Scan Success!', '#4ade80');
                        } else {
                            showError(data);
                            let msg = data.type === 'already_scanned' ? 'Already Scanned!' : 'Invalid Ticket!';
                            let color = data.type === 'already_scanned' ? '#fcd34d' : '#f87171';
                            startCooldown(msg, color);
                        }
                    })
                    .catch(err => {
                        console.error('Scan error:', err);
                        showError({ type: 'network', message: 'Network error. Please try again.' });
                        startCooldown('Network Error', '#f87171');
                    });
            }

            // ── UI State Management ─────────────────────────
            function showState(state) {
                document.getElementById('result-empty').style.display = 'none';
                document.getElementById('result-loading').style.display = 'none';
                document.getElementById('result-success').style.display = 'none';
                document.getElementById('result-error').style.display = 'none';

                if (state === 'empty') document.getElementById('result-empty').style.display = 'flex';
                if (state === 'loading') document.getElementById('result-loading').style.display = 'flex';
                if (state === 'success') document.getElementById('result-success').style.display = 'flex';
                if (state === 'error') document.getElementById('result-error').style.display = 'flex';
            }

            function showSuccess(attendee) {
                showState('success');
                const container = document.getElementById('result-success-data');
                container.innerHTML = buildResultRows([
                    { label: 'Attendee', value: attendee.name },
                    { label: 'Email', value: attendee.email },
                    { label: 'Phone', value: attendee.phone || '-' },
                    { label: 'Ticket Type', value: attendee.ticket_type },
                    { label: 'Code', value: attendee.unique_code, mono: true },
                    { label: 'Checked In', value: attendee.scanned_at },
                ]);

                playBeep('success');
                try { navigator.vibrate && navigator.vibrate(200); } catch (e) { }
            }

            function showError(data) {
                showState('error');
                const banner = document.getElementById('result-error-banner');
                const icon = document.getElementById('result-error-icon');
                const message = document.getElementById('result-error-message');
                const dataContainer = document.getElementById('result-error-data');

                if (data.type === 'already_scanned') {
                    banner.style.background = '#fef3c7';
                    banner.style.border = '2px solid #fde68a';
                    icon.style.background = '#f59e0b';
                    icon.innerHTML = '<svg style="width:2rem;height:2rem;color:white;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"></path></svg>';
                    message.style.color = '#92400e';
                    message.textContent = data.message;

                    if (data.attendee) {
                        dataContainer.innerHTML = buildResultRows([
                            { label: 'Attendee', value: data.attendee.name },
                            { label: 'Email', value: data.attendee.email },
                            { label: 'Ticket Type', value: data.attendee.ticket_type },
                            { label: 'Already Scanned', value: data.attendee.scanned_at },
                        ]);
                    } else {
                        dataContainer.innerHTML = '';
                    }
                } else {
                    banner.style.background = '#fef2f2';
                    banner.style.border = '2px solid #fecaca';
                    icon.style.background = '#ef4444';
                    icon.innerHTML = '<svg style="width:2rem;height:2rem;color:white;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>';
                    message.style.color = '#991b1b';
                    message.textContent = data.message;
                    dataContainer.innerHTML = '';
                }

                playBeep(data.type === 'already_scanned' ? 'warning' : 'error');
                try { navigator.vibrate && navigator.vibrate([100, 50, 100]); } catch (e) { }
            }

            function buildResultRows(rows) {
                return rows.map(r =>
                    `<div class="scan-result-row">
                        <span class="scan-result-label">${r.label}</span>
                        <span class="scan-result-value" ${r.mono ? 'style="font-family:monospace;font-size:0.8rem;"' : ''}>${r.value}</span>
                    </div>`
                ).join('');
            }

            function updateStats(stats) {
                if (!stats) return;
                const totalEl = document.getElementById('stat-total');
                const scannedEl = document.getElementById('stat-scanned');
                const remainingEl = document.getElementById('stat-remaining');
                if (totalEl) totalEl.textContent = stats.total.toLocaleString();
                if (scannedEl) scannedEl.textContent = stats.scanned.toLocaleString();
                if (remainingEl) remainingEl.textContent = stats.remaining.toLocaleString();
            }

            function refreshHistory() {
                if (!eventId) return;
                fetch(`/organizer/scan/history/${eventId}`, {
                    headers: { 'Accept': 'application/json' }
                })
                    .then(res => res.json())
                    .then(data => {
                        if (data.status === 'success') {
                            const tbody = document.getElementById('scan-history-body');
                            if (data.data.length === 0) {
                                tbody.innerHTML = `<tr><td colspan="6" style="padding:2.5rem 0;text-align:center;color:var(--text-muted);font-size:12px;font-weight:600;">no check-ins yet for this event.</td></tr>`;
                                return;
                            }
                            tbody.innerHTML = data.data.map((scan, i) =>
                                `<tr style="animation: fadeInUp 0.3s ease ${i * 0.05}s both;">
                                <td style="padding:0.9rem 1rem 0.9rem 0;border-bottom:1px solid var(--gray-light);font-weight:600;color:var(--text-muted);">${i + 1}</td>
                                <td style="padding:0.9rem 1rem;border-bottom:1px solid var(--gray-light);font-weight:700;color:var(--text-dark);">${scan.name}</td>
                                <td style="padding:0.9rem 1rem;border-bottom:1px solid var(--gray-light);color:var(--text-muted);font-weight:600;">${scan.email}</td>
                                <td style="padding:0.9rem 1rem;border-bottom:1px solid var(--gray-light);">
                                    <span style="font-size:10px;padding:3px 8px;background:var(--gray-light);border-radius:4px;font-weight:700;text-transform:uppercase;letter-spacing:0.05em;color:var(--text-dark);">${scan.ticket_type}</span>
                                </td>
                                <td style="padding:0.9rem 1rem;border-bottom:1px solid var(--gray-light);font-family:monospace;font-size:0.8rem;color:var(--text-muted);">${scan.unique_code}</td>
                                <td style="padding:0.9rem 0 0.9rem 1rem;border-bottom:1px solid var(--gray-light);text-align:right;font-weight:600;color:var(--text-dark);">${scan.scanned_at}</td>
                            </tr>`
                            ).join('');
                        }
                    })
                    .catch(err => console.error('History refresh error:', err));
            }
        </script>
    @endpush
</x-app-layout>