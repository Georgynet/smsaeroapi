smsaeroapi
==========

Класс Smsaero предназначен для работы с API сервиса http://smsaero.ru/.

Использование класса:

$sms = new Smsaero('test@local.ru', 'password', 'PROVERKA');

echo $sms->send(
    '79001234567',
    'Сообщение с тремя пробелами'
);
