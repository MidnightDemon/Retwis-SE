<?php
/*******************************************************************************
 * Redis PHP Bindings - http://code.google.com/p/redis/
 *
 * Copyright 2009 Ludovico Magnocavallo
 * Released under the same license as Redis.
 *
 * Version: 0.1
 *
 * $Revision: 139 $
 * $Date: 2009-03-15 22:59:40 +0100 (Dom, 15 Mar 2009) $
 *
 * Edited by M
 *
 ******************************************************************************/


class Redis {

	var $server;
	var $port;
	var $_sock;

	function Redis($host='localhost', $port=6379) {
		$this->host = $host;
		$this->port = $port;
	}

	//manual set
	function RedisSet($host, $port) {
		$this->host = $host;
		$this->port = $port;
	}	

	function connect() {
		if ($this->_sock)
			return;
		if ($sock = fsockopen($this->host, $this->port, $errno, $errstr)) {
			$this->_sock = $sock;
			return;
		}
		$msg = "Cannot open socket to {$this->host}:{$this->port}";
		if ($errno || $errmsg)
			$msg .= "," . ($errno ? " error $errno" : "") . ($errmsg ? " $errmsg" : "");
		trigger_error("$msg.", E_USER_ERROR);
	}

	function disconnect() {
		if ($this->_sock)
			@fclose($this->_sock);
		$this->_sock = null;
	}

	function &ping() {
		$this->connect();
		$this->_write("PING\r\n");
		$ret = $this->get_response(); 
		return $ret;
	}

	function &do_echo($s) {
		$this->connect();
		$this->_write("ECHO " . strlen($s) . "\r\n$s\r\n");
		return $this->_get_value();
	}

	private function format($args){
		$s = "*" . count($args) . "\r\n";
		foreach ($args as $i => $value)
			$s .= "$" . strlen($value). "\r\n$value\r\n";

		return $s;
	}

	// BORKED ATM

	function &set($name, $value) {
		print("in set ... " . $name . " " . $value . "<br />");

		$this->connect();
		$a = array("SET", (string)$name, (string)$value);
		$s = $this->format($a);
		//$s = $this->format(array("SET", (string)$name, (string)$value));
		//print("command: " . $s . "<br />");
		//$this->_write("*3\r\n$3\r\nSET\r\n$" . strlen($name). "\r\n$name\r\n$" . strlen($value) . "\r\n$value\r\n");
		
		$this->_write($s);
		$ret = $this->get_response();
		return $ret;
	}

	//END BORKEDNESS 

	function &setnx($name, $value) {
		print("in set next ... " . $name . " " . $value . "<br />");

		$this->connect();
		$this->_write("SETNX $name " . strlen($value) . "\r\n$value\r\n");
		$ret = $this->get_response(); return $ret;
	}	

	function &get($name) {
		$this->connect();
		$this->_write("GET $name\r\n");
		$ret = $this->_get_value();
		return $ret;
	}

	function &incr($name, $amount=1) {
		$this->connect();
		if ($amount == 1)
			$this->_write("INCR $name\r\n");
		else
			$this->_write("INCRBY $name $amount\r\n");
		$ret = $this->get_response();
		return $ret;
	}

	function &decr($name, $amount=1) {
		$this->connect();
		if ($amount == 1)
			$this->_write("DECR $name\r\n");
		else
			$this->_write("DECRBY $name $amount\r\n");
		$ret = $this->get_response(); return $ret;
	}

	function &exists($name) {
		$this->connect();
		$this->_write("EXISTS $name\r\n");
		$ret = $this->get_response(); return $ret;
	}

	function &del($name) {
		$this->connect();
		$a = array("DEL", $name);
		$s = $this->format($a);
		$this->_write($s);		
		$ret = $this->get_response(); 
		return $ret;
	}

	function &keys($pattern) {
		$this->connect();
		$a = array("KEYS", $pattern);
		$s = $this->format($a);
		$this->_write($s);
		return explode(' ', $this->_get_value());
	}

	function &randomkey() {
		$this->connect();
		$a = array("RANDOMKEY");
		$s = $this->format($a);
		$this->_write($s);
		$ret = $this->get_response(); 
		return $ret;
	}

	function &renamenx($src, $dst) {
		$this->connect();
		$a = array("RENAMENX", (string)$src, (string)$dst);
		$s = $this->format($a);
		$this->_write($s);		
		$ret = $this->get_response(); 
		return $ret;
	}	

	function &rename($src, $dst) {
		$this->connect();
		$a = array("RENAME", (string)$src, (string)$dst);
		$s = $this->format($a);
		$this->_write($s);
		$ret = $this->get_response(); 
		return $ret;
	}

	function &expire($name, $time) {
		$this->connect();
		$a = array("EXPIRE", (string)$name, (string)$time);
		$s = $this->format($a);
		$this->_write($s);
		$ret = $this->get_response(); 
		return $ret;
	}

	private function &push($name, $value, $tail) {
		// default is to append the element to the list
		$this->connect();
		$a = array((string)$tail, (string)$name, (string)$value);
		$s = $this->format($a);
		$this->_write($s);
		$ret = $this->get_response(); 
		return $ret;
	}			

	function &lpush($name, $value) {
		return $this->push($name, $value, "LPUSH");
	}

	function &rpush($name, $value) {
		return $this->push($name, $value, "RPUSH");
	}	

	function &ltrim($name, $start, $end) {
		$this->connect();
		$this->_write("LTRIM $name $start $end\r\n");
		$ret = $this->get_response(); 
		return $ret;
	}

