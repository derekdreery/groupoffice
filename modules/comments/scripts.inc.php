<?php
$GO_SCRIPTS_JS .='GO.comments.enableReadMore="'.\GO\Comments\CommentsModule::loadReadMore().'";';

$GO_SCRIPTS_JS .='GO.comments.categoryRequired="'.\GO\Comments\CommentsModule::commentsRequired().'";';