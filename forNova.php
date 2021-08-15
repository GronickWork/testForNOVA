<?php
    $name = $_GET['name'];
    $email = $_GET['email'];
    $phone = $_GET['phone'];
    $rvname = '/^[a-z0-9 \.!_-]+$/iu';//Регулярное выражение для проверки полученного имени 
    $rvemail = '/^[а-яё\w-.]+@[\w-]+(\.[\w-]+)*$/iu';//Выражение для проверки email
    $rvphone = '/\+?([0-9-() ]+)/iu'//Для проверки номера телефона
    if(!preg_match($rvname, $name)){
        //Здесь что-то делаем, если в имени что-то кроме русских букв
    }
    if(!preg_match($rvemail, $email)){
        //Здесь что-то делаем, если введенные данные не являются почтой
    }
    if(!preg_match($rvphone, $phone)){
        //Здесь что-то делаем, если введенные данные не являются номером телефона
    }
    $connect = new mysqli($servername, $username, $password, $nameDb);/* Подключаем БД
    Здесь:
        $servername - Имя хоста
        $username - Имя пользователя БД
        $password - Пароль для входа в БД
        $nameDb - Имя БД
    */
    if($connect ->connect_error) {die("Connection failed: " . $conn->connect_error);}//Если подключение не удалось

    $resEmail = mysqli_query($connect, "SELECT `$nmstb` FROM `$table` WHERE 1");//Получаем все email
    $resPhone = mysqli_query($connect, "SELECT `$nmphone` FROM `$table` WHERE 1");//Получаем все телефоны
    /*Здесь и далее:
        $table - Имя таблицы, где хранятся email, телефоны и Имена пользователей
        $nmstb - Имя столбца, где хранятся email
        $nmphone - Имя столбца, где хранянтся телефоны
        $nmname - Имя столбца, где хранятся имена пользователей
    */
    $dbEmail = mysqli_fetch_array($resEmail);//Получаем все email в массив
    $dbphone = mysqli_fetch_array($resPhone);//Получаем все телефоны в массив
    if(in_array($email, $dbEmail)) {//Если email есть в БД - обновляем все записи
        $id = mysqli_query($connect, "SELECT `ID` FROM `$table` WHERE `$nmstb` = '$email'");//Получаем id для обновления
        $escapEmail = sprintf("UPDATE `$table` SET `$nmstb`='%s', `$nmphone`='%s', `$nmname`='%s' WHERE `ID`='$id'",
        mysqli_real_escape_string($connect, $email), mysqli_real_escape_string($connect, $phone),
        mysqli_real_escape_string($connect, $name));
        // В этой и строке выше экранируем запрос к БД от вредного кода
        mysqli_query($connect, $escapEmail);
        mysqli_close($connect);

    } else {//Если email нет в БД - Заводим нового пользователя
        $namescap = sprintf("INSERT INTO $table(`ID`,`$nmstb`,`$nmphone`,`$nmname`)VALUES(NULL,'%s','%s','%s')",
        mysqli_real_escape_string($connect, $email), mysqli_real_escape_string($connect, $phone),
        mysqli_real_escape_string($connect, $name));
        mysqli_query($connect, $namescap);
        mysqli_close($connect);
    }
    exit;
?>