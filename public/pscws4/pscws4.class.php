<?php



require_once (dirname(__FILE__) . '/xdb_r.class.php');


define ('PSCWS4_RULE_MAX',		31);	
define ('PSCWS4_RULE_SPECIAL',	0x80000000);
define ('PSCWS4_RULE_NOSTATS',	0x40000000);
define ('PSCWS4_ZRULE_NONE',	0x00);
define ('PSCWS4_ZRULE_PREFIX',	0x01);
define ('PSCWS4_ZRULE_SUFFIX',	0x02);
define ('PSCWS4_ZRULE_INCLUDE',	0x04);	
define ('PSCWS4_ZRULE_EXCLUDE',	0x08);	
define ('PSCWS4_ZRULE_RANGE',	0x10);	


define ('PSCWS4_IGN_SYMBOL',	0x01);
define ('PSCWS4_DEBUG',			0x02);
define ('PSCWS4_DUALITY',		0x04);


define ('PSCWS4_MULTI_NONE',    0x0000);		
define ('PSCWS4_MULTI_SHORT',	0x1000);		
define ('PSCWS4_MULTI_DUALITY',	0x2000);		
define ('PSCWS4_MULTI_ZMAIN',   0x4000);		
define ('PSCWS4_MULTI_ZALL',	0x8000);		
define ('PSCWS4_MULTI_MASK',	0xf000);		
define ('PSCWS4_ZIS_USED',		0x8000000);


define ('PSCWS4_PFLAG_WITH_MB',	0x01);
define ('PSCWS4_PFLAG_ALNUM',	0x02);
define ('PSCWS4_PFLAG_VALID',	0x04);
define ('PSCWS4_PFLAG_DIGIT',	0x08);
define ('PSCWS4_PFLAG_ADDSYM',	0x10);


define ('PSCWS4_WORD_FULL',		0x01);	
define ('PSCWS4_WORD_PART',		0x02);	
define ('PSCWS4_WORD_USED',		0x04);	
define ('PSCWS4_WORD_RULE',		0x08);	

define ('PSCWS4_ZFLAG_PUT',		0x02);	
define ('PSCWS4_ZFLAG_N2',		0x04);	
define ('PSCWS4_ZFLAG_NR2',		0x08);	
define ('PSCWS4_ZFLAG_WHEAD',	0x10);	
define ('PSCWS4_ZFLAG_WPART',	0x20);	
define ('PSCWS4_ZFLAG_ENGLISH',	0x40);	
define ('PSCWS4_ZFLAG_SYMBOL',	0x80);	

define ('PSCWS4_MAX_EWLEN',		16);
define ('PSCWS4_MAX_ZLEN',		128);


