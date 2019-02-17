<?php

namespace Drupal\hello\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\node\Entity\Node;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class HelloController extends ControllerBase {
	public function description($node1) {
    $apisitekey = \Drupal::config('system.site')->get('apisitekey');
    
    if(empty($apisitekey))    
      throw new AccessDeniedHttpException();
    else
    {
      $content = Node::load($node1);
      if(is_object($content) && $content->getType() == 'page'){
        $arr['title'] = $content->title->value;
        $arr['body'] = strip_tags($content->body->value); 
        echo json_encode($arr);
        exit;        
      } 
      throw new AccessDeniedHttpException();
       
    } 
	  
  }      	

}