<html>
<body>

<form action="" method="POST" enctype="multipart/form-data">
    <input type="file" name="csv" />
    <input type="submit"/>
</form>

</body>
</html>


<?php



$databasehost = "localhost";
$databasename = "mysql";
$databasetable = "test";
$databaseusername = "root";
$databasepassword = '';
$fieldseparator = ";"; //разделитель. У "нормального" csv - это запятая, у MS EXCEL - ";"

$addauto = 1;
$lines = 0; //счетчик количества записанных строк
$linearray = array();



// количество пропускаемых строк "сверху"

$string_trash = 1;



setlocale (LC_ALL, 'ru_Ru');
$connect = mysql_connect($databasehost,$databaseusername,$databasepassword);
if (!$connect) {
    die('Ошибка соединения: ' . mysql_error());
}


// Указываем, что общаемся с БД только в UTF-8
mysql_query("SET NAMES 'utf8'");
mysql_query("SET CHARACTER SET 'utf8'");
mysql_query("SET SESSION collation_connection = 'utf8_general_ci'");

$select_db =  mysql_select_db($databasename) or die(mysql_error());
if (!$select_db ) {
    die ('Не удалось выбрать базу db_data: ' . mysql_error());
}

$result = mysql_query("Select * from test");

$n=mysql_num_rows($result );



//вывод на страничку в виде таблицы
echo "<table border=1> 
<tr><th>ID</th><th>ARTIKUL</th><th>PRICE</th><th>count</th></tr>";

//вывод построчно
for($i=0;$i<$n;$i++)
    echo
    "<tr><td>",mysql_result($result,$i,id),
    "</td><td>",mysql_result($result,$i,ARTICUL),
    "</td><td>",mysql_result($result,$i,PRICE),
    "</td><td>",mysql_result($result,$i,COUNT),
    "</td></tr>";
echo "</table>";




if(isset($_FILES['csv'])) {
    if ($_FILES['csv'][size] > 0) {
    //Получаем CSV файл
    $file = $_FILES[csv][tmp_name];
    $handle = fopen($file,"r");

    //Обрабатываем в цикле CSV файл и добавляем данные в БД $rowsToInsert = array();
    $rowsCount =  100;
    $insertStmt = 'INSERT INTO test (`ARTICUL`, `PRICE`, `COUNT`) VALUES ';
    while ($data = fgetcsv($handle, 1000, ";", "'")) {
        if (3 == count($data)) {
            //Будем считать, что артикул в базе уникален.
            $selectArticul = "SELECT ARTICUL,COUNT, PRICE  FROM test WHERE ARTICUL= '$data[0]'";
            $CountArticul =  mysql_query($selectArticul); // выполняем запрос к базе с ограничением по арктикулу, чтобы получить его количество и цену
            $dataDataBase = mysql_fetch_array($CountArticul); //добавляем в массив с данными по нужному артикулу из базы
            $num_rows_mysql = mysql_num_rows($CountArticul); //Получаем количество строк в выборке.


            if ($num_rows_mysql <> 0) { //Если что то взяли из базы, тогда это будет UPDATE. Чтобы сложить количество товара в базе и в файле
                //У нас получается что элементы в массиве csv файла и в массиве mysql стоят в разных местах. По этому такие индексы у массивов(не ошибка)
                $data[2] = $data[2] + $dataDataBase[1]; //Складываем количество в базе и количество в файле для апдейта.

            $sqlUpdate = "UPDATE test SET COUNT=$data[2] WHERE ARTICUL='$data[0]'";
            $query = mysql_query($sqlUpdate);
            }
            else //Иначе если в базе нет данного артикула тогда это будет Insert
            {
                $row = array_map('mysql_real_escape_string', $data);
                $row[2] = 0;
                $rowsToInsert[] = '("' . implode('", "', $row) . '")';
            }

        }
        //Если набралось достаточное количество строк
        if ($rowsCount <= count($rowsToInsert)) {
            mysql_query($insertStmt . implode(', ', $rowsToInsert));
            $rowsToInsert = array();
        }
    }
    //Если остались строки для добавления
    if (!empty($rowsToInsert)) {
        mysql_query($insertStmt . implode(', ', $rowsToInsert));
        $rowsToInsert = array();
    }

}
}

