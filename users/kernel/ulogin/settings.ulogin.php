<?php

/*
 * uLogin Settings class
 */

if (!class_exists("usersuLoginPluginSettings")) {
    class usersuLoginPluginSettings{
        private $_uLoginOptionsName;
        private $_uLoginOptions;

        public function init($uLoginOptions=array()){
            $this->_uLoginOptionsName = 'uLoginPluginOptions';
            $init_settings = array(
                                        'display' => 'window',  // small, panel, но лни плохо отображатся в android, лучше window
                                        'providers' => 'vkontakte', //odnoklassniki,mailru,facebook',
                                        'hidden' => 'other',
                                        'fields' => 'first_name,last_name,email,photo', //photo
                                        'optional' => 'phone',
                                        'label' => ''
            );
            $this->_uLoginOptions = array_merge($init_settings, $uLoginOptions);
        }

        public function getOptions(){
            return $this->_uLoginOptions;
        }
    }
}
?>
