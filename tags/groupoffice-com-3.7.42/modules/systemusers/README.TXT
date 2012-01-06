The module "systemusers" adds a system user when you add a new Group-Office user
and allows you to set an autoreply for local e-mail accounts. This module works
on Linux only and only works for local accounts. Therefore the autoreply will
only be working and enabled for accounts with the hostname "localhost" and
the directory /home/<username> must exist.

Install the module at:

Admin menu -> modules -> Install and after that double click the row and make
sure every system user has access to the module. Just adding the "Everyone"
group to should work in most cases.

You need to enter the following line with the "visudo" command:

<pre>
www-data ALL=NOPASSWD: /path/to/go/modules/systemusers/sudo.php
</pre>

Replace www-data with your apache run user.
