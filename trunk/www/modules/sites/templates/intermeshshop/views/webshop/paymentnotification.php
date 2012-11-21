<h1>Thank you!</h1>
<p><br /></p>								
<?php
echo $order->replaceTemplateTags($order->status->getLanguage($order->language_id)->screen_template);
?>