class PSCWS4
{	
	var $v2305;		
	var $v2306;		
	var $v2307;		
	var $v2308 = '';	
	var $v2309;		
	var $v2310 = 0;	
	var $v2311;		
	var $v2312;
	var $v2313;		
	var $v2314 = 0;
	var $v2315 = 0;
	var $v2316 = 0;
	var $v2317;
	var $v2318;

	
	function PSCWS4($v611 = 'utf8')
	{
		$this->v2305 = false;
		$this->v2306 = $this->v2307 = array();
		$this->set_charset($v611);
	}

	
	function __construct() { $this->PSCWS4(); }
	function __destruct() { $this->close(); }

	
	function set_charset($v611 = 'utf8')
	{
		$v611 = strtolower(trim($v611));
		if ($v611 !== $this->v2308)
		{
			$this->v2308 = $v611;
			
			
			$this->v2309 = array_fill(0, 0x81, 1);
			if ($v611 == 'utf-8' || $v611 == 'utf8')
			{
				
				$this->v2309 = array_pad($this->v2309, 0xc0, 1);
				$this->v2309 = array_pad($this->v2309, 0xe0, 2);
				$this->v2309 = array_pad($this->v2309, 0xf0, 3);
				$this->v2309 = array_pad($this->v2309, 0xf8, 4);
				$this->v2309 = array_pad($this->v2309, 0xfc, 5);
				$this->v2309 = array_pad($this->v2309, 0xfe, 6);
				$this->v2309[] = 1;
			}
			else
			{
				
				$this->v2309 = array_pad($this->v2309, 0xff, 2);
			}
			$this->v2309[] = 1;
		}		
	}

	
	function set_dict($v2319)
	{
		$v2320 = new XDB_R;
		if (!$v2320->Open($v2319)) return false;
		$this->v2305 = $v2320;
	}

	
	function set_rule($v2319)
	{
		$this->_rule_load($v2319);
	}

	
	function set_ignore($v2321)
	{
		if ($v2321 == true) $this->v2310 |= PSCWS4_IGN_SYMBOL;
		else $this->v2310 &= ~PSCWS4_IGN_SYMBOL;
	}

	
	function set_multi($v439)
	{	
		$v439 = (intval($v439) << 12);

		$this->v2310 &= ~PSCWS4_MULTI_MASK;
		if ($v439 & PSCWS4_MULTI_MASK) $this->v2310 |= $v439;
	}

	
	function set_debug($v2321)
	{
		if ($v2321 == true) $this->v2310 |= PSCWS4_DEBUG;
		else $this->v2310 &= ~PSCWS4_DEBUG;
	}

	
	function set_duality($v2321)
	{
		if ($v2321 == true) $this->v2310 |= PSCWS4_DUALITY;
		else $this->v2310 &= ~PSCWS4_DUALITY;
	}

	
	function send_text($v501)
	{
		$this->v2311 = (string) $v501;
		$this->v2315 = strlen($this->v2311);
		$this->v2314 = 0;
	}

	
	function get_result()
	{
		$v2322 = $this->v2314;
		$v609 = $this->v2315;
		$v2323 = $this->v2311;
		$this->v2312 = array();

		while (($v2322 < $v609) && (ord($v2323[$v2322]) <= 0x20))
		{
			if ($v2323[$v2322] == "\r" || $v2323[$v2322] == "\n")
			{
				$this->v2314 = $v2322 + 1;
				$this->_put_res($v2322, 0, 1, 'un');
				return $this->v2312;
			}
			$v2322++;
		}
		if ($v2322 >= $v609) return false;
		
		
		$this->v2314 = $v2322;
		$v1567 = $v2323[$v2322];
		$v2324 = ord($v1567);
		if ($this->_char_token($v1567))
		{
			$this->v2314++;
			$this->_put_res($v2322, 0, 1, 'un');
			return $this->v2312;
		}

		$v2325 = $this->v2309[$v2324];
		$v2326 = 1;
		$v2327 = ($v2325 > 1 ? PSCWS4_PFLAG_WITH_MB : ($this->_is_alnum($v2324) ? PSCWS4_PFLAG_ALNUM : 0));
		while (($v2322 = ($v2322 + $v2325)) < $v609)
		{
			$v1567 = $v2323[$v2322];
			$v2324 = ord($v1567);
			if ($v2324 <= 0x20 || $this->_char_token($v1567)) break;
			$v2325 = $this->v2309[$v2324];
			if (!($v2327 & PSCWS4_PFLAG_WITH_MB))
			{
				
				if ($v2325 == 1)
				{
					if (($v2327 & PSCWS4_PFLAG_ALNUM) && !$this->_is_alnum($v2324))
						$v2327 ^= PSCWS4_PFLAG_ALNUM;
				}
				else
				{
					if (!($v2327 & PSCWS4_PFLAG_ALNUM) || $v2326 > 2) break;
					$v2327 |= PSCWS4_PFLAG_WITH_MB;
				}
			}
			else if (($v2327 & PSCWS4_PFLAG_WITH_MB) && $v2325 == 1)
			{
				
				if (!$this->_is_alnum($v2324)) break;

				$v2327 &= ~PSCWS4_PFLAG_VALID;
				for ($v255 = $v2322+1; $v255 < ($v2322+3); $v255++)
				{
					$v1567 = $v2323[$v255];
					$v2324 = ord($v1567);
					if (($v255 >= $v609) || ($v2324 <= 0x20) || ($this->v2309[$v2324] > 1))
					{
						$v2327 |= PSCWS4_PFLAG_VALID;
						break;
					}
					if (!$this->_is_alnum($v2324)) break;
				}

				if (!($v2327 & PSCWS4_PFLAG_VALID)) break;
				$v2325 += ($v255 - $v2322 - 1);
			}
			
			if (++$v2326 >= PSCWS4_MAX_ZLEN) break;
		}
		
		
		if (($v1567 = $v2322) > $v609)	
			$v2322 -= $v2325;
		
		
		if ($v2322 <= $this->v2314) return false;
		else if ($v2327 & PSCWS4_PFLAG_WITH_MB) $this->_msegment($v2322, $v2326);
		else if (!($v2327 & PSCWS4_PFLAG_ALNUM) || (($v2322 - $this->v2314) >= PSCWS4_MAX_EWLEN)) $this->_ssegment($v2322);
		else
		{
			$v2326 = $v2322 - $this->v2314;
			$this->_put_res($this->v2314, 2.5*log($v2326), $v2326, 'en');
		}
		
		
		$this->v2314 = ($v1567 > $v609 ? $v609 : $v2322);
		if (count($this->v2312) == 0)
			return $this->get_result();

		return $this->v2312;
	}

	
	function get_tops($v97 = 10, $v2328 = '')
	{
		$v1191 = array();
		if (!$this->v2311) return false;

		$v2329 = false;
		$v660 = array();
		if ($v2328 != '')
		{
			if (substr($v2328, 0, 1) == '~')
			{
				$v2328 = substr($v2328, 1);
				$v2329 = true;
			}
			foreach (explode(',', $v2328) as $v248)
			{
				$v248 = strtolower(trim($v248));
				if (!empty($v248)) $v660[$v248] = true;
			}
		}

		
		$v2322 = $this->v2314;
		$this->v2314 = $v928 = 0;
		$v22 = array();

		while ($v2330 = $this->get_result())
		{
			foreach ($v2330 as $v248)
			{
				if ($v248['idf'] < 0.2 || substr($v248['attr'], 0, 1) == '#') continue;

				
				if (count($v660) > 0)
				{
					if ($v2329 == true && !isset($v660[$v248['attr']])) continue;
					if ($v2329 == false && isset($v660[$v248['attr']])) continue;
				}

				
				$v420 = strtolower($v248['word']);
				if ($this->_rule_checkbit($v420, PSCWS4_RULE_NOSTATS)) continue;

				
				if (isset($v22[$v420]))
				{
					$v22[$v420]['weight'] += $v248['idf'];
					$v22[$v420]['times']++;
				}
				else
				{
					$v22[$v420] = array('word'=>$v248['word'], 'times'=>1, 'weight'=>$v248['idf'], 'attr'=>$v248['attr']);
				}
			}
		}
		
		
		$this->v2314 = $v2322;

		
		$v477 = create_function('$a,$b', 'return ($b[\'weight\'] > $a[\'weight\'] ? 1 : -1);');
		usort($v22, $v477);
		if (count($v22) > $v97) $v22 = array_slice($v22, 0, $v97);

		return $v22;
	}

	
	function close()
	{
		
		if ($this->v2305)
		{
			$this->v2305->Close();
			$this->v2305 = false;
		}

		
		$this->v2307 = array();
		$this->v2306 = array();
	}

	
	function version()
	{
		return sprintf('PSCWS/4.0 - by hightman');
	}

	
	
	
	function _rule_load($v2319)
	{
		if (!($v2331 = fopen($v2319, 'r'))) return false;
		$this->v2306 = array();
		
		
		$v255 = $v1341 = 0;
		while ($v2332 = fgets($v2331, 512))
		{
			if (substr($v2332, 0, 1) != '[' || !($v770 = strpos($v2332, ']')))
				continue;
			if ($v770 == 1 || $v770 > 15) continue;

			$v8 = strtolower(substr($v2332, 1, $v770 - 1));
			if (isset($this->v2306[$v8])) continue;
			$v61 = array('tf'=>5.0, 'idf'=>3.5, 'attr'=>'un', 'bit'=>0, 'flag'=>0, 'zmin'=>0, 'zmax'=>0, 'inc'=>0, 'exc'=>0);
			if ($v8 == 'special') $v61['bit'] = PSCWS4_RULE_SPECIAL;
			else if ($v8 == 'nostats') $v61['bit'] = PSCWS4_RULE_NOSTATS;
			else 
			{
				$v61['bit'] = (1<<$v1341);
				$v1341++;
			}
			$this->v2306[$v8] = $v61;
			if (++$v255 >= PSCWS4_RULE_MAX)
				break;
		}

		
		rewind($v2331);
		$v2333 = false;
		unset($v61);
		while ($v2332 = fgets($v2331, 512))
		{
			$v1567 = substr($v2332, 0, 1);
			if ($v1567 == ';') continue;
			if ($v1567 == '[')
			{
				unset($v61);
				if (($v770 = strpos($v2332, ']')) > 1)
				{
					$v8 = strtolower(substr($v2332, 1, $v770 - 1));
					if (isset($this->v2306[$v8]))
					{
						$v2333 = true;	
						$v61 = &$this->v2306[$v8];
					}
				}
				continue;
			}

			
			if ($v1567 == ':')
			{
				$v2332 = substr($v2332, 1);
				if (!($v770 = strpos($v2332, '='))) continue;
				list($v2174, $v2334) = explode('=', $v2332, 2);
				$v2174 = trim($v2174);
				$v2334 = trim($v2334);
				if ($v2174 == 'line') $v2333 = (strtolower(substr($v2334, 0, 1)) == 'n' ? false : true);
				else if ($v2174 == 'tf') $v61['tf'] = floatval($v2334);
				else if ($v2174 == 'idf') $v61['idf'] = floatval($v2334);
				else if ($v2174 == 'attr') $v61['attr'] = $v2334;	
				else if ($v2174 == 'znum') 
				{
					if ($v770 = strpos($v2334, ','))
					{
						$v61['zmax'] = intval(trim(substr($v2334, $v770+1)));
						$v61['flag'] |= PSCWS4_ZRULE_RANGE;
						$v2334 = substr($v2334, 0, $v770);
					}
					$v61['zmin'] = intval($v2334);
				}
				else if ($v2174 == 'type')
				{
					if ($v2334 == 'prefix') $v61['flag'] |= PSCWS4_ZRULE_PREFIX;
					if ($v2334 == 'suffix') $v61['flag'] |= PSCWS4_ZRULE_SUFFIX;
				}
				else if ($v2174 == 'include' || $v2174 == 'exclude')
				{
					$v2335 = 0;
					foreach (explode(',', $v2334) as $v248)
					{
						$v248 = strtolower(trim($v248));
						if (!isset($this->v2306[$v248])) continue;
						$v2335 |= $this->v2306[$v248]['bit'];
					}
					if ($v2174 == 'include') 
					{
						$v61['inc'] |= $v2335;
						$v61['flag'] |= PSCWS4_ZRULE_INCLUDE;
					}
					else
					{
						$v61['exc'] |= $v2335; 
						$v61['flag'] |= PSCWS4_ZRULE_EXCLUDE;
					}
				}
				continue;
			}
			
			
			if (!isset($v61)) continue;
			$v2332 = trim($v2332);
			if (empty($v2332)) continue;

			
			if ($v2333) $this->v2307[$v2332] = &$v61;
			else
			{
				$v609 = strlen($v2332);
				for ($v2322 = 0; $v2322 < $v609; )
				{
					$v2336 = ord(substr($v2332, $v2322, 1));
					$v2326 = $this->v2309[$v2336];
					if ($v2322 + $v2326 >= $v609) break;
					$v2337 = substr($v2332, $v2322, $v2326);
					$this->v2307[$v2337] = &$v61;
					$v2322 += $v2326;
				}
			}
		}
	}

	
	function _rule_get($v10)
	{
		if (!isset($this->v2307[$v10])) return false;
		return $this->v2307[$v10];
	}

	
	function _rule_checkbit($v10, $v2338)
	{
		if (!isset($this->v2307[$v10])) return false;
		$v2339 = $this->v2307[$v10]['bit'];
		return ($v2338 & $v2339 ? true : false);
	}

	
	function _rule_check($v683, $v10)
	{
		if (($v683['flag'] & PSCWS4_ZRULE_INCLUDE) && !$this->_rule_checkbit($v10, $v683['bit']))
			return false;
		if (($v683['flag'] & PSCWS4_ZRULE_EXCLUDE) && $this->_rule_checkbit($v10, $v683['bit']))
			return false;
		return true;
	}

	
	function _put_res($v794, $v255, $v574, $v173)
	{
		$v420 = substr($this->v2311, $v794, $v574);
		$v61 = array('word'=>$v420, 'off'=>$v794, 'idf'=>$v255, 'len'=>$v574, 'attr'=>$v173);
		$this->v2312[] = $v61;		
	}

	
	function _is_alnum($v370)
	{
		return (($v370>=48&&$v370<=57)||($v370>=65&&$v370<=90)||($v370>=97&&$v370<=122));
	}

