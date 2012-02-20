<?php

define('SHOW_GROUPS', 'showGroups');
define('CREATE_GROUP', 'createGroup');
define('SHOW_PARAMS', 'showParams');
define('UPDATE_PARAMS', 'updateParams');
define('DELETE_INSTANCE', 'deleteinstance');
define('UPDATE_INSTANCE', 'updateinstance');

extract($_GET);

require_once 'AWSSDKforPHP/sdk.class.php';

$rds = new AmazonRDS();
$rds->set_region(AmazonRDS::REGION_APAC_NE1);
$res = null;

switch($act){
  case SHOW_GROUPS:
  $res = $rds->describe_db_parameter_groups();
  break;
  case CREATE_GROUP:
  $res = $rds->create_db_parameter_group($group, $family, $desc);
  break;
  case SHOW_PARAMS:
  $res = $rds->describe_db_parameters($group);
  break;
  case UPDATE_PARAMS:
  $p = array();
  foreach($_GET as $key=>$value){
    if ($key != 'act' && $key != 'group') {
      $p[$key]=$value;
    }
  }
  $res = $rds->modify_db_parameter_group($group, array($p));
  break;
  case DELETE_INSTANCE:
  $res = $rds->delete_db_instance($name, array('SkipFinalSnapshot'=>true));
  break;
  case UPDATE_INSTANCE:
  $p = array();
  foreach($_GET as $key=>$value){
    if ($key != 'name') {
      $p[$key]=$value;
    }
  }
  $res = $rds->modify_db_instance($name, $p);
  break;
}

if ($res->isOK()){

  switch($act){
    case SHOW_GROUPS:
    echo '<h2>List of Parameter Groups</h2>';
    $gs = $res->body->DescribeDBParameterGroupsResult->DBParameterGroups->DBParameterGroup;
    echo '<ul>';
    foreach($gs as $g){ echo '<li>'.$g->DBParameterGroupName.'</li>'; }
    echo '</ul>';
    break;
    case CREATE_GROUP:
    echo '<h2>Created Parameter Group</h2>';
    var_dump( $res->body->CreateDBParameterGroupsResult);
    break;
    case SHOW_PARAMS:
    echo "<h2>$group details</h2>";
    $ps = $res->body->DescribeDBParametersResult->Parameters->Parameter;
    echo '<table border="1">';
    foreach($ps as $p){
      echo "<tr><td>$p->ParameterName</td><td>$p->ParameterValue</td><td>$p->ApplyType</td><td>$p->Description</td><td>$p->AllowedValues</td></tr>";
    }
    echo '</table>';
    break;
    case UPDATE_PARAMS:
    if ($res->body->ModifyDBParameterGroupResult->DBParameterGroupName == $group)
      echo "Parameter apply successfully<br/>";
      break;
      case DELETE_INSTANCE:
      if($res->body->DeleteDBInstanceResult->DBInstance->DBInstanceIdentifier == $name)
        echo $name." deleted<br/>";
        case UPDATE_INSTANCE:
        if($res->body->ModifyDBInstanceResult->DBInstance->DBInstanceIdentifier == $name)
          echo $name." updated<br/>";
          break;
  }

}else{
  echo $res->body->Error->Message.'<br/>';
}
?>
