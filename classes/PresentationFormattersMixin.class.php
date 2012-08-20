<?

class PresentationFormattersMixin extends Mixin
{
  static function replace_in_file($path, $replace)
  {
    $s = file_get_contents($path);
    foreach($replace as $k=>$v)
    {
      $s = str_replace($k, $v, $s);
    }
    file_put_contents($path, $s);
    return $s;
    
  }
  
  
  static function scriptify($s, $quote="\"")
  {
  	$s = str_replace("\n", "\\n", $s);
  	$s = str_replace($quote, "\\$quote", $s);
  	return $quote . $s . $quote;
  }
  
  static function is_blank($v)
  {
    return !$v || trim($v)=='' || $v==0;
  }
  
  static function nonblank($v)
  {
    if (is_blank($v)) return '';
    return $v;
  }
  
  static function percent_format($n)
  {
  	return number_format(round($n*100,2),2) . "%";
  }
  
  static function splice_date($parts)
  {
  	return strtotime("${parts['month']}/${parts['day']}/${parts['year']}");
  }
  
  static function simple_format($s, $hyperlink=true)
  {
    $s = W::h($s);
    $s = preg_replace("/\n/", "<br/>", $s);
    $s = preg_replace("/\s\s/", " &nbsp;", $s);
    $s = preg_replace("/\t/", " &nbsp;", $s);
    if($hyperlink) $s = preg_replace('@(https?://([-\w\.:\@]+)+(:\d+)?(/([\w/_\.\)\(,\-]*(\?\S+)?)?)?)@', '<a href="$1" target="_blank">$1</a>', $s);
    return $s;
  }
  
  static function excerpt($s, $length=30)
  {
    $s = strip_tags($s);
    if(strlen($s)>$length) $s = substr($s, 0, $length)."...";
    return $s;
  }
  
  static function phone_format($phone)
  {
    $phone = preg_replace("/[^0-9]/", "", $phone);
    if(strlen($phone) == 7)
      return preg_replace("/([0-9]{3})([0-9]{4})/", "$1-$2", $phone);
    elseif(strlen($phone) == 10)
      return preg_replace("/([0-9]{3})([0-9]{3})([0-9]{4})/", "($1) $2-$3", $phone);
    else
      return $phone;
  }
  
  
  static function currency_format($f, $suppress_blanks = false)
  {
    if($suppress_blanks && is_blank($f)) return '';
    if ($f>=0)
    {
      return '$'.number_format($f,2);
    }
    return '($'.number_format(abs($f),2).')';
  }
  
  
  static function assemble_attrs($attrs, $args, $param_count=1)
  {
    for($i=$param_count;$i<count($args);$i+=2)
    {
      $k=$args[$i];
      $v=$args[$i+1];
      if ($k == 'class' && array_key_exists($k, $attrs))
      {
        $v = ' '.$v;
      } else {
        $attrs[$k]='';
      }
      $attrs[$k].=$v;
    }
    return $attrs;
  }
  
  static function splice_attrs($attrs, $args, $param_count=1)
  {
  	$attrs = self::assemble_attrs($attrs, $args, $param_count);
  	return self::to_xml_attributes($attrs);
  }
  
  static function to_xml_attributes($attrs)
  {
    $s = array();
    foreach($attrs as $k=>$v)
    {
      $v = W::h($v);
      $s[] = "$k = \"$v\"";
    }
    $s = join($s,' ');
    return $s;
  }
  
  static function extract_date($arr)
  {
    return strtotime($arr['month'] . '/' . $arr['day'] . '/' . $arr['year']);
  }
}