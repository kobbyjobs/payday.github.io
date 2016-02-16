<?php
// тип девайса: Mobile Desktop Tablet
if ($save_device == 'Mobile' OR $save_device == 'Tablet') {
echo json_encode('<p>Выводим что-то для мобильного юзера</p>');
} else {
echo json_encode('<p>Выводим для десктопного юзера</p>');
}

/* переменные которые можно использовать в этом скрипте:
$country - код страны типа: ru ua us de
применять можно для фильтрации по странам типа:
if ($country == 'ru' OR $country == 'ua') {echo json_encode('<p>Содержимое для украины и россии</p>');}
$key - ключевик страницы, применять можно для редиректа по ключу, 
не забыв при этом обернуть его в urlencode
*/
