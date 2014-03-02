<?php
/***************************************
MySQL通用函數庫 v2.1 for mysql by a0000778
授權採用 CC BY 3.0 (http://creativecommons.org/licenses/by/3.0/deed)
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
		if($dblink=mysql_connect($host,$user,$pass)){
			if(version_compare(PHP_VERSION,'5.2.3','>='))
				mysql_set_charset('UTF8',$dblink);
			else
				mysql_query('SET NAMES UTF8;',$dblink);
			if(mysql_select_db($dbname, $dblink)) return new DB($dblink);
		}
		return false;
	}
	function delete($table,$where,$wherearg=array()){
		$at=0;
		foreach($wherearg as $v){
			if(($at=strpos($where,'?',$at))===false) return false;
			if(is_numeric($v)) $sql=substr_replace($where,$v,$at,1);
			else $where=substr_replace($where,'\''.mysql_real_escape_string($v).'\'',$at,1);
		}
		$query=mysql_query('DELETE FROM `'.$table.'` WHERE '.$where.';',$this->dblink);
		return $query;
	}
	function disconnect(){
		mysql_close($this->dblink);
	}
	function exec($sql){
		return mysql_query($sql,$this->dblink);
	}
	function fetch($query){
		return mysql_fetch_array($query);
	}
	function fetchall($query){
		$data=array();
		while($item=mysql_fetch_array($query))
			$data[]=$item;
		return $data;
	}
	function insert($table,$data=array()){
		$p='';
		$d='';
		$s='';
		foreach($data as $k => $v){
			$p.=$s.'`'.$k.'`';
			$d.=$s.'\''.mysql_real_escape_string($v).'\'';
			$s=', ';
		}
		return mysql_query('INSERT INTO `'.$table.'` ('.$p.') VALUES ('.$d.');',$this->dblink);
	}
	function query($sql,$arg=array()){
		$at=0;
		foreach($arg as $v){
			if(($at=strpos($sql,'?',$at))===false) return false;
			if(is_numeric($v)) $sql=substr_replace($sql,$v,$at,1);
			else $sql=substr_replace($sql,'\''.mysql_real_escape_string($v).'\'',$at,1);
		}
		return mysql_query($sql,$this->dblink);
	}
	function update($table,$data=array(),$where='',$wherearg=array()){
		$d='';
		$s='';
		foreach($data as $k => $v){
			$d.=$s.'`'.$k.'` = \''.mysql_real_escape_string($v).'\'';
			$s=', ';
		}
		$at=0;
		foreach($wherearg as $v){
			if(($at=strpos($where,'?',$at))===false) return false;
			if(is_numeric($v)) $sql=substr_replace($where,$v,$at,1);
			else $where=substr_replace($where,'\''.mysql_real_escape_string($v).'\'',$at,1);
		}
		return mysql_query('UPDATE `'.$table.'` SET '.$d.($where? ' WHERE '.$where:'').';',$this->dblink);
	}
}
?>
