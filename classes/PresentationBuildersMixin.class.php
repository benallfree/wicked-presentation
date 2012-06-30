<?

class PresentationBuildersMixin extends Mixin
{
  
  static function link_to($text, $path=null, $qs = array())
  {
    if(is_array($text))
    {
      $path = call_user_func_array('W::url_sprintf', $text['href']);
      $text = $text['label'];
    }
    if(!$path) $path = $text;
    $qs = count($qs)>0 ? '?'.http_build_query($qs) : '';
    $attrs = array(
      'href'=>$path.$qs
    );
    $args = func_get_args();
    $s = W::splice_attrs($attrs, $args,3);
    $text = h($text);
  	return "<a $s >$text</a>";
  }
  
  static function button_to($text, $path)
  {
    $attrs = array(
      'class'=>'button',
      'onclick'=>"document.location='".j($path)."';",
    );
    $args = func_get_args();
    $s = W::splice_attrs($attrs, $args,2);
    $text = __($text);
    $html = "<div $s><a href='".h($path)."' onclick='return false;'>$text</a></div>";
    return $html;
  }
  
  
  
  static function mail_to($email, $text=null)
  {
  	$start=2;
  	if ($text==null)
  	{
  		$text=$email;
  		$start=1;
  	}
    $attrs = array( );
    $args = func_get_args();
    $args = array_shift($args);
    $s = W::splice_attrs($attrs, $args,$start);
    $email = h($email);
    return "<a href=\"mailto:$email\" $s>$text</a>";
  }
  
  
  static function stylesheet($name)
  {
    return "<link rel='stylesheet' href='".ROOT_VPATH."/css/$name.css'/>\n";
  }
  
  static function call_to($display, $number)
  {
    return link_to($display, "callto:+1".$number);
  }
  
  
}