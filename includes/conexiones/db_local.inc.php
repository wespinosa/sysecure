<?php
//include_once( $_SERVER['DOCUMENT_ROOT'] . '/config.ini.php' );
include_once( 'config_local.ini.php' );
class database {
	
	var $db = null;
	var $db_name = null;
	var $db_host = null;
	var $db_user = null;
	var $db_passwd = null;
	
	function database(){
		$this->db = new mysqli(HOST_NAME, USER_NAME, USER_PASSWD, DB_NAME);
		$this->db->query("SET NAMES 'utf8'");
		if ($this->db->connect_error) {
	    printf("Connect failed: %s\n", mysqli_connect_error());
	    exit();
		}

		return $this->db;
	}
	
	function query( $query ){
		$obj = $this->db->query($query);
		return $obj;
	}
	
	
	function maxid( $campo,$table ){
		$query = "SELECT MAX($campo) AS lastid FROM $table";
		$obj = $this->db->query($query);
                $id = $obj->fetch_object();
		return $id->lastid;
	}

	function lastid( ){
		
		return $this->db->insert_id; 
		
	}

	
	function newid( $table ){
		$id = $this->maxid($table);
		if ($id)
			$obj = $id + 1;
		else 
			$obj = 1;	
		
		return $obj;
	}
	
	function getlist( $id , $name ,$field ){
		$query = "SELECT id, description FROM $name";
		$obj = $this->db->query($query);
		$retval = '<select name="'.$field.'" class="formcontent" id="'.$field.'">
								  <option value="0">Ninguno</option>';
		while ($row = $obj->fetch_object() ){
			$selected = ($id == $row->id)?'"selected="selected"':"";
			$retval .= '<option value="'.$row->id.'" '.$selected.'>'.$row->description.'</option>';		      
		}
		$retval .= '</select>';
		
		return $retval;
	}

         public function num_rows($query_object) {
        $num = mysql_num_rows ( $query_object );
        return $num;
    }
    
}

?>