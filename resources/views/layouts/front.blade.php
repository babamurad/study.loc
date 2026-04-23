<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Web Dev Course | Твой путь в мир веба' }}</title>
    <meta name="description" content="Интерактивный курс по HTML и CSS для начинающих. От основ до профессиональной верстки.">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Fira+Code:wght@400;500&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <style>
        :root {
            --primary: #6366f1;
            --primary-light: #818cf8;
            --primary-dark: #4f46e5;
            --secondary: #a855f7;
            --bg: #0f172a;
            --card-bg: rgba(30, 41, 59, 0.7);
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --border: rgba(255, 255, 255, 0.1);
            --glass-bg: rgba(255, 255, 255, 0.03);
            --glass-border: rgba(255, 255, 255, 0.1);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg);
            background-image:
                radial-gradient(circle at 10% 20%, rgba(99, 102, 241, 0.15) 0%, transparent 40%),
                radial-gradient(circle at 90% 80%, rgba(168, 85, 247, 0.15) 0%, transparent 40%);
            color: var(--text-main);
            line-height: 1.6;
            min-height: 100vh;
            overflow-x: hidden;
        }

        .container {
            max-width: 1100px;
            margin: 0 auto;
            padding: 0 24px;
        }

        /* --- Header --- */
        header {
            padding: 32px 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 1.5rem;
            font-weight: 800;
            background: linear-gradient(135deg, var(--primary-light), var(--secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            letter-spacing: -0.02em;
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
        }

        .logo-box {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.2rem;
            box-shadow: 0 8px 16px rgba(99, 102, 241, 0.3);
        }

        /* --- Hero --- */
        .hero {
            padding: 80px 0 100px;
            text-align: center;
            position: relative;
        }

        .badge {
            display: inline-block;
            background: rgba(99, 102, 241, 0.1);
            border: 1px solid rgba(99, 102, 241, 0.2);
            color: var(--primary-light);
            padding: 6px 16px;
            border-radius: 100px;
            font-size: 0.875rem;
            font-weight: 600;
            margin-bottom: 24px;
            animation: fadeInDown 0.8s ease-out;
        }

        h1 {
            font-size: clamp(2.5rem, 8vw, 4.5rem);
            font-weight: 800;
            line-height: 1.1;
            margin-bottom: 24px;
            letter-spacing: -0.04em;
            animation: fadeInUp 0.8s ease-out;
        }

        h1 span {
            color: var(--primary-light);
            position: relative;
        }

        .hero p {
            font-size: clamp(1rem, 3vw, 1.25rem);
            color: var(--text-muted);
            max-width: 600px;
            margin: 0 auto 40px;
            animation: fadeInUp 1s ease-out;
        }

        /* --- Grid --- */
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 24px;
            margin-bottom: 100px;
        }

        .card {
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            backdrop-filter: blur(12px);
            padding: 32px;
            border-radius: 24px;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            text-decoration: none;
            color: inherit;
            display: flex;
            flex-direction: column;
            position: relative;
            overflow: hidden;
        }

        .card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.1), transparent);
            opacity: 0;
            transition: opacity 0.4s;
        }

        .card:hover {
            transform: translateY(-8px);
            border-color: var(--primary-light);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
        }

        .card:hover::before {
            opacity: 1;
        }

        .card-icon {
            font-size: 2.5rem;
            margin-bottom: 20px;
        }

        .card h3 {
            font-size: 1.5rem;
            margin-bottom: 12px;
            font-weight: 700;
        }

        .card p {
            color: var(--text-muted);
            margin-bottom: 24px;
            flex-grow: 1;
        }

        .card-footer {
            display: flex;
            align-items: center;
            gap: 8px;
            color: var(--primary-light);
            font-weight: 700;
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .card-footer svg {
            width: 18px;
            height: 18px;
            transition: transform 0.3s;
        }

        .card:hover .card-footer svg {
            transform: translateX(5px);
        }

        /* --- Roadmap --- */
        .section-title {
            text-align: center;
            margin-bottom: 60px;
        }

        .section-title h2 {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 16px;
        }

        .roadmap {
            position: relative;
            padding-left: 40px;
            max-width: 800px;
            margin: 0 auto 100px;
        }

        .roadmap::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            width: 2px;
            height: 100%;
            background: linear-gradient(to bottom, var(--primary), var(--secondary), transparent);
        }

        .roadmap-item {
            position: relative;
            margin-bottom: 40px;
            padding-left: 20px;
        }

        .roadmap-item::before {
            content: '';
            position: absolute;
            left: -46px;
            top: 0;
            width: 12px;
            height: 12px;
            background: var(--bg);
            border: 3px solid var(--primary);
            border-radius: 50%;
            z-index: 2;
        }

        .roadmap-content {
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            padding: 24px;
            border-radius: 20px;
            transition: 0.3s;
        }

        .roadmap-item:hover .roadmap-content {
            background: rgba(255, 255, 255, 0.06);
            transform: translateX(10px);
        }

        .roadmap-week {
            font-size: 0.75rem;
            font-weight: 800;
            color: var(--primary-light);
            text-transform: uppercase;
            margin-bottom: 8px;
            display: block;
        }

        .roadmap-title {
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .roadmap-desc {
            color: var(--text-muted);
            font-size: 0.9375rem;
        }

        .roadmap-item.active::before {
            background: var(--primary);
            box-shadow: 0 0 15px var(--primary);
        }

        .roadmap-item.completed::before {
            background: var(--secondary);
            border-color: var(--secondary);
        }

        /* --- Footer --- */
        footer {
            border-top: 1px solid var(--border);
            padding: 60px 0;
            text-align: center;
            color: var(--text-muted);
        }

        /* --- Animations --- */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Responsive */
        @media (max-width: 768px) {
            .hero {
                padding: 40px 0 60px;
            }
            .roadmap {
                padding-left: 20px;
            }
            .roadmap-item::before {
                left: -26px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        {{ $slot }}
    </div>

    @livewireScripts
</body>
</html>
