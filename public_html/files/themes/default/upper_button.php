<?php
  // кнопка перехода на мобильную версию сайта
  if( mobile_detect() && !isset($_COOKIE['to_mobile_not']) ){ ?>
  <div id="to_mobile_ver" onclick="location.href='http://<?php echo MOBILE_DOMAIN; ?>'">Мобильная версия</div>
  <div id='to_mobile_ver_close' onclick="close_to_mobile();">x</div>
  <script type="text/javascript">
    function close_to_mobile(){
      $('#to_mobile_ver').fadeOut(500);
      $('#to_mobile_ver_close').fadeOut(500);
      document.cookie = "to_mobile_not=dont display; path=/; domain=<?php echo ".".DOMAIN; ?>;";
      }
  </script>
<?php } ?>
