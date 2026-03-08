{{FILE: _templates/debug.php}}
<!DOCTYPE html>
<html lang="id">

<head>
    <title>DEBUG</title>
    <style>
        *,
        *::before,
        *::after {
            box-sizing: border-box;
        }

        body {
            background: #0d0f13;
            color: #c9cdd4;
            font-family: monospace;
            padding: 1.5rem;
            margin: 0;
        }

        .dbg-pre {
            background: #111318;
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 8px;
            padding: 1rem;
            font-size: .82rem;
            overflow: auto;
            margin-bottom: 1.5rem;
        }

        .dbg-card {
            background: #1a1d23;
            border: 1px solid rgba(255, 255, 255, 0.07);
            border-radius: 12px;
            overflow: hidden;
            margin-bottom: 1.5rem;
        }

        .dbg-card-header {
            background: #111318;
            padding: .75rem 1.25rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid rgba(255, 255, 255, 0.06);
        }

        .dbg-card-header h5 {
            margin: 0;
            font-size: .82rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .5px;
        }

        .dbg-card-header h5.warn {
            color: #facc15;
        }

        .dbg-card-header h5.info {
            color: #7eb8ff;
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
            padding: .4rem .9rem;
            border: none;
            border-radius: 6px;
            background: rgba(255, 255, 255, 0.08);
            color: #c9cdd4;
            font-size: .78rem;
            cursor: pointer;
        }

        .dbg-btn:hover {
            background: rgba(255, 255, 255, 0.15);
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
            border-radius: 8px;
            background: #111318;
            padding: .25rem;
        }

        .dbg-item span {
            font-size: .72rem;
            text-align: center;
            color: #9a9fa8;
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

