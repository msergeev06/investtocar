<?php

	global $MESS;

	class CInvestToCarDB {

		var $host;
		var $port;
		var $user;
		var $password;
		var $dbName;
		var $dbh;

		public function CInvestToCarDB ($host, $port, $user, $password, $database) {
			$this->host=$host;
			$this->port=$port;
			$this->user=$user;
			$this->password=$password;
			$this->dbName=$database;
			$this->Connect();
		}

		function Connect() {
			// connects to database
			$this->dbh=@mysql_connect($this->host, $this->user, $this->password);
			if (!@mysql_select_db($this->dbName, $this->dbh)) {
				$this->Error();
				return 0;
			} else {
				mysql_query("SET NAMES 'utf8';",$this->dbh);
				mysql_query("SET CHARACTER SET 'utf8';",$this->dbh);
				mysql_query("set character_set_client='utf8';",$this->dbh);
				mysql_query("set character_set_results='utf8';",$this->dbh);
				mysql_query("set collation_connection='utf8_general_ci';",$this->dbh);
				//mysql_query("SET SESSION collation_connection = 'utf8_general_ci';",$this->dbh);
				return 1;
			}
		}


		function Disconnect() {
			mysql_close($this->dbh);
		}


		function Error($query="") {
			echo "ERROR: (".mysql_errno().") ".mysql_error()."<br>".$query;
			/*
			registerError('sql', mysql_errno().": ".mysql_error()."\n$query");
			$err=new error(mysql_errno().": ".mysql_error()."<br>$query", 1);
			*/
			return 1;
		}


		function Select($query) {
			if ($result=mysql_query($query, $this->dbh)) {
				$res=array();
				for($i=0;$i<mysql_num_rows($result);$i++) {
					$rec=mysql_fetch_array($result, MYSQL_ASSOC);
					/*
					 if (Is_Array($rec))
					  foreach(array_keys($rec) as $k) {
					   if (is_numeric($k)) UnSet($rec[$k]);
					  }
					  */
					$res[]=$rec;
				}
				return $res;
			} else {
				$this->Error($query);
			}
		}

		function Query ($query) {
			if ($res=mysql_query($query, $this->dbh)) {
				return $res;
			}
			else {
				$this->Error($query);
			}
		}

		function Insert($query) {
			if (!mysql_query($query, $this->dbh)) {
				$this->error($qry);
				return 0;
			}
			return mysql_insert_id($this->dbh);
		}

		function Update($query) {
			if (!mysql_query($query, $this->dbh)) {
				$this->Error($query);
				return false;
			}
			return true;
		}

		function Delete($query) {
			if (!mysql_query($query, $this->dbh)) {
				$this->Error($query);
				return false;
			}
			return true;
		}
	}
