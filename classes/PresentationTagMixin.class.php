<?

class PresentationTagMixin extends Mixin
{
  static function script_tag($vpath)
  {
    return "<script src='$vpath' type='text/javascript'></script>";
  }
  
  static function image_tag($url)
  {
    $attrs = array(
      'src'=>$url,
      'alt'=>'image'
    );
    $args = func_get_args();
    $s = W::splice_attrs($attrs, $args);
    return "<img $s />";
  }
  
  
  static function submit_tag($text)
  {
    $attrs = array(
      'type'=>'submit',
      'name'=>'commit'
    );
    $args = func_get_args();
    $s = W::splice_attrs($attrs, $args);
    return "<button $s>".W::filter('text', $text)."</button>";
  }
  
  static function js_button_tag($text, $onclick)
  {
  	$attrs = array(
  		'type'=>'button',
  		'name'=>'link',
  		'value'=>$text,
  		'onclick'=>$onclick
  	);
  	$args = func_get_args();
  	// $args = array();
  	$s = W::splice_attrs($attrs, $args, 2);
  	return "<input $s/>";
  }
  
  static function image_button_tag($src)
  {
    $attrs = array(
      'type'=>'image',
      'name'=>'commit',
      'src'=>$src
    );
    $args = func_get_args();
    $s = W::splice_attrs($attrs, $args);
    return "<input $s/>";
  }
  
  
  static function select_tag($name, $options, $defaults=null, $value_field='id', $display_field='name')
  {
    $defaults = (!is_array($defaults)) ? (array($defaults)) : ($defaults);
    $default_values = array();
    foreach($defaults as $k=>$v) 
    {
      if(is_object($v))
      {
        $default_values[] = $v->$value_field;
      } else {
        $default_values[] = $v;
      }
    }
  	$s="";
  	$attrs=array();
  	$args = func_get_args();
  	$attrs = W::splice_attrs($attrs, $args, 5);
  	$s.= "<select name=\"$name\" $attrs>";
  	$s.= "<option value=''/>-- Choose --</option>";
  	foreach($options as $k=>$v)
  	{
  		if (is_numeric($k))
  		{
  		  if(!is_object($v))
  		  {
    		  if(is_array($v))
    		  {
    		    $v = (object)$v;
    		  } else {
    		    $v = (object)array(
    		      $value_field=>$v,
    		      $display_field=>$v,
    		    );
    		  }
    		}
  		  if(is_object($v->$value_field)) wax_error("select_tag() is expecting $value_field to be a string or int, but it's an object");
  		  if(!is_string($v->$display_field) && !is_numeric($v->$display_field)) wax_error("select_tag() is expecting $display_field to be a string, but it's not", array($options, $v, $v->$display_field));
        $value = $v->$value_field;
        $display = W::h($v->$display_field);
      } else {
        $value = $k;
        $display = W::h($v);
  	  }
  		$s.= self::option_tag($value, $display, $default_values); 
  	}
  	$s.="</select>";
  	return $s;
  }
  
  static function checklist_tag($name, $options, $defaults=null, $value_field='id', $display_field='name')
  {
    $defaults = (!is_array($defaults)) ? (array($defaults)) : ($defaults);
    $default_values = array();
    foreach($defaults as $k=>$v) 
    {
      if(is_object($v))
      {
        $default_values[] = $v->$value_field;
      } else {
        $default_values[] = $v;
      }
    }
  	$s="";
  	$attrs=array();
  	$args = func_get_args();
  	$attrs = W::splice_attrs($attrs, $args, 5);
  	foreach($options as $k=>$v)
  	{
  		if (is_numeric($k))
  		{
  		  if(is_object($v->$value_field)) wax_error("select_tag() is expecting $value_field to be a string or int, but it's an object");
  		  if(!is_string($v->$display_field) && !is_numeric($v->$display_field)) wax_error("select_tag() is expecting $display_field to be a string, but it's not", array($options, $v, $v->$display_field));
        $value = $v->$value_field;
        $display = W::h($v->$display_field);
      } else {
        $value = $k;
        $display = W::h($v);
  	  }
      $is_selected = array_search($value, $default_values)!==FALSE;
  
  		$s.= "<input name=\"$name\" type=\"checkbox\" value=\"".W::h($value)."\"" . (($is_selected) ? ' checked' : '') . " $attrs/>$display<br/>";
  	}
  	return $s;
  }
  
  static function option_tag($value, $display, $selected_values = array())
  {
    if(!is_array($selected_values)) $selected_values = array($selected_values);
    $is_selected = array_search($value, $selected_values)!==FALSE;
  
    return "<option value=\"".W::h($value)."\"" . (($is_selected) ? ' selected="selected"' : '') . ">$display</option>";
  }
  
  static function command_tag($name)
  {
    echo "<input type='hidden' name='cmd' value='".W::h($name)."'/>";
  }
  
  static function checkbox_tag($name, $value=1, $is_checked=false)
  {
    $attrs = array(
      'type'=>'checkbox',
      'name'=>$name,
      'value'=>$value
    );
    $args = func_get_args();
    $s = W::splice_attrs($attrs, $args,3);
  
    return "<input type='hidden' name='$name' value='0'/><input $s ".( $is_checked ? 'checked="checked"' : '')." />";
  }
  
  
  static function hidden_tag($name, $value=1)
  {
    $attrs = array(
      'type'=>'hidden',
      'name'=>$name,
      'value'=>$value
    );
    $args = func_get_args();
    $s = W::splice_attrs($attrs, $args,2);
    return "<input $s/>";
  }
  
  static function file_tag($name)
  {
    $attrs = array(
      'type'=>'file',
      'name'=>$name,
    );
    $args = func_get_args();
    $s = W::splice_attrs($attrs, $args,1);
    return "<input $s />";
  }
  
  static function text_tag($name, $value='')
  {
    $attrs = array(
      'type'=>'text',
      'name'=>$name,
      'value'=>$value,
    );
    $args = func_get_args();
    $s = W::splice_attrs($attrs, $args,2);
    return "<input $s />";
  }
  
  static function password_tag($name, $value='')
  {
    $attrs = array(
      'type'=>'password',
      'name'=>$name,
      'value'=>$value,
    );
    $args = func_get_args();
    $s = W::splice_attrs($attrs, $args,2);
    return "<input $s />";
  }
  
  
  static function textarea_tag($name, $value='')
  {
    $value = $value;
    $attrs = array(
      'name'=>$name
    );
    $args = func_get_args();
    $s = W::splice_attrs($attrs, $args,2);
    $value = W::h($value);
    return "<textarea $s>$value</textarea>";
  }
}