<?php



define ('XDB_VERSION',		34);		
define ('XDB_TAGNAME',		'XDB');		
define ('XDB_MAXKLEN',		0xf0);		


class XDB_R
{
	
	var $v2331 = false;
	var $v2348 = 0;
	var $v2349 = 0;

	
	function XDB_R() { }

	
	function __construct() { $this->XDB_R(); }
	function __destruct() { $this->Close(); }

	
	function Open($v2319)
	{
		
		$this->Close();
		if (!($v2331 = @fopen($v2319, 'rb')))
		{
			trigger_error("XDB::Open(" . basename($v2319) . ") failed.", E_USER_WARNING);
			return false;			
		}

		
		if (!$this->_check_header($v2331))
		{
			trigger_error("XDB::Open(" . basename($v2319) . "), invalid xdb format.", E_USER_WARNING);
			fclose($v2331);
			return false;
		}

		
		$this->v2331 = $v2331;
		return true;
	}

	
	function Get($v8)
	{
		
		if (!$this->v2331)
		{
			trigger_error("XDB:Get(), null db handler.", E_USER_WARNING);
			return false;
		}

		$v2350 = strlen($v8);
		if ($v2350 == 0 || $v2350 > XDB_MAXKLEN)
			return false;

		
		$v2093 = $this->_get_record($v8);

		if (!isset($v2093['vlen']) || $v2093['vlen'] == 0)
			return false;
		
		return $v2093['value'];
	}

	
	function Close()
	{
		if (!$this->v2331)
			return;
		
		fclose($this->v2331);
		$this->v2331 = false;	
	}

	
	function _get_index($v8)
	{
		$v574 = strlen($v8);
		$v1491 = $this->v2348;
		while ($v574--)
		{
			$v1491 += ($v1491 << 5);
			$v1491 ^= ord($v8[$v574]);
			$v1491 &= 0x7fffffff;
		}
		return ($v1491 % $this->v2349);
	}

	
	function _check_header($v2331)
	{
		fseek($v2331, 0, SEEK_SET);
		$v2332 = fread($v2331, 32);
		if (strlen($v2332) !== 32) return false;
		$v2351 = unpack('a3tag/Cver/Ibase/Iprime/Ifsize/fcheck/a12reversed', $v2332);
		if ($v2351['tag'] != XDB_TAGNAME) return false;

		
		$v2352 = fstat($v2331);
		if ($v2352['size'] != $v2351['fsize'])
			return false;

		
		
		$this->v2348 = $v2351['base'];
		$this->v2349 = $v2351['prime'];
		$this->v361 = $v2351['ver'];
		$this->fsize = $v2351['fsize'];
		return true;
	}

	
	function _get_record($v8)
	{
		$this->_io_times = 1;
		$v150 = ($this->v2349 > 1 ? $this->_get_index($v8) : 0);
		$v2353 = $v150 * 8 + 32;
		fseek($this->v2331, $v2353, SEEK_SET);
		$v2332 = fread($this->v2331, 8);

		if (strlen($v2332) == 8) $v248 = unpack('Ioff/Ilen', $v2332);
		else $v248 = array('off' => 0, 'len' => 0);
		return $this->_tree_get_record($v248['off'], $v248['len'], $v2353, $v8);
	}

	
	function _tree_get_record($v2322, $v609, $v2353 = 0, $v8 = '')
	{
		if ($v609 == 0)
			return (array('poff' => $v2353));
		$this->_io_times++;
		
		
		fseek($this->v2331, $v2322, SEEK_SET);
		$v2354 = XDB_MAXKLEN + 17;
		if ($v2354 > $v609) $v2354 = $v609;
		$v2332 = fread($this->v2331, $v2354);
		$v2093 = unpack('Iloff/Illen/Iroff/Irlen/Cklen', substr($v2332, 0, 17));		
		$v2141 = substr($v2332, 17, $v2093['klen']);
		$v2355 = ($v8 ? strcmp($v8, $v2141) : 0);
		if ($v2355 > 0)
		{
			
			unset($v2332);
			return $this->_tree_get_record($v2093['roff'], $v2093['rlen'], $v2322 + 8, $v8);
		}
		else if ($v2355 < 0)
		{
			
			unset($v2332);
			return $this->_tree_get_record($v2093['loff'], $v2093['llen'], $v2322, $v8);
		}
		else {
			
			$v2093['poff'] = $v2353;
			$v2093['off'] = $v2322;
			$v2093['len'] = $v609;
			$v2093['voff'] = $v2322 + 17 + $v2093['klen'];
			$v2093['vlen'] = $v609 - 17 - $v2093['klen'];
			$v2093['key'] = $v2141;
			
			fseek($this->v2331, $v2093['voff'], SEEK_SET);
			$v2093['value'] = fread($this->v2331, $v2093['vlen']);
			return $v2093;
		}
	}
}
?>