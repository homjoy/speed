<?php 
namespace Atom\Scripts;
use Atom\Package\Department\DepartmentInfo;
header("Content-type:text/html;charset=utf-8");
error_reporting(E_ALL);
class RecruitStructPush extends \Frame\Script{

  //const URL_PUSH   =  'http://api.wintalent.cn:8080/wt/xwebservices/externalOrgPostService?wsdl';
   const URL_PUSH   =  'http://api.wintalent.cn/wt/xwebservices/externalOrgPostService?wsdl';
   const CROP_CODE  =  'meilishuo';
   const PASSWORD   =  'vMcJtALb4bKLHQMu';
   const USER_NAME  =  'meilishuo';
   const ROOT_ORG_ID    =  '1';
   private $limit_start =   0;
   private $limit_end   =   2000;
   private $depart_info = array();
   private $post_data   = array();
   private $str_xml  =  NULL;

   public  function run(){
      //获取数据detpid deptname  parent_deptid
      $this->depart_info = DepartmentInfo::getInstance()->getAll($this->limit_start,$this->limit_end);

      $result = array();
      foreach($this->depart_info as $key => $depart){
          //对父亲不在的孤点处理 遍历没有父亲的TODO暂不处理 
           if($depart['depart_name']=='CEO'){//除掉CEO
               continue;
           }
           if( $depart['parent_id']== '1'||$depart['parent_id']=='0'){//清除父亲为CEO的节点赋予0
              $depart['parent_id']= '1';
              $ret['parent_id'] = $depart['parent_id'];
              $ret['depart_id'] = $depart['depart_id'];
              $ret['depart_name'] = $depart['depart_name'];
              $result[] = $ret;
           }
 
       }
      $depart_result = $this->NodeGet($this->depart_info,$result);//儿子
      $depart_result_end = $this->NodeGet($this->depart_info,$depart_result);//孙子
      $this->depart_info = array_merge($result ,$depart_result,$depart_result_end); //合并
      $this->str_xml  = self::_getPustEmployeeXml($this->depart_info );
      $this->post_data = array(
        'in0' => self::CROP_CODE,
        'in1' => self::USER_NAME,
        'in2' => self::PASSWORD,
        'in3' => $this->str_xml,
        'in4' => self::ROOT_ORG_ID
     );

      $return = $this->synchUserInfo($this->post_data);
      if(!$return){
        return FALSE;
      }
      switch($return->out){
      case '00':
          $this->response->setBody("创建成功.\n"); 
          return  TRUE;
      break; 
      case '01':
           return  $this->response->setBody("重复操作.\n");
      break;  
      case '02':
           return  $this->response->setBody("无对应Id或相关信息.\n");
      break;   
      case '03':
           return  $this->response->setBody("传入的ID为空.\n");
      break;    
      case '04':
           return  $this->response->setBody("参数不对或必填出现空值.\n");
      break;  
      case '05':
           return  $this->response->setBody("招聘系统内部出现异常.\n");
      break;   
      case '06':
           return  $this->response->setBody("用户名或密码错误.\n");
      break;
      case '07':
           return $this->response->setBody("企业不存在或已失效.\n");
      break;
    }
    $this->response->setBody("创建失败.\n");
    return FALSE;
   } 

   public function NodeGet($params = array(),$result = array()){
      $depart_result = array();
      foreach($params as $key => $depart){
          foreach($result as $key_result => $varlue_result){
                 if($varlue_result['depart_id']==$depart['parent_id']){
                       $ret['parent_id'] = $depart['parent_id'];
                       $ret['depart_id'] = $depart['depart_id'];
                       $ret['depart_name'] = $depart['depart_name'];
                       $depart_result[]= $ret;
                 }
          }
      }
    return $depart_result;
   }
  /**
   * 同步公司信息
   */
  public  function synchUserInfo($parameters){

    $soap = new \SoapClient(self::URL_PUSH,array('encoding'=>'UTF-8'));
    $res = $soap->synchOrgs($parameters);
    return $res;   
  }

  /**
   * 推送信息，组织xml
   */
  private static  function _getPustEmployeeXml($depart_info = array()){
    $org_xml = <<<XMLDATA
      <?xml version="1.0" encoding="UTF-8"?>
      <OrgList>
XMLDATA;
    $org_xml.=  '<Org>';
    $org_xml.=  '<detpid>1</detpid>';
    $org_xml.=  '<deptname>美丽说</deptname>';
    $org_xml.=  '<parent_deptid></parent_deptid>';
    $org_xml.=  '</Org>';
    foreach($depart_info as $key => $depart){
      $org_xml.='<Org>';
      $org_xml.='<detpid>'.$depart['depart_id'].'</detpid>';
      $org_xml.='<deptname>'.$depart['depart_name'].'</deptname>';
      $org_xml.='<parent_deptid>'.$depart['parent_id'].'</parent_deptid>';
      $org_xml.='</Org>';
    }
    $org_xml .='</OrgList>';
   
    return $org_xml;

  }

}


?>