<div>
    <header>
        <a href="{{ route('home') }}" class="logo">
            <div class="logo-box">/</div>
            WebDev Course
        </a>
        <nav style="display: flex; gap: 16px;">
            @auth
                <a href="{{ route('dashboard') }}" style="color: var(--text-main); text-decoration: none; font-weight: 500; padding: 8px 16px; border-radius: 8px; background: var(--glass-bg); border: 1px solid var(--glass-border); transition: 0.3s;">Личный кабинет</a>
            @else
                <a href="{{ route('login') }}" style="color: var(--text-main); text-decoration: none; font-weight: 500; padding: 8px 16px; border-radius: 8px; transition: 0.3s;">Войти</a>
                <a href="{{ route('register') }}" style="color: var(--text-main); text-decoration: none; font-weight: 500; padding: 8px 16px; border-radius: 8px; background: linear-gradient(135deg, var(--primary), var(--secondary)); box-shadow: 0 4px 12px rgba(99, 102, 241, 0.2); transition: 0.3s;">Регистрация</a>
            @endauth
        </nav>
    </header>

    <main>
        <section class="hero">
            <span class="badge">Курс для начинающих 🚀</span>
            <h1>Стань мастером <span>Web-разработки</span></h1>
            <p>Пройди путь от создания первой HTML-страницы до разработки современных адаптивных сайтов на профессиональном уровне.</p>
        </section>

        <div class="grid">
            <a href="#" class="card">
                <div class="card-icon">📖</div>
                <div class="roadmap-week">Неделя 1</div>
                <h3>Текущий урок</h3>
                <p>Фундамент и Текст: Узнай как работает браузер и создай структуру своего первого сайта.</p>
                <div class="card-footer">
                    Перейти к уроку
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                    </svg>
                </div>
            </a>

            <a href="#" class="card">
                <div class="card-icon">🚀</div>
                <div class="roadmap-week">Инструмент</div>
                <h3>Песочница</h3>
                <p>Интерактивная среда для экспериментов с кодом. Пиши HTML и сразу видишь результат.</p>
                <div class="card-footer">
                    Открыть редактор
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                    </svg>
                </div>
            </a>
        </div>

        <section class="section-title">
            <span class="badge">Твоя траектория</span>
            <h2>Дорожная карта обучения</h2>
        </section>

        <div class="roadmap">
            <div class="roadmap-item active">
                <div class="roadmap-content">
                    <span class="roadmap-week">Неделя 1</span>
                    <h4 class="roadmap-title">Фундамент и Текст</h4>
                    <p class="roadmap-desc">Инфраструктура, VS Code, иерархия заголовков, списки и скелет документа.</p>
                </div>
            </div>

            <div class="roadmap-item">
                <div class="roadmap-content">
                    <span class="roadmap-week">Неделя 2</span>
                    <h4 class="roadmap-title">Связи и Структура</h4>
                    <p class="roadmap-desc">Ссылки, атрибуты, файловая система (пути) и навигация сайта.</p>
                </div>
            </div>

            <div class="roadmap-item">
                <div class="roadmap-content">
                    <span class="roadmap-week">Неделя 3</span>
                    <h4 class="roadmap-title">Контент и Таблицы</h4>
                    <p class="roadmap-desc">Мультимедиа (img, video), SEO-атрибуты и верстка табличных данных.</p>
                </div>
            </div>

            <div class="roadmap-item">
                <div class="roadmap-content">
                    <span class="roadmap-week">Неделя 4</span>
                    <h4 class="roadmap-title">Формы и Интерактивы</h4>
                    <p class="roadmap-desc">Input, Select, Label. Создание функциональных форм обратной связи.</p>
                </div>
            </div>

            <div class="roadmap-item">
                <div class="roadmap-content">
                    <span class="roadmap-week">Неделя 5</span>
                    <h4 class="roadmap-title">Семантика HTML5</h4>
                    <p class="roadmap-desc">Правильная структура документа: main, section, article. Подготовка к CSS.</p>
                </div>
            </div>

            <div class="roadmap-item">
                <div class="roadmap-content">
                    <span class="roadmap-week">Недели 6–10</span>
                    <h4 class="roadmap-title">Погружение в CSS</h4>
                    <p class="roadmap-desc">Стилизация, Box Model, Позиционирование, Flexbox и Адаптивность.</p>
                </div>
            </div>
        </div>
    </main>

    <footer>
        <p>&copy; {{ date('Y') }} WebDev Academy &bull; Создано для обучения</p>
    </footer>
</div>
