<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Processing · Tripistry</title>
        <script>
            (function () {
                try {
                    const stored = localStorage.getItem('tripistry-theme');
                    const prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
                    const theme = stored || (prefersDark ? 'dark' : 'light');
                    document.documentElement.setAttribute('data-theme', theme);
                } catch (e) {
                    document.documentElement.setAttribute('data-theme', 'light');
                }
            })();
        </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400;1,700&family=DM+Sans:wght@300;400;500;600&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-main:        #fdf6f0;
            --bg-secondary:   #f5ece4;
            --text-main:      #2a1f35;
            --text-soft:      rgba(42, 31, 53, 0.68);
            --text-muted:     rgba(42, 31, 53, 0.38);
            --coral:          #e8614a;
            --rose:           #c9516e;
            --amber:          #d97b3a;
            --blush:          #e8a89c;
            --indigo:         #3d4a7a;
            --slate:          #6b7db3;
            --dusk:           #8f6fa8;
            --ice:            #c3cde6;
            --gradient-main:  #e8614a;
            --gradient-warm:  #d97b3a;
            --font-display:   'Playfair Display', Georgia, serif;
            --font-body:      'DM Sans', system-ui, sans-serif;
            --font-code:      'DM Mono', monospace;
            --ease:           cubic-bezier(0.22, 1, 0.36, 1);
        }

        html[data-theme="dark"] {
            --bg-main:        #0f0c14;
            --bg-secondary:   #171120;
            --text-main:      rgba(255, 248, 244, 0.92);
            --text-soft:      rgba(255, 248, 244, 0.72);
            --text-muted:     rgba(255, 248, 244, 0.46);
            --coral:          #ff6b55;
            --rose:           #e35b83;
            --amber:          #ff9a4b;
            --blush:          #ffb7aa;
            --indigo:         #6d7ad6;
            --ice:            #cfd7ff;
            --gradient-main:  var(--coral);
            --gradient-warm:  var(--amber);
        }

        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            background: var(--bg-main);
            color: var(--text-main);
            font-family: var(--font-body);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .orb {
            position: fixed; border-radius: 50%;
            filter: blur(120px); pointer-events: none; z-index: 0;
        }
        .orb-1 {
            width: 500px; height: 500px;
            background: rgba(232, 168, 156, 0.35); opacity: 0.4;
            top: -160px; left: -120px;
            animation: drift1 22s ease-in-out infinite alternate;
        }
        .orb-2 {
            width: 380px; height: 380px;
            background: rgba(195, 205, 230, 0.35); opacity: 0.35;
            bottom: -100px; right: -80px;
            animation: drift2 28s ease-in-out infinite alternate;
        }
        .orb-3 {
            width: 300px; height: 300px;
            background: rgba(217, 123, 58, 0.18); opacity: 0.18;
            top: 40%; right: 10%;
            animation: drift3 19s ease-in-out infinite alternate;
        }
        @keyframes drift1 { from{transform:translate(0,0)} to{transform:translate(60px,50px)} }
        @keyframes drift2 { from{transform:translate(0,0)} to{transform:translate(-50px,-60px)} }
        @keyframes drift3 { from{transform:translate(0,0) scale(1)} to{transform:translate(-25px,18px) scale(1.12)} }

        .loading-wrap {
            position: relative; z-index: 1;
            display: flex; flex-direction: column;
            align-items: center;
            opacity: 0;
            animation: screenFadeIn 0.6s 0.1s var(--ease) forwards;
        }

        .loading-logo {
            font-family: var(--font-display);
            font-weight: 700; font-style: italic;
            font-size: 1.6rem; color: var(--coral);
            letter-spacing: -0.01em;
            margin-bottom: 3.5rem;
            display: flex; align-items: center; gap: 0.5rem;
            opacity: 0;
            animation: fadeUp 0.7s 0.3s var(--ease) forwards;
        }
        .logo-dot {
            width: 8px; height: 8px; border-radius: 50%;
            background: var(--rose);
            box-shadow: 0 0 10px var(--rose);
        }

        .steps {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            width: 340px;
        }

        .step {
            display: flex;
            align-items: flex-start;
            gap: 1.2rem;
            opacity: 0;
            transform: translateY(16px);
        }

        .step-1 { animation: fadeUp 0.65s 0.7s  var(--ease) forwards; }
        .step-2 { animation: fadeUp 0.65s 1.55s var(--ease) forwards; }
        .step-3 { animation: fadeUp 0.65s 2.4s  var(--ease) forwards; }
        .step-4 { animation: fadeUp 0.65s 3.25s var(--ease) forwards; }

        .step-track {
            display: flex;
            flex-direction: column;
            align-items: center;
            flex-shrink: 0;
            padding-top: 3px;
        }

        .step-dot {
            width: 22px; height: 22px;
            border-radius: 50%;
            border: 2px solid rgba(232, 97, 74, 0.25);
            background: var(--bg-main);
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
            transition: all 0.4s var(--ease);
            position: relative;
        }
        .step-dot::after {
            content: '';
            position: absolute;
            inset: -5px;
            border-radius: 50%;
            border: 1.5px solid transparent;
        }

        .step.active .step-dot {
            background: var(--coral);
            border-color: transparent;
            box-shadow: 0 0 0 5px rgba(232, 97, 74, 0.12);
        }
        .step.active .step-dot::after {
            border-color: rgba(232, 97, 74, 0.18);
            animation: ringPulse 1.4s ease-out infinite;
        }

        .step.done .step-dot {
            background: var(--amber);
            border-color: transparent;
        }
        .step.done .step-dot::before {
            content: '';
            width: 6px; height: 10px;
            border-right: 2px solid #fff;
            border-bottom: 2px solid #fff;
            transform: rotate(45deg) translate(-1px, -1px);
        }

        @keyframes ringPulse {
            0%   { transform: scale(1);   opacity: 0.7; }
            100% { transform: scale(1.7); opacity: 0; }
        }

        .step-connector {
            width: 1px;
            height: 52px;
            margin: 5px 0;
            background: transparent;
            position: relative;
            overflow: hidden;
        }
        .step-connector::before {
            content: '';
            position: absolute;
            left: 0; top: 0;
            width: 1px;
            height: 100%;
            background: rgba(232, 97, 74, 0.28);
            transform: scaleY(0);
            transform-origin: top;
        }

        .connector-1::before { animation: drawLine 0.5s 1.2s var(--ease) forwards; }
        .connector-2::before { animation: drawLine 0.5s 2.05s var(--ease) forwards; }
        .connector-3::before { animation: drawLine 0.5s 2.9s var(--ease) forwards; }

        @keyframes drawLine {
            from { transform: scaleY(0); }
            to   { transform: scaleY(1); }
        }

        .step-body {
            padding-bottom: 0;
        }

        .step-label {
            font-family: var(--font-code);
            font-size: 0.7rem;
            font-weight: 500;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: var(--text-muted);
            margin-bottom: 0.2rem;
            transition: color 0.4s;
        }
        .step.active .step-label,
        .step.done  .step-label { color: var(--coral); }

        .step-name {
            font-family: var(--font-display);
            font-style: italic;
            font-size: 1.35rem;
            font-weight: 400;
            color: var(--text-muted);
            line-height: 1.2;
            transition: color 0.4s var(--ease);
        }
        .step.active .step-name { color: var(--coral); }
        .step.done   .step-name { color: var(--text-soft); }

        .loading-footer {
            margin-top: 3.5rem;
            font-family: var(--font-code);
            font-size: 0.72rem;
            letter-spacing: 0.1em;
            color: var(--text-muted);
            text-transform: uppercase;
            opacity: 0;
            animation: fadeUp 0.6s 4.0s var(--ease) forwards;
            display: flex; align-items: center; gap: 0.6rem;
        }
        .loading-footer::before,
        .loading-footer::after {
            content: '';
            width: 24px; height: 1px;
            background: var(--text-muted);
        }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(16px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        @keyframes screenFadeIn {
            from { opacity: 0; }
            to   { opacity: 1; }
        }
    </style>
</head>
<body>

    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>
    <div class="orb orb-3"></div>

    <div class="loading-wrap">

        <div class="loading-logo">
            <span class="logo-dot"></span>
            Tripistry
        </div>

        <div class="steps">

            <div class="step step-1" id="s1">
                <div class="step-track">
                    <div class="step-dot"></div>
                    <div class="step-connector connector-1"></div>
                </div>
                <div class="step-body">
                    <div class="step-label">01</div>
                    <div class="step-name">Securing your reservation</div>
                </div>
            </div>

            <div class="step step-2" id="s2">
                <div class="step-track">
                    <div class="step-dot"></div>
                    <div class="step-connector connector-2"></div>
                </div>
                <div class="step-body">
                    <div class="step-label">02</div>
                    <div class="step-name">Joining the group expedition</div>
                </div>
            </div>

            <div class="step step-3" id="s3">
                <div class="step-track">
                    <div class="step-dot"></div>
                    <div class="step-connector connector-3"></div>
                </div>
                <div class="step-body">
                    <div class="step-label">03</div>
                    <div class="step-name">Generating mission intel</div>
                </div>
            </div>

            <div class="step step-4" id="s4">
                <div class="step-track">
                    <div class="step-dot"></div>
                </div>
                <div class="step-body">
                    <div class="step-label">04</div>
                    <div class="step-name">Finalising your itinerary</div>
                </div>
            </div>

        </div>

        <div class="loading-footer">Preparing your adventure</div>

    </div>

    <script>
        const timings = [
            { id: 's1', activeAt: 700  },
            { id: 's2', activeAt: 1550 },
            { id: 's3', activeAt: 2400 },
            { id: 's4', activeAt: 3250 },
        ];

        timings.forEach((t, i) => {
            setTimeout(() => {
                document.getElementById(t.id).classList.add('active');
            }, t.activeAt);

            if (i > 0) {
                setTimeout(() => {
                    document.getElementById(timings[i - 1].id).classList.remove('active');
                    document.getElementById(timings[i - 1].id).classList.add('done');
                }, t.activeAt);
            }
        });

        setTimeout(async () => {
            const formDataJson = sessionStorage.getItem('checkoutFormData');
            if (formDataJson) {
                try {
                    const formData = new FormData();
                    const data = JSON.parse(formDataJson);
                    Object.keys(data).forEach(key => {
                        formData.append(key, data[key]);
                    });
                    
                    const response = await fetch('/traveller/process_booking', {
                        method: 'POST',
                        body: formData
                    });
                    
                    if (response.ok) {
                        window.location.href = '/traveller/dashboard?success=booking_confirmed';
                    } else {
                        window.location.href = '/traveller/packages?error=booking_failed';
                    }
                } catch (error) {
                    console.error('Booking submission error:', error);
                    window.location.href = '/traveller/packages?error=booking_failed';
                }
            }
        }, 8500);
    </script>

</body>
</html>