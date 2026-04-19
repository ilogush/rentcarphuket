<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/layout.php';

render_layout('Партнерство', function($data) {
    echo render_page_header('Станьте нашим партнером', 'Специальные условия для бизнеса и туристических агентств');
    
    echo '<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">';
    
    // Hero banner with features
    ob_start();
    ?>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <?php echo render_feature_card('15-25%', 'Высокая комиссионная ставка для агентов'); ?>
        <?php echo render_feature_card('API', 'Легкая интеграция с вашим сайтом'); ?>
        <?php echo render_feature_card('24/7', 'Круглосуточная поддержка партнеров'); ?>
    </div>
    <?php
    $featuresHtml = ob_get_clean();
    
    echo render_hero_banner('Почему выгодно работать с нами?', $featuresHtml);
    
    // Detailed partnership content
    ob_start();
    ?>
    <div class="space-y-12">
        <section>
            <h2 class="text-2xl font-black text-gray-800 mb-6">О партнерской программе</h2>
            <p class="text-gray-600 mb-4 leading-relaxed">
                Мы предлагаем выгодные условия сотрудничества для туристических агентств, отелей, 
                гидов и других компаний, работающих в сфере туризма. Наша партнерская программа 
                разработана для того, чтобы вы могли предоставлять своим клиентам качественные 
                услуги аренды автомобилей и получать стабильный доход.
            </p>
            <p class="text-gray-600 leading-relaxed">
                Мы работаем на рынке аренды автомобилей в Таиланде более 10 лет и имеем безупречную 
                репутацию. Наш автопарк включает более 100 автомобилей различных классов, от 
                экономичных седанов до премиальных внедорожников.
            </p>
        </section>

        <section>
            <h2 class="text-2xl font-black text-gray-800 mb-6">Преимущества партнерства</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-blue-50 p-6 rounded-2xl">
                    <h3 class="font-bold text-gray-800 mb-3">Высокий доход</h3>
                    <p class="text-gray-600 text-sm">
                        Комиссия от 15% до 25% в зависимости от объема бронирований. 
                        Чем больше клиентов вы приводите, тем выше ваша ставка.
                    </p>
                </div>
                <div class="bg-blue-50 p-6 rounded-2xl">
                    <h3 class="font-bold text-gray-800 mb-3">Широкий выбор автомобилей</h3>
                    <p class="text-gray-600 text-sm">
                        Более 100 автомобилей различных классов: от бюджетных до премиум-сегмента. 
                        Все автомобили в отличном состоянии и регулярно проходят техобслуживание.
                    </p>
                </div>
                <div class="bg-blue-50 p-6 rounded-2xl">
                    <h3 class="font-bold text-gray-800 mb-3">Быстрое оформление</h3>
                    <p class="text-gray-600 text-sm">
                        Онлайн-бронирование занимает 5 минут. Мгновенное подтверждение заказа. 
                        Минимум документов для оформления.
                    </p>
                </div>
                <div class="bg-blue-50 p-6 rounded-2xl">
                    <h3 class="font-bold text-gray-800 mb-3">Техническая поддержка</h3>
                    <p class="text-gray-600 text-sm">
                        API для интеграции с вашим сайтом. Готовые виджеты бронирования. 
                        Персональный менеджер для решения любых вопросов.
                    </p>
                </div>
                <div class="bg-blue-50 p-6 rounded-2xl">
                    <h3 class="font-bold text-gray-800 mb-3">Прозрачная отчетность</h3>
                    <p class="text-gray-600 text-sm">
                        Личный кабинет партнера с детальной статистикой. Отслеживание всех 
                        бронирований в режиме реального времени. Еженедельные отчеты о выплатах.
                    </p>
                </div>
                <div class="bg-blue-50 p-6 rounded-2xl">
                    <h3 class="font-bold text-gray-800 mb-3">Маркетинговая поддержка</h3>
                    <p class="text-gray-600 text-sm">
                        Готовые рекламные материалы и баннеры. Совместные маркетинговые кампании. 
                        Обучение вашего персонала работе с системой.
                    </p>
                </div>
            </div>
        </section>

        <section>
            <h2 class="text-2xl font-black text-gray-800 mb-6">Условия сотрудничества</h2>
            <div class="bg-gradient-to-br from-blue-50 to-indigo-50 p-8 rounded-2xl">
                <div class="space-y-6">
                    <div class="flex items-start gap-4">
                        <div class="bg-blue-500 text-white rounded-full w-8 h-8 flex items-center justify-center flex-shrink-0 font-bold">1</div>
                        <div>
                            <h3 class="font-bold text-gray-800 mb-2">Регистрация в партнерской программе</h3>
                            <p class="text-gray-600 text-sm">
                                Заполните заявку на сотрудничество. Мы рассмотрим вашу заявку в течение 1-2 рабочих дней 
                                и свяжемся с вами для обсуждения деталей.
                            </p>
                        </div>
                    </div>
                    <div class="flex items-start gap-4">
                        <div class="bg-blue-500 text-white rounded-full w-8 h-8 flex items-center justify-center flex-shrink-0 font-bold">2</div>
                        <div>
                            <h3 class="font-bold text-gray-800 mb-2">Подписание договора</h3>
                            <p class="text-gray-600 text-sm">
                                После согласования условий мы подписываем партнерский договор. Договор можно подписать 
                                электронно или лично в нашем офисе.
                            </p>
                        </div>
                    </div>
                    <div class="flex items-start gap-4">
                        <div class="bg-blue-500 text-white rounded-full w-8 h-8 flex items-center justify-center flex-shrink-0 font-bold">3</div>
                        <div>
                            <h3 class="font-bold text-gray-800 mb-2">Получение доступа к системе</h3>
                            <p class="text-gray-600 text-sm">
                                Вы получаете доступ к личному кабинету партнера, API-ключи для интеграции и все 
                                необходимые материалы для начала работы.
                            </p>
                        </div>
                    </div>
                    <div class="flex items-start gap-4">
                        <div class="bg-blue-500 text-white rounded-full w-8 h-8 flex items-center justify-center flex-shrink-0 font-bold">4</div>
                        <div>
                            <h3 class="font-bold text-gray-800 mb-2">Начало работы</h3>
                            <p class="text-gray-600 text-sm">
                                Вы начинаете принимать бронирования от своих клиентов. Комиссия начисляется автоматически 
                                после завершения каждой аренды.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section>
            <h2 class="text-2xl font-black text-gray-800 mb-6">Часто задаваемые вопросы</h2>
            <div class="space-y-4">
                <div class="bg-white border border-gray-200 rounded-xl p-6">
                    <h3 class="font-bold text-gray-800 mb-2">Как быстро я получу свою комиссию?</h3>
                    <p class="text-gray-600 text-sm">
                        Комиссия выплачивается еженедельно на указанный вами счет. Минимальная сумма для выплаты — 5000 ฿.
                    </p>
                </div>
                <div class="bg-white border border-gray-200 rounded-xl p-6">
                    <h3 class="font-bold text-gray-800 mb-2">Нужно ли мне иметь офис в Таиланде?</h3>
                    <p class="text-gray-600 text-sm">
                        Нет, вы можете работать из любой точки мира. Все операции проводятся онлайн.
                    </p>
                </div>
                <div class="bg-white border border-gray-200 rounded-xl p-6">
                    <h3 class="font-bold text-gray-800 mb-2">Какие документы нужны для регистрации?</h3>
                    <p class="text-gray-600 text-sm">
                        Для юридических лиц: регистрационные документы компании. Для физических лиц: паспорт и ИНН.
                    </p>
                </div>
                <div class="bg-white border border-gray-200 rounded-xl p-6">
                    <h3 class="font-bold text-gray-800 mb-2">Могу ли я устанавливать свои цены?</h3>
                    <p class="text-gray-600 text-sm">
                        Да, вы можете добавлять свою наценку к базовым ценам. Комиссия рассчитывается от базовой стоимости.
                    </p>
                </div>
            </div>
        </section>
    </div>
    <?php
    $detailedContent = ob_get_clean();
    
    echo render_card($detailedContent, 'p-6', 'rounded-[40px]', 'prose prose-blue max-w-none');
    
    echo '</div>';
});
