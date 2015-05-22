<?php
Class db_data{
  /********************************
	            SETTINGS
	*********************************/

	protected $host_db = 'localhost:3306';
	protected $user_db = 'root';
	protected $pass_db = '1LcL880y';
	protected $name_db = 'webapp';

	public function get_db_data(){
	  $data = array('host_db'=>$this->host_db, 'user_db'=>$this->user_db, 'pass_db'=>$this->pass_db, 'name_db'=>$this->name_db);
	  return $data;
	}
}
?>