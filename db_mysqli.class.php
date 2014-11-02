<?php
/***************************************
MySQL通用函數庫 v2.2 for mysqli by a0000778
MIT License
***************************************/
class DB{
	private $dblink;
	
	function __construct($mysql){
		$this->dblink=$mysql;
	}
	function __destruct(){
		$this->disconnect();
	}
	static function connect($host,$user,$pass,$dbname){
		$dblink=new mysqli($host,$user,$pass,$dbname);
		if($dblink->connect_error){
			if($dblink->set_charset('UTF8');) return new DB($dblink);
		}
		return false;
	}
	function delete($table,$where,$wherearg=array()){
		$at=0;
		foreach($wherearg as $v){
			if(($at=strpos($where,'?',$at))===false) return false;
			if(is_numeric($v)) $sql=substr_replace($where,$v,$at,1);
			else $where=substr_replace($where,'\''.$this->dblink->real_escape_string($v).'\'',$at,1);
		}
		$query=$this->dblink->query('DELETE FROM `'.$table.'` WHERE '.$where.';');
		return $query;
	}
	function disconnect(){
		$this->dblink->close();
	}
	function exec($sql){
		return $this->dblink->query($sql);
	}
	function fetch($query){
		return $query->fetch_array();
	}
	function fetchall($query){
		return $query->fetch_all(MYSQLI_BOTH);
	}
	function insert($table,$data=array()){
		$p='';
		$d='';
		$s='';
		foreach($data as $k => $v){
			$p.=$s.'`'.$k.'`';
			$d.=$s.'\''.$this->dblink->real_escape_string($v).'\'';
			$s=', ';
		}
		return $this->dblink->query('INSERT INTO `'.$table.'` ('.$p.') VALUES ('.$d.');');
	}
	function query($sql,$arg=array()){
		$at=0;
		foreach($arg as $v){
			if(($at=strpos($sql,'?',$at))===false) return false;
			if(is_numeric($v)) $sql=substr_replace($sql,$v,$at,1);
			else $sql=substr_replace($sql,'\''.$this->dblink->real_escape_string($v).'\'',$at,1);
		}
		return $this->dblink->query($sql);
	}
	function update($table,$data=array(),$where='',$wherearg=array()){
		$d='';
		$s='';
		foreach($data as $k => $v){
			$d.=$s.'`'.$k.'` = \''.$this->dblink->real_escape_string($v).'\'';
			$s=', ';
		}
		$at=0;
		foreach($wherearg as $v){
			if(($at=strpos($where,'?',$at))===false) return false;
			if(is_numeric($v)) $sql=substr_replace($where,$v,$at,1);
			else $where=substr_replace($where,'\''.$this->dblink->real_escape_string($v).'\'',$at,1);
		}
		return $this->dblink->query('UPDATE `'.$table.'` SET '.$d.($where? ' WHERE '.$where:'').';');
	}
}
?>
