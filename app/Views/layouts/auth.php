<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <?= view('components/head', ['title' => $title ?? 'Blax | Marketplace Authentication']) ?>
    <!-- Google Fonts: Literata (Headlines) & Nunito Sans (Body) for Terra Design System -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Literata:ital,opsz,wght@0,7..72,400..800;1,7..72,400..700&family=Nunito+Sans:ital,opsz,wght@0,6..12,300..900;1,6..12,300..900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">

    <style>
        .font-headline, .font-serif {
            font-family: 'Literata', Georgia, serif;
        }
        .font-body {
            font-family: 'Nunito Sans', 'Plus Jakarta Sans', sans-serif;
        }
        .glow-blur {
            filter: blur(80px);
        }
        .glass-card-dark {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .glass-card-dark:hover {
            background: rgba(255, 255, 255, 0.08);
            border-color: rgba(255, 255, 255, 0.18);
        }
    </style>
</head>

<body class="bg-[#faf6f0] text-[#2e3230] font-body antialiased min-h-screen selection:bg-[#4a7c59] selection:text-white">

    <?= $this->renderSection('content') ?>

    <?= $this->renderSection('scripts') ?>

</body>
</html>