	function _is_alpha($v370)
	{
		return (($v370>=65&&$v370<=90)||($v370>=97&&$v370<=122));
	}

	function _is_ualpha($v370)
	{
		return ($v370>=65&&$v370<=90);
	}

	function _is_digit($v370)
	{
		return ($v370>=48&&$v370<=57);
	}

	function _no_rule1($v1490)
	{
		return (($v1490 & (PSCWS4_ZFLAG_SYMBOL|PSCWS4_ZFLAG_ENGLISH)) || (($v1490 & (PSCWS4_ZFLAG_WHEAD|PSCWS4_ZFLAG_NR2)) == PSCWS4_ZFLAG_WHEAD));
	}

	function _no_rule2($v1490)
	{
		
		return $this->_no_rule1($v1490);
	}

	function _char_token($v370)
	{
		return ($v370=='('||$v370==')'||$v370=='['||$v370==']'||$v370=='{'||$v370=='}'||$v370==':'||$v370=='"');
	}

	
	function _dict_query($v420)
	{
		if (!$this->v2305) return false;
		$v6 = $this->v2305->Get($v420);
		if (!$v6) return false;
		
		$v248 = unpack('ftf/fidf/Cflag/a3attr', $v6);
		return $v248;
	}

	
	function _ssegment($v872)
	{
		$v610 = $this->v2314;
		$v2340 = $v872 - $v610;
		
		
		if ($v2340 > 1)
		{
			$v2323 = strtoupper(substr($this->v2311, $v610, $v2340));
			if ($this->_rule_checkbit($v2323, PSCWS4_RULE_SPECIAL))
			{
				$this->_put_res($v610, 9.5, $v2340, 'nz');
				return;
			}
		}
		
		$v2323 = $this->v2311;	
		
		
		if ($this->_is_ualpha(ord($v2323[$v610])) && $v2323[$v610+1] == '.')
		{
			for ($v1567 = $v610 + 2; $v1567 < $v872; $v1567++)
			{
				if (!$this->_is_ualpha(ord($v2323[$v1567]))) break;
				$v1567++;
				if ($v1567 == $v872 || $v2323[$v1567] != '.') break;
			}
			if ($v1567 == $v872)
			{
				$this->_put_res($v610, 7.5, $v2340, 'nz');
				return;
			}
		}
		
		
		while ($v610 < $v872)
		{
			$v1567 = $v2323[$v610++];
			$v2324 = ord($v1567);
			if ($this->_is_alnum($v2324))
			{
				$v2327 = $this->_is_digit($v2324) ? PSCWS4_PFLAG_DIGIT : 0;
				$v2340 = 1;
				while ($v610 < $v872)
				{
					$v1567 = $v2323[$v610];
					$v2324 = ord($v1567);
					if ($v2327 & PSCWS4_PFLAG_DIGIT)
					{
						if (!$this->_is_digit($v2324))
						{
							if (($v2327 & PSCWS4_PFLAG_ADDSYM) || $v2324 != 0x2e || !$this->_is_digit(ord($v2323[$v610+1])))
								break;
							$v2327 |= PSCWS4_PFLAG_ADDSYM;
						}
					}
					else
					{
						if (!$this->_is_alpha($v2324))
						{
							if (($v2327 & PSCWS4_PFLAG_ADDSYM) || $v2324 != 0x27 || !$this->_is_alpha(ord($v2323[$v610+1])))
								break;
							$v2327 |= PSCWS4_PFLAG_ADDSYM;
						}
					}
					$v610++;					
					if (++$v2340 >= PSCWS4_MAX_EWLEN) break;
				}
				$this->_put_res($v610 - $v2340, 2.5*log($v2340), $v2340, 'en');
			}
			else if (!($this->v2310 & PSCWS4_IGN_SYMBOL))
			{
				$this->_put_res($v610-1, 0, 1, 'un');
			}
		}
	}

	
	function _get_zs($v255, $v1341 = -1)
	{
		if ($v1341 == -1) $v1341 = $v255;
		return substr($this->v2311, $this->v2318[$v255]['start'], $this->v2318[$v1341]['end'] - $this->v2318[$v255]['start']);
	}

	
	function _mget_word($v255, $v1341)
	{
		$v2341 = $this->v2317;

		if (!($v2341[$v255][$v255]['flag'] & PSCWS4_ZFLAG_WHEAD)) return $v255;		
		for ($v534 = $v255, $v125 = $v255+1; $v125 <= $v1341; $v125++)
		{
			if ($v2341[$v255][$v125] && ($v2341[$v255][$v125]['flag'] & PSCWS4_WORD_FULL)) $v534 = $v125;			
		}
		return $v534;
	}

	
	function _mset_word($v255, $v1341)
	{
		$v2341 = $this->v2317;
		$v2342 = $this->v2318;
		$v61 = $v2341[$v255][$v1341];
		
		
		if (($v61 == false) || (($this->v2310 & PSCWS4_IGN_SYMBOL) 
			&& !($v61['flag'] & PSCWS4_ZFLAG_ENGLISH) && $v61['attr'] == 'un'))
		{
			return;
		}
		
		
		if ($this->v2310 & PSCWS4_DUALITY)
		{
			$v125 = $this->v2313;
			if ($v255 == $v1341 && !($v61['flag'] & PSCWS4_ZFLAG_ENGLISH) && $v61['attr'] == 'un')
			{
				$this->v2313 = $v255;
				if ($v125 < 0) return;
				
				$v255 = ($v125 & ~PSCWS4_ZIS_USED);
				if (($v255 != ($v1341-1)) || (!($v125 & PSCWS4_ZIS_USED) && $this->v2316 == $v255))
				{
					$this->_put_res($v2342[$v255]['start'], $v2341[$v255][$v255]['idf'], $v2342[$v255]['end'] - $v2342[$v255]['start'], $v2341[$v255][$v255]['attr']);
					if ($v255 != ($v1341-1)) return;
				}
				$this->v2313 |= PSCWS4_ZIS_USED;
			}
			else
			{
				if (($v125 >= 0) && (!($v125 & PSCWS4_ZIS_USED) || ($v1341 > $v255)))
				{
					$v125 &= ~PSCWS4_ZIS_USED;
					$this->_put_res($v2342[$v125]['start'], $v2341[$v125][$v125]['idf'], $v2342[$v125]['end'] - $v2342[$v125]['start'], $v2341[$v125][$v125]['attr']);
				}
				if ($v1341 > $v255) $this->v2316 = $v1341 + 1;
				$this->v2313 = -1;
			}
		}

		
		$this->_put_res($v2342[$v255]['start'], $v61['idf'], $v2342[$v1341]['end'] - $v2342[$v255]['start'], $v61['attr']);
		
		
		
		if (($v1341-$v255) > 1)
		{
			$v1571 = $v255;
			if ($this->v2310 & PSCWS4_MULTI_SHORT)
			{
				while ($v1571 < $v1341)
				{
					$v125 = $v1571;
					for ($v1361 = $v1571 + 1; $v1361 <= $v1341; $v1361++)
					{
						if ($v1361 == $v1341 && $v1571 == $v255) break;
						$v61 = $v2341[$v1571][$v1361];
						if ($v61 && ($v61['flag'] & PSCWS4_WORD_FULL))
						{
							$v125 = $v1361;
							$this->_put_res($v2342[$v1571]['start'], $v61['idf'], $v2342[$v1361]['end'] - $v2342[$v1571]['start'], $v61['attr']);
							if (!($v61['flag'] & PSCWS4_WORD_PART)) break; 
						}
					}
					if ($v125 == $v1571)
					{
						if ($v1571 == $v255) break;									
						$v61 = $v2341[$v1571][$v1571];
						$this->_put_res($v2342[$v1571]['start'], $v61['idf'], $v2342[$v1571]['end'] - $v2342[$v1571]['start'], $v61['attr']);
					}
					if (($v1571 = ($v125+1)) == $v1341)
					{
						$v1571--;
						break;
					}
				}
			}
			if ($this->v2310 & PSCWS4_MULTI_DUALITY)
			{
				while ($v1571 < $v1341)
				{
					$this->_put_res($v2342[$v1571]['start'], $v2341[$v1571][$v1571]['idf'], $v2342[$v1571+1]['end'] - $v2342[$v1571]['start'], $v2341[$v1571][$v1571]['attr']);
					$v1571++;
				}
			}
		}
		
		
		if (($v1341 > $v255) && ($this->v2310 & (PSCWS4_MULTI_ZMAIN|PSCWS4_MULTI_ZALL)))
		{
			if (($v1341 - $v255) == 1 && !$v2341[$v255][$v1341])
			{
				if ($v2341[$v255][$v255]['flag'] & PSCWS4_ZFLAG_PUT) $v255++;
				else $v2341[$v255][$v255]['flag'] |= PSCWS4_ZFLAG_PUT;
				$v2341[$v1341][$v1341]['flag'] |= PSCWS4_ZFLAG_PUT;
			}
			do
			{
				if ($v2341[$v255][$v255]['flag'] & PSCWS4_ZFLAG_PUT) continue;
				if (!($this->v2310 & PSCWS4_MULTI_ZALL) && !strchr("jnv", substr($v2341[$v255][$v255]['attr'],0,1))) continue;
				$this->_put_res($v2342[$v255]['start'], $v2341[$v255][$v255]['idf'], $v2342[$v255]['end'] - $v2342[$v255]['start'], $v2341[$v255][$v255]['attr']);
			}
			while (++$v255 <= $v1341);
		}
	}

	
	function _mseg_zone($v1490, $v690)
	{
		$v2216 = $v2343 = 0.0;
		$v2341 = &$this->v2317;
		$v2342 = $this->v2318;
		$v2344 = $v2345 = false;
		
		for ($v544 = $v255 = $v1490; $v255 <= $v690; $v255++)
		{
			$v1341 = $this->_mget_word($v255, $v690);
			if ($v1341 == $v255 || $v1341 <= $v544 || (($v2341[$v255][$v1341]['flag'] & PSCWS4_WORD_USED))) continue;
			
			
			if ($v255 == $v1490 && $v1341 == $v690)
			{
				$v2344 = array($v1341 - $v255, 0xff);
				break;
			}
			if ($v255 != $v1490 && ($v2341[$v255][$v1341]['flag'] & PSCWS4_WORD_RULE)) continue;
			
			
			$v2341[$v255][$v1341]['flag'] |= PSCWS4_WORD_USED;
			$v2343 = $v2341[$v255][$v1341]['tf'] * ($v1341 - $v255 + 1);
			if ($v255 == $v1490) $v2343 *= 1.2;
			else if ($v1341 == $v690) $v2343 *= 1.4;

			
			if ($v2345 == false)			
				$v2345 = array_fill(0, $v690-$v1490+2, 0xff);
			
			
			$v544 = 0;
			for ($v1571 = $v1490; $v1571 < $v255; $v1571 = $v1361+1)
			{
				$v1361 = $this->_mget_word($v1571, $v255-1);
				$v2343 *= $v2341[$v1571][$v1361]['tf'] * ($v1361-$v1571+1);
				$v2345[$v544++] = $v1361 - $v1571;
				if ($v1361 > $v1571) $v2341[$v1571][$v1361]['flag'] |= PSCWS4_WORD_USED;
			}
			
			
			$v2345[$v544++] = $v1341 - $v255;
			
			
			for ($v1571 = $v1341+1; $v1571 <= $v690; $v1571 = $v1361+1)
			{
				$v1361 = $this->_mget_word($v1571, $v690);
				$v2343 *= $v2341[$v1571][$v1361]['tf'] * ($v1361-$v1571+1);
				$v2345[$v544++] = $v1361 - $v1571;
				if ($v1361 > $v1571) $v2341[$v1571][$v1361]['flag'] |= PSCWS4_WORD_USED;			
			}
			
			$v2345[$v544] = 0xff;
			$v2343 /= pow($v544-1,4);
			
			
			if ($this->v2310 & PSCWS4_DEBUG)
			{
				printf("PATH by keyword = %s, (weight=%.4f):\n", $this->_get_zs($v255, $v1341), $v2343);
				for ($v544 = 0, $v1571 = $v1490; ($v1361 = $v2345[$v544]) != 0xff; $v544++)
				{
					$v1361 += $v1571;
					echo $this->_get_zs($v1571, $v1361) . " ";
					$v1571 = $v1361 + 1;
				}
				echo "\n--\n";
			}
			
			$v544 = $v1341;
			
			
			if ($v2343 > $v2216)
			{
				$v2216 = $v2343;
				$v2346 = $v2344;
				$v2344 = $v2345;
				$v2345 = $v2346;
				unset($v2346);
			}		
		}
		
		
		if ($v2344 == false) return;
		for ($v544 = 0, $v1571 = $v1490; ($v1361 = $v2344[$v544]) != 0xff; $v544++)
		{
			$v1361 += $v1571;
			$this->_mset_word($v1571, $v1361);
			$v1571 = $v1361 + 1;
		}
	}

	
	function _msegment($v872, $v2326)
	{
		$this->v2317 = array_fill(0, $v2326, array_fill(0, $v2326, false));
		$this->v2318 = array_fill(0, $v2326, false);
		$v2341 = &$this->v2317;
		$v2342 = &$this->v2318;
		$v2323 = $this->v2311;
		$v610 = $this->v2314;
		$this->v2313 = -1;

		
		for ($v255 = 0; $v610 < $v872; $v255++)
		{
			$v1567 = $v2323[$v610];
			$v2324 = ord($v1567);
			$v2325 = $this->v2309[$v2324];
			if ($v2325 == 1)
			{
				while ($v610++ < $v872)
				{
					$v2324 = ord($v2323[$v610]);
					if ($this->v2309[$v2324] > 1) break;
					$v2325++;				
				}
				$v2341[$v255][$v255] = array('tf'=>0.5, 'idf'=>0, 'flag'=>PSCWS4_ZFLAG_ENGLISH, 'attr'=>'un');
			}
			else
			{
				$v29 = $this->_dict_query(substr($v2323, $v610, $v2325));
				if (!$v29) $v2341[$v255][$v255] = array('tf'=>0.5, 'idf'=>0, 'flag'=>0, 'attr'=>'un');
				else 
				{					
					if (substr($v29['attr'],0,1) == '#') $v29['flag'] |= PSCWS4_ZFLAG_SYMBOL;
					$v2341[$v255][$v255] = $v29;
				}
				$v610 += $v2325;
			}
			
			$v2342[$v255] = array('start'=>$v610-$v2325, 'end'=>$v610);
		}	
		
		
		$v2326 = $v255;
		
		
		for ($v255 = 0; $v255 < $v2326; $v255++)
		{
			$v125 = 0;
			for ($v1341 = $v255+1; $v1341 < $v2326; $v1341++)
			{
				$v29 = $this->_dict_query($this->_get_zs($v255, $v1341));
				if (!$v29) break;
				
				$v1567 = $v29['flag'];
				if ($v1567 & PSCWS4_WORD_FULL)
				{
					$v2341[$v255][$v1341] = $v29;
					$v2341[$v255][$v255]['flag'] |= PSCWS4_ZFLAG_WHEAD;
					
					for ($v125 = $v255+1; $v125 <= $v1341; $v125++) $v2341[$v125][$v125]['flag'] |= PSCWS4_ZFLAG_WPART;
				}
				if (!($v1567 & PSCWS4_WORD_PART)) break;
			}

			if ($v125--)
			{
				
				if ($v125 == ($v255+1))
				{
					if ($v2341[$v255][$v125]['attr'] == 'nr') $v2341[$v255][$v255]['flag'] |= PSCWS4_ZFLAG_NR2;
					
				}
				
				
				if ($v125 < $v1341) $v2341[$v255][$v125]['flag'] ^= PSCWS4_WORD_PART;
			}
		}

		
		
		if (count($this->v2307) > 0)
		{
			
			for ($v255 = 0; $v255 < $v2326; $v255++)
			{
				if ($this->_no_rule1($v2341[$v255][$v255]['flag'])) continue;
				$v2347 = $this->_rule_get($this->_get_zs($v255));
				if (!$v2347) continue;
				$v2325 = ($v2347['zmin'] > 0 ? $v2347['zmin'] : 1);
				
				if (($v2347['flag'] & PSCWS4_ZRULE_PREFIX) && ($v255 < ($v2326 - $v2325)))
				{
					
					
					for ($v1567 = 1; $v1567 <= $v2325; $v1567++)
					{
						$v1341 = $v255 + $v1567;
						if ($v1341 >= $v2326 || $this->_no_rule2($v2341[$v1341][$v1341]['flag'])) break;
						if (!$this->_rule_check($v2347, $this->_get_zs($v1341))) break;
					}
					if ($v1567 <= $v2325) continue;
					
					
					$v1341 = $v255 + $v1567;
					while (true)
					{
						if ((!$v2347['zmax'] && $v2347['zmin']) || ($v2347['zmax'] && ($v2325 >= $v2347['zmax']))) break;
						if ($v1341 >= $v2326 || $this->_no_rule2($v2341[$v1341][$v1341]['flag'])) break;
						if (!$this->_rule_check($v2347, $this->_get_zs($v1341))) break;
						$v2325++;
						$v1341++;
					}
					
					
					if ($v2341[$v255][$v255]['flag'] & PSCWS4_ZFLAG_NR2)
					{
						if ($v2325 == 1) continue;
						$v2341[$v255][$v255+1]['flag'] |= PSCWS4_WORD_PART;
					}

					
					$v125 = $v255 + $v2325;
					$v2341[$v255][$v125] = array('tf'=>$v2347['tf'], 'idf'=>$v2347['idf'], 'flag'=>(PSCWS4_WORD_RULE|PSCWS4_WORD_FULL), 'attr'=>$v2347['attr']);
					$v2341[$v255][$v255]['flag'] |= PSCWS4_ZFLAG_WHEAD;
					for ($v1341 = $v255+1; $v1341 <= $v125; $v1341++) $v2341[$v1341][$v1341]['flag'] |= PSCWS4_ZFLAG_WPART;
					
					if (!($v2341[$v255][$v255]['flag'] & PSCWS4_ZFLAG_WPART)) $v255 = $v125;
					continue;
				}
				
				if (($v2347['flag'] & PSCWS4_ZRULE_SUFFIX) && ($v255 >= $v2325))
				{
					
					for ($v1567 = 1; $v1567 <= $v2325; $v1567++)
					{
						$v1341 = $v255 - $v1567;
						if ($v1341 < 0 || $this->_no_rule2($v2341[$v1341][$v1341]['flag'])) break;
						if (!$this->_rule_check($v2347, $this->_get_zs($v1341))) break;
					}
					if ($v1567 <= $v2325) continue;
					
					
					$v1341 = $v255 - $v1567;
					while (true)
					{
						if ((!$v2347['zmax'] && $v2347['zmin']) || ($v2347['zmax'] && ($v2325 >= $v2347['zmax']))) break;
						if ($v1341 < 0 || $this->_no_rule2($v2341[$v1341][$v1341]['flag'])) break;
						if (!$this->_rule_check($v2347, $this->_get_zs($v1341))) break;
						$v2325++;
						$v1341--;
					}
					
					
					$v125 = $v255 - $v2325;
					if ($v2341[$v125][$v255] != false) continue;
					$v2341[$v125][$v255] = array('tf'=>$v2347['tf'], 'idf'=>$v2347['idf'], 'flag'=>PSCWS4_WORD_FULL, 'attr'=>$v2347['attr']);
					$v2341[$v125][$v125]['flag'] |= PSCWS4_ZFLAG_WHEAD;
					for ($v1341 = $v125+1; $v1341 <= $v255; $v1341++)
					{
						$v2341[$v1341][$v1341]['flag'] |= PSCWS4_ZFLAG_WPART;
						if (($v1341 != $v255) && ($v2341[$v125][$v1341] != false)) $v2341[$v125][$v1341]['flag'] |= PSCWS4_WORD_PART;
					}
					continue;
				}
			}

			
			for ($v255 = $v2326 - 2; $v255 >= 0; $v255--)
			{
				
				if (($v2341[$v255][$v255+1] == false) || ($v2341[$v255][$v255+1]['flag'] & PSCWS4_WORD_PART)) continue;
				
				$v125 = $v255+1;
				$v2347 = $this->_rule_get($this->_get_zs($v255, $v125));
				if (!$v2347) continue;

				$v2325 = $v2347['zmin'] > 0 ? $v2347['zmin'] : 1;
				if (($v2347['flag'] & PSCWS4_ZRULE_PREFIX) && ($v125 < ($v2326 - $v2325)))
				{
					for ($v1567 = 1; $v1567 <= $v2325; $v1567++)
					{
						$v1341 = $v125 + $v1567;
						if ($v1341 >= $v2326 || $this->_no_rule2($v2341[$v1341][$v1341]['flag'])) break;
						if (!$this->_rule_check($v2347, $this->_get_zs($v1341))) break;
					}
					if ($v1567 <= $v2325) continue;
					
					
					$v1341 = $v125 + $v1567;
					while (true)
					{
						if ((!$v2347['zmax'] && $v2347['zmin']) || ($v2347['zmax'] && ($v2325 >= $v2347['zmax']))) break;
						if ($v1341 >= $v2326 || $this->_no_rule2($v2341[$v1341][$v1341]['flag'])) break;
						if (!$this->_rule_check($v2347, $this->_get_zs($v1341))) break;
						$v2325++;
						$v1341++;
					}
					
					
					$v125 = $v125 + $v2325;
					$v2341[$v255][$v125] = array('tf'=>$v2347['tf'], 'idf'=>$v2347['idf'], 'flag'=>PSCWS4_WORD_FULL, 'attr'=>$v2347['attr']);
					$v2341[$v255][$v255+1]['flag'] |= PSCWS4_WORD_PART;
					for ($v1341 = $v255+2; $v1341 <= $v125; $v1341++) $v2341[$v1341][$v1341]['flag'] |= PSCWS4_ZFLAG_WPART;
					$v255--;
					continue;
				}
				
				if (($v2347['flag'] & PSCWS4_ZRULE_SUFFIX) && ($v255 >= $v2325))
				{
					
					for ($v1567 = 1; $v1567 <= $v2325; $v1567++)
					{
						$v1341 = $v255 - $v1567;
						if ($v1341 < 0 || $this->_no_rule2($v2341[$v1341][$v1341]['flag'])) break;
						if (!$this->_rule_check($v2347, $this->_get_zs($v1341))) break;
					}
					if ($v1567 <= $v2325) continue;

					
					$v1341 = $v255 - $v1567;
					while (true)
					{
						if ((!$v2347['zmax'] && $v2347['zmin']) || ($v2347['zmax'] && ($v2325 >= $v2347['zmax']))) break;
						if ($v1341 < 0 || $this->_no_rule2($v2341[$v1341][$v1341]['flag'])) break;
						if (!$this->_rule_check($v2347, $this->_get_zs($v1341))) break;
						$v2325++;
						$v1341--;
					}

					
					$v125 = $v255 - $v2325;
					$v255 = $v255 + 1;
					$v2341[$v125][$v255] = array('tf'=>$v2347['tf'], 'idf'=>$v2347['idf'], 'flag'=>PSCWS4_WORD_FULL, 'attr'=>$v2347['attr']);
					$v2341[$v125][$v125]['flag'] |= PSCWS4_ZFLAG_WHEAD;
					for ($v1341 = $v125+1; $v1341 <= $v255; $v1341++)
					{
						$v2341[$v1341][$v1341]['flag'] |= PSCWS4_ZFLAG_WPART;
						if ($v2341[$v125][$v1341] != false) $v2341[$v125][$v1341]['flag'] |= PSCWS4_WORD_PART;
					}
					$v255 -= ($v2325+1);
					continue;
				}
			}
		}

		
		
		for ($v255 = 0, $v1341 = 0; $v255 < $v2326; $v255++)
		{
			if ($v2341[$v255][$v255]['flag'] & PSCWS4_ZFLAG_WPART) continue;
			if ($v255 > $v1341) $this->_mseg_zone($v1341, $v255-1);

			$v1341 = $v255;
			if (!($v2341[$v255][$v255]['flag'] & PSCWS4_ZFLAG_WHEAD))
			{
				$this->_mset_word($v255, $v255);
				$v1341++;
			}
		}
		
		
		if ($v255 > $v1341) $this->_mseg_zone($v1341, $v255-1);
		
		
		if (($this->v2310 & PSCWS4_DUALITY) && ($this->v2313 >= 0) && !($this->v2313 & PSCWS4_ZIS_USED))	
		{
			$v255 = $this->v2313;
			$this->_put_res($v2342[$v255]['start'], $v2341[$v255][$v255]['idf'], $v2342[$v255]['end'] - $v2342[$v255]['start'], $v2341[$v255][$v255]['attr']);	
		}
	}
}
?>
