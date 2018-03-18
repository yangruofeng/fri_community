<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2017/11/9
 * Time: 10:51
 */
class member_gradeModel extends tableModelBase
{

    public function __construct()
    {
        parent::__construct('member_grade');
    }

    public function insertGrade($param){
      $grade_code = $param['grade_code'];
      $min_score = $param['min_score'];
      $max_score = $param['max_score'];
      $grade_caption = $param['grade_caption'];
      $creator_id = $param['creator_id'];
      $creator_name = $param['creator_name'];
      $insert = $this->newRow();
      $insert->grade_code = $grade_code;
      $insert->min_score = $min_score;
      $insert->max_score = $max_score;
      $insert->grade_caption = $grade_caption;
      $insert->creator_id = $creator_id;
      $insert->creator_name = $creator_name;
      $insert->create_time = Now();
      $rt = $insert->insert();
      if ($rt->STS) {
        return new result(true, 'Add successful!');
      } else {
        return new result(false, 'Add failed--' . $rt->MSG);
      }
    }
}
