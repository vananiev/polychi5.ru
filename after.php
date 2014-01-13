<?php
	/***************************************************************************

		Файл подключаемый после вывода страницы

	**************************************************************************/

	// Выполняем завершающие PHP-команды задаваемые с помощью run_at_the_end('functions();')
	run_ending_cmd();
	
	// Выполняем javascript-функции добавленные в строку RUN_AFTER_LOAD
?>
<script  type="text/javascript">
	document.write("<script  type='text/javascript'>");
	document.write(RUN_AFTER_LOAD);
	document.write("</script"+">");
</script>