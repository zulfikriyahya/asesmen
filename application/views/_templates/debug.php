<!DOCTYPE html>
<html lang="id">

<head>
    <title>DEBUG</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Lexend:wght@300;400;500;600;700&display=swap">
    <style>
        *,
        *::before,
        *::after {
            box-sizing: border-box;
        }

        body {
            background: #0a0c10;
            background-image:
                radial-gradient(ellipse at 20% 50%, rgba(99, 102, 241, 0.07) 0%, transparent 60%),
                radial-gradient(ellipse at 80% 20%, rgba(139, 92, 246, 0.05) 0%, transparent 50%);
            color: rgba(255, 255, 255, 0.65);
            font-family: 'Lexend', monospace;
            padding: 1.75rem;
            margin: 0;
            min-height: 100vh;
        }

        .dbg-pre {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.07);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            border-radius: 10px;
            padding: 1.25rem;
            font-size: .8rem;
            font-family: monospace;
            overflow: auto;
            margin-bottom: 1.5rem;
            color: rgba(255, 255, 255, 0.6);
        }

        .dbg-card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.07);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-radius: 12px;
            overflow: hidden;
            margin-bottom: 1.5rem;
        }

        .dbg-card-header {
            background: rgba(255, 255, 255, 0.04);
            padding: .75rem 1.25rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid rgba(255, 255, 255, 0.06);
        }

        .dbg-card-header h5 {
            margin: 0;
            font-size: .78rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .6px;
        }

        .dbg-card-header h5.warn {
            color: #facc15;
        }

        .dbg-card-header h5.info {
            color: #818cf8;
        }

        .dbg-card-header h5.danger {
            color: #f87171;
        }

        .dbg-card-body {
            padding: 1.25rem;
        }

        .dbg-btn {
            display: inline-flex;
            align-items: center;
            gap: .35rem;
            padding: .35rem .85rem;
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 6px;
            background: rgba(255, 255, 255, 0.05);
            color: rgba(255, 255, 255, 0.55);
            font-family: 'Lexend', sans-serif;
            font-size: .75rem;
            cursor: pointer;
            transition: background .2s, color .2s;
        }

        .dbg-btn:hover {
            background: rgba(99, 102, 241, 0.2);
            color: rgba(255, 255, 255, 0.85);
            border-color: rgba(99, 102, 241, 0.4);
        }

        .dbg-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 1.25rem;
        }

        .dbg-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: .5rem;
            width: 90px;
        }

        .dbg-item img {
            width: 64px;
            height: 64px;
            object-fit: contain;
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.07);
            padding: .35rem;
        }

        .dbg-item span {
            font-size: .72rem;
            text-align: center;
            color: rgba(255, 255, 255, 0.35);
        }
    </style>
</head>

<body>

    <div class="dbg-pre"><?= htmlspecialchars($data, ENT_QUOTES, 'UTF-8') ?></div>

    <div class="dbg-card">
        <div class="dbg-card-header">
            <h5 class="info">INFO</h5>
            <button class="dbg-btn" onclick="location.reload()">
                <i class="fa fa-sync"></i> Reload
            </button>
        </div>
        <div class="dbg-card-body"></div>
    </div>

    <div class="dbg-card">
        <div class="dbg-card-header">
            <h5 class="warn">MENU UTAMA</h5>
        </div>
        <div class="dbg-card-body">
            <div class="dbg-grid">
                <?php foreach ($menu as $m): ?>
                    <div class="dbg-item">
                        <img src="<?= base_url() ?>/assets/app/img/<?= htmlspecialchars($m->icon, ENT_QUOTES, 'UTF-8') ?>" alt="">
                        <span><?= htmlspecialchars($m->title, ENT_QUOTES, 'UTF-8') ?></span>
                    </div>
                <?php endforeach ?>
            </div>
        </div>
    </div>

    <div class="dbg-card">
        <div class="dbg-card-header">
            <h5 class="danger">JADWAL HARI INI</h5>
            <button class="dbg-btn" onclick="location.reload()">
                <i class="fa fa-sync"></i> Reload
            </button>
        </div>
        <div class="dbg-card-body"></div>
    </div>

</body>

</html>
