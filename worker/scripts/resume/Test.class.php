<?php
namespace Worker\Scripts\Resume;
use Worker\Package\Utils\RegexMatcher;

/**
 *
 * Class Test
 * @package Worker\Scripts\Resume
 *
 */
class Test extends \Worker\Scripts\Resume\SyncBase{


    public function run()
    {
       $html = <<<EOF
       项目经验</td></tr><tr><td align="middle" valign="middle" height="4"><img src="http://img01.51jobcdn.com/imehire/ehire2007/default/image/im2009/line1_1.gif" width="100%" height="4"></td></tr><tr><td height="10" align="left" valign="middle"></td></tr><tr><td align="left" valign="middle"><table width="100%" border="0" cellspacing="0" cellpadding="0" class="table_set"><tr><td colspan="2" class="text_left">2013      /7--2013      /9：快乐购B2B2C平台</td></tr><tr><td width="16%" class="text_left" valign="top">项目描述：</td><td width="84%" class="text">通过外包+自主开发的形式完成了快乐购B2B2C平台的搭建工作。历时两个半月。后期完成了对平台的四期改造。<br>本平台解决了快乐购电商网站由B2C到B2B2C的转型。目前吸纳商家50多家，商品近万件。<br>网站地址：http://mall.happigo.com</td></tr><tr><td class="text_left" valign="top">责任描述：</td><td class="text">负责B2B2C平台的外包协作和联合开发工作。带领平台开发项目组进行需求分析和项目实施。<br>负责平台的后期四次改造任务。</td></tr><tr><td colspan="2" align="center" valign="middle"><hr size="1" noshade></td></tr><tr><td colspan="2" class="text_left">2010      /11--至今：湖南卫视快乐购官方网站</td></tr><tr><td width="16%" class="text_left" valign="top">项目描述：</td><td width="84%" class="text">本网站是湖南卫视快乐购的唯一官方网站。网址 http://www.happigo.com</td></tr><tr><td class="text_left" valign="top">责任描述：</td><td class="text">1.负责公司技术开发管理工作。<br>2.负责解决技术难题，培养技术骨干。<br>3.负责网站平台的开发、维护，保证网站的正常运行。<br>4.负责公司网站商品售前售后的技术支持工作。<br>5.负责部门内部日常管理、岗位培训、团队建设等。</td></tr><tr><td colspan="2" align="center" valign="middle"><hr size="1" noshade></td></tr><tr><td colspan="2" class="text_left">2010      /10--至今：&quot;我是大美人&quot;网站建设与维护</td></tr><tr><td width="16%" class="text_left" valign="top">项目描述：</td><td width="84%" class="text">“我是大美人”网 是由湖南卫视打造的一档电视节目的官方网站。网站主要发布电视节目视频、活动。为客户提供专业的美容护肤等知识。</td></tr><tr><td class="text_left" valign="top">责任描述：</td><td class="text">主要负责网站的建设更新与日常维护，包括信息发布等。</td></tr><tr><td colspan="2" align="center" valign="middle"><hr size="1" noshade></td></tr><tr><td colspan="2" class="text_left">2010      /7--2010      /10：公司内部ERP管理系统</td></tr><tr><td width="16%" class="text_left" valign="top">项目描述：</td><td width="84%" class="text">为电子商务网站建立一整套网站商品管理系统，包括商品管理，仓储管理，物流管理，购物流程管理，订单处理管理，采购管理，财务结算管理等。</td></tr><tr><td class="text_left" valign="top">责任描述：</td><td class="text">主要负责该系统设计与技术开发。制定系统设计标准与设计思路。负责系统开发框架设计。负责系统主要模块开发。</td></tr><tr><td colspan="2" align="center" valign="middle"><hr size="1" noshade></td></tr><tr><td colspan="2" class="text_left">2010      /3--2010      /5：热狗商城</td></tr><tr><td width="16%" class="text_left" valign="top">项目描述：</td><td width="84%" class="text">基于ECSHOP的手机购物网站。<br>http://www.regou.com</td></tr><tr><td class="text_left" valign="top">责任描述：</td><td class="text">负责前台程序设计与开发：<br>购物流程程序编写。<br>网站界面维护与调整，包括网站更新替换。<br>负责产品信息管理。</td></tr><tr><td colspan="2" align="center" valign="middle"><hr size="1" noshade></td></tr><tr><td colspan="2" class="text_left">2009      /9--2009      /11：摩能手机网</td></tr><tr><td width="16%" class="text_left" valign="top">项目描述：</td><td width="84%" class="text">中国专业的国产手机专营网站<br>http://www.moneng.cn</td></tr><tr><td class="text_left" valign="top">责任描述：</td><td class="text">负责前台程序设计与开发：<br>购物流程程序编写。<br>网站界面维护与调整，包括网站更新替换。<br>负责产品信息管理。</td></tr><tr><td colspan="2" align="center" valign="middle"><hr size="1" noshade></td></tr><tr><td colspan="2" class="text_left">2008      /7--2009      /2：管理型企业通用管理系统</td></tr><tr><td width="16%" class="text_left" valign="top">项目描述：</td><td width="84%" class="text">本软件主要针对管理型企业办公而开发。主要管理企业的合同、财务、进销存、客户、日常办公等方面的适宜。<br>开发8个月</td></tr><tr><td class="text_left" valign="top">责任描述：</td><td class="text">主要负责项目的前期规划，资料收集，数据库设计，软件架构，主要代码编写，后期调试，与潜在客户沟通，修改方案 ，销售</td></tr><tr><td colspan="2" align="center" valign="middle"><hr size="1" noshade></td></tr><tr><td colspan="2" class="text_left">2008      /3--2008      /7：河北廊坊纤维检验局管理系统</td></tr><tr><td width="16%" class="text_left" valign="top">项目描述：</td><td width="84%" class="text">为纤检局开发一套管理检验报告的系统，包括报告的签字与打印。开发周期为4个月，现在运行状态良好。</td></tr><tr><td class="text_left" valign="top">责任描述：</td><td class="text">系统整体设计，数据库设计，需求分析，部分代码编写 后期跟踪维护</td></tr><tr><td colspan="2" align="center" valign="middle"><hr size="1" noshade></td></tr><tr><td colspan="2" class="text_left">2007      /10--2008      /4：山东烟台质量监督检验局内部质检流程管理系统</td></tr><tr><td width="16%" class="text_left" valign="top">项目描述：</td><td width="84%" class="text">为山东烟台质检局开发一套专门管理检验报告的管理系统，开发用时半年 开发人员为4人 最终达到预期效果。</td></tr><tr><td class="text_left" valign="top">责任描述：</td><td class="text">负责检验报告高级查询部分，共计10项查询，并成为软件卖点，得到了烟台检验局的认可。</td></tr><tr><td colspan="2" align="center" valign="middle"><hr size="1" noshade></td></tr><tr><td colspan="2" class="text_left">2007      /5--2007      /6：大连正阳依势精密铸造厂内部供销管理系统</td></tr><tr><td width="16%" class="text_left" valign="top">项目描述：</td><td width="84%" class="text">二人参与研发。用时一个月。</td></tr><tr><td class="text_left" valign="top">责任描述：</td><td class="text">负责项目全部代码编写。数据库设计。后期维护。现场技术指导。</td></tr><tr><td colspan="2" align="center" valign="middle"><hr size="1" noshade></td></tr><tr><td colspan="2" class="text_left">2006      /7--2006      /9：山东烟台万华信息发布管理系统</td></tr><tr><td width="16%" class="text_left" valign="top">项目描述：</td><td width="84%" class="text">分为通知公告、新闻报道、企业文化、市场信息四个类别。可以发布该任何类别的相关信息。 和OA的信息发布系统连接。共用一个人员库。<br>2人参与研发．</td></tr><tr><td class="text_left" valign="top">责任描述：</td><td class="text">负责新闻报道、企业文化、市场信息、信息发布模块配置四个部分的开发。还包括页面的美化。</td></tr><tr><td colspan="2" align="center" valign="middle"><hr size="1" noshade></td></tr><tr><td colspan="2" class="text_left">2006      /6--2006      /11：北京威通易迅网络技术有限公司应用流量分析系统</td></tr><tr><td width="16%" class="text_left" valign="top">项目描述：</td><td width="84%" class="text">2人参与研发．</td></tr><tr><td class="text_left" valign="top">责任描述：</td><td class="text">全权负责系统后台配置管理（程序和页面设计）。前台用户登陆、注册部分。负责页面设计。负责数据库流量测试，参与数据库代码编写。</td></tr><tr><td colspan="2" align="center" valign="middle"><hr size="1" noshade></td></tr><tr><td colspan="2" class="text_left">2006      /3--2006      /7：中国龙鱼俱乐部网站</td></tr><tr><td width="16%" class="text_left" valign="top">项目描述：</td><td width="84%" class="text">3人参与研发．</td></tr><tr><td class="text_left" valign="top">责任描述：</td><td class="text">全权负责系统后台配置管理（程序和页面设计）。前台用户登陆、注册部分。专业知识管理部分。商家在线、渔场在线。全权负责网站论坛。网址：http://www.arowchinaclub.com</td></tr><tr><td colspan="2" align="center" valign="middle"><hr size="1" noshade></td></tr><tr><td colspan="2" class="text_left">2006      /1--2006      /4：中海油田股份有限公司IT客户服务网站</td></tr><tr><td width="16%" class="text_left" valign="top">项目描述：</td><td width="84%" class="text">本网站是一套配置比较全面的IT客户服务性网站。其中包括客户服务、在线留言、在线调查、网站论坛等模块。<br>开发人数：3人<br>开发用时：4个月</td></tr><tr><td class="text_left" valign="top">责任描述：</td><td class="text">本人主要负责了，在线调查、客户服务、网站论坛的设计编写。<br>后期调试与使用手册编写。</td></tr><tr><td colspan="2" align="center" valign="middle"><hr size="1" noshade></td></tr><tr><td colspan="2" class="text_left">2005      /6--2006      /6：山东烟台万华人力资源管理系统</td></tr><tr><td width="16%" class="text_left" valign="top">项目描述：</td><td width="84%" class="text">本系统是为山东烟台万华聚氨脂股份有限公司内部人员管理而设计开发人力资源系统.本系统用时１年．最终通过验收，取得成功．<br>开发人数：2人<br>开发用时：12个月</td></tr><tr><td class="text_left" valign="top">责任描述：</td><td class="text">主要负责人事信息管理，员工自助，两大部分设计与编写．包括数据编码管理和一些公用插件的编写．数据库的整体编写与建立．使用说明文档的编写．需求分析的编写．配合了现场的调试与安装．并负责后期的修改与维护．</td></tr><tr><td colspan="2" align="center" valign="middle"><hr size="1" noshade></td></tr><tr><td colspan="2" class="text_left">2004      /6--2005      /6：北京中信证券办公子系统</td></tr><tr><td class="text_left" valign="top"> 硬件环境：</td><td class="text">　</td></tr><tr><td width="16%" class="text_left" valign="top">项目描述：</td><td width="84%" class="text">本系统是同中信证券ＯＡ办公自动化系统配套的利用ＪＡＶＡ开发的办公子系统．ＯＡ办公系统与ＪＡＶＡ子系统相互联系与补充．<br>开发人数：3人<br>开发用时：12个月</td></tr><tr><td class="text_left" valign="top">责任描述：</td><td class="text">负责模块设计、编写与测试。包括：系统提醒、常用意见审批、用户群组、政策法规、资产管理（类型配置、管理配置、转移配置、报废配置）、档案管理（类型配置、卷目配置、管理配置、销毁、分发、阅档）等模块。</td></tr><tr><td colspan="2" align="center" valign="middle"><hr size="1" noshade></td></tr><tr><td colspan="2" class="text_left">2003      /4--2004      /6：中国齐鲁石化生产调度辅助决策指挥系统日常管理子系统</td></tr><tr><td width="16%" class="text_left" valign="top">项目描述：</td><td width="84%" class="text">本系统是为山东齐鲁石化公司设计制作的生产调度辅助决策指挥系统日常管理子系统。配合原有的调度系统一起使用。数据管理与计算十分烦琐。这也是本人来到公司参加的第一个系统。<br>本系统从设计到完成，用时１４个月，５人参与研发．最终按需求圆满完成计划．</td></tr><tr><td class="text_left" valign="top">责任描述：</td><td class="text">生产调令发布＼生产调令发布回复、检索＼周重点工作发布、反馈＼装置停工配置＼装置停工信息录入＼装置停工统计和查询(包括图形统计)。<br>系统使用说明与测试报告由本人编写。<br>并参加了后期的调试与维护。</td></tr></table>
EOF;
        $matcher = new RegexMatcher($html,true);
        $ret = $matcher->all('/<tr>\s*?<td.*?>(?<start_time>[\s\d]+\/\d+?)--(?<end_time>.*?)?：(?<project>.*?)<\/td><\/tr>(<tr><td.*?>\s*?软件环境：<\/td>\s*<td.*?>(?<env_software>.*?)<\/td><\/tr>)??(<tr><td.*?>\s*?硬件环境：<\/td><td.*?>(?<env_hardware>.*?)<\/td><\/tr>)??(<tr><td.*?>\s*?开发工具：<\/td><td.*?>(?<dev_tool>.*?)<\/td><\/tr>)??<tr><td.*?>项目描述：<\/td>\s*<td.*?>(?<project_desp>.*?)<\/td><\/tr>.*?责任描述：<\/td>\s*<td.*?>(?<resp_detail>.*?)<\/td>\s*<\/tr>/m');
        var_dump($ret);
    }
}