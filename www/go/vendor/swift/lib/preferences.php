<?php
// Sets the default charset so that setCharset() is not needed elsewhere
Swift_Preferences::getInstance()->setCharset('utf-8');

Swift_Preferences::getInstance()
				->setTempDir(GO::config()->tmpdir)
				->setCacheType('disk');

Swift_Preferences::getInstance()->setQPDotEscape(GO::config()->swift_qp_dot_escape);
