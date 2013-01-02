<?php
class GO_Sites_NotificationsObject {

	const NOTIFICATION_OK = 'notice-ok';
	const NOTIFICATION_INFO = 'notice-info';
	const NOTIFICATION_ERROR = 'notice-error';
	const NOTIFICATION_WARNING = 'notice-warning';
	
	private $_notifications=array();
	
	public function render($name=false,$remove=true){
		
		if(!$name){
			
			$notifications = $this->getAllNotifications($remove);
			
			if(empty($notifications))
				return false;
			
			$html = '';
			foreach($notifications as $notification){
				$html .= '<div class="notification '.$notification['type'].'">';
				$html .= $notification['notification'];
				$html .='</div>';
			}
			return $html;
			
		}else{
			$notification = $this->getNotification($name,$remove);
			if(!$notification){
				return false;
			}else{
				$html = '<div class="notification '.$notification['type'].'">';
				$html .= $notification['notification'];
				$html .='</div>';
				return $html;
			}		
		}
	}
	
	public function addNotification($name,$notification,$type=GO_Sites_NotificationsObject::NOTIFICATION_INFO){
		$this->_notifications[$name] = array('name'=>$name,'notification'=>$notification,'type'=>$type);
	}
	
	public function getAllNotifications($remove=true){
		if(empty($this->_notifications)){
			return false;
		}else{
			$notifications = $this->_notifications;
			
			if($remove){
				unset($this->_notifications);
				$this->_notifications = array();
			}
			
			return $notifications;
		}
	}
	
	public function getNotification($name,$remove=true){
		if(empty($this->_notifications[$name])){
			return false;
		}else{
			$return = $this->_notifications[$name];
			
			if($remove)
				unset($this->_notifications[$name]);
			
			return $return;
		}
	}
	
}