<?php

/*
 CommandListener interface in Swift Mailer.
 
 This program is free software: you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation, either version 3 of the License, or
 (at your option) any later version.
 
 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with this program.  If not, see <http://www.gnu.org/licenses/>.
 
 */

//@require 'Swift/Events/EventListener.php';
//@require 'Swift/Events/CommandEvent.php';

/**
 * Listens for Transports to send commands to the server.
 * @package Swift
 * @subpackage Events
 * @author Chris Corbyn
 */
interface Swift_Events_CommandListener extends Swift_Events_EventListener
{
  
  /**
   * Invoked immediately following a command being sent.
   * @param Swift_Events_ResponseEvent $evt
   */
  public function commandSent(Swift_Events_CommandEvent $evt);
  
}
