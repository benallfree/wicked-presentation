<?

if(isset($_POST['charset_check']))
{
  if($_POST['charset_check']!="ä™®")
  {
    W::error("Form not posted in UTF-8. Got back {$_POST['charset_check']} Set the page to UTF-8. See Presentation readme for more details.");
  }
}

W::add_mixin('PresentationBuildersMixin');
W::add_mixin('PresentationFormattersMixin');
W::add_mixin('PresentationFormMixin');
W::add_mixin('PresentationTagMixin');