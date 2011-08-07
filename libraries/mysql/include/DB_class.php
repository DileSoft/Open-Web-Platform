<?

class sqlDB
{

	var $result = "";

	var $link;

	function do_select ($table, $where = array())
	{
		if (count($where))
		{
			foreach ($where as $field => $data)
			{
				$wheres .= "$field='$data' AND ";
			}
			$wheres = rtrim($wheres, ' AND ');
			$wheres = "WHERE ( {$wheres} )";
		}
		
		$this->query("SELECT * FROM $table $wheres");
	}

	function select_first ($request)
	{
		$this->query($request);
		
		return $this->fetch();
	}

	function select_and_fetch ($query)
	{
		$this->query($query);
		return $this->fetch();
	}

	function do_select_all ($table, $where)
	{
		$this->do_select($table, $where);
		
		while ($row = $this->fetch())
			$res[] = $row;
		return $res;
	}

	function do_insert ($table, $array, $addslashes = "1")
	{
		foreach ($array as $field => $data)
		{
			$fields .= $field . ',';
			if (! ereg('FROM_UNIXTIME\(.*\)', $data) && ! ereg('^NOW\(\)$', $data))
			{
				if ($addslashes)
				{
					$data = addslashes($data);
				}
				$datas .= "'$data',";
			} else
			{
				$datas .= "$data,";
			}
		
		}
		$fields = rtrim($fields, ',');
		$datas = rtrim($datas, ',');
		$this->query("INSERT INTO $table ($fields) VALUES ($datas)");
	}

	function do_update ($table, $where, $array, $addslashes = "1")
	{
		foreach ($array as $field => $data)
		{
			if (! ereg('FROM_UNIXTIME\(.*\)', $data) && ! ereg('^NOW\(\)$', $data))
			{
				if ($addslashes)
				{
					$data = addslashes($data);
				}
				$datas .= "`$field`='$data',";
			} else
			{
				$datas .= "`$field`=$data,";
			}
		}
		$datas = rtrim($datas, ',');
		$this->query("UPDATE $table SET $datas WHERE $where");
	}

	function select_or_insert ($table, $array = array())
	{
		$this->do_select($table, $array);
		if (! $this->num())
		{
			$this->do_insert($table, $array);
			$this->do_select($table, $array);
		}
		
		return $this->fetch();
	
	}

	function fetch_all_field ($table, $field, $add = '')
	{
		$this->query("SELECT $field FROM $table $add");
		
		while ($row = $this->fetch())
			$res[] = $row[$field];
		return $res;
	}

	function insert_or_update ($table, $where = '', $array = array())
	{
		if ($where)
		{
			return $this->do_update($table, $where, $array);
		} else
		{
			return $this->do_insert($table, $array);
		}
	}

	function insert_or_update_auto ($table, $where = '', $array = array())
	{
		$this->query("SELECT * FROM `{$table}` WHERE {$where}");
		
		if ($this->num())
		{
			return $this->do_update($table, $where, $array);
		} else
		{
			return $this->do_insert($table, $array);
		}
	}

	function sqlDB ($host = 'localhost', $login = 'root', $password = '', $db_name = 'mysql', $encoding = "utf8")
	{
		$this->link = mysql_connect($host, $login, $password) or die("Could not query:" . mysql_error($this->link));
		
		mysql_select_db($db_name, $this->link);
//		$this->query("SET NAMES '{$encoding}'");
	}

	function query ($request)
	{
		$this->result = mysql_query($request, $this->link) or die("Could not query:" . mysql_error($this->link) . "<br>Request: $request<br><br>");
		return $this->result;
	}

	function select_all ($request)
	{
		$this->query($request);
		return $this->fetch_all();
	}

	function fetch_all ()
	{
		$res = array(
		);
		while ($row = $this->fetch())
			$res[] = $row;
		return $res;
	}

	function fetch ()
	{
		return mysql_fetch_array($this->result, MYSQL_ASSOC);
	}

	function fetch_request ($result)
	{
		return mysql_fetch_array($result, MYSQL_ASSOC);
	}

	function num ()
	{
		return mysql_num_rows($this->result);
	}

	function get_insert_id ()
	{
		return mysql_insert_id($this->link);
	}
}

?>