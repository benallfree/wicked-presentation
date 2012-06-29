<?

if(isset($_POST['charset_check']))
{
  if($_POST['charset_check']!="ä™®")
  {
    dprint($_POST['charset_check']);
    wicked_error("Form not posted in UTF-8");
  }
}