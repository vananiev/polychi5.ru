<?php
/*-----------------------------------------------------------------------
		
						Пересчитываем рейтинги пользователей

------------------------------------------------------------------------*/
if( !user_in_group('ADMIN', R_MSG) ) 
	return false; //проверка права просматривать полную информацию

$res = $task->db->query("SELECT solver,user,price,rating FROM `$table_task` WHERE status='OK'");
while($row = $res->fetch_assoc()){
	if($row['solver']==NULL) continue;
	$solvers[ $row['solver'] ]['price'][] = $row['price'];
	$solvers[ $row['solver'] ]['rating'][] = $row['rating'];
	$clients[ $row['user'] ]['count'][] = NULL;
}
// Найдем среднюю оценку по решающему и среднюю стоимость решенных заданий
// Вычислим оценку по решающему
foreach ($solvers as &$solver) {
	$cnt = count($solver['price']);
	if($cnt==0) countinue;
	$sum_price = 0;
	$sum_rating = 0;
	foreach ($solver['price'] as $price)
		$sum_price += $price;
	foreach ($solver['rating'] as $rating)
		$sum_rating += $rating;
	$solver['avg_price'] = $sum_price / $cnt;
	$solver['avg_mark'] = $sum_rating / $cnt;
	$solver['mark'] = $solver['avg_mark'] * pow(1.01, $cnt) * pow(1.0001, $solver['avg_price']);
}

// нормализуем оценки относительно 5-ки
// Найдем максимальную оценку и будем считать ее за 5
// Остальные оценки будут относительны этой максимальной
$max_mark = 0.1;
foreach ($solvers as &$solver) 
	if($solver['mark']>$max_mark)
		$max_mark = $solver['mark'];
foreach ($solvers as &$solver)
	$solver['relative_mark'] = str_replace(',','.', 5.0 * $solver['mark'] / $max_mark );

// Обнулим рейтинги
$users->db->query("UPDATE `$table_users` SET `rating` = 0");

// Обновим рейтинг в таблице пользователей
foreach ($solvers as $id => &$solver){
	// Вместо %f передаем %s, т.к. дробное число может иметь разделитель ',' - это не допустимо для MYSQL
	$users->db->query("UPDATE `$table_users` SET `rating` = '%s' WHERE `id`=%u", array( $solver['relative_mark'],$id) );
}
echo "<br>Рейтинги авторов выставлены<br>";


// Рейтинг заказчиков по числу заказов
foreach ($clients as &$user)
	$user['relative_mark'] = count($user['count']);

foreach ($clients as $id => &$user){
	$users->db->query("UPDATE `$table_users` SET `rating` = '%u' WHERE `id`=%u", array( $user['relative_mark'],$id) );
}
echo "Рейтинги заказчиков выставлены<br>";
?> 
