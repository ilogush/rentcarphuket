<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/layout.php';

render_layout('Условия', function($data) {
    echo render_page_header('Условия аренды', 'Пожалуйста, ознакомьтесь с правилами до бронирования');
    
    ob_start();
    ?>
    <div class="space-y-12">
        <section>
            <h2 class="text-2xl font-black text-gray-800 mb-6">Общие положения</h2>
            <p class="text-gray-600 mb-4 leading-relaxed">
                Настоящие условия аренды регулируют отношения между арендодателем (компанией по аренде автомобилей) 
                и арендатором (клиентом). Бронируя автомобиль через наш сайт, вы автоматически соглашаетесь 
                с данными условиями. Пожалуйста, внимательно ознакомьтесь с ними перед оформлением заказа.
            </p>
            <p class="text-gray-600 leading-relaxed">
                Мы стремимся обеспечить максимально комфортный и безопасный опыт аренды автомобилей в Таиланде. 
                Все наши автомобили регулярно проходят техническое обслуживание и застрахованы в соответствии 
                с законодательством Таиланда.
            </p>
        </section>

        <section>
            <h2 class="text-2xl font-black text-gray-800 mb-6">Требования к водителю</h2>
            <div class="bg-blue-50 p-6 rounded-2xl mb-6">
                <h3 class="font-bold text-gray-800 mb-4">Основные требования:</h3>
                <ul class="space-y-3 text-gray-600">
                    <li class="flex items-start gap-3">
                        <span class="text-blue-600 font-bold">✓</span>
                        <span><strong>Возраст:</strong> минимум 21 год (для некоторых категорий автомобилей — 25 лет)</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="text-blue-600 font-bold">✓</span>
                        <span><strong>Водительский стаж:</strong> не менее 1 года (для премиум-класса — 3 года)</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="text-blue-600 font-bold">✓</span>
                        <span><strong>Документы:</strong> действующий загранпаспорт и водительское удостоверение международного образца или национальное с нотариально заверенным переводом на английский язык</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="text-blue-600 font-bold">✓</span>
                        <span><strong>Кредитная карта:</strong> на имя основного водителя для блокировки депозита</span>
                    </li>
                </ul>
            </div>
            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded">
                <p class="text-sm text-gray-700">
                    <strong>Важно:</strong> Водительское удостоверение должно быть действительным на весь период аренды. 
                    Просроченные документы не принимаются.
                </p>
            </div>
        </section>

        <section>
            <h2 class="text-2xl font-black text-gray-800 mb-6">В стоимость аренды включено</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div class="bg-green-50 p-4 rounded-xl">
                    <h3 class="font-bold text-gray-800 mb-3">Страхование</h3>
                    <ul class="space-y-2 text-sm text-gray-600">
                        <li>• Страховка по условиям договора</li>
                        <li>• Покрытие зависит от выбранного автомобиля</li>
                        <li>• Размер ответственности фиксируется перед выдачей авто</li>
                    </ul>
                </div>
                <div class="bg-green-50 p-4 rounded-xl">
                    <h3 class="font-bold text-gray-800 mb-3">Пробег</h3>
                    <ul class="space-y-2 text-sm text-gray-600">
                        <li>• Неограниченный пробег</li>
                        <li>• Без доплат за километраж</li>
                        <li>• Можно путешествовать по всему Таиланду</li>
                    </ul>
                </div>
                <div class="bg-green-50 p-4 rounded-xl">
                    <h3 class="font-bold text-gray-800 mb-3">Доставка</h3>
                    <ul class="space-y-2 text-sm text-gray-600">
                        <li>• Бесплатная доставка в аэропорт (от 3 дней аренды)</li>
                        <li>• Доставка в отель (по запросу, может взиматься плата)</li>
                        <li>• Встреча с табличкой в аэропорту</li>
                    </ul>
                </div>
                <div class="bg-green-50 p-4 rounded-xl">
                    <h3 class="font-bold text-gray-800 mb-3">Дополнительно</h3>
                    <ul class="space-y-2 text-sm text-gray-600">
                        <li>• Детское кресло (по запросу, бесплатно)</li>
                        <li>• GPS-навигатор (по запросу)</li>
                        <li>• Круглосуточная техподдержка</li>
                    </ul>
                </div>
            </div>
        </section>

        <section>
            <h2 class="text-2xl font-black text-gray-800 mb-6">Правила аренды</h2>
            
            <div class="space-y-6">
                <div>
                    <h3 class="font-bold text-gray-800 mb-3">Запреты и ограничения</h3>
                    <ul class="space-y-2 text-gray-600 ml-6 list-disc">
                        <li>Категорически запрещено курение в автомобиле (штраф 3,000 ฿)</li>
                        <li>Запрещена перевозка домашних животных без специального разрешения</li>
                        <li>Запрещено использование автомобиля в коммерческих целях (такси, доставка)</li>
                        <li>Запрещено участие в гонках или других соревнованиях</li>
                        <li>Запрещен выезд за пределы Таиланда без письменного разрешения</li>
                        <li>Запрещена передача управления третьим лицам, не указанным в договоре</li>
                    </ul>
                </div>

                <div>
                    <h3 class="font-bold text-gray-800 mb-3">Топливо</h3>
                    <ul class="space-y-2 text-gray-600 ml-6 list-disc">
                        <li>Автомобиль выдается с полным баком топлива</li>
                        <li>Возврат также должен быть с полным баком</li>
                        <li>При возврате с неполным баком взимается плата: стоимость топлива + сервисный сбор 500 ฿</li>
                        <li>Тип топлива указан в документах автомобиля и на лючке бензобака</li>
                    </ul>
                </div>

                <div>
                    <h3 class="font-bold text-gray-800 mb-3">Получение и возврат</h3>
                    <ul class="space-y-2 text-gray-600 ml-6 list-disc">
                        <li>Получение автомобиля в согласованном месте (офис, аэропорт, отель)</li>
                        <li>Возврат в том же месте, если не оговорено иное</li>
                        <li>Возврат в другом городе возможен за дополнительную плату (от 1,500 ฿)</li>
                        <li>Опоздание на возврат более 1 часа — оплата полных суток аренды</li>
                        <li>При получении обязательный осмотр автомобиля и фиксация повреждений</li>
                    </ul>
                </div>

                <div>
                    <h3 class="font-bold text-gray-800 mb-3">Чистота автомобиля</h3>
                    <ul class="space-y-2 text-gray-600 ml-6 list-disc">
                        <li>Автомобиль выдается в чистом виде</li>
                        <li>Обычные загрязнения при возврате допустимы</li>
                        <li>За сильное загрязнение салона взимается плата за химчистку (от 1,000 ฿)</li>
                        <li>Рекомендуем не оставлять мусор в салоне</li>
                    </ul>
                </div>
            </div>
        </section>

        <section>
            <h2 class="text-2xl font-black text-gray-800 mb-6">Депозит и оплата</h2>
            <div class="bg-gradient-to-br from-blue-50 to-indigo-50 p-6 rounded-2xl space-y-4">
                <div>
                    <h3 class="font-bold text-gray-800 mb-2">Депозит</h3>
                    <p class="text-gray-600 text-sm mb-2">
                        При получении автомобиля вносится залог. Его размер зависит от выбранного авто:
                    </p>
                    <ul class="space-y-1 text-sm text-gray-600 ml-6 list-disc">
                        <li>актуальная сумма указана в карточке автомобиля;</li>
                        <li>залог отображается в расчете перед оформлением заявки;</li>
                        <li>оплата возможна наличными или переводом на карту.</li>
                    </ul>
                    <p class="text-gray-600 text-sm mt-2">
                        Залог возвращается после проверки автомобиля при возврате, если нет повреждений и нарушений условий аренды.
                    </p>
                </div>
                <div>
                    <h3 class="font-bold text-gray-800 mb-2">Оплата аренды</h3>
                    <ul class="space-y-2 text-sm text-gray-600">
                        <li>• Предоплата 30% при бронировании онлайн</li>
                        <li>• Остаток оплачивается при получении автомобиля</li>
                        <li>• Принимаем: наличные (THB, USD, EUR), банковские карты, банковский перевод</li>
                        <li>• При оплате картой комиссия не взимается</li>
                    </ul>
                </div>
            </div>
        </section>

        <section>
            <h2 class="text-2xl font-black text-gray-800 mb-6">Страхование и франшиза</h2>
            <div class="space-y-4">
                <p class="text-gray-600 leading-relaxed">
                    Автомобили застрахованы по условиям действующего договора. Покрытие, исключения и размер ответственности
                    зависят от выбранного автомобиля и фиксируются перед выдачей авто.
                </p>
                <div class="bg-white border-2 border-blue-200 rounded-xl p-6">
                    <h3 class="font-bold text-gray-800 mb-4">Что может покрывать страховка:</h3>
                    <ul class="space-y-2 text-gray-600 ml-6 list-disc">
                        <li>Повреждения кузова автомобиля в результате ДТП</li>
                        <li>Ущерб третьим лицам (в пределах лимита полиса)</li>
                        <li>Угон автомобиля или повреждения от стихийных бедствий, если это предусмотрено договором</li>
                    </ul>
                </div>
                <div class="bg-red-50 border-2 border-red-200 rounded-xl p-6">
                    <h3 class="font-bold text-gray-800 mb-4">Что обычно НЕ покрывает страховка:</h3>
                    <ul class="space-y-2 text-gray-600 ml-6 list-disc">
                        <li>Повреждения днища автомобиля</li>
                        <li>Повреждения шин и дисков</li>
                        <li>Повреждения салона (обивка, приборная панель)</li>
                        <li>Утеря ключей, документов автомобиля</li>
                        <li>Ущерб, причиненный в состоянии алкогольного опьянения</li>
                        <li>Ущерб при нарушении ПДД</li>
                    </ul>
                </div>
                <div class="bg-green-50 border-l-4 border-green-400 p-4 rounded">
                    <p class="text-sm text-gray-700">
                        <strong>Дополнительные опции:</strong> Возможность расширенного страхового покрытия уточняется
                        менеджером перед подтверждением бронирования.
                    </p>
                </div>
            </div>
        </section>

        <section>
            <h2 class="text-2xl font-black text-gray-800 mb-6">Действия при ДТП</h2>
            <div class="bg-yellow-50 border-2 border-yellow-300 rounded-xl p-6">
                <ol class="space-y-3 text-gray-700">
                    <li class="flex items-start gap-3">
                        <span class="bg-yellow-400 text-white rounded-full w-6 h-6 flex items-center justify-center flex-shrink-0 text-sm font-bold">1</span>
                        <span>Немедленно остановитесь и включите аварийную сигнализацию</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="bg-yellow-400 text-white rounded-full w-6 h-6 flex items-center justify-center flex-shrink-0 text-sm font-bold">2</span>
                        <span>Позвоните в нашу службу поддержки: +66 12 345 6789 (круглосуточно)</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="bg-yellow-400 text-white rounded-full w-6 h-6 flex items-center justify-center flex-shrink-0 text-sm font-bold">3</span>
                        <span>Вызовите полицию (туристическая полиция: 1155)</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="bg-yellow-400 text-white rounded-full w-6 h-6 flex items-center justify-center flex-shrink-0 text-sm font-bold">4</span>
                        <span>Сфотографируйте место происшествия и повреждения</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="bg-yellow-400 text-white rounded-full w-6 h-6 flex items-center justify-center flex-shrink-0 text-sm font-bold">5</span>
                        <span>Получите полицейский протокол (обязательно!)</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="bg-yellow-400 text-white rounded-full w-6 h-6 flex items-center justify-center flex-shrink-0 text-sm font-bold">6</span>
                        <span>Не признавайте вину до консультации с нашим представителем</span>
                    </li>
                </ol>
                <p class="text-sm text-gray-600 mt-4 font-semibold">
                    Важно: Без полицейского протокола страховка не действует!
                </p>
            </div>
        </section>

        <section>
            <div class="bg-gray-50 border-l-4 border-blue-500 p-6 rounded">
                <h3 class="font-bold text-gray-800 mb-3">Важная информация</h3>
                <ul class="space-y-2 text-sm text-gray-600">
                    <li>• Компания оставляет за собой право отказать в аренде без объяснения причин</li>
                    <li>• Все споры решаются в соответствии с законодательством Таиланда</li>
                    <li>• Условия могут быть изменены без предварительного уведомления</li>
                    <li>• Актуальная версия условий всегда доступна на нашем сайте</li>
                    <li>• При противоречии между языковыми версиями приоритет имеет английская версия</li>
                </ul>
            </div>
        </section>
    </div>
    <?php
    $content = ob_get_clean();
    
    echo '<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">';
    echo render_card($content, 'p-6', 'rounded-[40px]', 'prose prose-blue max-w-none');
    echo '</div>';
});
