<?

class PresentationFormMixin extends Mixin
{
  
  static $form_objs=array();
  static $form_multi=0;
  static $name_stack = array();
  
  
  static function form_for($obj, $action=null, $params="", $display_error_summary=true)
  {
    if (!$action) $action = W::request('path');
    self::$form_objs[] = $obj;
    $name_stack[] = singularize(tableize(get_class($obj)));
  
    $s = form_tag($action, $params);
    if ($display_error_summary)
    {
      $s = error_messages_for($obj) . $s;
    }
    return  $s;
  }
  
  
  static function end_form_for()
  {
    array_pop($name_stack);
    return end_form_tag();
  }
  
  
  
  static function form_tag($action='', $params=null)
  {
    $action = h($action);
    return <<<FORM
      <form method="post" enctype="multipart/form-data" action="$action" $params>
       <input type="hidden" name="charset_check" value="ä™®">
      <fieldset>
FORM;
  }
  
  static function end_form_tag()
  {
    return "</fieldset></form>";
  }
  
  
  
  
  static function error_messages_for($obj)
  {
  	if (!$obj->errors) return;
  	$s = "<div class='errors'>";
    foreach($obj->errors as $k=>$v)
    {
      $s.= "<div class='error'>";
  		$s.= humanize($k) . " $v";
  		$s.= "</div>";
    }
    $s.= "</div>";
  	return $s;
  }
  
  static function field_name($field=null)
  {
    if (count(self::$form_objs)==0) W::error("No form object found");
    $name = self::$name_stack[0];
    for($i=1;$i<count(self::$name_stack);$i++)
    {
    	$name.="[${name_stack[$i]}]";
    }
    $obj = self::$form_objs[count(self::$form_objs)-1];
    if (self::$form_multi>0)
    {
      if($obj->id())
      {
        $name .= "[{$obj->id()}]";
      } else {
        if($obj->is_new)
        {
          $name .= "[new]";
        }
      }
    }
    if ($field) $name.="[$field]";
    return $name;
  }
  
  
  static function fields_for_multi($obj,$namespace=null)
  {
    self::$form_multi++;
    self::$form_objs[] = $obj;
    if ($namespace===null) $namespace = pluralize($obj->tableized_klass);
    self::$name_stack[] = $namespace;
  }
  
  static function fields_for($obj_or_name, $data=null)
  {
    if(is_object($obj_or_name))
    {
      $data = $obj_or_name;
      $obj_or_name = get_class($obj_or_name);
    } else {
      $data = (object)$data;
    }
    self::$form_objs[] = $data;
    self::$name_stack[] = tableize($obj_or_name,false);
  }
  
  static function end_fields_for()
  {
    array_pop(self::$form_objs);
    array_pop(self::$name_stack);
  }
  
  static function end_fields_for_multi()
  {
    self::$form_multi--;
    array_pop(self::$form_objs);
    array_pop(self::$name_stack);
  }
  
  static function textarea_field()
  {
    $obj = self::$form_objs[count(self::$form_objs)-1];
    $field = func_get_arg(0);
    $value = $obj->$field;
    $attrs = array(
      'name'=>field_name($field)
    );
    if ($obj->errors && array_key_exists($field, $obj->errors) && $obj->errors[$field]!='') $attrs['class'] = 'error_field';
    $args = func_get_args();
    $s = splice_attrs($attrs, $args);
    $value = h($value);
    return "<textarea $s>$value</textarea>";
  }
  
  static function file_field()
  {
    $obj = self::$form_objs[count(self::$form_objs)-1];
    $field = func_get_arg(0);
    $attrs = array(
      'type'=>'file',
      'name'=>field_name($field)
    );
    if ($obj->errors && array_key_exists($field, $obj->errors) && $obj->errors[$field]!='') $attrs['class'] = 'error_field';
    $args = func_get_args();
    $s = splice_attrs($attrs, $args);
    return "<input $s />";
  }
  
  
  
  static function command_field($name)
  {
    return '<input type="hidden" name="cmd" value="'.h($name).'"/>';
  }
  
  static function hidden_field()
  {
    $obj = self::$form_objs[count(self::$form_objs)-1];
    $field = func_get_arg(0);
    $value = $obj->$field;
    $attrs = array(
      'type'=>'hidden',
      'name'=>field_name($field),
      'value'=>$value
    );
    $args = func_get_args();
    $s = splice_attrs($attrs, $args);
    return "<input $s />";
  }
  
