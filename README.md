Начальные данные:

дамп таблицы db.sql
файл выгрузки db.csv

Описание:

Таблица содержит 250 000 записей о товарах и включает в себя столбцы Артикул, Количество и Цена.
Файл выгрузки в формате csv содержит 300 000 записей, из которых 100 000 новые, а 200 000 уже присутствуют в таблице. 

Задача:

Загрузить данные из csv-файла в таблицу. 
Подсказка - сначала сделать и протестировать реализацию на небольшом объеме данных (файлы db_small.sql и db_small.csv), учитывая их расширяемость. Затем протестировать на больших данных. 

Описание:

Создать страницу, содержащую форму загрузки csv-файла, кнопку Импортировать и поле для вывода результата импорта. Форма должна позволять загрузить только файл подходящего формата, в ином случае выдавать ошибку. В идеале загрузка файла должна проходить в фоновом режиме.

При нажатии на кнопку должен запускаться скрипт, позволяющий импортировать данные из csv файла в таблицу, при условии - если файл не содержит товары с артикулами, которые есть в бд, то в бд в поле Количество для таких записей нужно поставить 0.

Результат импорта отображать на странице с формой.

После импорта в таблице должно стать 350 000 записей.


Результат:

Задача должна быть решена без использования cms и фреймворков. 


Решение:
Подключаемся к базе выводим в таблицу.
Затем когда пользователь выбирает файл начинаем его читать построчно и помещать в массив.
Я решил, что логично было бы добавить к имеющимся записям количества в БД количество из таблицы.
Идём циклом while по файлу. Берём артикул из файла, запрашиваем из базы. Если выборка не пустая, тогда складываем количество из базы и количество из файла.
Если выборка пустая делаем Insert.

