<?php
/**
 * Проверяет переданную дату на соответствие формату 'ГГГГ-ММ-ДД'
 *
 * Примеры использования:
 * is_date_valid('2019-01-01'); // true
 * is_date_valid('2016-02-29'); // true
 * is_date_valid('2019-04-31'); // false
 * is_date_valid('10.10.2010'); // false
 * is_date_valid('10/10/2010'); // false
 *
 * @param string $date Дата в виде строки
 *
 * @return bool true при совпадении с форматом 'ГГГГ-ММ-ДД', иначе false
 */
function is_date_valid(string $date) : bool {
    $format_to_check = 'Y-m-d';
    $dateTimeObj = date_create_from_format($format_to_check, $date);

    return $dateTimeObj !== false && array_sum(date_get_last_errors()) === 0;
}

/**
 * Создает подготовленное выражение на основе готового SQL запроса и переданных данных
 *
 * @param $link mysqli Ресурс соединения
 * @param $sql string SQL запрос с плейсхолдерами вместо значений
 * @param array $data Данные для вставки на место плейсхолдеров
 *
 * @return mysqli_stmt Подготовленное выражение
 */
function db_get_prepare_stmt($link, $sql, $data = []) {
    $stmt = mysqli_prepare($link, $sql);

    if ($stmt === false) {
        $errorMsg = 'Не удалось инициализировать подготовленное выражение: ' . mysqli_error($link);
        die($errorMsg);
    }

    if ($data) {
        $types = '';
        $stmt_data = [];

        foreach ($data as $value) {
            $type = 's';

            if (is_int($value)) {
                $type = 'i';
            }
            else if (is_string($value)) {
                $type = 's';
            }
            else if (is_double($value)) {
                $type = 'd';
            }

            if ($type) {
                $types .= $type;
                $stmt_data[] = $value;
            }
        }

        $values = array_merge([$stmt, $types], $stmt_data);

        $func = 'mysqli_stmt_bind_param';
        $func(...$values);

        if (mysqli_errno($link) > 0) {
            $errorMsg = 'Не удалось связать подготовленное выражение с параметрами: ' . mysqli_error($link);
            die($errorMsg);
        }
    }

    return $stmt;
}

/**
 * Возвращает корректную форму множественного числа
 * Ограничения: только для целых чисел
 *
 * Пример использования:
 * $remaining_minutes = 5;
 * echo "Я поставил таймер на {$remaining_minutes} " .
 *     get_noun_plural_form(
 *         $remaining_minutes,
 *         'минута',
 *         'минуты',
 *         'минут'
 *     );
 * Результат: "Я поставил таймер на 5 минут"
 *
 * @param int $number Число, по которому вычисляем форму множественного числа
 * @param string $one Форма единственного числа: яблоко, час, минута
 * @param string $two Форма множественного числа для 2, 3, 4: яблока, часа, минуты
 * @param string $many Форма множественного числа для остальных чисел
 *
 * @return string Рассчитанная форма множественнго числа
 */
function get_noun_plural_form (int $number, string $one, string $two, string $many): string
{
    $number = (int) $number;
    $mod10 = $number % 10;
    $mod100 = $number % 100;

    switch (true) {
        case ($mod100 >= 11 && $mod100 <= 20):
            return $many;

        case ($mod10 > 5):
            return $many;

        case ($mod10 === 1):
            return $one;

        case ($mod10 >= 2 && $mod10 <= 4):
            return $two;

        default:
            return $many;
    }
}

/**
 * Подключает шаблон, передает туда данные и возвращает итоговый HTML контент
 * @param string $name Путь к файлу шаблона относительно папки templates
 * @param array $data Ассоциативный массив с данными для шаблона
 * @return string Итоговый HTML
 */
function include_template($name, array $data = []) {
    $name = 'templates/' . $name;
    $result = '';

    if (!is_readable($name)) {
        return $result;
    }

    ob_start();
    extract($data);
    require $name;

    $result = ob_get_clean();

    return $result;
}

/**
 * Форматирует цену, разделяя тысячи пробелом и подставляя символ рубля
 * @param int $unformatted_price Цена без форматирования
 * @return string Форматированная цена
 */
function get_price_formatting($unformatted_price) {
    $ceil_unformatted_price = ceil($unformatted_price);
    $formatted_price = number_format($ceil_unformatted_price, 0, '.', ' ');
    $formatted_price .= " ₽";
    return $formatted_price;
}

/**
 * Определяет сколько времени осталось до полуночи 
 * @return string Время до полуночи 
 */
function get_time_to_midnight() {
    $time_now = date_create("now");
    $time_midnight = date_create("tomorrow");
    $time_difference = date_diff($time_now, $time_midnight);
    $time_formatted = date_interval_format($time_difference, "%H:%I");
    return $time_formatted;
}

/**
 * Определяет меньше ли часа значение времени, 
 * переданного в качестве аргумента
 * @param string $time Время для проверки в формате "%D:%H:%I"
 * @return boolean True если времени меньше часа  
 */
function is_time_to_midnight_finishing($time) {
    $time = str_replace("д", "", $time);
    $time = str_replace(":", "", $time);
    $time = (int)$time;
    if ($time <= 100) {
        return true;
    }
    return false;
}

/**
 * Определяет сколько дней:часов:минут осталось до конца существования лота
 * @param string $dt_exp Дата конца существования лота
 * @return string Время до конца существования лота в формате "%D:%H:%I"
 */
function get_time_to_expiration($dt_exp) {
    $time_now = date_create("now");
    $expiration = date_create($dt_exp);
    $time_difference = date_diff($time_now, $expiration);
    $time_formatted = date_interval_format($time_difference, "%Dд%H:%I");
    return $time_formatted;
}

/**
 * Определяет имя пользователя по его id в соответствии с таблицей users
 * @param object $db Ресурс соединения с базой данных
 * @param int $id id пользователя
 * @return string Имя пользователя или пустая строка
 */
function get_user_name_from_id($db, $id) {
    $sql_user_name = "SELECT user_name FROM users WHERE id=?;";
    $stmt = mysqli_prepare($db, $sql_user_name);
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $sql_user_name_result = mysqli_stmt_get_result($stmt);
    if ($sql_user_name_result) {
        $user_name_array = mysqli_fetch_assoc($sql_user_name_result);
        $user_name = $user_name_array["user_name"];
        return $user_name;
    }
    return "";
}

/**
 * Возвращает корректную запись времени ставки
 * @param string $dt_bet Дата и время ставки
 * @return string Корректная запись времени ставки
 */
function when_was_bet($dt_bet) {
    $output_str = "";
    $stamp_now = time();
    $stamp_midnight = strtotime(date("Y-m-d"));
    $stamp_bet = strtotime($dt_bet);
    
    if ($stamp_bet >= ($stamp_midnight - 86400) && 
            $stamp_bet < $stamp_midnight) {
        $output_str = date("Вчера в H:i", $stamp_bet);
    }
    elseif ($stamp_bet >= $stamp_midnight) {
        $hours = (int) (($stamp_now - $stamp_bet)/3600);
        $minutes = (int) ((($stamp_now - $stamp_bet)%3600)/60);
        $right_h = get_noun_plural_form($hours, " час", " часа", " часов");
        $right_m = get_noun_plural_form($minutes, " минута", " минуты", 
                " минут");
        if ($hours) {
            $output_str = $hours . $right_h . " " . $minutes . $right_m 
                    . " назад";
        }
        else {
            $output_str = $minutes . $right_m . " назад";
        }
    }
    else {
        $output_str = date("d.m.y в H:i", $stamp_bet);
    }      
    return $output_str;
}