  static function date_field() //Aaron - editing this because namespace_for is undefined ?!?
  //It becomes readily apparent that this static function does not work.  Is this a failing of select_tag???
  //It looks like select_tag needs a different test than is_numeric($k) for this to branch properly.
  {
    $obj = self::$form_objs[count(self::$form_objs)-1];
    $field = func_get_arg(0);
    if ($obj->$field==null) $obj->$field = time();
    $value = getdate($obj->$field);
  
  //  namespace_for($field.'_parts');
    
    $months=array('Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');
    $arr = array();
    for($i=1;$i<=12;$i++) $arr[] = (object) array('id'=>$i, 'name'=>$months[$i-1]);
  //  $s = select_tag(field_name('month'), $arr, $value['mon']);
    $s = select_tag($field."[month]", $arr, $value['mon']);
  
    $arr = array();
    for($i=1;$i<=31;$i++) $arr[] = (object) array('id'=>$i, 'name'=>$i);
  //  $s .= '/'. select_tag(field_name('day'), $arr, $value['mday']);
    $s .= '/'. select_tag($field."[day]", $arr, $value['mday']);
  
    $arr = array();
    for($i=$value['year']-10;$i<=date('Y')+10;$i++) $arr[] = (object) array('id'=>$i, 'name'=>$i);
  //  $s .= '/'.select_tag(field_name('year'), $arr, $value['year']);
    $s .= '/'.select_tag($field."[year]", $arr, $value['year']);
    
  //  end_namespace_for();
    
    return $s;
  }
  
  
  static function time_field() //Aaron - created
  {
    $obj = self::$form_objs[count(self::$form_objs)-1];
    $field = func_get_arg(0);
    if ($obj->$field==null) $obj->$field = time();
    $value = getdate($obj->$field);
    $ampm = 'AM';
    $hours = $value['hours'];
    if ($hours >= 12)
    {
  	$hours = $hours - 12;
  	$ampm = 'PM';
    }
    if ($value['hours'] == 0) $hours = 12;
  //  namespace_for($field.'_parts');
    
    $arr = array();
    for($i=1;$i<=12;$i++) $arr[] = (object) array('id'=>$i, 'name'=>$i);
  //  $s = select_tag(field_name('month'), $arr, $value['mon']);
    $s = select_tag($field."[hour]", $arr, $hours);
  
    $arr = array();
    for($i=0;$i<=5;$i+=5) $arr[] = (object) array('id'=>$i, 'name'=>'0'.$i);
    for($i=10;$i<=55;$i+=5) $arr[] = (object) array('id'=>$i, 'name'=>$i);
  //  $s .= '/'. select_tag(field_name('day'), $arr, $value['mday']);
    $s .= ':'. select_tag($field."[minute]", $arr, $value['minutes']);
  
    $arr = array((object) array('id'=>'AM', 'name'=>'AM'), (object) array('id'=>'PM', 'name'=>'PM'));
  //  $s .= '/'.select_tag(field_name('year'), $arr, $value['year']);
    $s .= ' '.select_tag($field."[ampm]", $arr, $ampm);
    
  //  end_namespace_for();
    
    return $s;
  }
  
  
  static function text_field()
  {
    $obj = self::$form_objs[count(self::$form_objs)-1];
    $field = func_get_arg(0);
    $value = isset($obj->$field) ? $obj->$field : '';
    $attrs = array(
      'type'=>'text',
      'name'=>field_name($field),
      'value'=>$value
    );
    if (isset($obj->errors) && isset($obj->errors[$field]) && $obj->errors[$field]!='') $attrs['class'] = 'error_field';
  
    $args = func_get_args();
    $s = splice_attrs($attrs, $args);
    return "<input $s />";
  }
  
  
  
  static function password_field()
  {
    $obj = self::$form_objs[count(self::$form_objs)-1];
    $field = func_get_arg(0);
    $value = $obj->$field;
    $attrs = array(
      'type'=>'password',
      'name'=>field_name($field),
      'value'=>$value
    );
    if ($obj->errors && array_key_exists($field, $obj->errors) && $obj->errors[$field]!='') $attrs['class'] = 'error_field';
    $args = func_get_args();
    $s = splice_attrs($attrs, $args);
    return "<input $s />";
  }
  
  static function select_field($field,  $options, $value_field='id', $display_field='name')
  {
    $obj = self::$form_objs[count(self::$form_objs)-1];
    $args = func_get_args();
    if(count($args)<2) W::error("Field name and options array are required.");
    if(count($args)<3) $args[] = 'id';
    if(count($args)<4) $args[] = 'name';
    if ($obj->errors && array_key_exists($field, $obj->errors) && $obj->errors[$field]!='')
    {
      $args[] = 'class';
      $args[] = 'error_field';
    }
  
    $args[0] = field_name($field);
    $args = array_merge(array_slice($args,0,2), array($obj->$field), array_slice($args,2));
    return call_user_func_array('select_tag', $args);
  }
  
  
  
  static function checkbox_field($field)
  {
    $obj = self::$form_objs[count(self::$form_objs)-1];
    $field = func_get_arg(0);
  
    $name = field_name($field);
    $attrs = array(
      'type'=>'checkbox',
      'name'=>$name,
      'value'=>1
    );
    if ($obj->errors && array_key_exists($field, $obj->errors) && $obj->errors[$field]!='') $attrs['class'] = 'error_field';
    $args = func_get_args();
    $attrs = assemble_attrs($attrs, $args);
    $is_checked = $obj->$field == $attrs['value'];
    $s = splice_attrs($attrs, $args);
    return "<input type='hidden' name='$name' value='0'/><input $s ".( $is_checked ? 'checked="checked"' : '')." />";
  }
  
  
  static function radio_field($field)
  {
    $obj = self::$form_objs[count(self::$form_objs)-1];
    $field = func_get_arg(0);
  
    $name = field_name($field);
    $attrs = array(
      'type'=>'radio',
      'name'=>$name,
      'value'=>func_get_arg(1)
    );
    if ($obj->errors && array_key_exists($field, $obj->errors) && $obj->errors[$field]!='') $attrs['class'] = 'error_field';
    $args = func_get_args();
    $attrs = assemble_attrs($attrs, $args, 2);
    $is_checked = $obj->$field == $attrs['value'];
    $s = splice_attrs($attrs, $args, 2);
    return "<input $s ".( $is_checked ? 'checked="checked"' : '')." />";
  }  

}