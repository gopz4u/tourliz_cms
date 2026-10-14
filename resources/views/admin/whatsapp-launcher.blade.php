<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#25d366">
    <title>Opening WhatsApp - Tourliz</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; min-height: 100vh; display: grid; place-items: center; padding: 24px; background: #f4f6fa; color: #0f172a; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif; }
        .card { width: min(100%, 420px); padding: 32px 24px; text-align: center; background: #fff; border: 1px solid #e2e8f0; border-radius: 20px; box-shadow: 0 16px 40px rgba(15, 23, 42, .1); }
        .icon { width: 64px; height: 64px; display: grid; place-items: center; margin: 0 auto 18px; border-radius: 20px; background: #25d366; color: #fff; font-size: 34px; font-weight: 700; }
        h1 { margin: 0 0 8px; font-size: 1.35rem; }
        p { margin: 0 0 22px; color: #64748b; line-height: 1.55; }
        a { display: block; padding: 12px 16px; border-radius: 11px; text-decoration: none; font-weight: 700; }
        .open { background: #25d366; color: #fff; }
        .back { margin-top: 10px; color: #475569; background: #f1f5f9; }
    </style>
</head>
<body>
    <main class="card">
        <div class="icon">W</div>
        <h1>Opening WhatsApp</h1>
        <p>Your prepared Tourliz message will open directly in the WhatsApp app.</p>
        <a class="open" href="{{ $nativeUrl }}">Open WhatsApp</a>
        <a class="back" href="javascript:history.back()">Back to Tourliz</a>
    </main>
    <script>
        window.location.href = @json($nativeUrl);
    </script>
</body>
</html>
