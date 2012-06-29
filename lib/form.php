<?

$form_objs=array();
$form_multi=0;


function form_for($obj, $action=null, $params="", $display_error_summary=true)
{
  global $__wicked;
  global $form_objs,$name_stack;
  if (!$action) $action = $__wicked['request']['request_path'];
  $form_objs[] = $obj;
  $name_stack[] = singularize(tableize(get_class($obj)));

  $s = form_tag($action, $params);
  if ($display_error_summary)
  {
    $s = error_messages_for($obj) . $s;
  }
  return  $s;
}


function end_form_for()
{
  global $form_objs,$name_stack;
  array_pop($name_stack);
  return end_form_tag();
}



function form_tag($action='', $params=null)
{
  $action = h($action);
  return <<<FORM
    <form method="post" enctype="multipart/form-data" action="$action" $params>
     <input type="hidden" name="charset_check" value="ä™®">
    <fieldset>
FORM;
}

function end_form_tag()
{
  return "</fieldset></form>";
}




function error_messages_for($obj)
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
