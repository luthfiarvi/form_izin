<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lampiran</title>
    <style>
        :root {
            --overlay: rgba(15, 23, 42, 0.65);
            --card: #ffffff;
            --text: #0f172a;
            --muted: #475569;
            --accent: #22c55e;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px 16px;
            font-family: system-ui, -apple-system, "Segoe UI", sans-serif;
            background: linear-gradient(135deg, #0f172a, #0b3b35, #0f172a);
            backdrop-filter: blur(8px);
        }
        .backdrop {
            position: fixed;
            inset: 0;
            background: var(--overlay);
            backdrop-filter: blur(8px);
            z-index: 1;
        }
        .modal {
            position: relative;
            z-index: 2;
            background: var(--card);
            width: min(1100px, 100%);
            max-height: 90vh;
            border-radius: 18px;
            box-shadow: 0 25px 60px rgba(0,0,0,0.35);
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }
        .header {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 16px;
            border-bottom: 1px solid rgba(15,23,42,0.08);
            background: #f8fafc;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 10px 14px;
            border-radius: 12px;
            border: 1px solid rgba(15,23,42,0.12);
            background: #ffffff;
            color: var(--text);
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            transition: transform 0.15s ease, box-shadow 0.15s ease, background 0.15s ease;
        }
        .btn:hover { transform: translateY(-1px); box-shadow: 0 10px 20px rgba(0,0,0,0.08); }
        .btn.primary { background: var(--accent); color: #ffffff; border-color: transparent; }
        .btn.primary:hover { box-shadow: 0 12px 24px rgba(34,197,94,0.35); }
        .title {
            margin-left: auto;
            color: var(--muted);
            font-size: 13px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 50%;
        }
        .viewer {
            flex: 1;
            background: #0b1221;
            overflow: auto;
            padding: 16px;
        }
        .viewer img, .viewer iframe, .viewer embed {
            display: block;
            margin: 0 auto;
            max-width: 100%;
            height: auto;
            border-radius: 12px;
            background: #0b1221;
            border: none;
        }
        .close {
            position: absolute;
            top: 10px; right: 12px;
            font-size: 22px;
            color: #475569;
            background: transparent;
            border: none;
            cursor: pointer;
        }
    </style>
</head>
@php
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    $isImage = in_array($ext, ['jpg','jpeg','png','gif','webp','bmp']);
@endphp
<body>
    <div class="backdrop"></div>
    <div class="modal">
        <button class="close" aria-label="Tutup" onclick="goBack()">×</button>
        <div class="header">
            <button class="btn" onclick="goBack()" type="button">← Kembali</button>
            <a class="btn primary" href="{{ $rawUrl }}&download=1" download>⬇️ Unduh</a>
            <div class="title" title="{{ $filename }}">{{ $filename }}</div>
        </div>
        <div class="viewer">
            @if($isImage)
                <img src="{{ $rawUrl }}" alt="Lampiran">
            @else
                <iframe src="{{ $rawUrl }}" title="Lampiran"></iframe>
            @endif
        </div>
    </div>

    <script>
        function goBack() {
            if (window.history.length > 1) {
                window.history.back();
            } else {
                window.close();
            }
        }
    </script>
</body>
</html>