	function &lindex($name, $index) {
		$this->connect();
		$this->_write("LINDEX $name $index\r\n");
		return $this->_get_value();
	}

	function &pop($name, $tail=true) {
		$this->connect();
		$this->_write(
			($tail ? 'RPOP' : 'LPOP') .
			" $name\r\n"
			);
		return $this->_get_value();
	}

	function &llen($name) {
		$this->connect();
		$this->_write("LLEN $name\r\n");
		$ret = $this->get_response(); return $ret;
	}

	function &lrange($name, $start, $end) {
		$this->connect();
		$this->_write("LRANGE $name $start $end\r\n");
		$ret = $this->get_response(); 
		return $ret;
	}

	function &sort($name, $query=false) {
		$this->connect();
		$this->_write($query == false ? "SORT $name\r\n" : "SORT $name $query\r\n");
		$ret = $this->get_response(); 
		return $ret;
	}

	function &lset($name, $value, $index) {
		$this->connect();
		$this->_write("LSET $name $index " . strlen($value) . "\r\n$value\r\n");
		$ret = $this->get_response(); 
		return $ret;
	}

	function &sadd($name, $value) {
		$this->connect();
		$this->_write($this->format(array("SADD", (string)$name, (string)$value)));
		$ret = $this->get_response(); 
		return $ret;
	}

	function &srem($name, $value) {
		$this->connect();
		$this->_write("SREM $name " . strlen($value) . "\r\n$value\r\n");
		$ret = $this->get_response(); 
		return $ret;
	}

	function &sismember($name, $value) {
		$this->connect();
		$this->_write("SISMEMBER $name " . strlen($value) . "\r\n$value\r\n");
		$ret = $this->get_response(); 
		return $ret;
	}

	function &sinter($sets) {
		$this->connect();
		$this->_write('SINTER ' . implode(' ', $sets) . "\r\n");
		$ret = $this->get_response(); 
		return $ret;
	}

	function &smembers($name) {
		$this->connect();
		$this->_write("SMEMBERS $name\r\n");
		$ret = $this->get_response(); 
		return $ret;
	}

	function &scard($name) {
		$this->connect();
		$this->_write("SCARD $name\r\n");
		$ret = $this->get_response(); 
		return $ret;
	}

	function &select_db($name) {
		$this->connect();
		$this->_write("SELECT $name\r\n");
		$ret = $this->get_response(); 
		return $ret;
	}

	function &move($name, $db) {
		$this->connect();
		$this->_write("MOVE $name $db\r\n");
		$ret = $this->get_response(); 
		return $ret;
	}

	function &save($background=false) {
		$this->connect();
		$this->_write(($background ? "BGSAVE\r\n" : "SAVE\r\n"));
		$ret = $this->get_response(); 
		return $ret;
	}

	function &lastsave() {
		$this->connect();
		$this->_write("LASTSAVE\r\n");
		$ret = $this->get_response(); 
		return $ret;
	}

	function &flush($all=false) {
		$this->connect();
		$this->_write($all ? "FLUSH\r\n" : "FLUSHDB\r\n");
		$ret = $this->get_response(); 
		return $ret;
	}

	function &info() {
		$this->connect();
		$this->_write("INFO\r\n");
		$info = array();
		$data =& $this->get_response();
		foreach (explode("\r\n", $data) as $l) {
			if (!$l)
				continue;
			list($k, $v) = explode(':', $l, 2);
			$_v = strpos($v, '.') !== false ? (float)$v : (int)$v;
			$info[$k] = (string)$_v == $v ? $_v : $v;
		}
		return $info;
	}

	function _write($s) {
		while ($s) {
			$sock = $this->_sock;
			$i = fwrite($sock, $s);
			if ($i == 0) // || $i == strlen($s))
				break;
			$s = substr($s, $i);
		}
	}

	function &_read($len=1024) {
		if ($s = fgets($this->_sock))
			return $s;
		$this->disconnect();
		trigger_error("Cannot read from socket.", E_USER_ERROR);
	}

	function get_response() {
		$data = trim($this->_read());

		print("data: " . $data . "<br />");

		$c = $data[0];
		$data = substr($data, 1);

		switch ($c) {
			case '-':
				trigger_error(substr($data, 0, 4) == 'ERR ' ? substr($data, 4) : $data, E_USER_ERROR);
				break;
			case '+':
				return $data;
			case '*':
				$num = (int)$data;
				if ((string)$num != $data)
					trigger_error("Cannot convert multi-response header '$data' to integer", E_USER_ERROR);
				$result = array();
				for ($i=0; $i<$num; $i++)
					$result[] =& $this->_get_value();
				return $result;
			default:
				$ret = $this->_get_value($c . $data);
				return $ret;
		}
	}

	function _get_value($data=null) {
		if ($data === null)
			$data =& trim($this->_read());
		if ($data == '$-1')
			return null;
		$c = $data[0];
		$data = substr($data, 1);
		$i = strpos($data, '.') !== false ? (int)$data : (float)$data;
		if ((string)$i != $data)
			trigger_error("Cannot convert data '$c$data' to integer", E_USER_ERROR);
		if ($c == ':')
			return $i;
		if ($c != '$')
			trigger_error("Unkown response prefix for '$c$data'", E_USER_ERROR);
		$buffer = '';
		while (true) {
			$data =& $this->_read();
			$i -= strlen($data);
			$buffer .= $data;
			if ($i < 0)
				break;
		}
		$s = substr($buffer, 0, -2);
		return $s;
	}

}   

//$r =& new Redis();
//var_dump($r->info());

?>
