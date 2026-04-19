# 🚗 Rent Car Phuket

Система аренды автомобилей на Пхукете (Таиланд) с административной панелью и файловым хранилищем данных.

## 🌐 Домен
**rentcarphuket.ru** | Валюта: ฿ (тайский бат)

## 🚀 Быстрый старт

### Требования
- PHP >= 8.0
- Apache с mod_rewrite
- Composer
- PHP расширения: mbstring, json, session, opcache (рекомендуется)

### Локальная установка

```bash
# Клонировать репозиторий
git clone <repository-url>
cd car_php

# Установить зависимости
composer install

# Создать .env файл
cp .env.production.example .env
nano .env

# Запустить локальный сервер
composer serve
```

### Настройка .env

```env
APP_URL=http://localhost:8000
APP_DEBUG=true

ADMIN_USERNAME=admin
ADMIN_PASSWORD_HASH=your_bcrypt_hash
HEALTH_CHECK_SECRET=change_me
DEPLOY_CHECK_SECRET=change_me

TELEGRAM_BOT_TOKEN=your_token
TELEGRAM_CHAT_ID=your_chat_id
```

## 📁 Структура проекта

```
/
├── .agent/              # 📚 Вся документация проекта
├── admin/               # 🔐 Административная панель
├── api/                 # 🔌 API endpoints
├── app/                 # 💼 Бизнес-логика (Repositories, Services)
├── assets/              # 🎨 Статические файлы (CSS, JS, изображения)
├── config/              # ⚙️ Конфигурационные файлы
├── includes/            # 🔧 Общие PHP файлы (компоненты, данные)
│   ├── components.php   # UI компоненты
│   ├── data_*.php       # 💾 Файловое хранилище данных
│   └── ...
├── pages/               # 📄 Страницы сайта
├── public/              # 🌐 Публичная директория (точка входа)
├── storage/logs/        # 📝 Логи приложения
├── vendor/              # 📦 Composer зависимости
├── .env                 # 🔒 Переменные окружения
├── router.php           # 🛣️ Основной роутер
└── index.php            # 🚪 Точка входа
```

## 🏗️ Архитектура

### Хранение данных
Проект использует **файловое хранилище** вместо базы данных:
- Данные хранятся как PHP-массивы в `includes/data_*.php`
- Repositories управляют чтением/записью через `var_export()`
- OPcache автоматически инвалидируется при изменениях

### Слои приложения
1. **Router** (`router.php`) - маршрутизация URL
2. **Pages** (`pages/`, `admin/`) - страницы и формы
3. **Repositories** (`app/Repositories/`) - работа с данными
4. **Components** (`includes/components.php`) - переиспользуемые UI блоки
5. **Data** (`includes/data_*.php`) - файловое хранилище

## 🎨 UI Компоненты

Проект использует систему переиспользуемых компонентов:

```php
// Карточка информации
echo render_info_card('shield', 'Страховка по договору', 'Условия покрытия зависят от выбранного авто');

// Список преимуществ
echo render_feature_list($features, 2);

// Карточка тарифа
echo render_price_card(1200, 'за день', $features, true);
```

Подробнее: [Примеры компонентов](.agent/COMPONENTS_EXAMPLES.md)

## 🔐 Безопасность

- CSRF защита на всех формах
- Secure session cookies
- XSS защита через заголовки
- Защита директорий через `.htaccess`
- Файловое хранилище данных (без SQL injection)

## 🚀 Развертывание

### Production


```bash
# 1. Загрузить файлы на сервер (см. FTP_DEPLOYMENT.md)
# 2. Создать .env с APP_DEBUG=false
# 3. Установить права
chmod 755 storage/logs/
chmod 644 includes/data_*.php
chmod 644 .env

# 4. Установить зависимости
composer install --no-dev --optimize-autoloader
```

### CI/CD

Автоматический деплой через GitHub Actions при push в `main`:
- FTP загрузка файлов
- Исключение `.agent/`, `.git/`, документации
- Секреты в GitHub Secrets

## 📊 Мониторинг

### Health Check
```
GET /health-check.php?secret=<HEALTH_CHECK_SECRET>
```

### Логи
- `storage/logs/access.log` - все запросы
- `storage/logs/error.log` - ошибки
- `storage/logs/debug.log` - отладка (только при APP_DEBUG=true)

## 🛠️ Разработка

### Добавление новой страницы
1. Создать файл в `pages/`
2. Добавить маршрут в `router.php`
3. Использовать компоненты из `includes/components.php`

### Работа с данными
1. Использовать существующий Repository из `app/Repositories/`
2. Или создать новый Repository для новой сущности

Подробнее: [Правила работы](.agent/RULES.md)

## 📝 Лицензия

Proprietary - Все права защищены

## 📞 Контакты

- **Сайт:** rentcarphuket.ru
- **Telegram:** Уведомления о критических ошибках
