<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>

    <?php
        SELECT * FROM `название_таблицы` -- выборка полей только из основной таблицы и не будет выбирать поля из связанной
        SELECT `поля`, `поля2` FROM `название_таблицы` -- выборка данных из всех таблиц в том числе и связанных 
        LEFT JOIN `имя_связанной_таблицы` ON `условие_связи (например: `айди_поля_связанной_таблицы (`id_cities`)` = `айди_поля_основной_таблицы (`name_cities`)`) ` -- достанет выбранные поля
        WHERE `условие_выборки` -- условие при котором это будет все выполняться (не обязательный пункт)
        SELECT * FROM `имя_таблицы` ORDER BY `имя_столбца` [ASC|DESC]; -- сортировка таблицы
        DELETE FROM `имя_таблицы` WHERE `условие (например удалить если значение зарплаты меньше 20 000)`;

        INSERT INTO `имя_таблицы` (колонка1, колонка2, колонка3, ...) VALUES (значение1, значение2, значение3, ...); -- добавление новых строк
        INSERT INTO `users` (username, password, city_id) VALUES ('user3', 'pass3', 1);

        UPDATE `имя_таблицы` SET `имя_столбца1` = `значение1`, `имя_столбца2` = `значение2`, ... WHERE `условие`; -- изменения существующих записей в таблице
        UPDATE `users` SET `email` = 'new_email@example.com' WHERE id = 1;

        SELECT `название_основной_таблицы`.`что_то_из_таблицы`, `название_основной_таблицы`.`что_то_из_таблицы2`, `название_связанной_таблицы`.`что_то_из_связанной_таблицы`
        FROM `название_основной_таблицы`
        JOIN `название_связанной_таблицы` ON `название_основной_таблицы`.`поле_которое_связали_с_полем_связанной_таблицы` = `название_связанной_таблицы`.`поле_которое_связали_с_полем_основной_таблицы`
        ORDER BY `название_связанной_таблицы`.`что_то_из_таблицы` ASC; -- сортируем результаты по имени города в порядке возрастания (ASC)
        => связали две таблицы (допустим юзер и город) и сделали сортировку городов
        ///////////////// ПРИМЕР
        // Подключение к базе данных
        $link = mysqli_connect('localhost', 'username', 'password', 'database_name'); // Замените на свои данные
        if (!$link) {
            die('Ошибка подключения: ' . mysqli_connect_error());
        }
        // SQL-запрос с сортировкой
        $query = "
            SELECT u.username, u.password, c.city_name
            FROM users u
            JOIN cities c ON u.city_id = c.id
            ORDER BY c.city_name ASC
        ";
        $res = mysqli_query($link, $query);
        if ($res) {
            // Вывод результатов
            while ($row = mysqli_fetch_assoc($res)) {
                echo "Пользователь: " . $row['username'] . " - Город: " . $row['city_name'] . "<br>";
            }
        } else {
            echo "Ошибка выполнения запроса: " . mysqli_error($link);
        }
        // Закрытие соединения с базой данных
        mysqli_close($link);


        ///////ПЕРЕХОД МЕЖДУ СТРАНИЦАМИ В ЗАВИСИМОСТИ ОТ РОЛИ
        if (!empty($_POST['login']) and !empty($_POST['password'])) {
            $login = $_POST['login'];
            $password = $_POST['password'];
            $roleQuery = 
            " SELECT `users`.`id_role`
            FROM `users`
            JOIN `roles` ON `users`.`id_role` = `roles`.`id_role`
            WHERE `login_users` = '$login' AND `password_users` = '$password'";
            foreach($result as $row) {
                switch ($row['айди_роли']) {
                    case 1:
                        header('Location: страница_админа.пхп');
                        break;
                    case 2:
                        header('Location: страница_менеджера.пхп');
                        break;
                    case 3:
                        header('Location: страница_оператора.пхп');
                        break;
                    case 4:
                        header('Location: страница_еще_кого_то_хз.пхп');
                        break;
                    default: header('Location: индэкс.пхп');
                }
            }
    }

    ///////ПРИМЕРНЫЙ ПОДСЧЕТ РЕЙТИНГА
    $query = "
    SELECT 
        u.id AS user_id, 
        u.username, 
        COUNT(t.id) AS task_count, 
        AVG(t.rating) AS average_rating
    FROM 
        users u
    LEFT JOIN 
        tasks t ON u.id = t.user_id AND t.completed = 1
    GROUP BY 
        u.id
    ORDER BY 
        average_rating DESC;
    ";

    ///////ПРИМЕРНЫЙ ПОИСК ПО ПЕРВОЙ БУКВЕ
    // Подключение к базе данных
    $link = mysqli_connect('localhost', 'username', 'password', 'database_name'); // Замените на свои данные

    if (!$link) {
        die('Ошибка подключения: ' . mysqli_connect_error());
    }

    // Получаем букву для поиска из запроса (например, из формы)
    $search_letter = isset($_GET['letter']) ? $_GET['letter'] : '';

    // Экранируем букву для предотвращения SQL-инъекций
    $search_letter = mysqli_real_escape_string($link, $search_letter);

    // Проверяем, что буква не пустая
    if (!empty($search_letter)) {
        // SQL-запрос для поиска пользователей по первой букве
        $query = "SELECT username FROM users WHERE username LIKE '$search_letter%'";

        $result = mysqli_query($link, $query);

        if ($result) {
            // Проверяем, есть ли результаты
            if (mysqli_num_rows($result) > 0) {
                // Выводим результаты
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "Пользователь: " . htmlspecialchars($row['username']) . "<br>";
                }
            } else {
                echo "Пользователи не найдены.";
            }
        } else {
            echo "Ошибка выполнения запроса: " . mysqli_error($link);
        }
    } else {
        echo "Введите букву для поиска.";
    }

    // Закрытие соединения с базой данных
    mysqli_close($link);


    ?>

    <!-- АВТОРИЗАЦИЯ ЧЕРЕЗ БД ПРОСТАЯ -->
    <!-- таблица -->
    <!-- id  login   password
         1   user     12345
         2   admin    admin -->

    <form action="" method="POST">
	    <input name="login">
	    <input name="password" type="password">
	    <input type="submit">
    </form>

    <?php
    session_start(); // начало сессии для работы с ними
	if (!empty($_POST['password']) and !empty($_POST['login'])) {
		$login = $_POST['login'];
		$password = $_POST['password'];
		
		$query = "SELECT * FROM users WHERE login='$login' AND password='$password'";
		$res = mysqli_query($link(всякий localhost или MySQL 8.0), $query);
		$user = mysqli_fetch_assoc($res);
		
		if (empty($user)) {
            $_SESSION['auth'] = true; // авторизация пользователя
			// прошел авторизацию
		} else {
			// неверно ввел логин или пароль
		}
	}

    $_SESSION['auth'] = null; // совершить выход из своего аккаунта
    
?>

    <!-- ДРУГАЯ СТРАНИЦА // ПРОВЕРЯЕМ ЕСЛИ ПОЛЬЗОВАТЕЛЬ АВТОРИЗОВАН ТО ВЫВОДИТСЯ СООТВЕТСТВУЮЩИЙ ТЕКСТ ИЛИ САМА СТРАНИЦА -->
    <!DOCTYPE html>
    <html>
        <head>
        </head>
        <body>
            <p>текст для любого пользователя</p>
            <?php
                if (!empty($_SESSION['auth'])) {
                    echo 'текст только для авторизованного пользователя';
                }
            ?>
            <p>текст для любого пользователя</p>
        </body>
    </html>


    <!-- РЕГИСТРАЦИЯ ЧЕРЕЗ БД ПРОСТАЯ -->
    <form action="" method="POST">
        <input name="login">
        <input name="password" type="password">
        <input type="submit">
    </form>

    <?php
        if (!empty($_POST['login']) and !empty($_POST['password'])) {
            $login = $_POST['login'];
            $password = $_POST['password'];
            
            $query = "INSERT INTO users SET login='$login', password='$password'";
            mysqli_query($link, $query);
        }
    ?>
    
</body>
</html>