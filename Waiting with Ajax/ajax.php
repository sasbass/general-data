<?php
class Ajax {
	private $ident = array('func'=>null);
	public function __construct(){

	}

	public function getMethod($method){
		if(method_exists($this,$method)) {
			echo $this->$method();
		} else {
			echo 'This method not exists!';
		}
	}

	private function check(){
		$this->ident['func'] = 'check';
		sleep(6); 	// Only for debug.
		return json_encode($this->ident);
	}

	private function getJackpot(){
		$this->ident['func'] = 'getJackpot';
		sleep(2); 	// Only for debug.
		return json_encode($this->ident);
	}
}

$ajax = new Ajax();
$ajax->getMethod($_POST['function']);