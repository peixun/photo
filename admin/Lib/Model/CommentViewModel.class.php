<?php
import('ViewModel');
class NeedCourseViewModel extends ViewModel
{
	 public $viewFields =  array(
        'Need'=>array('*'),
	
        'NeedCourse'=>array('status','_on'=>'NeedCourse.need_id=Need.id'),
        );
}
?>