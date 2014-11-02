<?php
/***************************************
MySQL通用函數庫 v2.2 for PDO by a0000778
MIT License
***************************************/
class DB{
	private $dblink;
	
	function __construct($pdo){
		$this->dblink=$pdo;
	}
	function __destruct(){
		$this->disconnect();
	}
	static function connect($host,$user,$pass,$dbname){
		try{
			$dblink=new PDO('mysql:host='.$host.';dbname='.$dbname,$user,$pass);
			$dblink->exec('SET NAMES UTF8;');
			return new DB($dblink);
		}catch(PDOException $e){
			return false;
		}
	}
	function delete($table,$where,$wherearg=array()){
		$query=$this->dblink->prepare('DELETE FROM `'.$table.'` WHERE '.$where.';');
		$query->execute($wherearg);
		return $query;
	}
	function disconnect(){
		$this->dblink=null;
	}
	function exec($sql){
		return $this->dblink->query($sql);
	}
	function fetch($query){
		return $query->fetch();
	}
	function fetchall($query){
		$data=array();
		while($item=$query->fetch())
			$data[]=$item;
		return $data;
	}
	function insert($table,$data=array()){
		$p='';
		$d=array();
		$s='';
		foreach($data as $k => $v){
			$p.=$s.'`'.$k.'`';
			$d[]=$v;
			$s=', ';
		}
		$query=$this->dblink->prepare('INSERT INTO `'.$table.'` ('.$p.') VALUES ('.implode(',',array_fill(0,count($d),'?')).');');
		$query->execute($d);
		return $query;
	}
	function query($sql,$arg=array()){
		//PHP錯誤，無法處理LIMIT等一類
		//處理對LIMIT的BUG
		if(preg_match('/ LIMIT +\? *(, *\?)?( |,|;|$)/',$sql,$match)){
			$limitat=strpos($sql,$match[0]);
			$argcount=substr_count($sql,'?',0,$limitat);
			if($match[1]){
				$sql=substr_replace($sql,' LIMIT '.(int)$arg[$argcount].','.(int)$arg[$argcount+1].$match[2],$limitat,strlen($match[0]));
				array_splice($arg,$argcount,2);
			}else{
				$sql=substr_replace($sql,' LIMIT '.(int)$arg[$argcount].$match[2],$limitat,strlen($match[0]));
				array_splice($arg,$argcount,1);
			}
		}
		
		$query=$this->dblink->prepare($sql);
		$query->setFetchMode(PDO::FETCH_ASSOC);
		$query->execute($arg);
		return $query;
	}
	function update($table,$data=array(),$where='',$wherearg=array()){
		$p='';
		$d=array();
		$s='';
		foreach($data as $k => $v){
			$p.=$s.'`'.$k.'` = ?';
			$d[]=$v;
			$s=', ';
		}
		$query=$this->dblink->prepare('UPDATE `'.$table.'` SET '.$p.($where? ' WHERE '.$where:'').';');
		$query->execute(array_merge($d,$wherearg));
		return $query;
	}
}
?>
