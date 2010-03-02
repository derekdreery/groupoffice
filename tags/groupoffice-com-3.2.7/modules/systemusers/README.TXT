This module adds a system user when you add a new Group-Office user and allows
to set an autoreply when on vacation in the e-mail.
This module works on Linux only.

You need to enter the following line with the "visudo" command:

www-data ALL=NOPASSWD: /path/to/go/modules/systemusers/sudo.php

Replace www-data with your apache run user.