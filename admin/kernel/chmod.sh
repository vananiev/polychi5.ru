#!/bin/bash

# Изменяем права доступа

# Корень сайта
if [ "$1" != ""  ] 
	then
	DIR=$1
else
	echo "$0: Не задана корневая директория скриптов (1 параметр)"
	exit 1
fi

# Пользователь:группа
OWNER="apache"
GROUP="vitek"

# Пользователь имеющий право на смену владельца папок и измениение прав доступа
SUSER="root"

# Список каталогов доступных для чтения (пути указывать относительно корневого каталога DIR)
# пример: RDIR="/about /info/save"
RDIR="/task/admin/solving/
	/public_html/files/temp/
        /public_html/files/modules/task/task/
        /public_html/files/modules/ticket/files/
        /public_html/files/modules/task/opened_solving/"
                                                        
# Список каталогов доступных для записи (пути указывать относительно корневого каталога DIR)
# пример: WDIR="/about /info/save"
WDIR="/task/admin/solving/
	/public_html/files/
	/public_html/files/temp/
	/public_html/files/modules/
	/public_html/files/modules/users/avatars/
	/public_html/files/modules/task/task/
	/public_html/files/modules/ticket/files/
	/public_html/files/modules/task/opened_solving/"

# Список файлов доступных для записи (пути указывать относительно корневого каталога DIR)
# пример: WDIR="/print.log /info/save/saved.txt"
WFILE=""

# Исполняемые файлы. Указывать через пробел
EFILE=".sh"

#
# 1. Каталоги
#
# =710
find $DIR ! -type f -exec chmod 770 {} \;

# 1.1 +чтение
if [ "$RDIR" != ""  ]
    then
    for name in $RDIR
    do
    chmod g+r $DIR/$name
    done
fi                    

# 1.2 Создание/удаление/переименование_файлов
if [ "$WDIR" != ""  ] 
    then
    for name in $WDIR
    do
    echo $DIR/$nam
    chmod g+w $DIR/$name
    done
fi

#
# 2. Файлы
#

# 2.1 Только чтение
#640
find $DIR -type f -exec chmod 660 {} \;

# 2.2 Запись+чтение
if [ "$WFILE" != "" ] 
    then
    for name in $WFILE
    do
    chmod g+w $DIR/$name
    done
fi

# 2.3 Исполняемые файлы
if [ "$EFILE" != "" ]
    then
    for name in $EFILE
    do
	find $DIR -maxdepth 170 -name "*$name" -exec chmod 0700 {} \; 
    done
fi

#
# 3. Меняем владельца
#

find $DIR -exec chown $OWNER:$GROUP {} \;

#
# 4. Вывод статистики
#
echo "Владецец:группа - "$OWNER:$GROUP
find $DIR -perm -g=w -type d  -exec echo "Директория доступна группе  на запись: "{} \;
find $DIR -perm -g=r -type d  -exec echo "Директория доступна группе на вывод содержимого: "{} \;
echo "Остальные папки имеют права только на доступ для группы"
find $DIR -perm -g=w -type f  -exec echo "Файл доступен группе на запись: "{} \;
find $DIR -perm -g=x -type f  -exec echo "Исполняемый файл для группы: "{} \;
echo "Остальные файлы доступны только на чтение группе"
echo "Владелец имеет полный доступ"