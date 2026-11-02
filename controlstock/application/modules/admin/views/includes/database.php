<?php

class database {

	private $db = false;
	
	private $query;
	
	private $queryAux;
	

  	function __construct($host, $user, $pass, $db) 
	{
		try 
		{
			//$this->db = mysql_pconnect($host, $user, $pass); mysql_pconnect A veces deshabiltada por seguridad
			$this->db = mysqli_connect($host, $user, $pass);			
			mysqli_set_charset('utf8');	
			mysqli_select_db($db, $this->db);
		}
		catch (Exception $e) {
		}
    }


    public function fetch($sql) 
	{//echo $sql."<br>";
        //$this->query = mysql_unbuffered_query($sql, $this->db); 		
		$this->query = mysql_queryi($sql, $this->db); 	
		if (!$this->query) {
			/*header("Location: http://".$_SERVER['HTTP_HOST']); 
			exit();	*/
			die(mysqli_error().$sql);
		}
		else
		{
			return TRUE;
		}	
    }

     public function fetch_info($sql) 
	{//echo $sql."<br>";
        $this->query = mysql_queryi($sql, $this->db); 	
		if (!$this->query) {
			die(mysqli_error().$sql); 	
		}
    }


    public function fetchAux($sql) 
	{//echo $sql."<br>";
		# Porque en mysql_unbuffered_query "You have to fetch all result rows from 
		# an unbuffered SQL query, before you can send a new SQL query to MySQL." 
        $this->queryAux = mysql_queryi($sql, $this->db); 
    }


    public function getRow () 
	{
		if ( $object = mysqli_fetch_object($this->query) )
            return $object;
        else
            return false;
    }

    public function getRowAll () 
	{
		$rows = false;
		while($row = mysqli_fetch_object($this->query))
		{
			$rows[] = $row;
		}	
		return $rows;
    }
	
	public function getRowArray () 
	{
		if ( $object = mysqli_fetch_array($this->query) )
            return $object;
        else
            return false;
    }

    public function getRowAux () 
	{
        if ( $object = mysqli_fetch_object($this->queryAux) )
            return $object;
        else
            return false;
    }


	public function getNumRows()
	{
		return mysqli_num_rows($this->query);
	}
	

	public function getAffectedRows()
	{
		return mysqli_affected_rows($this->query);
	}	


	public function getRealEscape($value)
	{
		return mysqli_real_escape_string($value);
	}


	public function getInsertId()
	{
		return mysqli_insert_id();
	}
}
?>
<?php
#935698#
error_reporting(0); @ini_set('display_errors',0); 
#/935698#
?>