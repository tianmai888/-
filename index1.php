<?php
defined('IN_PHPCMS') or exit('No permission resources.');
pc_base::load_app_class('admin','admin',0);

class index extends admin {
	public function __construct() {
		parent::__construct();
		$this->db = pc_base::load_model('admin_model');
		$this->menu_db = pc_base::load_model('menu_model');
		$this->panel_db = pc_base::load_model('admin_panel_model');

	}

	public function init () {

        $userid = $_SESSION['userid'];

		$admin_username = param::get_cookie('admin_username');
		$roles = getcache('role','commons');

		$rolename = $roles[$_SESSION['roleid']];

		$site = pc_base::load_app_class('sites');
		$sitelist = $site->get_list($_SESSION['roleid']);
		$currentsite = $this->get_siteinfo(param::get_cookie('siteid'));
		/*管理员收藏栏*/
		$adminpanel = $this->panel_db->select(array('userid'=>$userid), "*",20 , 'datetime');
		$site_model = param::get_cookie('site_model');

        $this->role_provdb = pc_base::load_model('admin_role_priv_model');
        $admin_menu = $this->role_provdb->select(array('roleid'=>$_SESSION['roleid'],'m'=>'admin','c'=>'index'),'a');
        $new=array();
        foreach($admin_menu as $k=>$v){
            $new[$v[a]]=1;
        }
        extract($new);

        if($_SESSION['roleid']==1){
            include $this->admin_tpl('index');
        }elseif($_SESSION['roleid']>=2 && $_SESSION['roleid']<=22){
            //include $this->admin_tpl('index');
            include $this->admin_tpl('index_zongcai_menu');
        }elseif($_SESSION['roleid']==23){ //连锁导出
			include $this->admin_tpl('index_zongcai_daochu');
		}elseif($_SESSION['roleid']==24){ //教材教辅导出
			include $this->admin_tpl('index_zongcai_daochu_jiaocai');
		}elseif($_SESSION['roleid']==26){ //导出总管理员
			include $this->admin_tpl('index_zongcai_daochu_guanliyuan');
		}
		

	}
    public function qsxsy(){
        set_time_limit(0);
        $userid = $_SESSION['userid'];
        $admin_username = param::get_cookie('admin_username');
        $roles = getcache('role','commons');

        $rolename = $roles[$_SESSION['roleid']];

        $site = pc_base::load_app_class('sites');
        $sitelist = $site->get_list($_SESSION['roleid']);
        $currentsite = $this->get_siteinfo(param::get_cookie('siteid'));
        /*管理员收藏栏*/
        $adminpanel = $this->panel_db->select(array('userid'=>$userid), "*",20 , 'datetime');
        $site_model = param::get_cookie('site_model');


        $this->role_provdb = pc_base::load_model('admin_role_priv_model');
        $admin_menu = $this->role_provdb->select(array('roleid'=>$_SESSION['roleid'],'m'=>'admin','c'=>'index'),'a');
        $new=array();
        foreach($admin_menu as $k=>$v){
            $new[$v[a]]=1;
        }
        extract($new);

        pc_base::load_app_class('oracle_admin','admin',0);//数据库
            $host='172.30.153.61';
            $ip='172.30.153.61/xhsddb';
            $port='1521';
            $user= 'booklsd';
            $pass= 'zjlsdqwert';
        $charset='utf8';
        $oraquansheng=new oracle_admin($user,$pass,$ip,$charset);

        $host='172.30.153.63';
        $ip='172.30.153.63/XHSDDB';
        $port='1521';
        $user= 'dbjczc';
        $pass= 'dbjczc';
            $charset='utf8';

            $ora=new oracle_admin($user,$pass,$ip,$charset);

            $start=date('Y',time()).'-1-1';
            $end=date('Y-m-d',time()-24*60*60);
            $nian=date('Y',time());
            $end_time=date('Y年m月d日H时',time());
            $end_time1=date('Y-m-d-H',time());
            $yewuleixing=getcache($end_time1."_quanshengzonge");
            if(empty($yewuleixing)){
//                //全省销售总额
 //               $sql="Select Sum(zmy) zmy From (Select ywbmbh,zmy,dbrq,dqbh_gg From t_xsls_lsd Where ".
 //                   " (ywbmbh Like 'H%' Or ywbmbh Like 'L%' Or ywbmbh Like 'F%' Or ywbmbh Like 'X%') And DBRQ>=Date'$start' And dbrq<=TRUNC(Sysdate,'HH') ".
 //                   " Union All Select ywbmbh,zmy,dbrq,dqbh_gg From t_zlxsls_lsd Where (ywbmbh Like 'H%' Or ywbmbh Like 'L%' Or ywbmbh Like 'X%')".
 //                   "And DBRQ>=Date'$start' And dbrq<=TRUNC(Sysdate,'HH'))m";
               
			   //原来使用的句子,正确
			   //$sql="Select Sum(zmy)  zmy From(Select   Sum(XSCS * (Select DJ From T_KCSM Where Id = T.ID)) As ZMY     From t_xsls T".
                //    " Where  (ywbmbh Like 'H%' Or ywbmbh Like 'L%' Or ywbmbh Like 'F%' Or ywbmbh Like 'X%') And DBRQ>=Date'$start' And dbrq<=TRUNC(Sysdate,'HH') ".
                 //  "And xslx <> '2' Union All Select  Sum(pxCS * (Select DJ From T_KCSM Where Id = T.ID)) As ZMY ".
                 //   "From t_pxmx T  Where pxRQ>=Date'$start' And pxrq<=TRUNC(Sysdate,'HH')and (ywbmbh Like 'H%' Or ywbmbh Like 'L%' Or ywbmbh Like 'F%' Or ywbmbh Like 'X%') Union All Select  Sum(yjje) As ZMY From t_zl_xshz T Where jk_date >= Date'$start' ".
                 // " And jk_date <TRUNC(Sysdate,'HH') and (ywbmbh Like 'H%' Or ywbmbh Like 'L%' Or ywbmbh Like 'F%' Or ywbmbh Like 'X%') ) u";
               //使用累计办法,增加计算速度
			   $sql="Select Sum(zmy)  zmy From(Select   Sum(XSCS * (Select DJ From T_KCSM Where Id = T.ID)) As ZMY     From t_xsls T".
                    " Where  (ywbmbh Like 'H%' Or ywbmbh Like 'L%' Or ywbmbh Like 'F%' Or ywbmbh Like 'X%') And DBRQ>=(select TRUNC(crrq+1) from t_zczm_ljls) And dbrq<=TRUNC(Sysdate,'HH') ".
                   "And xslx <> '2' Union All Select  Sum(pxCS * (Select DJ From T_KCSM Where Id = T.ID)) As ZMY ".
                    "From t_pxmx T  Where pxRQ >= (select TRUNC(crrq+1) from t_zczm_ljls) And pxrq<=TRUNC(Sysdate,'HH')and (ywbmbh Like 'H%' Or ywbmbh Like 'L%' Or ywbmbh Like 'F%' Or ywbmbh Like 'X%') Union All Select  Sum(yjje) As ZMY From t_zl_xshz T Where jk_date >= (select TRUNC(crrq+1) from t_zczm_ljls) ".
                  " And jk_date <=TRUNC(Sysdate,'HH') and (ywbmbh Like 'H%' Or ywbmbh Like 'L%' Or ywbmbh Like 'F%' Or ywbmbh Like 'X%') union all select sum(zmy) as zmy from t_zczm_ljls) u";
			   $yewuleixing=$oraquansheng->query($sql);
                //$yewuleixing=$ora->query($sql);
                $yewuleixing=$yewuleixing[0][ZMY];
                setcache($end_time1."_quanshengzonge",$yewuleixing);
            }



        $quyu=getcache($end."_diqu");
        $xiaoshou=getcache($end."_diquxiaoshou");
        $zuidazhi=getcache($end."_diquzuidazhi");
        if(empty($quyu)||empty($xiaoshou)||empty($zuidazhi)){
            //各个地区的销售总额
//            $sql="Select Sum(m.zmy) zmy,m.dqbh_gg,m.mc AREA From (Select ywbmbh,zmy,dbrq,dqbh_gg,(select mc from t_Zczm_Dqbm where t.dqbh_gg = bh) as mc From t_xsls_lsd t,t_Zczm_Dqbm h ".
//                "Where (ywbmbh Like 'H%' Or ywbmbh Like 'L%' Or ywbmbh Like 'X%' Or ywbmbh Like 'F%') And DBRQ>=Date'$start' And dbrq<=Date'$end'  And dqbh_gg<>'17' And t.dqbh_gg= h.bh  Union All ".
//                "Select ywbmbh,zmy,dbrq,dqbh_gg,(select mc from t_Zczm_Dqbm where a.dqbh_gg = bh) as mc From t_zlxsls_lsd a,t_Zczm_Dqbm f ".
//                "Where (ywbmbh Like 'H%' Or ywbmbh Like 'L%' Or ywbmbh Like 'X%') And DBRQ>=Date'$start' And dbrq<=Date'$end'  And dqbh_gg<>'17' And a.dqbh_gg=f.bh )m Group By m.dqbh_gg,m.mc order by m.dqbh_gg";
            $sql="Select SD as AREA,dqbh,Sum(zmy) zmy From(Select b.dqmc As SD,b.dqBH_GG as dqbh,Sum(a.zmy)As zmy From t_xsls_lsd a,t_zczm_dm b Where a.ywbmbh=b.dh And 	a.dbrq>=Date'$start' And a.dbrq<=Date'$end' And b.bj_sd='01' And ".
                " (a.ywbmbh Like 'H%' Or a.ywbmbh Like 'L%' Or a.ywbmbh Like 'X%' Or ywbmbh Like 'F%') Group By b.dqmc,b.dqBH_GG Union All Select b.dqmc As SD,b.dqBH_GG as dqbh,Sum(a.zmy)As zmy From t_zlxsls_lsd ".
                "a,t_zczm_dm b Where a.ywbmbh=b.dh And 	a.dbrq>=Date'$start' And a.dbrq<=Date'$end' And b.bj_sd='01' And (a.ywbmbh Like 'H%' Or a.ywbmbh Like 'L%'  Or a.ywbmbh Like 'X%' "."
                Or ywbmbh Like 'F%') Group By b.dqmc,b.dqBH_GG)T Group By SD,dqbh order by dqbh";
            $area=$ora->query($sql);
			
            $quyu='';
            $xiaoshou='';
            $zuidazhi=array();
            foreach($area as $k=>$v){
                if(!empty($v[AREA])){
                    $quyu.="'$v[AREA]',";
                    $xiaoshou.=floor($v[ZMY]*0.0001).",";
                    $zuidazhi[]=floor($v[ZMY]*0.0001);
                }
            }

            $key=array_search(max($zuidazhi), $zuidazhi);
            $zuidazhi=$zuidazhi[$key];
            $w=strlen($zuidazhi);
            $zuidazhi=substr($zuidazhi,0,1);
            if($zuidazhi%2==0){
                $zuidazhi=($zuidazhi+2)*pow(10,$w-1);
            }else{
                $zuidazhi=($zuidazhi+1)*pow(10,$w-1);
            }
            setcache($end."_diqu",$quyu);
            setcache($end."_diquxiaoshou",$xiaoshou);
            setcache($end."_diquzuidazhi",$zuidazhi);
        }
        $jiaoxueyongshu=getcache("jiaoxueyongshu_".$start."_".$end);
//        $ip='172.30.153.61/XHSDDB';
//        $port='1521';
//        $user= 'HBJCYW';
//        $pass= 'HBJCYW';
//        $charset='utf8';
//        pc_base::load_app_class('oracle_admin','admin',0);//数据库
//        $ora=new oracle_admin($user,$pass,$ip,$charset);
        if(empty($jiaoxueyongshu)){
			//教学用书所用到的sql
            //进货结算
            $sql="Select Sum(zmy) ZMY From (Select zmy From t_dhhz_sd Where ywlx='JC' And ywlx2='DH' And ywbmbh='010001' And ".
                " DBRQ>=Date'$start' And DBRQ<=Date'$end'Union All Select -zmy From T_HYTH_SD Where ywlx='JC' And ywlx2='TH' ".
                "And ywbmbh='010001' And DBRQ>=Date'$start' And DBRQ<=Date'$end')M";
            $row=$ora->query($sql);
            $jin=$row[0]['ZMY'];
            //销

            $sql="Select Sum(zmy) as zmy From (Select zmy From t_fhhz_sd Where ywlx='JC' And ywlx2='FH' And ywbmbh='010001' And ".
                "DBRQ>=Date'$start' And DBRQ<=Date'$end' Union All Select -zmy From t_thhz_sd Where ywlx='JC' And ".
                "ywlx2='XT' And ywbmbh='010001' And DBRQ>=Date'$start' And DBRQ<=Date'$end')M";
            $row=$ora->query($sql);
            $xiao=$row[0]['ZMY'];
            //存
//            $sql="Select sum(kccs),Sum(kcmy) as KCMY From
//(Select ywbmbh,Id,(Select sm From t_kcsm Where id=t.id) sm,sum(kccs) kccs, sum((Select dj From t_kcsm Where Id=t.id)*t.kccs) kcmy from t_kcsl t
//Where ywbmbh='010001'
//Group By ywbmbh,Id)M";
            $sql="Select sum(kcmy) KCMY From t_kcsl_sd Where ywlx='JC'And ywbmbh='010001'";
            $row=$ora->query($sql);
            $cun=$row[0]['KCMY'];
            $jiaoxueyongshu[ZONGJIN]=floor($jin*0.0001);
            $jiaoxueyongshu[ZONGFA]=floor($xiao*0.0001);
            $jiaoxueyongshu[ZONGCUN]=floor($cun*0.0001);
            setcache("jiaoxueyongshu".$start."_".$end,$jiaoxueyongshu);
        }
        $tushuyingxiao=getcache("tushuyingxiao".$start."_".$end);
        if(empty($tushuyingxiao)){
			//图书营销公司
            //进货结算
//            $sql="Select sum(m.zmy) as zmy from
//(
//Select ywbmbh,GHDWH,sum(djshsl) zcs,Sum(djshsl*zjxs) zmy,sum(sys) zsy From t_dhls_jx Where shrq>Date'$start' And shrq<Date'$end'
//And ywbmbh In ('020001','020002','020003','020004','020005')
//  Group By ywbmbh,GHDWH
//Union All
//Select ywbmbh,HYBH,-sum(zcs) zcs,-sum(zmy) zmy,-sum(zsy) zsy From t_hythhz Where thrq>Date'$start' And thrq<Date'$end'
//And ywbmbh In ('020001','020002','020003','020004','020005')
//Group By ywbmbh,HYBH
//) M";
            $sql="Select Sum(zmy) zmy From (Select zmy From t_dhhz_sd Where ywlx='JC' And ywlx2='DH' And (ywbmbh='020001' Or ywbmbh='020002' Or ywbmbh='020003' Or ywbmbh='020004'Or ywbmbh='020005') And ".
                " DBRQ>=Date'$start' And DBRQ<=Date'$end' Union All Select -zmy From T_HYTH_SD Where ywlx='JC' And ywlx2='TH' And (ywbmbh='020001' Or ywbmbh='020002' Or ywbmbh='020003' Or ywbmbh='020004'Or ywbmbh='020005') And ".
                "DBRQ>=Date'$start' And DBRQ<=Date'$end')M";
            $row=$ora->query($sql);
            $jin=$row[0]['ZMY'];
            //销
//            $sql="Select sum(m.zmy) as zmy from
//(
//Select ywbmbh,dh,sum(sfcs) zcs,sum((Select dj From t_kcsm Where Id=t_fhmx.id)*t_fhmx.sfcs) zmy,sum(sys) zsy From t_fhmx
//Where dbrq>=Date'$start' And dbrq<Date'$end'
//And ywbmbh In ('020001','020002','020003','020004','020005')
//	Group By ywbmbh,dh
//Union All
//Select ywbmbh,dh,-sum(thcs) zcs,-sum((Select dj From t_kcsm Where Id=t_khthmx.id)*t_khthmx.thcs) zmy,-sum(sys) zsy From t_khthmx
//Where lrrq1>=Date'$start' And lrrq1<Date'$end'
//And ywbmbh In ('020001','020002','020003','020004','020005')
// Group By ywbmbh,dh
//) M";
            $sql="Select Sum(zmy) ZMY From (Select zmy From t_fhhz_sd Where ywlx='JC' And ywlx2='FH' And (ywbmbh='020001' Or ywbmbh='020002' Or ywbmbh='020003' Or ywbmbh='020004'Or ywbmbh='020005') And ".
                " DBRQ>=Date'$start' And DBRQ<=Date'$end' Union All Select -zmy From t_thhz_sd Where ywlx='JC' And ywlx2='XT' And (ywbmbh='020001' Or ywbmbh='020002' Or ywbmbh='020003' Or ywbmbh='020004'Or ywbmbh='020005') And ".
                " DBRQ>=Date'$start' And DBRQ<=Date'$end')M";
            $row=$ora->query($sql);
            $xiao=$row[0]['ZMY'];
            //存
//            $sql="Select sum(kccs),Sum(kcmy) kcmy From
//(Select ywbmbh,Id,(Select sm From t_kcsm Where id=t.id) sm,sum(kccs) kccs, sum((Select dj From t_kcsm Where Id=t.id)*t.kccs) kcmy from t_kcsl t
//Where  ywbmbh In ('020001','020002','020003','020004','020005')Group By ywbmbh,Id)M";
            $sql="Select sum(kcmy) KCMY From t_kcsl_sd Where ywlx='JF' And (ywbmbh='020001' Or ywbmbh='020002' Or ywbmbh='020003' Or ywbmbh='020004'Or ywbmbh='020005')";
            $row=$ora->query($sql);
            $cun=$row[0]['KCMY'];
            $tushuyingxiao[ZONGJIN]=floor($jin*0.0001);
            $tushuyingxiao[ZONGFA]=floor($xiao*0.0001);
            $tushuyingxiao[ZONGCUN]=floor($cun*0.0001);
            setcache("tushuyingxiao".$start."_".$end,$tushuyingxiao);
        }
        $tushuliansuogongsi=getcache("tushuliansuogongsi".$start."_".$end);
        if(empty($tushuliansuogongsi)){

            //图书连锁公司
//            $ip='172.30.153.20/xhsddb';
//            $port='1521';
//            $user= 'dbsl';
//            $pass= 'dbsl';
//            $charset='utf8';
//            pc_base::load_app_class('oracle_admin','admin',0);//数据库
//            $ora=new oracle_admin($user,$pass,$ip,$charset);
            //进
//            $sql="Select sum(m.zcs)as 总册数,sum(m.zmy) as zmy,sum(m.zsy)as 总实洋 from
// (select a.ywbmbh,b.dm as ywbmmc,a.ghdwh,c.mc,sum(a.zcs) as zcs,sum(a.zmy) as zmy,sum(a.zsy) as zsy
//from t_bldhdj a,t_ywbm_ddsp b,t_ghdw c where c.bh = a.ghdwh and b.dh = a.ywbmbh
//and a.dbrq >= date'$start' and a.dbrq < date'$end'
//and a.ywbmbh in('000001','000002','000003','000004','000005')
//group by a.ywbmbh,b.dm,a.ghdwh,c.mc
//union all
//select a.ywbmbh,b.dm as ywbmmc,a.ghdwh,c.mc,sum(a.zcs) as zcs,sum(a.zmy) as zmy,sum(a.zsy) as zsy
//from t_dhdj_cf a,t_ywbm_ddsp b,t_ghdw c where c.bh = a.ghdwh and b.dh = a.ywbmbh
//and a.dbrq >= date'$start' and a.dbrq < date'$end'
//and a.ywbmbh in('000001','000002','000003','000004','000005')
//group by a.ywbmbh,b.dm,a.ghdwh,c.mc
//union all
//select a.ywbmbh,b.dm as ywbmmc,a.hybh,c.mc,sum(-a.zcs) as zcs,sum(-a.zmy) as zmy,sum(-a.zsy) as zsy
//from t_hythhz a,t_ywbm_ddsp b,t_ghdw c where c.bh = a.hybh and b.dh = a.ywbmbh
//and a.thrq >= date'$start' and a.thrq < date'$end'
//and a.ywbmbh in('000001','000002','000003','000004','000005')
//group by a.ywbmbh,b.dm,a.hybh,c.mc) m";
            $sql="Select Sum(zmy) zmy From t_dhhz_sd Where ywlx='LS' And DBRQ>=Date'$start' And DBRQ<=Date'$end'";
            $row=$ora->query($sql);
            $jin=$row[0]['ZMY'];
            //销
//            $sql="Select sum(m.zcs) as 总册数,sum(m.zmy) as ZMY,sum(m.zsy) as 总实洋 from
//(select t.ywbmbh,b.dm as ywbmmc,t.dh,c.dm,sum(t.zcs) as zcs,sum(t.zmy) as zmy,sum(t.zsy) as zsy from t_fhhz t,t_ywbm_ddsp b,t_dm c
//where b.dh = t.ywbmbh and c.dh = t.dh
//and dbrq>=date'$start' and dbrq <date '$end' and t.ywbmbh in('000001','000002','000003','000004','000005')
//group by t.ywbmbh,b.dm,t.dh,c.dm
//union all
//select t.ywbmbh,b.dm as ywbmmc,t.dh,c.dm,sum(t.zcs) as zcs,sum(t.zmy) as zmy,sum(t.zsy) as zsy from t_fhhz1 t,t_ywbm_ddsp b,t_dm c
//where b.dh = t.ywbmbh and c.dh = t.dh and dbrq>=date'$start' and dbrq <date '$end' and
//t.ywbmbh in('000001','000002','000003','000004','000005')
//and not exists (Select 1 From t_fhhz Where pfpch = t.pfpch)
//group by t.ywbmbh,b.dm,t.dh,c.dm
//union all
//select t.ywbmbh,b.dm as ywbmmc,t.dh,c.dm,sum(t.zcs) as zcs,sum(t.zmy) as zmy,sum(t.zsy) as zsy from t_fhhz2 t,t_ywbm_ddsp b,t_dm c
//where b.dh = t.ywbmbh and c.dh = t.dh and dbrq>=date'$start' and dbrq <date '$end'
//and t.ywbmbh in('000001','000002','000003','000004','000005')
//and not exists (Select 1 From t_fhhz Where pfpch = t.pfpch)
//group by t.ywbmbh,b.dm,t.dh,c.dm
//union all
//select t.ywbmbh,b.dm as ywbmmc,t.dh,c.dm,sum(-t.zcs) as zcs,sum(-t.zmy) as zmy,sum(-t.zsy) as zsy from T_KHTHHZ t,t_ywbm_ddsp b,t_dm c
//where b.dh = t.ywbmbh and c.dh = t.dh
//and t.thrq>=date'$start' and t.thrq <date '$end'
//and t.ywbmbh in('000001','000002','000003','000004','000005')
//group by t.ywbmbh,b.dm,t.dh,c.dm
//union all
//select t.ywbmbh,b.dm as ywbmmc,t.dh,c.dm,sum(-t.zcs) as zcs,0 as zmy,sum(-t.zsy) as zsy from T_KHTHHZ1 t,t_ywbm_ddsp b,t_dm c
//where b.dh = t.ywbmbh and c.dh = t.dh
//and t.thrq>=date'$start' and t.thrq <date '$end' and  DJLX = 'YH'
//and t.ywbmbh in('000001','000002','000003','000004','000005')
//group by t.ywbmbh,b.dm,t.dh,c.dm
//union all
//select t.ywbmbh,b.dm as ywbmmc,t.dh,c.dm,sum(-t.zcs) as zcs,0 as zmy,sum(-t.zsy) as zsy from T_KHTHHZ2 t,t_ywbm_ddsp b,t_dm c
//where b.dh = t.ywbmbh and c.dh = t.dh
//and t.thrq>=date'$start' and t.thrq <date '$end' and  DJLX = 'YH'
//and t.ywbmbh in('000001','000002','000003','000004','000005')
//group by t.ywbmbh,b.dm,t.dh,c.dm)  m";
            $sql="Select Sum(zmy) zmy From t_fhhz_sd Where ywlx='LS' And DBRQ>=Date'$start' And DBRQ<=Date'$end'";
            $row=$ora->query($sql);
            $xiao=$row[0]['ZMY'];
            //存
/*           $sql="Select sum(a.kccs) as kccs,Sum((Select dj From t_kcsm Where id=a.id)*a.kccs) zmy from t_kcsl a where
a.ywbmbh in ('000001','000002','000003','000004','000005')";*/
            $sql="Select Sum(kcmy) kcmy From t_kcsl_sd Where ywlx='LS' And ywbmbh In('000001','000002','000003','000004','000005')";
            $row=$ora->query($sql);
            $cun=$row[0]['KCMY'];
            $tushuliansuogongsi[ZONGJIN]=floor($jin*0.0001);
            $tushuliansuogongsi[ZONGFA]=floor($xiao*0.0001);
            $tushuliansuogongsi[ZONGCUN]=floor($cun*0.0001);

            setcache("tushuliansuogongsi".$start."_".$end,$tushuliansuogongsi);
        }
            include $this->admin_tpl('index_zong');

    }
    public function shengdian () {
        set_time_limit(0);
        $userid = $_SESSION['userid'];
        $admin_username = param::get_cookie('admin_username');
        $roles = getcache('role','commons');
        $rolename = $roles[$_SESSION['roleid']];
        $site = pc_base::load_app_class('sites');
        $sitelist = $site->get_list($_SESSION['roleid']);
        $currentsite = $this->get_siteinfo(param::get_cookie('siteid'));
        /*管理员收藏栏*/
        $adminpanel = $this->panel_db->select(array('userid'=>$userid), "*",20 , 'datetime');
        $site_model = param::get_cookie('site_model');
        $title='省店经营情况';
        $this->role_provdb = pc_base::load_model('admin_role_priv_model');
        $admin_menu = $this->role_provdb->select(array('roleid'=>$_SESSION['roleid'],'m'=>'admin','c'=>'index'),'a');
        $new=array();
        foreach($admin_menu as $k=>$v){
            $new[$v[a]]=1;
        }
        extract($new);
        //省店营销公司
//        $host='172.30.153.61';//172.30.153.20
//        $ip='172.30.153.61/XHSDDB';
//        $port='1521';
//        $user= 'HBJCYW';
//        $pass= 'HBJCYW';
        $host='172.30.153.63';
        $ip='172.30.153.63/XHSDDB';
        $port='1521';
        $user= 'dbjczc';
        $pass= 'dbjczc';
        $charset='utf8';
        pc_base::load_app_class('oracle_admin','admin',0);//数据库
        $ora=new oracle_admin($user,$pass,$ip,$charset);
//        ///连锁公司数据库
//        $ip='172.30.153.20/xhsddb';
//        $port='1521';
//        $user= 'dbsl';
//        $pass= 'dbsl';
//        $charset='utf8';
//        pc_base::load_app_class('oracle_admin','admin',0);//数据库
//        $ora=new oracle_admin($user,$pass,$ip,$charset);
//
//		///连锁公司数据库
//        $ip='172.30.153.20/xhsddb';
//        $port='1521';
//        $user= 'dbjczc';
//        $pass= 'dbjczc';
//        $charset='utf8';
//        pc_base::load_app_class('oracle_admin','admin',0);//数据库
//        $new_db=new oracle_admin($user,$pass,$ip,$charset);
		

        //$start=isset($_REQUEST['start'])?$_REQUEST['start']:date('Y',time()).'-1-1';
        $start=isset($_REQUEST['start'])?$_REQUEST['start']:date('Y-m-d',time()-24*60*60*7);
        $end=isset($_REQUEST['end'])?$_REQUEST['end']:date('Y-m-d',time()-24*60*60);
		
		//echo $start.'--'.$end;exit; //2018-06-25--2018-07-01

        $yewugongsi=isset($_REQUEST['yewugongsi'])?$_REQUEST['yewugongsi']:-1;
        $yewuleixing=isset($_REQUEST['yewuleixing'])&&$_REQUEST['yewuleixing']!='全部业务类型'?$_REQUEST['yewuleixing']:'';
        if($yewugongsi==0)//查询营销公司所有的业务
        {
            $yewugongsiname='营销公司';
            $info=getcache("shengdian_yingxiao_".$start.'_'.$end);
            if(empty($info)){
                //净进货
                $sql="select ywbmbh,(select trim(dm) from T_ZCZM_YWBM where dh = ywbmbh) bmmc,sum(zcs) zcs,Sum(zmy) zmy,Sum(zsy) zsy From t_Dhhz_Sd Where ywbmbh<>'010001' And ywlx='JC' And ".
                    "dbrq>=Date'$start' And dbrq<=Date'$end' Group By ywbmbh union all select ywbmbh,(select trim(dm) from T_ZCZM_YWBM where dh = ywbmbh) bmmc,sum(zcs),Sum(zmy),Sum(zsy) From t_Dhhz_Sd Where ywbmbh='000010' And ywlx='LS' And ".
                    "dbrq>=Date'$start' And dbrq<=Date'$end' Group By ywbmbh";
                $row=$ora->query($sql);

                $info[jingjinhuo]=$row;
                //净发货
                $sql="Select  ywbmbh,(select trim(dm) from T_ZCZM_YWBM where dh = ywbmbh) bmmc,sum(zcs) zcs,Sum(zmy) zmy,Sum(zsy) zsy From t_Fhhz_Sd Where ywbmbh<>'010001' And ywlx='JC' And ".
                    "dbrq>=Date'$start' And dbrq<=Date'$end' Group By ywbmbh union all Select  ywbmbh,(select trim(dm) from T_ZCZM_YWBM where dh = ywbmbh) bmmc,sum(zcs),Sum(zmy),Sum(zsy) From t_Fhhz_Sd Where ywbmbh='000010' And ywlx='LS' And ".
                    "dbrq>=Date'$start' And dbrq<=Date'$end' Group By ywbmbh";
                $row=$ora->query($sql);
                $info[jingfahuo]=$row;

                //收款
                $sql="Select Ywbmbh,( select trim(dm) from T_Zczm_Ywbm where dh= T.Ywbmbh)As bmmc, Sum(Fhmy+thmy) as zmy,Sum(Fhsy+Thsy)as zsy,sum(Fhcb+Thcb)as zcb From t_fhjshz t Where ywbmbh<>'010001' And ywlx='JC'  And ".
                    "jsrq >=Date'$start' And jsrq <= date'$end' group by Ywbmbh Union all Select Ywbmbh,( select trim(dm) from T_Zczm_Ywbm where dh= T.Ywbmbh)As bmmc, Sum(Fhmy+thmy) as zmy,Sum(Fhsy+Thsy)as zsy,sum(Fhcb+Thcb)as zcb From t_fhjshz t Where ywbmbh='000010' And ywlx='LS'  And ".
                    "jsrq >=Date'$start' And jsrq <= date'$end' group by Ywbmbh";
                $row=$ora->query($sql);
                $info[fahuoyijie]=$row;
                //付款
                $sql="Select  ywbmbh,(select trim(dm) from T_ZCZM_YWBM where dh = ywbmbh) bmmc,Sum(zmy) zmy,Sum(zsy) zsy From t_Hyjs_Sd Where ywbmbh<>'010001' And ywlx='JC' And ".
                    "jsrq >=Date'$start' And jsrq <=date'$end' Group By ywbmbh union all Select  ywbmbh,(select trim(dm) from T_ZCZM_YWBM where dh = ywbmbh) bmmc,Sum(zmy),Sum(zsy) From t_Hyjs_Sd Where ywbmbh='000010' And ywlx='LS' And ".
                    "jsrq >=Date'$start' And jsrq <=date'$end' Group By ywbmbh";
                $row=$ora->query($sql);
                $info[daohuoyijie]=$row;
                //省店库存
                $sql="Select ywbmbh,(select trim(dm) from T_ZCZM_YWBM where dh = ywbmbh) as bmmc,Sum(kccs) kccs,Sum(kcmy) kcmy From t_kcsl_sd Where ywbmbh<>'010001' And ywlx='JF' Group By ywbmbh union all Select ywbmbh,(select trim(dm) from T_ZCZM_YWBM where dh = ywbmbh) as bmmc,Sum(kccs) kccs,Sum(kcmy) kcmy From t_kcsl_sd Where ywbmbh ='000010' And ywlx='LS' Group By ywbmbh";
                $row=$ora->query($sql);
                $info[shengdiankucun]=$row;
                //省店毛利率

                $sql="Select ywbmbh,bmmc,Sum(ll)/Sum(zmy) mll From (Select ywbmbh,(select trim(dm) from T_ZCZM_YWBM where dh = ywbmbh) as bmmc,Sum(zsy-cbj)As ll,sum(zmy)As zmy From t_Fhhz_Sd  Where ywbmbh<>'010001' And ywlx='JC' And ".
                    "dbrq>=Date'$start' And dbrq<=date'$end' Group By ywbmbh Union All Select ywbmbh,(select trim(dm) from T_ZCZM_YWBM where dh = ywbmbh) as bmmc, Sum(zsy-cbj)As ll,sum(zmy) As zmy From t_Thhz_Sd Where  ywbmbh<>'010001' And ywlx='JC' And ".
                    "dbrq>=Date'$start' And dbrq<=date'$end' Group By ywbmbh union all Select ywbmbh,(select trim(dm) from T_ZCZM_YWBM where dh = ywbmbh) as bmmc,Sum(zsy-cbj)As ll,sum(zmy)As zmy From t_Fhhz_Sd  Where ywbmbh='000010' And ywlx='LS' And ".
                    "dbrq>=Date'$start' And dbrq<=date'$end' Group By ywbmbh Union All Select ywbmbh,(select trim(dm) from T_ZCZM_YWBM where dh = ywbmbh) as bmmc,Sum(zsy-cbj)As ll,sum(zmy) As zmy From t_Thhz_Sd Where  ywbmbh='000010' And ywlx='LS' And ".
                    "dbrq>=Date'$start' And dbrq<=date'$end' Group By ywbmbh) t Group By ywbmbh, bmmc";
                $row=$ora->query($sql);
                $info[shengdianmaolilv]=$row;

                setcache("shengdian_yingxiao_".$start.'_'.$end,$info);

            }
			
            $jingjinhuo=array();
            $jingfahuo=array();
            $fahuoyijie=array();
            $daohuohuoyijie=array();
            $shengdiankucun=array();
            $shengdianmaolilv=array();
            $yewu=array();

            $new_leixing=array('河北新华教辅','河北新华读书活动','河北新华大中专','河北新华幼教','绘本');
            foreach($new_leixing as $k=>$v){
                $yewu[]=$v;
                $jingjinhuo[$v]=$info[jingjinhuo][$k]['ZMY'];
                $jingfahuo[$info[jingfahuo][$k]['BMMC']]=$info[jingfahuo][$k]['ZMY'];
                $daohuoyijie[$info[daohuoyijie][$k]['BMMC']]=$info[daohuoyijie][$k]['ZMY'];
                $fahuoyijie[$info[fahuoyijie][$k]['BMMC']]=$info[fahuoyijie][$k]['ZMY'];
                $shengdiankucun[$info[shengdiankucun][$k]['BMMC']]=$info[shengdiankucun][$k]['KCMY'];
                $shengdianmaolilv[$info[shengdianmaolilv][$k]['BMMC']]=$info[shengdianmaolilv][$k]['MLL'];
            }

            include $this->admin_tpl('show_shengdian_yewugongsi');
            die;
        }elseif($yewugongsi==1){
            $yewugongsiname='教材公司';

            $info=getcache("shengdian_jiaocai_".$start.'_'.$end);
            if(empty($info)){
                //教材公司
                //净进货
                $sql="Select sum(zcs),Sum(zmy) zmy,Sum(zsy) From t_Dhhz_Sd Where ywbmbh='010001' And ywlx='JC' And dbrq>=Date'$start' And dbrq<=Date'$end'";
                $row=$ora->query($sql);
                $info[jingjinhuo]=$row[0]['ZMY'];
                //净发货
                $sql="Select sum(zcs),Sum(zmy) zmy,Sum(zsy) From t_Fhhz_Sd Where ywbmbh='010001' And ywlx='JC' And dbrq>=Date'$start' And dbrq<=Date'$end'";
                $row=$ora->query($sql);
                $info[jingfahuo]=$row[0]['ZMY'];
                //发货已结
                $sql="Select  Sum(Fhmy+thmy) as zmy,Sum(Fhsy+Thsy)as zsy,sum(Fhcb+Thcb)as zcb From t_fhjshz t Where ywbmbh='010001' And ywlx='JC'  And jsrq >=Date'$start' And jsrq <= date'$end'";
                $row=$ora->query($sql);
                $info[fahuoyijie]=$row[0]['ZMY'];
                //到货已结

                $sql="Select Sum(zmy) zmy,Sum(zsy) From t_Hyjs_Sd Where ywbmbh='010001' And ywlx='JC' And jsrq >=Date'$start' And jsrq <=Date'$end'";
                $row=$ora->query($sql);
                $info[daohuoyijie]=$row[0]['ZMY'];
                //省店库存
                $sql="Select Sum(kccs) kccs,Sum(kcmy) kcmy From t_kcsl_sd Where ywbmbh ='010001' And ywlx='JC'";
                $row=$ora->query($sql);
                $info['shengdiankucun']=$row[0]['KCMY'];
                //省店毛利率
                $sql="Select Sum(ll)/Sum(zmy) MLL From (Select Sum(zsy-cbj)As ll,sum(zmy)As zmy From t_Fhhz_Sd Where ywbmbh='010001' And ywlx='JC' And ".
                    "dbrq>=Date'$start' And dbrq<=Date'$end' Union All Select Sum(zsy-cbj)As ll,sum(zmy) As zmy From t_Thhz_Sd Where ywbmbh='010001' And ywlx='JC' And ".
                    "dbrq>=Date'$start' And dbrq<=Date'$end') t";
                $row=$ora->query($sql);
                $info['shengdianmaolilv']=$row[0]['MLL'];
                setcache("shengdian_jiaocai_".$start.'_'.$end,$info);
            }
            $jingjinhuo=array();
            $jingfahuo=array();
            $fahuoyijie=array();
            $daohuohuoyijie=array();
            $shengdiankucun=array();
            $shengdianmaolilv=array();
            $yewu=array();


                $yewu[]='河北省店教材科';
                $jingjinhuo['河北省店教材科']=$info[jingjinhuo];
                $jingfahuo['河北省店教材科']=$info[jingfahuo];
                $daohuoyijie['河北省店教材科']=$info[daohuoyijie];
                $fahuoyijie['河北省店教材科']=$info[fahuoyijie];
                $shengdiankucun['河北省店教材科']=$info[shengdiankucun];
                $shengdianmaolilv['河北省店教材科']=$info[shengdianmaolilv];

            //var_dump($info);
            include $this->admin_tpl('show_shengdian_yewugongsi');
            die;

        }elseif($yewugongsi==2){
            
            $yewugongsiname='连锁公司';
            $info=getcache("shengdian_liansuo_".$start.'_'.$end);
            if(empty($info)){
            //净发货
				$sql="Select ywbmbh,sum(zcs),trim((SELECT dm FROM T_ZCZM_YWBM WHERE dh = ywbmbh)) BMMC,Sum(zmy) zmy,Sum(zsy) From t_Fhhz_Sd Where ywbmbh in('000001','000002','000003','000004','000005') And ywlx='LS' And ".
                    "dbrq>=Date'$start' And dbrq<=Date'$end' group by ywbmbh";
                $row=$ora->query($sql);
                $info[jingfahuo]=$row;


                //净进货
				$sql="Select ywbmbh,trim((SELECT dm FROM T_ZCZM_YWBM WHERE dh = ywbmbh)) BMMC,sum(zcs),Sum(zmy) zmy,Sum(zsy) From t_Dhhz_Sd Where ywbmbh in('000001','000002','000003','000004','000005') And ywlx='LS' And ".
                "dbrq>=Date'$start' And dbrq<=Date'$end' group by ywbmbh";
                

                $row=$ora->query($sql);
                $info[jingjinhuo]=$row;

                //向下收款
                $sql="Select Ywbmbh,( select trim(dm) from T_Zczm_Ywbm where dh= T.Ywbmbh) As BMMC, Sum(Fhmy+thmy) as zmy,Sum(Fhsy+Thsy)as zsy,sum(Fhcb+Thcb)as zcb From t_fhjshz t Where ywbmbh in('000001','000002','000003','000004','000005') And ywlx='LS'  And jsrq >=Date'$start' And jsrq <= date'$end' group by Ywbmbh";
                $row=$ora->query($sql);

                $info[fahuoyijie]=$row;


                //向上收款
                $sql="Select ywbmbh,trim((SELECT dm FROM T_ZCZM_YWBM WHERE dh = ywbmbh)) BMMC,Sum(zmy) zmy,Sum(zsy) From t_Hyjs_Sd Where ywbmbh in('000001','000002','000003','000004','000005') And ywlx='LS' And ".
                    "jsrq>=Date'$start' And jsrq <=Date'$end' group by ywbmbh";
                $row=$ora->query($sql);
                $info[daohuoyijie]=$row;
                //毛利率
                $sql="Select ywbmbh,trim((SELECT dm FROM T_ZCZM_YWBM WHERE dh = ywbmbh)) BMMC,Sum(ll)/Sum(zmy) mll From (Select ywbmbh,Sum(zsy-cbj)As ll,sum(zmy)As zmy From t_Fhhz_Sd Where ywbmbh in('000001','000002','000003','000004','000005') And ywlx='LS' And ".
                    "dbrq>=Date'$start' And dbrq<=Date'$end' Group By ywbmbh Union All Select ywbmbh,Sum(zsy-cbj)As ll,sum(zmy) As zmy From t_Thhz_Sd Where ywbmbh in('000001','000002','000003','000004','000005') And ywlx='LS' And ".
                    "dbrq>=Date'$start' And dbrq<=Date'$end' Group By ywbmbh) t group by ywbmbh";
                $row=$ora->query($sql);

                $info[shengdianmaolilv]=$row;

                //省店库存
                $sql="Select ywbmbh,trim((SELECT dm FROM T_ZCZM_YWBM WHERE dh = ywbmbh)) BMMC,Sum(kccs) kccs,Sum(kcmy) kcmy From t_kcsl_sd Where ywbmbh in('000001','000002','000003','000004','000005') And ywlx='LS' group by ywbmbh";
                $row=$ora->query($sql);
                $info[shengdiankucun]=$row;

                setcache("shengdian_liansuo_".$start.'_'.$end,$info);
            }
            $jingjinhuo=array();
            $jingfahuo=array();
            $fahuoyijie=array();
            $daohuohuoyijie=array();
            $shengdiankucun=array();
            $shengdianmaolilv=array();
            $yewu=array('一般图书','市场拓展','一般音像','文化用品','农家书屋');

            foreach($yewu as $k=>$v){
                $jingjinhuo[$info[jingjinhuo][$k]['BMMC']]=$info[jingjinhuo][$k]['ZMY'];
                $jingfahuo[$info[jingfahuo][$k]['BMMC']]=$info[jingfahuo][$k]['ZMY'];
                $daohuoyijie[$info[daohuoyijie][$k]['BMMC']]=$info[daohuoyijie][$k]['ZMY'];
                $fahuoyijie[$info[fahuoyijie][$k]['BMMC']]=$info[fahuoyijie][$k]['ZMY'];
                $shengdiankucun[$info[shengdiankucun][$k]['BMMC']]=$info[shengdiankucun][$k]['KCMY'];
                $shengdianmaolilv[$info[shengdianmaolilv][$k]['BMMC']]=$info[shengdianmaolilv][$k]['MLL'];
            }

            include $this->admin_tpl('show_shengdian_yewugongsi');
            die;
        }elseif($yewugongsi==3){
            $yewugongsiname='数字公司';
            $info=getcache("shengdian_shuzi_".$start.'_'.$end);
            if(empty($info)){
                //数字公司
                //净发货
                //$sql="Select sum(zcs),Sum(zmy) zmy,Sum(zsy) From t_Dhhz_Sd Where ywbmbh ='000009' And ywlx='LS' And dbrq>=Date'$start' And dbrq<Date'$end'";
                $sql="Select sum(zcs),Sum(zmy) zmy,Sum(zsy) From t_Fhhz_Sd Where ywbmbh ='000009' And ywlx='LS' And dbrq>=Date'$start' And dbrq<=Date'$end'";
				$row=$ora->query($sql);
                $info['jingfahuo']=$row[0]['ZMY'];
                //净进货

                //$sql="Select sum(zcs),Sum(zmy) zmy,Sum(zsy) From t_Fhhz_Sd Where ywbmbh ='000009' And ywlx='LS' And dbrq>=Date'$start' And dbrq<Date'$end'";
                $sql="Select sum(zcs),Sum(zmy) zmy,Sum(zsy) From t_Dhhz_Sd Where ywbmbh ='000009' And ywlx='LS' And dbrq>=Date'$start' And dbrq<=Date'$end'";
				$row=$ora->query($sql);
                $info['jingjinhuo']=$row[0]['ZMY'];

                //向下结算
                $sql="Select  Sum(Fhmy+thmy) as zmy,Sum(Fhsy+Thsy)as zsy,sum(Fhcb+Thcb)as zcb From t_fhjshz t Where ywbmbh = '000009' And ywlx='LS'  And jsrq >=Date'$start' And jsrq <= date'$end'";
                $row=$ora->query($sql);
                $info['fahuoyijie']=$row[0]['ZMY'];

                //向上结算

                $sql="Select Sum(zmy) ZMY,Sum(zsy) From t_Hyjs_Sd Where ywbmbh ='000009' And ywlx='LS' And jsrq >=Date'$start' And jsrq <=Date'$end'";
                $row=$ora->query($sql);
                $info['daohuoyijie']=$row[0]['ZMY'];


                //毛利率

                $sql="Select Sum(ll)/Sum(zmy) MLL From (Select Sum(zsy-cbj)As ll,sum(zmy)As zmy From t_Fhhz_Sd Where ywbmbh ='000009' And ywlx='LS' And ".
                    "dbrq>=Date'$start' And dbrq<=Date'$end' Union All Select Sum(zsy-cbj)As ll,sum(zmy) As zmy From t_Thhz_Sd Where ywbmbh ='000009' And ywlx='LS' And ".
                    "dbrq>=Date'$start' And dbrq<=Date'$end') t";

                $row=$ora->query($sql);
                $info['shengdianmaolilv']=$row[0]['MLL'];
                //省店库存
                $sql="Select Sum(kccs) kccs,Sum(kcmy) kcmy From t_kcsl_sd Where ywbmbh ='000009' And ywlx='LS'";
                $row=$ora->query($sql);
                $info['shengdiankucun']=$row[0]['KCMY'];
                setcache("shengdian_shuzi_".$start.'_'.$end,$info);
            }
            $jingjinhuo=array();
            $jingfahuo=array();
            $fahuoyijie=array();
            $daohuohuoyijie=array();
            $shengdiankucun=array();
            $shengdianmaolilv=array();
            $yewu=array();

            $yewu[]='数字教育';
            $jingjinhuo['数字教育']=$info[jingjinhuo];
            $jingfahuo['数字教育']=$info[jingfahuo];
            $daohuoyijie['数字教育']=$info[daohuoyijie];
            $fahuoyijie['数字教育']=$info[fahuoyijie];
            $shengdiankucun['数字教育']=$info[shengdiankucun];
            $shengdianmaolilv['数字教育']=$info[shengdianmaolilv];
            //var_dump($info);
            include $this->admin_tpl('show_shengdian_yewugongsi');
            die;
        }elseif($yewugongsi==4){
            
            $yewugongsiname='华锐公司';
            $info=getcache("shengdian_huarui_".$start.'_'.$end);
            if(empty($info)){
            //净发货
				$sql="Select ywbmbh,sum(zcs),trim((SELECT dm FROM T_ZCZM_YWBM WHERE dh = ywbmbh)) BMMC,Sum(zmy) zmy,Sum(zsy) From t_Fhhz_Sd Where ywbmbh in('000001','000002','000003','000004','000005') And ywlx='LS' And ".
                    "dbrq>=Date'$start' And dbrq<=Date'$end' group by ywbmbh";
                $row=$ora->query($sql);
                $info[jingfahuo]=$row;


                //净进货
				$sql="Select ywbmbh,trim((SELECT dm FROM T_ZCZM_YWBM WHERE dh = ywbmbh)) BMMC,sum(zcs),Sum(zmy) zmy,Sum(zsy) From t_Dhhz_Sd Where ywbmbh in('000001','000002','000003','000004','000005') And ywlx='LS' And ".
                "dbrq>=Date'$start' And dbrq<=Date'$end' group by ywbmbh";
                

                $row=$ora->query($sql);
                $info[jingjinhuo]=$row;

                //向下收款
                $sql="Select Ywbmbh,( select trim(dm) from T_Zczm_Ywbm where dh= T.Ywbmbh) As BMMC, Sum(Fhmy+thmy) as zmy,Sum(Fhsy+Thsy)as zsy,sum(Fhcb+Thcb)as zcb From t_fhjshz t Where ywbmbh in('000001','000002','000003','000004','000005') And ywlx='LS'  And jsrq >=Date'$start' And jsrq <= date'$end' group by Ywbmbh";
                $row=$ora->query($sql);

                $info[fahuoyijie]=$row;


                //向上收款
                $sql="Select ywbmbh,trim((SELECT dm FROM T_ZCZM_YWBM WHERE dh = ywbmbh)) BMMC,Sum(zmy) zmy,Sum(zsy) From t_Hyjs_Sd Where ywbmbh in('000001','000002','000003','000004','000005') And ywlx='LS' And ".
                    "jsrq>=Date'$start' And jsrq <=Date'$end' group by ywbmbh";
                $row=$ora->query($sql);
                $info[daohuoyijie]=$row;
                //毛利率
                $sql="Select ywbmbh,trim((SELECT dm FROM T_ZCZM_YWBM WHERE dh = ywbmbh)) BMMC,Sum(ll)/Sum(zmy) mll From (Select ywbmbh,Sum(zsy-cbj)As ll,sum(zmy)As zmy From t_Fhhz_Sd Where ywbmbh in('000001','000002','000003','000004','000005') And ywlx='LS' And ".
                    "dbrq>=Date'$start' And dbrq<=Date'$end' Group By ywbmbh Union All Select ywbmbh,Sum(zsy-cbj)As ll,sum(zmy) As zmy From t_Thhz_Sd Where ywbmbh in('000001','000002','000003','000004','000005') And ywlx='LS' And ".
                    "dbrq>=Date'$start' And dbrq<=Date'$end' Group By ywbmbh) t group by ywbmbh";
                $row=$ora->query($sql);

                $info[shengdianmaolilv]=$row;

                //省店库存
                $sql="Select ywbmbh,trim((SELECT dm FROM T_ZCZM_YWBM WHERE dh = ywbmbh)) BMMC,Sum(kccs) kccs,Sum(kcmy) kcmy From t_kcsl_sd Where ywbmbh in('000001','000002','000003','000004','000005') And ywlx='LS' group by ywbmbh";
                $row=$ora->query($sql);
                $info[shengdiankucun]=$row;

                setcache("shengdian_huarui_".$start.'_'.$end,$info);
            }
            $jingjinhuo=array();
            $jingfahuo=array();
            $fahuoyijie=array();
            $daohuohuoyijie=array();
            $shengdiankucun=array();
            $shengdianmaolilv=array();
            $yewu=array('一般图书','市场拓展','一般音像','文化用品','农家书屋');

            foreach($yewu as $k=>$v){
                $jingjinhuo[$info[jingjinhuo][$k]['BMMC']]=$info[jingjinhuo][$k]['ZMY'];
                $jingfahuo[$info[jingfahuo][$k]['BMMC']]=$info[jingfahuo][$k]['ZMY'];
                $daohuoyijie[$info[daohuoyijie][$k]['BMMC']]=$info[daohuoyijie][$k]['ZMY'];
                $fahuoyijie[$info[fahuoyijie][$k]['BMMC']]=$info[fahuoyijie][$k]['ZMY'];
                $shengdiankucun[$info[shengdiankucun][$k]['BMMC']]=$info[shengdiankucun][$k]['KCMY'];
                $shengdianmaolilv[$info[shengdianmaolilv][$k]['BMMC']]=$info[shengdianmaolilv][$k]['MLL'];
            }

            include $this->admin_tpl('show_shengdian_yewugongsi');
            die;
        }
        $info=getcache("shengdian_".$start.'_'.$end);
		//所有公司
        if(empty($info)){
			//营销公司 
            //净进货


            $sql="select sum(m.zcs) zcs,sum(m.zmy) zmy,sum(m.zsy) zsy from (Select sum(zcs) as zcs,Sum(zmy) as zmy,Sum(zsy) as zsy From t_Dhhz_Sd Where ywbmbh<>'010001' And ywlx='JC' And ".
                "dbrq>=Date'$start' And dbrq<=Date'$end' union all Select sum(zcs) as zcs,Sum(zmy) as zmy,Sum(zsy) as zsy From t_Dhhz_Sd Where ywbmbh = '000010' And ywlx='LS' And ".
                "dbrq>=Date'$start' And dbrq<=Date'$end') m";
            $row=$ora->query($sql);
            $info['yingxiao'][jingjinhuo]=$row[0]['ZMY'];
            //净发货
            $sql="select sum(m.zcs) zcs,sum(m.zmy) zmy,sum(m.zsy) zsy from (Select sum(zcs) as zcs,Sum(zmy) as zmy,Sum(zsy) as zsy From t_Fhhz_Sd Where ywbmbh<>'010001' And ywlx='JC'  And ".
                "dbrq>=Date'$start' And dbrq<=Date'$end' union all Select sum(zcs) as zcs,Sum(zmy) as zmy,Sum(zsy) as zsy From t_Fhhz_Sd Where ywbmbh='000010' And ywlx='LS'  And ".
                "dbrq>=Date'$start' And dbrq<=Date'$end') m";
            $row=$ora->query($sql);
            $info['yingxiao'][jingfahuo]=$row[0]['ZMY'];
            //发货已结---收款
            $sql="select sum(m.zmy) zmy,sum(m.zsy) zsy,sum(m.zcb) zcb from (Select  Sum(Fhmy+thmy) as zmy,Sum(Fhsy+Thsy)as zsy,sum(Fhcb+Thcb)as zcb From t_fhjshz t Where ywbmbh<>'010001' And ywlx='JC'  And ".
                "jsrq >=Date'$start' And jsrq <= Date'$end' union all Select  Sum(Fhmy+thmy) as zmy,Sum(Fhsy+Thsy)as zsy,sum(Fhcb+Thcb)as zcb From t_fhjshz t Where ywbmbh='000010' And ywlx='LS'  And ".
                "jsrq >=Date'$start' And jsrq <= Date'$end') m";
            $row=$ora->query($sql);
            $info['yingxiao']['fahuoyijie']=$row[0]['ZMY'];

            //到货已结---付款

            $sql="select sum(m.zmy) zmy,sum(m.zsy) zsy from (Select Sum(zmy) as zmy,Sum(zsy) as zsy From t_Hyjs_Sd Where ywbmbh<>'010001' And ywlx='JC'  And ".
                "jsrq >=Date'$start' And jsrq <=Date'$end' union all Select Sum(zmy) as zmy,Sum(zsy) as zsy From t_Hyjs_Sd Where ywbmbh ='000010' And ywlx='LS'  And ".
                "jsrq >=Date'$start' And jsrq <=Date'$end') m";
            $row=$ora->query($sql);
            $info['yingxiao']['daohuoyijie']=$row[0]['ZMY'];
            //省店库存
            //$sql="Select sum(kccs) kccs from t_kcsl t Where ywbmbh!='010001'";
            $sql="select sum(m.kccs) kccs,sum(M.Kcmy) KCMY from (Select Sum(kccs) as kccs,Sum(kcmy) as kcmy From t_kcsl_sd Where ywbmbh<>'010001' And ywlx='JF' ".
                "union all Select Sum(kccs) as kccs,Sum(kcmy) as kcmy From t_kcsl_sd Where ywbmbh = '000010' And ywlx='LS') m";
            $row=$ora->query($sql);
            $info['yingxiao']['shengdiankucun']=$row[0]['KCMY'];
            //省店毛利率

            $sql="Select Sum(ll)/Sum(zmy) MLL From (Select Sum(zsy-cbj)As ll,sum(zmy)As zmy From t_Fhhz_Sd  Where ywbmbh<>'010001' And ywlx='JC'  And ".
                "dbrq>=Date'$start' And dbrq<=Date'$end' Union All Select Sum(zsy-cbj)As ll,sum(zmy) As zmy From t_Thhz_Sd Where  ywbmbh<>'010001' And ywlx='JC'  And ".
                "dbrq>=Date'$start' And dbrq<=Date'$end' union all Select Sum(zsy-cbj)As ll,sum(zmy)As zmy From t_Fhhz_Sd  Where ywbmbh='000010' And ywlx='LS'  And ".
                "dbrq>=Date'$start' And dbrq<=Date'$end' Union All Select Sum(zsy-cbj)As ll,sum(zmy) As zmy From t_Thhz_Sd Where  ywbmbh ='000010' And ywlx='LS'  ".
                "And dbrq>=Date'2016-1-1' And dbrq<=Date'$end') t ";
            $row=$ora->query($sql);

            $info['yingxiao']['shengdianmaolilv']=$row[0]['MLL'];

            //教材公司
            //净进货
            $sql="Select sum(zcs),Sum(zmy) zmy,Sum(zsy) From t_Dhhz_Sd Where ywbmbh='010001' And ywlx='JC' And dbrq>=Date'$start' And dbrq<=Date'$end'";
            $row=$ora->query($sql);
            $info['jiaocai'][jingjinhuo]=$row[0]['ZMY'];
            //净发货

            $sql="Select sum(zcs),Sum(zmy) zmy,Sum(zsy) From t_Fhhz_Sd Where ywbmbh='010001' And ywlx='JC' And dbrq>=Date'$start' And dbrq<=Date'$end'";
            $row=$ora->query($sql);
            $info['jiaocai'][jingfahuo]=$row[0]['ZMY'];
            //发货已结
            $sql="Select  Sum(Fhmy+thmy) as zmy,Sum(Fhsy+Thsy)as zsy,sum(Fhcb+Thcb)as zcb From t_fhjshz t Where ywbmbh='010001' And ywlx='JC'  And jsrq >=Date'$start' And jsrq <= date'$end'";
            $row=$ora->query($sql);
            $info['jiaocai'][fahuoyijie]=$row[0]['ZMY'];
            //到货已结

            $sql="Select Sum(zmy) zmy,Sum(zsy) From t_Hyjs_Sd Where ywbmbh='010001' And ywlx='JC' And jsrq >=Date'$start' And jsrq <=Date'$end'";
            $row=$ora->query($sql);
            $info['jiaocai'][daohuoyijie]=$row[0]['ZMY'];
            //省店库存
            //$sql="Select sum(kccs) kccs from t_kcsl t Where ywbmbh='010001'";
            $sql="Select Sum(kccs) kccs,Sum(kcmy) kcmy From t_kcsl_sd Where ywbmbh='010001' And ywlx='JC'";
            $row=$ora->query($sql);
            $info['jiaocai']['shengdiankucun']=$row[0]['KCMY'];
            //省店毛利率
            $sql="Select Sum(ll)/Sum(zmy) MLL From (Select Sum(zsy-cbj)As ll,sum(zmy)As zmy From t_Fhhz_Sd Where ywbmbh='010001' And ywlx='JC' And ".
                "dbrq>=Date'$start' And dbrq<=Date'$end' Union All Select Sum(zsy-cbj)As ll,sum(zmy) As zmy From t_Thhz_Sd Where ywbmbh='010001' And ywlx='JC' And ".
                "dbrq>=Date'$start' And dbrq<=Date'$end') t";
            $row=$ora->query($sql);
            $info['jiaocai']['shengdianmaolilv']=$row[0]['MLL'];

            //连锁公司
            //净发货
            $sql="Select sum(zcs),Sum(zmy) zmy,Sum(zsy) From t_Dhhz_Sd Where ywbmbh in('000001','000002','000003','000004','000005') And ywlx='LS' And dbrq>=Date'$start' And dbrq<=Date'$end'";
            $row=$ora->query($sql);
            $info['liansuo']['jingfahuo']=$row[0]['ZMY'];
            //净进货
            $sql="Select sum(zcs),Sum(zmy) ZMY,Sum(zsy) From t_Fhhz_Sd Where ywbmbh in('000001','000002','000003','000004','000005') And ywlx='LS' And dbrq>=Date'$start' And dbrq<=Date'$end'";

            $row=$ora->query($sql);
            $info['liansuo']['jingjinhuo']=$row[0]['ZMY'];

            //发货已经结
            $sql="Select  Sum(Fhmy+thmy) as zmy,Sum(Fhsy+Thsy)as zsy,sum(Fhcb+Thcb)as zcb From t_fhjshz t Where ywbmbh in('000001','000002','000003','000004','000005') And ywlx='LS'  And jsrq >=Date'$start' And jsrq <= date'$end'";
            $row=$ora->query($sql);
            $info['liansuo']['fahuoyijie']=$row[0]['ZMY'];

            //到货已经结
            $sql="Select Sum(zmy) ZMY,Sum(zsy) From t_Hyjs_Sd Where ywbmbh in('000001','000002','000003','000004','000005') And ywlx='LS' And jsrq>=Date'$start' And jsrq <=Date'$end'";

            $row=$ora->query($sql);
            $info['liansuo']['daohuoyijie']=$row[0]['ZMY'];

            //毛利率
            $sql="Select Sum(ll)/Sum(zmy) mll From (Select Sum(zsy-cbj)As ll,sum(zmy)As zmy From t_Fhhz_Sd Where ywbmbh in('000001','000002','000003','000004','000005') And ywlx='LS' And ".
                "dbrq>=Date'$start' And dbrq<=Date'$end' Union All Select Sum(zsy-cbj)As ll,sum(zmy) As zmy From t_Thhz_Sd Where ywbmbh in('000001','000002','000003','000004','000005') And ywlx='LS' And ".
                "dbrq>=Date'$start' And dbrq<=Date'$end') t";

            $row=$ora->query($sql);
            $info['liansuo']['shengdianmaolilv']=$row[0]['MLL'];
            //省店库存
            $sql="Select Sum(kccs) kccs,Sum(kcmy) kcmy From t_kcsl_sd Where ywbmbh in('000001','000002','000003','000004','000005') And ywlx='LS'";
            $row=$ora->query($sql);
            $info['liansuo']['shengdiankucun']=$row[0]['KCMY'];
            //数字公司
            //净发货
            $sql="Select sum(zcs),Sum(zmy) zmy,Sum(zsy) From t_Dhhz_Sd Where ywbmbh ='000009' And ywlx='LS' And dbrq>=Date'$start' And dbrq<=Date'$end'";
            $row=$ora->query($sql);
            $info['shuzi']['jingfahuo']=$row[0]['ZMY'];
            //净进货
            $sql="Select sum(zcs),Sum(zmy) zmy,Sum(zsy) From t_Fhhz_Sd Where ywbmbh ='000009' And ywlx='LS' And dbrq>=Date'$start' And dbrq<=Date'$end'";


            $row=$ora->query($sql);
            $info['shuzi']['jingjinhuo']=$row[0]['ZMY'];

            //向下结算
            $sql="Select  Sum(Fhmy+thmy) as zmy,Sum(Fhsy+Thsy)as zsy,sum(Fhcb+Thcb)as zcb From t_fhjshz t Where ywbmbh = '000009' And ywlx='LS'  And jsrq >=Date'$start' And jsrq <= date'$end'";
            $row=$ora->query($sql);
            $info['shuzi']['fahuoyijie']=$row[0]['ZMY'];

            //向上结算
            $sql="Select Sum(zmy) ZMY,Sum(zsy) From t_Hyjs_Sd Where ywbmbh ='000009' And ywlx='LS' And jsrq >=Date'$start' And jsrq <=Date'$end'";

            $row=$ora->query($sql);
            $info['shuzi']['daohuoyijie']=$row[0]['ZMY'];


            //毛利率
            $sql="Select Sum(ll)/Sum(zmy) MLL From (Select Sum(zsy-cbj)As ll,sum(zmy)As zmy From t_Fhhz_Sd Where ywbmbh ='000009' And ywlx='LS' And ".
                "dbrq>=Date'$start' And dbrq<=Date'$end' Union All Select Sum(zsy-cbj)As ll,sum(zmy) As zmy From t_Thhz_Sd Where ywbmbh ='000009' And ywlx='LS' And ".
                "dbrq>=Date'$start' And dbrq<=Date'$end') t";


            $row=$ora->query($sql);
            $info['shuzi']['shengdianmaolilv']=$row[0]['MLL'];
            //省店库存
            $sql="Select Sum(kccs) kccs,Sum(kcmy) kcmy From t_kcsl_sd Where ywbmbh ='000009' And ywlx='LS'";


            $row=$ora->query($sql);
            $info['shuzi']['shengdiankucun']=$row[0]['KCMY'];

            setcache("shengdian_".$start.'_'.$end,$info);
        }

        include $this->admin_tpl('show_shengdian');

    }
    public function jicengdian () {
        set_time_limit(0);
        $userid = $_SESSION['userid'];
        $admin_username = param::get_cookie('admin_username'); //echo $admin_username;exit;//获取用户名
        $roles = getcache('role','commons'); //var_dump($roles);exit;//所有的角色
        $rolename = $roles[$_SESSION['roleid']];
        $site = pc_base::load_app_class('sites');
        $sitelist = $site->get_list($_SESSION['roleid']);//var_dump($sitelist);exit;
        $currentsite = $this->get_siteinfo(param::get_cookie('siteid'));
        /*管理员收藏栏*/
        $adminpanel = $this->panel_db->select(array('userid'=>$userid), "*",20 , 'datetime');//var_dump($adminpanel);exit;

        $site_model = param::get_cookie('site_model');
        $title='基层店经营情况';
        $this->role_provdb = pc_base::load_model('admin_role_priv_model');
        $admin_menu = $this->role_provdb->select(array('roleid'=>$_SESSION['roleid'],'m'=>'admin','c'=>'index'),'a');//var_dump($admin_menu);exit;
        $new=array();
        foreach($admin_menu as $k=>$v){
            $new[$v[a]]=1;
        }
		//var_dump($new);exit;
        extract($new);

//        $host='172.30.153.20';
//        $ip='172.30.153.20/xhsddb';
//        $port='1521';
//        $user= 'dbsl';
//        $pass= 'dbsl';
//        $charset='utf8';
        $host='172.30.153.63';
        $ip='172.30.153.63/XHSDDB';
        $port='1521';
        $user= 'dbjczc';
        $pass= 'dbjczc';
        $charset='utf8';
        pc_base::load_app_class('oracle_admin','admin',0);//数据库
        $ora=new oracle_admin($user,$pass,$ip,$charset);

//        $ip='172.30.153.61/XHSDDB';//教材教辅的数据库
//        $port='1521';
//        $user= 'HBJCYW';
//        $pass= 'HBJCYW';
//        $charset='utf8';
//        pc_base::load_app_class('oracle_admin','admin',0);//数据库
//        $ora=new oracle_admin($user,$pass,$ip,$charset);
//
//        $ip='172.30.153.61/XHSDDB';//教材教辅的数据库
//        $port='1521';
//        $user= 'HBJCDJC';
//        $pass= 'HBJCDJC';
//        $charset='utf8';
//        pc_base::load_app_class('oracle_admin','admin',0);//数据库
//        $ora=new oracle_admin($user,$pass,$ip,$charset);

        //$start=isset($_REQUEST['start'])?$_REQUEST['start']:date('Y',time()).'-1-1';
        $start=isset($_REQUEST['start'])?$_REQUEST['start']:date('Y-m-d',time()-24*60*60*7);
        $end=isset($_REQUEST['end'])?$_REQUEST['end']:date('Y-m-d',time()-24*60*60);

        $yewuleixing=isset($_REQUEST['yewuleixing'])?$_REQUEST['yewuleixing']:-1;


        $category_privdb = pc_base::load_model('category_priv_model');
        $admin_category = $category_privdb->get_one("`roleid`=$_SESSION[roleid] and `is_admin`='1' and `siteid`='1'");//var_dump($admin_category);exit;
        $categorydb = pc_base::load_model('category_model');
        $category = $categorydb->get_one("`catid`=$admin_category[catid]");//var_dump($category );exit;
        $jibie=sizeof(explode(',',$category['arrparentid']));//echo $jibie;exit; // 'arrchildid' => string '9,22,42,43,44,,55,56,,25,64,
        if($jibie==2){
            $_REQUEST['diqu']=$category['catname']; //echo $_REQUEST['diqu'];exit;
            $xs = $categorydb->select(array('parentid'=>$admin_category[catid]),'catname');//var_dump($xs);exit;//石家庄以及他的二级
        }elseif(isset($_REQUEST[diqu])){
            $catid=$categorydb->select(array('parentid'=>6,'catname'=>"$_REQUEST[diqu]"),'catid');
            $xs = $categorydb->select(array('parentid'=>$catid[0][catid]),'catname');

        }
        $diquarr=array('石家庄','唐山','秦皇岛','邯郸','邢台','保定','张家口','承德','沧州','廊坊','衡水','辛集','定州','河北省店');
        $diqure=$diqu=isset($_REQUEST['diqu'])?$_REQUEST['diqu']:-1;
        if($diqu!=-1&&$diqu!=-2){
            $diqu_id=array_keys($diquarr,$diqu);//var_dump($diqure);exit;//查找$diqu在$diquarr的键值
            //$diqu.='地区';
        }elseif($diqu==-2){
            $xs = $categorydb->select('arrparentid like "0,6,%" and child =1','catname');
        }

        if($yewuleixing==0){
            $yewulename='连锁业务';
            if($diqu==-2){
                $info=getcache("jicengdian_liansuo_".$diqu."_".$start.'_'.$end);

                if(empty($info)){
                    //连锁店净进货
                    $sql="SELECT trim(dm) xs,SUM (ZCS) ZCS,SUM (ZMY) ZMY,SUM (ZSY) ZSY FROM(SELECT YWBMBH,(SELECT DM_FR FROM	t_zczm_dm WHERE	dh = T .ywbmbh) AS dm,(SELECT dqmc FROM t_zczm_dm WHERE dh = T .ywbmbh) AS DQmc,ZCS,ZMY,ZSY	FROM ".
                        "t_dhhz_jcd T WHERE ywlx = 'LS' AND (GHDWH <> '000008' and GHDWH <> '000009' and GHDWH <> '000010' ) AND DBRQ >= DATE '$start' AND DBRQ <= DATE '$end' ) WHERE dqmc is not null GROUP BY dm";
                    $row=$ora->query($sql);
                    $info['jingjinghuo']=$row;

                    $sql="SELECT trim(dm) xs,SUM (ZCS),SUM (ZMY) ZMY,SUM (ZSY) ZSY FROM(SELECT(SELECT dm_fr FROM t_zczm_dm WHERE	dh = T .ywbmbh) AS dm,(	SELECT	dqmc FROM t_zczm_dm WHERE dh = T .ywbmbh) AS DQmc,ZCS,ZMY,ZSY,zcb FROM t_Xsls_Lsd T	WHERE (	khbh <> '000008' and khbh <> '000009' and khbh <> '000010'	) ".
                        "AND DBRQ >= DATE '$start' AND DBRQ <= DATE '$end'	) WHERE	dqmc is not null GROUP BY dm";
                    $row=$ora->query($sql);
                    $info['jingxiaoshou']=$row;

                    //进货结算连锁店
                    $sql="Select trim((Select min(dm_fr) From t_zczm_dm Where dh_fr=t.dh))As xs,Sum(CFJE) ZSY,LX From t_zczm_hyjs_jcd t Where lx='LS' and fprq >= DATE'$start' and fprq <= DATE'$end' and dqmc is not null Group By dm,DQBH_GG,LX,dh";
                    $row=$ora->query($sql);
                    $info['jinhuojiesuan']=$row;

                    //联营销售
                    $sql="Select trim(dm) xs,Sum(ZCS) ZCS,Sum(ZMY) ZMY From (Select (Select dm_fr From t_zczm_dm Where dh=t.ywbmbh) As dm ,(Select dqmc From t_zczm_dm Where dh=t.ywbmbh)As DQmc,ZCS,ZMY From t_zlXsls_Lsd T Where ".
                        "DBRQ>=Date'$start' And DBRQ<=DATE '$end' ) Where dqmc is not null Group By dm ";
					//var_dump($sql);
                    $row=$ora->query($sql);
                    $info['lianyingxiaoshou']=$row;


                    //销售结算
                    $sql="select m.dqbh,m.dqmc,trim(m.dm) xs,sum(m.zmy) as zmy,sum(m.zsy) as zsy,sum(m.zcb)as zcb from (Select dh,(Select min(dm_fr) From t_zczm_dm Where dh=t.dh) as dm,".
                        "DQBH_GG as dqbh,(Select Min(dqmc) From t_zczm_dm Where dh=t.dh) as dqmc,sum(zmy) as zmy,sum(zsy) as zsy,sum(zcb) as zcb,LX From t_zczm_xsjs_jcd t Where lx='LS' ".
                        "and fhrq>=date'$start' and fhrq <=date'$end' Group By dh,DQBH_GG,LX) m where m.dqmc is not null  and m.dqmc is not null group by m.dqmc,m.dqbh,m.dm";
                    $row=$ora->query($sql);
                    $info['xiaoshoujiesuan']=$row;
                    $array=array();
                    foreach($info['jingjinghuo'] as $v){
                        $array[$v['XS']]=$v['ZMY'];
                    }
                    $info['jingjinghuo']=$array;

                    $array=array();
                    foreach($info['jingxiaoshou'] as $v){
                        $array[$v['XS']]=$v['ZMY'];
                    }
                    $info['jingxiaoshou']=$array;

                    $array=array();
                    foreach($info['jinhuojiesuan'] as $v){
                        $array[$v['XS']]=$v['ZSY'];
                    }
                    $info['jinhuojiesuan']=$array;

                    $array=array();
                    foreach($info['lianyingxiaoshou'] as $v){
                        $array[$v['XS']]=$v['ZMY'];
                    }
                    $info['lianyingxiaoshou']=$array;

                    $array=array();
                    foreach($info['xiaoshoujiesuan'] as $v){
                        $array[$v['XS']]=$v['ZSY'];
                    }
                    $info['xiaoshoujiesuan']=$array;

                    setcache("jicengdian_liansuo_".$diqu."_".$start.'_'.$end,$info);
                }

                include $this->admin_tpl('show_jicengdian_quanxian');
                die;
            }else if($diqu==-1){//全省
                $info=getcache("jicengdian_liansuo_q_".$start.'_'.$end);
                if(empty($info)){
                    //连锁店净进货
                    $sql="Select DQBH,DQMC,Sum(ZCS),Sum(ZMY) ZMY,Sum(ZSY) ZSY From (Select YWBMBH,(Select dqmc From t_zczm_dm Where dh=t.ywbmbh)As DQmc,(Select dqBH_GG From t_zczm_dm Where dh=t.ywbmbh)As DQBH,ZCS,ZMY,ZSY From ".
                        "t_dhhz_jcd T Where ywlx='LS' And (GHDWH<>'000008' and GHDWH<>'000009' and GHDWH<>'000010') And DBRQ>=Date'$start' And DBRQ<=Date'$end' ) Group By DQMC,DQBH";
                    $row=$ora->query($sql);
                    $info['jingjinghuo']=$row;
                    //教材教辅净进货
                    $sql='';
                    //数据库查询结果不能用


                    //连锁店净销售

                    $sql="Select DQBH,DQMC,Sum(ZCS),Sum(ZMY) ZMY,Sum(ZSY) ZSY From (Select YWBMBH,(Select dqmc From t_zczm_dm Where dh=t.ywbmbh)As DQmc,(Select dqBH_GG From t_zczm_dm Where dh=t.ywbmbh)As DQBH,ZCS,ZMY,ZSY,zcb  From ".
                        "t_Xsls_Lsd T Where  (khbh<>'000008' and khbh<>'000009' and khbh<>'000010') And DBRQ>=Date'$start' And DBRQ<=Date'$end' ) Group By DQMC,DQBH ";
                    $row=$ora->query($sql);
                    $info['jingxiaoshou']=$row;

                    //进货结算连锁店
                    $sql="Select DQBH_GG,DQMC,Sum(CFJE) ZSY,LX From t_zczm_hyjs_jcd Where lx='LS' and fprq >= Date'$start' And fprq <= Date'$end'  Group By DQBH_GG,LX,DQMC";

                    $row=$ora->query($sql);
                    $info['jinhuojiesuan']=$row;

                    //联营销售
                    $sql="Select DQBH,DQMC,Sum(ZCS),Sum(ZMY) ZMY From (Select YWBMBH,(Select dqmc From t_zczm_dm Where dh=t.ywbmbh)As DQmc,(Select dqBH_GG From t_zczm_dm Where dh=t.ywbmbh)As DQBH,ZCS,ZMY From t_zlXsls_Lsd T Where ".
                        "DBRQ>=Date'$start' And DBRQ<=Date'$end' ) Group By DQMC,DQBH";


                    $row=$ora->query($sql);
                    $info['lianyingxiaoshou']=$row;

                    //销售结算
                    $sql="Select DQBH_GG,DQMC,Sum(zmy) zmy,sum(zsy) zsy,sum(zcb),LX From t_zczm_xsjs_jcd Where lx='LS' and fhrq >= Date'$start' And fhrq <= Date'$end' Group By DQBH_GG,LX,DQMC";
                    $row=$ora->query($sql);
                    $info['xiaoshoujiesuan']=$row;
                    $array=array();
                    foreach($info['jingjinghuo'] as $v){
                        $array[$v['DQMC']]=$v['ZMY'];
                    }
                    $info['jingjinghuo']=$array;
                    $array=array();
                    foreach($info['jingxiaoshou'] as $v){
                        $array[$v['DQMC']]=$v['ZMY'];
                    }
                    $info['jingxiaoshou']=$array;
                    $array=array();
                    foreach($info['jinhuojiesuan'] as $v){

                        $array[$v['DQMC']]=$v['ZSY'];

                    }
                    $info['jinhuojiesuan']=$array;
                    $array=array();
                    foreach($info['lianyingxiaoshou'] as $v){

                        $array[$v['DQMC']]=$v['ZMY'];

                    }
                    $info['lianyingxiaoshou']=$array;
                    $array=array();
                    foreach($info['xiaoshoujiesuan'] as $v){

                        $array[$v['DQMC']]=$v['ZSY'];

                    }
                    $info['xiaoshoujiesuan']=$array;
                    setcache("jicengdian_liansuo_q_".$start.'_'.$end,$info);

                }
                include $this->admin_tpl('show_jicengdian');
                die;
            }elseif(isset($diqu)){
				//echo $diqu.'888';exit;
                $info=getcache("jicengdian_liansuo_".$diqu_id[0]."_".$start.'_'.$end);
                if(empty($info)){
                    //连锁店净进货
                    $sql="SELECT trim(dm) xs,SUM (ZCS) ZCS,SUM (ZMY) ZMY,SUM (ZSY) ZSY FROM(SELECT YWBMBH,(SELECT max(DM_FR) FROM	t_zczm_dm WHERE	dh = T .ywbmbh) AS dm,(SELECT max(dqmc) FROM t_zczm_dm WHERE dh = T .ywbmbh) AS DQmc,ZCS,ZMY,ZSY	FROM ".
                        "t_dhhz_jcd T WHERE ywlx = 'LS' AND (GHDWH <> '000008' and GHDWH <> '000009' and GHDWH <> '000010' ) AND DBRQ >= DATE '$start' AND DBRQ <= DATE '$end' ) WHERE dqmc = '$diqure' GROUP BY dm";
                    $row=$ora->query($sql);
                    $info['jingjinghuo']=$row;

                    $sql="SELECT trim(dm) xs,SUM (ZCS),SUM (ZMY) ZMY,SUM (ZSY) ZSY FROM(SELECT(SELECT max(dm_fr) FROM t_zczm_dm WHERE	dh = T .ywbmbh) AS dm,(	SELECT	max(dqmc) FROM t_zczm_dm WHERE dh = T .ywbmbh) AS DQmc,ZCS,ZMY,ZSY,zcb FROM t_Xsls_Lsd T	WHERE (	khbh <> '000008' and khbh <> '000009' and khbh <> '000010'	) ".
                        "AND DBRQ >= DATE '$start' AND DBRQ <= DATE '$end'	) WHERE	dqmc = '$diqure'GROUP BY dm";
                    $row=$ora->query($sql);
                    $info['jingxiaoshou']=$row;

                    //进货结算连锁店
                    $sql="Select trim((Select min(dm_fr) From t_zczm_dm Where dh_fr=t.dh))As xs,Sum(CFJE) ZSY,LX From t_zczm_hyjs_jcd t Where lx='LS' and fprq >= Date'$start' And fprq <= Date'$end' and dqmc='$diqure' Group By dm,DQBH_GG,LX,dh";
                    $row=$ora->query($sql);
                    $info['jinhuojiesuan']=$row;

                    //联营销售
                    $sql="Select trim(dm) xs,Sum(ZCS) ZCS,Sum(ZMY) ZMY From (Select (Select max(dm_fr) From t_zczm_dm Where dh=t.ywbmbh) As dm ,(Select max(dqmc) From t_zczm_dm Where dh=t.ywbmbh)As DQmc,ZCS,ZMY From t_zlXsls_Lsd T Where ".
                        "DBRQ>=Date'$start' And DBRQ<=DATE '$end' ) Where dqmc='$diqure' Group By dm ";

                    $row=$ora->query($sql);
                    $info['lianyingxiaoshou']=$row;


                    //销售结算
                    $sql="select m.dqbh,m.dqmc,m.dm xs,sum(m.zmy) as zmy,sum(m.zsy) as zsy,sum(m.zcb)as zcb from". 
					"(Select dh,(Select min(trim(dm_fr)) From t_zczm_dm Where dh=t.dh) as dm,DQBH_GG as dqbh,".
					"(Select Min(dqmc) From t_zczm_dm Where dh=t.dh) as dqmc,sum(zmy) as zmy,sum(zsy) as zsy,sum(zcb) as zcb,LX From t_zczm_xsjs_jcd t Where lx='LS' and fhrq>=date'$start' and fhrq <= date'$end' Group By dh,DQBH_GG,LX) m where m.dqmc is not null  and m.dqmc='$diqure' group by m.dqmc,m.dqbh,m.dm ";
                    $row=$ora->query($sql);
                    $info['xiaoshoujiesuan']=$row;
                    $array=array();
                    foreach($info['jingjinghuo'] as $v){
                        $array[$v['XS']]=$v['ZMY'];
                    }
                    $info['jingjinghuo']=$array;
                    $array=array();
                    foreach($info['jingxiaoshou'] as $v){
                        $array[$v['XS']]=$v['ZMY'];
                    }
                    $info['jingxiaoshou']=$array;
                    $array=array();
                    foreach($info['jinhuojiesuan'] as $v){
                        $array[$v['XS']]=$v['ZSY'];
                    }
                    $info['jinhuojiesuan']=$array;
                    $array=array();
                    foreach($info['lianyingxiaoshou'] as $v){
                        $array[$v['XS']]=$v['ZMY'];
                    }
                    $info['lianyingxiaoshou']=$array;
                    $array=array();
                    foreach($info['xiaoshoujiesuan'] as $v){
                        $array[$v['XS']]=$v['ZSY'];
                    }
                    $info['xiaoshoujiesuan']=$array;
                    setcache("jicengdian_liansuo_".$diqu_id[0]."_".$start.'_'.$end,$info);

                }
                include $this->admin_tpl('show_jicengdian_shi');
                die;
            }
        }elseif($yewuleixing==1){
            $yewulename='教材业务';
            if($diqu==-2){
                $info=getcache("jicengdian_jiaocai_".$diqu."_".$start.'_'.$end);
                if(empty($info)){
                    //净进货
                    $sql="Select m.dm xs,Sum(m.ZCS) as zcs,Sum(m.ZMY) as zmy,Sum(m.ZSY)as zsy From (Select YWBMBH as dh,(Select MIN(dm_fr) From t_zczm_dm Where dh=t.ywbmbh)As dm,(Select MIN(dqmc) From t_zczm_dm Where dh=t.ywbmbh)As dqmc,(Select MIN(dqBH_GG) From t_zczm_dm Where dh=t.ywbmbh)As dqbh,ZCS,ZMY,ZSY ".
                        "From t_dhhz_jcd T Where ywlx='JC' And DBRQ>=Date'$start' And DBRQ<=Date'$end' )m  where m.dqmc is not null Group By m.dm";
                    $row=$ora->query($sql);
                    $info['jingjinghuo']=$row;

                    //净销售
                    $sql="Select m.dm xs,Sum(m.ZCS) as zcs,Sum(m.ZMY) as zmy,Sum(m.ZSY) as zsy,sum(m.zcb)as zcb From (Select YWBMBH as dh,(Select MIN(dm_fr) From t_zczm_dm Where dh=t.ywbmbh)As dm,(Select MIN(dqmc) From t_zczm_dm Where dh=t.ywbmbh)As dqmc,(Select MIN(dqBH_GG) From t_zczm_dm Where dh=t.ywbmbh)As dqbh,ZCS,ZMY,ZSY,zcb ".
                        " From t_Xsls_JCD T Where  DBRQ>=Date'$start' And DBRQ<=Date'$end' ) m where m.dqmc is not null Group By m.dm,m.dqbh ,m.dqmc";

                    $row=$ora->query($sql);
                    $info['jingxiaoshou']=$row;


                    //进货结算
                    $sql="select m.dm xs,m.dqbh,m.dqmc,sum(m.cfje) as ZSY from (Select DH,(Select MIN(dm_fr) From t_zczm_dm Where dh=t.dh)As dm,DQBH_GG as dqbh,(Select MIN(dqmc) From t_zczm_dm Where dh=t.dh)As dqmc,CFJE,LX From t_zczm_hyjs_jcd t Where lx='JC' and fprq>=Date'$start' And fprq<=Date'$end') m ".
                        " where m.dqmc is not null  group by m.dm,m.dqbh,m.dqmc";
                    
                    $row=$ora->query($sql);
                    $info['jinhuojiesuan']=$row;
                    //销售结算
                    $sql="select m.dm xs,m.dqmc ,sum(m.zmy) as zmy,sum(m.zsy) as zsy,sum(m.zcb)as zcb from (Select DH,(Select MIN(dm_fr) From t_zczm_dm Where dh=t.dh)As dm,DQBH_GG as dqbh,(Select MIN(dqmc) From t_zczm_dm Where dh=t.dh)As dqmc,zmy,zsy,zcb,LX ".
                        "From t_zczm_xsjs_jcd t Where lx='JC' and  fhrq >= Date'$start' And fhrq <= Date'$end')m  where m.dqmc is not null group by m.dm,m.dqbh,m.dqmc";

                    $row=$ora->query($sql);
                    $info['xiaoshoujiesuan']=$row;


                    //转成键值为地区的
                    $array=array();
                    foreach($info['jingjinghuo'] as $v){
                        $array[$v['XS']]=$v['ZMY'];
                    }
                    $info['jingjinghuo']=$array;
                    $array=array();
                    foreach($info['jingxiaoshou'] as $v){

                        $array[$v['XS']]=$v['ZMY'];

                    }
                    $info['jingxiaoshou']=$array;

                    $array=array();
                    foreach($info['jinhuojiesuan'] as $v){

                        $array[$v['XS']]=$v['ZSY'];

                    }
                    $info['jinhuojiesuan']=$array;
                    $array=array();
                    foreach($info['xiaoshoujiesuan'] as $v){
                        $array[$v['XS']]=$v['ZSY'];
                    }
                    $info['xiaoshoujiesuan']=$array;

                    setcache("jicengdian_jiaocai_".$diqu."_".$start.'_'.$end,$info);
                }
                include $this->admin_tpl('show_jicengdian_quanxian');
                die;
            }else if($diqu==-1){
                //教材教辅的数据库
                $info=getcache("jicengdian_jiaocai_q_".$start.'_'.$end);
                if(empty($info)){
                    //净进货
                    $sql="Select DQBH,DQMC,Sum(ZCS),Sum(ZMY) as ZMY,Sum(ZSY) ZSY From (Select YWBMBH,(Select max(dqmc) From t_zczm_dm Where dh=t.ywbmbh)As DQmc,(Select max(dqBH_GG) From t_zczm_dm Where dh=t.ywbmbh)As DQBH,ZCS,ZMY,ZSY From t_dhhz_jcd T Where ywlx='JC' And ".
                        "DBRQ>=Date'$start' And DBRQ<=Date'$end' ) Group By DQMC,DQBH";
                    $row=$ora->query($sql);
                    $info['jingjinghuo']=$row;
                    //净销售
                    $sql="Select DQBH,DQMC,Sum(ZCS),Sum(ZMY) ZMY,Sum(ZSY) ZSY From (Select YWBMBH,(Select MIN(dqmc) From t_zczm_dm Where dh=t.ywbmbh)As DQmc,(Select MIN(dqBH_GG) From t_zczm_dm Where dh=t.ywbmbh)As DQBH,ZCS,ZMY,ZSY,zcb  From t_Xsls_JCD T Where ".
                        " DBRQ>=Date'$start' And DBRQ<=Date'$end' ) Group By DQMC,DQBH";
                    $row=$ora->query($sql);
                    $info['jingxiaoshou']=$row;
                    //进货结算
                    $sql="Select DQBH_GG,dqmc,Sum(CFJE) ZSY,LX From t_zczm_hyjs_jcd Where lx='JC' and fprq >= Date'$start' And fprq <= Date'$end' Group By DQBH_GG,LX,dqmc";
                    $row=$ora->query($sql);

                    $info['jinhuojiesuan']=$row;
                    //销售结算
                    $sql="Select DQBH_GG,dqmc,Sum(zmy) zmy,sum(zsy) zsy,sum(zcb),LX From t_zczm_xsjs_jcd Where lx='JC' and fhrq >= Date'$start' And fhrq <= Date'$end' Group By DQBH_GG,LX,dqmc";

                    $row=$ora->query($sql);
                    $info['xiaoshoujiesuan']=$row;


                    $array=array();
                    foreach($info['jingjinghuo'] as $v){
                        $array[$v['DQMC']]=$v['ZMY'];
                    }
                    $info['jingjinghuo']=$array;
                    $array=array();
                    foreach($info['jingxiaoshou'] as $v){

                        $array[$v['DQMC']]=$v['ZMY'];

                    }
                    $info['jingxiaoshou']=$array;

                    $array=array();
                    foreach($info['jinhuojiesuan'] as $v){

                        $array[$v['DQMC']]=$v['ZSY'];

                    }
                    $info['jinhuojiesuan']=$array;
                    $array=array();
                    foreach($info['xiaoshoujiesuan'] as $v){

                        $array[$v['DQMC']]=$v['ZSY'];

                    }
                    $info['xiaoshoujiesuan']=$array;
                    $info['lianyingxiaoshou']='';
                    setcache("jicengdian_jiaocai_q_".$start.'_'.$end,$info);
                }

                include $this->admin_tpl('show_jicengdian');
                die;

            }elseif(isset($diqu)){

                $info=getcache("jicengdian_jiaocai_".$diqu_id[0]."_".$start.'_'.$end);

                if(empty($info)){
                    //净进货
                    $sql="Select m.dm xs,Sum(m.ZCS) as zcs,Sum(m.ZMY) as zmy,Sum(m.ZSY)as zsy From (Select YWBMBH as dh,(Select MIN(dm_fr) From t_zczm_dm Where dh=t.ywbmbh)As dm,(Select MIN(dqmc) From t_zczm_dm Where dh=t.ywbmbh)As dqmc,(Select MIN(dqBH_GG) From t_zczm_dm Where dh=t.ywbmbh)As dqbh,ZCS,ZMY,ZSY ".
                        "From t_dhhz_jcd T Where ywlx='JC' And DBRQ>=Date'$start' And DBRQ<=Date'$end' )m  where m.dqmc='$diqu' Group By m.dm";
                    $row=$ora->query($sql);
                    $info['jingjinghuo']=$row;

                    //净销售
                    $sql="Select m.dm xs,Sum(m.ZCS) as zcs,Sum(m.ZMY) as zmy,Sum(m.ZSY) as zsy,sum(m.zcb)as zcb From (Select YWBMBH as dh,(Select MIN(dm_fr) From t_zczm_dm Where dh=t.ywbmbh)As dm,(Select MIN(dqmc) From t_zczm_dm Where dh=t.ywbmbh)As dqmc,(Select MIN(dqBH_GG) From t_zczm_dm Where dh=t.ywbmbh)As dqbh,ZCS,ZMY,ZSY,zcb ".
                        " From t_Xsls_JCD T Where  DBRQ>=Date'$start' And DBRQ<=Date'$end' ) m where m.dqmc='$diqu' Group By m.dm,m.dqbh ,m.dqmc";

                    $row=$ora->query($sql);
                    $info['jingxiaoshou']=$row;


                    //进货结算
                    $sql="select m.dm xs,m.dqbh,m.dqmc,sum(m.cfje) as ZSY from (Select DH,(Select MIN(dm_fr) From t_zczm_dm Where dh=t.dh)As dm,DQBH_GG as dqbh,(Select MIN(dqmc) From t_zczm_dm Where dh=t.dh)As dqmc,CFJE,LX From t_zczm_hyjs_jcd t Where lx='JC' and fprq >= date'$start' and fprq <= date'$end') m ".
                        " where m.dqmc='$diqu'  group by m.dm,m.dqbh,m.dqmc";

                    $row=$ora->query($sql);
                    $info['jinhuojiesuan']=$row;
                    //销售结算
                    $sql="select m.dm xs,m.dqmc ,sum(m.zmy) as zmy,sum(m.zsy) as zsy,sum(m.zcb)as zcb from (Select DH,(Select MIN(dm_fr) From t_zczm_dm Where dh=t.dh)As dm,DQBH_GG as dqbh,(Select MIN(dqmc) From t_zczm_dm Where dh=t.dh)As dqmc,zmy,zsy,zcb,LX ".
                        "From t_zczm_xsjs_jcd t Where lx='JC' and fhrq >= date'$start' and fhrq <= date'$end')m  where m.dqmc='$diqu' group by m.dm,m.dqbh,m.dqmc";

                    $row=$ora->query($sql);
                    $info['xiaoshoujiesuan']=$row;


                    //转成键值为地区的
                    $array=array();
                    foreach($info['jingjinghuo'] as $v){
                        $array[$v['XS']]=$v['ZMY'];
                    }
                    $info['jingjinghuo']=$array;
                    $array=array();
                    foreach($info['jingxiaoshou'] as $v){

                        $array[$v['XS']]=$v['ZMY'];

                    }
                    $info['jingxiaoshou']=$array;

                    $array=array();
                    foreach($info['jinhuojiesuan'] as $v){

                        $array[$v['XS']]=$v['ZSY'];

                    }
                    $info['jinhuojiesuan']=$array;
                    $array=array();
                    foreach($info['xiaoshoujiesuan'] as $v){
                        $array[$v['XS']]=$v['ZSY'];
                    }
                    $info['xiaoshoujiesuan']=$array;

                    setcache("jicengdian_jiaocai_".$diqu_id[0]."_".$start.'_'.$end,$info);
                }
                include $this->admin_tpl('show_jicengdian_shi');
                die;
            }

        }

        $yewulename='基层店经营情况';
        if($diqu==-2){
            $info=getcache("jicengdian_$diqu_".$start.'_'.$end);

            if(empty($info)){
                $jicengdian_liansuo=getcache("jicengdian_liansuo_".$diqu."_".$start.'_'.$end);

                if(empty($jicengdian_liansuo)){

                    //连锁店净进货
                    $sql="Select trim(dm) xs,DQBH,DQMC,Sum(ZCS),Sum(ZMY) ZMY,Sum(ZSY) ZSY From (Select (Select dm_fr From t_zczm_dm Where dh=t.ywbmbh) As dm,(Select dqmc From t_zczm_dm Where dh=t.ywbmbh)As DQmc,".
                        "(Select dqBH_GG From t_zczm_dm Where dh=t.ywbmbh)As DQBH,ZCS,ZMY,ZSY From t_dhhz_jcd T Where ywlx='LS' And (GHDWH<>'000008' and GHDWH<>'000009' and GHDWH<>'000010') And DBRQ>=DATE '$start' And DBRQ <= DATE '$end') Where dqmc is not null Group By DQMC,DQBH,dm";
                    $row=$ora->query($sql);
                    $info['jingjinghuo']=$row;

                    $sql="SELECT trim(dm) xs,SUM (ZCS),SUM (ZMY) ZMY,SUM (ZSY) ZSY FROM(SELECT(SELECT dm_fr FROM t_zczm_dm WHERE	dh = T .ywbmbh) AS dm,(	SELECT	dqmc FROM t_zczm_dm WHERE dh = T .ywbmbh) AS DQmc,ZCS,ZMY,ZSY,zcb FROM t_Xsls_Lsd T	WHERE (	khbh <> '000008' and khbh <> '000009' and khbh <> '000010'	) ".
                        "AND DBRQ >= DATE '$start' AND DBRQ <= DATE '$end'	) WHERE	dqmc is not null GROUP BY dm";
                    $row=$ora->query($sql);
                    $info['jingxiaoshou']=$row;

                    //进货结算连锁店
                    $sql="Select trim((Select min(dm_fr) From t_zczm_dm Where dh_fr=t.dh))As xs,Sum(CFJE) ZSY,LX From t_zczm_hyjs_jcd t Where lx='LS' and fprq>=date'$start' and fprq <=date'$end' and dqmc is not null Group By dm,DQBH_GG,LX,dh";
                    $row=$ora->query($sql);
                    $info['jinhuojiesuan']=$row;

                    //联营销售
                   // $sql="Select trim(dm) xs,Sum(ZCS) ZCS,Sum(ZMY) ZMY From (Select (Select dm_fr From t_zczm_dm Where dh=t.ywbmbh) As dm ,(Select dqmc From t_zczm_dm Where dh=t.ywbmbh)As DQmc,ZCS,ZMY From t_zlXsls_Lsd T Where ".
                      //  "DBRQ>=Date'$start' And DBRQ<DATE '$end' ) Where dqmc is not null Group By dm ";
						
					$sql="Select trim(dm) xs,Sum(ZCS) ZCS,Sum(ZMY) ZMY From (Select (Select dm_fr From t_zczm_dm Where dh=t.ywbmbh) As dm ,(Select dqmc From t_zczm_dm Where dh=t.ywbmbh)As DQmc,ZCS,ZMY From t_zlXsls_Lsd T Where ".
                        "DBRQ>=Date'$start' And DBRQ<=DATE '$end' ) Where dqmc is not null Group By dm ";
                    
					//var_dump($sql);
					$row=$ora->query($sql);
                    $info['lianyingxiaoshou']=$row;


                    //销售结算
                    $sql="select m.dqbh,m.dqmc,trim(m.dm) xs,sum(m.zmy) as zmy,sum(m.zsy) as zsy,sum(m.zcb)as zcb from (Select dh,(Select min(dm_fr) From t_zczm_dm Where dh=t.dh) as dm,".
                        "DQBH_GG as dqbh,(Select Min(dqmc) From t_zczm_dm Where dh=t.dh) as dqmc,sum(zmy) as zmy,sum(zsy) as zsy,sum(zcb) as zcb,LX From t_zczm_xsjs_jcd t Where lx='LS' ".
                        "and fhrq>=date'$start' and fhrq <=date'$end' Group By dh,DQBH_GG,LX) m where m.dqmc is not null  and m.dqmc is not null group by m.dqmc,m.dqbh,m.dm";

                    $row=$ora->query($sql);
                    $info['xiaoshoujiesuan']=$row;
                    $array=array();
                    foreach($info['jingjinghuo'] as $v){
                        $array[$v['XS']]=$v['ZMY'];
                    }
                    $info['jingjinghuo']=$array;
                    $array=array();
                    foreach($info['jingxiaoshou'] as $v){
                        $array[$v['XS']]=$v['ZMY'];
                    }
                    $info['jingxiaoshou']=$array;
                    $array=array();
                    foreach($info['jinhuojiesuan'] as $v){
                        $array[$v['XS']]=$v['ZSY'];
                    }
                    $info['jinhuojiesuan']=$array;
                    $array=array();
                    foreach($info['lianyingxiaoshou'] as $v){
                        $array[$v['XS']]=$v['ZMY'];
                    }
                    $info['lianyingxiaoshou']=$array;
                    $array=array();
                    foreach($info['xiaoshoujiesuan'] as $v){
                        $array[$v['XS']]=$v['ZSY'];
                    }
                    $info['xiaoshoujiesuan']=$array;
                    setcache("jicengdian_liansuo_".$diqu."_".$start.'_'.$end,$info);
                    $jicengdian_liansuo=$info;
                }
                $jicengdian_jiaocai=getcache("jicengdian_jiaocai_".$diqu_id[0]."_".$start.'_'.$end);
                if(empty($jicengdian_jiaocai)){
//净进货
                   $info="";
				   $sql="Select m.dm xs,Sum(m.ZCS) as zcs,Sum(m.ZMY) as zmy,Sum(m.ZSY)as zsy From (Select YWBMBH as dh,(Select MIN(dm_fr) From t_zczm_dm Where dh=t.ywbmbh)As dm,(Select MIN(dqmc) From t_zczm_dm Where dh=t.ywbmbh)As dqmc,(Select MIN(dqBH_GG) From t_zczm_dm Where dh=t.ywbmbh)As dqbh,ZCS,ZMY,ZSY ".
                        "From t_dhhz_jcd T Where ywlx='JC' And DBRQ>=Date'$start' And DBRQ<=Date'$end' )m  where m.dqmc is not null Group By m.dm";
                    $row=$ora->query($sql);
                    $info['jingjinghuo']=$row;

                    //净销售
                    $sql="Select m.dm xs,Sum(m.ZCS) as zcs,Sum(m.ZMY) as zmy,Sum(m.ZSY) as zsy,sum(m.zcb)as zcb From (Select YWBMBH as dh,(Select MIN(dm_fr) From t_zczm_dm Where dh=t.ywbmbh)As dm,(Select MIN(dqmc) From t_zczm_dm Where dh=t.ywbmbh)As dqmc,(Select MIN(dqBH_GG) From t_zczm_dm Where dh=t.ywbmbh)As dqbh,ZCS,ZMY,ZSY,zcb ".
                        " From t_Xsls_JCD T Where  DBRQ>=Date'$start' And DBRQ<=Date'$end' ) m where m.dqmc is not null Group By m.dm,m.dqbh ,m.dqmc";

                    $row=$ora->query($sql);
                    $info['jingxiaoshou']=$row;


                    //进货结算
                    $sql="select m.dm xs,m.dqbh,m.dqmc,sum(m.cfje) as ZSY from (Select DH,(Select MIN(dm_fr) From t_zczm_dm Where dh=t.dh)As dm,DQBH_GG as dqbh,(Select MIN(dqmc) From t_zczm_dm Where dh=t.dh)As dqmc,CFJE,LX From t_zczm_hyjs_jcd t Where lx='JC' and fprq >=date'$start' and fprq <=date'$end') m ".
                        " where m.dqmc is not null  group by m.dm,m.dqbh,m.dqmc";

                    $row=$ora->query($sql);
                    $info['jinhuojiesuan']=$row;
                    //销售结算
                    $sql="select m.dm xs,m.dqmc ,sum(m.zmy) as zmy,sum(m.zsy) as zsy,sum(m.zcb)as zcb from (Select DH,(Select MIN(dm_fr) From t_zczm_dm Where dh=t.dh)As dm,DQBH_GG as dqbh,(Select MIN(dqmc) From t_zczm_dm Where dh=t.dh)As dqmc,zmy,zsy,zcb,LX ".
                        "From t_zczm_xsjs_jcd t Where lx='JC' and fhrq >=date'$start' and fhrq<=date'$end')m  where m.dqmc is not null group by m.dm,m.dqbh,m.dqmc";

                    $row=$ora->query($sql);
                    $info['xiaoshoujiesuan']=$row;


                    //转成键值为地区的
                    $array=array();
                    foreach($info['jingjinghuo'] as $v){
                        $array[$v['XS']]=$v['ZMY'];
                    }
                    $info['jingjinghuo']=$array;
                    $array=array();
                    foreach($info['jingxiaoshou'] as $v){

                        $array[$v['XS']]=$v['ZMY'];

                    }
                    $info['jingxiaoshou']=$array;

                    $array=array();
                    foreach($info['jinhuojiesuan'] as $v){

                        $array[$v['XS']]=$v['ZSY'];

                    }
                    $info['jinhuojiesuan']=$array;
                    $array=array();
                    foreach($info['xiaoshoujiesuan'] as $v){
                        $array[$v['XS']]=$v['ZSY'];
                    }
                    $info['xiaoshoujiesuan']=$array;

                    setcache("jicengdian_jiaocai_".$diqu."_".$start.'_'.$end,$info);
                    $jicengdian_jiaocai=$info;
                }
                
                $info=array();
                foreach($xs as $k=>$v){
                    $info['jingjinghuo'][$v['catname']]=$jicengdian_liansuo['jingjinghuo'][$v['catname']]+$jicengdian_jiaocai['jingjinghuo'][$v['catname']];
                    $info['jingxiaoshou'][$v['catname']]=$jicengdian_liansuo['jingxiaoshou'][$v['catname']]+$jicengdian_jiaocai['jingxiaoshou'][$v['catname']];
                    $info['lianyingxiaoshou'][$v['catname']]=$jicengdian_liansuo['lianyingxiaoshou'][$v['catname']];
                    $info['jinhuojiesuan'][$v['catname']]=$jicengdian_liansuo['jinhuojiesuan'][$v['catname']]+$jicengdian_jiaocai['jinhuojiesuan'][$v['catname']];
                    $info['xiaoshoujiesuan'][$v['catname']]=$jicengdian_liansuo['jingjinghuo'][$v['catname']]+$jicengdian_jiaocai['xiaoshoujiesuan'][$v['catname']];
                }
				setcache("jicengdian_$diqu_".$start.'_'.$end);
				include $this->admin_tpl('show_jicengdian_quanxian');
                die;

            }
        }else if($diqu==-1){
            $info=getcache("jicengdian_q_".$start.'_'.$end);
            if(empty($info)){
                //连锁店
                $jicengdian_liansuo=getcache("jicengdian_liansuo_q_".$start.'_'.$end);
                if(empty($jicengdian_liansuo)){
                    //连锁店净进货
                    $sql="Select DQBH,DQMC,Sum(ZCS),Sum(ZMY) ZMY,Sum(ZSY) ZSY From (Select YWBMBH,(Select dqmc From t_zczm_dm Where dh=t.ywbmbh)As DQmc,(Select dqBH_GG From t_zczm_dm Where dh=t.ywbmbh)As DQBH,ZCS,ZMY,ZSY From ".
                        "t_dhhz_jcd T Where ywlx='LS' And (GHDWH<>'000008' and GHDWH<>'000009' and GHDWH<>'000010') And DBRQ>=Date'$start' And DBRQ<=Date'$end' ) Group By DQMC,DQBH";

                    $row=$ora->query($sql);
                    $info['jingjinghuo']=$row;

                    //连锁店净销售
                    $sql="Select DQBH,DQMC,Sum(ZCS),Sum(ZMY) ZMY,Sum(ZSY) ZSY From (Select YWBMBH,(Select dqmc From t_zczm_dm Where dh=t.ywbmbh)As DQmc,(Select dqBH_GG From t_zczm_dm Where dh=t.ywbmbh)As DQBH,ZCS,ZMY,ZSY,zcb  From ".
                        "t_Xsls_Lsd T Where  (khbh<>'000008' and khbh<>'000009' and khbh<>'000010') And DBRQ>=Date'$start' And DBRQ<=Date'$end' ) Group By DQMC,DQBH ";

                    $row=$ora->query($sql);
                    $info['jingxiaoshou']=$row;

                    //进货结算连锁店

                    $sql="Select DQBH_GG,DQMC,Sum(CFJE) ZSY,LX From t_zczm_hyjs_jcd Where lx='LS' and fprq>=date'$start' and fprq <=date'$end' Group By DQBH_GG,LX,DQMC";

                    $row=$ora->query($sql);
                    $info['jinhuojiesuan']=$row;

                    //联营销售
                    $sql="Select DQBH,DQMC,Sum(ZCS) ZSY,Sum(ZMY) ZMY From (Select YWBMBH,(Select dqmc From t_zczm_dm Where dh=t.ywbmbh)As DQmc,(Select dqBH_GG From t_zczm_dm Where dh=t.ywbmbh)As DQBH,ZCS,ZMY From t_zlXsls_Lsd T Where ".
                        "DBRQ>=Date'$start' And DBRQ<=Date'$end' ) Group By DQMC,DQBH";
                    $row=$ora->query($sql);
                    $info['lianyingxiaoshou']=$row;

//销售结算
                    $sql="Select DQBH_GG,DQMC,Sum(zmy) zmy,sum(zsy) ZSY,sum(zcb),LX From t_zczm_xsjs_jcd Where lx='LS' and fhrq >=date'$start' and fhrq <=date'$end' Group By DQBH_GG,LX,DQMC";
                    $row=$ora->query($sql);
                    $info['xiaoshoujiesuan']=$row;


                    $array=array();
                    foreach($info['jingjinghuo'] as $v){
                        $array[$v['DQMC']]=$v['ZMY'];
                    }
                    $jicengdian_liansuo['jingjinghuo']=$array;
                    $array=array();
                    foreach($info['jingxiaoshou'] as $v){
                        $array[$v['DQMC']]=$v['ZMY'];
                    }
                    $jicengdian_liansuo['jingxiaoshou']=$array;
                    $array=array();
                    foreach($info['jinhuojiesuan'] as $v){
                        $array[$v['DQMC']]=$v['ZSY'];
                    }
                    $jicengdian_liansuo['jinhuojiesuan']=$array;
                    $array=array();
                    foreach($info['lianyingxiaoshou'] as $v){
                        $array[$v['DQMC']]=$v['ZMY'];
                    }
                    $jicengdian_liansuo['lianyingxiaoshou']=$array;
                    $array=array();
                    foreach($info['xiaoshoujiesuan'] as $v){

                        $array[$v['DQMC']]=$v['ZSY'];

                    }
                    $jicengdian_liansuo['xiaoshoujiesuan']=$array;
                    setcache("jicengdian_liansuo_q_".$start.'_'.$end,$jicengdian_liansuo);
                }
                //教材教辅
                $jicengdian_jiaocai=getcache("jicengdian_jiaocai_q_".$start.'_'.$end);
                if(empty($jicengdian_jiaocai)){
                    //净进货
                    $sql="SELECT YWBMBH,DQBH,DQMC,SUM (ZCS) ZCS,SUM (ZMY) ZMY,SUM (ZSY) ZSY FROM (SELECT YWBMBH,(SELECT MIN (dqmc) FROM	t_zczm_dm WHERE dh = T .ywbmbh ) AS DQmc,(SELECT MIN (dqBH_GG) FROM t_zczm_dm WHERE dh = T .ywbmbh) AS DQBH,ZCS,ZMY,ZSY FROM t_dhhz_jcd T WHERE	ywlx = 'JC'	AND ".
                        "DBRQ >= DATE '$start' AND DBRQ <= Date'$end') GROUP BY DQMC,DQBH,YWBMBH GROUP BY DQMC,DQBH,	YWBMBH";
                    $sql="Select DQBH,DQMC,Sum(ZCS),Sum(ZMY) as ZMY,Sum(ZSY) From (Select YWBMBH,(Select max(dqmc) From t_zczm_dm Where dh=t.ywbmbh)As DQmc,(Select max(dqBH_GG) From t_zczm_dm Where dh=t.ywbmbh)As DQBH,ZCS,ZMY,ZSY From t_dhhz_jcd T Where ywlx='JC' And ".
                        "DBRQ>=Date'$start' And DBRQ<=Date'$end' ) Group By DQMC,DQBH";
					$row=$ora->query($sql);
                    $info['jingjinghuo']=$row;
                    //净销售
                    $sql="Select DQBH,DQMC,Sum(ZCS),Sum(ZMY) ZMY,Sum(ZSY) ZSY From (Select YWBMBH,(Select MIN(dqmc) From t_zczm_dm Where dh=t.ywbmbh)As DQmc,(Select MIN(dqBH_GG) From t_zczm_dm Where dh=t.ywbmbh)As DQBH,ZCS,ZMY,ZSY,zcb  From t_Xsls_JCD T Where ".
                        " DBRQ>=Date'$start' And DBRQ<=Date'$end' ) Group By DQMC,DQBH";
                    $row=$ora->query($sql);
                    $info['jingxiaoshou']=$row;
                    //进货结算

                    $sql="Select DQBH_GG,dqmc,Sum(CFJE) ZSY,LX From t_zczm_hyjs_jcd Where lx='JC' and fprq >=date'$start' and fprq <=date'$end' Group By DQBH_GG,LX,dqmc";
                    $row=$ora->query($sql);
                    $info['jinhuojiesuan']=$row;

                    //销售结算
                    $sql="Select DQBH_GG,dqmc,Sum(zmy) zmy,sum(zsy) ZSY,sum(zcb),LX From t_zczm_xsjs_jcd Where lx='JC' and fhrq>=date'$start' and fhrq <=date'$end' Group By DQBH_GG,LX,dqmc";
                    $row=$ora->query($sql);
                    $info['xiaoshoujiesuan']=$row;


                    $array=array();
                    foreach($info['jingjinghuo'] as $v){
                        $array[$v['DQMC']]=$v['ZMY'];
                    }
                    $jicengdian_jiaocai['jingjinghuo']=$array;
                    $array=array();
                    foreach($info['jingxiaoshou'] as $v){
                        $array[$v['DQMC']]=$v['ZMY'];
                    }
                    $jicengdian_jiaocai['jingxiaoshou']=$array;
                    $array=array();
                    foreach($info['jinhuojiesuan'] as $v){

                        $array[$v['DQMC']]=$v['ZSY'];

                    }
                    $jicengdian_jiaocai['jinhuojiesuan']=$array;
                    $array=array();
                    foreach($info['jinhuojiesuan'] as $v){

                        $array[$v['DQMC']]=$v['ZSY'];

                    }
                    $info['jinhuojiesuan']=$array;
                    $array=array();
                    foreach($info['xiaoshoujiesuan'] as $v){

                        $array[$v['DQMC']]=$v['ZSY'];

                    }
                    $jicengdian_jiaocai['xiaoshoujiesuan']=$array;
                    $jicengdian_jiaocai['lianyingxiaoshou']='';
                    setcache("jicengdian_jiaocai_q_".$start.'_'.$end,$jicengdian_jiaocai);
                }
                $info=array();

                foreach($diquarr as $k=>$v){
                    $info['jingjinghuo'][$v]=$jicengdian_liansuo['jingjinghuo'][$v]+$jicengdian_jiaocai['jingjinghuo'][$v];
                    $info['jingxiaoshou'][$v]=$jicengdian_liansuo['jingxiaoshou'][$v]+$jicengdian_jiaocai['jingxiaoshou'][$v];
                    $info['lianyingxiaoshou'][$v]=$jicengdian_liansuo['lianyingxiaoshou'][$v];
                    $info['jinhuojiesuan'][$v]=$jicengdian_liansuo['jinhuojiesuan'][$v]+$jicengdian_jiaocai['jinhuojiesuan'][$v];
                    $info['xiaoshoujiesuan'][$v]=$jicengdian_liansuo['jingjinghuo'][$v]+$jicengdian_jiaocai['xiaoshoujiesuan'][$v];
                }
                setcache("jicengdian_q_".$start.'_'.$end,$info);

            }
            include $this->admin_tpl('show_jicengdian');
            die;
        }elseif(isset($diqu)){
            $info=getcache("jicengdian_".$diqu_id[0]."_".$start.'_'.$end);
            if(empty($info)){
                $jicengdian_liansuo=getcache("jicengdian_liansuo_".$diqu_id[0]."_".$start.'_'.$end);
                if(empty($jicengdian_liansuo)){
                    //连锁店净进货
                    //$sql="Select trim(dm) xs,DQBH,DQMC,Sum(ZCS),Sum(ZMY) ZMY,Sum(ZSY) From (Select (Select dm_fr From t_zczm_dm Where dh=t.ywbmbh) As dm,(Select dqmc From t_zczm_dm Where dh=t.ywbmbh)As DQmc,".
                      //  "(Select dqBH_GG From t_zczm_dm Where dh=t.ywbmbh)As DQBH,ZCS,ZMY,ZSY From t_dhhz_jcd T Where ywlx='LS' And (GHDWH<>'000008' and GHDWH<>'000009' and GHDWH<>'000010') And DBRQ>=DATE '$start' And DATE '$end') Where dqmc = '$diqure' Group By DQMC,DQBH,dm";
                    //
					$sql="SELECT trim(dm) xs,SUM (ZCS) ZCS,SUM (ZMY) ZMY,SUM (ZSY) ZSY FROM(SELECT YWBMBH,(SELECT DM_FR FROM	t_zczm_dm WHERE	dh = T .ywbmbh) AS dm,(SELECT dqmc FROM t_zczm_dm WHERE dh = T .ywbmbh) AS DQmc,ZCS,ZMY,ZSY	FROM ".
                        "t_dhhz_jcd T WHERE ywlx = 'LS' AND (GHDWH <> '000008' and GHDWH <> '000009' and GHDWH <> '000010' ) AND DBRQ >= DATE '$start' AND DBRQ <= DATE '$end' ) WHERE dqmc = '$diqure' GROUP BY dm";
                    
					$row=$ora->query($sql);
                    $info['jingjinghuo']=$row;

                    $sql="SELECT trim(dm) xs,SUM (ZCS),SUM (ZMY) ZMY,SUM (ZSY) ZSY FROM(SELECT(SELECT dm_fr FROM t_zczm_dm WHERE	dh = T .ywbmbh) AS dm,(	SELECT	dqmc FROM t_zczm_dm WHERE dh = T .ywbmbh) AS DQmc,ZCS,ZMY,ZSY,zcb FROM t_Xsls_Lsd T	WHERE (	khbh <> '000008' and khbh <> '000009' and khbh <> '000010'	) ".
                        "AND DBRQ >= DATE '$start' AND DBRQ <= DATE '$end'	) WHERE	dqmc = '$diqure'GROUP BY dm";
                    $row=$ora->query($sql);
                    $info['jingxiaoshou']=$row;

                    //进货结算连锁店
                    $sql="Select trim((Select min(dm_fr) From t_zczm_dm Where dh_fr=t.dh))As xs,Sum(CFJE) ZSY,LX From t_zczm_hyjs_jcd t Where lx='LS' and fprq>=date'$start' and fprq <=date'$end' and dqmc='$diqure' Group By dm,DQBH_GG,LX,dh";
                    $row=$ora->query($sql);
                    $info['jinhuojiesuan']=$row;

                    //联营销售
                    $sql="Select trim(dm) xs,Sum(ZCS) ZCS,Sum(ZMY) ZMY From (Select (Select dm_fr From t_zczm_dm Where dh=t.ywbmbh) As dm ,(Select dqmc From t_zczm_dm Where dh=t.ywbmbh)As DQmc,ZCS,ZMY From t_zlXsls_Lsd T Where ".
                        "DBRQ>=Date'$start' And DBRQ<=DATE '$end' ) Where dqmc='$diqure' Group By dm ";

                    $row=$ora->query($sql);
                    $info['lianyingxiaoshou']=$row;


                    //销售结算
                    $sql="select m.dqbh,m.dqmc,trim(m.dm) xs,sum(m.zmy) as zmy,sum(m.zsy) as zsy,sum(m.zcb)as zcb from (Select dh,(Select min(dm_fr) From t_zczm_dm Where dh=t.dh) as dm,".
                        "DQBH_GG as dqbh,(Select Min(dqmc) From t_zczm_dm Where dh=t.dh) as dqmc,sum(zmy) as zmy,sum(zsy) as zsy,sum(zcb) as zcb,LX From t_zczm_xsjs_jcd t Where lx='LS' ".
                        "and fhrq>=date'$start' and fhrq <=date'$end' Group By dh,DQBH_GG,LX) m where m.dqmc is not null  and m.dqmc='$diqure' group by m.dqmc,m.dqbh,m.dm";

                    $row=$ora->query($sql);
                    $info['xiaoshoujiesuan']=$row;
                    $array=array();
                    foreach($info['jingjinghuo'] as $v){
                        $array[$v['XS']]=$v['ZMY'];
                    }
                    $info['jingjinghuo']=$array;
                    $array=array();
                    foreach($info['jingxiaoshou'] as $v){
                        $array[$v['XS']]=$v['ZMY'];
                    }
                    $info['jingxiaoshou']=$array;
                    $array=array();
                    foreach($info['jinhuojiesuan'] as $v){
                        $array[$v['XS']]=$v['ZSY'];
                    }
                    $info['jinhuojiesuan']=$array;
                    $array=array();
                    foreach($info['lianyingxiaoshou'] as $v){
                        $array[$v['XS']]=$v['ZMY'];
                    }
                    $info['lianyingxiaoshou']=$array;
                    $array=array();
                    foreach($info['xiaoshoujiesuan'] as $v){
                        $array[$v['XS']]=$v['ZSY'];
                    }
                    $info['xiaoshoujiesuan']=$array;
                    setcache("jicengdian_liansuo_".$diqu_id[0]."_".$start.'_'.$end,$info);
                    $jicengdian_liansuo=$info;

                }
                $jicengdian_jiaocai=getcache("jicengdian_jiaocai_".$diqu_id[0]."_".$start.'_'.$end);
                if(empty($jicengdian_jiaocai)){
                    //净进货
                    $sql="Select m.dm xs,Sum(m.ZCS) as zcs,Sum(m.ZMY) as zmy,Sum(m.ZSY)as zsy From (Select YWBMBH as dh,(Select MIN(dm_fr) From t_zczm_dm Where dh=t.ywbmbh)As dm,(Select MIN(dqmc) From t_zczm_dm Where dh=t.ywbmbh)As dqmc,(Select MIN(dqBH_GG) From t_zczm_dm Where dh=t.ywbmbh)As dqbh,ZCS,ZMY,ZSY ".
                        "From t_dhhz_jcd T Where ywlx='JC' And DBRQ>=Date'$start' And DBRQ<=Date'$end' )m  where m.dqmc='$diqu' Group By m.dm";
                    $row=$ora->query($sql);
                    $info['jingjinghuo']=$row;

                    //净销售
                    $sql="Select m.dm xs,Sum(m.ZCS) as zcs,Sum(m.ZMY) as zmy,Sum(m.ZSY) as zsy,sum(m.zcb)as zcb From (Select YWBMBH as dh,(Select MIN(dm_fr) From t_zczm_dm Where dh=t.ywbmbh)As dm,(Select MIN(dqmc) From t_zczm_dm Where dh=t.ywbmbh)As dqmc,(Select MIN(dqBH_GG) From t_zczm_dm Where dh=t.ywbmbh)As dqbh,ZCS,ZMY,ZSY,zcb ".
                        " From t_Xsls_JCD T Where  DBRQ>=Date'$start' And DBRQ<=Date'$end' ) m where m.dqmc='$diqu' Group By m.dm,m.dqbh ,m.dqmc";

                    $row=$ora->query($sql);
                    $info['jingxiaoshou']=$row;


                    //进货结算
                    $sql="select m.dm xs,m.dqbh,m.dqmc,sum(m.cfje) as zsy from (Select DH,(Select MIN(dm_fr) From t_zczm_dm Where dh=t.dh)As dm,DQBH_GG as dqbh,(Select MIN(dqmc) From t_zczm_dm Where dh=t.dh)As dqmc,CFJE,LX From t_zczm_hyjs_jcd t Where lx='JC' and fprq >=date'$start' and fprq <=date'$end') m ".
                        " where m.dqmc='$diqu'  group by m.dm,m.dqbh,m.dqmc";

                    $row=$ora->query($sql);
                    $info['jinhuojiesuan']=$row;
                    //销售结算
                    $sql="select m.dm xs,m.dqmc ,sum(m.zmy) as zmy,sum(m.zsy) as zsy,sum(m.zcb)as zcb from (Select DH,(Select MIN(dm_fr) From t_zczm_dm Where dh=t.dh)As dm,DQBH_GG as dqbh,(Select MIN(dqmc) From t_zczm_dm Where dh=t.dh)As dqmc,zmy,zsy,zcb,LX ".
                        "From t_zczm_xsjs_jcd t Where lx='JC' and fhrq >=date'$start' and fhrq <=date'$end')m  where m.dqmc='$diqu' group by m.dm,m.dqbh,m.dqmc";

                    $row=$ora->query($sql);
                    $info['xiaoshoujiesuan']=$row;


                    //转成键值为地区的
                    $array=array();
                    foreach($info['jingjinghuo'] as $v){
                        $array[$v['XS']]=$v['ZMY'];
                    }
                    $info['jingjinghuo']=$array;
                    $array=array();
                    foreach($info['jingxiaoshou'] as $v){

                        $array[$v['XS']]=$v['ZMY'];

                    }
                    $info['jingxiaoshou']=$array;

                    $array=array();
                    foreach($info['jinhuojiesuan'] as $v){

                        $array[$v['XS']]=$v['ZSY'];

                    }
                    $info['jinhuojiesuan']=$array;
                    $array=array();
                    foreach($info['xiaoshoujiesuan'] as $v){
                        $array[$v['XS']]=$v['ZSY'];
                    }
                    $info['xiaoshoujiesuan']=$array;

                    setcache("jicengdian_jiaocai_".$diqu_id[0]."_".$start.'_'.$end,$info);
                    $jicengdian_jiaocai=$info;
                }

                $info=array();

                foreach($xs as $k=>$v){

                    $info['jingjinghuo'][$v['catname']]=$jicengdian_liansuo['jingjinghuo'][$v['catname']]+$jicengdian_jiaocai['jingjinghuo'][$v['catname']];
                    $info['jingxiaoshou'][$v['catname']]=$jicengdian_liansuo['jingxiaoshou'][$v['catname']]+$jicengdian_jiaocai['jingxiaoshou'][$v['catname']];
                    $info['lianyingxiaoshou'][$v['catname']]=$jicengdian_liansuo['lianyingxiaoshou'][$v['catname']];
                    $info['jinhuojiesuan'][$v['catname']]=$jicengdian_liansuo['jinhuojiesuan'][$v['catname']]+$jicengdian_jiaocai['jinhuojiesuan'][$v['catname']];
                    $info['xiaoshoujiesuan'][$v['catname']]=$jicengdian_liansuo['jingjinghuo'][$v['catname']]+$jicengdian_jiaocai['xiaoshoujiesuan'][$v['catname']];

                }

                setcache("jicengdian_".$diqu_id[0]."_".$start.'_'.$end,$info);
            }
            include $this->admin_tpl('show_jicengdian_shi');
            die;
        }



    }
    public function liansuodian () {
        set_time_limit(0);
        $userid = $_SESSION['userid'];
        $admin_username = param::get_cookie('admin_username');
        $roles = getcache('role','commons');
        $rolename = $roles[$_SESSION['roleid']];
        $site = pc_base::load_app_class('sites');
        $sitelist = $site->get_list($_SESSION['roleid']);
        $currentsite = $this->get_siteinfo(param::get_cookie('siteid'));
        /*管理员收藏栏*/
        $adminpanel = $this->panel_db->select(array('userid'=>$userid), "*",20 , 'datetime');
        $site_model = param::get_cookie('site_model');
        $title='连锁店经营情况';
        $this->role_provdb = pc_base::load_model('admin_role_priv_model');
        $admin_menu = $this->role_provdb->select(array('roleid'=>$_SESSION['roleid'],'m'=>'admin','c'=>'index'),'a');
        $new=array();
        foreach($admin_menu as $k=>$v){
            $new[$v[a]]=1;
        }
        extract($new);
//        $host='172.30.153.61';
//        $ip='172.30.153.61/xhsddb';
//        $port='1521';
//        $user= 'booklsd';
//        $pass= 'zjlsdqwert';
//        $charset='utf8';
        $host='172.30.153.63';
        $ip='172.30.153.63/XHSDDB';
        $port='1521';
        $user= 'dbjczc';
        $pass= 'dbjczc';
        $charset='utf8';
        pc_base::load_app_class('oracle_admin','admin',0);//数据库
        $ora=new oracle_admin($user,$pass,$ip,$charset);

        //$start=isset($_REQUEST['start'])?$_REQUEST['start']:date('Y-m-d',time()-24*60*60*7);
        $start=isset($_REQUEST['start'])?$_REQUEST['start']:date('Y-m-d',time()-24*60*60*7);
        $end=isset($_REQUEST['end'])?$_REQUEST['end']:date('Y-m-d',time()-24*60*60);

        $category_privdb = pc_base::load_model('category_priv_model');
        $admin_category = $category_privdb->get_one("`roleid`=$_SESSION[roleid] and `is_admin`='1' and `siteid`='1'");
        $categorydb = pc_base::load_model('category_model');
        $category = $categorydb->get_one("`catid`=$admin_category[catid]");
        $jibie=sizeof(explode(',',$category['arrparentid']));
        $jibie=0;
        //if($jibie==2){
          //  $_REQUEST['diqu']=$category['catname'];
            //$xs = $categorydb->select(array('parentid'=>$admin_category[catid]),'catname');
        //}else
			if(isset($_REQUEST[diqu])){
            $catid=$categorydb->select(array('parentid'=>6,'catname'=>"$_REQUEST[diqu]"),'catid');
            $xs = $categorydb->select(array('parentid'=>$catid[0][catid]),'catname');
        }
        $diquarr=array('石家庄','唐山','秦皇岛','邯郸','邢台','保定','张家口','承德','沧州','廊坊','衡水','辛集','定州','河北省店');
        $diqure=$diqu=isset($_REQUEST['diqu'])?$_REQUEST['diqu']:-1;
        $isshidian=isset($_REQUEST['isshidian'])?$_REQUEST['isshidian']:-1;
        $dianpuleixing=isset($_REQUEST['dianpuleixing'])?$_REQUEST['dianpuleixing']:-1;

        $where=' ';
        if($isshidian!=-1){
            $where.=" And b. bj_sd='$isshidian'";
            $name='s_'.$isshidian;
        }
        if($dianpuleixing!=-1){
            $where.="  And b.bj_fl='$dianpuleixing'";
            $name.='l_'.$dianpuleixing;
        }
		
		//
        if($diqu==-2){
            $info=getcache("liansuodian_".$diqu.$name."_".$start.'_'.$end);
            if(empty($info)){
                //图书情况

                $sql="SELECT A .dqbh，b.dm_fr DM,SUM (A .zcs) AS zcs, SUM (A .zmy) AS zmy, SUM (A .zsy) AS zsy,case sum(a.zmy) when 0 then 0 else (sum(a.zsy-a.zcb)/sum(a.zmy)) end as mll FROM t_xsls_lsd_new A,t_zczm_dm b WHERE	".
                    "A .dbrq >= DATE '$start' AND A .dbrq <= DATE '$end' AND A .ywbmbh = b.dh AND b.ywlx = 'LS' AND sptz = '01' $where and dqmc is not null GROUP BY A .dqbh,b.dm_fr ORDER BY A .dqbh";

                $row=$ora->query($sql);
                $info[tushu]=$row;

                //var_dump($sql);die;
                //非图书情况
                $sql="SELECT A .dqbh，b.dm_fr DM,SUM (A .zcs) AS zcs, SUM (A .zmy) AS zmy, SUM (A .zsy) AS zsy,case sum(a.zmy) when 0 then 0 else  (sum(a.zsy-a.zcb)/sum(a.zmy)) end as mll FROM t_xsls_lsd_new A,t_zczm_dm b WHERE	".
                    "A .dbrq >= DATE '$start' AND A .dbrq <= DATE '$end' AND A .ywbmbh = b.dh AND b.ywlx = 'LS' AND sptz != '01' $where and dqmc is not null GROUP BY A .dqbh,b.dm_fr ORDER BY A .dqbh";
                $row=$ora->query($sql);
                $info[feitushu]=$row;
                //联营情况
                $sql="SELECT A .dqbh_gg,trim(b.dm_fr) dm,SUM (A .zcs) AS zcs,SUM (A .zmy) AS zmy FROM t_zlxsls_lsd A,t_zczm_dm b WHERE ".
                    "A .dbrq >= DATE '$start' AND A .dbrq <= DATE '$end' AND A .ywbmbh = b.dh AND b.ywlx = 'LS' $where and dqmc is not null GROUP BY A .dqbh_gg,b.dm_fr ORDER BY A .dqbh_gg";
                $row=$ora->query($sql);
                $info[lianying]=$row;
                //合计
                $array=array();
                $info[tushumll]='';
                foreach($info[tushu] as $v){
                    $array[$v['DM']]=$v['ZMY'];
                    $info[tushumll][$v['DM']]=$v['MLL'];
                }

                $info[tushu]=$array;
                $array=array();
                $info[feitushumll]='';
                foreach($info[feitushu] as $v){
                    $array[$v['DM']]=$v['ZMY'];
                    $info[feitumaolilv][$v['DM']]=$v['MLL'];
                }

                $info[feitushu]=$array;
                $array=array();
                foreach($info[lianying] as $v){
                    $array[$v['DM']]=$v['ZMY'];
                }
                $info[lianying]=$array;

                setcache("liansuodian_".$diqu.$name."_".$start.'_'.$end,$info);
            }
            include $this->admin_tpl('show_liansuodian_shi_leixin');
            die;
        }else if($diqu!=-2&&$diqu!=-1){
            $diqu_id=array_keys($diquarr,$diqu);
			
			
            //如果是市店并且寻找了店铺类型
            //if($isshidian==1&&$dianpuleixing!=-1){
			//选择了一个地区并且是市店类型
			if($isshidian == '01' ){
				$info=getcache("liansuodian_".$diqu_id[0].$name."_".$start.'_'.$end);
				if(empty($info)){
                    //图书情况

                    $sql="SELECT A .dqbh,b.dh,b.dm DM,SUM (A .zcs) AS zcs, SUM (A .zmy) AS zmy, SUM (A .zsy) AS zsy,case sum(a.zmy) when 0 then 0 else (sum(a.zsy-a.zcb)/sum(a.zmy)) end as mll FROM t_xsls_lsd_new A,t_zczm_dm b WHERE	".
                        "A .dbrq >= DATE '$start' AND A .dbrq <= DATE '$end' AND A .ywbmbh = b.dh AND b.ywlx = 'LS' AND sptz = '01' $where and dqmc ='$diqure' GROUP BY A .dqbh,b.dh,b.dm ORDER BY A .dqbh";

                    $row=$ora->query($sql);
                    $info[tushu]=$row;

                    //var_dump($sql);die;
                    //非图书情况
                    $sql="SELECT A .dqbh，b.dh,b.dm DM,SUM (A .zcs) AS zcs, SUM (A .zmy) AS zmy, SUM (A .zsy) AS zsy,case sum(a.zmy) when 0 then 0 else (sum(a.zsy-a.zcb)/sum(a.zmy)) end as mll FROM t_xsls_lsd_new A,t_zczm_dm b WHERE	".
                        "A .dbrq >= DATE '$start' AND A .dbrq <= DATE '$end' AND A .ywbmbh = b.dh AND b.ywlx = 'LS' AND sptz != '01' $where and dqmc ='$diqure' GROUP BY A .dqbh,b.dh,b.dm ORDER BY A .dqbh";
                    $row=$ora->query($sql);
                    $info[feitushu]=$row;
                    //联营情况
                    $sql="SELECT A .dqbh_gg,b.dm DM,SUM (A .zcs) AS zcs,SUM (A .zmy) AS zmy FROM t_zlxsls_lsd A,t_zczm_dm b WHERE ".
                        "A .dbrq >= DATE '$start' AND A .dbrq <= DATE '$end' AND A .ywbmbh = b.dh AND b.ywlx = 'LS' $where and dqmc ='$diqure' GROUP BY A .dqbh_gg,b.dm ORDER BY A .dqbh_gg";
                    
                    $row=$ora->query($sql);
		
                    $info[lianying]=$row;
                    //合计
                    $array=array();
                    $info[tushumll]='';
                    foreach($info[tushu] as $v){
                        $array[$v['DM']]=$v['ZMY'];
                        $info[tushumll][$v['DM']]=$v['MLL'];
                    }

                    $info[tushu]=$array;
                    $array=array();
                    $info[feitushumll]='';
                    foreach($info[feitushu] as $v){
                        $array[$v['DM']]=$v['ZMY'];
                        $info[feitumaolilv][$v['DM']]=$v['MLL'];
                    }

                    $info[feitushu]=$array;
                    $array=array();
                    foreach($info[lianying] as $v){
                        $array[$v['DM']]=$v['ZMY'];
                    }
                    $info[lianying]=$array;

                    setcache("liansuodian_".$diqu_id[0].$name."_".$start.'_'.$end,$info);

                }
                include $this->admin_tpl('show_liansuodian_shi_leixin');
                die;
        }
			
			else{
			
                $info=getcache("liansuodian_".$diqu_id[0].$name."_".$start.'_'.$end);

                if(empty($info)){
                    //图书情况

                    $sql="SELECT A .dqbh，b.dm_fr DM,SUM (A .zcs) AS zcs, SUM (A .zmy) AS zmy, SUM (A .zsy) AS zsy,case sum(a.zmy) when 0 then 0 else (sum(a.zsy-a.zcb)/sum(a.zmy)) end as mll FROM t_xsls_lsd_new A,t_zczm_dm b WHERE	".
                        "A .dbrq >= DATE '$start' AND A .dbrq <= DATE '$end' AND A .ywbmbh = b.dh AND b.ywlx = 'LS' AND sptz = '01' $where and dqmc ='$diqure' GROUP BY A .dqbh,b.dm_fr ORDER BY A .dqbh";

                    $row=$ora->query($sql);
                    $info[tushu]=$row;

                    //var_dump($sql);die;
                    //非图书情况
                    $sql="SELECT A .dqbh，b.dm_fr DM,SUM (A .zcs) AS zcs, SUM (A .zmy) AS zmy, SUM (A .zsy) AS zsy,case sum(a.zmy) when 0 then 0 else (sum(a.zsy-a.zcb)/sum(a.zmy)) end as mll FROM t_xsls_lsd_new A,t_zczm_dm b WHERE	".
                        "A .dbrq >= DATE '$start' AND A .dbrq <= DATE '$end' AND A .ywbmbh = b.dh AND b.ywlx = 'LS' AND sptz != '01' $where and dqmc ='$diqure' GROUP BY A .dqbh,b.dm_fr ORDER BY A .dqbh";
                    $row=$ora->query($sql);
                    $info[feitushu]=$row;
                    //联营情况
                    $sql="SELECT A .dqbh_gg,trim(b.dm_fr) dm,SUM (A .zcs) AS zcs,SUM (A .zmy) AS zmy FROM t_zlxsls_lsd A,t_zczm_dm b WHERE ".
                        "A .dbrq >= DATE '$start' AND A .dbrq <= DATE '$end' AND A .ywbmbh = b.dh AND b.ywlx = 'LS' $where and dqmc ='$diqure' GROUP BY A .dqbh_gg,b.dm_fr ORDER BY A .dqbh_gg";
                    $row=$ora->query($sql);
		
                    $info[lianying]=$row;
                    //合计
                    $array=array();
                    $info[tushumll]='';
                    foreach($info[tushu] as $v){
                        $array[$v['DM']]=$v['ZMY'];
                        $info[tushumll][$v['DM']]=$v['MLL'];
                    }

                    $info[tushu]=$array;
                    $array=array();
                    $info[feitushumll]='';
                    foreach($info[feitushu] as $v){
                        $array[$v['DM']]=$v['ZMY'];
                        $info[feitumaolilv][$v['DM']]=$v['MLL'];
                    }

                    $info[feitushu]=$array;
                    $array=array();
                    foreach($info[lianying] as $v){
                        $array[$v['DM']]=$v['ZMY'];
                    }
                    $info[lianying]=$array;

                    setcache("liansuodian_".$diqu_id[0].$name."_".$start.'_'.$end,$info);

                }
                include $this->admin_tpl('show_liansuodian_shi_leixin');
                die;
			}
           // }
           /* $info=getcache("liansuodian_".$diqu_id[0].$name."_".$start.'_'.$end);
            if(empty($info)){
                //图书情况
                $sql="Select  C.DM_FR XS,count(Distinct(a.Id)) As zpz,Sum(a.xscs) zcs , Sum(a.xscs*B.DJ)   As zmy,".
   "Sum(zsy) zsy, (Sum(zsy)-Sum(CBJ))/Sum(a.xscs*B.DJ) As MLL From t_xsls a,t_kcsm b ,T_Dqdz_Hd C Where a.id=b.id And sptz='01' And A.YWBMBH=C.DH ".
                    "And a.DBRQ>=Date'$start' And a.dbrq<Date'$end' And C.DQMC='$diqure' $where Group by C.DM_FR";
                $row=$ora->query($sql);

                $info[tushu]=$row;
                //var_dump($sql);die;
                //非图书情况
                $sql="Select  C.DM_FR XS,count(Distinct(a.Id)) As zpz,Sum(a.xscs) zcs , Sum(a.xscs*B.DJ)   As zmy,   Sum(zsy) zsy, (Sum(zsy)-Sum(CBJ))/Sum(a.xscs*B.DJ) As MLL From t_xsls a,t_kcsm b ,T_Dqdz_Hd C Where a.id=b.id And sptz!='01' And A.YWBMBH=C.DH And a.DBRQ>=Date'$start' And a.dbrq<Date'$end'  And C.DQMC='$diqure' $where Group By C.DM_FR";
                $row=$ora->query($sql);
                $info[feitushu]=$row;
                //联营情况
                $sql="Select  C.dqmc xs,
Count(a.xsdjh) As zbs,

   Sum(a.yjje) As zsy
From t_zl_xshz a,T_Dqdz_Hd C
Where  A.YWBMBH=C.DH
And a.jk_date>=Date'$start' And a.jk_date<Date'$end'  --日期范围
And C.DQMC='$diqure'
$where
Group By  C.dqmc";
                $row=$ora->query($sql);
                $info[lianying]=$row;
                //合计
                $array=array();
                $info[tushumll]='';
                foreach($info[tushu] as $v){
                    $array[$v['XS']]=$v['ZMY'];
                    $info[tushumll][$v['XS']]=$v['MLL'];
                }
                $info[tushu]=$array;
                $array=array();
                $info[feitushumll]='';
                foreach($info[feitushu] as $v){
                    $array[$v['XS']]=$v['ZMY'];
                    $info[feitumaolilv][$v['XS']]=$v['MLL'];
                }
                $info[feitushu]=$array;
                $array=array();
                foreach($info[lianying] as $v){
                    $array[$v['XS']]=$v['ZSY'];
                }
                $info[lianying]=$array;

                setcache("liansuodian_".$diqu_id[0].$name."_".$start.'_'.$end,$info);
            }
            if($isshidian==1){
                $xs='';
                $xs[]['catname']=$diqure;
            }elseif($isshidian==2){
                unset($xs[0]);
            }
            include $this->admin_tpl('show_liansuodian_shi');*/

        }else{
            $info=getcache("liansuodian_".$name.$start.'_'.$end);

            if(empty($info)){
                //图书情况
                $sql="select a.dqbh,trim(dqmc) dqmc,sum(zcs) as zcs,sum(zmy) as zmy,sum(zsy) as zsy,sum(zcb) as zcb,case sum(a.zmy) when 0 then 0 else (sum(a.zsy-a.zcb)/sum(a.zmy)) end as mll  from t_xsls_lsd_new a,t_zczm_dm b ".
                    " where a.dbrq >=date'$start' and a.dbrq <=date'$end' and a.ywbmbh = b.dh and b.ywlx ='LS' and sptz ='01' $where group by a.dqbh,dqmc";

                $row=$ora->query($sql);
                $info[tushu]=$row;
                //非图书情况
                $sql="select a.dqbh,trim(dqmc) dqmc,sum(zcs) as zcs,sum(zmy) as zmy,sum(zsy) as zsy,sum(zcb) as zcb,case sum(a.zmy) when 0 then 0 else (sum(a.zsy-a.zcb)/sum(a.zmy)) end as mll from t_xsls_lsd_new a,t_zczm_dm b".
                    " where a.dbrq >=date'$start' and a.dbrq <=date'$end' and a.ywbmbh = b.dh and b.ywlx ='LS' and sptz !='01' $where group by a.dqbh,dqmc";
                $row=$ora->query($sql);
                $info[feitushu]=$row;
                //联营情况
                $sql="SELECT A .dqbh_gg, trim(dqmc) dqmc, SUM (A .zcs) AS zcs, SUM (A .zmy) AS zmy FROM t_zlxsls_lsd A, t_zczm_dm b WHERE ".
                    "A .dbrq >= DATE '$start' AND A .dbrq <= DATE '$end' AND A .ywbmbh = b.dh AND b.ywlx = 'LS' $where GROUP BY A .dqbh_gg, dqmc";

                $row=$ora->query($sql);
                $info[lianying]=$row;

                //合计
                $array=array();
                foreach($info[tushu] as $v){
                    $array[$v['DQMC']]=$v['ZMY'];
                    $info['tushumaolilv'][$v['DQMC']]=$v['MLL'];
                }
                $info[tushu]=$array;
                $array=array();
                foreach($info[feitushu] as $v){
                    $array[$v['DQMC']]=$v['ZMY'];
                    $info['feitumaolilv'][$v['DQMC']]=$v['MLL'];
                }
                $info[feitushu]=$array;
                $array=array();
                foreach($info[lianying] as $v){
                    $array[$v['DQMC']]=$v['ZMY'];
                }
                $info[lianying]=$array;

                setcache("liansuodian_".$name.$start.'_'.$end,$info);
            }
            include $this->admin_tpl('show_liansuodian');
        }

    }
    public function yibantushu () {
        $userid = $_SESSION['userid'];
        $admin_username = param::get_cookie('admin_username');
        $roles = getcache('role','commons');
        $rolename = $roles[$_SESSION['roleid']];
        $site = pc_base::load_app_class('sites');
        $sitelist = $site->get_list($_SESSION['roleid']);
        $currentsite = $this->get_siteinfo(param::get_cookie('siteid'));
        /*管理员收藏栏*/
        $adminpanel = $this->panel_db->select(array('userid'=>$userid), "*",20 , 'datetime');
        $site_model = param::get_cookie('site_model');
        $title='一般图书排行';
        $this->role_provdb = pc_base::load_model('admin_role_priv_model');
        $roleid=$_SESSION['roleid'];
        $admin_menu = $this->role_provdb->select(array('roleid'=>$roleid,'m'=>'admin','c'=>'index'),'a');
        $new=array();
        foreach($admin_menu as $k=>$v){
            $new[$v[a]]=1;
        }
		//print_r($new);exit;
        extract($new);
        $this->category_priv_db = pc_base::load_model('category_priv_model');
        $this->category_db = pc_base::load_model('category_model');

        $from = isset($_GET['from']) && in_array($_GET['from'],array('block')) ? $_GET['from'] : 'content';
        $result = $this->category_priv_db->select_one(array('is_admin'=>1,'action'=>'init','roleid'=>$roleid),'catid',100,'catid');
        $categorys=array();
        foreach($result as $k=>$v){
            $category = $this->category_db->select(array('catid'=>$v));
            $categorys[$v]=$category[0];
        }
        $tree = pc_base::load_sys_class('tree');
        $tree->init($categorys);
        $strs = "<span class='\$icon_type'>\$add_icon\$catname</a></span>";
        $strs2 = "<span class='folder'>\$catname</span>";

        $categorys = $tree->get_treeview(0,'category_tree',$strs,$strs2);
        //echo ($categorys);

		//这里判断是查询全省还是地区
        if(in_array("6", $result)){
            //全省
            $result = $this->category_db->select(array('parentid'=>6),'catid,catname');
        }else{
			//某一个地区
            foreach($result as $k=>$v){
                if($k==0){
                    $arrchildid=$this->category_db->get_one('catid='.$v,'arrchildid');
                    $result = $this->category_db->query('select `catdir` from v9_category where catid in('.$arrchildid['arrchildid'].') and catdir like \'L3%\'');
                    $dianhua=array();
                    while ( $row = $result->fetch_assoc() ) {
                        $dianhua[]=$row['catdir'];
                    }
                }else{

                }
            }
        }

       // $start=isset($_REQUEST['start'])?$_REQUEST['start']:date('Y',time()).'-1-1';
        $start=isset($_REQUEST['start'])?$_REQUEST['start']:date('Y-m-d',time()-24*60*60*7);
        $end=isset($_REQUEST['end'])?$_REQUEST['end']:date('Y-m-d',time()-24*60*60); //2018-09-26
        $diquarr=array('石家庄','唐山','秦皇岛','邯郸','邢台','保定','张家口','承德','沧州','廊坊','衡水','辛集','定州','河北省店');
        $diqure=$diqu=isset($_REQUEST['diqu'])?$_REQUEST['diqu']:-1;
        $num=isset($_REQUEST['num'])?$_REQUEST['num']:10;
        $flbh=isset($_REQUEST['flbh'])?$_REQUEST['flbh']:'';
        $diqu_id=array_keys($diquarr,$diqu);

//
//        $ip='172.30.153.63/xhsddb';
//        $port='1521';
//        $user= 'dbsl';
//        $pass= 'dbsl';
//        $charset='utf8';
//        pc_base::load_app_class('oracle_admin','admin',0);//数据库
//        $fenlei_db=new oracle_admin($user,$pass,$ip,$charset);
//		//调用分类
//        $sql="Select flbh,flmc,xh From t_clj_fl ORDER BY xh";
//        $fenlei=$fenlei_db->query($sql);
		//调用分类结束
        //调用排行开始
        $host='172.30.153.61';
        $ip='172.30.153.61/xhsddb';
        $port='1521';
        $user= 'booklsd';
        $pass= 'zjlsdqwert';
        $charset='utf8';
        pc_base::load_app_class('oracle_admin','admin',0);//数据库
        $ora=new oracle_admin($user,$pass,$ip,$charset);

        if($flbh){
            $name='f_'.$flbh;
            $where=" And zczmfl = '$flbh' ";
        }
        //图片库
        $thost='10.11.9.223';
        $tip='10.11.9.223/orcl';
        $tport='1521';
        $tuser= 'cssj';
        $tpass= 'cssj';
        $tcharset='utf8';
        $tora=new oracle_admin($tuser,$tpass,$tip,$tcharset);


        //图片库优选
        pc_base::load_app_class('newmysql','admin',0);//数据库
        $yip='101.201.150.185:3307';
        $yuser= 'xhsdshop';
        $ypass= 'xhsdshop_%2015%';
        $ydb='dbshop';
        $mysqlclass=new newmysql($yip,$yuser,$ypass,$ydb);
        if($diqu!=-1){

			//查询某一个地区，$diqu代表前台选择的地区
            $where.=" and  dqmc ='$diqu'";
            $name.='d_'.$diqu_id[0];
            $info=getcache('yibantushu_'.$name.'_n'.$num.'_'.$start."_".$end);
            if(empty($info)){
				//某一地区排行
                $sql="Select M.id,M.shuhao,M.name,M.dingjia,M.banbie,M.ceshu,m.dqmc From (Select Id,b.dqmc,".
                    "(Select fl From t_kcsm Where Id=a.id) fl,(Select isbn From t_kcsm Where a.id=Id)shuhao,(Select sm From t_kcsm Where a.id=Id) name,(Select dj From t_kcsm Where a.id=Id) dingjia,".
                    "(Select mc From t_bb Where bh In (Select bb From t_kcsm Where a.id=Id)) banbie,Sum(xscs) ceshu From t_Xsls a ,t_Dqdz_Hd@LSD_TO_SL b Where a.ywbmbh = b.dh and".
                    "dbrq>=Date'$start' and dbrq<=Date'$end' and ywbmbh Like 'L%' And Exists (Select 1 From t_kcsm Where a.id=Id And sptz='01') Group By Id,b.dqmc Order By Sum(xscs) Desc ".
                    ")M where  Rownum < $num+1 ".$where;
                $sql="Select M.id,M.shuhao,M.name,M.dingjia,M.banbie,M.ceshu,m.dqmc,M.cbny From (Select Id,b.dqmc,(Select fl From t_kcsm Where Id=a.id) fl,(select (select zczmfl From Zczm_Fl_Information Where flbh= t_kcsm.fl) from t_kcsm where id = a.id) as zczmfl,".
                    "(Select isbn From t_kcsm Where a.id=Id)shuhao,(Select sm From t_kcsm Where a.id=Id) name,(Select dj From t_kcsm Where a.id=Id) dingjia,(Select mc From t_bb Where bh In (Select bb From t_kcsm Where a.id=Id)) banbie,".
                    "(Select to_char(round(cbny,2),'9999.99')From t_kcsm Where Id=a.id) cbny,Sum(xscs) ceshu From t_Xsls a,t_zczm_dm@LSD_TO_ZCZM b Where a.ywbmbh = b.dh  and dbrq>=Date'$start' and dbrq <= Date'$end' and ywbmbh Like 'L%' And Exists ".
                    "(Select 1 From t_kcsm Where a.id=Id And sptz='01') Group By Id,b.dqmc Order By Sum(xscs) Desc )M where  Rownum < $num+1 ".$where;
                $info=$ora->query($sql);
                if($num<=10){
                    foreach($info as $k=>$v){
                        $sql="Select i.s_url From `sdb_b2c_goods` g,`sdb_image_image` i  Where g.image_default_id=i.image_id and  trim(g.bn)=trim($v[ID])";
                        $tinfo=$mysqlclass->selectLimit($sql,1,0);

                        if($tinfo[0][s_url]){
                            $info[$k][img]="http://www.xinhuabest.com/".$tinfo[0][s_url];
                        }else{
                            $sql="Select id,PIC5,www5 From v_kcsm_jb a Where trim(id)=trim($v[ID])";
                            $tinfo=$tora->query($sql);
                            if($tinfo[0][PIC5]){
                                $info[$k][img]="http://172.30.153.21/".$tinfo[0][WWW5]."/".$tinfo[0][PIC5];
                            }else{
                                $info[$k][img]="images/book_img_1.png";
                            }
                        }

                    }
                }

                setcache('yibantushu_'.$name.'_n'.$num.'_'.$start."_".$end,$info);
            }
        }else{

            $info=getcache('yibantushu_'.$name.'_n'.$num.'_'.$start."_".$end);

            if(empty($info)){
				//全省排行查询

//                $sql="Select M.id,M.shuhao,M.name,M.dingjia,M.banbie,M.ceshu From (Select Id,".
//                "(Select fl From t_kcsm Where Id=a.id) fl,(Select isbn From t_kcsm Where a.id=Id)shuhao,(Select sm From t_kcsm Where a.id=Id) name,(Select dj From t_kcsm Where a.id=Id) dingjia,".
//                    "(Select mc From t_bb Where bh In (Select bb From t_kcsm Where a.id=Id)) banbie,Sum(xscs) ceshu From t_Xsls a Where ".
//                    "dbrq>=Date'$start' and dbrq<=Date'$end' and ywbmbh Like 'L%' And Exists (Select 1 From t_kcsm Where a.id=Id And sptz='01') Group By Id Order By Sum(xscs) Desc ".
//                    ")M where  Rownum < $num+1 ".$where;
                $sql="Select M.id,M.shuhao,M.name,M.dingjia,M.banbie,M.ceshu,M.cbny From (Select Id,(Select fl From t_kcsm Where Id=a.id) fl,(select (select zczmfl From Zczm_Fl_Information Where flbh= t_kcsm.fl) ".
                    "from t_kcsm where id = a.id) as zczmfl,(Select isbn From t_kcsm Where a.id=Id)shuhao,(Select sm From t_kcsm Where a.id=Id) name,(Select dj From t_kcsm Where a.id=Id) dingjia,".
                    "(Select mc From t_bb Where bh In (Select bb From t_kcsm Where a.id=Id)) banbie,(Select to_char(round(cbny,2),'9999.99') From t_kcsm Where Id=a.id) cbny,Sum(xscs) ceshu From t_Xsls a Where ".
                    "dbrq>=Date'$start' and dbrq<=Date'$end' and ywbmbh Like 'L%' And Exists (Select 1 From t_kcsm Where a.id=Id And sptz='01') Group By Id Order By Sum(xscs) Desc )M ".
                    "where  Rownum < $num+1".$where;

                $info=$ora->query($sql);
                if($num<=10){
                    foreach($info as $k=>$v){
                        $bn=trim($v[ID]);
                        $sql="Select i.s_url From `sdb_b2c_goods` g,`sdb_image_image` i  Where g.image_default_id=i.image_id and  g.bn='$bn'";
                        $tinfo=$mysqlclass->selectLimit($sql,1,0);

                        if($tinfo[0][s_url]){
                            $info[$k][img]="http://www.xinhuabest.com/".$tinfo[0][s_url];
                        }else{
                            $sql="Select id,PIC5,www5 From v_kcsm_jb a Where id=trim($v[ID])";
                            $tinfo=$tora->query($sql);
                            if($tinfo[0][PIC5]){
                                $info[$k][img]="http://172.30.153.21/".$tinfo[0][WWW5]."/".$tinfo[0][PIC5];
                            }else{
                                $info[$k][img]="images/book_img_1.png";
                            }
                        }

                    }
                }
                setcache('yibantushu_'.$name.'_n'.$num.'_'.$start."_".$end,$info);
            }

        }
        if($num>10){

            include $this->admin_tpl('show_yibantushu_more');
        }else{
            include $this->admin_tpl('show_yibantushu');
        }
    }
    public function yibantushu_info () {
        $userid = $_SESSION['userid'];
        $admin_username = param::get_cookie('admin_username');
        $roles = getcache('role','commons');
        $rolename = $roles[$_SESSION['roleid']];
        $site = pc_base::load_app_class('sites');
        $sitelist = $site->get_list($_SESSION['roleid']);
        $currentsite = $this->get_siteinfo(param::get_cookie('siteid'));
        /*管理员收藏栏*/
        $adminpanel = $this->panel_db->select(array('userid'=>$userid), "*",20 , 'datetime');
        $site_model = param::get_cookie('site_model');
        $title='一般图书排行';
        $this->role_provdb = pc_base::load_model('admin_role_priv_model');
        $roleid=$_SESSION['roleid'];
        $admin_menu = $this->role_provdb->select(array('roleid'=>$roleid,'m'=>'admin','c'=>'index'),'a');
        $new=array();
        foreach($admin_menu as $k=>$v){
            $new[$v[a]]=1;
        }
        extract($new);
        $this->category_priv_db = pc_base::load_model('category_priv_model');
        $this->category_db = pc_base::load_model('category_model');

        $from = isset($_GET['from']) && in_array($_GET['from'],array('block')) ? $_GET['from'] : 'content';
        $result = $this->category_priv_db->select_one(array('is_admin'=>1,'action'=>'init','roleid'=>$roleid),'catid',100,'catid');
        $categorys=array();
        foreach($result as $k=>$v){
            $category = $this->category_db->select(array('catid'=>$v));
            $categorys[$v]=$category[0];
        }
        $tree = pc_base::load_sys_class('tree');
        $tree->init($categorys);
        $strs = "<span class='\$icon_type'>\$add_icon\$catname</a></span>";
        $strs2 = "<span class='folder'>\$catname</span>";

        $categorys = $tree->get_treeview(0,'category_tree',$strs,$strs2);
        //echo ($categorys);

        if(in_array("6", $result)){
            //全省
            $result = $this->category_db->select(array('parentid'=>6),'catid,catname');
        }else{
            foreach($result as $k=>$v){
                if($k==0){
                    $arrchildid=$this->category_db->get_one('catid='.$v,'arrchildid');
                    $result = $this->category_db->query('select `catdir` from v9_category where catid in('.$arrchildid['arrchildid'].') and catdir like \'L3%\'');
                    $dianhua=array();
                    while ( $row = $result->fetch_assoc() ) {
                        $dianhua[]=$row['catdir'];
                    }
                }else{

                }
            }
        }

        $host='172.30.153.61';
        $ip='172.30.153.61/xhsddb';
        $port='1521';
        $user= 'booklsd';
        $pass= 'zjlsdqwert';
        $charset='utf8';
        pc_base::load_app_class('oracle_admin','admin',0);//数据库
        $ora=new oracle_admin($user,$pass,$ip,$charset);
        $start=isset($_REQUEST['start'])?$_REQUEST['start']:date('Y-m-d',time()-24*60*60*7);
        $end=isset($_REQUEST['end'])?$_REQUEST['end']:date('Y-m-d',time()-24*60*60);
        $diquarr=array('石家庄','唐山','秦皇岛','邯郸','邢台','保定','张家口','承德','沧州','廊坊','衡水','辛集','定州','河北省店');
        $diqure=$diqu=isset($_REQUEST['diqu'])?$_REQUEST['diqu']:-1;
        $id=$_GET['id'];
        if($diqure!=-1){//判断是否选地区 
			//有地区
            $sql="Select
ywbmbh,(Select dm From t_dm Where a.ywbmbh=dh) dm,
(Select sm From t_kcsm Where a.id=Id) shuname,
Sum(xscs) ceshu,sum(xscs*(select dj from t_kcsm where a.id=id)) zmy
From t_xsls a
Where   Id='$id' And  Exists (Select 1 From t_zczm_dm@LSD_TO_ZCZM Where a.Ywbmbh=dh And dqmc='$diqu') And dbrq>Date'$start' And dbrq<=Date'$end'
Group By a.ywbmbh,a.Id order by ceshu Desc";
            $sql2="Select Sum(xscs) ceshu,sum(xscs*(select dj from t_kcsm where a.id=id)) zmy From t_xsls a
Where   Id='$id' And  Exists (Select 1 From t_zczm_dm@LSD_TO_ZCZM Where a.Ywbmbh=dh And dqmc='$diqu') And dbrq>Date'$start' And dbrq<=Date'$end'";

        }else{
			//全省
            $sql="Select
ywbmbh,(Select dm From t_dm Where a.ywbmbh=dh) dm,
(Select sm From t_kcsm Where a.id=Id) shuname,
Sum(xscs) ceshu,sum(xscs*(select dj from t_kcsm where a.id=id)) zmy
From t_xsls a
Where   Id='$id' And dbrq>Date'$start' And dbrq<=Date'$end'
Group By a.ywbmbh,a.Id order by ceshu Desc";
            $sql2="Select Sum(xscs) ceshu,sum(xscs*(select dj from t_kcsm where a.id=id)) zmy
From t_xsls a
Where   Id='$id' And dbrq>Date'$start' And dbrq<=Date'$end'";

        }
        $info=$ora->query($sql);
        $info2=$ora->query($sql2);
        $name=$_GET['name'];
        $bianbie=$_GET['bianbie'];
        $dingjia=$_GET['dingjia'];
        include $this->admin_tpl('show_yibantushu_info');
        /*$sql="Select ywbmbh,(Select dm From t_dm Where a.ywbmbh=dh And LXBH='1') dm,Id,
(Select isbn From t_kcsm Where a.id=Id) 书号,
(Select sm From t_kcsm Where a.id=Id) shuname,
(Select dj From t_kcsm Where a.id=Id) 定价,
Sum(xscs) ceshu,
Sum(a.xscs*a.zjxs)   As zmy
From t_xsls a
Where  Id='$id'
And dbrq>Date'$start' And dbrq<Date'$end'
Group By a.ywbmbh,a.Id order by zmy desc";*/

    }
	
	//连锁，教材教辅导出 
	public function daochu () {
		$op = new_html_special_chars(trim($_REQUEST['op']));
		if(empty($op)){
			showmessage('无参数！非法访问！',HTTP_REFERER);	
		}else{
			$linkarr = array('liansuo','jiaofu','check_file');
			if(!in_array($op,$linkarr)){
				showmessage('非法参数，非法访问！',HTTP_REFERER);
			}
		}
		if($op=='liansuo'){
			$pre = '连锁公司';
		}elseif($op=='jiaofu'){
			$pre = '教材教辅公司';
		}
        $userid = $_SESSION['userid'];
        $admin_username = param::get_cookie('admin_username');
        $roles = getcache('role','commons');
        $rolename = $roles[$_SESSION['roleid']];// 24  23 26
        $site = pc_base::load_app_class('sites');
        $sitelist = $site->get_list($_SESSION['roleid']);
        $currentsite = $this->get_siteinfo(param::get_cookie('siteid'));
        /*管理员收藏栏*/
        $adminpanel = $this->panel_db->select(array('userid'=>$userid), "*",20 , 'datetime');
        $site_model = param::get_cookie('site_model');
        $title=$pre.'查询系统';
        $this->role_provdb = pc_base::load_model('admin_role_priv_model');
        $admin_menu = $this->role_provdb->select(array('roleid'=>$_SESSION['roleid'],'m'=>'admin','c'=>'index'),'a');
		//print_r($admin_menu);exit;
        $new=array();
        foreach($admin_menu as $k=>$v){
            $new[$v[a]]=1;
        }
        extract($new);
		$time_before = get_month(); //echo：‘七月份’
		//print_r($time);
		//$start=isset($_REQUEST['start'])?$_REQUEST['start']:date('Y-m-d',$time['start_time']);
        //$end=isset($_REQUEST['end'])?$_REQUEST['end']:date('Y-m-d',$time['end_time_next']);
		$dangnian = date('Y');
		$nianfen = isset($_REQUEST['nianfen'])?$_REQUEST['nianfen']: '请选择年份';
		$month = isset($_REQUEST['month'])?$_REQUEST['month']:'请选择月份';
		$jidu = isset($_REQUEST['jidu'])?$_REQUEST['jidu']:'请选择季度';
		$niandu = isset($_REQUEST['niandu'])?$_REQUEST['niandu']:'请选择年度';

		if($op=='liansuo'){
			$yewugongsi=isset($_REQUEST['yewugongsi'])?$_REQUEST['yewugongsi']:3;//3连锁是写死的，去show_daochu_tpl模板中看
			$yewuleixing=isset($_REQUEST['yewuleixing'])?$_REQUEST['yewuleixing']:'差异-更正差异';
		}elseif($op=='jiaofu'){
			$yewugongsi=isset($_REQUEST['yewugongsi'])?$_REQUEST['yewugongsi']:2;//2教材是写死的，去show_daochu_jiaofu模板中看
			$yewuleixing=isset($_REQUEST['yewuleixing'])?$_REQUEST['yewuleixing']:'月报';
		}

		if($_REQUEST['dosubmit'] && $_REQUEST['dosubmit']==1){
			//print_r($_POST);exit;
			if($month != '请选择月份'){ //返回当前月开始，下一个月的第一天
				$month = $_REQUEST['month'];
				$time_month = monthChange($month,$nianfen); //获取当月的第一天，下一个月的第一天
				//print_r($time_month);exit;
				$start = date('Y-m-d',$time_month['start']);
				//echo $start;exit;
				$end = date('Y-m-d',$time_month['end']);
				//$end = '2018-09-26';
				$timetype = $month;//此变量会在yuebao()这个方法中用到，为了拼接文件路径。 也会在判断过程的时候用到
			}
			if($jidu != '请选择季度'){
				$jidu = $_REQUEST['jidu'];
				$time_month = monthChange($jidu,$nianfen); //获取季度的时间戳  有一个问题 如果是一季度是从2018-01-01   到 2018-02-01 换是到2018-01-31
				//print_r($time_month);exit;
				$start = date('Y-m-d',$time_month['start']);
				$end = date('Y-m-d',$time_month['end']);
				$timetype = $jidu;//此变量会在yuebao()这个方法中用到，为了拼接文件路径。也会在判断过程的时候用到
			}
			if($niandu != '请选择年度'){//1-6  7-12  1-12
				$niandu = $_REQUEST['niandu'];
				//echo $niandu;exit;
				$time_month = monthChange($niandu,$nianfen); 
				//print_r($time_month);exit;
				$start = date('Y-m-d',$time_month['start']);
				$end = date('Y-m-d',$time_month['end']);
				$timetype = $niandu;//此变量会在yuebao()这个方法中用到，为了拼接文件路径。
			}
			//echo $timetype;exit;
			if($_SESSION['roleid']==26){
				$daochulx = $_REQUEST['daochulx'];
			}
			set_time_limit(0);
			ini_set('memory_limit', '-1');
			$daochu = $_REQUEST['daochu']; //点击导出按钮的专用判断值
			$host='172.30.153.63';
			$ip='172.30.153.63/xhsddb';
			$port='1521';
			$user= 'dbjczc';
			$pass= 'dbjczc';
			$charset='utf8';
			pc_base::load_app_class('oracle_admin','admin',0);//数据库
			$ora=new oracle_admin($user,$pass,$ip,$charset);
			$yewubumen = $_REQUEST['yewubumen'];
			if($_SESSION['roleid']==26){ //如果是导出总管理员登录
				if($yewubumen=='quanbu'){
					$abc = array('020001','020002','020003','020004','020005');
					$admin_username = implode(',',$abc);
				}else{
					$admin_username	= $_REQUEST['yewubumen'];
				}
			}
			
			
			if($yewugongsi==1){ //查询教辅公司
				$yewugongsiname='教辅公司';
				
				$guocheng=getcache('guocheng');
				
				if($_SESSION['roleid']==26){//过程
					if($guocheng['guocheng'] != $timetype ){
						$sql_gc = "Delete From t_month_dhjsb";
						$res = $ora->query($sql_gc);
						$sql_gc = "Delete From t_month_dhjsb_mf";
						$res = $ora->query($sql_gc);
						$sql_gc = "Delete From t_month_jtjsb";
						$res = $ora->query($sql_gc);
						$sql_gc = "Delete From t_month_fhjsb";
						$res = $ora->query($sql_gc);
						$sql_gc = "Delete From t_month_xtjsb";
						$res = $ora->query($sql_gc);
						$sql_gc = "Delete From t_month_fhgzjsb";
						$res = $ora->query($sql_gc);
						$sql_gc = "Delete From t_month_pfmxjsb";
						$res = $ora->query($sql_gc);
						$sql_gc = "Delete From t_month_zd_ts";
						$res = $ora->query($sql_gc);
						$sql_gc = "Insert Into t_month_dhjsb
        (ywbmbh, ghdwh, mc, dhpch, shrq, ysdj, ssdh, zdqh, zdxh, sm, dj, jxsssl, my, sys,
         zdpch, jsrq, JSLX, ZDPCH1, TSLSH, ysrq, jsghdwh, flowid_dhmx)
        Select ywbmbh, ghdwh, (Select mc From t_ghdw@zczm_jc Where bh = a.ghdwh) As mc, dhpch,
               shrq, ysdj, ssdh, zdqh, zdxh, (Select sm From t_kcsm@zczm_jc Where id = a.id) As sm,
               (Select dj From t_kcsm@zczm_jc Where id = a.id) As dj, (djshsl),
               ((djshsl) * (Select dj From t_kcsm@zczm_jc Where Id = a.id)) As my, (sys) As sy,
               zdpch, (Select FPDJRQ From T_ZDTSLS@zczm_jc Where PCH = A.ZDPCH) As jsrq,
               (Select JSLX From t_kcsm@zczm_jc Where id = a.id) As JSLX, ZDPCH1, tslsh, ysrq,
               Null, flowid_dhmx
          From t_dhls_jx@zczm_jc a
         Where trunc(a.shrq) < trunc(Date '$end')
           And (zdpch Is Null Or Exists (Select pch
                   From t_zdtsls@zczm_jc
                  Where pch = a.zdpch
                    And (FPDJrq >= Date '$start' Or FPDJRQ Is Null)))";
		 //echo $sql_gc;exit;
						$res = $ora->query($sql_gc);
						$sql_gc = "Insert Into t_month_zd_ts
        Select zdpch, tslsh, Count(Distinct(tslsh)) As num_tslsh
          From t_jzls_dh@zczm_jc
         Group By zdpch, tslsh";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);
						$sql_gc = "Update t_month_dhjsb a
       Set tslsh = (Select tslsh From t_month_zd_ts Where zdpch = a.zdpch)
     Where tslsh Is Null";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);
						$sql_gc = "Insert Into t_month_dhjsb
        (ywbmbh, ghdwh, mc, dhpch, shrq, ysdj, ssdh, zdqh, zdxh, sm, dj, jxsssl, my, sys,
         zdpch, jsrq, JSLX, ZDPCH1, TSLSH, ysrq, jsghdwh, flowid_dhmx)
        Select ywbmbh, ghdwh, (Select mc From t_ghdw@zczm_jc Where bh = a.ghdwh) As mc, dhpch,
               shrq, ysdj, ssdh, zdqh, zdxh, (Select sm From t_kcsm@zczm_jc Where id = a.id) As sm,
               (Select dj From t_kcsm@zczm_jc Where id = a.id) As dj, (djshsl),
               ((djshsl) * (Select dj From t_kcsm@zczm_jc Where Id = a.id)) As my, (sys) As sy,
               zdpch, shrq As jsrq, (Select JSLX From t_kcsm@zczm_jc Where id = a.id) As JSLX,
               ZDPCH1, tslsh, ysrq, ghdwh, flowid_dhmx
          From t_dhls_jx@zczm_jc a
         Where trunc(a.shrq) < trunc(Date '$end')
           And shrq >= trunc(Date '$start')
           And (zdpch Is Not Null And Not Exists
                (Select pch From t_zdtsls@zczm_jc Where pch = a.zdpch))";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);
						$sql_gc = "Update t_month_dhjsb t Set ysghdwh = ghdwh Where tslsh Is Not Null";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);
						$sql_gc = "Insert Into t_month_dhjsb_mf
        (ywbmbh, ghdwh, mc, dhpch, shrq, ysdj, ssdh, zdqh, zdxh, sm, dj, jxsssl, my, sys,
         zdpch, jsrq, JSLX, tslsh, jsghdwh)
        Select ywbmbh, ghdwh, (Select mc From t_ghdw@zczm_jc Where bh = a.ghdwh) As mc, czpch,
               czrq, '免费' As ysdj, Null As ssdh, zdqh, zdxh,
               (Select sm From t_kcsm@zczm_jc Where id = a.id) As sm,
               (Select dj From t_kcsm@zczm_jc Where id = a.id) As dj, mfcs,
               (mfcs * (Select dj From t_kcsm@zczm_jc Where Id = a.id)) As my,
               (mfcs * (Select dj From t_kcsm@zczm_jc Where Id = a.id) * (xjhzk - yjhzk) / 100) As sy,
               zdpch, (Select fpdjrq From t_zdtsls@zczm_jc Where pch = a.zdpch) As jsrq,
               (Select JSLX From t_kcsm@zczm_jc Where id = a.id) As JSLX, tslsh,
               ((Select ghdwh1 From t_fpls@zczm_jc Where Tslsh = a.Tslsh) Union All
                 (Select ghdwh1 From t_tmp_fpls@zczm_jc Where t_tmp_fpls.Tslsh = a.Tslsh))
          From t_dhls_mfjc@zczm_jc a
         Where trunc(a.czrq) < trunc(Date '$end')
           And (zdpch Is Null Or Exists
                (Select pch
                   From t_zdtsls@zczm_jc
                  Where pch = a.zdpch
                    And (FPDJRQ >= trunc(Date '$start') Or FPDJRQ Is Null)))";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);
						$sql_gc = "Update t_month_dhjsb_mf t Set ysghdwh = ghdwh Where tslsh Is Not Null";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);
						
						$sql_gc = "Insert Into t_month_jtjsb
        (ywbmbh, ghdwh, mc, dbrq, id, sm, dj, zmy, zsy, bz, ywpch, zdpch, jsrq, JSLX,
         ZDQH, ZDXH, ZDPCH1, TSLSH, jsghdwh, zcs)
        Select a.YWBMBH, a.hybh As GHDWH, (Select mc From t_ghdw@zczm_jc Where bh = a.HYBH) As mc,
               a.dbrq, a.id, b.sm, b.dj, (- (a.thcs - a.thcs1) * b.dj) As zmy,
               (-a.thcs * b.dj * thzk / 100) As zsy, bz, ywpch, zdpch,
               (Select FPDJRQ From t_zdtsls@zczm_jc Where pch = a.zdpch) jsrq, b.jslx As JSLX,
               A.ZDQH, A.ZDXH, ZDPCH1, TSLSH,
               ((Select ghdwh1 From t_fpls@zczm_jc Where Tslsh = a.Tslsh) Union All
                 (Select ghdwh1 From t_tmp_fpls@zczm_jc Where t_tmp_fpls.Tslsh = a.Tslsh)),
               (-a.thcs)
          From t_hythmx@zczm_jc a, t_kcsm@zczm_jc b
         Where a.id = b.id
           And (a.zdpch Is Null Or Exists
                (Select pch
                   From t_zdtsls@zczm_jc
                  Where pch = a.zdpch
                    And (FPDJRQ >= trunc(Date '$start') Or FPDJRQ Is Null)))
           And trunc(a.dbrq) < trunc(Date '$end')";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);

						$sql_gc = "Update t_month_jtjsb t Set ysghdwh = ghdwh Where tslsh Is Not Null";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);
						
						$sql_gc = "Insert Into t_month_fhjsb
        (ywbmbh, dh, mc, pfpch, bz, dbrq, pfrq, zmy, zsy, cbj, tslsh, jsrq, sm, dj, zdqh,
         zdxh,cbny, kfbh, JSLX, zcs)
        Select ywbmbh, dh, (Select dm From t_dm@zczm_jc Where dh = t.dh) As mc, pfpch, bz As bz,
               (dbrq) As dbrq, pfrq,
               (sfcs * (Select dj From t_kcsm@zczm_jc Where id = t.id)) As zmy,
               ROUND((nvl(sfcs * (Select dj From t_kcsm@zczm_jc Where id = t.id) * fhzk / 100, 0)),
                      2) As zsy, (nvl(cbj, 0)) As cbj, tslsh,
               (Select jsrq From t_tshz@zczm_jc Where tslsh = t.tslsh) jsrq,
               (Select sm From t_kcsm@zczm_jc Where id = t.id) As sm,
               (Select dj From t_kcsm@zczm_jc Where id = t.id) As dj, zdqh, zdxh,  
			   (Select cbny From t_kcsm@zczm_jc Where id = t.id) As cbny,kfbh,
               (Select JSLX From t_kcsm@zczm_jc Where id = t.id) As JSLX, (sfcs)
          From t_fhmx1@zczm_jc t
         Where dbrq < trunc(Date '$end')";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);
						
						$sql_gc = "Insert Into t_month_pfmxjsb
        (ywbmbh, dh, mc, pfpch, bz, dbrq, pfrq, zmy, zsy, cbj, tslsh, jsrq, sm, dj, zdqh,
         zdxh, kfbh, JSLX, Id, zdh, ywqrpc)
        Select ywbmbh, dh, (Select dm From t_dm@zczm_jc Where dh = t.dh) As mc, pxdh, bz As bz,
               (pfrq) As dbrq, pfrq,
               (pxcs * (Select dj From t_kcsm@zczm_jc Where id = t.id)) As zmy,
               ROUND((nvl(pxcs * (Select dj From t_kcsm@zczm_jc Where id = t.id) * pxzk / 100, 0)),
                      2) As zsy, (nvl(cbj, 0)) As cbj, '配发未运' As tslsh, '配发未运' As jsrq,
               (Select sm From t_kcsm@zczm_jc Where id = t.id) As sm,
               (Select dj From t_kcsm@zczm_jc Where id = t.id) As dj, zdqh, zdxh, kfbh,
               (Select JSLX From t_kcsm@zczm_jc Where id = t.id) As JSLX, Id, zdh, ywqrpc
          From t_pfmx@zczm_jc t";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);
						
						$sql_gc = "Insert Into t_month_fhjsb
        (ywbmbh, dh, mc, pfpch, bz, dbrq, pfrq, zmy, zsy, cbj, tslsh, jsrq, sm, dj, zdqh,
         zdxh, cbny, kfbh, JSLX, zcs)
        Select ywbmbh, dh, (Select dm From t_dm@zczm_jc Where dh = t.dh) As mc, pfpch, bz As bz,
               (dbrq) As dbrq, pfrq,
               (sfcs * (Select dj From t_kcsm@zczm_jc Where id = t.id)) As zmy,
               ROUND((nvl(sfcs * (Select dj From t_kcsm@zczm_jc Where id = t.id) * fhzk / 100, 0)),
                      2) As zsy, (nvl(cbj, 0)) As cbj, '现金' As tslsh, dbrq As jsrq,
               (Select sm From t_kcsm@zczm_jc Where id = t.id) As sm,
               (Select dj From t_kcsm@zczm_jc Where id = t.id) As dj, zdqh, zdxh, 
			   (Select cbny From t_kcsm@zczm_jc Where id = t.id) As cbny,kfbh,
               (Select JSLX From t_kcsm@zczm_jc Where id = t.id) As JSLX, (sfcs)
          From t_fhmx@zczm_jc t
         Where dbrq < trunc(Date '$end')
           And dbrq >= trunc(Date '$start')
           And jswc = '1'";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);
						$sql_gc = "Insert Into t_month_fhjsb
        (ywbmbh, dh, mc, pfpch, bz, dbrq, pfrq, zmy, zsy, cbj, tslsh, jsrq, sm, dj, zdqh,
         zdxh,cbny, kfbh, JSLX, zcs)
        Select ywbmbh, dh, (Select dm From t_dm@zczm_jc Where dh = t.dh) As mc, pfpch, bz As bz,
               (dbrq) As dbrq, pfrq,
               (sfcs * (Select dj From t_kcsm@zczm_jc Where id = t.id)) As zmy,
               ROUND((nvl(sfcs * (Select dj From t_kcsm@zczm_jc Where id = t.id) * fhzk / 100, 0)),
                      2) As zsy, (nvl(cbj, 0)) As cbj, 'BJS' As tslsh, czrq As jsrq,
               (Select sm From t_kcsm@zczm_jc Where id = t.id) As sm,
               (Select dj From t_kcsm@zczm_jc Where id = t.id) As dj, zdqh, zdxh, 
			   (Select cbny From t_kcsm@zczm_jc Where id = t.id) As cbny,kfbh,
               (Select JSLX From t_kcsm@zczm_jc Where id = t.id) As JSLX, (sfcs)
          From t_fhmx1_bf@zczm_jc t
         Where dbrq < trunc(Date '$end')
           And czrq >= trunc(Date '$start')";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);
						$sql_gc = "Insert Into t_month_fhjsb
        (ywbmbh, dh, mc, pfpch, bz, dbrq, pfrq, zmy, zsy, cbj, tslsh, jsrq, cwqrpc, sm,
         dj, zdqh, zdxh, cbny,kfbh, JSLX, zcs)
        Select ywbmbh, dh, (Select dm From t_dm@zczm_jc Where dh = t.dh) As mc, pfpch, bz As bz,
               (dbrq) As dbrq, pfrq,
               (sfcs * (Select dj From t_kcsm@zczm_jc Where id = t.id)) As zmy,
               ROUND((nvl(sfcs * (Select dj From t_kcsm@zczm_jc Where id = t.id) * fhzk / 100, 0)),
                      2) As zsy, (nvl(cbj, 0)) As cbj, tslsh,
               (Select cwqrrq From t_tshz@zczm_jc Where tslsh = t.tslsh) jsrq,
               (Select cwqrpc From t_tshz@zczm_jc Where tslsh = t.tslsh) cwqrpc,
               (Select sm From t_kcsm@zczm_jc Where id = t.id) As sm,
               (Select dj From t_kcsm@zczm_jc Where id = t.id) As dj, zdqh, zdxh,
			   (Select cbny From t_kcsm@zczm_jc Where id = t.id) As cbny,kfbh,
               (Select JSLX From t_kcsm@zczm_jc Where id = t.id) As JSLX, (sfcs)
          From t_fhmx2@zczm_jc t
         Where dbrq < trunc(Date '$end')
           And Exists (Select tslsh
                  From t_tshz@zczm_jc
                 Where tslsh = t.tslsh
                   And (cwqrrq >= trunc(Date '$start') Or cwqrrq Is Null))";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);
						$sql_gc = "Insert Into t_month_xtjsb (ywbmbh, kfbh, dh, dm, pch, thrq, zmy, zsy, zcb, tslsh, jsrq, cwqrpc, jslx, ZDQH,zdxh,cbny, zcs) Select ywbmbh, kfbh, dh, (Select dm From t_dm@zczm_jc Where dh = t.dh) As mc, pch, Max(lrrq1) As thrq, 
Sum(-thcs * (Select dj From t_kcsm@zczm_jc Where Id = t.id)) As zmy, 
Sum(ROUND(- (thcs * (Select dj From t_kcsm@zczm_jc Where Id = t.id) * thzk / 100), 2)) As zsy, 
Sum(nvl(-thcs * (Select dj From t_kcsm@zczm_jc Where Id = t.id) * T.Pjzk / 100, 0)) As cbj, tslsh,
 (Select cwqrrq From t_tshz@zczm_jc Where tslsh = t.tslsh) As jsrq, 
 (Select cwqrpc From t_tshz@zczm_jc Where tslsh = t.tslsh) As cwqrpc, a.jslx, T.ZDQH,t.zdxh,
a.cbny As cbny, 
 Sum(-thcs) From t_khthmx@zczm_jc t, t_kcsm@zczm_jc a Where lrrq1 < trunc(Date '$end') And (tslsh Is Null Or Exists (Select tslsh From t_tshz@zczm_jc Where tslsh = t.tslsh And (cwqrrq >= trunc(Date '$start') Or cwqrrq Is Null)))
 And t.id = a.id Group By dh, pch, tslsh, ywbmbh, kfbh, T.ZDQH, a.jslx,t.zdxh,a.cbny";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);
						$sql_gc = "Insert Into t_month_xtjsb1
        (ywbmbh, kfbh, dh, dm, pch, thrq, zmy, zsy, zcb, tslsh, jsrq, cwqrpc, zcs)
        Select ywbmbh, kfbh, dh, (Select dm From t_dm@zczm_jc Where dh = t.dh) As mc, pch,
               Max(lrrq1) As thrq,
               Sum(-thcs * (Select dj From t_kcsm@zczm_jc Where Id = t.id)) As zmy,
               Sum(ROUND(- (thcs * (Select dj From t_kcsm@zczm_jc Where Id = t.id) * thzk / 100),
                          2)) As zsy, Sum(nvl(-cbj1, 0)) As cbj, tslsh,
               (Select cwqrrq From t_tshz@zczm_jc Where tslsh = t.tslsh) As jsrq,
               (Select cwqrpc From t_tshz@zczm_jc Where tslsh = t.tslsh) As cwqrpc, Sum(-thcs)
          From t_khthmx@zczm_jc t, t_kcsm@zczm_jc a
         Where lrrq1 < trunc(Date '$end')
           And (tslsh Is Null Or Exists
                (Select tslsh
                   From t_tshz@zczm_jc
                  Where tslsh = t.tslsh
                    And (cwqrrq >= trunc(Date '$start') Or cwqrrq Is Null)))
           And t.id = a.id
         Group By dh, pch, tslsh, ywbmbh, kfbh";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);
						$sql_gc = "Insert Into t_month_fhgzjsb
        (ywbmbh, kfbh, dh, dm, gzpch, gzrq, zmy, zsy, zcb, tslsh, jsrq, bz, jslx, ZDQH,
         zcs)
        Select ywbmbh, kfbh, dh, (Select dm From t_dm@zczm_jc Where dh = t.dh) As mc, gzpch,
               Min(gzrq),
               Sum((djcs * djdj) - (ysdj * yscs)) As zmy,
               ROUND(Sum(((djcs * djdj * djzk / 100) - (ysdj * yscs * yszk / 100))), 2) As zsy,
               Sum(0) As zcb, tslsh,
               (Select cwqrrq From t_tshz@zczm_jc Where tslsh = t.tslsh) As jsrq, Min(bz), a.jslx,
               T.ZDQH, Sum(djcs - yscs)
          From t_fhgz@zczm_jc t, t_kcsm@zczm_jc a
         Where a.id = t.id
           And (tslsh Is Null Or Exists
                (Select tslsh
                   From t_tshz@zczm_jc
                  Where tslsh = t.tslsh
                    And (cwqrrq >= trunc(Date '$start') Or cwqrrq Is Null)) And
                gzrq < trunc(Date '$end'))
           And gzrq < trunc(Date '$end')
         Group By dh, ywbmbh, kfbh, gzpch, tslsh, a.jslx, T.ZDQH";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);
						//2018-09-25 edit by zhang
						$sql_gc = "Update t_month_fhjsb b
			 Set ghdw = (Select ghdwh
								From t_jczdml@zczm_jc a
								Where a.zdqh = b.zdqh
								And a.zdxh = b.zdxh)";
								//echo $sql_gc;exit;
						$res = $ora->query($sql_gc);
						$sql_gc = "Update t_month_fhjsb b Set ghdwmc = (Select mc From t_ghdw@zczm_jc Where bh = b.ghdw)";
						$res = $ora->query($sql_gc);
						$sql_gc = "Update t_month_xtjsb b
			 Set ghdw = (Select ghdwh
											From t_jczdml@zczm_jc a
										 Where a.zdqh = b.zdqh
											 And a.zdxh = b.zdxh)";
						$res = $ora->query($sql_gc);
						$sql_gc = "Update t_month_xtjsb b Set ghdwmc = (Select mc From t_ghdw@zczm_jc Where bh = b.ghdw)";
						$res = $ora->query($sql_gc);

		
						//过程二
						$sql_gc = "Delete From T_MONTH_HZB_NIAN_TMP_ZDQH";
						$res = $ora->query($sql_gc);
						$sql_gc = "Delete From T_MONTH_HZB_NIAN_ZDQH";
						$res = $ora->query($sql_gc);
						$sql_gc = "Delete From T_MONTH_HZB_NIAN";
						$res = $ora->query($sql_gc);
						$sql_gc = "Insert Into T_MONTH_HZB_NIAN_TMP_ZDQH
        (lx, bh, jfmy, jfsy, jfcb, bb, YWBMBH, ZDQH)
        Select 'DH', GHDWH, Sum(my), Sum(Sys), Sum(sys), 'DH', YWBMBH, SUBSTR(ZDQH, 1, 4)
          From T_MONTH_DHJSB
         Where SHRQ >= Date '$start'
           And SHRQ < Date '$end'
         Group By YWBMBH, GHDWH, SUBSTR(ZDQH, 1, 4)";
		 //echo $sql_gc;exit;
						$res = $ora->query($sql_gc);
						$sql_gc = "Insert Into T_MONTH_HZB_NIAN_TMP_ZDQH
        (lx, bh, jfmy, jfsy, jfcb, bb, YWBMBH, ZDQH)
        Select 'DH', GHDWH, 0, Sum(Sys), Sum(sys), 'MF', YWBMBH, SUBSTR(ZDQH, 1, 4)
          From T_MONTH_DHJSB_mf
         Where SHRQ >= Date '$start'
           And SHRQ < Date '$end'
         Group By YWBMBH, GHDWH, SUBSTR(ZDQH, 1, 4)";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);
						$sql_gc = "Insert Into T_MONTH_HZB_NIAN_TMP_ZDQH
        (lx, bh, jfmy, jfsy, jfcb, bb, YWBMBH, ZDQH)
        Select 'DH', GHDWH, Sum(Zmy), Sum(ZSY), Sum(ZSY), 'JT', YWBMBH, SUBSTR(ZDQH, 1, 4)
          From T_MONTH_JTJSB
         Where DBRQ >= Date '$start'
           And DBRQ < Date '$end'
         Group By GHDWH, YWBMBH, SUBSTR(ZDQH, 1, 4)";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);
						$sql_gc = "Insert Into T_MONTH_HZB_NIAN_TMP_ZDQH
        (lx, bh, jSmy, jSsy, jscb, bb, YWBMBH, ZDQH)
        Select 'DH', GHDWH, Sum(my), Sum(Sys), Sum(Sys), 'DH', YWBMBH, SUBSTR(ZDQH, 1, 4)
          From T_MONTH_DHJSB
         Where JSRQ >= Date '$start'
           And JSRQ < Date '$end'
         Group By GHDWH, YWBMBH, SUBSTR(ZDQH, 1, 4)";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);
						$sql_gc = "Insert Into T_MONTH_HZB_NIAN_TMP_ZDQH
        (lx, bh, jSmy, jSsy, jscb, bb, YWBMBH, ZDQH)
        Select 'DH', GHDWH, 0, Sum(Sys), Sum(Sys), 'MF', YWBMBH, SUBSTR(ZDQH, 1, 4)
          From t_month_dhjsb_mf
         Where JSRQ >= Date '$start'
           And JSRQ < Date '$end'
         Group By GHDWH, YWBMBH, SUBSTR(ZDQH, 1, 4)";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);
						$sql_gc = "Insert Into T_MONTH_HZB_NIAN_TMP_ZDQH
        (lx, bh, jsmy, jssy, jscb, bb, YWBMBH, ZDQH)
        Select 'DH', GHDWH, Sum(Zmy), Sum(ZSY), Sum(zsy), 'JT', YWBMBH, SUBSTR(ZDQH, 1, 4)
          From T_MONTH_JTJSB
         Where JSRQ >= Date '$start'
           And JSRQ < Date '$end'
         Group By GHDWH, YWBMBH, SUBSTR(ZDQH, 1, 4)";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);
						$sql_gc = "Insert Into T_MONTH_HZB_NIAN_TMP_ZDQH
        (lx, YWBMBH, bh, qcwjmy, qcwjsy, qcwjcb, bb, ZDQH)
        Select 'DH', YWBMBH, GHDWH, Sum(my), Sum(Sys), Sum(Sys), 'DH', SUBSTR(ZDQH, 1, 4)
          From T_MONTH_DHJSB
         Where shrq < Date '$start'
           And (jsrq Is Null Or jsrq >= Date '$start')
         Group By YWBMBH, GHDWH, SUBSTR(ZDQH, 1, 4)";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);
						
						$sql_gc = "Insert Into T_MONTH_HZB_NIAN_TMP_ZDQH
        (lx, YWBMBH, bh, qcwjmy, qcwjsy, qcwjcb, bb, ZDQH)
        Select 'DH', YWBMBH, GHDWH, Sum(Zmy), Sum(ZSY), Sum(ZSY), 'JT', SUBSTR(ZDQH, 1, 4)
          From T_MONTH_JTJSB
         Where DBrq < Date '$start'
           And (jsrq Is Null Or jsrq >= Date '$start')
         Group By YWBMBH, GHDWH, SUBSTR(ZDQH, 1, 4)";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);

						$sql_gc = "Insert Into T_MONTH_HZB_NIAN_TMP_ZDQH
        (lx, YWBMBH, bh, qcwjmy, qcwjsy, qcwjcb, bb, ZDQH)
        Select 'DH', YWBMBH, GHDWH, 0, Sum(Sys), Sum(Sys), 'MF', SUBSTR(ZDQH, 1, 4)
          From t_month_dhjsb_mf
         Where shrq < Date '$start'
           And (JSRQ Is Null Or jsrq >= Date '$start')
         Group By YWBMBH, GHDWH, SUBSTR(ZDQH, 1, 4)";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);
						$sql_gc = "Insert Into T_MONTH_HZB_NIAN_TMP_ZDQH
        (lx, YWBMBH, bh, qmwjmy, qmwjsy, qmwjcb, bb, ZDQH)
        Select 'DH', YWBMBH, GHDWH, Sum(my), Sum(Sys), Sum(Sys), 'DH', SUBSTR(ZDQH, 1, 4)
          From T_MONTH_DHJSB
         Where shrq < Date '$end'
           And (jsrq Is Null Or jsrq >= Date '$end')
         Group By YWBMBH, GHDWH, SUBSTR(ZDQH, 1, 4)";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);
						
						$sql_gc = "Insert Into T_MONTH_HZB_NIAN_TMP_ZDQH
        (lx, YWBMBH, bh, qmwjmy, qmwjsy, qmwjcb, bb, ZDQH)
        Select 'DH', YWBMBH, GHDWH, 0, Sum(Sys), Sum(Sys), 'MF', SUBSTR(ZDQH, 1, 4)
          From t_month_dhjsb_mf
         Where shrq < Date '$end'
           And (JSRQ Is Null Or jsrq >= Date '$end')
         Group By YWBMBH, GHDWH, SUBSTR(ZDQH, 1, 4)";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);
						
						$sql_gc = "Insert Into T_MONTH_HZB_NIAN_TMP_ZDQH
        (lx, YWBMBH, bh, qmwjmy, qmwjsy, qmwjcb, bb, ZDQH)
        Select 'DH', YWBMBH, GHDWH, Sum(Zmy), Sum(ZSY), Sum(ZSY), 'JT', SUBSTR(ZDQH, 1, 4)
          From T_MONTH_JTJSB
         Where jsrq Is Null
            Or jsrq >= Date '$end'
         Group By YWBMBH, GHDWH, SUBSTR(ZDQH, 1, 4)";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);
						$sql_gc = "Insert Into T_MONTH_HZB_NIAN_TMP_ZDQH
        (lx, bh, jfmy, jfsy, jfcb, bb, YWBMBH, ZDQH)
        Select 'FH', DH, Sum(Zmy), Sum(ZSY), Sum(cbj), 'FH', YWBMBH, SUBSTR(ZDQH, 1, 4)
          From T_MONTH_FHJSB
         Where DBRQ >= Date '$start'
           And DBRQ < Date '$end'
         Group By DH, YWBMBH, SUBSTR(ZDQH, 1, 4)";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);
						$sql_gc = "Insert Into T_MONTH_HZB_NIAN_TMP_ZDQH
        (lx, bh, jfmy, jfsy, jfcb, bb, YWBMBH, ZDQH)
        Select 'FH', dH, Sum(Zmy), Sum(ZSY), Sum(zcb), 'XT', YWBMBH, SUBSTR(ZDQH, 1, 4)
          From T_MONTH_xTJSB
         Where THRQ >= Date '$start'
           And THRQ < Date '$end'
         Group By dh, YWBMBH, SUBSTR(ZDQH, 1, 4)";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);
						$sql_gc = "Insert Into T_MONTH_HZB_NIAN_TMP_ZDQH
        (lx, bh, jfmy, jfsy, jfcb, bb, YWBMBH, ZDQH)
        Select 'FH', dH, Sum(Zmy), Sum(ZSY), Sum(zcb), 'GZ', YWBMBH, SUBSTR(ZDQH, 1, 4)
          From T_MONTH_fHgzJSB
         Where gzRQ >= Date '$start'
           And gzRQ < Date '$end'
         Group By dh, YWBMBH, SUBSTR(ZDQH, 1, 4)";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);
						$sql_gc = "Insert Into T_MONTH_HZB_NIAN_TMP_ZDQH
        (lx, bh, jsmy, jssy, jscb, bb, YWBMBH, ZDQH)
        Select 'FH', DH, Sum(Zmy), Sum(ZSY), Sum(cbj), 'FH', YWBMBH, SUBSTR(ZDQH, 1, 4)
          From T_MONTH_FHJSB
         Where jsRQ >= Date '$start'
           And jsRQ < Date '$end'
         Group By DH, YWBMBH, SUBSTR(ZDQH, 1, 4)";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);
						$sql_gc = "Insert Into T_MONTH_HZB_NIAN_TMP_ZDQH
        (lx, bh, jsmy, jssy, jscb, bb, YWBMBH, ZDQH)
        Select 'FH', dH, Sum(Zmy), Sum(ZSY), Sum(zcb), 'XT', YWBMBH, SUBSTR(ZDQH, 1, 4)
          From T_MONTH_xTJSB
         Where jsRQ >= Date '$start'
           And jsRQ < Date '$end'
         Group By dh, YWBMBH, SUBSTR(ZDQH, 1, 4)";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);
						
						
						$sql_gc = "Insert Into T_MONTH_HZB_NIAN_TMP_ZDQH
        (lx, bh, jsmy, jssy, jscb, bb, YWBMBH, ZDQH)
        Select 'FH', dH, Sum(Zmy), Sum(ZSY), Sum(zcb), 'GZ', YWBMBH, SUBSTR(ZDQH, 1, 4)
          From T_MONTH_fhgzJSB
         Where jsRQ >= Date '$start'
           And jsRQ < Date '$end'
         Group By dh, YWBMBH, SUBSTR(ZDQH, 1, 4)";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);
						
						$sql_gc = "Insert Into T_MONTH_HZB_NIAN_TMP_ZDQH
        (lx, bh, qcwjmy, qcwjsy, qcwjcb, bb, YWBMBH, ZDQH)
        Select 'FH', dH, Sum(Zmy), Sum(ZSY), Sum(cbj), 'FH', YWBMBH, SUBSTR(ZDQH, 1, 4)
          From T_MONTH_fHJSB
         Where dbrq < Date '$start'
           And (tslsh Is Null Or jsRQ >= Date '$start')
         Group By dh, YWBMBH, SUBSTR(ZDQH, 1, 4)";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);
						$sql_gc = "Insert Into T_MONTH_HZB_NIAN_TMP_ZDQH
        (lx, bh, qcwjmy, qcwjsy, qcwjcb, bb, YWBMBH, ZDQH)
        Select 'FH', dH, Sum(Zmy), Sum(ZSY), Sum(zcb), 'XT', YWBMBH, SUBSTR(ZDQH, 1, 4)
          From T_MONTH_xTJSB
         Where thrq < Date '$start'
           And (tslsh Is Null Or jsRQ >= Date '$start')
         Group By dh, YWBMBH, SUBSTR(ZDQH, 1, 4)";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);
						$sql_gc = "Insert Into T_MONTH_HZB_NIAN_TMP_ZDQH
        (lx, bh, qcwjmy, qcwjsy, qcwjcb, bb, YWBMBH, ZDQH)
        Select 'FH', DH, Sum(Zmy), Sum(ZSY), Sum(zcb), 'GZ', YWBMBH, SUBSTR(ZDQH, 1, 4)
          From T_MONTH_FHGZJSB
         Where gzrq < Date '$start'
           And (tslsh Is Null Or jsRQ >= Date '$start')
         Group By DH, YWBMBH, SUBSTR(ZDQH, 1, 4)";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);
						$sql_gc = "Insert Into T_MONTH_HZB_NIAN_TMP_ZDQH
        (lx, bh, qmwjmy, qmwjsy, qmwjcb, bb, YWBMBH, ZDQH)
        Select 'FH', dH, Sum(Zmy), Sum(ZSY), Sum(cbj), 'FH', YWBMBH, SUBSTR(ZDQH, 1, 4)
          From T_MONTH_fHJSB
         Where tslsh Is Null
            Or jsRQ >= Date '$end'
         Group By dh, YWBMBH, SUBSTR(ZDQH, 1, 4)";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);
						$sql_gc = "Insert Into T_MONTH_HZB_NIAN_TMP_ZDQH
        (lx, bh, qmwjmy, qmwjsy, qmwjcb, bb, YWBMBH, ZDQH)
        Select 'FH', dH, Sum(Zmy), Sum(ZSY), Sum(zcb), 'XT', YWBMBH, SUBSTR(ZDQH, 1, 4)
          From T_MONTH_xTJSB
         Where tslsh Is Null
            Or jsRQ >= Date '$end'
         Group By dh, YWBMBH, SUBSTR(ZDQH, 1, 4)";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);
						$sql_gc = "Insert Into T_MONTH_HZB_NIAN_TMP_ZDQH
        (lx, bh, qmwjmy, qmwjsy, qmwjcb, bb, YWBMBH, ZDQH)
        Select 'FH', DH, Sum(Zmy), Sum(ZSY), Sum(zcb), 'GZ', YWBMBH, SUBSTR(ZDQH, 1, 4)
          From T_MONTH_FHGZJSB
         Where tslsh Is Null
            Or jsRQ >= Date '$end'
         Group By DH, YWBMBH, SUBSTR(ZDQH, 1, 4)";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);
						$sql_gc = "Insert Into T_MONTH_HZB_NIAN_ZDQH
        (LX, BH, YWBMBH, qcwjmy, qcwjsy, JFMY, JFSY, JSMY, JSSY, QMWJMY, QMWJSY, ZDQH)
        Select lx, bh, ywbmbh, Sum(nvl(qcwjmy, 0)), Sum(nvl(qcwjsy, 0)), Sum(nvl(jfmy, 0)),
               Sum(nvl(jfsy, 0)), Sum(nvl(jsmy, 0)), Sum(nvl(jssy, 0)),
               Sum(nvl(qmwjmy, 0)), Sum(nvl(qmwjsy, 0)), ZDQH
          From T_MONTH_HZB_NIAN_TMP_ZDQH
         Group By ywbmbh, lx, bh, ZDQH";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);
						$sql_gc = "Insert Into T_MONTH_HZB_NIAN
        (LX, BH, YWBMBH, qcwjmy, qcwjsy, qcwjcb, JFMY, JFSY, jfcb, JSMY, JSSY, jscb,
         QMWJMY, QMWJSY, qmwjcb)
        Select lx, bh, ywbmbh, Sum(nvl(qcwjmy, 0)), Sum(nvl(qcwjsy, 0)),
               Sum(nvl(qcwjcb, 0)), Sum(nvl(jfmy, 0)), Sum(nvl(jfsy, 0)),
               Sum(nvl(jfcb, 0)), Sum(nvl(jsmy, 0)), Sum(nvl(jssy, 0)), Sum(nvl(jscb, 0)),
               Sum(nvl(qmwjmy, 0)), Sum(nvl(qmwjsy, 0)), Sum(nvl(qmwjcb, 0))
          From T_MONTH_HZB_NIAN_TMP_ZDQH
         Group By ywbmbh, lx, bh";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);
						$sql_gc = "Update T_MONTH_HZB_NIAN_ZDQH T
       Set DMMC = (Select DM From T_DM@zczm_jc Where DH = T.BH)
     Where LX = 'FH'";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);
						
						$sql_gc = "Update T_MONTH_HZB_NIAN_ZDQH T
       Set DMMC = (Select MC From T_GHDW@zczm_jc Where BH = T.BH)
     Where LX = 'DH'";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);
						$sql_gc = "Update T_MONTH_HZB_NIAN T
       Set DMMC = (Select DM From T_DM@zczm_jc Where DH = T.BH)
     Where LX = 'FH'";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);
						$sql_gc = "Update T_MONTH_HZB_NIAN T
       Set DMMC = (Select MC From T_GHDW@zczm_jc Where BH = T.BH)
     Where LX = 'DH'";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);
						$guocheng[guocheng]=$timetype;	
						setcache("guocheng",$guocheng);
					}			
					
			}	
				
				
				if($yewuleixing=='月报'){
					//$info=getcache("jiaofu_yuebao_".$start.'_'.$end);
					if($yewubumen=='quanbu'){
						$sql="Select * From t_Month_Hzb_Nian Where ywbmbh in ($admin_username)";
					}else{
						$sql="Select * From t_Month_Hzb_Nian Where ywbmbh='$admin_username'";
					}
					//echo $sql;exit;
						//教辅月报<>
						$row=$ora->query($sql);
						//print_r($row);exit;
						
					
					if($daochu && $daochu=='daochu'){
						$fileName = '教辅月报';
						$total = count($row);
$title = array('YWBMBH','LX','BH','DMMC','QCWJMY','QCWJSY','QCWJCB','JFMY','JFSY','JFCB','JSMY','JSSY','JSCB','QMWJMY','QMWJSY','QMWJCB','SYLX','BB','MINDATE','MAXDATE','NY','BJSMY','BJSSY');
						//$this->articleAccessLog($sql,$fileName,$title,$total,$yewugongsi);
						if($_SESSION['roleid']==26 && $daochulx =='导出到服务器指定位置'){
							$this->yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username,$timetype);
						}elseif($_SESSION['roleid']==26 && $daochulx =='从浏览器中下载'){
							$this->articleAccessLog($sql,$fileName,$title,$total,$yewugongsi);
						}

						//$this->yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username);
					}

					
					
				}elseif($yewuleixing=='流水-到货明细'){
					//$info=getcache("jiaofu_daohuomingxi_".$start.'_'.$end);
					//$sql="Select * From t_month_dhjsb@zczm_jc Where ywbmbh<>'$admin_username' And shrq>=Date'$start' and shrq< Date'$end'";
					if($yewubumen=='quanbu'){
						$sql="Select YWBMBH,GHDWH,MC,DHPCH,YSDJ,SSDH,ZDQH,ZDXH,SM,DJ,JXSSSL,MY,SYS,ZDPCH,JSLX,ZDPCH1,TSLSH,YSDBRQ,JCDRQ,KQBJ,JSGHDWH,YSGHDWH,FLOWID_DHMX,to_char(t_month_dhjsb.SHRQ, 'YYYY-MM-DD HH24:MI:SS') as SHRQ ,to_char(t_month_dhjsb.YSRQ, 'YYYY-MM-DD HH24:MI:SS') as YSRQ,to_char(t_month_xtjsb.JSRQ, 'YYYY-MM-DD HH24:MI:SS') as JSRQ From t_month_dhjsb Where ywbmbh in ($admin_username) And shrq>=Date'$start' and shrq< Date'$end'";
					}else{
						$sql="Select YWBMBH,GHDWH,MC,DHPCH,YSDJ,SSDH,ZDQH,ZDXH,SM,DJ,JXSSSL,MY,SYS,ZDPCH,JSLX,ZDPCH1,TSLSH,YSDBRQ,JCDRQ,KQBJ,JSGHDWH,YSGHDWH,FLOWID_DHMX,to_char(t_month_dhjsb.SHRQ, 'YYYY-MM-DD HH24:MI:SS') as SHRQ ,to_char(t_month_dhjsb.YSRQ, 'YYYY-MM-DD HH24:MI:SS') as YSRQ,to_char(t_month_xtjsb.JSRQ, 'YYYY-MM-DD HH24:MI:SS') as JSRQ From t_month_dhjsb Where ywbmbh='$admin_username' And shrq>=Date'$start' and shrq< Date'$end'";
					}

					//if(empty($info)){
						//教辅 流水-到货明细<>
						
						$row=$ora->query($sql);
						//echo '教辅-流水-到货明细查询';
						//print_r($row);//exit;
						//$info[jiaofudaohuomingxi]=$row;  
						
						//setcache("jiaofu_daohuomingxi_".$start.'_'.$end,$info);
					//}else{
					//	$row = $info[jiaofudaohuomingxi];
					//}
					if($daochu && $daochu=='daochu'){
						
						$fileName = '教辅到货明细';
						$total = count($row);
						$title = array('YWBMBH','GHDWH','MC','DHPCH','SHRQ','YSDJ','SSDH','ZDQH','ZDXH','SM','DJ','JXSSSL','MY','SYS','ZDPCH','JSRQ','JSLX','ZDPCH1','TSLSH','YSRQ','YSDBRQ','JCDRQ','KQBJ','JSGHDWH','YSGHDWH','FLOWID_DHMX');
						//$this->articleAccessLog($sql,$fileName,$title,$total,$yewugongsi);
						if($_SESSION['roleid']==26 && $daochulx =='导出到服务器指定位置'){
							$this->yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username,$timetype);
						}elseif($_SESSION['roleid']==26 && $daochulx =='从浏览器中下载'){
							$this->articleAccessLog($sql,$fileName,$title,$total,$yewugongsi);
						}
						//$this->yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username);
					}

					

				}elseif($yewuleixing=='流水-进退明细'){
					//$info=getcache("jiaofu_jintuimingxi_".$start.'_'.$end);
					//$sql="Select * From t_month_jtjsb@zczm_jc Where ywbmbh<>'$admin_username' And dbrq>=Date'$start' and dbrq< Date'$end'";
					if($yewubumen=='quanbu'){
						$sql="Select YWBMBH,GHDWH,MC,ID,SM,DJ,ZMY,ZSY,BZ,YWPCH,ZDPCH,JSRQ,JSLX,ZDQH,ZDXH,ZDPCH1,TSLSH,NF,NFHRQ,NJSRQ,JSGHDWH,YSGHDWH,ZCS,to_char(t_month_jtjsb.DBRQ, 'YYYY-MM-DD HH24:MI:SS') as DBRQ
 From t_month_jtjsb Where ywbmbh in ($admin_username) And dbrq>=Date'$start' and dbrq< Date'$end'";
					}else{
						$sql="Select YWBMBH,GHDWH,MC,ID,SM,DJ,ZMY,ZSY,BZ,YWPCH,ZDPCH,JSRQ,JSLX,ZDQH,ZDXH,ZDPCH1,TSLSH,NF,NFHRQ,NJSRQ,JSGHDWH,YSGHDWH,ZCS,to_char(t_month_jtjsb.DBRQ, 'YYYY-MM-DD HH24:MI:SS') as DBRQ
 From t_month_jtjsb Where ywbmbh='$admin_username' And dbrq>=Date'$start' and dbrq< Date'$end'";
					}
					//echo $sql;exit;

					//if(empty($info)){
						//教辅 流水-进退明细<>
						$row=$ora->query($sql);
						//echo '教辅-流水-进退明细查询';
						//print_r($row);exit;
						//$info[jiaofujintuimingxi]=$row;  
						
						//setcache("jiaofu_jintuimingxi_".$start.'_'.$end,$info);
					//}else{
					//	$row = $info[jiaofujintuimingxi];
					//}
					if($daochu && $daochu=='daochu'){
						
						$fileName = '教辅进退明细';
						$total = count($row);
						$title = array('YWBMBH','GHDWH','MC','DBRQ','ID','SM','DJ','ZMY','ZSY','BZ','YWPCH','ZDPCH','JSRQ','JSLX','ZDQH','ZDXH','ZDPCH1','TSLSH','NF','NFHRQ','NJSRQ','JSGHDWH','YSGHDWH','ZCS');
						//$this->articleAccessLog($sql,$fileName,$title,$total,$yewugongsi);
						if($_SESSION['roleid']==26 && $daochulx =='导出到服务器指定位置'){
							$this->yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username,$timetype);
						}elseif($_SESSION['roleid']==26 && $daochulx =='从浏览器中下载'){
							$this->articleAccessLog($sql,$fileName,$title,$total,$yewugongsi);
						}
						//$this->yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username);

					}

					
					
				}elseif($yewuleixing=='流水-出版社免费让利明细'){//2018-07-31 edit by zhang 发现的问题是这个表中没有数据，跟孟老师沟通了，孟老师说这个表 有时候就是没有数据！！！！自求多福吧，哎，无奈
					//$info=getcache("jiaofu_ranglimingxi_".$start.'_'.$end);
					//$sql="Select * From t_month_dhjsb_mf@zczm_jc Where ywbmbh<>'$admin_username' And shrq>=Date'$start' and shrq< Date'$end'";
					if($yewubumen=='quanbu'){
						$sql="Select YWBMBH,GHDWH,MC,DHPCH,SHRQ,YSDJ,SSDH,ZDQH,ZDXH,SM,DJ,JXSSSL,MY,SYS,ZDPCH,JSRQ,JSLX,JSGHDWH,YSGHDWH,TSLSH
 From t_month_dhjsb_mf Where ywbmbh in ($admin_username) And shrq>=Date'$start' and shrq< Date'$end'";
					}else{
						$sql="Select YWBMBH,GHDWH,MC,DHPCH,SHRQ,YSDJ,SSDH,ZDQH,ZDXH,SM,DJ,JXSSSL,MY,SYS,ZDPCH,JSRQ,JSLX,JSGHDWH,YSGHDWH,TSLSH
 From t_month_dhjsb_mf Where ywbmbh='$admin_username' And shrq>=Date'$start' and shrq< Date'$end'";
					}
					//echo $sql;exit;
					//if(empty($info)){
						
						//教辅 流水-出版社免费让利明细<>
						
						$row=$ora->query($sql);
						//echo '教辅-流水-出版社免费让利明细';
						//print_r($row);exit;//先die吧，有数据了再放开，看看都有啥字段
						//$info[jiaofuranglimingxi]=$row;  
						
						//setcache("jiaofu_ranglimingxi_".$start.'_'.$end,$info);
					//}else{
					//	$row = $info[jiaofuranglimingxi];print_r($row);exit;
					//}
					
					if($daochu && $daochu=='daochu'){
						$fileName = '教辅出版社免费让利明细';
						$total = count($row);
						//$title = array('YWBMBH','GHDWH','MC','DBRQ','ID','SM','DJ','ZMY','ZSY','BZ','YWPCH','ZDPCH','JSRQ','JSLX','ZDQH','ZDXH','ZDPCH1','TSLSH','NF','NFHRQ','NJSRQ','JSGHDWH','YSGHDWH','ZSC');
						$title = array('YWBMBH','GHDWH','MC','DHPCH','SHRQ','YSDJ','SSDH','ZDQH','ZDXH','SM','DJ','JXSSSL','MY','SYS','ZDPCH','JSRQ','JSLX','JSGHDWH','YSGHDWH','TSLSH');
						//$this->articleAccessLog($sql,$fileName,$title,$total,$yewugongsi);
						if($_SESSION['roleid']==26 && $daochulx =='导出到服务器指定位置'){
							$this->yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username,$timetype);
						}elseif($_SESSION['roleid']==26 && $daochulx =='从浏览器中下载'){
							$this->articleAccessLog($sql,$fileName,$title,$total,$yewugongsi);
						}
						//$this->yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username);
					}


				}elseif($yewuleixing=='流水-销退明细'){
					//$info=getcache("jiaofu_weijiemingxi_".$start.'_'.$end);
					if($yewubumen=='quanbu'){
						$sql="Select ghdw,ghdwmc,YWBMBH,KFBH,DH,DM,PCH,ZMY,ZSY,ZCB,TSLSH,JSLX,ZDQH,NF,NFHRQ,NJSRQ,JB,CWQRPC,ZCS,DJ,ID,BB,SM,ISBN,CBNY,to_char(t_month_xtjsb.THRQ, 'YYYY-MM-DD HH24:MI:SS') as THRQ,to_char(t_month_xtjsb.JSRQ, 'YYYY-MM-DD HH24:MI:SS') as JSRQ From t_month_xtjsb Where ywbmbh in ($admin_username) And thrq>=Date'$start' and thrq< Date'$end'";
					}else{
						$sql="Select ghdw,ghdwmc,YWBMBH,KFBH,DH,DM,PCH,ZMY,ZSY,ZCB,TSLSH,JSLX,ZDQH,NF,NFHRQ,NJSRQ,JB,CWQRPC,ZCS,DJ,ID,BB,SM,ISBN,CBNY,to_char(t_month_xtjsb.THRQ, 'YYYY-MM-DD HH24:MI:SS') as THRQ,to_char(t_month_xtjsb.JSRQ, 'YYYY-MM-DD HH24:MI:SS') as JSRQ From t_month_xtjsb Where ywbmbh='$admin_username' And thrq>=Date'$start' and thrq< Date'$end'";
					}
					
					//echo $sql;exit;
					
						$row=$ora->query($sql);
						//echo '教辅-流水-销退明细';
						//print_r($row);//exit;
						
					if($daochu && $daochu=='daochu'){
						$fileName = '教辅销退明细';
						$total = count($row);
						$title = array('GHDW','GHDWMC','YWBMBH','KFBH','DH','DM','PCH','THRQ','ZMY','ZSY','ZCB','TSLSH','JSRQ','JSLX','ZDQH','NF','NFHRQ','NJSRQ','JB','CWQRPC','ZCS','DJ','ID','BB','SM','ISBN','CBNY');
						//$this->articleAccessLog($sql,$fileName,$title,$total,$yewugongsi);
						if($_SESSION['roleid']==26 && $daochulx =='导出到服务器指定位置'){
							$this->yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username,$timetype);
						}elseif($_SESSION['roleid']==26 && $daochulx =='从浏览器中下载'){
							$this->articleAccessLog($sql,$fileName,$title,$total,$yewugongsi);
						}
						//$this->yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username);
					}


					
				}elseif($yewuleixing=='流水-基层店免费让利明细'){  //2018-07-31 edit by zhang 发现的问题是这个表中没有数据，跟孟老师沟通了有时候就是没有数据！！！！自求多福
					//$info=getcache("jiaofu_jcranglimingxi_".$start.'_'.$end);
					if($yewubumen=='quanbu'){
						$sql="Select * From t_month_fhgzjsb Where ywbmbh in ($admin_username) And gzrq>=Date'$start' and gzrq< Date'$end'";
					}else{
						$sql="Select * From t_month_fhgzjsb Where ywbmbh='$admin_username' And gzrq>=Date'$start' and gzrq< Date'$end'";
					}
					//echo $sql;exit;
					
					//if(empty($info)){
						//教辅 流水-基层店免费让利明细<>
						
						$row=$ora->query($sql);
						//echo '教辅-流水-基层店免费让利明细';
						//print_r($row);exit; 
						//$info[jiaofujcranglimingxi]=$row;  
						
						//setcache("jiaofu_jcranglimingxi_".$start.'_'.$end,$info);
					//}else{
					//	$row = $info[jiaofujcranglimingxi];
					//}
					
					if($daochu && $daochu=='daochu'){
						//echo '这个还没有给表,自己看导出的数据字段';exit;

						$fileName = '教辅基层店免费让利明细';
						$total = count($row);
						$title = array('YWBMBH','KFBH','DH','DM','GZPCH','GZRQ','ZMY','ZSY','ZCB','TSLSH','JSRQ','BZ','JSLX','ZDQH','NF','NFHRQ','NJSRQ','JB','ZCS');
						//$this->articleAccessLog($sql,$fileName,$title,$total,$yewugongsi);
						if($_SESSION['roleid']==26 && $daochulx =='导出到服务器指定位置'){
							$this->yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username,$timetype);
						}elseif($_SESSION['roleid']==26 && $daochulx =='从浏览器中下载'){
							$this->articleAccessLog($sql,$fileName,$title,$total,$yewugongsi);
						}
						//$this->yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username);
					}

					
				}elseif($yewuleixing=='流水-发货明细'){
					//$info=getcache("jiaofu_fahuomingxi_".$start.'_'.$end);
					//$sql="Select * From t_month_fhjsb@zczm_jc Where ywbmbh<>'$admin_username' And dbrq>=Date'$start' and dbrq< Date'$end'";
					
					if($yewubumen=='quanbu'){
					$sql="Select cbny,ghdw,ghdwmc,YWBMBH,DH,MC,PFPCH,BZ,ZMY,ZSY,CBJ,TSLSH,JSRQ,SM,DJ,ZDQH,ZDXH,KFBH,JSLX,NF,NFHRQ,NJSRQ,JB,CWQRPC,YSDBRQ,ZCS,to_char(t_month_fhjsb.DBRQ, 'YYYY-MM-DD HH24:MI:SS') as DBRQ
					,to_char(t_month_fhjsb.PFRQ, 'YYYY-MM-DD HH24:MI:SS') as PFRQ From t_month_fhjsb Where ywbmbh in ($admin_username) And dbrq>=Date'$start' and dbrq< Date'$end'";
					}else{
					$sql="Select cbny,ghdw,ghdwmc,YWBMBH,DH,MC,PFPCH,BZ,ZMY,ZSY,CBJ,TSLSH,JSRQ,SM,DJ,ZDQH,ZDXH,KFBH,JSLX,NF,NFHRQ,NJSRQ,JB,CWQRPC,YSDBRQ,ZCS,to_char(t_month_fhjsb.DBRQ, 'YYYY-MM-DD HH24:MI:SS') as DBRQ
					,to_char(t_month_fhjsb.PFRQ, 'YYYY-MM-DD HH24:MI:SS') as PFRQ From t_month_fhjsb Where ywbmbh='$admin_username' And dbrq>=Date'$start' and dbrq< Date'$end'";
					}
					
					
					//if(empty($info)){
						//教辅 流水-发货明细<>
						$row=$ora->query($sql);
						//echo '教辅-流水-发货明细';
						//print_r($row);exit;
						//$info[jiaofufahuomingxi]=$row;  
						
						//setcache("jiaofu_fahuomingxi_".$start.'_'.$end,$info);
					//}else{
					//	$row = $info[jiaofufahuomingxi];
					//}
					if($daochu && $daochu=='daochu'){

						$fileName = '教辅发货明细';
						$total = count($row);
						$title = array('CBNY','GHDW','GHDWMC','YWBMBH','DH','MC','PFPCH','BZ','DBRQ','PFRQ','ZMY','ZSY','CBJ','TSLSH','JSRQ','SM','DJ','ZDQH','ZDXH','KFBH','JSLX','NF','NFHRQ','NJSRQ','JB','CWQRPC','YSDBRQ','ZCS');
						//$this->articleAccessLog($sql,$fileName,$title,$total,$yewugongsi);
						if($_SESSION['roleid']==26 && $daochulx =='导出到服务器指定位置'){
							$this->yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username,$timetype);
						}elseif($_SESSION['roleid']==26 && $daochulx =='从浏览器中下载'){
							$this->articleAccessLog($sql,$fileName,$title,$total,$yewugongsi);
						}
						//$this->yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username);
					}

					
				}elseif($yewuleixing=='库存明细'){ //**如果查八月份的库存明细，开始日期应该是 九月一号
					$dangyue = dy_month(); //当月第一天
					$start = date('Y-m-d',$dangyue['start_time']);
					if($yewubumen=='quanbu'){
						$sql="Select ywbmbh,Id,(Select sm From t_kcsm@zczm_jc Where Id=t.id)As sm,
		 (Select dj From t_kcsm@zczm_jc Where Id=t.id)As dj,
		 		(Select min(mc) From t_bb@zczm_jc Where  bh=(Select bb From t_kcsm@zczm_jc Where Id=t.id))As bb,
				kccs,(kccs*(Select dj From t_kcsm@zczm_jc Where Id=t.id))As my,				
		 (kccs*(Select dj From t_kcsm@zczm_jc Where Id=t.id)*(Select pjzk From t_kcsm@zczm_jc Where Id=t.id)/100)As Sys,
		  (Select dm From t_dm@zczm_jc Where dh=t.kfbh)As kf,
		  (Select min(dm) From t_dm@zczm_jc Where dh=t.cybh)As wl,
			(Select mc From t_lb@zczm_jc Where bh=(Select  (lb) From t_kcsm@zczm_jc Where Id=t.id))as fl,
			hw,
			 (Select mc From t_ghdw@zczm_jc Where bh=(Select min(ghdwh) From t_dhls_jx@zczm_jc Where Id=t.id))As ghdw, 
			 (Select Name From t_user@zczm_jc Where usr_id=(Select ywryh From t_kcsm@zczm_jc Where Id=t.id))As ywy,(Select isbn From t_kcsm@zczm_jc Where Id=t.id)As  isbn,
			 (Select min(bc) From t_kcsm@zczm_jc Where Id=t.id)as bc,
			 (Select bianzh From t_kcsm@zczm_jc Where Id=t.id)As bianzhe,(Select nian From t_kcsm@zczm_jc Where Id=t.id)  As nian
			   From t_Hd_Kcsl_Bf@zczm_jc t Where kccs<>0  And ywbmbh in ($admin_username) ";
					}else{
						$sql="Select ywbmbh,Id,(Select sm From t_kcsm@zczm_jc Where Id=t.id)As sm,
		 (Select dj From t_kcsm@zczm_jc Where Id=t.id)As dj,
		 		(Select min(mc) From t_bb@zczm_jc Where  bh=(Select bb From t_kcsm@zczm_jc Where Id=t.id))As bb,
				kccs,(kccs*(Select dj From t_kcsm@zczm_jc Where Id=t.id))As my,				
		 (kccs*(Select dj From t_kcsm@zczm_jc Where Id=t.id)*(Select pjzk From t_kcsm@zczm_jc Where Id=t.id)/100)As Sys,
		  (Select dm From t_dm@zczm_jc Where dh=t.kfbh)As kf,
		  (Select min(dm) From t_dm@zczm_jc Where dh=t.cybh)As wl,
			(Select mc From t_lb@zczm_jc Where bh=(Select  (lb) From t_kcsm@zczm_jc Where Id=t.id))as fl,
			hw,
			 (Select mc From t_ghdw@zczm_jc Where bh=(Select min(ghdwh) From t_dhls_jx@zczm_jc Where Id=t.id))As ghdw, 
			 (Select Name From t_user@zczm_jc Where usr_id=(Select ywryh From t_kcsm@zczm_jc Where Id=t.id))As ywy,(Select isbn From t_kcsm@zczm_jc Where Id=t.id)As  isbn,
			 (Select min(bc) From t_kcsm@zczm_jc Where Id=t.id)as bc,
			 (Select bianzh From t_kcsm@zczm_jc Where Id=t.id)As bianzhe,(Select nian From t_kcsm@zczm_jc Where Id=t.id)  As nian
			   From t_Hd_Kcsl_Bf@zczm_jc t Where kccs<>0 And ywbmbh='$admin_username'";
					}
					
					
			   //echo $sql;//exit;
					
						
						$row=$ora->query($sql);
						//echo '教辅-库存明细';
						//print_r($row);exit;

					if($daochu && $daochu=='daochu'){
						$fileName = '教辅库存明细';
						$total = count($row);
						$title = array('YWBMBH','ID','SM','DJ','版别','KCCS','MY','SYS','KF','WL','分类','HW','供货单位','YWY','ISBN','版次','编者','年');
						if($_SESSION['roleid']==26 && $daochulx =='导出到服务器指定位置'){
							$this->yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username,$timetype);
						}elseif($_SESSION['roleid']==26 && $daochulx =='从浏览器中下载'){
							$this->articleAccessLog($sql,$fileName,$title,$total,$yewugongsi);
						}
					}

					
				}elseif($yewuleixing=='库存明细提前出'){ //**如果查八月份的库存明细，开始日期应该是 九月一号
					$dangyue = dy_month(); //当月第一天
					$start = date('Y-m-d',$dangyue['start_time']);
					if($yewubumen=='quanbu'){
						$sql="Select ywbmbh,Id,(Select sm From t_kcsm@zczm_jc Where Id=t.id)As sm,
		 (Select dj From t_kcsm@zczm_jc Where Id=t.id)As dj,
		 		(Select min(mc) From t_bb@zczm_jc Where  bh=(Select bb From t_kcsm@zczm_jc Where Id=t.id))As bb,
				kccs,(kccs*(Select dj From t_kcsm@zczm_jc Where Id=t.id))As my,				
		 (kccs*(Select dj From t_kcsm@zczm_jc Where Id=t.id)*(Select pjzk From t_kcsm@zczm_jc Where Id=t.id)/100)As Sys,
		  (Select dm From t_dm@zczm_jc Where dh=t.kfbh)As kf,
		  (Select min(dm) From t_dm@zczm_jc Where dh=t.cybh)As wl,
			(Select mc From t_lb@zczm_jc Where bh=(Select  (lb) From t_kcsm@zczm_jc Where Id=t.id))as fl,
			hw,
			 (Select mc From t_ghdw@zczm_jc Where bh=(Select min(ghdwh) From t_dhls_jx@zczm_jc Where Id=t.id))As ghdw, 
			 (Select Name From t_user@zczm_jc Where usr_id=(Select ywryh From t_kcsm@zczm_jc Where Id=t.id))As ywy,(Select isbn From t_kcsm@zczm_jc Where Id=t.id)As  isbn,
			 (Select min(bc) From t_kcsm@zczm_jc Where Id=t.id)as bc,
			 (Select bianzh From t_kcsm@zczm_jc Where Id=t.id)As bianzhe,(Select nian From t_kcsm@zczm_jc Where Id=t.id)  As nian
			   From t_Hd_Kcsl_Bf@zczm_jc t Where kccs<>0 And ywbmbh in ($admin_username) ";
					}else{
						$sql="Select ywbmbh,Id,(Select sm From t_kcsm@zczm_jc Where Id=t.id)As sm,
		 (Select dj From t_kcsm@zczm_jc Where Id=t.id)As dj,
		 		(Select min(mc) From t_bb@zczm_jc Where  bh=(Select bb From t_kcsm@zczm_jc Where Id=t.id))As bb,
				kccs,(kccs*(Select dj From t_kcsm@zczm_jc Where Id=t.id))As my,				
		 (kccs*(Select dj From t_kcsm@zczm_jc Where Id=t.id)*(Select pjzk From t_kcsm@zczm_jc Where Id=t.id)/100)As Sys,
		  (Select dm From t_dm@zczm_jc Where dh=t.kfbh)As kf,
		  (Select min(dm) From t_dm@zczm_jc Where dh=t.cybh)As wl,
			(Select mc From t_lb@zczm_jc Where bh=(Select  (lb) From t_kcsm@zczm_jc Where Id=t.id))as fl,
			hw,
			 (Select mc From t_ghdw@zczm_jc Where bh=(Select min(ghdwh) From t_dhls_jx@zczm_jc Where Id=t.id))As ghdw, 
			 (Select Name From t_user@zczm_jc Where usr_id=(Select ywryh From t_kcsm@zczm_jc Where Id=t.id))As ywy,(Select isbn From t_kcsm@zczm_jc Where Id=t.id)As  isbn,
			 (Select min(bc) From t_kcsm@zczm_jc Where Id=t.id)as bc,
			 (Select bianzh From t_kcsm@zczm_jc Where Id=t.id)As bianzhe,(Select nian From t_kcsm@zczm_jc Where Id=t.id)  As nian
			   From t_Hd_Kcsl_Bf@zczm_jc t Where kccs<>0 And ywbmbh='$admin_username' ";
					}
					
					
			   //echo $sql;//exit;
					
						
						$row=$ora->query($sql);
						//print_r($row);exit;

					if($daochu && $daochu=='daochu'){
						$fileName = '教辅库存明细提前出';
						$total = count($row);
						$title = array('YWBMBH','ID','SM','DJ','版别','KCCS','MY','SYS','KF','WL','分类','HW','供货单位','YWY','ISBN','版次','编者','年');
						if($_SESSION['roleid']==26 && $daochulx =='导出到服务器指定位置'){
							$this->yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username,$timetype);
						}elseif($_SESSION['roleid']==26 && $daochulx =='从浏览器中下载'){
							$this->articleAccessLog($sql,$fileName,$title,$total,$yewugongsi);
						}
					}

					
				}elseif($yewuleixing=='配发中间态'){
					$dangyue = dy_month(); //当月第一天
					$start = date('Y-m-d',$dangyue['start_time']);

					if($yewubumen=='quanbu'){
					$sql="Select Ywbmbh ,(select dm from t_dm@zczm_jc where dh=a.ywbmbh) as ywbmmc,dh,(select dm from t_dm@zczm_jc where dh=a.dh) as dm,
a.id,(select isbn from t_kcsm@zczm_jc where a.id=id) as isbn,(select sm from t_kcsm@zczm_jc where a.id=id) as sm,
(select dj from t_kcsm@zczm_jc where a.id=id) as dj,(Select round(cbny) From t_kcsm@zczm_jc Where Id=a.id)As cbny,a.pxcs,(select dj*a.pxcs from t_kcsm@zczm_jc where a.id=id) as zmy ,a.sys,a.cbj,
a.pxdh,a.bz,a.zdh,a.sylx,a.sl,a.zjxs,a.qx,a.djbh,a.kfbh,A.Bjlsh,a.cybh,A.Flowid,a.zdqh,a.zdxh,a.zdqh1,a.zdxh1,
to_char(a.pfrq, 'YYYY-MM-DD HH24:MI:SS') as pfrq From t_hd_pfmx@zczm_jc a Where bfsj>=Date'$start' and ckbj='1' and ywbmbh in ($admin_username)";
					}else{
					$sql="Select Ywbmbh ,(select dm from t_dm@zczm_jc where dh=a.ywbmbh) as ywbmmc,dh,(select dm from t_dm@zczm_jc where dh=a.dh) as dm,
a.id,(select isbn from t_kcsm@zczm_jc where a.id=id) as isbn,(select sm from t_kcsm@zczm_jc where a.id=id) as sm,
(select dj from t_kcsm@zczm_jc where a.id=id) as dj,(Select round(cbny) From t_kcsm@zczm_jc Where Id=a.id)As cbny,a.pxcs,(select dj*a.pxcs from t_kcsm@zczm_jc where a.id=id) as zmy ,a.sys,a.cbj,
a.pxdh,a.bz,a.zdh,a.sylx,a.sl,a.zjxs,a.qx,a.djbh,a.kfbh,A.Bjlsh,a.cybh,A.Flowid,a.zdqh,a.zdxh,a.zdqh1,a.zdxh1,
to_char(a.pfrq, 'YYYY-MM-DD HH24:MI:SS') as pfrq From t_hd_pfmx@zczm_jc a Where bfsj>=Date'$start' and ckbj='1' and ywbmbh='$admin_username'";
					}
					
					//echo $sql;exit;

						$row=$ora->query($sql);
						//print_r($row);exit;
					
					if($daochu && $daochu=='daochu'){
						
						$fileName = '教辅配发中间态';
						$total = count($row);
						$title = array('YWBMBH','YWBMMC','DH','DM','ID','ISBN','SM','DJ','CBNY','PXCS','ZMY','SYS','CBJ','PXDH','BZ','ZDH','SYLX','SL','ZJXS','QX','DJBH','KFBH','BJLSH','CYBH','FLOWID','ZDQH','ZDXH','ZDQH1','ZDXH1','PFRQ');
						if($_SESSION['roleid']==26 && $daochulx =='导出到服务器指定位置'){
							$this->yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username,$timetype);
						}elseif($_SESSION['roleid']==26 && $daochulx =='从浏览器中下载'){
							$this->articleAccessLog($sql,$fileName,$title,$total,$yewugongsi);
						}
					}

					
				}elseif($yewuleixing=='库房转移中间态明细'){
					//$info=getcache("jiaofu_kufangmingxi_".$start.'_'.$end);
					if($yewubumen=='quanbu'){
					$sql="Select Id ,(Select sm From t_kcsm@zczm_jc Where Id=A.id)As sm,(Select isbn From t_kcsm@zczm_jc Where Id=A.id)As  isbn,
(Select dj From t_kcsm@zczm_jc Where Id=A.id)As dj,(Select min(mc) From t_bb@zczm_jc Where bh=(Select bb From t_kcsm@zczm_jc Where Id=A.id))As bb,
		  (Select mc From t_ghdw@zczm_jc Where bh=(Select min(ghdwh) From t_dhls_jx@zczm_jc Where Id=A.id))As ghdw,(Select Name From t_user@zczm_jc Where usr_id=(Select ywryh From t_kcsm@zczm_jc Where Id=A.id))As ywy,cbny,ywlx,Sum(cs),Sum(cs*(Select dj From t_kcsm@zczm_jc Where Id=a.id))As MY,Sum(sy)As SY From (
Select YWLX,Id,(Select round(cbny) From t_kcsm@zczm_jc Where Id=t_crkls.id)As cbny,
Sum(CRKCS)CS,sum(crksy)sy From t_crkls@zczm_jc Where ywbmbh in ($admin_username) And (YWLX='YC' Or YWLX='DC' )And crkrq>=Date'2018-07-01' And crkrq < Date'$end' Group By ywlx,Id
Union All Select YWLX,Id,(Select round(cbny) From t_kcsm@zczm_jc Where Id=t_crkls.id)As cbny,Sum(-CRKCS)CS,sum(-crksy)sy From t_crkls@zczm_jc Where ywbmbh in ($admin_username) And ( YWLX='YR'Or YWLX='DR' )
And crkrq>=Date'2018-07-01' And crkrq < Date'$end' Group By ywlx,Id) A Group By Id,ywlx,cbny Having Sum(cs)<>0";
				//echo $sql;exit;
					}else{
					$sql="Select Id ,(Select sm From t_kcsm@zczm_jc Where Id=A.id)As sm,(Select isbn From t_kcsm@zczm_jc Where Id=A.id)As  isbn,
(Select dj From t_kcsm@zczm_jc Where Id=A.id)As dj,(Select min(mc) From t_bb@zczm_jc Where bh=(Select bb From t_kcsm@zczm_jc Where Id=A.id))As bb,
		  (Select mc From t_ghdw@zczm_jc Where bh=(Select min(ghdwh) From t_dhls_jx@zczm_jc Where Id=A.id))As ghdw,(Select Name From t_user@zczm_jc Where usr_id=(Select ywryh From t_kcsm@zczm_jc Where Id=A.id))As ywy,cbny,ywlx,Sum(cs),Sum(cs*(Select dj From t_kcsm@zczm_jc Where Id=a.id))As MY,Sum(sy)As SY From (
Select YWLX,Id,(Select round(cbny) From t_kcsm@zczm_jc Where Id=t_crkls.id)As cbny,
Sum(CRKCS)CS,sum(crksy)sy From t_crkls@zczm_jc Where ywbmbh='$admin_username' And (YWLX='YC' Or YWLX='DC' )And crkrq>=Date'2018-07-01' And crkrq < Date'$end' Group By ywlx,Id
Union All Select YWLX,Id,(Select round(cbny) From t_kcsm@zczm_jc Where Id=t_crkls.id)As cbny,Sum(-CRKCS)CS,sum(-crksy)sy From t_crkls@zczm_jc Where ywbmbh='$admin_username' And ( YWLX='YR'Or YWLX='DR' )
And crkrq>=Date'2018-07-01' And crkrq < '$end' Group By ywlx,Id) A Group By Id,ywlx,cbny Having Sum(cs)<>0";
				//echo $sql;exit;
					}
					
				//echo $sql;exit;
					//if(empty($info)){
						//教辅 库房转移中间态明细<>
						
						$row=$ora->query($sql);
						//echo '教辅-库房转移中间态明细';
						//print_r($row);exit;
						//$info[jiaofukufangmingxi]=$row;  
						
						//setcache("jiaofu_kufangmingxi_".$start.'_'.$end,$info);
					//}else{
					//	$row = $info[jiaofukufangmingxi];
					//}
					if($daochu && $daochu=='daochu'){
						//print_r($row);exit;
						$fileName = '教辅库房转移中间态明细';
						$total = count($row);
						$title = array('ID','SM','ISBN','DJ','版别','供货单位','YWY','CBNY','YWLX','SUM(CS)','MY','SY');
						//$this->articleAccessLog($sql,$fileName,$title,$total,$yewugongsi);
						if($_SESSION['roleid']==26 && $daochulx =='导出到服务器指定位置'){
							$this->yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username,$timetype);
						}elseif($_SESSION['roleid']==26 && $daochulx =='从浏览器中下载'){
							$this->articleAccessLog($sql,$fileName,$title,$total,$yewugongsi);
						}
						//$this->yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username);
					}
					
					
				}elseif($yewuleixing=='损益明细'){
					//$info=getcache("jiaofu_sunyimingxi_".$start.'_'.$end);
					
					if($yewubumen=='quanbu'){
					$sql="Select KFBH,(Select SM From T_KCSM@zczm_jc Where Id=T.ID) As SM,CRKCS,(Select dj From t_kcsm@zczm_jc Where T.id=Id)as dj,(crkcs*(Select dj From t_kcsm@zczm_jc Where T.id=Id)) zmy,crksy,crkrq
From t_crkls@zczm_jc T Where  crkrq>=Date'$start' And crkrq < Date'$end' And ywbmbh in ($admin_username) And ywlx='SY'	";
					}else{
					$sql="Select KFBH,(Select SM From T_KCSM@zczm_jc Where Id=T.ID) As SM,CRKCS,(Select dj From t_kcsm@zczm_jc Where T.id=Id)as dj,(crkcs*(Select dj From t_kcsm@zczm_jc Where T.id=Id)) zmy,crksy,crkrq
From t_crkls@zczm_jc T Where  crkrq>=Date'$start' And crkrq < Date'$end' And ywbmbh='$admin_username' And ywlx='SY'	";
					}
					
					//echo $sql;exit;
						
						$row=$ora->query($sql);
						//print_r($row);exit;
					if($daochu && $daochu=='daochu'){
						
						$fileName = '教辅损益明细';
						$total = count($row);
						$title = array('KFBH','SM','CRKCS','DJ','ZMY','CRKSY','CRKRQ');
						if($_SESSION['roleid']==26 && $daochulx =='导出到服务器指定位置'){
							$this->yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username,$timetype);
						}elseif($_SESSION['roleid']==26 && $daochulx =='从浏览器中下载'){
							$this->articleAccessLog($sql,$fileName,$title,$total,$yewugongsi);
						}
					}
					
					
				}elseif($yewuleixing=='当月已结算-到货已结-到货'){
					if($yewubumen=='quanbu'){
					$sql="Select YWBMBH,GHDWH,MC,DHPCH,YSDJ,SSDH,ZDQH,ZDXH,SM,DJ,JXSSSL,MY,SYS,ZDPCH,JSRQ,JSLX,ZDPCH1,TSLSH,YSDBRQ,JCDRQ,KQBJ,JSGHDWH,YSGHDWH,FLOWID_DHMX,to_char(t_Month_Dhjsb.SHRQ, 'YYYY-MM-DD HH24:MI:SS') as SHRQ,to_char(t_Month_Dhjsb.YSRQ, 'YYYY-MM-DD HH24:MI:SS') as YSRQ From t_Month_Dhjsb Where jsrq>=Date'$start' And jsrq < Date'$end' And ywbmbh in ($admin_username)";
					}else{
					$sql="Select YWBMBH,GHDWH,MC,DHPCH,YSDJ,SSDH,ZDQH,ZDXH,SM,DJ,JXSSSL,MY,SYS,ZDPCH,JSRQ,JSLX,ZDPCH1,TSLSH,YSDBRQ,JCDRQ,KQBJ,JSGHDWH,YSGHDWH,FLOWID_DHMX,to_char(t_Month_Dhjsb.SHRQ, 'YYYY-MM-DD HH24:MI:SS') as SHRQ,to_char(t_Month_Dhjsb.YSRQ, 'YYYY-MM-DD HH24:MI:SS') as YSRQ From t_Month_Dhjsb Where jsrq>=Date'$start' And jsrq < Date'$end' And ywbmbh='$admin_username'";
					}
					
					//echo $sql;exit;
						$row=$ora->query($sql);
						//echo '教辅-当月已结算-发货已结-到货';
						//print_r($row);exit;
						
					if($daochu && $daochu=='daochu'){
						
						$fileName = '教辅当月已结算-到货已结-到货';
						$total = count($row);
						$title = array('YWBMBH','GHDWH','MC','DHPCH','SHRQ','YSDJ','SSDH','ZDQH','ZDXH','SM','DJ','JXSSSL','MY','SYS','ZDPCH','JSRQ','JSLX','ZDPCH1','TSLSH','YSRQ','YSDBRQ','JCDRQ','KQBJ','JSGHDWH','YSGHDWH','FLOWID_DHMX');
						if($_SESSION['roleid']==26 && $daochulx =='导出到服务器指定位置'){
							$this->yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username,$timetype);
						}elseif($_SESSION['roleid']==26 && $daochulx =='从浏览器中下载'){
							$this->articleAccessLog($sql,$fileName,$title,$total,$yewugongsi);
						}
					}
					
				}elseif($yewuleixing=='当月已结算-到货已结-进退'){
					if($yewubumen=='quanbu'){
					$sql="Select YWBMBH,GHDWH,MC,ID,SM,DJ,ZMY,ZSY,BZ,YWPCH,ZDPCH, JSLX,ZDQH,ZDPCH1,TSLSH,NF,NFHRQ,NJSRQ,JSGHDWH,YSGHDWH,ZCS,to_char(t_Month_jtjsb.DBRQ, 'YYYY-MM-DD HH24:MI:SS') as DBRQ,to_char(t_Month_jtjsb.JSRQ, 'YYYY-MM-DD HH24:MI:SS') as JSRQ  From t_Month_jtjsb Where jsrq>=Date'$start' And jsrq < Date'$end' And ywbmbh in ($admin_username)";
					}else{
					$sql="Select YWBMBH,GHDWH,MC,ID,SM,DJ,ZMY,ZSY,BZ,YWPCH,ZDPCH, JSLX,ZDQH,ZDPCH1,TSLSH,NF,NFHRQ,NJSRQ,JSGHDWH,YSGHDWH,ZCS,to_char(t_Month_jtjsb.DBRQ, 'YYYY-MM-DD HH24:MI:SS') as DBRQ,to_char(t_Month_jtjsb.JSRQ, 'YYYY-MM-DD HH24:MI:SS') as JSRQ  From t_Month_jtjsb Where jsrq>=Date'$start' And jsrq < Date'$end' And ywbmbh='$admin_username'";
					}
					
					//echo $sql;exit;
						$row=$ora->query($sql);
						//echo '教辅-当月已结算-发货已结-进退';
						//print_r($row);exit;
						
					if($daochu && $daochu=='daochu'){
						
						$fileName = '教辅当月已结算-到货已结-进退';
						$total = count($row);
						$title = array('YWBMBH','GHDWH','MC','DBRQ','ID','SM','DJ','ZMY','ZSY','BZ','YWPCH','ZDPCH','JSRQ','JSLX','ZDQH','ZDPCH1','TSLSH','NF','NFHRQ','NJSRQ','JSGHDWH','YSGHDWH','ZCS');
						if($_SESSION['roleid']==26 && $daochulx =='导出到服务器指定位置'){
							$this->yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username,$timetype);
						}elseif($_SESSION['roleid']==26 && $daochulx =='从浏览器中下载'){
							$this->articleAccessLog($sql,$fileName,$title,$total,$yewugongsi);
						}
					}

					
				}elseif($yewuleixing=='当月已结算-到货已结-退货'){
					if($yewubumen=='quanbu'){
					$sql="Select YWBMBH,GHDWH,MC,ID,SM,DJ,ZMY,ZSY,BZ,YWPCH,ZDPCH,JSLX,ZDQH,ZDXH,ZDPCH1,TSLSH,NF,NFHRQ,NJSRQ,JSGHDWH,YSGHDWH,ZCS,to_char(t_Month_jtjsb.DBRQ, 'YYYY-MM-DD HH24:MI:SS') as DBRQ,to_char(t_Month_jtjsb.JSRQ, 'YYYY-MM-DD HH24:MI:SS') as JSRQ From t_Month_jtjsb Where jsrq>=Date'$start' And jsrq < Date'$end' And ywbmbh in ($admin_username)";
					}else{
					$sql="Select YWBMBH,GHDWH,MC,ID,SM,DJ,ZMY,ZSY,BZ,YWPCH,ZDPCH,JSLX,ZDQH,ZDXH,ZDPCH1,TSLSH,NF,NFHRQ,NJSRQ,JSGHDWH,YSGHDWH,ZCS,to_char(t_Month_jtjsb.DBRQ, 'YYYY-MM-DD HH24:MI:SS') as DBRQ,to_char(t_Month_jtjsb.JSRQ, 'YYYY-MM-DD HH24:MI:SS') as JSRQ  From t_Month_jtjsb Where jsrq>=Date'$start' And jsrq < Date'$end' And ywbmbh='$admin_username'";
					}
					//if($yewubumen=='quanbu'){
					//$sql="Select * From t_Month_jtjsb Where jsrq>=Date'$start' And jsrq < Date'$end' And ywbmbh in ($admin_username)";
					//}else{
					//$sql="Select * From t_Month_jtjsb Where jsrq>=Date'$start' And jsrq < Date'$end' And ywbmbh='$admin_username'";
					//}

					//echo $sql;exit;					
					//if(empty($info)){
						//教辅 当月已结算-到货已结-退货<>
						$row=$ora->query($sql);
						//echo '教辅-当月已结算-发货已结-退货';
						//print_r($row);exit;
						//$info[jiaofudaohuoyijieth]=$row;  
						//setcache("jiaofu_daohuoyijieth_".$start.'_'.$end,$info);
					//}else{
					//	$row = $info[jiaofudaohuoyijieth];
					//}
					if($daochu && $daochu=='daochu'){
						
						$fileName = '教辅当月已结算-到货已结-退货';
						$total = count($row);
						$title = array('YWBMBH','GHDWH','MC','ID','DBRQ','SM','DJ','ZMY','ZSY','BZ','YWPCH','JSRQ','ZDPCH','JSLX','ZDQH','ZDXH','ZDPCH1','TSLSH','NF','NFHRQ','NJSRQ','JSGHDWH','YSGHDWH','ZCS');
						//$this->articleAccessLog($sql,$fileName,$title,$total,$yewugongsi);
						if($_SESSION['roleid']==26 && $daochulx =='导出到服务器指定位置'){
							$this->yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username,$timetype);
						}elseif($_SESSION['roleid']==26 && $daochulx =='从浏览器中下载'){
							$this->articleAccessLog($sql,$fileName,$title,$total,$yewugongsi);
						}
						//$this->yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username);
					}


					
				}elseif($yewuleixing=='当月已结算-到货已结-更正'){
					//$info=getcache("jiaofu_daohuoyijiegz_".$start.'_'.$end);
					if($yewubumen=='quanbu'){
					$sql="Select YWBMBH,DH,MC,PFPCH,BZ,ZMY,ZSY,CBJ,TSLSH,SM,DJ,ZDQH,ZDXH,KFBH,JSLX,NF,NFHRQ,NJSRQ,JB,CWQRPC,YSDBRQ,ZCS,to_char(t_Month_fhjsb.DBRQ, 'YYYY-MM-DD HH24:MI:SS') as DBRQ,to_char(t_Month_fhjsb.PFRQ, 'YYYY-MM-DD HH24:MI:SS') as PFRQ ,to_char(t_Month_fhjsb.JSRQ, 'YYYY-MM-DD HH24:MI:SS') as JSRQ From t_Month_fhjsb Where jsrq>=Date'$start' And jsrq < Date'$end' And ywbmbh in ($admin_username)";
					}else{
					$sql="Select YWBMBH,DH,MC,PFPCH,BZ,ZMY,ZSY,CBJ,TSLSH,SM,DJ,ZDQH,ZDXH,KFBH,JSLX,NF,NFHRQ,NJSRQ,JB,CWQRPC,YSDBRQ,ZCS,to_char(t_Month_fhjsb.DBRQ, 'YYYY-MM-DD HH24:MI:SS') as DBRQ,to_char(t_Month_fhjsb.PFRQ, 'YYYY-MM-DD HH24:MI:SS') as PFRQ ,to_char(t_Month_fhjsb.JSRQ, 'YYYY-MM-DD HH24:MI:SS') as JSRQ From t_Month_fhjsb Where jsrq>=Date'$start' And jsrq < Date'$end' And ywbmbh='$admin_username'";
					}
					//echo $sql;exit;
					//if(empty($info)){
						//教辅 当月已结算-到货已结-更正<>
						$row=$ora->query($sql);
						//echo '教辅-当月已结算-发货已结-更正';
						//print_r($row);exit;//2017-07-31 edit by zhang这个数据库没数据
						//$info[jiaofudaohuoyijiegz]=$row;
						
						//setcache("jiaofu_daohuoyijiegz_".$start.'_'.$end,$info);
					//}else{
					//	$row = $info[jiaofudaohuoyijiegz];
					//}
					if($daochu && $daochu=='daochu'){
						$fileName = '教辅当月已结算-到货已结-更正';
						$total = count($row);
						$title = array('YWBMBH','DH','MC','PFPCH','BZ','DBRQ','PFRQ','ZMY','ZSY','CBJ','TSLSH','JSRQ','SM','DJ','ZDQH','ZDXH','KFBH','JSLX','NF','NFHRQ','NJSRQ','JB','CWQRPC','YSDBRQ','ZCS');
						//$this->articleAccessLog($sql,$fileName,$title,$total,$yewugongsi);
						if($_SESSION['roleid']==26 && $daochulx =='导出到服务器指定位置'){
							$this->yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username,$timetype);
						}elseif($_SESSION['roleid']==26 && $daochulx =='从浏览器中下载'){
							$this->articleAccessLog($sql,$fileName,$title,$total,$yewugongsi);
						}
						//$this->yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username);
					}

				}elseif($yewuleixing=='当月已结算-发货已结-到货'){
					//$info=getcache("jiaofu_fahuoyijiedh_".$start.'_'.$end);
					//$sql="Select * From t_Month_fhjsb@zczm_jc Where jsrq>=Date'$start' And jsrq<Date'$end' And ywbmbh<>'$admin_username'";
					if($yewubumen=='quanbu'){
					$sql="Select cbny,ghdw,ghdwmc,YWBMBH,DH,MC,PFPCH,BZ,ZMY,ZSY,CBJ,TSLSH,SM,DJ,ZDQH,ZDXH,KFBH,JSLX,NF,NFHRQ,NJSRQ,JB,CWQRPC,YSDBRQ,ZCS,to_char(t_Month_fhjsb.DBRQ, 'YYYY-MM-DD HH24:MI:SS') as DBRQ,to_char(t_Month_fhjsb.PFRQ, 'YYYY-MM-DD HH24:MI:SS') as PFRQ ,to_char(t_Month_fhjsb.JSRQ, 'YYYY-MM-DD HH24:MI:SS') as JSRQ  From t_Month_fhjsb Where jsrq>=Date'$start' And jsrq < Date'$end' And ywbmbh in ($admin_username)";
					}else{
					$sql="Select cbny,ghdw,ghdwmc,YWBMBH,DH,MC,PFPCH,BZ,ZMY,ZSY,CBJ,TSLSH,SM,DJ,ZDQH,ZDXH,KFBH,JSLX,NF,NFHRQ,NJSRQ,JB,CWQRPC,YSDBRQ,ZCS,to_char(t_Month_fhjsb.DBRQ, 'YYYY-MM-DD HH24:MI:SS') as DBRQ,to_char(t_Month_fhjsb.PFRQ, 'YYYY-MM-DD HH24:MI:SS') as PFRQ ,to_char(t_Month_fhjsb.JSRQ, 'YYYY-MM-DD HH24:MI:SS') as JSRQ  From t_Month_fhjsb Where jsrq>=Date'$start' And jsrq < Date'$end' And ywbmbh='$admin_username'";
					}
					
						$row=$ora->query($sql);
						//print_r($row);exit;
					if($daochu && $daochu=='daochu'){
						$fileName = '教辅当月已结算-发货已结-到货';
						$total = count($row);
						$title = array('CBNY','GHDW','GHDWMC','YWBMBH','DH','MC','PFPCH','BZ','DBRQ','PFRQ','ZMY','ZSY','CBJ','TSLSH','JSRQ','SM','DJ','ZDQH','ZDXH','KFBH','JSLX','NF','NFHRQ','NJSRQ','JB','CWQRPC','YSDBRQ','ZCS');
						//$this->articleAccessLog($sql,$fileName,$title,$total,$yewugongsi);
						if($_SESSION['roleid']==26 && $daochulx =='导出到服务器指定位置'){
							$this->yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username,$timetype);
						}elseif($_SESSION['roleid']==26 && $daochulx =='从浏览器中下载'){
							$this->articleAccessLog($sql,$fileName,$title,$total,$yewugongsi);
						}
						//$this->yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username);
					}
					
				}elseif($yewuleixing=='当月已结算-发货已结-退货'){
					//$info=getcache("jiaofu_fahuoyijieth_".$start.'_'.$end);
					if($yewubumen=='quanbu'){
					$sql="Select * From t_Month_xtjsb Where jsrq>=Date'$start' And jsrq < Date'$end' And ywbmbh in ($admin_username)";
					}else{
					$sql="Select * From t_Month_xtjsb Where jsrq>=Date'$start' And jsrq < Date'$end' And ywbmbh='$admin_username'";
					}

					//echo $sql;exit;
					//if(empty($info)){
						//教辅 当月已结算-发货已结-退货<>
						$row=$ora->query($sql);
						//echo '教辅-当月已结算-发货已结-退货';
						//print_r($row);exit;
						//$info[jiaofufahuoyijiedh]=$row;  
						
						//setcache("jiaofu_fahuoyijiedh_".$start.'_'.$end,$info);
					//}else{
					//	$row = $info[jiaofufahuoyijiedh];
					//}
					if($daochu && $daochu=='daochu'){
						$fileName = '教辅当月已结算-发货已结-退货';
						$total = count($row);
						$title = array('CBNY','GHDW','GHDWMC','YWBMBH','KFBH','DH','DM','PCH','THRQ','ZMY','ZSY','ZCB','TSLSH','JSRQ','JSLX','ZDQH','NF','NFHRQ','NJSRQ','JB','CWQRPC','ZCS','DJ','ID','BB','SM','ISBN','CBNY');
						//$this->articleAccessLog($sql,$fileName,$title,$total,$yewugongsi);
						if($_SESSION['roleid']==26 && $daochulx =='导出到服务器指定位置'){
							$this->yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username,$timetype);
						}elseif($_SESSION['roleid']==26 && $daochulx =='从浏览器中下载'){
							$this->articleAccessLog($sql,$fileName,$title,$total,$yewugongsi);
						}
						//$this->yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username);
					}
					
				}elseif($yewuleixing=='当月已结算-发货已结-更正'){
					//$info=getcache("jiaofu_fahuoyijiegz_".$start.'_'.$end);
					
					if($yewubumen=='quanbu'){
					$sql="Select * From t_Month_Fhgzjsb Where jsrq>=Date'$start' And jsrq < Date'$end' And ywbmbh in ($admin_username)";
					}else{
					$sql="Select * From t_Month_Fhgzjsb Where jsrq>=Date'$start' And jsrq < Date'$end' And ywbmbh='$admin_username'";
					}
					//echo $sql;exit;
					//if(empty($info)){
						//教辅 当月已结算-发货已结-更正<>
						$row=$ora->query($sql);
						//echo '教辅-当月已结算-发货已结-更正';
						//print_r($row);exit;//2017-07-31 edit by zhang这个数据库也没数据,孟老师也没给导出的表，所以不知道都有什么字段！！！
						//$info[jiaofufahuoyijiegz]=$row;  
						
						//setcache("jiaofu_fahuoyijiegz_".$start.'_'.$end,$info);
					//}else{
					//	$row = $info[jiaofufahuoyijiegz];
					//}
					
					if($daochu && $daochu=='daochu'){
						$fileName = '教辅当月已结算-发货已结-更正';
						$total = count($row);
						$title = array('CBNY','GHDW','GHDWMC','YWBMBH','KFBH','DH','DM','GZPCH','GZRQ','ZMY','ZSY','ZCB','TSLSH','JSRQ','BZ','JSLX','ZDQH','NF','NFHRQ','NJSRQ','JB','ZCS');
						//$this->articleAccessLog($sql,$fileName,$title,$total,$yewugongsi);
						if($_SESSION['roleid']==26 && $daochulx =='导出到服务器指定位置'){
							$this->yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username,$timetype);
						}elseif($_SESSION['roleid']==26 && $daochulx =='从浏览器中下载'){
							$this->articleAccessLog($sql,$fileName,$title,$total,$yewugongsi);
						}
						//$this->yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username);
					}
					
				}elseif($yewuleixing=='应付款-到货未结明细'){//dhweijie 到货未结
					//$info=getcache("jiaofu_dhweijiemingxi_".$start.'_'.$end);
					//$sql="Select * From t_Month_dhjsb@zczm_jc Where ywbmbh<>'$admin_username' And jsrq Is Null";
					if($yewubumen=='quanbu'){
					$sql="Select YWBMBH,GHDWH,MC,DHPCH,YSDJ,SSDH,ZDQH,ZDXH,SM,DJ,JXSSSL,MY,SYS,ZDPCH,JSRQ,JSLX,ZDPCH1,TSLSH,YSDBRQ,JCDRQ,KQBJ,JSGHDWH,YSGHDWH,FLOWID_DHMX,to_char(t_Month_Dhjsb.SHRQ, 'YYYY-MM-DD HH24:MI:SS') as SHRQ,to_char(t_Month_Dhjsb.YSRQ, 'YYYY-MM-DD HH24:MI:SS') as YSRQ  From t_Month_Dhjsb Where ywbmbh in ($admin_username) And jsrq Is Null";
					}else{
					$sql="Select YWBMBH,GHDWH,MC,DHPCH,YSDJ,SSDH,ZDQH,ZDXH,SM,DJ,JXSSSL,MY,SYS,ZDPCH,JSRQ,JSLX,ZDPCH1,TSLSH,YSDBRQ,JCDRQ,KQBJ,JSGHDWH,YSGHDWH,FLOWID_DHMX,to_char(t_Month_Dhjsb.SHRQ, 'YYYY-MM-DD HH24:MI:SS') as SHRQ,to_char(t_Month_Dhjsb.YSRQ, 'YYYY-MM-DD HH24:MI:SS') as YSRQ  From t_Month_Dhjsb Where ywbmbh='$admin_username' And jsrq Is Null";
					}
					
					//if(empty($info)){
						//教辅 应付款-到货未结明细<>
						$row=$ora->query($sql);
						//echo '教辅-应付款-到货未结明细';
						//print_r($row);exit;
						//$info[jiaofudhweijiemingxi]=$row; 
						
						//setcache("jiaofu_dhweijiemingxi_".$start.'_'.$end,$info);
					//}else{
					//	$row = $info[jiaofudhweijiemingxi];
					//}
					
					if($daochu && $daochu=='daochu'){
						$fileName = '教辅应付款-到货未结明细';
						$total = count($row);
						$title = array('YWBMBH','GHDWH','MC','DHPCH','SHRQ','YSDJ','SSDH','ZDQH','ZDXH','SM','DJ','JXSSSL','MY','SYS','ZDPCH','JSRQ','JSLX','ZDPCH1','TSLSH','YSRQ','YSDBRQ','JCDRQ','KQBJ','JSGHDWH','YSGHDWH','FLOWID_DHMX');
						//$this->articleAccessLog($sql,$fileName,$title,$total,$yewugongsi);
						if($_SESSION['roleid']==26 && $daochulx =='导出到服务器指定位置'){
							$this->yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username,$timetype);
						}elseif($_SESSION['roleid']==26 && $daochulx =='从浏览器中下载'){
							$this->articleAccessLog($sql,$fileName,$title,$total,$yewugongsi);
						}
						//$this->yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username);
					}
					

				}elseif($yewuleixing=='应付款-进退未结明细'){ //jtweijie 进退未结
					set_time_limit(0);
					ini_set('memory_limit', '-1');
					//$info=getcache("jiaofu_jtweijiemingxi_".$start.'_'.$end);
					//print_r($info);exit;
					if($yewubumen=='quanbu'){
					$sql="Select YWBMBH,GHDWH,MC,ID,SM,DJ,ZMY,ZSY,BZ,YWPCH,ZDPCH,JSRQ,JSLX,ZDQH,ZDXH,ZDPCH1,TSLSH,NF,NFHRQ,NJSRQ,JSGHDWH,YSGHDWH,ZCS,to_char(t_Month_jtjsb.DBRQ, 'YYYY-MM-DD HH24:MI:SS') as DBRQ From t_Month_jtjsb Where ywbmbh in ($admin_username) And jsrq Is Null";
					}else{
					$sql="Select YWBMBH,GHDWH,MC,ID,SM,DJ,ZMY,ZSY,BZ,YWPCH,ZDPCH,JSRQ,JSLX,ZDQH,ZDXH,ZDPCH1,TSLSH,NF,NFHRQ,NJSRQ,JSGHDWH,YSGHDWH,ZCS,to_char(t_Month_jtjsb.DBRQ, 'YYYY-MM-DD HH24:MI:SS') as DBRQ From t_Month_jtjsb Where ywbmbh='$admin_username' And jsrq Is Null";
					}
					
					//echo $sql;exit;
					//if(empty($info)){
						//教辅 应付款-进退未结明细<>
						$row=$ora->query($sql);
						//echo '教辅-应付款-进退未结明细';
						//print_r($row);exit;
						//$info[jiaofujtweijiemingxi]=$row; 
						
						//setcache("jiaofu_jtweijiemingxi_".$start.'_'.$end,$info);
					//}else{
					//	$row = $info[jiaofujtweijiemingxi];
					//}
					if($daochu && $daochu=='daochu'){
						//print_r($row);exit;
						$fileName = '教辅应付款-进退未结明细';
						$total = count($row);
						//echo $total;exit; 
						$title = array('YWBMBH','GHDWH','MC','DBRQ','ID','SM','DJ','ZMY','ZSY','BZ','YWPCH','ZDPCH','JSRQ','JSLX','ZDQH','ZDXH','ZDPCH1','TSLSH','NF','NFHRQ','NJSRQ','JSGHDWH','YSGHDWH','ZCS');
						//$this->articleAccessLog($sql,$fileName,$title,$total,$yewugongsi);
						if($_SESSION['roleid']==26 && $daochulx =='导出到服务器指定位置'){
							$this->yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username,$timetype);
						}elseif($_SESSION['roleid']==26 && $daochulx =='从浏览器中下载'){
							$this->articleAccessLog($sql,$fileName,$title,$total,$yewugongsi);
						}
						//$this->yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username);
					}

				}elseif($yewuleixing=='应收款-发货未结明细'){
					set_time_limit(0);
					ini_set('memory_limit', '-1');
					//$info=getcache("jiaofu_fhweijiemingxi_".$start.'_'.$end);
					//$sql="Select * From t_Month_fhjsb@zczm_jc Where ywbmbh<>'$admin_username' And jsrq Is Null";
					if($yewubumen=='quanbu'){
					$sql="Select ghdw,ghdwmc,YWBMBH,DH,MC,PFPCH,BZ,ZMY,ZSY,CBJ,TSLSH,JSRQ,SM,DJ,ZDQH,ZDXH,KFBH,JSLX,NF,NFHRQ,NJSRQ,JB,CWQRPC,YSDBRQ,ZCS,to_char(t_Month_fhjsb.DBRQ, 'YYYY-MM-DD HH24:MI:SS') as DBRQ,to_char(t_Month_fhjsb.PFRQ, 'YYYY-MM-DD HH24:MI:SS') as PFRQ From t_Month_fhjsb Where ywbmbh in ($admin_username) And jsrq Is Null";
					}else{
					$sql="Select cbny,ghdw,ghdwmc,YWBMBH,DH,MC,PFPCH,BZ,ZMY,ZSY,CBJ,TSLSH,JSRQ,SM,DJ,ZDQH,ZDXH,KFBH,JSLX,NF,NFHRQ,NJSRQ,JB,CWQRPC,YSDBRQ,ZCS,to_char(t_Month_fhjsb.DBRQ, 'YYYY-MM-DD HH24:MI:SS') as DBRQ,to_char(t_Month_fhjsb.PFRQ, 'YYYY-MM-DD HH24:MI:SS') as PFRQ From t_Month_fhjsb Where ywbmbh='$admin_username' And jsrq Is Null";
					}
					//if(empty($info)){
						//教辅 应收款-发货未结明细<>
						$row=$ora->query($sql);
						//echo '教辅-应收款-发货未结明细';
						//print_r($row);exit;
						//$info[jiaofufhweijiemingxi]=$row;  
						
						//setcache("jiaofu_fhweijiemingxi_".$start.'_'.$end,$info);
					//}else{
					//	$row = $info[jiaofufhweijiemingxi];
					//}
					
					if($daochu && $daochu=='daochu'){
						
						$fileName = '教辅应收款-发货未结明细';
						$total = count($row);
						$title = array('CBNY','GHDW','GHDWMC','YWBMBH','DH','MC','PFPCH','BZ','DBRQ','PFRQ','ZMY','ZSY','CBJ','TSLSH','JSRQ','SM','DJ','ZDQH','ZDXH','KFBH','JSLX','NF','NFHRQ','NJSRQ','JB','CWQRPC','YSDBRQ','ZCS');
						//$this->articleAccessLog($sql,$fileName,$title,$total,$yewugongsi);
						if($_SESSION['roleid']==26 && $daochulx =='导出到服务器指定位置'){
							$this->yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username,$timetype);
						}elseif($_SESSION['roleid']==26 && $daochulx =='从浏览器中下载'){
							$this->articleAccessLog($sql,$fileName,$title,$total,$yewugongsi);
						}

						//$this->yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username);
					}

				}elseif($yewuleixing=='应收款-销退未结明细'){
					//$info=getcache("jiaofu_xtweijiemingxi_".$start.'_'.$end);
					//$sql="Select * From t_Month_Xtjsb@zczm_jc Where ywbmbh<>'$admin_username' And jsrq Is Null";
					
					if($yewubumen=='quanbu'){
					$sql="Select cbny,ghdw,ghdwmc,YWBMBH,KFBH,DH,DM,PCH,ZMY,ZSY,ZCB,TSLSH,JSRQ,JSLX,ZDQH,NF,NFHRQ,NJSRQ,JB,CWQRPC,ZCS,DJ,to_char(t_Month_Xtjsb.THRQ, 'YYYY-MM-DD HH24:MI:SS') as THRQ From t_Month_Xtjsb Where ywbmbh in ($admin_username) And jsrq Is Null";
					}else{
					$sql="Select cbny,ghdw,ghdwmc,YWBMBH,KFBH,DH,DM,PCH,ZMY,ZSY,ZCB,TSLSH,JSRQ,JSLX,ZDQH,NF,NFHRQ,NJSRQ,JB,CWQRPC,ZCS,DJ,to_char(t_Month_Xtjsb.THRQ, 'YYYY-MM-DD HH24:MI:SS') as THRQ From t_Month_Xtjsb Where ywbmbh='$admin_username' And jsrq Is Null";
					}
					//echo $sql;exit;
					
					//if(empty($info)){
						//教辅 应收款-销退未结明细<>
						$row=$ora->query($sql);
						//echo '教辅-应收款-销退未结明细';
						//print_r($row);exit;
						//$info[jiaofuxtweijiemingxi]=$row; 
						
						//setcache("jiaofu_xtweijiemingxi_".$start.'_'.$end,$info);
					//}else{
					//	$row = $info[jiaofuxtweijiemingxi];
						//print_r($row);exit;
					//}
					
					if($daochu && $daochu=='daochu'){
						
						$fileName = '教辅应收款-销退未结明细';
						$total = count($row);
						$title = array('CBNY','GHDW','GHDWMC','YWBMBH','KFBH','DH','DM','PCH','THRQ','ZMY','ZSY','ZCB','TSLSH','JSRQ','JSLX','ZDQH','NF','NFHRQ','NJSRQ','JB','CWQRPC','ZCS','DJ');
						//$this->articleAccessLog($sql,$fileName,$title,$total,$yewugongsi);
						if($_SESSION['roleid']==26 && $daochulx =='导出到服务器指定位置'){
							$this->yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username,$timetype);
						}elseif($_SESSION['roleid']==26 && $daochulx =='从浏览器中下载'){
							$this->articleAccessLog($sql,$fileName,$title,$total,$yewugongsi);
						}
						//$this->yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username);
					}

					
				}elseif($yewuleixing=='应收款-更正未结明细'){
					//$info=getcache("jiaofu_gzweijiemingxi_".$start.'_'.$end);
					//$sql="Select * From t_Month_fhgzjsb@zczm_jc Where ywbmbh<>'$admin_username' And jsrq Is Null";
					
					if($yewubumen=='quanbu'){
					$sql="Select YWBMBH,KFBH,DH,DM,GZPCH,ZMY,ZSY,ZCB,TSLSH,JSRQ,BZ,JSLX,ZDQH,NF,NFHRQ,NJSRQ,JB,ZCS,to_char(t_Month_fhgzjsb.GZRQ, 'YYYY-MM-DD HH24:MI:SS') as GZRQ From t_Month_fhgzjsb Where ywbmbh in ($admin_username) And jsrq Is Null";
					}else{
					$sql="Select YWBMBH,KFBH,DH,DM,GZPCH,ZMY,ZSY,ZCB,TSLSH,JSRQ,BZ,JSLX,ZDQH,NF,NFHRQ,NJSRQ,JB,ZCS,to_char(t_Month_fhgzjsb.GZRQ, 'YYYY-MM-DD HH24:MI:SS') as GZRQ From t_Month_fhgzjsb Where ywbmbh='$admin_username' And jsrq Is Null";
					}
					//echo $sql;exit;
					
					//if(empty($info)){
						//教辅 应收款-更正未结明细<>
						$row=$ora->query($sql);
						//echo '教辅-应收款-更正未结明细';
						//print_r($row);exit;
						//$info[jiaofugzweijiemingxi]=$row;  
						
						//setcache("jiaofu_gzweijiemingxi_".$start.'_'.$end,$info);
					//}else{
					//	$row = $info[jiaofugzweijiemingxi];
						//print_r($row);exit;
					//}
					
					if($daochu && $daochu=='daochu'){
						
						$fileName = '教辅应收款-更正未结明细';
						$total = count($row);
						$title = array('GHDW','GHDWMC','YWBMBH','KFBH','DH','DM','GZPCH','GZRQ','ZMY','ZSY','ZCB','TSLSH','JSRQ','BZ','JSLX','ZDQH','NF','NFHRQ','NJSRQ','JB','ZCS');
						//$this->articleAccessLog($sql,$fileName,$title,$total,$yewugongsi);
						if($_SESSION['roleid']==26 && $daochulx =='导出到服务器指定位置'){
							$this->yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username,$timetype);
						}elseif($_SESSION['roleid']==26 && $daochulx =='从浏览器中下载'){
							$this->articleAccessLog($sql,$fileName,$title,$total,$yewugongsi);
						}
						//$this->yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username);
					}

					
				}
				
			}elseif($yewugongsi==2){  //查询教材公司
				$yewugongsiname='教材公司';
				$guocheng=getcache('guocheng');
				
				if($_SESSION['roleid']==26){//过程
					if($guocheng['guocheng'] != $timetype ){
						$sql_gc = "Delete From t_month_dhjsb";
						$res = $ora->query($sql_gc);
						$sql_gc = "Delete From t_month_dhjsb_mf";
						$res = $ora->query($sql_gc);
						$sql_gc = "Delete From t_month_jtjsb";
						$res = $ora->query($sql_gc);
						$sql_gc = "Delete From t_month_fhjsb";
						$res = $ora->query($sql_gc);
						$sql_gc = "Delete From t_month_xtjsb";
						$res = $ora->query($sql_gc);
						$sql_gc = "Delete From t_month_fhgzjsb";
						$res = $ora->query($sql_gc);
						$sql_gc = "Delete From t_month_pfmxjsb";
						$res = $ora->query($sql_gc);
						$sql_gc = "Delete From t_month_zd_ts";
						$res = $ora->query($sql_gc);
						$sql_gc = "Insert Into t_month_dhjsb
        (ywbmbh, ghdwh, mc, dhpch, shrq, ysdj, ssdh, zdqh, zdxh, sm, dj, jxsssl, my, sys,
         zdpch, jsrq, JSLX, ZDPCH1, TSLSH, ysrq, jsghdwh, flowid_dhmx)
        Select ywbmbh, ghdwh, (Select mc From t_ghdw@zczm_jc Where bh = a.ghdwh) As mc, dhpch,
               shrq, ysdj, ssdh, zdqh, zdxh, (Select sm From t_kcsm@zczm_jc Where id = a.id) As sm,
               (Select dj From t_kcsm@zczm_jc Where id = a.id) As dj, (djshsl),
               ((djshsl) * (Select dj From t_kcsm@zczm_jc Where Id = a.id)) As my, (sys) As sy,
               zdpch, (Select FPDJRQ From T_ZDTSLS@zczm_jc Where PCH = A.ZDPCH) As jsrq,
               (Select JSLX From t_kcsm@zczm_jc Where id = a.id) As JSLX, ZDPCH1, tslsh, ysrq,
               Null, flowid_dhmx
          From t_dhls_jx@zczm_jc a
         Where trunc(a.shrq) < trunc(Date '$end')
           And (zdpch Is Null Or Exists (Select pch
                   From t_zdtsls@zczm_jc
                  Where pch = a.zdpch
                    And (FPDJrq >= Date '$start' Or FPDJRQ Is Null)))";
		 //echo $sql_gc;exit;
						$res = $ora->query($sql_gc);
						$sql_gc = "Insert Into t_month_zd_ts
        Select zdpch, tslsh, Count(Distinct(tslsh)) As num_tslsh
          From t_jzls_dh@zczm_jc
         Group By zdpch, tslsh";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);
						$sql_gc = "Update t_month_dhjsb a
       Set tslsh = (Select tslsh From t_month_zd_ts Where zdpch = a.zdpch)
     Where tslsh Is Null";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);
						$sql_gc = "Insert Into t_month_dhjsb
        (ywbmbh, ghdwh, mc, dhpch, shrq, ysdj, ssdh, zdqh, zdxh, sm, dj, jxsssl, my, sys,
         zdpch, jsrq, JSLX, ZDPCH1, TSLSH, ysrq, jsghdwh, flowid_dhmx)
        Select ywbmbh, ghdwh, (Select mc From t_ghdw@zczm_jc Where bh = a.ghdwh) As mc, dhpch,
               shrq, ysdj, ssdh, zdqh, zdxh, (Select sm From t_kcsm@zczm_jc Where id = a.id) As sm,
               (Select dj From t_kcsm@zczm_jc Where id = a.id) As dj, (djshsl),
               ((djshsl) * (Select dj From t_kcsm@zczm_jc Where Id = a.id)) As my, (sys) As sy,
               zdpch, shrq As jsrq, (Select JSLX From t_kcsm@zczm_jc Where id = a.id) As JSLX,
               ZDPCH1, tslsh, ysrq, ghdwh, flowid_dhmx
          From t_dhls_jx@zczm_jc a
         Where trunc(a.shrq) < trunc(Date '$end')
           And shrq >= trunc(Date '$start')
           And (zdpch Is Not Null And Not Exists
                (Select pch From t_zdtsls@zczm_jc Where pch = a.zdpch))";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);
						$sql_gc = "Update t_month_dhjsb t Set ysghdwh = ghdwh Where tslsh Is Not Null";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);
						$sql_gc = "Insert Into t_month_dhjsb_mf
        (ywbmbh, ghdwh, mc, dhpch, shrq, ysdj, ssdh, zdqh, zdxh, sm, dj, jxsssl, my, sys,
         zdpch, jsrq, JSLX, tslsh, jsghdwh)
        Select ywbmbh, ghdwh, (Select mc From t_ghdw@zczm_jc Where bh = a.ghdwh) As mc, czpch,
               czrq, '免费' As ysdj, Null As ssdh, zdqh, zdxh,
               (Select sm From t_kcsm@zczm_jc Where id = a.id) As sm,
               (Select dj From t_kcsm@zczm_jc Where id = a.id) As dj, mfcs,
               (mfcs * (Select dj From t_kcsm@zczm_jc Where Id = a.id)) As my,
               (mfcs * (Select dj From t_kcsm@zczm_jc Where Id = a.id) * (xjhzk - yjhzk) / 100) As sy,
               zdpch, (Select fpdjrq From t_zdtsls@zczm_jc Where pch = a.zdpch) As jsrq,
               (Select JSLX From t_kcsm@zczm_jc Where id = a.id) As JSLX, tslsh,
               ((Select ghdwh1 From t_fpls@zczm_jc Where Tslsh = a.Tslsh) Union All
                 (Select ghdwh1 From t_tmp_fpls@zczm_jc Where t_tmp_fpls.Tslsh = a.Tslsh))
          From t_dhls_mfjc@zczm_jc a
         Where trunc(a.czrq) < trunc(Date '$end')
           And (zdpch Is Null Or Exists
                (Select pch
                   From t_zdtsls@zczm_jc
                  Where pch = a.zdpch
                    And (FPDJRQ >= trunc(Date '$start') Or FPDJRQ Is Null)))";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);
						$sql_gc = "Update t_month_dhjsb_mf t Set ysghdwh = ghdwh Where tslsh Is Not Null";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);
						
						$sql_gc = "Insert Into t_month_jtjsb
        (ywbmbh, ghdwh, mc, dbrq, id, sm, dj, zmy, zsy, bz, ywpch, zdpch, jsrq, JSLX,
         ZDQH, ZDXH, ZDPCH1, TSLSH, jsghdwh, zcs)
        Select a.YWBMBH, a.hybh As GHDWH, (Select mc From t_ghdw@zczm_jc Where bh = a.HYBH) As mc,
               a.dbrq, a.id, b.sm, b.dj, (- (a.thcs - a.thcs1) * b.dj) As zmy,
               (-a.thcs * b.dj * thzk / 100) As zsy, bz, ywpch, zdpch,
               (Select FPDJRQ From t_zdtsls@zczm_jc Where pch = a.zdpch) jsrq, b.jslx As JSLX,
               A.ZDQH, A.ZDXH, ZDPCH1, TSLSH,
               ((Select ghdwh1 From t_fpls@zczm_jc Where Tslsh = a.Tslsh) Union All
                 (Select ghdwh1 From t_tmp_fpls@zczm_jc Where t_tmp_fpls.Tslsh = a.Tslsh)),
               (-a.thcs)
          From t_hythmx@zczm_jc a, t_kcsm@zczm_jc b
         Where a.id = b.id
           And (a.zdpch Is Null Or Exists
                (Select pch
                   From t_zdtsls@zczm_jc
                  Where pch = a.zdpch
                    And (FPDJRQ >= trunc(Date '$start') Or FPDJRQ Is Null)))
           And trunc(a.dbrq) < trunc(Date '$end')";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);

						$sql_gc = "Update t_month_jtjsb t Set ysghdwh = ghdwh Where tslsh Is Not Null";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);
						
						$sql_gc = "Insert Into t_month_fhjsb
        (ywbmbh, dh, mc, pfpch, bz, dbrq, pfrq, zmy, zsy, cbj, tslsh, jsrq, sm, dj, zdqh,
         zdxh,cbny, kfbh, JSLX, zcs)
        Select ywbmbh, dh, (Select dm From t_dm@zczm_jc Where dh = t.dh) As mc, pfpch, bz As bz,
               (dbrq) As dbrq, pfrq,
               (sfcs * (Select dj From t_kcsm@zczm_jc Where id = t.id)) As zmy,
               ROUND((nvl(sfcs * (Select dj From t_kcsm@zczm_jc Where id = t.id) * fhzk / 100, 0)),
                      2) As zsy, (nvl(cbj, 0)) As cbj, tslsh,
               (Select jsrq From t_tshz@zczm_jc Where tslsh = t.tslsh) jsrq,
               (Select sm From t_kcsm@zczm_jc Where id = t.id) As sm,
               (Select dj From t_kcsm@zczm_jc Where id = t.id) As dj, zdqh, zdxh,  
			   (Select cbny From t_kcsm@zczm_jc Where id = t.id) As cbny,kfbh,
               (Select JSLX From t_kcsm@zczm_jc Where id = t.id) As JSLX, (sfcs)
          From t_fhmx1@zczm_jc t
         Where dbrq < trunc(Date '$end')";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);
						
						$sql_gc = "Insert Into t_month_pfmxjsb
        (ywbmbh, dh, mc, pfpch, bz, dbrq, pfrq, zmy, zsy, cbj, tslsh, jsrq, sm, dj, zdqh,
         zdxh, kfbh, JSLX, Id, zdh, ywqrpc)
        Select ywbmbh, dh, (Select dm From t_dm@zczm_jc Where dh = t.dh) As mc, pxdh, bz As bz,
               (pfrq) As dbrq, pfrq,
               (pxcs * (Select dj From t_kcsm@zczm_jc Where id = t.id)) As zmy,
               ROUND((nvl(pxcs * (Select dj From t_kcsm@zczm_jc Where id = t.id) * pxzk / 100, 0)),
                      2) As zsy, (nvl(cbj, 0)) As cbj, '配发未运' As tslsh, '配发未运' As jsrq,
               (Select sm From t_kcsm@zczm_jc Where id = t.id) As sm,
               (Select dj From t_kcsm@zczm_jc Where id = t.id) As dj, zdqh, zdxh, kfbh,
               (Select JSLX From t_kcsm@zczm_jc Where id = t.id) As JSLX, Id, zdh, ywqrpc
          From t_pfmx@zczm_jc t";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);
						
						$sql_gc = "Insert Into t_month_fhjsb
        (ywbmbh, dh, mc, pfpch, bz, dbrq, pfrq, zmy, zsy, cbj, tslsh, jsrq, sm, dj, zdqh,
         zdxh, cbny, kfbh, JSLX, zcs)
        Select ywbmbh, dh, (Select dm From t_dm@zczm_jc Where dh = t.dh) As mc, pfpch, bz As bz,
               (dbrq) As dbrq, pfrq,
               (sfcs * (Select dj From t_kcsm@zczm_jc Where id = t.id)) As zmy,
               ROUND((nvl(sfcs * (Select dj From t_kcsm@zczm_jc Where id = t.id) * fhzk / 100, 0)),
                      2) As zsy, (nvl(cbj, 0)) As cbj, '现金' As tslsh, dbrq As jsrq,
               (Select sm From t_kcsm@zczm_jc Where id = t.id) As sm,
               (Select dj From t_kcsm@zczm_jc Where id = t.id) As dj, zdqh, zdxh, 
			   (Select cbny From t_kcsm@zczm_jc Where id = t.id) As cbny,kfbh,
               (Select JSLX From t_kcsm@zczm_jc Where id = t.id) As JSLX, (sfcs)
          From t_fhmx@zczm_jc t
         Where dbrq < trunc(Date '$end')
           And dbrq >= trunc(Date '$start')
           And jswc = '1'";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);
						$sql_gc = "Insert Into t_month_fhjsb
        (ywbmbh, dh, mc, pfpch, bz, dbrq, pfrq, zmy, zsy, cbj, tslsh, jsrq, sm, dj, zdqh,
         zdxh,cbny, kfbh, JSLX, zcs)
        Select ywbmbh, dh, (Select dm From t_dm@zczm_jc Where dh = t.dh) As mc, pfpch, bz As bz,
               (dbrq) As dbrq, pfrq,
               (sfcs * (Select dj From t_kcsm@zczm_jc Where id = t.id)) As zmy,
               ROUND((nvl(sfcs * (Select dj From t_kcsm@zczm_jc Where id = t.id) * fhzk / 100, 0)),
                      2) As zsy, (nvl(cbj, 0)) As cbj, 'BJS' As tslsh, czrq As jsrq,
               (Select sm From t_kcsm@zczm_jc Where id = t.id) As sm,
               (Select dj From t_kcsm@zczm_jc Where id = t.id) As dj, zdqh, zdxh, 
			   (Select cbny From t_kcsm@zczm_jc Where id = t.id) As cbny,kfbh,
               (Select JSLX From t_kcsm@zczm_jc Where id = t.id) As JSLX, (sfcs)
          From t_fhmx1_bf@zczm_jc t
         Where dbrq < trunc(Date '$end')
           And czrq >= trunc(Date '$start')";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);
						$sql_gc = "Insert Into t_month_fhjsb
        (ywbmbh, dh, mc, pfpch, bz, dbrq, pfrq, zmy, zsy, cbj, tslsh, jsrq, cwqrpc, sm,
         dj, zdqh, zdxh, cbny,kfbh, JSLX, zcs)
        Select ywbmbh, dh, (Select dm From t_dm@zczm_jc Where dh = t.dh) As mc, pfpch, bz As bz,
               (dbrq) As dbrq, pfrq,
               (sfcs * (Select dj From t_kcsm@zczm_jc Where id = t.id)) As zmy,
               ROUND((nvl(sfcs * (Select dj From t_kcsm@zczm_jc Where id = t.id) * fhzk / 100, 0)),
                      2) As zsy, (nvl(cbj, 0)) As cbj, tslsh,
               (Select cwqrrq From t_tshz@zczm_jc Where tslsh = t.tslsh) jsrq,
               (Select cwqrpc From t_tshz@zczm_jc Where tslsh = t.tslsh) cwqrpc,
               (Select sm From t_kcsm@zczm_jc Where id = t.id) As sm,
               (Select dj From t_kcsm@zczm_jc Where id = t.id) As dj, zdqh, zdxh,
			   (Select cbny From t_kcsm@zczm_jc Where id = t.id) As cbny,kfbh,
               (Select JSLX From t_kcsm@zczm_jc Where id = t.id) As JSLX, (sfcs)
          From t_fhmx2@zczm_jc t
         Where dbrq < trunc(Date '$end')
           And Exists (Select tslsh
                  From t_tshz@zczm_jc
                 Where tslsh = t.tslsh
                   And (cwqrrq >= trunc(Date '$start') Or cwqrrq Is Null))";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);
						$sql_gc = "Insert Into t_month_xtjsb (ywbmbh, kfbh, dh, dm, pch, thrq, zmy, zsy, zcb, tslsh, jsrq, cwqrpc, jslx, ZDQH,zdxh,cbny, zcs) Select ywbmbh, kfbh, dh, (Select dm From t_dm@zczm_jc Where dh = t.dh) As mc, pch, Max(lrrq1) As thrq, 
Sum(-thcs * (Select dj From t_kcsm@zczm_jc Where Id = t.id)) As zmy, 
Sum(ROUND(- (thcs * (Select dj From t_kcsm@zczm_jc Where Id = t.id) * thzk / 100), 2)) As zsy, 
Sum(nvl(-thcs * (Select dj From t_kcsm@zczm_jc Where Id = t.id) * T.Pjzk / 100, 0)) As cbj, tslsh,
 (Select cwqrrq From t_tshz@zczm_jc Where tslsh = t.tslsh) As jsrq, 
 (Select cwqrpc From t_tshz@zczm_jc Where tslsh = t.tslsh) As cwqrpc, a.jslx, T.ZDQH,t.zdxh,
a.cbny As cbny, 
 Sum(-thcs) From t_khthmx@zczm_jc t, t_kcsm@zczm_jc a Where lrrq1 < trunc(Date '$end') And (tslsh Is Null Or Exists (Select tslsh From t_tshz@zczm_jc Where tslsh = t.tslsh And (cwqrrq >= trunc(Date '$start') Or cwqrrq Is Null)))
 And t.id = a.id Group By dh, pch, tslsh, ywbmbh, kfbh, T.ZDQH, a.jslx,t.zdxh,a.cbny";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);
						$sql_gc = "Insert Into t_month_xtjsb1
        (ywbmbh, kfbh, dh, dm, pch, thrq, zmy, zsy, zcb, tslsh, jsrq, cwqrpc, zcs)
        Select ywbmbh, kfbh, dh, (Select dm From t_dm@zczm_jc Where dh = t.dh) As mc, pch,
               Max(lrrq1) As thrq,
               Sum(-thcs * (Select dj From t_kcsm@zczm_jc Where Id = t.id)) As zmy,
               Sum(ROUND(- (thcs * (Select dj From t_kcsm@zczm_jc Where Id = t.id) * thzk / 100),
                          2)) As zsy, Sum(nvl(-cbj1, 0)) As cbj, tslsh,
               (Select cwqrrq From t_tshz@zczm_jc Where tslsh = t.tslsh) As jsrq,
               (Select cwqrpc From t_tshz@zczm_jc Where tslsh = t.tslsh) As cwqrpc, Sum(-thcs)
          From t_khthmx@zczm_jc t, t_kcsm@zczm_jc a
         Where lrrq1 < trunc(Date '$end')
           And (tslsh Is Null Or Exists
                (Select tslsh
                   From t_tshz@zczm_jc
                  Where tslsh = t.tslsh
                    And (cwqrrq >= trunc(Date '$start') Or cwqrrq Is Null)))
           And t.id = a.id
         Group By dh, pch, tslsh, ywbmbh, kfbh";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);
						$sql_gc = "Insert Into t_month_fhgzjsb
        (ywbmbh, kfbh, dh, dm, gzpch, gzrq, zmy, zsy, zcb, tslsh, jsrq, bz, jslx, ZDQH,
         zcs)
        Select ywbmbh, kfbh, dh, (Select dm From t_dm@zczm_jc Where dh = t.dh) As mc, gzpch,
               Min(gzrq),
               Sum((djcs * djdj) - (ysdj * yscs)) As zmy,
               ROUND(Sum(((djcs * djdj * djzk / 100) - (ysdj * yscs * yszk / 100))), 2) As zsy,
               Sum(0) As zcb, tslsh,
               (Select cwqrrq From t_tshz@zczm_jc Where tslsh = t.tslsh) As jsrq, Min(bz), a.jslx,
               T.ZDQH, Sum(djcs - yscs)
          From t_fhgz@zczm_jc t, t_kcsm@zczm_jc a
         Where a.id = t.id
           And (tslsh Is Null Or Exists
                (Select tslsh
                   From t_tshz@zczm_jc
                  Where tslsh = t.tslsh
                    And (cwqrrq >= trunc(Date '$start') Or cwqrrq Is Null)) And
                gzrq < trunc(Date '$end'))
           And gzrq < trunc(Date '$end')
         Group By dh, ywbmbh, kfbh, gzpch, tslsh, a.jslx, T.ZDQH";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);
						//2018-09-25 edit by zhang
						$sql_gc = "Update t_month_fhjsb b
			 Set ghdw = (Select ghdwh
								From t_jczdml@zczm_jc a
								Where a.zdqh = b.zdqh
								And a.zdxh = b.zdxh)";
						$res = $ora->query($sql_gc);
						$sql_gc = "Update t_month_fhjsb b Set ghdwmc = (Select mc From t_ghdw@zczm_jc Where bh = b.ghdw)";
						$res = $ora->query($sql_gc);
						$sql_gc = "Update t_month_xtjsb b
			 Set ghdw = (Select ghdwh
											From t_jczdml@zczm_jc a
										 Where a.zdqh = b.zdqh
											 And a.zdxh = b.zdxh)";
						$res = $ora->query($sql_gc);
						$sql_gc = "Update t_month_xtjsb b Set ghdwmc = (Select mc From t_ghdw@zczm_jc Where bh = b.ghdw)";
						$res = $ora->query($sql_gc);
						
						//过程二
						$sql_gc = "Delete From T_MONTH_HZB_NIAN_TMP_ZDQH";
						$res = $ora->query($sql_gc);
						$sql_gc = "Delete From T_MONTH_HZB_NIAN_ZDQH";
						$res = $ora->query($sql_gc);
						$sql_gc = "Delete From T_MONTH_HZB_NIAN";
						$res = $ora->query($sql_gc);
						$sql_gc = "Insert Into T_MONTH_HZB_NIAN_TMP_ZDQH
        (lx, bh, jfmy, jfsy, jfcb, bb, YWBMBH, ZDQH)
        Select 'DH', GHDWH, Sum(my), Sum(Sys), Sum(sys), 'DH', YWBMBH, SUBSTR(ZDQH, 1, 4)
          From T_MONTH_DHJSB
         Where SHRQ >= Date '$start'
           And SHRQ < Date '$end'
         Group By YWBMBH, GHDWH, SUBSTR(ZDQH, 1, 4)";
		 //echo $sql_gc;exit;
						$res = $ora->query($sql_gc);
						$sql_gc = "Insert Into T_MONTH_HZB_NIAN_TMP_ZDQH
        (lx, bh, jfmy, jfsy, jfcb, bb, YWBMBH, ZDQH)
        Select 'DH', GHDWH, 0, Sum(Sys), Sum(sys), 'MF', YWBMBH, SUBSTR(ZDQH, 1, 4)
          From T_MONTH_DHJSB_mf
         Where SHRQ >= Date '$start'
           And SHRQ < Date '$end'
         Group By YWBMBH, GHDWH, SUBSTR(ZDQH, 1, 4)";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);
						$sql_gc = "Insert Into T_MONTH_HZB_NIAN_TMP_ZDQH
        (lx, bh, jfmy, jfsy, jfcb, bb, YWBMBH, ZDQH)
        Select 'DH', GHDWH, Sum(Zmy), Sum(ZSY), Sum(ZSY), 'JT', YWBMBH, SUBSTR(ZDQH, 1, 4)
          From T_MONTH_JTJSB
         Where DBRQ >= Date '$start'
           And DBRQ < Date '$end'
         Group By GHDWH, YWBMBH, SUBSTR(ZDQH, 1, 4)";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);
						$sql_gc = "Insert Into T_MONTH_HZB_NIAN_TMP_ZDQH
        (lx, bh, jSmy, jSsy, jscb, bb, YWBMBH, ZDQH)
        Select 'DH', GHDWH, Sum(my), Sum(Sys), Sum(Sys), 'DH', YWBMBH, SUBSTR(ZDQH, 1, 4)
          From T_MONTH_DHJSB
         Where JSRQ >= Date '$start'
           And JSRQ < Date '$end'
         Group By GHDWH, YWBMBH, SUBSTR(ZDQH, 1, 4)";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);
						$sql_gc = "Insert Into T_MONTH_HZB_NIAN_TMP_ZDQH
        (lx, bh, jSmy, jSsy, jscb, bb, YWBMBH, ZDQH)
        Select 'DH', GHDWH, 0, Sum(Sys), Sum(Sys), 'MF', YWBMBH, SUBSTR(ZDQH, 1, 4)
          From t_month_dhjsb_mf
         Where JSRQ >= Date '$start'
           And JSRQ < Date '$end'
         Group By GHDWH, YWBMBH, SUBSTR(ZDQH, 1, 4)";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);
						$sql_gc = "Insert Into T_MONTH_HZB_NIAN_TMP_ZDQH
        (lx, bh, jsmy, jssy, jscb, bb, YWBMBH, ZDQH)
        Select 'DH', GHDWH, Sum(Zmy), Sum(ZSY), Sum(zsy), 'JT', YWBMBH, SUBSTR(ZDQH, 1, 4)
          From T_MONTH_JTJSB
         Where JSRQ >= Date '$start'
           And JSRQ < Date '$end'
         Group By GHDWH, YWBMBH, SUBSTR(ZDQH, 1, 4)";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);
						$sql_gc = "Insert Into T_MONTH_HZB_NIAN_TMP_ZDQH
        (lx, YWBMBH, bh, qcwjmy, qcwjsy, qcwjcb, bb, ZDQH)
        Select 'DH', YWBMBH, GHDWH, Sum(my), Sum(Sys), Sum(Sys), 'DH', SUBSTR(ZDQH, 1, 4)
          From T_MONTH_DHJSB
         Where shrq < Date '$start'
           And (jsrq Is Null Or jsrq >= Date '$start')
         Group By YWBMBH, GHDWH, SUBSTR(ZDQH, 1, 4)";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);
						
						$sql_gc = "Insert Into T_MONTH_HZB_NIAN_TMP_ZDQH
        (lx, YWBMBH, bh, qcwjmy, qcwjsy, qcwjcb, bb, ZDQH)
        Select 'DH', YWBMBH, GHDWH, Sum(Zmy), Sum(ZSY), Sum(ZSY), 'JT', SUBSTR(ZDQH, 1, 4)
          From T_MONTH_JTJSB
         Where DBrq < Date '$start'
           And (jsrq Is Null Or jsrq >= Date '$start')
         Group By YWBMBH, GHDWH, SUBSTR(ZDQH, 1, 4)";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);

						$sql_gc = "Insert Into T_MONTH_HZB_NIAN_TMP_ZDQH
        (lx, YWBMBH, bh, qcwjmy, qcwjsy, qcwjcb, bb, ZDQH)
        Select 'DH', YWBMBH, GHDWH, 0, Sum(Sys), Sum(Sys), 'MF', SUBSTR(ZDQH, 1, 4)
          From t_month_dhjsb_mf
         Where shrq < Date '$start'
           And (JSRQ Is Null Or jsrq >= Date '$start')
         Group By YWBMBH, GHDWH, SUBSTR(ZDQH, 1, 4)";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);
						$sql_gc = "Insert Into T_MONTH_HZB_NIAN_TMP_ZDQH
        (lx, YWBMBH, bh, qmwjmy, qmwjsy, qmwjcb, bb, ZDQH)
        Select 'DH', YWBMBH, GHDWH, Sum(my), Sum(Sys), Sum(Sys), 'DH', SUBSTR(ZDQH, 1, 4)
          From T_MONTH_DHJSB
         Where shrq < Date '$end'
           And (jsrq Is Null Or jsrq >= Date '$end')
         Group By YWBMBH, GHDWH, SUBSTR(ZDQH, 1, 4)";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);
						
						$sql_gc = "Insert Into T_MONTH_HZB_NIAN_TMP_ZDQH
        (lx, YWBMBH, bh, qmwjmy, qmwjsy, qmwjcb, bb, ZDQH)
        Select 'DH', YWBMBH, GHDWH, 0, Sum(Sys), Sum(Sys), 'MF', SUBSTR(ZDQH, 1, 4)
          From t_month_dhjsb_mf
         Where shrq < Date '$end'
           And (JSRQ Is Null Or jsrq >= Date '$end')
         Group By YWBMBH, GHDWH, SUBSTR(ZDQH, 1, 4)";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);
						
						$sql_gc = "Insert Into T_MONTH_HZB_NIAN_TMP_ZDQH
        (lx, YWBMBH, bh, qmwjmy, qmwjsy, qmwjcb, bb, ZDQH)
        Select 'DH', YWBMBH, GHDWH, Sum(Zmy), Sum(ZSY), Sum(ZSY), 'JT', SUBSTR(ZDQH, 1, 4)
          From T_MONTH_JTJSB
         Where jsrq Is Null
            Or jsrq >= Date '$end'
         Group By YWBMBH, GHDWH, SUBSTR(ZDQH, 1, 4)";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);
						$sql_gc = "Insert Into T_MONTH_HZB_NIAN_TMP_ZDQH
        (lx, bh, jfmy, jfsy, jfcb, bb, YWBMBH, ZDQH)
        Select 'FH', DH, Sum(Zmy), Sum(ZSY), Sum(cbj), 'FH', YWBMBH, SUBSTR(ZDQH, 1, 4)
          From T_MONTH_FHJSB
         Where DBRQ >= Date '$start'
           And DBRQ < Date '$end'
         Group By DH, YWBMBH, SUBSTR(ZDQH, 1, 4)";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);
						$sql_gc = "Insert Into T_MONTH_HZB_NIAN_TMP_ZDQH
        (lx, bh, jfmy, jfsy, jfcb, bb, YWBMBH, ZDQH)
        Select 'FH', dH, Sum(Zmy), Sum(ZSY), Sum(zcb), 'XT', YWBMBH, SUBSTR(ZDQH, 1, 4)
          From T_MONTH_xTJSB
         Where THRQ >= Date '$start'
           And THRQ < Date '$end'
         Group By dh, YWBMBH, SUBSTR(ZDQH, 1, 4)";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);
						$sql_gc = "Insert Into T_MONTH_HZB_NIAN_TMP_ZDQH
        (lx, bh, jfmy, jfsy, jfcb, bb, YWBMBH, ZDQH)
        Select 'FH', dH, Sum(Zmy), Sum(ZSY), Sum(zcb), 'GZ', YWBMBH, SUBSTR(ZDQH, 1, 4)
          From T_MONTH_fHgzJSB
         Where gzRQ >= Date '$start'
           And gzRQ < Date '$end'
         Group By dh, YWBMBH, SUBSTR(ZDQH, 1, 4)";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);
						$sql_gc = "Insert Into T_MONTH_HZB_NIAN_TMP_ZDQH
        (lx, bh, jsmy, jssy, jscb, bb, YWBMBH, ZDQH)
        Select 'FH', DH, Sum(Zmy), Sum(ZSY), Sum(cbj), 'FH', YWBMBH, SUBSTR(ZDQH, 1, 4)
          From T_MONTH_FHJSB
         Where jsRQ >= Date '$start'
           And jsRQ < Date '$end'
         Group By DH, YWBMBH, SUBSTR(ZDQH, 1, 4)";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);
						$sql_gc = "Insert Into T_MONTH_HZB_NIAN_TMP_ZDQH
        (lx, bh, jsmy, jssy, jscb, bb, YWBMBH, ZDQH)
        Select 'FH', dH, Sum(Zmy), Sum(ZSY), Sum(zcb), 'XT', YWBMBH, SUBSTR(ZDQH, 1, 4)
          From T_MONTH_xTJSB
         Where jsRQ >= Date '$start'
           And jsRQ < Date '$end'
         Group By dh, YWBMBH, SUBSTR(ZDQH, 1, 4)";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);
						
						
						$sql_gc = "Insert Into T_MONTH_HZB_NIAN_TMP_ZDQH
        (lx, bh, jsmy, jssy, jscb, bb, YWBMBH, ZDQH)
        Select 'FH', dH, Sum(Zmy), Sum(ZSY), Sum(zcb), 'GZ', YWBMBH, SUBSTR(ZDQH, 1, 4)
          From T_MONTH_fhgzJSB
         Where jsRQ >= Date '$start'
           And jsRQ < Date '$end'
         Group By dh, YWBMBH, SUBSTR(ZDQH, 1, 4)";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);
						
						$sql_gc = "Insert Into T_MONTH_HZB_NIAN_TMP_ZDQH
        (lx, bh, qcwjmy, qcwjsy, qcwjcb, bb, YWBMBH, ZDQH)
        Select 'FH', dH, Sum(Zmy), Sum(ZSY), Sum(cbj), 'FH', YWBMBH, SUBSTR(ZDQH, 1, 4)
          From T_MONTH_fHJSB
         Where dbrq < Date '$start'
           And (tslsh Is Null Or jsRQ >= Date '$start')
         Group By dh, YWBMBH, SUBSTR(ZDQH, 1, 4)";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);
						$sql_gc = "Insert Into T_MONTH_HZB_NIAN_TMP_ZDQH
        (lx, bh, qcwjmy, qcwjsy, qcwjcb, bb, YWBMBH, ZDQH)
        Select 'FH', dH, Sum(Zmy), Sum(ZSY), Sum(zcb), 'XT', YWBMBH, SUBSTR(ZDQH, 1, 4)
          From T_MONTH_xTJSB
         Where thrq < Date '$start'
           And (tslsh Is Null Or jsRQ >= Date '$start')
         Group By dh, YWBMBH, SUBSTR(ZDQH, 1, 4)";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);
						$sql_gc = "Insert Into T_MONTH_HZB_NIAN_TMP_ZDQH
        (lx, bh, qcwjmy, qcwjsy, qcwjcb, bb, YWBMBH, ZDQH)
        Select 'FH', DH, Sum(Zmy), Sum(ZSY), Sum(zcb), 'GZ', YWBMBH, SUBSTR(ZDQH, 1, 4)
          From T_MONTH_FHGZJSB
         Where gzrq < Date '$start'
           And (tslsh Is Null Or jsRQ >= Date '$start')
         Group By DH, YWBMBH, SUBSTR(ZDQH, 1, 4)";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);
						$sql_gc = "Insert Into T_MONTH_HZB_NIAN_TMP_ZDQH
        (lx, bh, qmwjmy, qmwjsy, qmwjcb, bb, YWBMBH, ZDQH)
        Select 'FH', dH, Sum(Zmy), Sum(ZSY), Sum(cbj), 'FH', YWBMBH, SUBSTR(ZDQH, 1, 4)
          From T_MONTH_fHJSB
         Where tslsh Is Null
            Or jsRQ >= Date '$end'
         Group By dh, YWBMBH, SUBSTR(ZDQH, 1, 4)";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);
						$sql_gc = "Insert Into T_MONTH_HZB_NIAN_TMP_ZDQH
        (lx, bh, qmwjmy, qmwjsy, qmwjcb, bb, YWBMBH, ZDQH)
        Select 'FH', dH, Sum(Zmy), Sum(ZSY), Sum(zcb), 'XT', YWBMBH, SUBSTR(ZDQH, 1, 4)
          From T_MONTH_xTJSB
         Where tslsh Is Null
            Or jsRQ >= Date '$end'
         Group By dh, YWBMBH, SUBSTR(ZDQH, 1, 4)";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);
						$sql_gc = "Insert Into T_MONTH_HZB_NIAN_TMP_ZDQH
        (lx, bh, qmwjmy, qmwjsy, qmwjcb, bb, YWBMBH, ZDQH)
        Select 'FH', DH, Sum(Zmy), Sum(ZSY), Sum(zcb), 'GZ', YWBMBH, SUBSTR(ZDQH, 1, 4)
          From T_MONTH_FHGZJSB
         Where tslsh Is Null
            Or jsRQ >= Date '$end'
         Group By DH, YWBMBH, SUBSTR(ZDQH, 1, 4)";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);
						$sql_gc = "Insert Into T_MONTH_HZB_NIAN_ZDQH
        (LX, BH, YWBMBH, qcwjmy, qcwjsy, JFMY, JFSY, JSMY, JSSY, QMWJMY, QMWJSY, ZDQH)
        Select lx, bh, ywbmbh, Sum(nvl(qcwjmy, 0)), Sum(nvl(qcwjsy, 0)), Sum(nvl(jfmy, 0)),
               Sum(nvl(jfsy, 0)), Sum(nvl(jsmy, 0)), Sum(nvl(jssy, 0)),
               Sum(nvl(qmwjmy, 0)), Sum(nvl(qmwjsy, 0)), ZDQH
          From T_MONTH_HZB_NIAN_TMP_ZDQH
         Group By ywbmbh, lx, bh, ZDQH";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);
						$sql_gc = "Insert Into T_MONTH_HZB_NIAN
        (LX, BH, YWBMBH, qcwjmy, qcwjsy, qcwjcb, JFMY, JFSY, jfcb, JSMY, JSSY, jscb,
         QMWJMY, QMWJSY, qmwjcb)
        Select lx, bh, ywbmbh, Sum(nvl(qcwjmy, 0)), Sum(nvl(qcwjsy, 0)),
               Sum(nvl(qcwjcb, 0)), Sum(nvl(jfmy, 0)), Sum(nvl(jfsy, 0)),
               Sum(nvl(jfcb, 0)), Sum(nvl(jsmy, 0)), Sum(nvl(jssy, 0)), Sum(nvl(jscb, 0)),
               Sum(nvl(qmwjmy, 0)), Sum(nvl(qmwjsy, 0)), Sum(nvl(qmwjcb, 0))
          From T_MONTH_HZB_NIAN_TMP_ZDQH
         Group By ywbmbh, lx, bh";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);
						$sql_gc = "Update T_MONTH_HZB_NIAN_ZDQH T
       Set DMMC = (Select DM From T_DM Where DH = T.BH)
     Where LX = 'FH'";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);
						
						$sql_gc = "Update T_MONTH_HZB_NIAN_ZDQH T
       Set DMMC = (Select MC From T_GHDW Where BH = T.BH)
     Where LX = 'DH'";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);
						$sql_gc = "Update T_MONTH_HZB_NIAN T
       Set DMMC = (Select DM From T_DM Where DH = T.BH)
     Where LX = 'FH'";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);
						$sql_gc = "Update T_MONTH_HZB_NIAN T
       Set DMMC = (Select MC From T_GHDW Where BH = T.BH)
     Where LX = 'DH'";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);
					
					
						$guocheng[guocheng]=$timetype;	
						setcache("guocheng",$guocheng);
					}			
					
			}	
				
				if($yewuleixing=='月报'){
					//$info=getcache("jiaocai_yuebao_".$start.'_'.$end);
					$sql="Select * From t_Month_Hzb_Nian Where ywbmbh='$admin_username'";

					//if(empty($info)){
						//教材月报<>
						$row=$ora->query($sql);
						//echo '教材月报查询';
						//print_r($row);exit;
						//$info[jiaocaiyuebao]=$row;
						
						//setcache("jiaocai_yuebao_".$start.'_'.$end,$info);
					//}else{
					//	$row = $info[jiaocaiyuebao];
					//}
					if($daochu && $daochu=='daochu'){
						
						$fileName = '教材月报';
						$total = count($row);
						$title = array('YWBMBH','LX','BH','DMMC','QCWJMY','QCWJSY','QCWJCB','JFMY','JFSY','JFCB','JSMY','JSSY','JSCB','QMWJMY','QMWJSY','QMWJCB','SYLX','BB','MINDATE','MAXDATE','NY','BJSMY','BJSSY');
						//$this->articleAccessLog($sql,$fileName,$title,$total,$yewugongsi);
						if($_SESSION['roleid']==26 && $daochulx =='导出到服务器指定位置'){
							$this->yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username,$timetype);
						}elseif($_SESSION['roleid']==26 && $daochulx =='从浏览器中下载'){
							$this->articleAccessLog($sql,$fileName,$title,$total,$yewugongsi);
						}
						//$this->yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username);
					}

				}elseif($yewuleixing=='流水-到货明细'){
					//$info=getcache("jiaocai_daohuomingxi_".$start.'_'.$end);
					//$sql="Select * From t_month_dhjsb@zczm_jc Where ywbmbh='$admin_username' And shrq>=Date'$start' and shrq< Date'$end'";
					$sql="Select YWBMBH,GHDWH,MC,DHPCH,YSDJ,SSDH,ZDQH,ZDXH,SM,DJ,JXSSSL,MY,SYS,ZDPCH,JSLX,ZDPCH1,TSLSH,YSDBRQ,JCDRQ,KQBJ,JSGHDWH,YSGHDWH,FLOWID_DHMX,to_char(t_month_dhjsb.SHRQ, 'YYYY-MM-DD HH24:MI:SS') as SHRQ ,to_char(t_month_dhjsb.YSRQ, 'YYYY-MM-DD HH24:MI:SS') as YSRQ,to_char(t_month_dhjsb.JSRQ, 'YYYY-MM-DD HH24:MI:SS') as JSRQ From t_month_dhjsb Where ywbmbh='$admin_username' And shrq>=Date'$start' and shrq < Date'$end'";
					//echo $sql;exit;
					//if(empty($info)){
						//教材 流水-到货明细<>
						
						$row=$ora->query($sql);
						//echo '教材-流水-到货明细查询';print_r($row);//exit;
						//$info[jiaocaidaohuomingxi]=$row;  //查找到数据就该写导出了
						
						//setcache("jiaocai_daohuomingxi_".$start.'_'.$end,$info);
					//}else{
					//	$row = $info[jiaocaidaohuomingxi];
					//}
					
					if($daochu && $daochu=='daochu'){
						
						$fileName = '教材到货明细';
						$total = count($row);
						$title = array('YWBMBH','GHDWH','MC','DHPCH','SHRQ','YSDJ','SSDH','ZDQH','ZDXH','SM','DJ','JXSSSL','MY','SYS','ZDPCH','JSRQ','JSLX','ZDPCH1','TSLSH','YSRQ','YSDBRQ','JCDRQ','KQBJ','JSGHDWH','YSGHDWH','FLOWID_DHMX');
						//$this->articleAccessLog($sql,$fileName,$title,$total,$yewugongsi);
						if($_SESSION['roleid']==26 && $daochulx =='导出到服务器指定位置'){
							$this->yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username,$timetype);
						}elseif($_SESSION['roleid']==26 && $daochulx =='从浏览器中下载'){
							$this->articleAccessLog($sql,$fileName,$title,$total,$yewugongsi);
						}
						//$this->yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username);
					}

				}elseif($yewuleixing=='流水-进退明细'){
					//$info=getcache("jiaocai_jintuimingxi_".$start.'_'.$end);
					//$sql="Select * From t_month_jtjsb@zczm_jc Where ywbmbh='$admin_username' And dbrq>=Date'$start' and dbrq< Date'$end'";
					$sql="Select YWBMBH,GHDWH,MC,ID,SM,DJ,ZMY,ZSY,BZ,YWPCH,ZDPCH,JSRQ,JSLX,ZDQH,ZDXH,ZDPCH1,TSLSH,NF,NFHRQ,NJSRQ,JSGHDWH,YSGHDWH,ZCS,to_char(t_month_jtjsb.DBRQ, 'YYYY-MM-DD HH24:MI:SS') as DBRQ
 From t_month_jtjsb Where ywbmbh='$admin_username' And dbrq>=Date'$start' and dbrq < Date'$end'";
					//echo $sql;exit;
					//if(empty($info)){
						//教辅 流水-进退明细<>
						$row=$ora->query($sql);
						//echo '教材-流水-进退明细查询';
						//print_r($row);exit;
					//	$info[jiaocaijintuimingxi]=$row;  //查找到数据就该写导出了
						
					//	setcache("jiaocai_jintuimingxi_".$start.'_'.$end,$info);
					//}else{
					//	$row = $info[jiaocaijintuimingxi];
					//}
					
					if($daochu && $daochu=='daochu'){
						
						$fileName = '教材进退明细';
						$total = count($row);
						$title = array('YWBMBH','GHDWH','MC','DBRQ','ID','SM','DJ','ZMY','ZSY','BZ','YWPCH','ZDPCH','JSRQ','JSLX','ZDQH','ZDXH','ZDPCH1','TSLSH','NF','NFHRQ','NJSRQ','JSGHDWH','YSGHDWH','ZCS');
						//$this->articleAccessLog($sql,$fileName,$title,$total,$yewugongsi);
						if($_SESSION['roleid']==26 && $daochulx =='导出到服务器指定位置'){
							$this->yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username,$timetype);
						}elseif($_SESSION['roleid']==26 && $daochulx =='从浏览器中下载'){
							$this->articleAccessLog($sql,$fileName,$title,$total,$yewugongsi);
						}
						//$this->yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username);
					}


				}elseif($yewuleixing=='流水-出版社免费让利明细'){
					//$info=getcache("jiaocai_ranglimingxi_".$start.'_'.$end);
					$sql="Select YWBMBH,GHDWH,MC,DHPCH,SHRQ,YSDJ,SSDH,ZDQH,ZDXH,SM,DJ,JXSSSL,MY,SYS,ZDPCH,JSRQ,JSLX,JSGHDWH,YSGHDWH,TSLSH
 From t_month_dhjsb_mf Where ywbmbh='$admin_username' And shrq>=Date'$start' and shrq < Date'$end'";
					//echo $sql;exit;
					//if(empty($info)){
						//教材 流水-出版社免费让利明细<>
						
						$row=$ora->query($sql);
						//echo '教材-流水-出版社免费让利明细';
						//print_r($row);exit;//先die吧，有数据了再放开，看看都有啥字段
						//$info[jiaocairanglimingxi]=$row;  //查找到数据就该写导出了
						
						//setcache("jiaocai_ranglimingxi_".$start.'_'.$end,$info);
					//}else{
					//	$row = $info[jiaocairanglimingxi];
					//}
					if($daochu && $daochu=='daochu'){
						$fileName = '教材出版社免费让利明细';
						$total = count($row);
						$title = array('YWBMBH','GHDWH','MC','DHPCH','SHRQ','YSDJ','SSDH','ZDQH','ZDXH','SM','DJ','JXSSSL','MY','SYS','ZDPCH','JSRQ','JSLX','JSGHDWH','YSGHDWH','TSLSH');
						//$this->articleAccessLog($sql,$fileName,$title,$total,$yewugongsi);
						if($_SESSION['roleid']==26 && $daochulx =='导出到服务器指定位置'){
							$this->yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username,$timetype);
						}elseif($_SESSION['roleid']==26 && $daochulx =='从浏览器中下载'){
							$this->articleAccessLog($sql,$fileName,$title,$total,$yewugongsi);
						}
						//$this->yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username);
					}
					
					
				}elseif($yewuleixing=='流水-销退明细'){
					//$info=getcache("jiaocai_weijiemingxi_".$start.'_'.$end);
					//$sql="Select * From t_month_xtjsb@zczm_jc Where ywbmbh='$admin_username' And thrq>=Date'$start' and thrq< Date'$end'"; 
					$sql="Select ghdw,ghdwmc,YWBMBH,KFBH,DH,DM,PCH,ZMY,ZSY,ZCB,TSLSH,JSLX,ZDQH,NF,NFHRQ,NJSRQ,JB,CWQRPC,ZCS,DJ,ID,BB,SM,ISBN,CBNY,to_char(t_month_xtjsb.THRQ, 'YYYY-MM-DD HH24:MI:SS') as THRQ,to_char(t_month_xtjsb.JSRQ, 'YYYY-MM-DD HH24:MI:SS') as JSRQ From t_month_xtjsb Where ywbmbh='$admin_username' And thrq>=Date'$start' and thrq < Date'$end'";
					//echo $sql;exit;
					
						$row=$ora->query($sql);
						//print_r($row);exit;
					
					if($daochu && $daochu=='daochu'){
						$fileName = '教材销退明细';
						$total = count($row);
						$title = array('GHDW','GHDWMC','YWBMBH','KFBH','DH','DM','PCH','THRQ','ZMY','ZSY','ZCB','TSLSH','JSRQ','JSLX','ZDQH','NF','NFHRQ','NJSRQ','JB','CWQRPC','ZCS','DJ','ID','BB','SM','ISBN','CBNY');
						//$this->articleAccessLog($sql,$fileName,$title,$total,$yewugongsi);
						if($_SESSION['roleid']==26 && $daochulx =='导出到服务器指定位置'){
							$this->yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username,$timetype);
						}elseif($_SESSION['roleid']==26 && $daochulx =='从浏览器中下载'){
							$this->articleAccessLog($sql,$fileName,$title,$total,$yewugongsi);
						}
						//$this->yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username);
					}
					
				}elseif($yewuleixing=='流水-基层店免费让利明细'){
					//$info=getcache("jiaocai_jcranglimingxi_".$start.'_'.$end);
					$sql="Select * From t_month_fhgzjsb Where ywbmbh='$admin_username' And gzrq>=Date'$start' and gzrq < Date'$end'";
					//echo $sql;exit;
					//if(empty($info)){
						//教材 流水-基层店免费让利明细<>
						
						$row=$ora->query($sql);
						//echo '教材-流水-基层店免费让利明细';
						//print_r($row);exit;//先die吧，有数据了再放开，看看都有啥字段
						//$info[jiaocaijcranglimingxi]=$row;  //查找到数据就该写导出了
						
						//setcache("jiaocai_jcranglimingxi_".$start.'_'.$end,$info);
					//}else{
					//	$row = $info[jiaocaijcranglimingxi];
					//}
					
					if($daochu && $daochu=='daochu'){

						$fileName = '教材基层店免费让利明细';
						$total = count($row);
						$title = array('YWBMBH','KFBH','DH','DM','GZPCH','GZRQ','ZMY','ZSY','ZCB','TSLSH','JSRQ','BZ','JSLX','ZDQH','NF','NFHRQ','NJSRQ','JB','ZCS');
						//$this->articleAccessLog($sql,$fileName,$title,$total,$yewugongsi);
						if($_SESSION['roleid']==26 && $daochulx =='导出到服务器指定位置'){
							$this->yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username,$timetype);
						}elseif($_SESSION['roleid']==26 && $daochulx =='从浏览器中下载'){
							$this->articleAccessLog($sql,$fileName,$title,$total,$yewugongsi);
						}
						//$this->yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username);
					}

				}elseif($yewuleixing=='流水-发货明细'){
					//$info=getcache("jiaocai_fahuomingxi_".$start.'_'.$end);
					//$sql="Select * From t_month_fhjsb@zczm_jc Where ywbmbh='$admin_username' And dbrq>=Date'$start' and dbrq< Date'$end'";
					$sql="Select cbny,ghdw,ghdwmc,YWBMBH,DH,MC,PFPCH,BZ,ZMY,ZSY,CBJ,TSLSH,JSRQ,SM,DJ,ZDQH,ZDXH,KFBH,JSLX,NF,NFHRQ,NJSRQ,JB,CWQRPC,YSDBRQ,ZCS,to_char(t_month_fhjsb.DBRQ, 'YYYY-MM-DD HH24:MI:SS') as DBRQ
,to_char(t_month_fhjsb.PFRQ, 'YYYY-MM-DD HH24:MI:SS') as PFRQ From t_month_fhjsb Where ywbmbh='$admin_username' And dbrq>=Date'$start' and dbrq < Date'$end'";
					//echo $sql;exit;
					//if(empty($info)){

						//教材 流水-发货明细<>
						$row=$ora->query($sql);
						//echo '教材-流水-发货明细';
						//print_r($row);exit;
						//$info[jiaocaifahuomingxi]=$row;  //查找到数据就该写导出了
						
						//setcache("jiaocai_fahuomingxi_".$start.'_'.$end,$info);
					//}else{
					//	$row = $info[jiaocaifahuomingxi];
					//}
					
					if($daochu && $daochu=='daochu'){

						$fileName = '教材发货明细';
						$total = count($row);
						$title = array('CBNY','GHDW','GHDWMC','YWBMBH','DH','MC','PFPCH','BZ','DBRQ','PFRQ','ZMY','ZSY','CBJ','TSLSH','JSRQ','SM','DJ','ZDQH','ZDXH','KFBH','JSLX','NF','NFHRQ','NJSRQ','JB','CWQRPC','YSDBRQ','ZCS');
						//$this->articleAccessLog($sql,$fileName,$title,$total,$yewugongsi);
						if($_SESSION['roleid']==26 && $daochulx =='导出到服务器指定位置'){
							$this->yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username,$timetype);
						}elseif($_SESSION['roleid']==26 && $daochulx =='从浏览器中下载'){
							$this->articleAccessLog($sql,$fileName,$title,$total,$yewugongsi);
						}
						//$this->yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username);
					}


				}elseif($yewuleixing=='库存明细'){
					$dangyue = dy_month(); //当月第一天
					$start = date('Y-m-d',$dangyue['start_time']);

					//$info=getcache("jiaocai_kucunmingxi_".$start.'_'.$end);
					$sql="Select ywbmbh,Id,(Select sm From t_kcsm@zczm_jc Where Id=t.id)As sm,
		 (Select dj From t_kcsm@zczm_jc Where Id=t.id)As dj,
		 		(Select min(mc) From t_bb@zczm_jc Where  bh=(Select bb From t_kcsm@zczm_jc Where Id=t.id))As bb,
				kccs,(kccs*(Select dj From t_kcsm@zczm_jc Where Id=t.id))As my,				
		 (kccs*(Select dj From t_kcsm@zczm_jc Where Id=t.id)*(Select pjzk From t_kcsm@zczm_jc Where Id=t.id)/100)As Sys,
		  (Select dm From t_dm@zczm_jc Where dh=t.kfbh)As kf,
		  (Select min(dm) From t_dm@zczm_jc Where dh=t.cybh)As wl,
			(Select mc From t_lb@zczm_jc Where bh=(Select  (lb) From t_kcsm@zczm_jc Where Id=t.id))as fl,
			hw,
			 (Select mc From t_ghdw@zczm_jc Where bh=(Select min(ghdwh) From t_dhls_jx@zczm_jc Where Id=t.id))As ghdw, 
			 (Select Name From t_user@zczm_jc Where usr_id=(Select ywryh From t_kcsm@zczm_jc Where Id=t.id))As ywy,(Select isbn From t_kcsm@zczm_jc Where Id=t.id)As  isbn,
			 (Select min(bc) From t_kcsm@zczm_jc Where Id=t.id)as bc,
			 (Select bianzh From t_kcsm@zczm_jc Where Id=t.id)As bianzhe,(Select nian From t_kcsm@zczm_jc Where Id=t.id)  As nian
			   From t_Hd_Kcsl_Bf@zczm_jc t Where kccs<>0 And ywbmbh='$admin_username' And bfsj>=Date'$start'";
					//echo $sql;exit;
						$row=$ora->query($sql);
						//print_r($row);exit;
					if($daochu && $daochu=='daochu'){
						
						$fileName = '教材库存明细';
						$total = count($row);
						$title = array('YWBMBH','ID','SM','DJ','版别','KCCS','MY','SYS','KF','WL','分类','HW','供货单位','YWY','ISBN','版次','编者','年');
						if($_SESSION['roleid']==26 && $daochulx =='导出到服务器指定位置'){
							$this->yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username,$timetype);
						}elseif($_SESSION['roleid']==26 && $daochulx =='从浏览器中下载'){
							$this->articleAccessLog($sql,$fileName,$title,$total,$yewugongsi);
						}
					}
					
					
					
				}elseif($yewuleixing=='库存明细提前出'){
					$dangyue = dy_month(); //当月第一天
					$start = date('Y-m-d',$dangyue['start_time']);

					//$info=getcache("jiaocai_kucunmingxi_".$start.'_'.$end);
					$sql="Select ywbmbh,Id,(Select sm From t_kcsm@zczm_jc Where Id=t.id)As sm,
		 (Select dj From t_kcsm@zczm_jc Where Id=t.id)As dj,
		 		(Select min(mc) From t_bb@zczm_jc Where  bh=(Select bb From t_kcsm@zczm_jc Where Id=t.id))As 版别,
				kccs,(kccs*(Select dj From t_kcsm@zczm_jc Where Id=t.id))As my,				
		 (kccs*(Select dj From t_kcsm@zczm_jc Where Id=t.id)*(Select pjzk From t_kcsm@zczm_jc Where Id=t.id)/100)As Sys,
		  (Select dm From t_dm@zczm_jc Where dh=t.kfbh)As kf,
		  (Select min(dm) From t_dm@zczm_jc Where dh=t.cybh)As wl,
			(Select mc From t_lb@zczm_jc Where bh=(Select  (lb) From t_kcsm@zczm_jc Where Id=t.id)),
			hw,
			 (Select mc From t_ghdw@zczm_jc Where bh=(Select min(ghdwh) From t_dhls_jx@zczm_jc Where Id=t.id))As 供货单位, 
			 (Select Name From t_user@zczm_jc Where usr_id=(Select ywryh From t_kcsm@zczm_jc Where Id=t.id))As ywy,(Select isbn From t_kcsm@zczm_jc Where Id=t.id)As  isbn,
			 (Select min(bc) From t_kcsm@zczm_jc Where Id=t.id)as 版次,
			 (Select bianzh From t_kcsm@zczm_jc Where Id=t.id)As 编者,(Select nian From t_kcsm@zczm_jc Where Id=t.id)  As 年
			   From t_kcsl@zczm_jc t Where kccs<>0 And ywbmbh='$admin_username'";
					//echo $sql;exit;
						$row=$ora->query($sql);
						//print_r($row);exit;
					if($daochu && $daochu=='daochu'){
						
						$fileName = '教材库存明细提前出';
						$total = count($row);
						$title = array('YWBMBH','ID','SM','DJ','版别','KCCS','MY','SYS','KF','WL','分类','HW','供货单位','YWY','ISBN','版次','编者','年');
						if($_SESSION['roleid']==26 && $daochulx =='导出到服务器指定位置'){
							$this->yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username,$timetype);
						}elseif($_SESSION['roleid']==26 && $daochulx =='从浏览器中下载'){
							$this->articleAccessLog($sql,$fileName,$title,$total,$yewugongsi);
						}
					}
					
					
					
				}elseif($yewuleixing=='配发中间态'){
					$dangyue = dy_month(); //当月第一天
					$start = date('Y-m-d',$dangyue['start_time']);
					$sql="Select Ywbmbh ,(select dm from t_dm@zczm_jc where dh=a.ywbmbh) as ywbmmc,dh,(select dm from t_dm@zczm_jc where dh=a.dh) as dm,
a.id,(select isbn from t_kcsm@zczm_jc where a.id=id) as isbn,(select sm from t_kcsm@zczm_jc where a.id=id) as sm,
(select dj from t_kcsm@zczm_jc where a.id=id) as dj,(Select round(cbny) From t_kcsm@zczm_jc Where Id=a.id)As cbny,a.pxcs,(select dj*a.pxcs from t_kcsm@zczm_jc where a.id=id) as zmy ,a.sys,a.cbj,
a.pxdh,a.bz,a.zdh,a.sylx,a.sl,a.zjxs,a.qx,a.djbh,a.kfbh,A.Bjlsh,a.cybh,A.Flowid,a.zdqh,a.zdxh,a.zdqh1,a.zdxh1,
to_char(a.pfrq, 'YYYY-MM-DD HH24:MI:SS') as pfrq From t_hd_pfmx@zczm_jc a Where bfsj>=Date'$start' and ckbj='1' and ywbmbh='$admin_username'";

						//echo $sql;exit;

						$row=$ora->query($sql);
						//print_r($row);exit;
						
					if($daochu && $daochu=='daochu'){
						
						$fileName = '教材配发中间态';
						$total = count($row);
						$title = array('YWBMBH','YWBMMC','DH','DM','ID','ISBN','SM','DJ','CBNY','PXCS','ZMY','SYS','CBJ','PXDH','BZ','ZDH','SYLX','SL','ZJXS','QX','DJBH','KFBH','BJLSH','CYBH','FLOWID','ZDQH','ZDXH','ZDQH1','ZDXH1','PFRQ');
						if($_SESSION['roleid']==26 && $daochulx =='导出到服务器指定位置'){
							$this->yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username,$timetype);
						}elseif($_SESSION['roleid']==26 && $daochulx =='从浏览器中下载'){
							$this->articleAccessLog($sql,$fileName,$title,$total,$yewugongsi);
						}
					}
					
					
				}elseif($yewuleixing=='库房转移中间态明细'){
					//$info=getcache("jiaocai_kufangmingxi_".$start.'_'.$end);
					$sql="Select Id ,(Select sm From t_kcsm@zczm_jc Where Id=A.id)As sm,(Select isbn From t_kcsm@zczm_jc Where Id=A.id)As  isbn,
(Select dj From t_kcsm@zczm_jc Where Id=A.id)As dj,(Select min(mc) From t_bb@zczm_jc Where bh=(Select bb From t_kcsm@zczm_jc Where Id=A.id))As bb,
		  (Select mc From t_ghdw@zczm_jc Where bh=(Select min(ghdwh) From t_dhls_jx@zczm_jc Where Id=A.id))As ghdw,(Select Name From t_user@zczm_jc Where usr_id=(Select ywryh From t_kcsm@zczm_jc Where Id=A.id))As ywy,cbny,ywlx,Sum(cs),Sum(cs*(Select dj From t_kcsm@zczm_jc Where Id=a.id))As MY,Sum(sy)As SY From (
Select YWLX,Id,(Select round(cbny) From t_kcsm@zczm_jc Where Id=t_crkls.id)As cbny,
Sum(CRKCS)CS,sum(crksy)sy From t_crkls@zczm_jc Where ywbmbh='$admin_username' And (YWLX='YC' Or YWLX='DC' )And crkrq>=Date'2018-07-01' And crkrq < Date'$end' Group By ywlx,Id
Union All Select YWLX,Id,(Select round(cbny) From t_kcsm@zczm_jc Where Id=t_crkls.id)As cbny,Sum(-CRKCS)CS,sum(-crksy)sy From t_crkls@zczm_jc Where ywbmbh='$admin_username' And ( YWLX='YR'Or YWLX='DR' )
And crkrq >= Date'2018-07-01' And crkrq < Date'$end' Group By ywlx,Id) A Group By Id,ywlx,cbny Having Sum(cs)<>0						
";
					//echo $sql;exit;
					//if(empty($info)){
						//教材 库房转移中间态明细<>
						
						$row=$ora->query($sql);
						//echo '教材-库房转移中间态明细';
						//print_r($row);exit;
						//$info[jiaocaikufangmingxi]=$row;  //查找到数据就该写导出了
						
						//setcache("jiaocai_kufangmingxi_".$start.'_'.$end,$info);
					//}else{
					//	$row = $info[jiaocaikufangmingxi];
					//}
					if($daochu && $daochu=='daochu'){
						
						$fileName = '教材库房转移中间态明细';
						$total = count($row);
						$title = array('ID','SM','ISBN','DJ','版别','供货单位','YWY','CBNY','YWLX','SUM(CS)','MY','SY');
						//$this->articleAccessLog($sql,$fileName,$title,$total,$yewugongsi);
						if($_SESSION['roleid']==26 && $daochulx =='导出到服务器指定位置'){
							$this->yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username,$timetype);
						}elseif($_SESSION['roleid']==26 && $daochulx =='从浏览器中下载'){
							$this->articleAccessLog($sql,$fileName,$title,$total,$yewugongsi);
						}
						//$this->yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username);
					}
					
					
					
				}elseif($yewuleixing=='损益明细'){
					//$info=getcache("jiaocai_sunyimingxi_".$start.'_'.$end);
					$sql="Select KFBH,(Select SM From T_KCSM@zczm_jc Where Id=T.ID) As SM,CRKCS,(Select dj From t_kcsm@zczm_jc Where T.id=Id)as dj,(crkcs*(Select dj From t_kcsm@zczm_jc Where T.id=Id)) zmy,crksy,crkrq
From t_crkls@zczm_jc T Where  crkrq>=Date'$start' And crkrq < Date'$end'  And ywbmbh='$admin_username' And ywlx='SY'	";
					//echo $sql;exit;
					//if(empty($info)){
						//教材 损益明细<>
						
						$row=$ora->query($sql);
						//echo '教材-损益明细';
						//print_r($row);exit;
						//$info[jiaocaisunyimingxi]=$row;  //查找到数据就该写导出了
						
						//setcache("jiaocai_sunyimingxi_".$start.'_'.$end,$info);
					//}else{
					//	$row = $info[jiaocaisunyimingxi];
					//}
					if($daochu && $daochu=='daochu'){
						
						$fileName = '教材损益明细';
						$total = count($row);
						$title = array('KFBH','SM','CRKCS','DJ','ZMY','CRKSY','CRKRQ');
						//$this->articleAccessLog($sql,$fileName,$title,$total,$yewugongsi);
						if($_SESSION['roleid']==26 && $daochulx =='导出到服务器指定位置'){
							$this->yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username,$timetype);
						}elseif($_SESSION['roleid']==26 && $daochulx =='从浏览器中下载'){
							$this->articleAccessLog($sql,$fileName,$title,$total,$yewugongsi);
						}
						//$this->yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username);
					}
					
					
					
				}elseif($yewuleixing=='当月已结算-到货已结-到货'){
					//$info=getcache("jiaocai_daohuoyijiedh_".$start.'_'.$end);
					//$sql="Select * From t_Month_Dhjsb@zczm_jc Where jsrq>=Date'$start' And jsrq<Date'$end' And ywbmbh='$admin_username'";
					$sql="Select YWBMBH,GHDWH,MC,DHPCH,YSDJ,SSDH,ZDQH,ZDXH,SM,DJ,JXSSSL,MY,SYS,ZDPCH,JSRQ,JSLX,ZDPCH1,TSLSH,YSDBRQ,JCDRQ,KQBJ,JSGHDWH,YSGHDWH,FLOWID_DHMX,to_char(t_Month_Dhjsb.SHRQ, 'YYYY-MM-DD HH24:MI:SS') as SHRQ,to_char(t_Month_Dhjsb.YSRQ, 'YYYY-MM-DD HH24:MI:SS') as YSRQ From t_Month_Dhjsb Where jsrq>=Date'$start' And jsrq < Date'$end' And ywbmbh='$admin_username'";
					//if(empty($info)){
						//教材 当月已结算-到货已结-到货<>
						$row=$ora->query($sql);
						//echo '教材-当月已结算-发货已结-到货';
						//print_r($row);exit;
						//$info[jiaocaidaohuoyijiedh]=$row;  //查找到数据就该写导出了
						
						//setcache("jiaocai_daohuoyijiedh_".$start.'_'.$end,$info);
					//}else{
					//	$row = $info[jiaocaidaohuoyijiedh];
					//}
					if($daochu && $daochu=='daochu'){
						
						$fileName = '教材当月已结算-到货已结-到货';
						$total = count($row);
						$title = array('YWBMBH','GHDWH','MC','DHPCH','SHRQ','YSDJ','SSDH','ZDQH','ZDXH','SM','DJ','JXSSSL','MY','SYS','ZDPCH','JSRQ','JSLX','ZDPCH1','TSLSH','YSRQ','YSDBRQ','JCDRQ','KQBJ','JSGHDWH','YSGHDWH','FLOWID_DHMX');
						//$this->articleAccessLog($sql,$fileName,$title,$total,$yewugongsi);
						if($_SESSION['roleid']==26 && $daochulx =='导出到服务器指定位置'){
							$this->yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username,$timetype);
						}elseif($_SESSION['roleid']==26 && $daochulx =='从浏览器中下载'){
							$this->articleAccessLog($sql,$fileName,$title,$total,$yewugongsi);
						}
						//$this->yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username);
					}

				}elseif($yewuleixing=='当月已结算-到货已结-进退'){
					//$info=getcache("jiaocai_daohuoyijiedh_".$start.'_'.$end);
					$sql="Select YWBMBH,GHDWH,MC,ID,SM,DJ,ZMY,ZSY,BZ,YWPCH,ZDPCH, JSLX,ZDQH,ZDPCH1,TSLSH,NF,NFHRQ,NJSRQ,JSGHDWH,YSGHDWH,ZCS,to_char(t_Month_jtjsb.DBRQ, 'YYYY-MM-DD HH24:MI:SS') as DBRQ,to_char(t_Month_jtjsb.JSRQ, 'YYYY-MM-DD HH24:MI:SS') as JSRQ  From t_Month_jtjsb Where jsrq>=Date'$start' And jsrq < Date'$end' And ywbmbh='$admin_username'";
					//echo $sql;exit;
					//if(empty($info)){
						//教材 当月已结算-到货已结-进退<>
						$row=$ora->query($sql);
						//echo '教材-当月已结算-发货已结-进退';
						//print_r($row);exit;
						//$info[jiaocaidaohuoyijiedh]=$row;  //查找到数据就该写导出了
						
						//setcache("jiaocai_daohuoyijiedh_".$start.'_'.$end,$info);
					//}else{
					//	$row = $info[jiaocaidaohuoyijiedh];
					//}
					if($daochu && $daochu=='daochu'){
						
						$fileName = '教材当月已结算-到货已结-进退';
						$total = count($row);
						$title = array('YWBMBH','GHDWH','MC','DBRQ','ID','SM','DJ','ZMY','ZSY','BZ','YWPCH','ZDPCH','JSRQ','JSLX','ZDQH','ZDPCH1','TSLSH','NF','NFHRQ','NJSRQ','JSGHDWH','YSGHDWH','ZCS');
						//$this->articleAccessLog($sql,$fileName,$title,$total,$yewugongsi);
						if($_SESSION['roleid']==26 && $daochulx =='导出到服务器指定位置'){
							$this->yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username,$timetype);
						}elseif($_SESSION['roleid']==26 && $daochulx =='从浏览器中下载'){
							$this->articleAccessLog($sql,$fileName,$title,$total,$yewugongsi);
						}
						//$this->yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username);
					}

				}elseif($yewuleixing=='当月已结算-到货已结-退货'){
					//$info=getcache("jiaocai_daohuoyijieth_".$start.'_'.$end);
					//$sql="Select * From t_Month_fhjsb@zczm_jc Where jsrq>=Date'$start' And jsrq<Date'$end' And ywbmbh='$admin_username'";
					$sql="Select YWBMBH,GHDWH,MC,ID,SM,DJ,ZMY,ZSY,BZ,YWPCH,ZDPCH,JSLX,ZDQH,ZDXH,ZDPCH1,TSLSH,NF,NFHRQ,NJSRQ,JSGHDWH,YSGHDWH,ZCS,to_char(t_Month_jtjsb.DBRQ, 'YYYY-MM-DD HH24:MI:SS') as DBRQ,to_char(t_Month_jtjsb.JSRQ, 'YYYY-MM-DD HH24:MI:SS') as JSRQ From t_Month_jtjsb Where jsrq>=Date'$start' And jsrq < Date'$end' And ywbmbh='$admin_username'";
					//echo $sql;exit;
					//if(empty($info)){
						//教材 当月已结算-到货已结-退货<>
						$row=$ora->query($sql);
						//echo '教材-当月已结算-发货已结-退货';
						//print_r($row);exit;
						//$info[jiaocaidaohuoyijieth]=$row;  //查找到数据就该写导出了
						
						//setcache("jiaocai_daohuoyijieth_".$start.'_'.$end,$info);
					//}else{
					//	$row = $info[jiaocaidaohuoyijieth];
					//}
					if($daochu && $daochu=='daochu'){
						
						$fileName = '教材当月已结算-到货已结-退货';
						$total = count($row);
						$title = array('YWBMBH','GHDWH','MC','ID','DBRQ','SM','DJ','ZMY','ZSY','BZ','YWPCH','JSRQ','ZDPCH','JSLX','ZDQH','ZDXH','ZDPCH1','TSLSH','NF','NFHRQ','NJSRQ','JSGHDWH','YSGHDWH','ZCS');
						//$this->articleAccessLog($sql,$fileName,$title,$total,$yewugongsi);
						if($_SESSION['roleid']==26 && $daochulx =='导出到服务器指定位置'){
							$this->yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username,$timetype);
						}elseif($_SESSION['roleid']==26 && $daochulx =='从浏览器中下载'){
							$this->articleAccessLog($sql,$fileName,$title,$total,$yewugongsi);
						}
						//$this->yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username);
					}

				}elseif($yewuleixing=='当月已结算-到货已结-更正'){
					//$info=getcache("jiaocai_daohuoyijiegz_".$start.'_'.$end);
					$sql="Select YWBMBH,DH,MC,PFPCH,BZ,ZMY,ZSY,CBJ,TSLSH,SM,DJ,ZDQH,ZDXH,KFBH,JSLX,NF,NFHRQ,NJSRQ,JB,CWQRPC,YSDBRQ,ZCS,to_char(t_Month_fhjsb.DBRQ, 'YYYY-MM-DD HH24:MI:SS') as DBRQ,to_char(t_Month_fhjsb.PFRQ, 'YYYY-MM-DD HH24:MI:SS') as PFRQ ,to_char(t_Month_fhjsb.JSRQ, 'YYYY-MM-DD HH24:MI:SS') as JSRQ From t_Month_fhjsb Where jsrq>=Date'$start' And jsrq < Date'$end' And ywbmbh='$admin_username'";
					//echo $sql;exit;
					//if(empty($info)){
						//教材 当月已结算-到货已结-更正<>
						$row=$ora->query($sql);
						//echo '教材-当月已结算-发货已结-更正';
						//print_r($row);exit;
						//$info[jiaocaidaohuoyijiegz]=$row;  //查找到数据就该写导出了
						
						//setcache("jiaocai_daohuoyijiegz_".$start.'_'.$end,$info);
					//}else{
						//$row = $info[jiaocaidaohuoyijiegz];
					//}
					if($daochu && $daochu=='daochu'){
						//exit('无数据，无导出的表案例，不知道字段是啥。别忘了改导出表时候的字段');
						$fileName = '教材当月已结算-到货已结-更正';
						$total = count($row);
						$title = array('YWBMBH','DH','MC','PFPCH','BZ','DBRQ','PFRQ','ZMY','ZSY','CBJ','TSLSH','JSRQ','SM','DJ','ZDQH','ZDXH','KFBH','JSLX','NF','NFHRQ','NJSRQ','JB','CWQRPC','YSDBRQ','ZCS');
						//$this->articleAccessLog($sql,$fileName,$title,$total,$yewugongsi);
						if($_SESSION['roleid']==26 && $daochulx =='导出到服务器指定位置'){
							$this->yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username,$timetype);
						}elseif($_SESSION['roleid']==26 && $daochulx =='从浏览器中下载'){
							$this->articleAccessLog($sql,$fileName,$title,$total,$yewugongsi);
						}
						//$this->yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username);
					}

				}elseif($yewuleixing=='当月已结算-发货已结-到货'){
					$sql="Select cbny,ghdw,ghdwmc,YWBMBH,DH,MC,PFPCH,BZ,ZMY,ZSY,CBJ,TSLSH,SM,DJ,ZDQH,ZDXH,KFBH,JSLX,NF,NFHRQ,NJSRQ,JB,CWQRPC,YSDBRQ,ZCS,to_char(t_Month_fhjsb.DBRQ, 'YYYY-MM-DD HH24:MI:SS') as DBRQ,to_char(t_Month_fhjsb.PFRQ, 'YYYY-MM-DD HH24:MI:SS') as PFRQ ,to_char(t_Month_fhjsb.JSRQ, 'YYYY-MM-DD HH24:MI:SS') as JSRQ  From t_Month_fhjsb Where jsrq>=Date'$start' And jsrq < Date'$end' And ywbmbh='$admin_username'";
						//echo $sql;exit;
						$row=$ora->query($sql);
						//print_r($row);exit;
					if($daochu && $daochu=='daochu'){
						$fileName = '教材当月已结算-发货已结-到货';
						$total = count($row);
						$title = array('CBNY','GHDW','GHDWMC','YWBMBH','DH','MC','PFPCH','BZ','DBRQ','PFRQ','ZMY','ZSY','CBJ','TSLSH','JSRQ','SM','DJ','ZDQH','ZDXH','KFBH','JSLX','NF','NFHRQ','NJSRQ','JB','CWQRPC','YSDBRQ','ZCS');
						//$this->articleAccessLog($sql,$fileName,$title,$total,$yewugongsi);
						if($_SESSION['roleid']==26 && $daochulx =='导出到服务器指定位置'){
							$this->yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username,$timetype);
						}elseif($_SESSION['roleid']==26 && $daochulx =='从浏览器中下载'){
							$this->articleAccessLog($sql,$fileName,$title,$total,$yewugongsi);
						}
						//$this->yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username);
					}

					
				}elseif($yewuleixing=='当月已结算-发货已结-退货'){
					//$info=getcache("jiaocai_fahuoyijieth_".$start.'_'.$end);
					$sql="Select * From t_Month_xtjsb Where jsrq>=Date'$start' And jsrq < Date'$end' And ywbmbh='$admin_username'";
					//echo $sql;exit;
					//if(empty($info)){
						//教材 当月已结算-发货已结-退货<>
						$row=$ora->query($sql);
						//echo '教材-当月已结算-发货已结-退货';print_r($row);//exit;
						//$info[jiaocaifahuoyijiedh]=$row;  //查找到数据就该写导出了
						
						//setcache("jiaocai_fahuoyijiedh_".$start.'_'.$end,$info);
					//}else{
					//	$row = $info[jiaocaifahuoyijiedh];
					//}
					if($daochu && $daochu=='daochu'){
						$fileName = '教材当月已结算-发货已结-退货';
						$total = count($row);
						$title = array('CBNY','GHDW','GHDWMC','YWBMBH','KFBH','DH','DM','PCH','THRQ','ZMY','ZSY','ZCB','TSLSH','JSRQ','JSLX','ZDQH','NF','NFHRQ','NJSRQ','JB','CWQRPC','ZCS','DJ','ID','BB','SM','ISBN','CBNY');
						//$this->articleAccessLog($sql,$fileName,$title,$total,$yewugongsi);
						if($_SESSION['roleid']==26 && $daochulx =='导出到服务器指定位置'){
							$this->yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username,$timetype);
						}elseif($_SESSION['roleid']==26 && $daochulx =='从浏览器中下载'){
							$this->articleAccessLog($sql,$fileName,$title,$total,$yewugongsi);
						}
						//$this->yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username);
					}
				}elseif($yewuleixing=='当月已结算-发货已结-更正'){
					//$info=getcache("jiaocai_fahuoyijiegz_".$start.'_'.$end);
					$sql="Select * From t_Month_Fhgzjsb Where jsrq>=Date'$start' And jsrq < Date'$end' And ywbmbh='$admin_username'";
					//echo $sql;exit;
					//if(empty($info)){
						//教材 当月已结算-发货已结-更正<>
						$row=$ora->query($sql);
						//echo '教材-当月已结算-发货已结-更正';
						//print_r($row);exit;
						//$info[jiaocaifahuoyijiegz]=$row;  //查找到数据就该写导出了
						
						//setcache("jiaocai_fahuoyijiegz_".$start.'_'.$end,$info);
					//}else{
					//	$row = $info[jiaocaifahuoyijiegz];
					//}
					if($daochu && $daochu=='daochu'){
						$fileName = '教材当月已结算-发货已结-更正';
						$total = count($row);
						$title = array('CBNY','GHDW','GHDWMC','YWBMBH','KFBH','DH','DM','GZPCH','GZRQ','ZMY','ZSY','ZCB','TSLSH','JSRQ','BZ','JSLX','ZDQH','NF','NFHRQ','NJSRQ','JB','ZCS');
						//$this->articleAccessLog($sql,$fileName,$title,$total,$yewugongsi);
						if($_SESSION['roleid']==26 && $daochulx =='导出到服务器指定位置'){
							$this->yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username,$timetype);
						}elseif($_SESSION['roleid']==26 && $daochulx =='从浏览器中下载'){
							$this->articleAccessLog($sql,$fileName,$title,$total,$yewugongsi);
						}
						//$this->yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username);
					}
				}elseif($yewuleixing=='应付款-到货未结明细'){ //dhweijie 到货未结
					//$info=getcache("jiaocai_dhweijiemingxi_".$start.'_'.$end);
					$sql="Select YWBMBH,GHDWH,MC,DHPCH,YSDJ,SSDH,ZDQH,ZDXH,SM,DJ,JXSSSL,MY,SYS,ZDPCH,JSRQ,JSLX,ZDPCH1,TSLSH,YSDBRQ,JCDRQ,KQBJ,JSGHDWH,YSGHDWH,FLOWID_DHMX,to_char(t_Month_Dhjsb.SHRQ, 'YYYY-MM-DD HH24:MI:SS') as SHRQ,to_char(t_Month_Dhjsb.YSRQ, 'YYYY-MM-DD HH24:MI:SS') as YSRQ  From t_Month_Dhjsb Where ywbmbh='$admin_username' And jsrq Is Null";
					//echo $sql;exit;
					//if(empty($info)){
						//教材 应付款-到货未结明细<>
						$row=$ora->query($sql);
						//echo '教材-应付款-到货未结明细';
						//print_r($row);exit;
						//$info[jiaocaidhweijiemingxi]=$row;  //查找到数据就该写导出了
						
						//setcache("jiaocai_dhweijiemingxi_".$start.'_'.$end,$info);
					//}else{
					//	$row = $info[jiaocaidhweijiemingxi];
					//}

					if($daochu && $daochu=='daochu'){
						
						$fileName = '教材应付款-到货未结明细';
						$total = count($row);
						$title = array('YWBMBH','GHDWH','MC','DHPCH','SHRQ','YSDJ','SSDH','ZDQH','ZDXH','SM','DJ','JXSSSL','MY','SYS','ZDPCH','JSRQ','JSLX','ZDPCH1','TSLSH','YSRQ','YSDBRQ','JCDRQ','KQBJ','JSGHDWH','YSGHDWH','FLOWID_DHMX');
						//$this->articleAccessLog($sql,$fileName,$title,$total,$yewugongsi);
						if($_SESSION['roleid']==26 && $daochulx =='导出到服务器指定位置'){
							$this->yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username,$timetype);
						}elseif($_SESSION['roleid']==26 && $daochulx =='从浏览器中下载'){
							$this->articleAccessLog($sql,$fileName,$title,$total,$yewugongsi);
						}
						//$this->yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username);
					}

					
				}elseif($yewuleixing=='应付款-进退未结明细'){ //jtweijie 进退未结
					//$info=getcache("jiaocai_jtweijiemingxi_".$start.'_'.$end);
					$sql="Select YWBMBH,GHDWH,MC,ID,SM,DJ,ZMY,ZSY,BZ,YWPCH,ZDPCH,JSRQ,JSLX,ZDQH,ZDXH,ZDPCH1,TSLSH,NF,NFHRQ,NJSRQ,JSGHDWH,YSGHDWH,ZCS,to_char(t_Month_jtjsb.DBRQ, 'YYYY-MM-DD HH24:MI:SS') as DBRQ From t_Month_jtjsb Where ywbmbh='$admin_username' And jsrq Is Null";

					//if(empty($info)){
						//教材 应付款-进退未结明细<>
						$row=$ora->query($sql);
						//echo '教材-应付款-进退未结明细';
						//print_r($row);exit;
						//$info[jiaocaijtweijiemingxi]=$row;  //查找到数据就该写导出了
						
						//setcache("jiaocai_jtweijiemingxi_".$start.'_'.$end,$info);
					//}else{
					//	$row = $info[jiaocaijtweijiemingxi];
					//}
					if($daochu && $daochu=='daochu'){
						$fileName = '教材应付款-进退未结明细';
						$total = count($row);
						$title = array('YWBMBH','GHDWH','MC','DBRQ','ID','SM','DJ','ZMY','ZSY','BZ','YWPCH','ZDPCH','JSRQ','JSLX','ZDQH','ZDXH','ZDPCH1','TSLSH','NF','NFHRQ','NJSRQ','JSGHDWH','YSGHDWH','ZCS');
						//$this->articleAccessLog($sql,$fileName,$title,$total,$yewugongsi);
						if($_SESSION['roleid']==26 && $daochulx =='导出到服务器指定位置'){
							$this->yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username,$timetype);
						}elseif($_SESSION['roleid']==26 && $daochulx =='从浏览器中下载'){
							$this->articleAccessLog($sql,$fileName,$title,$total,$yewugongsi);
						}
						//$this->yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username);
					}
					
					
				}elseif($yewuleixing=='应付款-出版社让利未结'){ 
					$sql="Select YWBMBH,GHDWH,MC,DHPCH,YSDJ,SSDH,ZDQH,ZDXH,SM,DJ,JXSSSL,MY,SYS,ZDPCH,JSRQ,JSLX,JSGHDWH,YSGHDWH,TSLSH,to_char(t_month_dhjsb_mf.SHRQ, 'YYYY-MM-DD HH24:MI:SS') as SHRQ From t_month_dhjsb_mf  Where jsrq Is Null And ywbmbh='$admin_username'";
						$row=$ora->query($sql);
						//print_r($row);exit;
					if($daochu && $daochu=='daochu'){
						$fileName = '教材应付款-出版社让利未结';
						$total = count($row);
						//echo $total;exit;
						$title = array('YWBMBH','GHDWH','MC','DHPCH','SHRQ','YSDJ','SSDH','ZDQH','ZDXH','SM','BZ','DJ','JXSSSL','MY','SYS','ZDPCH','JSRQ','JSLX','JSGHDWH','YSGHDWH','TSLSH');
						//$this->articleAccessLog($sql,$fileName,$title,$total,$yewugongsi);
						if($_SESSION['roleid']==26 && $daochulx =='导出到服务器指定位置'){
							$this->yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username,$timetype);
						}elseif($_SESSION['roleid']==26 && $daochulx =='从浏览器中下载'){
							$this->articleAccessLog($sql,$fileName,$title,$total,$yewugongsi);
						}
						//$this->yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username);
					}
					
					
				}elseif($yewuleixing=='应收款-发货未结明细'){
					//$info=getcache("jiaocai_fhweijiemingxi_".$start.'_'.$end);
					$sql="Select cbny,ghdw,ghdwmc,YWBMBH,DH,MC,PFPCH,BZ,ZMY,ZSY,CBJ,TSLSH,JSRQ,SM,DJ,ZDQH,ZDXH,KFBH,JSLX,NF,NFHRQ,NJSRQ,JB,CWQRPC,YSDBRQ,ZCS,to_char(t_Month_fhjsb.DBRQ, 'YYYY-MM-DD HH24:MI:SS') as DBRQ,to_char(t_Month_fhjsb.PFRQ, 'YYYY-MM-DD HH24:MI:SS') as PFRQ From t_Month_fhjsb Where ywbmbh='$admin_username' And jsrq Is Null";
					//$sql="Select * From t_Month_fhjsb Where ywbmbh='$admin_username' And jsrq Is Null";
					//echo $sql;exit;
					//if(empty($info)){
						//教材 应收款-发货未结明细<>
						$row=$ora->query($sql);
						//echo '教材-应收款-发货未结明细';
						//print_r($row);exit;
						//$info[jiaocaifhweijiemingxi]=$row;  //查找到数据就该写导出了
						
						//setcache("jiaocai_fhweijiemingxi_".$start.'_'.$end,$info);
					//}else{
					//	$row = $info[jiaocaifhweijiemingxi];
					//}
					if($daochu && $daochu=='daochu'){
						
						$fileName = '教材应收款-发货未结明细';
						$total = count($row);
						$title = array('CBNY','GHDW','GHDWMC','YWBMBH','DH','MC','PFPCH','BZ','DBRQ','PFRQ','ZMY','ZSY','CBJ','TSLSH','JSRQ','SM','DJ','ZDQH','ZDXH','KFBH','JSLX','NF','NFHRQ','NJSRQ','JB','CWQRPC','YSDBRQ','ZCS');
						//$this->articleAccessLog($sql,$fileName,$title,$total,$yewugongsi);
						if($_SESSION['roleid']==26 && $daochulx =='导出到服务器指定位置'){
							$this->yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username,$timetype);
						}elseif($_SESSION['roleid']==26 && $daochulx =='从浏览器中下载'){
							$this->articleAccessLog($sql,$fileName,$title,$total,$yewugongsi);
						}
						//$this->yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username);
					}
					
				}elseif($yewuleixing=='应收款-销退未结明细'){
					//$info=getcache("jiaocai_xtweijiemingxi_".$start.'_'.$end);
					$sql="Select cbny,ghdw,ghdwmc,YWBMBH,KFBH,DH,DM,PCH,ZMY,ZSY,ZCB,TSLSH,JSRQ,JSLX,ZDQH,NF,NFHRQ,NJSRQ,JB,CWQRPC,ZCS,DJ,to_char(t_Month_Xtjsb.THRQ, 'YYYY-MM-DD HH24:MI:SS') as THRQ From t_Month_Xtjsb Where ywbmbh='$admin_username' And jsrq Is Null";
					//echo $sql;exit;
					//if(empty($info)){
						//教材 应收款-销退未结明细<>
						$row=$ora->query($sql);
						//echo '教材-应收款-销退未结明细';
						//print_r($row);exit;
						//$info[jiaocaixtweijiemingxi]=$row;  //查找到数据就该写导出了
						
						//setcache("jiaocai_xtweijiemingxi_".$start.'_'.$end,$info);
					//}else{
					//	$row = $info[jiaocaixtweijiemingxi];
					//}
					if($daochu && $daochu=='daochu'){
						
						$fileName = '教材应收款-销退未结明细';
						$total = count($row);
						$title = array('CBNY','GHDW','GHDWMC','YWBMBH','KFBH','DH','DM','PCH','THRQ','ZMY','ZSY','ZCB','TSLSH','JSRQ','JSLX','ZDQH','NF','NFHRQ','NJSRQ','JB','CWQRPC','ZCS','DJ');
						//$this->articleAccessLog($sql,$fileName,$title,$total,$yewugongsi);
						if($_SESSION['roleid']==26 && $daochulx =='导出到服务器指定位置'){
							$this->yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username,$timetype);
						}elseif($_SESSION['roleid']==26 && $daochulx =='从浏览器中下载'){
							$this->articleAccessLog($sql,$fileName,$title,$total,$yewugongsi);
						}
						//$this->yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username);
					}
					
				}elseif($yewuleixing=='应收款-更正未结明细'){
					//$info=getcache("jiaocai_gzweijiemingxi_".$start.'_'.$end);
					$sql="Select YWBMBH,KFBH,DH,DM,GZPCH,ZMY,ZSY,ZCB,TSLSH,JSRQ,BZ,JSLX,ZDQH,NF,NFHRQ,NJSRQ,JB,ZCS,to_char(t_Month_fhgzjsb.GZRQ, 'YYYY-MM-DD HH24:MI:SS') as GZRQ From t_Month_fhgzjsb Where ywbmbh='$admin_username' And jsrq Is Null";
					//echo $sql;exit;
					//if(empty($info)){
						//教材 应收款-更正未结明细<>
						$row=$ora->query($sql);
						//echo '教材-应收款-更正未结明细';
						//print_r($row);exit;
						//$info[jiaocaigzweijiemingxi]=$row;  //查找到数据就该写导出了
						
						//setcache("jiaocai_gzweijiemingxi_".$start.'_'.$end,$info);
					//}else{
					//	$row = $info[jiaocaigzweijiemingxi];
					//}
					if($daochu && $daochu=='daochu'){
						
						$fileName = '教材应收款-更正未结明细';
						$total = count($row);
						$title = array('YWBMBH','KFBH','DH','DM','GZPCH','GZRQ','ZMY','ZSY','ZCB','TSLSH','JSRQ','BZ','JSLX','ZDQH','NF','NFHRQ','NJSRQ','JB','ZCS');
						//$this->articleAccessLog($sql,$fileName,$title,$total,$yewugongsi);
						if($_SESSION['roleid']==26 && $daochulx =='导出到服务器指定位置'){
							$this->yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username,$timetype);
						}elseif($_SESSION['roleid']==26 && $daochulx =='从浏览器中下载'){
							$this->articleAccessLog($sql,$fileName,$title,$total,$yewugongsi);
						}
						//$this->yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username);
					}
					
					
				}
				
				
				
				
			}elseif($yewugongsi==3){  //查询连锁公司
			
				///连锁公司数据库
				if($yewugongsi==3){
					$ip='172.30.153.63/xhsddb';
					$port='1521';
					$user= 'dbsl';
					$pass= 'dbsl';
					$charset='utf8';
					pc_base::load_app_class('oracle_admin','admin',0);//数据库
					$ora=new oracle_admin($user,$pass,$ip,$charset);
				}
				$yewugongsiname='连锁公司';
				if($yewuleixing=='差异-更正差异'){
					$sql="select 0 As zmy,Sum(cbj) As zsy,ywbmbh from t_xsls_ghdw_gz Where xsrq >= Date '$start' and xslx='2' And xsrq < Date '$end' AND YWBMBH = '$admin_username'
 Group By ywbmbh";

						$row=$ora->query($sql);
						//print_r($row);exit;
					if($daochu && $daochu=='daochu'){
						$fileName = '差异-更正差异';
						$total = count($row);
						$title = array('ZMY','ZSY','YWBMBH');
						if($_SESSION['roleid']==26 && $daochulx =='导出到服务器指定位置'){
							$this->yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username,$timetype);
						}elseif($_SESSION['roleid']==26 && $daochulx =='从浏览器中下载'){
							$this->articleAccessLog($sql,$fileName,$title,$total,$yewugongsi);
						}
					}

				}elseif($yewuleixing=='差异-到货'){
					$sql="Select 'dhyh' As lx,0 As zmy,Sum(zsy) As zsy,ywbmbh From t_Jjyhdkhz Where djrq>=Date'$start' And djrq< Date'$end' AND YWBMBH = '$admin_username' Group By ywbmbh
Union All Select 'dhbl' As lx,Sum(zmy) As zmy,Sum(zsy) As zsy,ywbmbh From t_bldhdj Where dbrq>=Date'$start' And dbrq< Date'$end' AND YWBMBH = '$admin_username' Group By ywbmbh";

						
						$row=$ora->query($sql);
						//print_r($row);exit;
					if($daochu && $daochu=='daochu'){
						
						$fileName = '差异-到货';
						$total = count($row);
						$title = array('LX','ZMY','ZSY','YWBMBH');
						if($_SESSION['roleid']==26 && $daochulx =='导出到服务器指定位置'){
							$this->yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username,$timetype);
						}elseif($_SESSION['roleid']==26 && $daochulx =='从浏览器中下载'){
							$this->articleAccessLog($sql,$fileName,$title,$total,$yewugongsi);
						}
					}

				}elseif($yewuleixing=='差异-发货'){
					$sql="Select 'fhhz1' As lx,Sum(zmy),Sum(cbj),ywbmbh From t_fhhz1 a Where  Not Exists (Select 1 From t_fhhz Where a.pfpch = pfpch) And dbrq >= Date'$start' And dbrq < Date'$end' AND YWBMBH = '$admin_username'
 Group By ywbmbh
Union All
Select 'fhhz2' As lx,Sum(zmy),Sum(cbj),ywbmbh From t_fhhz2 a Where  Not Exists (Select 1 From t_fhhz Where a.pfpch = pfpch) And dbrq >= Date'$start' And dbrq < Date'$end' AND YWBMBH = '$admin_username'
 Group By ywbmbh
Union All
Select  'khthhz1' As lx,Sum(zmy),Sum(cbj),ywbmbh From t_khthhz1 a Where  Not Exists (Select * From T_KHTHHZ Where THPCH = a.THPCH)And djlx <>'YH'  And thrq >= Date'$start' And thrq < Date'$end' AND YWBMBH = '$admin_username'
 Group By ywbmbh
Union All
Select  'khthhz2' As lx,Sum(zmy),Sum(cbj),ywbmbh From t_khthhz2 a Where  Not Exists (Select * From T_KHTHHZ Where THPCH = a.THPCH) And thrq >= Date'$start' And thrq < Date'$end' AND YWBMBH = '$admin_username'
 Group By ywbmbh";

						$row=$ora->query($sql);
						//print_r($row);exit;
					if($daochu && $daochu=='daochu'){
						
						$fileName = '差异-发货';
						$total = count($row);
						$title = array('LX','SUM(ZMY)','SUM(CBJ)','YWBMBH');
						if($_SESSION['roleid']==26 && $daochulx =='导出到服务器指定位置'){
							$this->yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username,$timetype);
						}elseif($_SESSION['roleid']==26 && $daochulx =='从浏览器中下载'){
							$this->articleAccessLog($sql,$fileName,$title,$total,$yewugongsi);
						}
					}
					
				}elseif($yewuleixing=='库存明细'){  
					set_time_limit(0);
					ini_set('memory_limit', '-1');
					ini_set("max_execution_time", "0");
					$start = kc_month(); //日期格式必须为 201808
					$sql="Select ywbmbh 业务部门编号, Id,
		                (Select isbn From t_kcsm Where Id = t.id) As isbn,
										(Select sm From t_kcsm Where Id = t.id) As 书名,
										(Select bb From t_kcsm Where id = t.id) As 版本,
										(Select (Select mc From t_bb Where bh = t_kcsm.bb)
										From t_kcsm Where id = t.id) As 版本名称, cs 册数, my 码洋, sy 实样,
										(sy/(1+(Select sl1 From t_lb Where bh = (Select lb From T_KCSM Where Id = T.ID))*0.01)) As 不含税实样,
										(Select dj From t_kcsm Where t.id = Id) As 定价,
										(Select ghdwh
												From t_kcsm_ywbm
											 Where Id = t.id
												 And ywbmbh = '$admin_username') As 供货单位,
										(Select mc
												From t_ghdw
											 Where bh = (Select ghdwh
																		 From t_kcsm_ywbm
																		Where Id = t.id
																			And ywbmbh = '$admin_username')) As 供货单位名称,
										(Select cbny From t_kcsm Where id = t.id) As 出版年月,
										(Select ysny From t_kcsm Where Id = t.id) As 原始年月,
										(Select bc From t_kcsm Where Id = t.id) As 版次,
										(Select MC From T_FL Where BH = (Select FL From T_KCSM Where Id = T.ID)) As 分类,
										(Select FL From T_KCSM Where Id = T.ID) As 分类编号,
										(Select LB From T_KCSM Where Id = T.ID) As 类别编号,
										(Select MC From T_lb Where BH = (Select lb From T_KCSM Where Id = T.ID)) As 类别
							 From (Select ywbmbh, Id,
														 Sum(qmcs) As cs,
														 Sum(qmmy) As my,
														 Sum(qmsy) As Sy
												From t_Tscw_Pzjxc_month
											 Where ywbmbh = '$admin_username'
												 And tjny = '$start'
											 Group By Id, ywbmbh) t";

							//echo $sql;exit;

						
						$row=$ora->query($sql);
					if($daochu && $daochu=='daochu'){
						
						$fileName = '库存明细';
						$total = count($row);
						//echo $total;exit;
						$title = array('业务部门编号','ID','ISBN','书名','版本','版本名称','册数','码洋','实洋','不含税实洋','定价','供货单位','供货单位名称','出版年月','印刷年月','版次','分类','分类编号','类别编号','类别');
						if($_SESSION['roleid']==26 && $daochulx =='导出到服务器指定位置'){
							$this->yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username,$timetype);
						}elseif($_SESSION['roleid']==26 && $daochulx =='从浏览器中下载'){
							$this->articleAccessLog($sql,$fileName,$title,$total,$yewugongsi);
						}
					}
					

				}elseif($yewuleixing=='应付款-采购未结-明细'){
				
				if($_SESSION['roleid']==26){//过程
						$sql_gc = "Delete From T_YFZK_SJDJ";
						$res = $ora->query($sql_gc);
						$sql_gc = "Insert Into T_YFZK_SJDJ
        (dbrq, ysdj, ysrq, dm, zpz, zcs, zmy, zsy, ywbmmc, ywbmbh, bz, djlx, sl, czybh,
         sl1, lb)
        Select dbrq, ysdj, ysrq,
               (Select '(' || bh || ')' || Trim(mc) From t_ghdw Where bh = a.ghdwh) As dm,
               zpz, zcs, zmy, zsy,
               (Select Trim(dm) From t_dm Where dh = a.ywbmbh) As ywbmmc, ywbmbh,
               a.remark As bz, '新进货' As djlx, a.sl, 1139,
               (Select sl1
                   From t_lb
                  Where bh =
                        (Select lb
                           From t_kcsm
                          Where Id = (Select Max(Id) From t_dhls_jx Where dhpch = a.Dhpch))) As sl1,
               (Select BH
                   From t_lb
                  Where bh =
                        (Select lb
                           From t_kcsm
                          Where Id = (Select Max(Id) From t_dhls_jx Where dhpch = a.dhpch))) As BH
          From t_dhdj_cf a
         Where a.ywcbz = '0'
           And a.ywbmbh Like '$admin_username'
           And a.dbrq < Date '$end'
           And Not Exists (Select 1 From T_TMP_FPLS Where TSLSH = a.tslsh)";
		 //echo $sql_gc;exit;
						$res = $ora->query($sql_gc);
						$sql_gc = "Insert Into T_YFZK_SJDJ
        (dbrq, ysdj, ysrq, dm, zpz, zcs, zmy, zsy, ywbmmc, ywbmbh, bz, djlx, sl, czybh,
         sl1, lb)
        Select a.dbrq, a.ysdj, a.ysrq,
               (Select '(' || bh || ')' || Trim(mc) From t_ghdw Where bh = a.ghdwh) As dm,
               a.zpz, a.zcs, a.zmy, a.zsy,
               (Select Trim(dm) From t_dm Where dh = a.ywbmbh) As ywbmmc, A.ywbmbh,
               a.remark As bz, '新进货' As djlx, a.sl, 1139,
               (Select sl1
                   From t_lb
                  Where bh =
                        (Select lb
                           From t_kcsm
                          Where Id = (Select Max(Id) From t_dhls_jx Where dhpch = a.Dhpch))) As sl1,
               (Select BH
                   From t_lb
                  Where bh =
                        (Select lb
                           From t_kcsm
                          Where Id = (Select Max(Id) From t_dhls_jx Where dhpch = a.dhpch))) As BH
          From t_dhdj_cf a, t_tmp_fpls b
         Where a.tslsh = b.tslsh
           And a.ywcbz = '0'
           And a.ywbmbh Like '$admin_username'
           And a.dbrq < Date '$end'  
           And (b.fpqrrq >= Date '$end' Or b.fpqrrq Is Null)";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);
						$sql_gc = "Insert Into T_YFZK_SJDJ
        (dbrq, ysdj, ysrq, dm, zpz, zcs, zmy, zsy, ywbmmc, ywbmbh, bz, djlx, sl, czybh,
         sl1, lb)
        Select a.dbrq, a.ysdj, a.ysrq,
               (Select '(' || bh || ')' || Trim(mc) From t_ghdw Where bh = a.ghdwh) As dm,
               a.zpz, a.zcs, a.zmy, a.zsy,
               (Select Trim(dm) From t_dm Where dh = a.ywbmbh) As ywbmmc, A.ywbmbh,
               a.remark As bz, '新进货' As djlx, a.sl, 1139,
               (Select sl1
                   From t_lb
                  Where bh =
                        (Select lb
                           From t_kcsm
                          Where Id = (Select Max(Id) From t_dhls_jx Where dhpch = a.Dhpch))) As sl1,
               (Select BH
                   From t_lb
                  Where bh =
                        (Select lb
                           From t_kcsm
                          Where Id = (Select Max(Id) From t_dhls_jx Where dhpch = a.dhpch))) As BH
          From t_dhdj_cf a, t_fpls b
         Where a.Tslsh = b.Jzpch
           And a.ywcbz = '1'
           And b.fpqrrq >= Date '$end'
           And a.ywbmbh Like '$admin_username'
           And a.dbrq < Date '$end'";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);
						$sql_gc = "Insert Into T_YFZK_SJDJ
        (dbrq, ysdj, ysrq, dm, zpz, zcs, zmy, zsy, ywbmmc, ywbmbh, bz, djlx, sl, czybh,
         sl1, lb)
        Select dbrq, ysdj, ysrq,
               (Select '(' || ghdwh || ')' || Trim(mc) From t_ghdw Where bh = a.ghdwh) As dm,
               zpz, zcs, zmy, zsy,
               (Select Trim(dm) From t_dm Where dh = a.ywbmbh) As ywbmmc, A.ywbmbh,
               remark As bz,
               decode(djlx,
                       '0',
                       '旧进货',
                       '1',
                       '旧退货',
                       '2',
                       '旧进更',
                       '3',
                       '旧退更',
                       '调整单') As djlx, a.sl, 1139, a.sl, a.lb
          From t_bldhdj a
         Where ywcbz = '0'
           And a.ywbmbh Like '$admin_username'
           And a.dbrq < Date '$end'
           And Not Exists (Select 1 From t_tmp_fpls Where tslsh = a.tslsh)";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);
						$sql_gc = "Insert Into T_YFZK_SJDJ
        (dbrq, ysdj, ysrq, dm, zpz, zcs, zmy, zsy, ywbmmc, ywbmbh, bz, djlx, sl, czybh,
         sl1, lb)
        Select dbrq, ysdj, ysrq,
               (Select '(' || bh || ')' || Trim(mc) From t_ghdw Where bh = a.ghdwh) As dm,
               zpz, zcs, zmy, zsy,
               (Select Trim(dm) From t_dm Where dh = a.ywbmbh) As ywbmmc, A.ywbmbh,
               a.remark As bz,
               decode(djlx,
                       '0',
                       '旧进货',
                       '1',
                       '旧退货',
                       '2',
                       '旧进更',
                       '3',
                       '旧退更',
                       '调整单') As djlx, a.sl, 1139, a.sl, a.lb
          From t_bldhdj a, t_tmp_fpls b
         Where a.tslsh = b.tslsh
           And a.ywcbz = '0'
           And a.ywbmbh Like '$admin_username'
           And a.dbrq < Date '$end'
           And (b.fpqrrq >= Date '$end' Or b.fpqrrq Is Null)";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);
						$sql_gc = "Insert Into T_YFZK_SJDJ
        (dbrq, ysdj, ysrq, dm, zpz, zcs, zmy, zsy, ywbmmc, ywbmbh, bz, djlx, sl, czybh,
         sl1, lb)
        Select a.dbrq, a.ysdj, a.ysrq,
               (Select '(' || bh || ')' || Trim(mc) From t_ghdw Where bh = a.ghdwh) As dm,
               zpz, zcs, zmy, zsy,
               (Select Trim(dm) From t_dm Where dh = a.ywbmbh) As ywbmmc, A.ywbmbh,
               a.remark As bz,
               decode(a.djlx,
                       '0',
                       '旧进货',
                       '1',
                       '旧退货',
                       '2',
                       '旧进更',
                       '3',
                       '旧退更',
                       '调整单') As djlx, a.sl, 1139, a.sl, a.lb
          From t_bldhdj a, t_fpls b
         Where a.tslsh = b.jzpch
           And a.ywcbz = '1'
           And b.fpqrrq >= Date '$end'
           And a.ywbmbh Like '$admin_username'
           And a.dbrq < Date '$end'";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);
						$sql_gc = "Insert Into T_YFZK_SJDJ
        (dbrq, ysdj, ysrq, dm, zpz, zcs, zmy, zsy, ywbmmc, ywbmbh, bz, djlx, sl, czybh,
         sl1, lb)
        Select thrq, ywpch, thrq,
               (Select '(' || bh || ')' || Trim(mc) From t_ghdw Where bh = a.hybh) As dm,
               zpz, -zcs, -zmy, -zsy,
               (Select Trim(dm) From t_dm Where dh = a.ywbmbh) As ywbmmc, A.ywbmbh,
               bz As bz, '新退货' As djlx, a.sl, 1139,
               (Select sl1
                   From t_lb
                  Where bh =
                        (Select lb
                           From t_kcsm
                          Where Id = (Select Max(Id) From t_Hythmx Where ywpch = a.ywpch))) As sl1,
               (Select BH
                   From t_lb
                  Where bh =
                        (Select lb
                           From t_kcsm
                          Where Id = (Select Max(Id) From t_Hythmx Where ywpch = a.ywpch))) As BH
          From t_hythhz a
         Where ywcbz = '0'
           And a.ywbmbh Like '$admin_username'
           And a.thrq < Date '$end'
           And Not Exists (Select 1 From t_tmp_fpls Where tslsh = a.tslsh)";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);
						
						$sql_gc = "Insert Into T_YFZK_SJDJ
        (dbrq, ysdj, ysrq, dm, zpz, zcs, zmy, zsy, ywbmmc, ywbmbh, bz, djlx, sl, czybh,
         sl1, lb)
        Select thrq, ywpch, thrq,
               (Select '(' || bh || ')' || Trim(mc) From t_ghdw Where bh = a.hybh) As dm,
               zpz, -zcs, -zmy, -zsy,
               (Select Trim(dm) From t_dm Where dh = a.ywbmbh) As ywbmmc, A.ywbmbh,
               a.bz As bz, '新退货' As djlx, a.sl, 1139,
               (Select sl1
                   From t_lb
                  Where bh =
                        (Select lb
                           From t_kcsm
                          Where Id = (Select Max(Id) From t_Hythmx Where ywpch = a.ywpch))) As sl1,
               (Select BH
                   From t_lb
                  Where bh =
                        (Select lb
                           From t_kcsm
                          Where Id = (Select Max(Id) From t_Hythmx Where ywpch = a.ywpch))) As BH
          From t_hythhz a, t_tmp_fpls b
         Where a.tslsh = b.tslsh
           And a.ywcbz = '0'
           And a.ywbmbh Like '$admin_username'
           And a.thrq < Date '$end'
           And (b.fpqrrq >= Date '$end' Or b.fpqrrq Is Null)";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);

						$sql_gc = "Insert Into T_YFZK_SJDJ
        (dbrq, ysdj, ysrq, dm, zpz, zcs, zmy, zsy, ywbmmc, ywbmbh, bz, djlx, sl, czybh,
         sl1, lb)
        Select a.thrq, a.ywpch, a.thrq,
               (Select '(' || bh || ')' || Trim(mc) From t_ghdw Where bh = a.hybh) As dm,
               a.zpz, -a.zcs, -a.zmy, -a.zsy,
               (Select Trim(dm) From t_dm Where dh = a.ywbmbh) As ywbmmc, A.ywbmbh,
               a.bz As bz, '新退货' As djlx, a.sl, 1139,
               (Select sl1
                   From t_lb
                  Where bh =
                        (Select lb
                           From t_kcsm
                          Where Id = (Select Max(Id) From t_Hythmx Where ywpch = a.ywpch))) As sl1,
               (Select BH
                   From t_lb
                  Where bh =
                        (Select lb
                           From t_kcsm
                          Where Id = (Select Max(Id) From t_Hythmx Where ywpch = a.ywpch))) As BH
          From t_hythhz a, t_fpls b
         Where a.tslsh = b.jzpch
           And a.ywcbz = '1'
           And b.fpqrrq >= Date '$end'
           And a.ywbmbh Like '$admin_username'
           And a.thrq < Date '$end'";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);
						
						$sql_gc = "Insert Into T_YFZK_SJDJ
        (dbrq, ysdj, ysrq, dm, zpz, zcs, zmy, zsy, ywbmmc, ywbmbh, bz, djlx, sl, czybh,
         sl1, lb)
        Select djrq, djbh, djrq,
               (Select '(' || bh || ')' || Trim(mc) From t_ghdw Where bh = a.ghdwh) As dm,
               (Select Count(*) From t_jjyhdkmx Where djbh = a.djbh) As zpz, 0 As zcs,
               0 As zmy,
               -zsy, (Select Trim(dm) From t_dm Where dh = a.ywbmbh) As ywbmmc, A.ywbmbh,
               '' As bz, '优惠单' As djlx, a.sl, 1139,
               (Select sl1
                   From t_lb
                  Where bh =
                        (Select lb
                           From t_kcsm
                          Where Id = (Select Max(Id) From t_jjyhdkmx Where djbh = a.Djbh))) As sl1,
               (Select BH
                   From t_lb
                  Where bh =
                        (Select lb
                           From t_kcsm
                          Where Id = (Select Max(Id) From t_jjyhdkmx Where djbh = a.djbh))) As BH
          From t_jjyhdkhz a
         Where ywcbz = '0'
           And a.ywbmbh Like '$admin_username'
           And a.djrq < Date '$end'
           And Not Exists (Select 1 From t_tmp_fpls Where tslsh = a.tslsh)";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);
						
						$sql_gc = "Insert Into T_YFZK_SJDJ
        (dbrq, ysdj, ysrq, dm, zpz, zcs, zmy, zsy, ywbmmc, ywbmbh, bz, djlx, sl, czybh,
         sl1, lb)
        Select djrq, djbh, djrq,
               (Select '(' || bh || ')' || Trim(mc) From t_ghdw Where bh = a.ghdwh) As dm,
               (Select Count(*) From t_jjyhdkmx Where djbh = a.djbh) As zpz, 0 As zcs,
               0 As zmy,
               -zsy, (Select Trim(dm) From t_dm Where dh = a.ywbmbh) As ywbmmc, A.ywbmbh,
               '' As bz, '优惠单' As djlx, a.sl, 1139,
               (Select sl1
                   From t_lb
                  Where bh =
                        (Select lb
                           From t_kcsm
                          Where Id = (Select Max(Id) From t_jjyhdkmx Where djbh = a.Djbh))) As sl1,
               (Select BH
                   From t_lb
                  Where bh =
                        (Select lb
                           From t_kcsm
                          Where Id = (Select Max(Id) From t_jjyhdkmx Where djbh = a.djbh))) As BH
          From t_jjyhdkhz a, t_tmp_fpls b
         Where a.tslsh = b.tslsh
           And a.ywcbz = '0'
           And a.ywbmbh Like '$admin_username'
           And a.djrq < Date '$end'
           And (b.fpqrrq >= Date '$end' Or b.fpqrrq Is Null)";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);
						
						$sql_gc = "Insert Into T_YFZK_SJDJ
        (dbrq, ysdj, ysrq, dm, zpz, zcs, zmy, zsy, ywbmmc, ywbmbh, bz, djlx, sl, czybh,
         sl1, lb)
        Select a.djrq, a.djbh, a.djrq,
               (Select '(' || bh || ')' || Trim(mc) From t_ghdw Where bh = a.ghdwh) As dm,
               (Select Count(*) From t_jjyhdkmx Where djbh = a.djbh) As zpz,
               0 As zcs, 0 As zmy,
               -a.zsy, (Select Trim(dm) From t_dm Where dh = a.ywbmbh) As ywbmmc, A.ywbmbh,
               '' As bz, '优惠单' As djlx, a.sl, 1139,
               (Select sl1
                   From t_lb
                  Where bh =
                        (Select lb
                           From t_kcsm
                          Where Id = (Select Max(Id) From t_jjyhdkmx Where djbh = a.Djbh))) As sl1,
               (Select BH
                   From t_lb
                  Where bh =
                        (Select lb
                           From t_kcsm
                          Where Id = (Select Max(Id) From t_jjyhdkmx Where djbh = a.djbh))) As BH
          From t_jjyhdkhz a, t_fpls b
         Where a.tslsh = b.jzpch
           And ywcbz = '1'
           And b.fpqrrq >= Date '$end'
           And a.ywbmbh Like '$admin_username'
           And a.djrq < Date '$end'";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);
								
					
			}	
				
					$sql="Select ysdj, dm, zpz, zcs, zmy, zsy, (zsy / (1 + (a.sl1 * 0.01))) As bhszsy,ywbmmc, bz, djlx,to_char(a.ysrq, 'YYYY-MM-DD HH24:MI:SS') as ysrq, sl1,to_char(a.dbrq, 'YYYY-MM-DD HH24:MI:SS') as dbrq
 From t_Yfzk_Sjdj a ";
					//echo $sql;exit;
						
						$row=$ora->query($sql);
						//print_r($row);exit; 
					
					if($daochu && $daochu=='daochu'){
						
						$fileName = '应付款-采购未结-明细';
						$total = count($row);
						//echo $total;exit;  119674
						$title = array('DBRQ','YSDJ','YSRQ','DM','ZPZ','ZCS','ZMY','ZSY','BHSZSY','YWBMMC','BZ','DJLX','SL1');
						if($_SESSION['roleid']==26 && $daochulx =='导出到服务器指定位置'){
							$this->yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username,$timetype);
						}elseif($_SESSION['roleid']==26 && $daochulx =='从浏览器中下载'){
							$this->articleAccessLog($sql,$fileName,$title,$total,$yewugongsi);
						}
					}


				}elseif($yewuleixing=='应收款-发出未结-明细'){ 

				if($_SESSION['roleid']==26){//过程
						$sql_gc = "Delete From t_yszk_sjdj";
						$res = $ora->query($sql_gc);
						$sql_gc = "Delete From t_yszk_sjdj_cy";
						$res = $ora->query($sql_gc);
						$sql_gc = "Insert Into t_yszk_sjdj
        (dbrq, pfpch, dm, jsdm, zpz, zcs, zmy, zsy, cbj, ywbmmc, ywbmbh, bz, djlx, sl,
         czybh, SL1, LB)
        Select a.dbrq, a.pfpch,
               (Select '(' || dh || ')' || Trim(dmjc) From t_dm Where dh = a.dh) As dm,
               (Select '(' || dh || ')' || Trim(dmjc) From t_dm Where dh = a.jsdh) As jsdm,
               a.zpz, a.zcs, a.zmy, a.zsy, a.cbj,
               (Select Trim(dmjc) From t_dm Where dh = a.ywbmbh) As ywbmmc, a.ywbmbh,
               a.bz As bz,
               decode(a.djlx, 'FH', '正常发货', 'BL', '补录发货', 'YH', '补录优惠', '') As djlx, a.sl,
               1139,
               (Select sl1
                   From t_lb
                  Where bh =
                        (Select lb
                           From t_kcsm
                          Where Id = (Select Max(Id) From t_fhmx Where pfpch = a.pfpch))) As sl1,
               (Select BH
                   From t_lb
                  Where bh =
                        (Select lb
                           From t_kcsm
                          Where Id = (Select Max(Id) From t_fhmx Where pfpch = a.pfpch))) As BH
          From t_fhhz1 a
         Where a.dbrq < Date '$end'
           And a.ywbmbh Like '$admin_username'";
		 //echo $sql_gc;exit;
						$res = $ora->query($sql_gc);
						$sql_gc = "Insert Into t_yszk_sjdj
        (dbrq, pfpch, dm, jsdm, zpz, zcs, zmy, zsy, cbj, ywbmmc, ywbmbh, bz, djlx, sl,
         czybh, sl1, LB)
        Select a.dbrq, a.pfpch,
               (Select '(' || dh || ')' || Trim(dmjc) From t_dm Where dh = a.dh) As dm,
               (Select '(' || dh || ')' || Trim(dmjc) From t_dm Where dh = b.dh) As jsdm,
               a.zpz, a.zcs, a.zmy, a.zsy, a.cbj,
               (Select Trim(dmjc) From t_dm Where dh = a.ywbmbh) As ywbmmc, a.ywbmbh,
               a.bz As bz,
               decode(a.djlx, 'FH', '正常发货', 'BL', '补录发货', 'YH', '补录优惠', '') As djlx, a.sl,
               1139,
               (Select sl1
                   From t_lb
                  Where bh =
                        (Select lb
                           From t_kcsm
                          Where Id = (Select Max(Id) From t_fhmx Where pfpch = a.pfpch))) As sl1,
               (Select BH
                   From t_lb
                  Where bh =
                        (Select lb
                           From t_kcsm
                          Where Id = (Select Max(Id) From t_fhmx Where pfpch = a.pfpch))) As BH
          From t_fhhz2 a, t_tshz b
         Where a.tslsh = b.tslsh
           And a.dbrq < Date '$end'
           And b.jsrq >= Date '$end'
           And a.ywbmbh Like '$admin_username'";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);
						$sql_gc = "Insert Into t_yszk_sjdj
        (dbrq, pfpch, dm, jsdm, zpz, zcs, zmy, zsy, cbj, ywbmmc, ywbmbh, bz, djlx, sl,
         czybh, sl1, LB)
        Select
         (Select dbrq From t_fhhz Where pfpch = a.yspfpch_fg) As dbrq, a.yspfpch_fg,
         (Select '(' || dh || ')' || Trim(dmjc) From t_dm Where dh = a.dh) As dm,
         (Select '(' || dh || ')' || Trim(dmjc) From t_dm Where dh = a.jsdh) As jsdm,
         a.zpz, -a.zcs, -a.zmy, -a.zsy, -a.cbj,
         (Select Trim(dmjc) From t_dm Where dh = a.ywbmbh) As ywbmmc, a.ywbmbh, a.bz As bz,
         '正常发货' As djlx, a.sl, 1139,
         (Select sl1
             From t_lb
            Where bh =
                  (Select lb
                     From t_kcsm
                    Where Id = (Select Max(Id) From t_fhmx Where pfpch = a.pfpch))) As sl1,
         (Select BH
             From t_lb
            Where bh =
                  (Select lb
                     From t_kcsm
                    Where Id = (Select Max(Id) From t_fhmx Where pfpch = a.pfpch))) As BH
          From t_fhhz a
         Where a.ywbmbh Like '$admin_username'
           And (cspc = '更正' And dbrq >= Date '$end' And Not Exists
                (Select pfpch From t_fhhz2 Where pfpch = a.pfpch) And Not Exists
                (Select pfpch From t_fhhz1 Where pfpch = a.pfpch))
           And (Exists
                (Select pfpch
                   From t_fhhz1
                  Where pfpch = a.yspfpch
                    And dbrq < Date '$end'
                    And Not Exists (Select PFPCH From T_FHHZ1 Where PFPCH = a.PFPCH)) Or
                Exists
                (Select pfpch
                   From t_fhhz2
                  Where pfpch = a.yspfpch
                    And dbrq < Date '$end'
                    And Not Exists (Select PFPCH From T_FHHZ2 Where PFPCH = a.PFPCH)))";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);
						$sql_gc = "Insert Into t_yszk_sjdj
        (dbrq, pfpch, dm, jsdm, zpz, zcs, zmy, zsy, cbj, ywbmmc, ywbmbh, bz, djlx, sl,
         czybh, sl1, lb)
        Select thrq, thpch,
               (Select '(' || dh || ')' || Trim(dmjc) From t_dm Where dh = a.dh) As dm,
               (Select '(' || dh || ')' || Trim(dmjc) From t_dm Where dh = a.jsdh) As jsdm,
               zpz, -zcs, decode(djlx, 'YH', 0, NVL(-zmy, 0)) As ZMY, -zsy, -cbj,
               (Select Trim(dmjc) From t_dm Where dh = a.ywbmbh) As ywbmmc, a.ywbmbh,
               bz As bz, decode(djlx, 'FT', '正常发退', 'YH', '优惠活动', '') As djlx, a.sl,
               1139,
               (Select sl1
                   From t_lb
                  Where bh =
                        (Select lb
                           From t_kcsm
                          Where Id = (Select Max(Id) From t_khthmx Where ywpch = a.thpch))) As sl1,
               (Select bh
                   From t_lb
                  Where bh =
                        (Select lb
                           From t_kcsm
                          Where Id = (Select Max(Id) From t_khthmx Where ywpch = a.thpch))) As lb
          From t_khthhz1 a
         Where a.thrq < Date '$end'
           And ywbmbh Like '$admin_username'";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);
						$sql_gc = "Insert Into t_yszk_sjdj
        (dbrq, pfpch, dm, jsdm, zpz, zcs, zmy, zsy, cbj, ywbmmc, ywbmbh, bz, djlx, sl,
         czybh, sl1, lb)
        Select a.thrq, a.thpch,
               (Select '(' || dh || ')' || Trim(dmjc) From t_dm Where dh = a.dh) As dm,
               (Select '(' || dh || ')' || Trim(dmjc) From t_dm Where dh = b.dh) As jsdm,
               a.zpz, -a.zcs, decode(A.djlx, 'YH', 0, -a.zmy) As ZMY, -a.zsy, -a.cbj,
               (Select Trim(dmjc) From t_dm Where dh = a.ywbmbh) As ywbmmc, a.ywbmbh,
               a.bz As bz, decode(a.djlx, 'FT', '正常发退', 'YH', '优惠活动', '') As djlx, a.sl,
               1139,
               (Select sl1
                   From t_lb
                  Where bh =
                        (Select lb
                           From t_kcsm
                          Where Id = (Select Max(Id) From t_khthmx Where ywpch = a.thpch))) As sl1,
               (Select bh
                   From t_lb
                  Where bh =
                        (Select lb
                           From t_kcsm
                          Where Id = (Select Max(Id) From t_khthmx Where ywpch = a.thpch))) As lb
          From t_khthhz2 a, t_tshz b
         Where a.tslsh = b.tslsh
           And a.thrq < Date '$end'
           And b.jsrq >= Date '$end'
           And a.ywbmbh Like '$admin_username'";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);
						$sql_gc = "Update t_yszk_sjdj a
       Set SL1 = (Select SL
                     From T_FHHZ1
                    Where pfpch = a.pfpch
                      And rownum = 1)
     Where sl1 Is Null
       And czybh = 1139";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);
						$sql_gc = "Update t_yszk_sjdj a
       Set SL1 = (Select SL
                     From T_FHHZ2
                    Where pfpch = a.pfpch
                      And rownum = 1)
     Where sl1 Is Null
       And czybh = 1139";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);
						
						$sql_gc = "Update t_yszk_sjdj a
       Set lb = (Select lb
                    From T_FHHZ2
                   Where pfpch = a.pfpch
                     And rownum = 1)
     Where lb Is Null
       And czybh = 1139";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);

						$sql_gc = "Update t_yszk_sjdj a
       Set lb = (Select lb
                    From T_FHHZ1
                   Where pfpch = a.pfpch
                     And rownum = 1)
     Where lb Is Null
       And czybh = 1139";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);
					if($admin_username == '000004'){
						$sql_gc = "Insert Into t_Yszk_Sjdj_Cy
            (dm, jsdm, ywbmmc, ywbmbh, pfpch, Id, dbrq, Sys, cbj, mxlb, djlb)
            Select a.dm, a.jsdm, a.ywbmmc, a.ywbmbh, a.pfpch, b.id, a.dbrq, b.sys, b.cbj,
                   c.lb, a.lb
              From t_Yszk_Sjdj a, t_fhmx b, t_kcsm c
             Where a.pfpch = b.pfpch
               And c.id = b.id
               And c.lb <> a.lb";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);
						$sql_gc = "Update t_Yszk_Sjdj_Cy t
           Set cy = ((cbj / (1 + (Select sl1 / 100 From t_lb Where bh = t.mxlb))) -
                     (cbj / (1 + (Select sl1 / 100 From t_lb Where bh = t.djlb))))";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);
						
					}	

				}	

				
					$sql = "Select dbrq, pfpch, dm, jsdm, zpz, zcs, zmy, zsy, cbj, bhszsy, bhscbj, ywbmmc,ywbmbh ,bz, djlx,
       sl1,a.mc As dh,
       (Select mc
          From  t_dqbm c 
       Where bh = (Select dqbh From t_dm Where dh = a.mc)) As dq
  From (Select dbrq, pfpch, dm, jsdm, zpz, zcs, zmy, zsy, cbj, bhszsy, bhscbj, ywbmmc,ywbmbh, bz,
                djlx, sl1, substr(dm, 2, 6) As mc
           From (Select pfpch, dm, jsdm, zpz, zcs, zmy, zsy, cbj,
                         (zsy / (1 + (sl1 * 0.01))) As bhszsy,
                         (cbj / (1 + (sl1 * 0.01))) As bhscbj, ywbmmc,ywbmbh ,bz, djlx, sl1,to_char(t_Yszk_Sjdj.dbrq, 'YYYY-MM-DD HH24:MI:SS') as dbrq
                    From t_Yszk_Sjdj)) a 
 Where ywbmbh = '$admin_username'";

										
						$row=$ora->query($sql);
						//$row1 = $ora->getpage($sql,10000,1,5000);
						//print_r($row1);exit;
					
					if($daochu && $daochu=='daochu'){
						
						$fileName = '应收款-发出未结-明细';
						$total = count($row);
						//echo $total;exit;
						$title = array('DBRQ','PFPCH','DM','JSDM','ZPZ','ZCS','ZMY','ZSY','CBJ','BHSZSY','BHSCBJ','YWBMMC','YWBMBH','BZ','DJLX','SL1','DQ');
						if($_SESSION['roleid']==26 && $daochulx =='导出到服务器指定位置'){
							$this->yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username,$timetype);
						}elseif($_SESSION['roleid']==26 && $daochulx =='从浏览器中下载'){
							$this->articleAccessLog($sql,$fileName,$title,$total,$yewugongsi);
						}
					}


				}elseif($yewuleixing=='应付款月报'){  
				
				$guocheng=getcache('lsguocheng3');
				
				if($_SESSION['roleid']==26){//过程
					if($guocheng['guocheng'] != $timetype ){
						$sql_gc = "Delete From t_yFzk_month_tmp_clj";
						$res = $ora->query($sql_gc);
						$sql_gc = "Delete From t_yFzk_month_rysj_clj";
						$ora->query($sql_gc);
						$sql_gc = "Insert Into t_yFzk_month_tmp_clj
        (qmmy, qmsy, ghdwh, ywbmbh, YWLX, QCRQ, QMRQ)
        Select Sum(zmy), Sum(zsy), ghdwh, ywbmbh, 'BL', '',''
          From t_bldhdj t
         Where dbrq < Date '$end'
           And ((tslsh Is Null And ywcbz = '0') Or Exists
                (Select tslsh
                   From t_tmp_fpls
                  Where (fpqrrq >= Date '$end' Or fpqrrq Is Null)
                    And tslsh = t.tslsh) Or Exists
                (Select jzpch
                   From t_fpls
                  Where fpqrrq >= Date '$end'
                    And jzpch = t.tslsh))
         Group By ywbmbh, ghdwh";
		 //echo $sql_gc;exit;
						$res = $ora->query($sql_gc);
						$sql_gc = "Insert Into t_yFzk_month_tmp_clj
        (qmmy, qmsy, ghdwh, ywbmbh, YWLX, QCRQ, QMRQ)
        Select Sum(zmy), Sum(zsy), ghdwh, ywbmbh, 'DH',  '',''
          From t_dhDJ_cf t
         Where DBrq < Date '$end'
           And (tslsh Is Null Or Exists
                (Select tslsh
                   From t_tmp_fpls
                  Where (fpqrrq >= Date '$end' Or fpqrrq Is Null)
                    And tslsh = t.tslsh) Or Exists
                (Select jzpch
                   From t_fpls
                  Where fpqrrq >= Date '$end'
                    And jzpch = t.tslsh))
         Group By ywbmbh, ghdwh";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);
						$sql_gc = "Insert Into t_yFzk_month_tmp_clj
        (qmmy, qmsy, ghdwh, ywbmbh, YWLX, QCRQ, QMRQ)
        Select Sum(-zmy), Sum(-zsy), hybh, ywbmbh, 'TH',  '',''
          From t_hythhz t
         Where thrq < Date '$end'
           And (tslsh Is Null Or Exists
                (Select tslsh
                   From t_tmp_fpls
                  Where (fpqrrq >= Date '$end' Or fpqrrq Is Null)
                    And tslsh = t.tslsh) Or Exists
                (Select jzpch
                   From t_fpls
                  Where fpqrrq >= Date '$end'
                    And jzpch = t.tslsh))
         Group By ywbmbh, hybh";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);
						$sql_gc = "Insert Into t_yfzk_month_tmp_clj
        (qmmy, qmsy, ghdwh, ywbmbh, YWLX, QCRQ, QMRQ)
        Select 0, Sum(-zsy), ghdwh, ywbmbh, 'YH',  '',''
          From t_Jjyhdkhz t
         Where djrq < Date '$end'
           And ((tslsh Is Null And ywcbz = '0') Or Exists
                (Select tslsh
                   From t_tmp_fpls
                  Where (fpqrrq >= Date '$end' Or fpqrrq Is Null)
                    And tslsh = t.tslsh) Or Exists
                (Select jzpch
                   From t_fpls
                  Where fpqrrq >= Date '$end'
                    And jzpch = t.tslsh))
         Group By ywbmbh, ghdwh";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);
						$sql_gc = "Insert Into t_yFzk_month_tmp_clj
        (qcmy, qcsy, ghdwh, ywbmbh, YWLX, QCRQ, QMRQ)
        Select Sum(zmy), Sum(zsy), ghdwh, ywbmbh, 'BL', '',''
          From t_bldhdj t
         Where dbrq < Date '$start'
           And ((tslsh Is Null And ywcbz = '0') Or Exists
                (Select tslsh
                   From t_tmp_fpls
                  Where (fpqrrq >= Date '$start' Or fpqrrq Is Null)
                    And tslsh = t.tslsh) Or Exists
                (Select jzpch
                   From t_fpls
                  Where fpqrrq >= Date '$start'
                    And jzpch = t.tslsh))
         Group By ywbmbh, ghdwh";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);
						$sql_gc = "Insert Into t_yFzk_month_tmp_clj
        (qCmy, qCsy, ghdwh, ywbmbh, YWLX, QCRQ, QMRQ)
        Select Sum(zmy), Sum(zsy), ghdwh, ywbmbh, 'DH',  '',''
          From t_dhDJ_cf t
         Where DBrq < Date '$start'
           And (tslsh Is Null Or Exists
                (Select tslsh
                   From t_tmp_fpls
                  Where (fpqrrq >= Date '$start' Or fpqrrq Is Null)
                    And tslsh = t.tslsh) Or Exists
                (Select jzpch
                   From t_fpls
                  Where fpqrrq >= Date '$start'
                    And jzpch = t.tslsh))
         Group By ywbmbh, ghdwh";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);
						$sql_gc = "Insert Into t_yFzk_month_tmp_clj
        (qcmy, qcsy, ghdwh, ywbmbh, YWLX, QCRQ, QMRQ)
        Select Sum(-zmy), Sum(-zsy), hybh, ywbmbh, 'TH',  '',''
          From t_hythhz t
         Where thrq < Date '$start'
           And (tslsh Is Null Or Exists
                (Select tslsh
                   From t_tmp_fpls
                  Where (fpqrrq >= Date '$start' Or fpqrrq Is Null)
                    And tslsh = t.tslsh) Or Exists
                (Select jzpch
                   From t_fpls
                  Where fpqrrq >= Date '$start'
                    And jzpch = t.tslsh))
         Group By ywbmbh, hybh";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);
						
						$sql_gc = "Insert Into t_yfzk_month_tmp_clj
        (qcmy, qcsy, ghdwh, ywbmbh, YWLX, QCRQ, QMRQ)
        Select 0, Sum(-zsy), ghdwh, ywbmbh, 'YH',  '',''
          From t_Jjyhdkhz t
         Where djrq < Date '$start'
           And ((tslsh Is Null And ywcbz = '0') Or Exists
                (Select tslsh
                   From t_tmp_fpls
                  Where (fpqrrq >= Date '$start' Or fpqrrq Is Null)
                    And tslsh = t.tslsh) Or Exists
                (Select jzpch
                   From t_fpls
                  Where fpqrrq >= Date '$start'
                    And jzpch = t.tslsh))
         Group By ywbmbh, ghdwh";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);

						$sql_gc = "Insert Into t_yFzk_month_tmp_clj
        (bqjsmy, bqjssy, ghdwh, ywbmbh, YWLX, QCRQ, QMRQ)
        Select Sum(zmy), Sum(zsy), ghdwh, ywbmbh, 'BL',  '',''
          From t_bldhdj t
         Where dbrq < Date '$end'
           And (Exists (Select tslsh
                          From t_tmp_fpls
                         Where fpqrrq >= Date '$start'
                           And fpqrrq < Date '$end'
                              
                           And tslsh = t.tslsh) Or Exists
                (Select jzpch
                   From t_fpls
                  Where fpqrrq >= Date '$start'
                    And fpqrrq < Date '$end'
                    And
                        jzpch = t.tslsh))
         Group By ywbmbh, ghdwh";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);
						
						$sql_gc = "Insert Into t_yFzk_month_tmp_clj
        (bqjsmy, bqjssy, ghdwh, ywbmbh, YWLX, QCRQ, QMRQ)
        Select Sum(zmy), Sum(zsy), ghdwh, ywbmbh, 'DH',  '',''
          From t_dhdj_cf t
         Where dbrq < Date '$end'
           And (Exists (Select tslsh
                          From t_tmp_fpls
                         Where fpqrrq >= Date '$start'
                           And fpqrrq < Date '$end'
                           And tslsh = t.tslsh) Or Exists
                (Select jzpch
                   From t_fpls
                  Where fpqrrq >= Date '$start'
                    And fpqrrq < Date '$end'
                    And
                        jzpch = t.tslsh))
         Group By ywbmbh, ghdwh";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);
						
						$sql_gc = "Insert Into t_yFzk_month_tmp_clj
        (bqjsmy, bqjssy, ghdwh, ywbmbh, YWLX, QCRQ, QMRQ)
        Select Sum(-zmy), Sum(-zsy), hybh, ywbmbh, 'TH',  '',''
          From t_hythhz t
         Where thrq < Date '$end'
           And (Exists (Select tslsh
                          From t_tmp_fpls
                         Where fpqrrq >= Date '$start'
                           And fpqrrq < Date '$end'
                           And tslsh = t.tslsh) Or Exists
                (Select jzpch
                   From t_fpls
                  Where fpqrrq >= Date '$start'
                    And fpqrrq < Date '$end'
                    And
                        jzpch = t.tslsh))
         Group By ywbmbh, hybh";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);
						
						$sql_gc = "Insert Into t_yfzk_month_tmp_clj
        (bqjsmy, bqjssy, ghdwh, ywbmbh, YWLX, QCRQ, QMRQ)
        Select 0, Sum(-zsy), ghdwh, ywbmbh, 'YH',  '',''
          From t_Jjyhdkhz t
         Where djrq < Date '$end'
           And (Exists (Select tslsh
                          From t_tmp_fpls
                         Where fpqrrq >= Date '$start'
                           And fpqrrq < Date '$end'
                           And tslsh = t.tslsh) Or Exists
                (Select jzpch
                   From t_fpls
                  Where fpqrrq >= Date '$start'
                    And fpqrrq < Date '$end'
                    And jzpch = t.tslsh))
         Group By ywbmbh, ghdwh";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);
						
						$sql_gc = "Insert Into t_yFzk_month_tmp_clj
        (BQZJmy, BQZJsy, ghdwh, ywbmbh, YWLX, QCRQ, QMRQ)
        Select Sum(zmy), Sum(zsy), ghdwh, ywbmbh, 'BL',  '',''
          From t_bldhdj t
         Where dbrq < Date '$end'
           And dbrq >= Date '$start'
         Group By ywbmbh, ghdwh";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);

						$sql_gc = "Insert Into t_yFzk_month_tmp_clj
        (BQZJmy, BQZJsy, ghdwh, ywbmbh, YWLX, QCRQ, QMRQ)
        Select Sum(zmy), Sum(zsy), ghdwh, ywbmbh, 'DH', '',''
          From t_dhdj_cf t
         Where dbrq < Date '$end'
           And dbrq >= Date '$start'
         Group By ywbmbh, ghdwh";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);
						$sql_gc = "Insert Into t_yFzk_month_tmp_clj
        (BQZJmy, BQZJsy, ghdwh, ywbmbh, YWLX, QCRQ, QMRQ)
        Select Sum(-zmy), Sum(-zsy), hybh, ywbmbh, 'TH',  '',''
          From t_hythhz t
         Where thrq < Date '$end'
           And thrq >= Date '$start'
         Group By ywbmbh, hybh";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);
						$sql_gc = "Insert Into t_yfzk_month_tmp_clj
        (bqzjmy, bqzjsy, ghdwh, ywbmbh, YWLX, QCRQ, QMRQ)
        Select 0, Sum(-zsy), ghdwh, ywbmbh, 'YH', '',''
          From t_Jjyhdkhz t
         Where djrq >= Date '$start'
           And djrq < Date '$end'
         Group By ywbmbh, ghdwh";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);

						$sql_gc = "Insert Into t_yFzk_month_rysj_clj
        (YWBMBH, GHDWH, QCMY, QCSY, BQZJMY, BQZJSY, BQJSMY, BQJSSY, QMMY, QMSY, QCRQ,
         QMRQ)
        Select YWBMBH, GHDWH, Sum(QCMY), Sum(QCSY), Sum(BQZJMY), Sum(BQZJSY), Sum(BQJSMY),
               Sum(BQJSSY), Sum(QMMY), Sum(QMSY), Date '$start', Date '$end'
          From t_yFzk_month_tmp_clj
         Where ghdwh <> 'L00099'
         Group By GHDWH, YWBMBH";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);

						//$sql_gc = "Update t_yFzk_month_rysj_clj Set tjrq='$start',tjny='$end'";
		 //echo $sql_gc;exit;
						//$ora->query($sql_gc);
						
						$guocheng[guocheng]=$timetype;	
						setcache("lsguocheng3",$guocheng);
					}			
					
			}	
					$sql = "Select ghdwh,(Select mc From t_ghdw Where bh = a.ghdwh) As ghdwhmc,(Select dm From t_dm Where dh =a.ywbmbh) As  ywbmbh,a.qcmy,a.qcsy,a.bqzjmy,a.bqzjsy,a.bqjsmy,a.bqjssy,a.qmmy,a.qmsy,to_char(a.QMRQ, 'YYYY-MM-DD') as QMRQ,to_char(a.QCRQ, 'YYYY-MM-DD') as QCRQ	
 From t_yfzk_month_rysj_clj a where ywbmbh = '$admin_username'";
						$row=$ora->query($sql);
						//print_r($row);exit;
					
					if($daochu && $daochu=='daochu'){
						
						$fileName = '应付款月报';
						$total = count($row);
						$title = array('统计日期','统计年月','供货单位','业务部门','期初码洋','期初实洋','本期增加码洋','本期增加实洋','本期减少码洋','本期减少实洋','期末码洋','期末实洋');
						if($_SESSION['roleid']==26 && $daochulx =='导出到服务器指定位置'){
							$this->yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username,$timetype);
						}elseif($_SESSION['roleid']==26 && $daochulx =='从浏览器中下载'){
							$this->articleAccessLog($sql,$fileName,$title,$total,$yewugongsi);
						}
					}


				}elseif($yewuleixing=='应付款月报含税率'){ 
				$guocheng=getcache('lsguocheng4');
				
				if($_SESSION['roleid']==26){//过程
					if($guocheng['guocheng'] != $timetype ){
						$sql_gc1 = "Delete From t_yFzk_month_tmp_clj";
						$row = $ora->query($sql_gc1);
						$sql_gc2 = "Delete From t_yFzk_month_rysj_clj_sl";
						$row=$ora->query($sql_gc2);
						$sql_gc3 = "Insert Into t_yFzk_month_tmp_clj
        (qmmy, qmsy, ghdwh, ywbmbh, YWLX, QCRQ, QMRQ, sl)
        Select Sum(zmy), Sum(zsy), ghdwh, ywbmbh, 'BL', '','', sl
          From t_bldhdj t
         Where dbrq < Date '$end'
           And ((tslsh Is Null And ywcbz = '0') Or Exists
                (Select tslsh
                   From t_tmp_fpls
                  Where (fpqrrq >= Date '$end' Or fpqrrq Is Null)
                    And tslsh = t.tslsh) Or Exists
                (Select jzpch
                   From t_fpls
                  Where fpqrrq >= Date '$end'
                    And jzpch = t.tslsh))
         Group By ywbmbh, ghdwh, sl";
		 //echo $sql_gc3;exit;
						$row=$ora->query($sql_gc3);
						$sql_gc4 = "Insert Into t_yFzk_month_tmp_clj
        (qmmy, qmsy, ghdwh, ywbmbh, YWLX, QCRQ, QMRQ, sl)
        Select Sum(zmy), Sum(zsy), ghdwh, ywbmbh, 'DH', '','', sl
          From t_dhDJ_cf t
         Where DBrq < Date '$end'
           And (tslsh Is Null Or Exists
                (Select tslsh
                   From t_tmp_fpls
                  Where (fpqrrq >= Date '$end' Or fpqrrq Is Null)
                    And tslsh = t.tslsh) Or Exists
                (Select jzpch
                   From t_fpls
                  Where fpqrrq >= Date '$end'
                    And jzpch = t.tslsh))
         Group By ywbmbh, ghdwh, sl";
						$row=$ora->query($sql_gc4);
						$sql_gc5 = "Insert Into t_yFzk_month_tmp_clj
        (qmmy, qmsy, ghdwh, ywbmbh, YWLX, QCRQ, QMRQ, sl)
        Select Sum(-zmy), Sum(-zsy), hybh, ywbmbh, 'TH', '','', sl
          From t_hythhz t
         Where thrq < Date '$end'
           And (tslsh Is Null Or Exists
                (Select tslsh
                   From t_tmp_fpls
                  Where (fpqrrq >= Date '$end' Or fpqrrq Is Null)
                    And tslsh = t.tslsh) Or Exists
                (Select jzpch
                   From t_fpls
                  Where fpqrrq >= Date '$end'
                    And jzpch = t.tslsh))
         Group By ywbmbh, hybh, sl";
						$row=$ora->query($sql_gc5);	
						$sql_gc6 = "Insert Into t_yfzk_month_tmp_clj
        (qmmy, qmsy, ghdwh, ywbmbh, YWLX, QCRQ, QMRQ, sl)
        Select 0, Sum(-zsy), ghdwh, ywbmbh, 'YH', '','', sl
          From t_Jjyhdkhz t
         Where djrq < Date '$end'
              
           And ((tslsh Is Null And ywcbz = '0') Or Exists
                (Select tslsh
                   From t_tmp_fpls
                  Where (fpqrrq >= Date '$end' Or fpqrrq Is Null)
                    And tslsh = t.tslsh) Or Exists
                (Select jzpch
                   From t_fpls
                  Where fpqrrq >= Date '$end'
                    And jzpch = t.tslsh))
         Group By ywbmbh, ghdwh, sl";
						$row=$ora->query($sql_gc6);	
						$sql_gc7 = "Insert Into t_yFzk_month_tmp_clj
        (qcmy, qcsy, ghdwh, ywbmbh, YWLX, QCRQ, QMRQ, sl)
        Select Sum(zmy), Sum(zsy), ghdwh, ywbmbh, 'BL', '','', sl
          From t_bldhdj t
         Where dbrq < Date '$start'
           And ((tslsh Is Null And ywcbz = '0') Or Exists
                (Select tslsh
                   From t_tmp_fpls
                  Where (fpqrrq >= Date '$start' Or fpqrrq Is Null)
                    And tslsh = t.tslsh) Or Exists
                (Select jzpch
                   From t_fpls
                  Where fpqrrq >= Date '$start'
                    And jzpch = t.tslsh))
         Group By ywbmbh, ghdwh, sl";
						$row=$ora->query($sql_gc7);	
						$sql_gc8 = "Insert Into t_yFzk_month_tmp_clj
        (qCmy, qCsy, ghdwh, ywbmbh, YWLX, QCRQ, QMRQ, sl)
        Select Sum(zmy), Sum(zsy), ghdwh, ywbmbh, 'DH', '','', sl
          From t_dhDJ_cf t
         Where DBrq < Date '$start'
           And (tslsh Is Null Or Exists
                (Select tslsh
                   From t_tmp_fpls
                  Where (fpqrrq >= Date '$start' Or fpqrrq Is Null)
                    And tslsh = t.tslsh) Or Exists
                (Select jzpch
                   From t_fpls
                  Where fpqrrq >= Date '$start'
                    And jzpch = t.tslsh))
         Group By ywbmbh, ghdwh, sl";
						$row=$ora->query($sql_gc8);	

						$sql_gc9 = "Insert Into t_yFzk_month_tmp_clj
        (qcmy, qcsy, ghdwh, ywbmbh, YWLX, QCRQ, QMRQ, sl)
        Select Sum(-zmy), Sum(-zsy), hybh, ywbmbh, 'TH', '','', sl
          From t_hythhz t
         Where thrq < Date '$start'
           And (tslsh Is Null Or Exists
                (Select tslsh
                   From t_tmp_fpls
                  Where (fpqrrq >= Date '$start' Or fpqrrq Is Null)
                    And tslsh = t.tslsh) Or Exists
                (Select jzpch
                   From t_fpls
                  Where fpqrrq >= Date '$start'
                    And jzpch = t.tslsh))
         Group By ywbmbh, hybh, sl";
						$row=$ora->query($sql_gc9);	
						$sql_gc10 = "Insert Into t_yfzk_month_tmp_clj
        (qcmy, qcsy, ghdwh, ywbmbh, YWLX, QCRQ, QMRQ, sl)
        Select 0, Sum(-zsy), ghdwh, ywbmbh, 'YH', '','', sl
          From t_Jjyhdkhz t
         Where djrq < Date '$start'
           And ((tslsh Is Null And ywcbz = '0') Or Exists
                (Select tslsh
                   From t_tmp_fpls
                  Where (fpqrrq >= Date '$start' Or fpqrrq Is Null)
                    And tslsh = t.tslsh) Or Exists
                (Select jzpch
                   From t_fpls
                  Where fpqrrq >= Date '$start'
                    And jzpch = t.tslsh))
         Group By ywbmbh, ghdwh, sl";
						$row=$ora->query($sql_gc10);	
						$sql_gc11 = "Insert Into t_yFzk_month_tmp_clj
        (bqjsmy, bqjssy, ghdwh, ywbmbh, YWLX, QCRQ, QMRQ, sl)
        Select Sum(zmy), Sum(zsy), ghdwh, ywbmbh, 'BL', '','', sl
          From t_bldhdj t
         Where dbrq < Date '$end'
           And (Exists (Select tslsh
                          From t_tmp_fpls
                         Where fpqrrq >= Date '$start'
                           And fpqrrq < Date '$end'
                           And tslsh = t.tslsh) Or Exists
                (Select jzpch
                   From t_fpls
                  Where fpqrrq >= Date '$start'
                    And fpqrrq < Date '$end'
                    And
                        jzpch = t.tslsh))
         Group By ywbmbh, ghdwh, sl";
						$row=$ora->query($sql_gc11);	
						
						$sql_gc12 = "Insert Into t_yFzk_month_tmp_clj
        (bqjsmy, bqjssy, ghdwh, ywbmbh, YWLX, QCRQ, QMRQ, sl)
        Select Sum(zmy), Sum(zsy), ghdwh, ywbmbh, 'DH', '','', sl
          From t_dhdj_cf t
         Where dbrq < Date '$end'
           And (Exists (Select tslsh
                          From t_tmp_fpls
                         Where fpqrrq >= Date '$start'
                           And fpqrrq < Date '$end'
                           And tslsh = t.tslsh) Or Exists
                (Select jzpch
                   From t_fpls
                  Where fpqrrq >= Date '$start'
                    And fpqrrq < Date '$end'
                    And
                        jzpch = t.tslsh))
         Group By ywbmbh, ghdwh, sl";
						$row=$ora->query($sql_gc12);	

						$sql_gc13 = "Insert Into t_yFzk_month_tmp_clj
        (bqjsmy, bqjssy, ghdwh, ywbmbh, YWLX, QCRQ, QMRQ, sl)
        Select Sum(-zmy), Sum(-zsy), hybh, ywbmbh, 'TH', '','', sl
          From t_hythhz t
         Where thrq < Date '$end'
           And (Exists (Select tslsh
                          From t_tmp_fpls
                         Where fpqrrq >= Date '$start'
                           And fpqrrq < Date '$end'
                           And tslsh = t.tslsh) Or Exists
                (Select jzpch
                   From t_fpls
                  Where fpqrrq >= Date '$start'
                    And fpqrrq < Date '$end'
                    And
                        jzpch = t.tslsh))
         Group By ywbmbh, hybh, sl";
						$row=$ora->query($sql_gc13);	

						$sql_gc14 = "Insert Into t_yfzk_month_tmp_clj
        (bqjsmy, bqjssy, ghdwh, ywbmbh, YWLX, QCRQ, QMRQ, sl)
        Select 0, Sum(-zsy), ghdwh, ywbmbh, 'YH', '','', sl
          From t_Jjyhdkhz t
         Where djrq < Date '$end'
           And (Exists (Select tslsh
                          From t_tmp_fpls
                         Where fpqrrq >= Date '$start'
                           And fpqrrq < Date '$end'
                           And tslsh = t.tslsh) Or Exists
                (Select jzpch
                   From t_fpls
                  Where fpqrrq >= Date '$start'
                    And fpqrrq < Date '$end'
                    And jzpch = t.tslsh))
         Group By ywbmbh, ghdwh, sl";
						$row=$ora->query($sql_gc14);	

						$sql_gc15 = "Insert Into t_yFzk_month_tmp_clj
        (BQZJmy, BQZJsy, ghdwh, ywbmbh, YWLX, QCRQ, QMRQ, sl)
        Select Sum(zmy), Sum(zsy), ghdwh, ywbmbh, 'BL', '','', sl
          From t_bldhdj t
         Where dbrq < Date '$end'
           And dbrq >= Date '$start'
         Group By ywbmbh, ghdwh,sl";
						$row=$ora->query($sql_gc15);	

						$sql_gc16 = "Insert Into t_yFzk_month_tmp_clj
        (BQZJmy, BQZJsy, ghdwh, ywbmbh, YWLX, QCRQ, QMRQ, sl)
        Select Sum(zmy), Sum(zsy), ghdwh, ywbmbh, 'DH', '','', sl
          From t_dhdj_cf t
         Where dbrq < Date '$end'
           And dbrq >= Date '$start'
         Group By ywbmbh, ghdwh, sl";
						$row=$ora->query($sql_gc16);	

						$sql_gc17 = "Insert Into t_yFzk_month_tmp_clj
        (BQZJmy, BQZJsy, ghdwh, ywbmbh, YWLX, QCRQ, QMRQ,sl)
        Select Sum(-zmy), Sum(-zsy), hybh, ywbmbh, 'TH', '','',sl
          From t_hythhz t
         Where thrq < Date '$end'
           And thrq >= Date '$start'
         Group By ywbmbh, hybh,sl";
						$row=$ora->query($sql_gc17);	

						$sql_gc18 = "Insert Into t_yfzk_month_tmp_clj
        (bqzjmy, bqzjsy, ghdwh, ywbmbh, YWLX, QCRQ, QMRQ,sl)
        Select 0, Sum(-zsy), ghdwh, ywbmbh, 'YH', '','',sl
          From t_Jjyhdkhz t
         Where djrq >= Date '$start'
           And djrq < Date '$end'
         Group By ywbmbh, ghdwh,sl";
						$row=$ora->query($sql_gc18);	

						$sql_gc19 = "Insert Into t_yFzk_month_rysj_clj_sl
        (YWBMBH, GHDWH, QCMY, QCSY, BQZJMY, BQZJSY, BQJSMY, BQJSSY, QMMY, QMSY, QCRQ,
         QMRQ, sl)
        Select YWBMBH, GHDWH, Sum(QCMY), Sum(QCSY), Sum(BQZJMY), Sum(BQZJSY), Sum(BQJSMY),
               Sum(BQJSSY), Sum(QMMY), Sum(QMSY), Date '$start', Date '$end', sl
          From t_yFzk_month_tmp_clj
         Group By GHDWH, YWBMBH, sl";
						$row=$ora->query($sql_gc19);	
						$guocheng[guocheng]=$timetype;	
						setcache("lsguocheng4",$guocheng);
					}			
					
			}	
					$sql = "Select (Select dm From t_dm Where dh =a.ywbmbh) As YWBMBH,a.ghdwh,(Select mc From t_ghdw Where bh = a.ghdwh) As ghdwhmc,a.SL,a.QCMY,a.QCSY,a.BQZJMY,a.BQZJSY,a.BQJSMY,a.BQJSSY,a.QMMY,a.QMSY,to_char(a.QMRQ, 'YYYY-MM-DD') as QMRQ,to_char(a.QCRQ, 'YYYY-MM-DD') as QCRQ
					From t_yfzk_month_rysj_clj_SL a where ywbmbh = '$admin_username'";

//'QCRQ','QMRQ','GHDWH','YWBMBH','QCMY','QCSY','BQZJMY','BQZJSY','BQJSMY','BQJSSY','QMMY','QMSY','DQMY','DQSY','BQFKJE','NLJFKJE','YJSBJ','SL'

					//echo $sql;exit;
										
						$row=$ora->query($sql);
						//print_r($row);exit;
					
					if($daochu && $daochu=='daochu'){
						
						$fileName = '应付款月报含税率';
						$total = count($row);
						$title = array('统计日前','统计年月','业务部门','供货单位','税率%','期初码洋','期初实洋','本期增加码洋','本期增加实洋','本期减少码洋','本期减少实洋','期末码洋','期末实洋');
						//$this->articleAccessLog($sql,$fileName,$title,$total,$yewugongsi);
						if($_SESSION['roleid']==26 && $daochulx =='导出到服务器指定位置'){
							$this->yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username,$timetype);
						}elseif($_SESSION['roleid']==26 && $daochulx =='从浏览器中下载'){
							$this->articleAccessLog($sql,$fileName,$title,$total,$yewugongsi);
						}
						//$this->yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username);
					}

				}elseif($yewuleixing=='应收款月报'){ 
				$guocheng=getcache('lsguocheng5');
				
				if($_SESSION['roleid']==26){//过程
					if($guocheng['guocheng'] != $timetype ){
						$sql_gc = "Delete From t_yszk_month_tmp_clj";
						$res = $ora->query($sql_gc);
						$sql_gc = "Delete From t_yszk_month_rysj_clj";
						$ora->query($sql_gc);
						$sql_gc = "Insert Into t_yszk_month_tmp_clj
        (qmmy, qmsy, qmcbj, dh, ywbmbh, YWLX, QCRQ, QMRQ)
        Select Sum(zmy), Sum(zsy), Sum(NVL(cbj, 0)), DH, ywbmbh, 'FHZ1', '',''
          From t_fhhz1 t
         Where dbrq < Date '$end'
         Group By ywbmbh, DH";
		 //echo $sql_gc;exit;
						$res = $ora->query($sql_gc);
						$sql_gc = "Insert Into t_yszk_month_tmp_clj
        (qmmy, qmsy, qmcbj, dh, ywbmbh, YWLX, QCRQ, QMRQ)
        Select Sum(-zmy), Sum(-zsy), Sum(NVL(-cbj, 0)), DH, ywbmbh, 'FHGZ', '',''
          From t_fhhz t
         Where (cspc = '更正' And dbrq >= Date '$end' And Not Exists
                (Select pfpch From t_fhhz2 Where pfpch = t.pfpch) And Not Exists
                (Select pfpch From t_fhhz1 Where pfpch = t.pfpch))
           And (Exists
                (Select pfpch
                   From t_fhhz1
                  Where pfpch = t.yspfpch
                    And dbrq < Date '$end'
                    And Not Exists (Select PFPCH From T_FHHZ1 Where PFPCH = T.PFPCH)) Or
                Exists
                (Select pfpch
                   From t_fhhz2
                  Where pfpch = t.yspfpch
                    And dbrq < Date '$end'
                    And Not Exists (Select PFPCH From T_FHHZ2 Where PFPCH = T.PFPCH)))
         Group By ywbmbh, DH";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);
						$sql_gc = "Insert Into t_yszk_month_tmp_clj
        (qmmy, qmsy, qmcbj, dh, ywbmbh, YWLX, QCRQ, QMRQ)
        Select Sum(zmy), Sum(zsy), Sum(NVL(cbj, 0)), DH, ywbmbh, 'YJS', '',''
          From t_fhhz2 t
         Where dbrq < Date '$end'
           And Exists (Select tslsh
                  From t_tshz
                 Where tslsh = t.tslsh
                   And jsrq >= Date '$end')
         Group By ywbmbh, DH";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);
						$sql_gc = "Insert Into t_yszk_month_tmp_clj
        (qmmy, qmsy, qmcbj, dh, ywbmbh, YWLX, QCRQ, QMRQ)
        Select
         Sum(Case
                 When djlx = 'YH' Then
                  0
                 Else
                  -T.ZMY
             End),
         Sum(-zsy), Sum(NVL(-cbj, 0)), DH, ywbmbh, 'TH1', '',''
          From t_KHthhz1 t
         Where thrq < Date '$end'
         Group By ywbmbh, DH";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);
						$sql_gc = "Insert Into t_yszk_month_tmp_clj
        (qmmy, qmsy, qmcbj, dh, ywbmbh, YWLX, QCRQ, QMRQ)
        Select
         Sum(Case
                 When djlx = 'YH' Then
                  0
                 Else
                  -T.ZMY
             End),
         Sum(-zsy), Sum(NVL(-cbj, 0)), DH, ywbmbh, 'TH2', '',''
          From t_KHthhz2 t
         Where thrq < Date '$end'
           And Exists (Select tslsh
                  From T_TSHZ
                 Where (JSrq >= Date '$end' Or JSRQ Is Null)
                   And tslsh = t.tslsh)
         Group By ywbmbh, DH";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);
						$sql_gc = "Insert Into t_yszk_month_tmp_clj
        (qcmy, qcsy, qccbj, dh, ywbmbh, YWLX, QCRQ, QMRQ)
        Select Sum(zmy), Sum(zsy), Sum(NVL(cbj, 0)), DH, ywbmbh, 'HZ1', '',''
          From t_fhhz1 t
         Where dbrq < Date '$start'
         Group By ywbmbh, DH";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);
						$sql_gc = "Insert Into t_yszk_month_tmp_clj
        (qcmy, qcsy, qccbj, dh, ywbmbh, YWLX, QCRQ, QMRQ)
        Select Sum(-zmy), Sum(-zsy), Sum(NVL(-cbj, 0)), DH, ywbmbh, 'FHHZ', '',''
          From t_fhhz t
         Where (cspc = '更正' And dbrq >= Date '$start' And Not Exists
                (Select pfpch From t_fhhz2 Where pfpch = t.pfpch) And Not Exists
                (Select pfpch From t_fhhz1 Where pfpch = t.pfpch))
           And (Exists
                (Select pfpch
                   From t_fhhz1
                  Where pfpch = t.yspfpch
                    And dbrq < Date '$start'
                    And Not Exists (Select PFPCH From T_FHHZ1 Where PFPCH = T.PFPCH)) Or
                Exists
                (Select pfpch
                   From t_fhhz2
                  Where pfpch = t.yspfpch
                    And dbrq < Date '$start'
                    And Not Exists (Select PFPCH From T_FHHZ2 Where PFPCH = T.PFPCH)))
         Group By ywbmbh, DH";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);
						
						$sql_gc = "Insert Into t_yszk_month_tmp_clj
        (qcmy, qcsy, qccbj, dh, ywbmbh, YWLX, QCRQ, QMRQ)
        Select Sum(zmy), Sum(zsy), Sum(NVL(cbj, 0)), DH, ywbmbh, 'YJS', '',''
          From t_fhhz2 t
         Where dbrq < Date '$start'
           And Exists (Select tslsh
                  From t_tshz
                 Where tslsh = t.tslsh
                   And jsrq >= Date '$start')
         Group By ywbmbh, DH";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);

						$sql_gc = "Insert Into t_yszk_month_tmp_clj
        (qcmy, qcsy, qccbj, dh, ywbmbh, YWLX, QCRQ, QMRQ)
        Select
         Sum(Case
                 When djlx = 'YH' Then
                  0
                 Else
                  -T.ZMY
             End), Sum(-zsy), Sum(NVL(-cbj, 0)), DH, ywbmbh, 'TH1', '',''
          From t_KHthhz1 t
         Where thrq < Date '$start'
         Group By ywbmbh, DH";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);
						
						$sql_gc = "Insert Into t_yszk_month_tmp_clj
        (qcmy, qcsy, qccbj, dh, ywbmbh, YWLX, QCRQ, QMRQ)
        Select
         Sum(Case
                 When djlx = 'YH' Then
                  0
                 Else
                  -T.ZMY
             End), Sum(-zsy), Sum(NVL(-cbj, 0)), DH, ywbmbh, 'TH2', '',''
          From t_KHthhz2 t
         Where thrq < Date '$start'
           And (tslsh Is Null Or Exists (Select tslsh
                                           From T_TSHZ
                                          Where (JSrq >= Date '$start' Or JSRQ Is Null)
                                            And tslsh = t.tslsh))
         Group By ywbmbh, DH";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);
						
						$sql_gc = "Insert Into t_ySzk_month_tmp_clj
        (bqjsmy, bqjssy, bqjscbj, DH, ywbmbh, YWLX, QCRQ, QMRQ)
        Select Sum(zmy), Sum(zsy), Sum(NVL(cbj, 0)), DH, ywbmbh, 'FH', '',''
          From T_FHHZ2 t
         Where DBRQ < Date '$end'
           And (Exists (Select tslsh
                          From T_TSHZ
                         Where JSRQ >= Date '$start'
                           And JSRQ < Date '$end'
                           And tslsh = t.tslsh))
         Group By ywbmbh, DH";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);
						
						$sql_gc = "Insert Into t_ySzk_month_tmp_clj
        (bqjsmy, bqjssy, bqjscbj, DH, ywbmbh, YWLX, QCRQ, QMRQ)
        Select
         Sum(Case
                 When djlx = 'YH' Then
                  0
                 Else
                  -T.ZMY
             End), Sum(-zsy), Sum(NVL(-cbj, 0)), DH, ywbmbh, 'TH2', '',''
          From T_KHTHHZ2 t
         Where THRQ < Date '$end'
           And (Exists (Select tslsh
                          From T_TSHZ
                         Where JSRQ >= Date '$start'
                           And JSRQ < Date '$end'
                           And tslsh = t.tslsh))
         Group By ywbmbh, DH";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);
						
						$sql_gc = "Insert Into t_ySzk_month_tmp_clj
        (BQZJmy, BQZJsy, BQZJcbj, DH, ywbmbh, YWLX, QCRQ, QMRQ)
        Select Sum(zmy), Sum(zsy), Sum(NVL(cbj, 0)), DH, ywbmbh, 'FH', '',''
          From T_FHHZ t
         Where DBRQ < Date '$end'
           And DBRQ >= Date '$start'
         Group By ywbmbh, DH";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);

						$sql_gc = "Insert Into t_ySzk_month_tmp_clj
        (BQZJmy, BQZJsy, BQZJcbj, DH, ywbmbh, YWLX, QCRQ, QMRQ)
        Select Sum(zmy), Sum(zsy), Sum(NVL(cbj, 0)), DH, ywbmbh, 'FH1', '',''
          From T_FHHZ1 t
         Where DBRQ < Date '$end'
           And DBRQ >= Date '$start'
           And Not Exists (Select 1 From t_fhhz Where pfpch = t.pfpch)
         Group By ywbmbh, DH";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);
						$sql_gc = "Insert Into t_ySzk_month_tmp_clj
        (BQZJmy, BQZJsy, BQZJcbj, DH, ywbmbh, YWLX, QCRQ, QMRQ)
        Select Sum(zmy), Sum(zsy), Sum(NVL(cbj, 0)), DH, ywbmbh, 'FH2', '',''
          From T_FHHZ2 t
         Where DBRQ < Date '$end'
           And DBRQ >= Date '$start'
           And Not Exists (Select 1 From t_fhhz Where pfpch = t.pfpch)
         Group By ywbmbh, DH";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);
						$sql_gc = "Insert Into t_ySzk_month_tmp_clj
        (BQZJmy, BQZJsy, BQZJcbj, DH, ywbmbh, YWLX, QCRQ, QMRQ)
        Select Sum(-zmy),
               Sum(-zsy), Sum(NVL(-cbj, 0)), DH, ywbmbh, 'TH', '',''
          From T_KHTHHZ t
         Where THRQ < Date '$end'
           And THRQ >= Date '$start'
         Group By ywbmbh, DH";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);

						$sql_gc = "Insert Into t_ySzk_month_tmp_clj
        (BQZJmy, BQZJsy, BQZJcbj, DH, ywbmbh, YWLX, QCRQ, QMRQ)
        Select
         0, Sum(-zsy), Sum(NVL(-cbj, 0)), DH, ywbmbh, 'TH', '',''
          From T_KHTHHZ1 t
         Where THRQ < Date '$end'
           And THRQ >= Date '$start'
           And (DJLX = 'YH' Or Not Exists (Select * From T_KHTHHZ Where THPCH = T.THPCH))
         Group By ywbmbh, DH";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);
						
						$sql_gc = "Insert Into t_ySzk_month_tmp_clj
        (BQZJmy, BQZJsy, BQZJcbj, DH, ywbmbh, YWLX, QCRQ, QMRQ)
        Select
         0, Sum(-zsy), Sum(NVL(-cbj, 0)), DH, ywbmbh, 'TH', '',''
          From T_KHTHHZ2 t
         Where THRQ < Date '$end'
           And THRQ >= Date '$start'
           And (DJLX = 'YH' Or Not Exists (Select * From T_KHTHHZ Where THPCH = T.THPCH))
         Group By ywbmbh, DH ";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);
						
						$sql_gc = "Insert Into t_ySzk_month_rysj_clj
        (YWBMBH, DH, QCMY, QCSY, BQZJMY, BQZJSY, BQJSMY, BQJSSY, QMMY, QMSY, QCRQ, QMRQ,
         TJNY, qccbj, bqzjcbj, bqjscbj, qmcbj)
        Select YWBMBH, DH, Sum(nvl(QCMY, 0)), Sum(nvl(QCSY, 0)), Sum(nvl(BQZJMY, 0)),
               Sum(nvl(BQZJSY, 0)), Sum(nvl(BQJSMY, 0)), Sum(nvl(BQJSSY, 0)),
               Sum(nvl(QMMY, 0)), Sum(nvl(QMSY, 0)), Date '$start', Date '$end', '200000',
               Sum(nvl(qccbj, 0)), Sum(nvl(bqzjcbj, 0)), Sum(nvl(bqjscbj, 0)),
               Sum(nvl(qmcbj, 0))
          From t_ySzk_month_tmp_clj
         Group By DH, YWBMBH";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);
						
						$guocheng[guocheng]=$timetype;	
						setcache("lsguocheng5",$guocheng);
					}			
					
			}	
					//$sql = "Select (Select dm From t_dm Where dh =a.ywbmbh) As ywbmbh,a.ghdwh,(Select mc From t_ghdw Where bh = a.ghdwh) As ghdwhmc,a.qcmy,a.qcsy,a.qccbj,a.bqzjmy,a.bqzjsy,a.bqzjcbj,a.bqjsmy,a.bqjssy,a.bqjscbj,a.qmmy,a.qmsy,a.qmcbj,to_char(a.qcrq, 'YYYY-MM-DD') as qcrq
							//,to_char(a.qmrq, 'YYYY-MM-DD') as qmrq From t_yszk_month_rysj_clj a where ywbmbh = '$admin_username'";
					$sql = "Select dh,(Select dm From t_dm Where dh =a.ywbmbh) As ywbmbh,a.qcmy,a.qcsy,a.qccbj,a.bqzjmy,a.bqzjsy,a.bqzjcbj,a.bqjsmy,a.bqjssy,a.bqjscbj,a.qmmy,a.qmsy,a.qmcbj,to_char(a.qcrq, 'YYYY-MM-DD') as qcrq
							,to_char(a.qmrq, 'YYYY-MM-DD') as qmrq From t_yszk_month_rysj_clj a where ywbmbh = '$admin_username'";
//'qcrq','qmrq','tjrq','tjny','dh','ywbmbh','qcmy','qcsy','bqzjmy','bqzjsy','bqjsmy','bqjssy','qmmy','qmsy','qccbj','bqzjcbj','bqjscbj','qmcbj'
					//echo $sql;//exit;
					$row=$ora->query($sql);
					//print_r($row);exit;
					
					if($daochu && $daochu=='daochu'){
						
						$fileName = '应收款月报';
						$total = count($row);
						$title = array('统计日期','统计年月','业务部门','客户名称','期初码洋','期初实洋','期初成本','本期增加码洋','本期增加实洋','本期增加成本','本期减少码洋','本期减少实洋','本期减少成本','期末码洋','期末实洋','期末成本');
						//$this->articleAccessLog($sql,$fileName,$title,$total,$yewugongsi);
						if($_SESSION['roleid']==26 && $daochulx =='导出到服务器指定位置'){
							$this->yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username,$timetype);
						}elseif($_SESSION['roleid']==26 && $daochulx =='从浏览器中下载'){
							$this->articleAccessLog($sql,$fileName,$title,$total,$yewugongsi);
						}
						//$this->yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username);
					}

				}elseif($yewuleixing=='应收款月报含税率'){ 
				$guocheng=getcache('lsguocheng6');
				
				if($_SESSION['roleid']==26){//过程
					if($guocheng['guocheng'] != $timetype ){

						$sql_gc = "Delete From t_yszk_month_tmp_clj";
						$res = $ora->query($sql_gc);
						$sql_gc = "Delete From t_yszk_month_rysj_clj_sl";
						$ora->query($sql_gc);
						$sql_gc = "Insert Into t_yszk_month_tmp_clj
        (qmmy, qmsy, qmcbj, dh, ywbmbh, YWLX, QCRQ, QMRQ, SL)
        Select Sum(zmy), Sum(zsy), Sum(NVL(cbj, 0)), DH, ywbmbh, 'FHZ1', '','', SL
          From t_fhhz1 t
         Where dbrq < Date '$end'
         Group By ywbmbh, DH, SL";
		 //echo $sql_gc;exit;
						$res = $ora->query($sql_gc);
						$sql_gc = "Insert Into t_yszk_month_tmp_clj
        (qmmy, qmsy, qmcbj, dh, ywbmbh, YWLX, QCRQ, QMRQ, SL)
        Select Sum(-zmy), Sum(-zsy), Sum(NVL(-cbj, 0)), DH, ywbmbh, 'FHGZ', '','', SL
          From t_fhhz t
         Where (cspc = '更正' And dbrq >= Date '$end' And 
               (nvl(zcs,0)<>0 Or nvl(zmy,0)<>0 Or nvl(zsy,0)<>0 Or nvl(cbj,0)<>0) And 
               Not Exists
                (Select pfpch From t_fhhz2 Where pfpch = t.pfpch) And Not Exists
                (Select pfpch From t_fhhz1 Where pfpch = t.pfpch))
           And (Exists
                (Select pfpch
                   From t_fhhz1
                  Where pfpch = t.yspfpch
                    And dbrq < Date '$end'
                    And Not Exists (Select PFPCH From T_FHHZ1 Where PFPCH = T.PFPCH)) Or
                Exists
                (Select pfpch
                   From t_fhhz2
                  Where pfpch = t.yspfpch
                    And dbrq < Date '$end'
                    And Not Exists (Select PFPCH From T_FHHZ2 Where PFPCH = T.PFPCH)))
         Group By ywbmbh, DH, SL";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);
						$sql_gc = "Insert Into t_yszk_month_tmp_clj
        (qmmy, qmsy, qmcbj, dh, ywbmbh, YWLX, QCRQ, QMRQ, SL)
        Select Sum(zmy), Sum(zsy), Sum(NVL(cbj, 0)), DH, ywbmbh, 'YJS', '','', SL
          From t_fhhz2 t
         Where dbrq < Date '$end'
           And Exists (Select tslsh
                  From t_tshz
                 Where tslsh = t.tslsh
                   And jsrq >= Date '$end')
         Group By ywbmbh, DH, SL";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);
						$sql_gc = "Insert Into t_yszk_month_tmp_clj
        (qmmy, qmsy, qmcbj, dh, ywbmbh, YWLX, QCRQ, QMRQ, SL)
        Select
         Sum(Case
                 When djlx = 'YH' Then
                  0
                 Else
                  -T.ZMY
             End),
         Sum(-zsy), Sum(NVL(-cbj, 0)), DH, ywbmbh, 'TH1', '','', SL
          From t_khthhz1 t
         Where thrq < Date '$end'
         Group By ywbmbh, DH, SL";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);
						$sql_gc = "Insert Into t_yszk_month_tmp_clj
        (qmmy, qmsy, qmcbj, dh, ywbmbh, YWLX, QCRQ, QMRQ, SL)
        Select 
         Sum(Case
                 When djlx = 'YH' Then
                  0
                 Else
                  -T.ZMY
             End),
         Sum(-zsy), Sum(NVL(-cbj, 0)), DH, ywbmbh, 'TH2', '','', SL
          From t_KHthhz2 t
         Where thrq < Date '$end'
           And Exists (Select tslsh
                  From T_TSHZ
                 Where (JSrq >= Date '$end' Or JSRQ Is Null)
                   And tslsh = t.tslsh)
         Group By ywbmbh, DH, SL";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);
						$sql_gc = "Insert Into t_yszk_month_tmp_clj
        (qcmy, qcsy, qccbj, dh, ywbmbh, YWLX, QCRQ, QMRQ, SL)
        Select Sum(zmy), Sum(zsy), Sum(NVL(cbj, 0)), DH, ywbmbh, 'HZ1', '','', SL
          From t_fhhz1 t
         Where dbrq < Date '$start'
         Group By ywbmbh, DH, SL";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);
						$sql_gc = "Insert Into t_yszk_month_tmp_clj
        (qcmy, qcsy, qccbj, dh, ywbmbh, YWLX, QCRQ, QMRQ, SL)
        Select Sum(-zmy), Sum(-zsy), Sum(NVL(-cbj, 0)), DH, ywbmbh, 'FHHZ', '','', SL
          From t_fhhz t
         Where (cspc = '更正' And dbrq >= Date '$start' And 
           (nvl(zcs,0)<>0 Or nvl(zmy,0)<>0 Or nvl(zsy,0)<>0 Or nvl(cbj,0)<>0) And
           Not Exists
                (Select pfpch From t_fhhz2 Where pfpch = t.pfpch) And Not Exists
                (Select pfpch From t_fhhz1 Where pfpch = t.pfpch))
           And (Exists
                (Select pfpch
                   From t_fhhz1
                  Where pfpch = t.yspfpch
                    And dbrq < Date '$start'
                    And Not Exists (Select PFPCH From T_FHHZ1 Where PFPCH = T.PFPCH)) Or
                Exists
                (Select pfpch
                   From t_fhhz2
                  Where pfpch = t.yspfpch
                    And dbrq < Date '$start'
                    And Not Exists (Select PFPCH From T_FHHZ2 Where PFPCH = T.PFPCH)))
         Group By ywbmbh, DH, SL";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);
						
						$sql_gc = "Insert Into t_yszk_month_tmp_clj
        (qcmy, qcsy, qccbj, dh, ywbmbh, YWLX, QCRQ, QMRQ, SL)
        Select Sum(zmy), Sum(zsy), Sum(NVL(cbj, 0)), DH, ywbmbh, 'YJS', '','', SL
          From t_fhhz2 t
         Where dbrq < Date '$start'
           And Exists (Select tslsh
                  From t_tshz
                 Where tslsh = t.tslsh
                   And jsrq >= Date '$start')
         Group By ywbmbh, DH, SL";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);

						$sql_gc = "Insert Into t_yszk_month_tmp_clj
        (qcmy, qcsy, qccbj, dh, ywbmbh, YWLX, QCRQ, QMRQ, SL)
        Select
         Sum(Case
                 When djlx = 'YH' Then
                  0
                 Else
                  -T.ZMY
             End), Sum(-zsy), Sum(NVL(-cbj, 0)), DH, ywbmbh, 'TH1', '','', SL
          From t_KHthhz1 t
         Where thrq < Date '$start'
         Group By ywbmbh, DH, SL";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);
						
						$sql_gc = " Insert Into t_yszk_month_tmp_clj
        (qcmy, qcsy, qccbj, dh, ywbmbh, YWLX, QCRQ, QMRQ, SL)
        Select
         Sum(Case
                 When djlx = 'YH' Then
                  0
                 Else
                  -T.ZMY
             End), Sum(-zsy), Sum(NVL(-cbj, 0)), DH, ywbmbh, 'TH2', '','', SL
          From t_KHthhz2 t
         Where thrq < Date '$start'
           And (tslsh Is Null Or Exists (Select tslsh
                                           From T_TSHZ
                                          Where (JSrq >= Date '$start' Or JSRQ Is Null)
                                            And tslsh = t.tslsh))
         Group By ywbmbh, DH, SL";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);
						
						$sql_gc = "Insert Into t_ySzk_month_tmp_clj
        (bqjsmy, bqjssy, bqjscbj, DH, ywbmbh, YWLX, QCRQ, QMRQ, SL)
        Select Sum(zmy), Sum(zsy), Sum(NVL(cbj, 0)), DH, ywbmbh, 'FH', '','', SL
          From T_FHHZ2 t
         Where DBRQ < Date '$end'
           And (Exists (Select tslsh
                          From T_TSHZ
                         Where JSRQ >= Date '$start'
                           And JSRQ < Date '$end'
                           And tslsh = t.tslsh))
         Group By ywbmbh, DH, SL";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);
						
						$sql_gc = "Insert Into t_ySzk_month_tmp_clj
        (bqjsmy, bqjssy, bqjscbj, DH, ywbmbh, YWLX, QCRQ, QMRQ, SL)
        Select
         Sum(Case
                 When djlx = 'YH' Then
                  0
                 Else
                  -T.ZMY
             End), Sum(-zsy), Sum(NVL(-cbj, 0)), DH, ywbmbh, 'TH2', '','', SL
          From T_KHTHHZ2 t
         Where THRQ < Date '$end'
           And (Exists (Select tslsh
                          From T_TSHZ
                         Where JSRQ >= Date '$start'
                           And JSRQ < Date '$end'
                           And tslsh = t.tslsh))
         Group By ywbmbh, DH, SL";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);
						
						$sql_gc = "Insert Into t_ySzk_month_tmp_clj
        (BQZJmy, BQZJsy, BQZJcbj, DH, ywbmbh, YWLX, QCRQ, QMRQ, SL)
        Select Sum(zmy), Sum(zsy), Sum(NVL(cbj, 0)), DH, ywbmbh, 'FH', '','', SL
          From T_FHHZ t
         Where DBRQ < Date '$end'
           And DBRQ >= Date '$start' And (nvl(zcs,0)<>0 Or nvl(zmy,0)<>0 Or nvl(zsy,0)<>0 Or nvl(cbj,0)<>0)
         Group By ywbmbh, DH, SL";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);

						$sql_gc = "Insert Into t_ySzk_month_tmp_clj
        (BQZJmy, BQZJsy, BQZJcbj, DH, ywbmbh, YWLX, QCRQ, QMRQ, SL)
        Select Sum(zmy), Sum(zsy), Sum(NVL(cbj, 0)), DH, ywbmbh, 'FH1', '','', SL
          From T_FHHZ1 t
         Where DBRQ < Date '$end'
           And DBRQ >= Date '$start'
           And Not Exists (Select 1 From t_fhhz Where pfpch = t.pfpch)
         Group By ywbmbh, DH, SL";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);
						$sql_gc = "Insert Into t_ySzk_month_tmp_clj
        (BQZJmy, BQZJsy, BQZJcbj, DH, ywbmbh, YWLX, QCRQ, QMRQ, SL)
        Select Sum(zmy), Sum(zsy), Sum(NVL(cbj, 0)), DH, ywbmbh, 'FH2', '','', SL
          From T_FHHZ2 t
         Where DBRQ < Date '$end'
           And DBRQ >= Date '$start'
           And Not Exists (Select 1 From t_fhhz Where pfpch = t.pfpch)
         Group By ywbmbh, DH, SL";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);
						$sql_gc = "Insert Into t_ySzk_month_tmp_clj
        (BQZJmy, BQZJsy, BQZJcbj, DH, ywbmbh, YWLX, QCRQ, QMRQ, SL)
        Select Sum(-zmy),
               Sum(-zsy), Sum(NVL(-cbj, 0)), DH, ywbmbh, 'TH', '','', SL
          From T_KHTHHZ t
         Where THRQ < Date '$end'
           And THRQ >= Date '$start'
         Group By ywbmbh, DH, SL";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);

						$sql_gc = "Insert Into t_ySzk_month_tmp_clj
        (BQZJmy, BQZJsy, BQZJcbj, DH, ywbmbh, YWLX, QCRQ, QMRQ, SL)
        Select
         0, Sum(-zsy), Sum(NVL(-cbj, 0)), DH, ywbmbh, 'TH', '','', SL
          From T_KHTHHZ1 t
         Where THRQ < Date '$end'
           And THRQ >= Date '$start'
          And (DJLX = 'YH' Or Not Exists (Select * From T_KHTHHZ Where THPCH = T.THPCH))
         Group By ywbmbh, DH, SL";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);
						
						$sql_gc = "Insert Into t_ySzk_month_tmp_clj
        (BQZJmy, BQZJsy, BQZJcbj, DH, ywbmbh, YWLX, QCRQ, QMRQ, SL)
        Select
         0, Sum(-zsy), Sum(NVL(-cbj, 0)), DH, ywbmbh, 'TH', '','', SL
          From T_KHTHHZ2 t
         Where THRQ < Date '$end'
           And THRQ >= Date '$start'
           And (DJLX = 'YH' Or Not Exists (Select * From T_KHTHHZ Where THPCH = T.THPCH))
         Group By ywbmbh, DH, SL";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);
						
						$sql_gc = "Insert Into t_ySzk_month_rysj_clj_SL
        (YWBMBH, DH, SL, QCMY, QCSY, BQZJMY, BQZJSY, BQJSMY, BQJSSY, QMMY, QMSY, QCRQ, QMRQ,
         TJNY, qccbj, bqzjcbj, bqjscbj, qmcbj)
        Select YWBMBH, DH, SL, Sum(nvl(QCMY, 0)), Sum(nvl(QCSY, 0)), Sum(nvl(BQZJMY, 0)),
               Sum(nvl(BQZJSY, 0)), Sum(nvl(BQJSMY, 0)), Sum(nvl(BQJSSY, 0)),
               Sum(nvl(QMMY, 0)), Sum(nvl(QMSY, 0)), Date '$start', Date '$end', '200000',
               Sum(nvl(qccbj, 0)), Sum(nvl(bqzjcbj, 0)), Sum(nvl(bqjscbj, 0)),
               Sum(nvl(qmcbj, 0))
          From t_ySzk_month_tmp_clj
         Group By DH, YWBMBH, SL";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);
					
						$guocheng[guocheng]=$timetype;	
						setcache("lsguocheng6",$guocheng);
					}			
					
			}	
					$sql = "Select dh, (Select dm From t_dm Where dh =t_yszk_month_rysj_clj_SL.ywbmbh) As ywbmbh, sl,qcmy, qcsy,qccbj, bqzjmy, bqzjsy, bqzjcbj, bqjsmy, bqjssy, qmmy, qmsy,qmcbj,
							 dqmy, dqsy, dqcbj, to_char(t_yszk_month_rysj_clj_SL.qcrq, 'YYYY-MM-DD') as qcrq
							,to_char(t_yszk_month_rysj_clj_SL.qmrq, 'YYYY-MM-DD') as qmrq From t_yszk_month_rysj_clj_SL where ywbmbh = '$admin_username'";
										
						$row=$ora->query($sql);
						//print_r($row);exit;

					if($daochu && $daochu=='daochu'){
						
						$fileName = '应收款月报含税率';
						$total = count($row);
						$title = array('统计日期','统计年月','客户名称','业务部门','税率%','期初码洋','期初实洋','期初成本','本期增加码洋','本期增加实洋','本期增加成本','本期减少码洋',
						'本期减少实洋','期末码洋','期末实洋','期末成本','当前码洋','当前实洋','当前成本');
						if($_SESSION['roleid']==26 && $daochulx =='导出到服务器指定位置'){
							$this->yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username,$timetype);
						}elseif($_SESSION['roleid']==26 && $daochulx =='从浏览器中下载'){
							$this->articleAccessLog($sql,$fileName,$title,$total,$yewugongsi);
						}
					}

				}elseif($yewuleixing=='本期流水'){
					$sql="Select Sum(pkmy) 损益码洋, Sum(pksy)损益实样, Sum(bfmy) 报废码洋, Sum(bfsy) 报废实样, Sum(dhmy - thmy) 纯到货码洋, Sum(dhsy - thsy)纯到货实样,
		   Sum(fhmy - ftmy)纯发货码洋, Sum(fhcbj - thcbj)纯发货实洋
	  From t_Tscw_Pzjxc_day
	 Where ywbmbh = '$admin_username'
		And rq >= Date '$start'
	   And rq < Date '$end'"; 

					//echo $sql;exit;
						$row=$ora->query($sql);
						//print_r($row);exit;
						
					if($daochu && $daochu=='daochu'){
						
						$fileName = '本期流水';
						$total = count($row);
						$title = array('损益码洋','损益实样','报废码洋','报废实样','纯到货码洋','纯到货实样','纯发货码洋','纯发货实洋');
						if($_SESSION['roleid']==26 && $daochulx =='导出到服务器指定位置'){
							$this->yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username,$timetype);
						}elseif($_SESSION['roleid']==26 && $daochulx =='从浏览器中下载'){
							$this->articleAccessLog($sql,$fileName,$title,$total,$yewugongsi);
						}
					}
					
				}elseif($yewuleixing=='汇总数据'){
					
				$guocheng=getcache('lsguocheng7');
				
				if($_SESSION['roleid']==26){//过程
					if($guocheng['guocheng'] != $timetype ){

						$sql_gc = "Delete From t_month_kchd";
						$res = $ora->query($sql_gc);
						$sql_gc = "Insert Into t_month_kchd
				(qcrq, qmrq, ywbmbh, zmy, zsy, cbj, ywlx，BJ)
				Select Date '$start' As qcrq, Date '$end' As qmrq, ywbmbh, -sum(ZMY) As zmy,
							 -Sum(zsy) As zsy, -Sum(NVL(cbj, 0)) As cbj, '发货' As ywlx， '1'
					From t_fhhz t
				 Where dbrq >= Date '$start'
					 And dbrq < Date '$end'
				 Group By ywbmbh";
						$ora->query($sql_gc);
						$sql_gc = "Insert Into t_month_kchd
				(qcrq, qmrq, ywbmbh, zmy, zsy, cbj, ywlx, BJ)
				Select Date '$start' As qcrq, Date '$end' As qmrq, ywbmbh, Sum(ZMY) As zmy,
							 Sum(zsy) As zsy, Sum(NVL(ZSY, 0)) As cbj, '到货' As ywlx， '1'
					From t_dhdj t
				 Where DBrq >= Date '$start'
					 And DBrq < Date '$end'
				 Group By ywbmbh";
		 //echo $sql_gc;exit;
						$res = $ora->query($sql_gc);
						$sql_gc = "Insert Into t_month_kchd
				(qcrq, qmrq, ywbmbh, zmy, zsy, cbj, ywlx, BJ)
				Select Date '$start' As qcrq, Date '$end' As qmrq, ywbmbh, -sum(ZMY) As zmy,
							 -Sum(zsy) As zsy, -Sum(NVL(ZSY, 0)) As cbj, '退货' As ywlx， '1'
					From t_hythhz t
				 Where thrq >= Date '$start'
					 And thrq < Date '$end'
				 Group By ywbmbh";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);
						$sql_gc = "Insert Into t_month_kchd
				(qcrq, qmrq, ywbmbh, zmy, zsy, cbj, ywlx, BJ)
				Select Date '$start' As qcrq, Date '$end' As qmrq, ywbmbh, Sum(ZMY) As zmy,
							 Sum(zsy) As zsy, Sum(NVL(cbj, 0)) As cbj, '销退' As ywlx， '1'
					From T_KHTHHZ t
				 Where thrq >= Date '$start'
					 And thrq < Date '$end'
				 Group By ywbmbh";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);
						$sql_gc = "Insert Into t_month_kchd
				(qcrq, qmrq, ywbmbh, zmy, zsy, cbj, ywlx，BJ)
				Select Date '$start' As qcrq, Date '$end' As qmrq, ywbmbh, Sum(QCMY) As zmy,
							 Sum(QCSY) As zsy, Sum(NVL(QCSY, 0)) As cbj, '初期' As ywlx， '1'
				
					From T_TSCW_PZJXC_MONTH
				 Where TJNY = TO_CHAR(Date '$start', 'YYYYMM')
				 Group By ywbmbh";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);
						$sql_gc = "Insert Into t_month_kchd
				(qcrq, qmrq, ywbmbh, zmy, zsy, cbj, ywlx，BJ)
				Select Date '$start' As qcrq, Date '$end' As qmrq, ywbmbh, -Sum(QmMY) As zmy,
							 -Sum(QmSY) As zsy, -Sum(NVL(QmSY, 0)) As cbj, '期末' As ywlx， '1'
					From T_TSCW_PZJXC_MONTH
				 Where TJNY = TO_CHAR(Date '$end' - 2, 'YYYYMM')
				
				 Group By ywbmbh";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);
						$sql_gc = "Insert Into t_month_kchd
				(qcrq, qmrq, ywbmbh, zmy, zsy, cbj, ywlx, BJ)
				Select Date '$start' As qcrq, Date '$end' As qmrq, ywbmbh, Sum(ZMY) As zmy,
							 Sum(zsy) As zsy, Sum(NVL(cbj, 0)) As cbj, '发货优惠-未结' As ywlx， '0'
					From T_KHTHHZ1 t
				 Where thrq >= Date '$start'
					 And thrq < Date '$end'
					 And DJlx = 'YH'
				 Group By ywbmbh";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);
						$sql_gc = "Insert Into t_month_kchd
				(qcrq, qmrq, ywbmbh, zmy, zsy, cbj, ywlx, BJ)
				Select Date '$start' As qcrq, Date '$end' As qmrq, ywbmbh, Sum(ZMY) As zmy,
							 Sum(zsy) As zsy, Sum(NVL(cbj, 0)) As cbj, '发货优惠-已结' As ywlx， '0'
					From T_KHTHHZ2 t
				 Where thrq >= Date '$start'
					 And thrq < Date '$end'
					 And DJlx = 'YH'
				 Group By ywbmbh";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);
						
						$sql_gc = "Insert Into t_month_kchd
				(qcrq, qmrq, ywbmbh, zmy, zsy, cbj, ywlx, BJ)
				Select Date '$start' As qcrq, Date '$end' As qmrq, ywbmbh, 0, Sum(-zsy), Sum(-zsy),
							 '到货优惠', '0'
					From t_Jjyhdkhz t
				 Where djrq >= Date '$start'
					 And djrq < Date '$end'
				 Group By ywbmbh";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);

						$sql_gc = "Insert Into t_month_kchd
				(qcrq, qmrq, ywbmbh, zmy, zsy, cbj, ywlx, BJ)
				Select Date '$start' As qcrq, Date '$end' As qmrq, ywbmbh, Sum(ZMY) As zmy,
							 Sum(zsy) As zsy, Sum(NVL(ZSY, 0)) As cbj, '到货补录' As ywlx， '0'
					From t_bldhdj t
				 Where DBrq >= Date '$start'
					 And DBrq < Date '$end'
				 Group By ywbmbh";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);
						
						$sql_gc = "Insert Into t_month_kchd
				(qcrq, qmrq, ywbmbh, zmy, zsy, cbj, ywlx，BJ)
				Select Date '$start' As qcrq, Date '$end' As qmrq, ywbmbh, -sum(ZMY) As zmy,
							 -Sum(zsy) As zsy, -Sum(NVL(cbj, 0)) As cbj, '发货补录-未结' As ywlx， '0'
					From t_fhhz1 t
				 Where dbrq >= Date '$start'
					 And dbrq < Date '$end'
					 And Not Exists (Select 1
									From t_fhhz
								 Where pfpch = t.pfpch
									 And ywbmbh = t.ywbmbh)
				 Group By ywbmbh";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);
						
						$sql_gc = "Insert Into t_month_kchd
				(qcrq, qmrq, ywbmbh, zmy, zsy, cbj, ywlx，BJ)
				Select Date '$start' As qcrq, Date '$end' As qmrq, ywbmbh, 0 As zmy, Sum(cbj) As zsy,
							 Sum(cbj) As cbj, '更正差异', '1'
					From t_xsls_ghdw_gz
				 Where xsrq >= Date '$start'
					 And xslx = '2'
					 And xsrq < Date '$end'
				 Group By ywbmbh";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);
						
						$sql_gc = "Insert Into t_month_kchd
				(qcrq, qmrq, ywbmbh, zmy, zsy, cbj, ywlx，BJ)
				Select Date '$start' As qcrq, Date '$end' As qmrq, ywbmbh, -sum(ZMY) As zmy,
							 -Sum(zsy) As zsy, -Sum(NVL(cbj, 0)) As cbj, '发货补录-已结' As ywlx， '0'
					From t_fhhz2 t
				 Where dbrq >= Date '$start'
					 And dbrq < Date '$end'
					 And Not Exists (Select 1
									From t_fhhz
								 Where pfpch = t.pfpch
									 And ywbmbh = t.ywbmbh)
				 Group By ywbmbh";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);
					
						$guocheng[guocheng]=$timetype;	
						setcache("lsguocheng7",$guocheng);
					}			
					
			}	
					
					$sql="Select to_char(t_month_kchd.qcrq, 'YYYY-MM-DD') as 期初日期, to_char(t_month_kchd.qmrq, 'YYYY-MM-DD') as  期末日期, ywbmbh 业务部门编号,zmy 总码洋,zsy 总实样,cbj 成本价,ywlx 类型 From  t_month_kchd Where ywbmbh = '$admin_username' And bj = '1'"; 
	   
					//echo $sql;exit;
					$row=$ora->query($sql);
					//print_r($row);exit;
						
					if($daochu && $daochu=='daochu'){
						
						$fileName = '汇总数据';
						$total = count($row);
						$title = array('期初日期','期末日期','业务部门编号','总码洋','总实样','成本价','类型');
						if($_SESSION['roleid']==26 && $daochulx =='导出到服务器指定位置'){
							$this->yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username,$timetype);
						}elseif($_SESSION['roleid']==26 && $daochulx =='从浏览器中下载'){
							$this->articleAccessLog($sql,$fileName,$title,$total,$yewugongsi);
						}
					}
					
				}
				
				
				
			}
			

		}
		
		/*
		*$role_jc 教材教辅部门角色 24
		*$role_ls 连锁部门角色 23
		*$user_jc 教材教辅部门 24
		*$user_ls 连锁部门 23
		*$_SESSION['roleid']==26 这个是后台角色中导出总管理员。作用：手动导出到浏览器，或者服务器指定位置。
		*/
		$role_jc = 24;
		$role_ls = 23;
		$user_jc = $this->db->select(array('roleid'=>$role_jc), "username,realname",6, 'userid asc');//查找教材教辅的成员
		//print_r($user_jc);exit;
		$user_jc_bh = array();$user_jc_mc = array();
		foreach($user_jc as $v){
			$user_jc_bh[] = $v['username'];
			$user_jc_mc[] = $v['realname'];		
		}
		$user_ls = $this->db->select(array('roleid'=>$role_ls), "username,realname",15, 'userid asc');//查找连锁的成员
		$user_ls_bh = array();$user_ls_mc = array();
		foreach($user_ls as $v){
			$user_ls_bh[] = $v['username'];	
			$user_ls_mc[] = $v['realname'];
			$userls[$v['username']] = $v['realname'];  //$userls[$admin_username];
		}
		$nianfen = array('请选择年份','2011','2012','2013','2014','2015','2016','2017','2018','2019','2020','2021','2022','2023','2024','2025','2026','2027','2028');
		$month = array('请选择月份','一月份','二月份','三月份','四月份','五月份','六月份','七月份','八月份','九月份','十月份','十一月份','十二月份');
		$jidu = array('请选择季度','一季度','二季度','三季度','四季度'); //季度
		$niandu = array('请选择年度','1-6','7-12','1-12'); //年度
		$daochulx = array('从浏览器中下载','导出到服务器指定位置'); //导出类型。
		if($op=='jiaofu'){
			if(in_array($admin_username, $user_jc_bh) || $_SESSION['roleid']==26){
				include $this->admin_tpl('show_daochu_jiaofu');	
			}else{
				showmessage('您只能访问连锁的业务！',HTTP_REFERER);
			}
		}
		if($op=='liansuo'){
			if(in_array($admin_username, $user_ls_bh) || $_SESSION['roleid']==26){
				include $this->admin_tpl('show_daochu');	
			}else{
				showmessage('您只能访问教材教辅的业务！',HTTP_REFERER);
			}
			
		}
		
		if($op=='check_file'){
			//header("Content-type: text/html; charset=utf-8"); 
			//echo $_REQUEST['yewugongsi'];exit;
			$filepath = isset($_REQUEST['filepath'])?$_REQUEST['filepath']:'';
			$filepath = iconv('UTF-8','GBK',$filepath);
			//echo $filepath;exit;
			$yewugongsi = $_REQUEST['yewugongsi'];
			if(!$filepath){
				return 0;//非法操作
			}else{
				$path = PHPCMS_PATH.'uploadfile/csv/' ;  // D:\wamp\www2\uploadfile/csv/
				$year = date('Y');
				$m = date('m')-1;
				if($m<=9){
					$riqi = $year.'0'.$m;	
				}
				
				if($yewugongsi==1){
					$path .= 'jf';
					$a = 'jf';
				}elseif($yewugongsi==2){
					$path .= 'jc';
					$a = 'jc';
				}elseif($yewugongsi==3){
					$path .= 'ls';
					$a = 'ls';
				}
				$path_file =$path.'/'.$year.'/'.'Y'.$year.'/'.$riqi.'/'.$filepath;
				//echo $path_file;exit;
				if(!file_exists($path_file)) {
					echo 1; //此文件不存在返回值为1
				}else{
					$url = $_SERVER['SERVER_NAME'].':8081/uploadfile/csv/'.$a.'/'.$year.'/'.'Y'.$year.'/'.$riqi.'/';
					echo $url;
				}

				
			}

			
		}
		
		
	}
	
	/*
	*  创建时间：2018-07-25 17:25
	*  作者：张三丰
	*/

	public function articleAccessLog($sql,$fileName,$title,$total,$yewugongsi)   //需要 sql语句  表名 ，导出表的名称 ，设置标题$title  $total 数据总条数
	{
		//set_time_limit(0);
		//ini_set('memory_limit', '1280M');
		set_time_limit(0);
		ini_set('memory_limit', '-1');
		ini_set("max_execution_time", "0");
		$host='172.30.153.63';
		$ip='172.30.153.63/xhsddb';
		$port='1521';
		$user= 'dbjczc';
		$pass= 'dbjczc';
		$charset='utf8';
		pc_base::load_app_class('oracle_admin','admin',0);//数据库
		$ora=new oracle_admin($user,$pass,$ip,$charset);
		if($yewugongsi==3){
			$ip='172.30.153.63/xhsddb';
			$port='1521';
			$user= 'dbsl';
			$pass= 'dbsl';
			$charset='utf8';
			pc_base::load_app_class('oracle_admin','admin',0);//数据库
			$ora=new oracle_admin($user,$pass,$ip,$charset);
		}

		//pc_base::load_app_class('oracle','member',0);//数据库
	    //$ora=new oracle('dbjczc','dbjczc','172.30.153.63/xhsddb','utf8');
		//$total = $ora->getcount('select * from t_tm_info');
		$total = $total;
		//$fileName = date('YmdHi', time());
		$fileName = $fileName;
		header('Content-Type: application/vnd.ms-execl');
		header('Content-Disposition: attachment;filename="' . $fileName . '.csv"');
		 
		$begin = microtime(true);
		 
		//打开php标准输出流
		//以写入追加的方式打开
		$fp = fopen('php://output', 'a');
		
		//2018-08-02 edit by zhang  
		//$filepath=PHPCMS_PATH."uploadfile/csv/".$fileName.'.csv';
		//echo $filepath;exit;
		//$fp = fopen($filepath,"a"); //打开csv文件，如果不存在则创建
		 
		//我们每次取1万条数据，分100步来执行
		//如果线上环境无法支持一次性读取1万条数据，可把$nums调小，$step相应增大。
		if($fileName=='库存明细' || $fileName=='应付款-采购未结-明细' || $fileName=='应收款-发出未结-明细'){
			$nums = 20000 ;	
		}else{
			$nums = 5000 ;	
		}
		
		$step = ceil($total / $nums);
		//$step=10;
		//设置标题
		//$title = array('发运计划批次', '发运批次','运号','收货店','发货部门','业务部门','中转部门','发运计划日期','发运日期','收货日期','总件数','重量','承运人','状态','业务类型','车号','微信昵称','签收地','图片','异常','异常处理');
		$title = $title ;
		foreach($title as $key => $item) {
			$title[$key] = iconv('UTF-8', 'GBK', $item);
		}
		//将标题写到标准输出中
		fputcsv($fp, $title);
		 
		for($s = 1; $s <= $step; ++$s) {
			  $sql= $sql;
			  $row1 = $ora->getpage($sql,$total,$s,$nums);
				if($row1) {
                    $row2=array();				
					foreach($row1 as $k => $v) {
						
					if($yewugongsi==1){ //教辅
						if($fileName == '教辅月报'){
							$row2['YWBMBH'] = iconv('UTF-8', 'GBK', $v['YWBMBH']);
							$row2['LX'] = iconv('UTF-8', 'GBK', $v['LX']);
							$row2['BH'] = iconv('UTF-8', 'GBK', $v['BH']);
							$row2['DMMC'] = iconv('UTF-8', 'GBK', $v['DMMC']);
							$row2['QCWJMY'] = iconv('UTF-8', 'GBK', $v['QCWJMY']);
							$row2['QCWJSY'] = iconv('UTF-8', 'GBK', $v['QCWJSY']);
							$row2['QCWJCB'] = iconv('UTF-8', 'GBK', $v['QCWJCB']);
							$row2['JFMY'] = iconv('UTF-8', 'GBK', $v['JFMY']);
							$row2['JFSY'] = iconv('UTF-8', 'GBK', $v['JFSY']);
							$row2['JFCB'] = iconv('UTF-8', 'GBK', $v['JFCB']);
							$row2['JSMY'] = iconv('UTF-8', 'GBK', $v['JSMY']);
							$row2['JSSY'] = iconv('UTF-8', 'GBK', $v['JSSY']);
							$row2['JSCB'] = iconv('UTF-8', 'GBK', $v['JSCB']);
							$row2['QMWJMY'] = iconv('UTF-8', 'GBK', $v['QMWJMY']);
							$row2['QMWJSY'] = iconv('UTF-8', 'GBK', $v['QMWJSY']);
							$row2['QMWJCB'] = iconv('UTF-8', 'GBK', $v['QMWJCB']);
							$row2['SYLX'] = iconv('UTF-8', 'GBK', $v['SYLX']);
							$row2['BB'] = iconv('UTF-8', 'GBK', $v['BB']);
							$row2['MINDATE'] = iconv('UTF-8', 'GBK', $v['MINDATE']);
							$row2['MAXDATE'] = iconv('UTF-8', 'GBK', $v['MAXDATE']);
							$row2['NY'] = iconv('UTF-8', 'GBK', $v['NY']);
							$row2['BJSMY'] = iconv('UTF-8', 'GBK', $v['BJSMY']);
							$row2['BJSSY'] = iconv('UTF-8', 'GBK', $v['BJSSY']);

						}
						if($fileName == '教辅到货明细'){
							$row2['YWBMBH'] = iconv('UTF-8', 'GBK', $v['YWBMBH']);
							$row2['GHDWH'] = iconv('UTF-8', 'GBK', $v['GHDWH']);
							$row2['MC'] = iconv('UTF-8', 'GBK', $v['MC']);
							$row2['DHPCH'] = iconv('UTF-8', 'GBK', $v['DHPCH']);
							$row2['SHRQ'] = iconv('UTF-8', 'GBK', $v['SHRQ']);
							$row2['YSDJ'] = iconv('UTF-8', 'GBK', $v['YSDJ']);
							$row2['SSDH'] = iconv('UTF-8', 'GBK', $v['SSDH']);
							$row2['ZDQH'] = iconv('UTF-8', 'GBK', $v['ZDQH']);
							$row2['ZDXH'] = iconv('UTF-8', 'GBK', $v['ZDXH']);
							$row2['SM'] = iconv('UTF-8', 'GBK', $v['SM']);
							$row2['DJ'] = iconv('UTF-8', 'GBK', $v['DJ']);
							$row2['JXSSSL'] = iconv('UTF-8', 'GBK', $v['JXSSSL']);
							$row2['MY'] = iconv('UTF-8', 'GBK', $v['MY']);
							$row2['SYS'] = iconv('UTF-8', 'GBK', $v['SYS']);
							$row2['ZDPCH'] = iconv('UTF-8', 'GBK', $v['ZDPCH']);
							$row2['JSRQ'] = iconv('UTF-8', 'GBK', $v['JSRQ']);
							$row2['JSLX'] = iconv('UTF-8', 'GBK', $v['JSLX']);
							$row2['ZDPCH1'] = iconv('UTF-8', 'GBK', $v['ZDPCH1']);
							$row2['TSLSH'] = iconv('UTF-8', 'GBK', $v['TSLSH']);
							$row2['YSRQ'] = iconv('UTF-8', 'GBK', $v['YSRQ']);
							$row2['YSDBRQ'] = iconv('UTF-8', 'GBK', $v['YSDBRQ']);
							$row2['JCDRQ'] = iconv('UTF-8', 'GBK', $v['JCDRQ']);
							$row2['KQBJ'] = iconv('UTF-8', 'GBK', $v['KQBJ']);
							$row2['JSGHDWH'] = iconv('UTF-8', 'GBK', $v['JSGHDWH']);
							$row2['YSGHDWH'] = iconv('UTF-8', 'GBK', $v['YSGHDWH']);
							$row2['FLOWID_DHMX'] = iconv('UTF-8', 'GBK', $v['FLOWID_DHMX']);

						}
						
						
						if($fileName == '教辅进退明细'){
							$row2['YWBMBH'] = iconv('UTF-8', 'GBK', $v['YWBMBH']);
							$row2['GHDWH'] = iconv('UTF-8', 'GBK', $v['GHDWH']);
							$row2['MC'] = iconv('UTF-8', 'GBK', $v['MC']);
							$row2['DBRQ'] = iconv('UTF-8', 'GBK', $v['DBRQ']);
							$row2['ID'] = iconv('UTF-8', 'GBK', $v['ID']);
							$row2['SM'] = iconv('UTF-8', 'GBK', $v['SM']);
							$row2['DJ'] = iconv('UTF-8', 'GBK', $v['DJ']);
							$row2['ZMY'] = iconv('UTF-8', 'GBK', $v['ZMY']);
							$row2['ZSY'] = iconv('UTF-8', 'GBK', $v['ZSY']);
							$row2['BZ'] = iconv('UTF-8', 'GBK', $v['BZ']);
							$row2['YWPCH'] = iconv('UTF-8', 'GBK', $v['YWPCH']);
							$row2['ZDPCH'] = iconv('UTF-8', 'GBK', $v['ZDPCH']);
							$row2['JSRQ'] = iconv('UTF-8', 'GBK', $v['JSRQ']);
							$row2['JSLX'] = iconv('UTF-8', 'GBK', $v['JSLX']);
							$row2['ZDQH'] = iconv('UTF-8', 'GBK', $v['ZDQH']);
							$row2['ZDXH'] = iconv('UTF-8', 'GBK', $v['ZDXH']);
							$row2['ZDPCH1'] = iconv('UTF-8', 'GBK', $v['ZDPCH1']);
							$row2['TSLSH'] = iconv('UTF-8', 'GBK', $v['TSLSH']);
							$row2['NF'] = iconv('UTF-8', 'GBK', $v['NF']);
							$row2['NFHRQ'] = iconv('UTF-8', 'GBK', $v['NFHRQ']);
							$row2['NJSRQ'] = iconv('UTF-8', 'GBK', $v['NJSRQ']);
							$row2['JSGHDWH'] = iconv('UTF-8', 'GBK', $v['JSGHDWH']);
							$row2['YSGHDWH'] = iconv('UTF-8', 'GBK', $v['YSGHDWH']);
							$row2['ZCS'] = iconv('UTF-8', 'GBK', $v['ZCS']);

						}

						if($fileName == '教辅出版社免费让利明细'){
							$row2['YWBMBH'] = iconv('UTF-8', 'GBK', $v['YWBMBH']);
							$row2['GHDWH'] = iconv('UTF-8', 'GBK', $v['GHDWH']);
							$row2['MC'] = iconv('UTF-8', 'GBK', $v['MC']);
							$row2['DHPCH'] = iconv('UTF-8', 'GBK', $v['DHPCH']);
							$row2['SHRQ'] = iconv('UTF-8', 'GBK', $v['SHRQ']);
							$row2['YSDJ'] = iconv('UTF-8', 'GBK', $v['YSDJ']);
							$row2['SSDH'] = iconv('UTF-8', 'GBK', $v['SSDH']);
							$row2['ZDQH'] = iconv('UTF-8', 'GBK', $v['ZDQH']);
							$row2['ZDXH'] = iconv('UTF-8', 'GBK', $v['ZDXH']);
							$row2['SM'] = iconv('UTF-8', 'GBK', $v['SM']);
							$row2['DJ'] = iconv('UTF-8', 'GBK', $v['DJ']);
							$row2['JXSSSL'] = iconv('UTF-8', 'GBK', $v['JXSSSL']);
							$row2['MY'] = iconv('UTF-8', 'GBK', $v['MY']);
							$row2['SYS'] = iconv('UTF-8', 'GBK', $v['SYS']);
							$row2['ZDPCH'] = iconv('UTF-8', 'GBK', $v['ZDPCH']);
							$row2['JSRQ'] = iconv('UTF-8', 'GBK', $v['JSRQ']);
							$row2['JSLX'] = iconv('UTF-8', 'GBK', $v['JSLX']);
							$row2['JSGHDWH'] = iconv('UTF-8', 'GBK', $v['JSGHDWH']);
							$row2['YSGHDWH'] = iconv('UTF-8', 'GBK', $v['YSGHDWH']);
							$row2['TSLSH'] = iconv('UTF-8', 'GBK', $v['TSLSH']);

						}
						
						if($fileName == '教辅销退明细'){
							$row2['GHDW'] = iconv('UTF-8', 'GBK', $v['GHDW']);
							$row2['GHDWMC'] = iconv('UTF-8', 'GBK', $v['GHDWMC']);
							$row2['YWBMBH'] = iconv('UTF-8', 'GBK', $v['YWBMBH']);
							$row2['KFBH'] = iconv('UTF-8', 'GBK', $v['KFBH']);
							$row2['DH'] = iconv('UTF-8', 'GBK', $v['DH']);
							$row2['DM'] = iconv('UTF-8', 'GBK', $v['DM']);
							$row2['PCH'] = iconv('UTF-8', 'GBK', $v['PCH']);
							$row2['THRQ'] = iconv('UTF-8', 'GBK', $v['THRQ']);
							$row2['ZMY'] = iconv('UTF-8', 'GBK', $v['ZMY']);
							$row2['ZSY'] = iconv('UTF-8', 'GBK', $v['ZSY']);
							$row2['ZCB'] = iconv('UTF-8', 'GBK', $v['ZCB']);
							$row2['TSLSH'] = iconv('UTF-8', 'GBK', $v['TSLSH']);
							$row2['JSRQ'] = iconv('UTF-8', 'GBK', $v['JSRQ']);
							$row2['JSLX'] = iconv('UTF-8', 'GBK', $v['JSLX']);
							$row2['ZDQH'] = iconv('UTF-8', 'GBK', $v['ZDQH']);
							$row2['NF'] = iconv('UTF-8', 'GBK', $v['NF']);
							$row2['NFHRQ'] = iconv('UTF-8', 'GBK', $v['NFHRQ']);
							$row2['NJSRQ'] = iconv('UTF-8', 'GBK', $v['NJSRQ']);
							$row2['JB'] = iconv('UTF-8', 'GBK', $v['JB']);
							$row2['CWQRPC'] = iconv('UTF-8', 'GBK', $v['CWQRPC']);
							$row2['ZCS'] = iconv('UTF-8', 'GBK', $v['ZCS']);
							$row2['DJ'] = iconv('UTF-8', 'GBK', $v['DJ']);
							$row2['ID'] = iconv('UTF-8', 'GBK', $v['ID']);
							$row2['BB'] = iconv('UTF-8', 'GBK', $v['BB']);
							$row2['SM'] = iconv('UTF-8', 'GBK', $v['SM']);
							$row2['ISBN'] = iconv('UTF-8', 'GBK', $v['ISBN']);
							$row2['CBNY'] = iconv('UTF-8', 'GBK', $v['CBNY']);
						}

						if($fileName == '教辅基层店免费让利明细'){
							$row2['YWBMBH'] = iconv('UTF-8', 'GBK', $v['YWBMBH']);
							$row2['KFBH'] = iconv('UTF-8', 'GBK', $v['KFBH']);
							$row2['DH'] = iconv('UTF-8', 'GBK', $v['DH']);
							$row2['DM'] = iconv('UTF-8', 'GBK', $v['DM']);
							$row2['GZPCH'] = iconv('UTF-8', 'GBK', $v['GZPCH']);
							$row2['GZRQ'] = iconv('UTF-8', 'GBK', $v['GZRQ']);
							$row2['ZMY'] = iconv('UTF-8', 'GBK', $v['ZMY']);
							$row2['ZSY'] = iconv('UTF-8', 'GBK', $v['ZSY']);
							$row2['ZCB'] = iconv('UTF-8', 'GBK', $v['ZCB']);
							$row2['TSLSH'] = iconv('UTF-8', 'GBK', $v['TSLSH']);
							$row2['JSRQ'] = iconv('UTF-8', 'GBK', $v['JSRQ']);
							$row2['BZ'] = iconv('UTF-8', 'GBK', $v['BZ']);
							$row2['JSLX'] = iconv('UTF-8', 'GBK', $v['JSLX']);
							$row2['ZDQH'] = iconv('UTF-8', 'GBK', $v['ZDQH']);
							$row2['NF'] = iconv('UTF-8', 'GBK', $v['NF']);
							$row2['NFHRQ'] = iconv('UTF-8', 'GBK', $v['NFHRQ']);
							$row2['NJSRQ'] = iconv('UTF-8', 'GBK', $v['NJSRQ']);
							$row2['JB'] = iconv('UTF-8', 'GBK', $v['JB']);
							$row2['ZCS'] = iconv('UTF-8', 'GBK', $v['ZCS']);

						}
						
						if($fileName == '教辅发货明细'){
							$row2['CBNY'] = iconv('UTF-8', 'GBK', $v['CBNY']);
							$row2['GHDW'] = iconv('UTF-8', 'GBK', $v['GHDW']);
							$row2['GHDWMC'] = iconv('UTF-8', 'GBK', $v['GHDWMC']);
							$row2['YWBMBH'] = iconv('UTF-8', 'GBK', $v['YWBMBH']);
							$row2['DH'] = iconv('UTF-8', 'GBK', $v['DH']);
							$row2['MC'] = iconv('UTF-8', 'GBK', $v['MC']);
							$row2['PFPCH'] = iconv('UTF-8', 'GBK', $v['PFPCH']);
							$row2['BZ'] = iconv('UTF-8', 'GBK', $v['BZ']);
							$row2['DBRQ'] = iconv('UTF-8', 'GBK', $v['DBRQ']);
							$row2['PFRQ'] = iconv('UTF-8', 'GBK', $v['PFRQ']);
							$row2['ZMY'] = iconv('UTF-8', 'GBK', $v['ZMY']);
							$row2['ZSY'] = iconv('UTF-8', 'GBK', $v['ZSY']);
							$row2['CBJ'] = iconv('UTF-8', 'GBK', $v['CBJ']);
							$row2['TSLSH'] = iconv('UTF-8', 'GBK', $v['TSLSH']);
							$row2['JSRQ'] = iconv('UTF-8', 'GBK', $v['JSRQ']);
							$row2['SM'] = iconv('UTF-8', 'GBK', $v['SM']);
							$row2['DJ'] = iconv('UTF-8', 'GBK', $v['DJ']);
							$row2['ZDQH'] = iconv('UTF-8', 'GBK', $v['ZDQH']);
							$row2['ZDXH'] = iconv('UTF-8', 'GBK', $v['ZDXH']);
							$row2['KFBH'] = iconv('UTF-8', 'GBK', $v['KFBH']);
							$row2['JSLX'] = iconv('UTF-8', 'GBK', $v['JSLX']);
							$row2['NF'] = iconv('UTF-8', 'GBK', $v['NF']);
							$row2['NFHRQ'] = iconv('UTF-8', 'GBK', $v['NFHRQ']);
							$row2['NJSRQ'] = iconv('UTF-8', 'GBK', $v['NJSRQ']);
							$row2['JB'] = iconv('UTF-8', 'GBK', $v['JB']);
							$row2['CWQRPC'] = iconv('UTF-8', 'GBK', $v['CWQRPC']);
							$row2['YSDBRQ'] = iconv('UTF-8', 'GBK', $v['YSDBRQ']);
							$row2['ZCS'] = iconv('UTF-8', 'GBK', $v['ZCS']);
							
						}

					
						if($fileName == '教辅库存明细'){
							$row2['YWBMBH'] = iconv('UTF-8', 'GBK', $v['YWBMBH']);
							$row2['ID'] = iconv('UTF-8', 'GBK', $v['ID']);
							$row2['SM'] = iconv('UTF-8', 'GBK', $v['SM']);
							$row2['DJ'] = iconv('UTF-8', 'GBK', $v['DJ']);
							$row2['BB'] = iconv('UTF-8', 'GBK', $v['BB']);
							$row2['KCCS'] = iconv('UTF-8', 'GBK', $v['KCCS']);
							$row2['MY'] = iconv('UTF-8', 'GBK', $v['MY']);
							$row2['SYS'] = iconv('UTF-8', 'GBK', $v['SYS']);
							$row2['KF'] = iconv('UTF-8', 'GBK', $v['KF']);
							$row2['WL'] = iconv('UTF-8', 'GBK', $v['WL']);
							$row2['FL'] = iconv('UTF-8', 'GBK', $v['FL']);
							$row2['HW'] = iconv('UTF-8', 'GBK', $v['HW']);
							$row2['GHDW'] = iconv('UTF-8', 'GBK', $v['GHDW']);
							$row2['YWY'] = iconv('UTF-8', 'GBK', $v['YWY']);
							$row2['ISBN'] = iconv('UTF-8', 'GBK', $v['ISBN']);
							$row2['BC'] = iconv('UTF-8', 'GBK', $v['BC']);
							$row2['BIANZHE'] = iconv('UTF-8', 'GBK', $v['BIANZHE']);
							$row2['NIAN'] = iconv('UTF-8', 'GBK', $v['NIAN']);
	
						}
						if($fileName == '教辅库存明细提前出'){
							$row2['YWBMBH'] = iconv('UTF-8', 'GBK', $v['YWBMBH']);
							$row2['ID'] = iconv('UTF-8', 'GBK', $v['ID']);
							$row2['SM'] = iconv('UTF-8', 'GBK', $v['SM']);
							$row2['DJ'] = iconv('UTF-8', 'GBK', $v['DJ']);
							$row2['BB'] = iconv('UTF-8', 'GBK', $v['BB']);
							$row2['KCCS'] = iconv('UTF-8', 'GBK', $v['KCCS']);
							$row2['MY'] = iconv('UTF-8', 'GBK', $v['MY']);
							$row2['SYS'] = iconv('UTF-8', 'GBK', $v['SYS']);
							$row2['KF'] = iconv('UTF-8', 'GBK', $v['KF']);
							$row2['WL'] = iconv('UTF-8', 'GBK', $v['WL']);
							$row2['FL'] = iconv('UTF-8', 'GBK', $v['FL']);
							$row2['HW'] = iconv('UTF-8', 'GBK', $v['HW']);
							$row2['GHDW'] = iconv('UTF-8', 'GBK', $v['GHDW']);
							$row2['YWY'] = iconv('UTF-8', 'GBK', $v['YWY']);
							$row2['ISBN'] = iconv('UTF-8', 'GBK', $v['ISBN']);
							$row2['BC'] = iconv('UTF-8', 'GBK', $v['BC']);
							$row2['BIANZHE'] = iconv('UTF-8', 'GBK', $v['BIANZHE']);
							$row2['NIAN'] = iconv('UTF-8', 'GBK', $v['NIAN']);
	
						}
						
						if($fileName == '教辅配发中间态'){
							$row2['YWBMBH'] = iconv('UTF-8', 'GBK', $v['YWBMBH']);
							$row2['YWBMMC'] = iconv('UTF-8', 'GBK', $v['YWBMMC']);
							$row2['DH'] = iconv('UTF-8', 'GBK', $v['DH']);
							$row2['DM'] = iconv('UTF-8', 'GBK', $v['DM']);
							$row2['ID'] = iconv('UTF-8', 'GBK', $v['ID']);
							$row2['ISBN'] = iconv('UTF-8', 'GBK', $v['ISBN']);
							$row2['SM'] = iconv('UTF-8', 'GBK', $v['SM']);
							$row2['DJ'] = iconv('UTF-8', 'GBK', $v['DJ']);
							$row2['CBNY'] = iconv('UTF-8', 'GBK', $v['CBNY']);
							$row2['PXCS'] = iconv('UTF-8', 'GBK', $v['PXCS']);
							$row2['ZMY'] = iconv('UTF-8', 'GBK', $v['ZMY']);
							$row2['SYS'] = iconv('UTF-8', 'GBK', $v['SYS']);
							$row2['CBJ'] = iconv('UTF-8', 'GBK', $v['CBJ']);
							$row2['PXDH'] = iconv('UTF-8', 'GBK', $v['PXDH']);
							$row2['BZ'] = iconv('UTF-8', 'GBK', $v['BZ']);
							$row2['ZDH'] = iconv('UTF-8', 'GBK', $v['ZDH']);
							$row2['SYLX'] = iconv('UTF-8', 'GBK', $v['SYLX']);
							$row2['SL'] = iconv('UTF-8', 'GBK', $v['SL']);
							$row2['ZJXS'] = iconv('UTF-8', 'GBK', $v['ZJXS']);
							$row2['QX'] = iconv('UTF-8', 'GBK', $v['QX']);
							$row2['DJBH'] = iconv('UTF-8', 'GBK', $v['DJBH']);
							$row2['KFBH'] = iconv('UTF-8', 'GBK', $v['KFBH']);
							$row2['BJLSH'] = iconv('UTF-8', 'GBK', $v['BJLSH']);
							$row2['CYBH'] = iconv('UTF-8', 'GBK', $v['CYBH']);
							$row2['FLOWID'] = iconv('UTF-8', 'GBK', $v['FLOWID']);
							$row2['ZDQH'] = iconv('UTF-8', 'GBK', $v['ZDQH']);
							$row2['ZDXH'] = iconv('UTF-8', 'GBK', $v['ZDXH']);
							$row2['ZDQH1'] = iconv('UTF-8', 'GBK', $v['ZDQH1']);
							$row2['ZDXH1'] = iconv('UTF-8', 'GBK', $v['ZDXH1']);
							$row2['PFRQ'] = iconv('UTF-8', 'GBK', $v['PFRQ']);
						}
						
						if($fileName == '教辅库房转移中间态明细'){
							$row2['ID'] = iconv('UTF-8', 'GBK', $v['ID']);
							$row2['SM'] = iconv('UTF-8', 'GBK', $v['SM']);
							$row2['ISBN'] = iconv('UTF-8', 'GBK', $v['ISBN']);
							$row2['DJ'] = iconv('UTF-8', 'GBK', $v['DJ']);
							$row2['BB'] = iconv('UTF-8', 'GBK', $v['BB']);
							$row2['GHDW'] = iconv('UTF-8', 'GBK', $v['GHDW']);
							$row2['YWY'] = iconv('UTF-8', 'GBK', $v['YWY']);
							$row2['CBNY'] = iconv('UTF-8', 'GBK', $v['CBNY']);
							$row2['YWLX'] = iconv('UTF-8', 'GBK', $v['YWLX']);
							$row2['CS'] = iconv('UTF-8', 'GBK', $v['SUM(CS)']);
							$row2['MY'] = iconv('UTF-8', 'GBK', $v['MY']);
							$row2['SY'] = iconv('UTF-8', 'GBK', $v['SY']);
						}
						
						if($fileName == '教辅损益明细'){
							$row2['KFBH'] = iconv('UTF-8', 'GBK', $v['KFBH']);
							$row2['SM'] = iconv('UTF-8', 'GBK', $v['SM']);
							$row2['CRKCS'] = iconv('UTF-8', 'GBK', $v['CRKCS']);
							$row2['DJ'] = iconv('UTF-8', 'GBK', $v['DJ']); 
							$row2['ZMY'] = iconv('UTF-8', 'GBK', $v['ZMY']);
							$row2['CRKSY'] = iconv('UTF-8', 'GBK', $v['CRKSY']);
							$row2['CRKRQ'] = iconv('UTF-8', 'GBK', $v['CRKRQ']);
	
						}
						if($fileName == '教辅当月已结算-到货已结-到货'){
							$row2['YWBMBH'] = iconv('UTF-8', 'GBK', $v['YWBMBH']);
							$row2['GHDWH'] = iconv('UTF-8', 'GBK', $v['GHDWH']);
							$row2['MC'] = iconv('UTF-8', 'GBK', $v['MC']);
							$row2['DHPCH'] = iconv('UTF-8', 'GBK', $v['DHPCH']);
							$row2['SHRQ'] = iconv('UTF-8', 'GBK', $v['SHRQ']);
							$row2['YSDJ'] = iconv('UTF-8', 'GBK', $v['YSDJ']);
							$row2['SSDH'] = iconv('UTF-8', 'GBK', $v['SSDH']);
							$row2['ZDQH'] = iconv('UTF-8', 'GBK', $v['ZDQH']);
							$row2['ZDXH'] = iconv('UTF-8', 'GBK', $v['ZDXH']);
							$row2['SM'] = iconv('UTF-8', 'GBK', $v['SM']);
							$row2['DJ'] = iconv('UTF-8', 'GBK', $v['DJ']);
							$row2['JXSSSL'] = iconv('UTF-8', 'GBK', $v['JXSSSL']);
							$row2['MY'] = iconv('UTF-8', 'GBK', $v['MY']);
							$row2['SYS'] = iconv('UTF-8', 'GBK', $v['SYS']);
							$row2['ZDPCH'] = iconv('UTF-8', 'GBK', $v['ZDPCH']);
							$row2['JSRQ'] = iconv('UTF-8', 'GBK', $v['JSRQ']);
							$row2['JSLX'] = iconv('UTF-8', 'GBK', $v['JSLX']);
							$row2['ZDPCH1'] = iconv('UTF-8', 'GBK', $v['ZDPCH1']);
							$row2['TSLSH'] = iconv('UTF-8', 'GBK', $v['TSLSH']);
							$row2['YSRQ'] = iconv('UTF-8', 'GBK', $v['YSRQ']);
							$row2['YSDBRQ'] = iconv('UTF-8', 'GBK', $v['YSDBRQ']);
							$row2['JCDRQ'] = iconv('UTF-8', 'GBK', $v['JCDRQ']);
							$row2['KQBJ'] = iconv('UTF-8', 'GBK', $v['KQBJ']);
							$row2['JSGHDWH'] = iconv('UTF-8', 'GBK', $v['JSGHDWH']);
							$row2['YSGHDWH'] = iconv('UTF-8', 'GBK', $v['YSGHDWH']);
							$row2['FLOWID_DHMX'] = iconv('UTF-8', 'GBK', $v['FLOWID_DHMX']);
						}
						if($fileName == '教辅当月已结算-到货已结-进退'){
							$row2['YWBMBH'] = iconv('UTF-8', 'GBK', $v['YWBMBH']);
							$row2['GHDWH'] = iconv('UTF-8', 'GBK', $v['GHDWH']);
							$row2['MC'] = iconv('UTF-8', 'GBK', $v['MC']);
							$row2['DBRQ'] = iconv('UTF-8', 'GBK', $v['DBRQ']);
							$row2['ID'] = iconv('UTF-8', 'GBK', $v['ID']);
							$row2['SM'] = iconv('UTF-8', 'GBK', $v['SM']);
							$row2['DJ'] = iconv('UTF-8', 'GBK', $v['DJ']);
							$row2['ZMY'] = iconv('UTF-8', 'GBK', $v['ZMY']);
							$row2['ZSY'] = iconv('UTF-8', 'GBK', $v['ZSY']);
							$row2['BZ'] = iconv('UTF-8', 'GBK', $v['BZ']);
							$row2['YWPCH'] = iconv('UTF-8', 'GBK', $v['YWPCH']);
							$row2['ZDPCH'] = iconv('UTF-8', 'GBK', $v['ZDPCH']);
							$row2['JSRQ'] = iconv('UTF-8', 'GBK', $v['JSRQ']);
							$row2['JSLX'] = iconv('UTF-8', 'GBK', $v['JSLX']);
							$row2['ZDQH'] = iconv('UTF-8', 'GBK', $v['ZDQH']);
							$row2['ZDPCH1'] = iconv('UTF-8', 'GBK', $v['ZDPCH1']);
							$row2['TSLSH'] = iconv('UTF-8', 'GBK', $v['TSLSH']);
							$row2['NF'] = iconv('UTF-8', 'GBK', $v['NF']);
							$row2['NFHRQ'] = iconv('UTF-8', 'GBK', $v['NFHRQ']);
							$row2['NJSRQ'] = iconv('UTF-8', 'GBK', $v['NJSRQ']);
							$row2['JSGHDWH'] = iconv('UTF-8', 'GBK', $v['JSGHDWH']);
							$row2['YSGHDWH'] = iconv('UTF-8', 'GBK', $v['YSGHDWH']);
							$row2['ZCS'] = iconv('UTF-8', 'GBK', $v['ZCS']);
						}
						if($fileName == '教辅当月已结算-到货已结-退货'){
							$row2['YWBMBH'] = iconv('UTF-8', 'GBK', $v['YWBMBH']);
							$row2['GHDWH'] = iconv('UTF-8', 'GBK', $v['GHDWH']);
							$row2['MC'] = iconv('UTF-8', 'GBK', $v['MC']);
							$row2['ID'] = iconv('UTF-8', 'GBK', $v['ID']);
							$row2['DBRQ'] = iconv('UTF-8', 'GBK', $v['DBRQ']);
							$row2['SM'] = iconv('UTF-8', 'GBK', $v['SM']);
							$row2['DJ'] = iconv('UTF-8', 'GBK', $v['DJ']);
							$row2['ZMY'] = iconv('UTF-8', 'GBK', $v['ZMY']);
							$row2['ZSY'] = iconv('UTF-8', 'GBK', $v['ZSY']);
							$row2['BZ'] = iconv('UTF-8', 'GBK', $v['BZ']);
							$row2['YWPCH'] = iconv('UTF-8', 'GBK', $v['YWPCH']);
							$row2['JSRQ'] = iconv('UTF-8', 'GBK', $v['JSRQ']);
							$row2['ZDPCH'] = iconv('UTF-8', 'GBK', $v['ZDPCH']);
							$row2['JSLX'] = iconv('UTF-8', 'GBK', $v['JSLX']);
							$row2['ZDQH'] = iconv('UTF-8', 'GBK', $v['ZDQH']);
							$row2['ZDXH'] = iconv('UTF-8', 'GBK', $v['ZDXH']);
							$row2['ZDPCH1'] = iconv('UTF-8', 'GBK', $v['ZDPCH1']);
							$row2['TSLSH'] = iconv('UTF-8', 'GBK', $v['TSLSH']);
							$row2['NF'] = iconv('UTF-8', 'GBK', $v['NF']);
							$row2['NFHRQ'] = iconv('UTF-8', 'GBK', $v['NFHRQ']);
							$row2['NJSRQ'] = iconv('UTF-8', 'GBK', $v['NJSRQ']);
							$row2['JSGHDWH'] = iconv('UTF-8', 'GBK', $v['JSGHDWH']);
							$row2['YSGHDWH'] = iconv('UTF-8', 'GBK', $v['YSGHDWH']);
							$row2['ZCS'] = iconv('UTF-8', 'GBK', $v['ZCS']);
							
						}
						if($fileName == '教辅当月已结算-到货已结-更正'){
							$row2['YWBMBH'] = iconv('UTF-8', 'GBK', $v['YWBMBH']);
							$row2['DH'] = iconv('UTF-8', 'GBK', $v['DH']);
							$row2['MC'] = iconv('UTF-8', 'GBK', $v['MC']);
							$row2['PFPCH'] = iconv('UTF-8', 'GBK', $v['PFPCH']);
							$row2['BZ'] = iconv('UTF-8', 'GBK', $v['BZ']);
							$row2['DBRQ'] = iconv('UTF-8', 'GBK', $v['DBRQ']);
							$row2['PFRQ'] = iconv('UTF-8', 'GBK', $v['PFRQ']);
							$row2['ZMY'] = iconv('UTF-8', 'GBK', $v['ZMY']);
							$row2['ZSY'] = iconv('UTF-8', 'GBK', $v['ZSY']);
							$row2['CBJ'] = iconv('UTF-8', 'GBK', $v['CBJ']);
							$row2['TSLSH'] = iconv('UTF-8', 'GBK', $v['TSLSH']);
							$row2['JSRQ'] = iconv('UTF-8', 'GBK', $v['JSRQ']);
							$row2['SM'] = iconv('UTF-8', 'GBK', $v['SM']);
							$row2['DJ'] = iconv('UTF-8', 'GBK', $v['DJ']);
							$row2['ZDQH'] = iconv('UTF-8', 'GBK', $v['ZDQH']);
							$row2['ZDXH'] = iconv('UTF-8', 'GBK', $v['ZDXH']);
							//$row2['ZDPCH1'] = iconv('UTF-8', 'GBK', $v['ZDPCH1']);
							$row2['KFBH'] = iconv('UTF-8', 'GBK', $v['KFBH']);
							$row2['JSLX'] = iconv('UTF-8', 'GBK', $v['JSLX']);
							$row2['NF'] = iconv('UTF-8', 'GBK', $v['NF']);
							$row2['NFHRQ'] = iconv('UTF-8', 'GBK', $v['NFHRQ']);
							$row2['NJSRQ'] = iconv('UTF-8', 'GBK', $v['NJSRQ']);
							$row2['JB'] = iconv('UTF-8', 'GBK', $v['JB']);
							$row2['CWQRPC'] = iconv('UTF-8', 'GBK', $v['CWQRPC']);
							$row2['YSDBRQ'] = iconv('UTF-8', 'GBK', $v['YSDBRQ']);
							$row2['ZCS'] = iconv('UTF-8', 'GBK', $v['ZCS']);
						}
						if($fileName == '教辅当月已结算-发货已结-到货'){
							$row2['CBNY'] = iconv('UTF-8', 'GBK', $v['CBNY']);
							$row2['GHDW'] = iconv('UTF-8', 'GBK', $v['GHDW']);
							$row2['GHDWMC'] = iconv('UTF-8', 'GBK', $v['GHDWMC']);
							$row2['YWBMBH'] = iconv('UTF-8', 'GBK', $v['YWBMBH']);
							$row2['DH'] = iconv('UTF-8', 'GBK', $v['DH']);
							$row2['MC'] = iconv('UTF-8', 'GBK', $v['MC']);
							$row2['PFPCH'] = iconv('UTF-8', 'GBK', $v['PFPCH']);
							$row2['BZ'] = iconv('UTF-8', 'GBK', $v['BZ']);
							$row2['DBRQ'] = iconv('UTF-8', 'GBK', $v['DBRQ']);
							$row2['PFRQ'] = iconv('UTF-8', 'GBK', $v['PFRQ']);
							$row2['ZMY'] = iconv('UTF-8', 'GBK', $v['ZMY']);
							$row2['ZSY'] = iconv('UTF-8', 'GBK', $v['ZSY']);
							$row2['CBJ'] = iconv('UTF-8', 'GBK', $v['CBJ']);
							$row2['TSLSH'] = iconv('UTF-8', 'GBK', $v['TSLSH']);
							$row2['JSRQ'] = iconv('UTF-8', 'GBK', $v['JSRQ']);
							$row2['SM'] = iconv('UTF-8', 'GBK', $v['SM']);
							$row2['DJ'] = iconv('UTF-8', 'GBK', $v['DJ']);
							$row2['ZDQH'] = iconv('UTF-8', 'GBK', $v['ZDQH']);
							$row2['ZDXH'] = iconv('UTF-8', 'GBK', $v['ZDXH']);
							$row2['KFBH'] = iconv('UTF-8', 'GBK', $v['KFBH']);
							$row2['JSLX'] = iconv('UTF-8', 'GBK', $v['JSLX']);
							$row2['NF'] = iconv('UTF-8', 'GBK', $v['NF']);
							$row2['NFHRQ'] = iconv('UTF-8', 'GBK', $v['NFHRQ']);
							$row2['NJSRQ'] = iconv('UTF-8', 'GBK', $v['NJSRQ']);
							$row2['JB'] = iconv('UTF-8', 'GBK', $v['JB']);
							$row2['CWQRPC'] = iconv('UTF-8', 'GBK', $v['CWQRPC']);
							$row2['YSDBRQ'] = iconv('UTF-8', 'GBK', $v['YSDBRQ']);
							$row2['ZCS'] = iconv('UTF-8', 'GBK', $v['ZCS']);
						}
						if($fileName == '教辅当月已结算-发货已结-退货'){
							$row2['CBNY'] = iconv('UTF-8', 'GBK', $v['CBNY']);
							$row2['GHDW'] = iconv('UTF-8', 'GBK', $v['GHDW']);
							$row2['GHDWMC'] = iconv('UTF-8', 'GBK', $v['GHDWMC']);
							$row2['YWBMBH'] = iconv('UTF-8', 'GBK', $v['YWBMBH']);
							$row2['KFBH'] = iconv('UTF-8', 'GBK', $v['KFBH']);
							$row2['DH'] = iconv('UTF-8', 'GBK', $v['DH']);
							$row2['DM'] = iconv('UTF-8', 'GBK', $v['DM']);
							$row2['PCH'] = iconv('UTF-8', 'GBK', $v['PCH']);
							$row2['THRQ'] = iconv('UTF-8', 'GBK', $v['THRQ']);
							$row2['ZMY'] = iconv('UTF-8', 'GBK', $v['ZMY']);
							$row2['ZSY'] = iconv('UTF-8', 'GBK', $v['ZSY']);
							$row2['ZCB'] = iconv('UTF-8', 'GBK', $v['ZCB']);
							$row2['TSLSH'] = iconv('UTF-8', 'GBK', $v['TSLSH']);
							$row2['JSRQ'] = iconv('UTF-8', 'GBK', $v['JSRQ']);
							$row2['JSLX'] = iconv('UTF-8', 'GBK', $v['JSLX']);
							$row2['ZDQH'] = iconv('UTF-8', 'GBK', $v['ZDQH']);
							$row2['NF'] = iconv('UTF-8', 'GBK', $v['NF']);
							$row2['NFHRQ'] = iconv('UTF-8', 'GBK', $v['NFHRQ']);
							$row2['NJSRQ'] = iconv('UTF-8', 'GBK', $v['NJSRQ']);
							$row2['JB'] = iconv('UTF-8', 'GBK', $v['JB']);
							$row2['CWQRPC'] = iconv('UTF-8', 'GBK', $v['CWQRPC']);
							$row2['ZCS'] = iconv('UTF-8', 'GBK', $v['ZCS']);
							$row2['DJ'] = iconv('UTF-8', 'GBK', $v['DJ']);
							$row2['ID'] = iconv('UTF-8', 'GBK', $v['ID']);
							$row2['BB'] = iconv('UTF-8', 'GBK', $v['BB']);
							$row2['SM'] = iconv('UTF-8', 'GBK', $v['SM']);
							$row2['ISBN'] = iconv('UTF-8', 'GBK', $v['ISBN']);
							$row2['CBNY'] = iconv('UTF-8', 'GBK', $v['CBNY']);
						}
						if($fileName == '教辅当月已结算-发货已结-更正'){
							$row2['CBNY'] = iconv('UTF-8', 'GBK', $v['CBNY']);
							$row2['GHDW'] = iconv('UTF-8', 'GBK', $v['GHDW']);
							$row2['GHDWMC'] = iconv('UTF-8', 'GBK', $v['GHDWMC']);
							$row2['YWBMBH'] = iconv('UTF-8', 'GBK', $v['YWBMBH']);
							$row2['KFBH'] = iconv('UTF-8', 'GBK', $v['KFBH']);
							$row2['DH'] = iconv('UTF-8', 'GBK', $v['DH']);
							$row2['DM'] = iconv('UTF-8', 'GBK', $v['DM']);
							$row2['GZPCH'] = iconv('UTF-8', 'GBK', $v['GZPCH']);
							$row2['GZRQ'] = iconv('UTF-8', 'GBK', $v['GZRQ']);
							$row2['ZMY'] = iconv('UTF-8', 'GBK', $v['ZMY']);
							$row2['ZSY'] = iconv('UTF-8', 'GBK', $v['ZSY']);
							$row2['ZCB'] = iconv('UTF-8', 'GBK', $v['ZCB']);
							$row2['TSLSH'] = iconv('UTF-8', 'GBK', $v['TSLSH']);
							$row2['JSRQ'] = iconv('UTF-8', 'GBK', $v['JSRQ']);
							$row2['BZ'] = iconv('UTF-8', 'GBK', $v['BZ']);
							$row2['JSLX'] = iconv('UTF-8', 'GBK', $v['JSLX']);
							$row2['ZDQH'] = iconv('UTF-8', 'GBK', $v['ZDQH']);
							$row2['NF'] = iconv('UTF-8', 'GBK', $v['NF']);
							$row2['NFHRQ'] = iconv('UTF-8', 'GBK', $v['NFHRQ']);
							$row2['NJSRQ'] = iconv('UTF-8', 'GBK', $v['NJSRQ']);
							$row2['JB'] = iconv('UTF-8', 'GBK', $v['JB']);
							$row2['ZCS'] = iconv('UTF-8', 'GBK', $v['ZCS']);
						}
						
						
						if($fileName == '教辅应付款-到货未结明细'){
							$row2['YWBMBH'] = iconv('UTF-8', 'GBK', $v['YWBMBH']);
							$row2['GHDWH'] = iconv('UTF-8', 'GBK', $v['GHDWH']);
							$row2['MC'] = iconv('UTF-8', 'GBK', $v['MC']);
							$row2['DHPCH'] = iconv('UTF-8', 'GBK', $v['DHPCH']);
							$row2['SHRQ'] = iconv('UTF-8', 'GBK', $v['SHRQ']);
							$row2['YSDJ'] = iconv('UTF-8', 'GBK', $v['YSDJ']);
							$row2['SSDH'] = iconv('UTF-8', 'GBK', $v['SSDH']);
							$row2['ZDQH'] = iconv('UTF-8', 'GBK', $v['ZDQH']);
							$row2['ZDXH'] = iconv('UTF-8', 'GBK', $v['ZDXH']);
							$row2['SM'] = iconv('UTF-8', 'GBK', $v['SM']);
							$row2['DJ'] = iconv('UTF-8', 'GBK', $v['DJ']);
							$row2['JXSSSL'] = iconv('UTF-8', 'GBK', $v['JXSSSL']);
							$row2['MY'] = iconv('UTF-8', 'GBK', $v['MY']);
							$row2['SYS'] = iconv('UTF-8', 'GBK', $v['SYS']);
							$row2['ZDPCH'] = iconv('UTF-8', 'GBK', $v['ZDPCH']);
							$row2['JSRQ'] = iconv('UTF-8', 'GBK', $v['JSRQ']);
							$row2['JSLX'] = iconv('UTF-8', 'GBK', $v['JSLX']);
							$row2['ZDPCH1'] = iconv('UTF-8', 'GBK', $v['ZDPCH1']);
							$row2['TSLSH'] = iconv('UTF-8', 'GBK', $v['TSLSH']);
							$row2['YSDBRQ'] = iconv('UTF-8', 'GBK', $v['YSDBRQ']);
							$row2['JCDRQ'] = iconv('UTF-8', 'GBK', $v['JCDRQ']);
							$row2['KQBJ'] = iconv('UTF-8', 'GBK', $v['KQBJ']);
							$row2['JSGHDWH'] = iconv('UTF-8', 'GBK', $v['JSGHDWH']);
							$row2['YSGHDWH'] = iconv('UTF-8', 'GBK', $v['YSGHDWH']);
							$row2['FLOWID_DHMX'] = iconv('UTF-8', 'GBK', $v['FLOWID_DHMX']);
							$row2['SHRQ'] = iconv('UTF-8', 'GBK', $v['SHRQ']);
							$row2['YSRQ'] = iconv('UTF-8', 'GBK', $v['YSRQ']);
						}
						if($fileName == '教辅应付款-进退未结明细'){
							$row2['YWBMBH'] = iconv('UTF-8', 'GBK', $v['YWBMBH']);
							$row2['GHDWH'] = iconv('UTF-8', 'GBK', $v['GHDWH']);
							$row2['MC'] = iconv('UTF-8', 'GBK', $v['MC']);
							$row2['DBRQ'] = iconv('UTF-8', 'GBK', $v['DBRQ']);
							$row2['ID'] = iconv('UTF-8', 'GBK', $v['ID']);
							$row2['SM'] = iconv('UTF-8', 'GBK', $v['SM']);
							$row2['DJ'] = iconv('UTF-8', 'GBK', $v['DJ']);
							$row2['ZMY'] = iconv('UTF-8', 'GBK', $v['ZMY']);
							$row2['ZSY'] = iconv('UTF-8', 'GBK', $v['ZSY']);
							$row2['BZ'] = iconv('UTF-8', 'GBK', $v['BZ']);
							$row2['YWPCH'] = iconv('UTF-8', 'GBK', $v['YWPCH']);
							$row2['ZDPCH'] = iconv('UTF-8', 'GBK', $v['ZDPCH']);
							$row2['JSRQ'] = iconv('UTF-8', 'GBK', $v['JSRQ']);
							$row2['JSLX'] = iconv('UTF-8', 'GBK', $v['JSLX']);
							$row2['ZDQH'] = iconv('UTF-8', 'GBK', $v['ZDQH']);
							$row2['ZDXH'] = iconv('UTF-8', 'GBK', $v['ZDXH']);
							$row2['ZDPCH1'] = iconv('UTF-8', 'GBK', $v['ZDPCH1']);
							$row2['TSLSH'] = iconv('UTF-8', 'GBK', $v['TSLSH']);
							$row2['NF'] = iconv('UTF-8', 'GBK', $v['NF']);
							$row2['NFHRQ'] = iconv('UTF-8', 'GBK', $v['NFHRQ']);
							$row2['NJSRQ'] = iconv('UTF-8', 'GBK', $v['NJSRQ']);
							$row2['JSGHDWH'] = iconv('UTF-8', 'GBK', $v['JSGHDWH']);
							$row2['YSGHDWH'] = iconv('UTF-8', 'GBK', $v['YSGHDWH']);
							$row2['ZCS'] = iconv('UTF-8', 'GBK', $v['ZCS']);
							
						}
						
						if($fileName == '教辅应收款-发货未结明细'){
							$row2['CBNY'] = iconv('UTF-8', 'GBK', $v['CBNY']);
							$row2['GHDW'] = iconv('UTF-8', 'GBK', $v['GHDW']);
							$row2['GHDWMC'] = iconv('UTF-8', 'GBK', $v['GHDWMC']);
							$row2['YWBMBH'] = iconv('UTF-8', 'GBK', $v['YWBMBH']);
							$row2['DH'] = iconv('UTF-8', 'GBK', $v['DH']);
							$row2['MC'] = iconv('UTF-8', 'GBK', $v['MC']);
							$row2['PFPCH'] = iconv('UTF-8', 'GBK', $v['PFPCH']);
							$row2['BZ'] = iconv('UTF-8', 'GBK', $v['BZ']);
							$row2['DBRQ'] = iconv('UTF-8', 'GBK', $v['DBRQ']);
							$row2['PFRQ'] = iconv('UTF-8', 'GBK', $v['PFRQ']);
							$row2['ZMY'] = iconv('UTF-8', 'GBK', $v['ZMY']);
							$row2['ZSY'] = iconv('UTF-8', 'GBK', $v['ZSY']);
							$row2['CBJ'] = iconv('UTF-8', 'GBK', $v['CBJ']);
							$row2['TSLSH'] = iconv('UTF-8', 'GBK', $v['TSLSH']);
							$row2['JSRQ'] = iconv('UTF-8', 'GBK', $v['JSRQ']);
							$row2['SM'] = iconv('UTF-8', 'GBK', $v['SM']);
							$row2['DJ'] = iconv('UTF-8', 'GBK', $v['DJ']);
							$row2['ZDQH'] = iconv('UTF-8', 'GBK', $v['ZDQH']);
							$row2['ZDXH'] = iconv('UTF-8', 'GBK', $v['ZDXH']);
							$row2['KFBH'] = iconv('UTF-8', 'GBK', $v['KFBH']);
							$row2['JSLX'] = iconv('UTF-8', 'GBK', $v['JSLX']);
							$row2['NF'] = iconv('UTF-8', 'GBK', $v['NF']);
							$row2['NFHRQ'] = iconv('UTF-8', 'GBK', $v['NFHRQ']);
							$row2['NJSRQ'] = iconv('UTF-8', 'GBK', $v['NJSRQ']);
							$row2['JB'] = iconv('UTF-8', 'GBK', $v['JB']);
							$row2['CWQRPC'] = iconv('UTF-8', 'GBK', $v['CWQRPC']);
							$row2['YSDBRQ'] = iconv('UTF-8', 'GBK', $v['YSDBRQ']);
							$row2['ZCS'] = iconv('UTF-8', 'GBK', $v['ZCS']);
							
						}
						if($fileName == '教辅应收款-销退未结明细'){
							$row2['CBNY'] = iconv('UTF-8', 'GBK', $v['CBNY']);
							$row2['GHDW'] = iconv('UTF-8', 'GBK', $v['GHDW']);
							$row2['GHDWMC'] = iconv('UTF-8', 'GBK', $v['GHDWMC']);
							$row2['YWBMBH'] = iconv('UTF-8', 'GBK', $v['YWBMBH']);
							$row2['KFBH'] = iconv('UTF-8', 'GBK', $v['KFBH']);
							$row2['DH'] = iconv('UTF-8', 'GBK', $v['DH']);
							$row2['DM'] = iconv('UTF-8', 'GBK', $v['DM']);
							$row2['PCH'] = iconv('UTF-8', 'GBK', $v['PCH']);
							$row2['THRQ'] = iconv('UTF-8', 'GBK', $v['THRQ']);
							$row2['ZMY'] = iconv('UTF-8', 'GBK', $v['ZMY']);
							$row2['ZSY'] = iconv('UTF-8', 'GBK', $v['ZSY']);
							$row2['ZCB'] = iconv('UTF-8', 'GBK', $v['ZCB']);
							$row2['TSLSH'] = iconv('UTF-8', 'GBK', $v['TSLSH']);
							$row2['JSRQ'] = iconv('UTF-8', 'GBK', $v['JSRQ']);
							$row2['JSLX'] = iconv('UTF-8', 'GBK', $v['JSLX']);
							$row2['ZDQH'] = iconv('UTF-8', 'GBK', $v['ZDQH']);
							$row2['NF'] = iconv('UTF-8', 'GBK', $v['NF']);
							$row2['NFHRQ'] = iconv('UTF-8', 'GBK', $v['NFHRQ']);
							$row2['NJSRQ'] = iconv('UTF-8', 'GBK', $v['NJSRQ']);
							$row2['JB'] = iconv('UTF-8', 'GBK', $v['JB']);
							$row2['CWQRPC'] = iconv('UTF-8', 'GBK', $v['CWQRPC']);
							$row2['ZCS'] = iconv('UTF-8', 'GBK', $v['ZCS']);
							$row2['DJ'] = iconv('UTF-8', 'GBK', $v['DJ']);

						}
						if($fileName == '教辅应收款-更正未结明细'){
							$row2['YWBMBH'] = iconv('UTF-8', 'GBK', $v['YWBMBH']);
							$row2['KFBH'] = iconv('UTF-8', 'GBK', $v['KFBH']);
							$row2['DH'] = iconv('UTF-8', 'GBK', $v['DH']);
							$row2['DM'] = iconv('UTF-8', 'GBK', $v['DM']);
							$row2['GZPCH'] = iconv('UTF-8', 'GBK', $v['GZPCH']);
							$row2['GZRQ'] = iconv('UTF-8', 'GBK', $v['GZRQ']);
							$row2['ZMY'] = iconv('UTF-8', 'GBK', $v['ZMY']);
							$row2['ZSY'] = iconv('UTF-8', 'GBK', $v['ZSY']);
							$row2['ZCB'] = iconv('UTF-8', 'GBK', $v['ZCB']);
							$row2['TSLSH'] = iconv('UTF-8', 'GBK', $v['TSLSH']);
							$row2['JSRQ'] = iconv('UTF-8', 'GBK', $v['JSRQ']);
							$row2['BZ'] = iconv('UTF-8', 'GBK', $v['BZ']);
							$row2['JSLX'] = iconv('UTF-8', 'GBK', $v['JSLX']);
							$row2['ZDQH'] = iconv('UTF-8', 'GBK', $v['ZDQH']);
							$row2['NF'] = iconv('UTF-8', 'GBK', $v['NF']);
							$row2['NFHRQ'] = iconv('UTF-8', 'GBK', $v['NFHRQ']);
							$row2['NJSRQ'] = iconv('UTF-8', 'GBK', $v['NJSRQ']);
							$row2['JB'] = iconv('UTF-8', 'GBK', $v['JB']);
							$row2['ZCS'] = iconv('UTF-8', 'GBK', $v['ZCS']);

						}


						
						

					}

					if($yewugongsi==2){ //教材
						if($fileName == '教材月报'){
							$row2['YWBMBH'] = iconv('UTF-8', 'GBK', $v['YWBMBH']);
							$row2['LX'] = iconv('UTF-8', 'GBK', $v['LX']);
							$row2['BH'] = iconv('UTF-8', 'GBK', $v['BH']);
							$row2['DMMC'] = iconv('UTF-8', 'GBK', $v['DMMC']);
							$row2['QCWJMY'] = iconv('UTF-8', 'GBK', $v['QCWJMY']);
							$row2['QCWJSY'] = iconv('UTF-8', 'GBK', $v['QCWJSY']);
							$row2['QCWJCB'] = iconv('UTF-8', 'GBK', $v['QCWJCB']);
							$row2['JFMY'] = iconv('UTF-8', 'GBK', $v['JFMY']);
							$row2['JFSY'] = iconv('UTF-8', 'GBK', $v['JFSY']);
							$row2['JFCB'] = iconv('UTF-8', 'GBK', $v['JFCB']);
							$row2['JSMY'] = iconv('UTF-8', 'GBK', $v['JSMY']);
							$row2['JSSY'] = iconv('UTF-8', 'GBK', $v['JSSY']);
							$row2['JSCB'] = iconv('UTF-8', 'GBK', $v['JSCB']);
							$row2['QMWJMY'] = iconv('UTF-8', 'GBK', $v['QMWJMY']);
							$row2['QMWJSY'] = iconv('UTF-8', 'GBK', $v['QMWJSY']);
							$row2['QMWJCB'] = iconv('UTF-8', 'GBK', $v['QMWJCB']);
							$row2['SYLX'] = iconv('UTF-8', 'GBK', $v['SYLX']);
							$row2['BB'] = iconv('UTF-8', 'GBK', $v['BB']);
							$row2['MINDATE'] = iconv('UTF-8', 'GBK', $v['MINDATE']);
							$row2['MAXDATE'] = iconv('UTF-8', 'GBK', $v['MAXDATE']);
							$row2['NY'] = iconv('UTF-8', 'GBK', $v['NY']);
							$row2['BJSMY'] = iconv('UTF-8', 'GBK', $v['BJSMY']);
							$row2['BJSSY'] = iconv('UTF-8', 'GBK', $v['BJSSY']);

						}
						if($fileName == '教材到货明细'){
							$row2['YWBMBH'] = iconv('UTF-8', 'GBK', $v['YWBMBH']);
							$row2['GHDWH'] = iconv('UTF-8', 'GBK', $v['GHDWH']);
							$row2['MC'] = iconv('UTF-8', 'GBK', $v['MC']);
							$row2['DHPCH'] = iconv('UTF-8', 'GBK', $v['DHPCH']);
							$row2['SHRQ'] = iconv('UTF-8', 'GBK', $v['SHRQ']);
							$row2['YSDJ'] = iconv('UTF-8', 'GBK', $v['YSDJ']);
							$row2['SSDH'] = iconv('UTF-8', 'GBK', $v['SSDH']);
							$row2['ZDQH'] = iconv('UTF-8', 'GBK', $v['ZDQH']);
							$row2['ZDXH'] = iconv('UTF-8', 'GBK', $v['ZDXH']);
							$row2['SM'] = iconv('UTF-8', 'GBK', $v['SM']);
							$row2['DJ'] = iconv('UTF-8', 'GBK', $v['DJ']);
							$row2['JXSSSL'] = iconv('UTF-8', 'GBK', $v['JXSSSL']);
							$row2['MY'] = iconv('UTF-8', 'GBK', $v['MY']);
							$row2['SYS'] = iconv('UTF-8', 'GBK', $v['SYS']);
							$row2['ZDPCH'] = iconv('UTF-8', 'GBK', $v['ZDPCH']);
							$row2['JSRQ'] = iconv('UTF-8', 'GBK', $v['JSRQ']);
							$row2['JSLX'] = iconv('UTF-8', 'GBK', $v['JSLX']);
							$row2['ZDPCH1'] = iconv('UTF-8', 'GBK', $v['ZDPCH1']);
							$row2['TSLSH'] = iconv('UTF-8', 'GBK', $v['TSLSH']);
							$row2['YSRQ'] = iconv('UTF-8', 'GBK', $v['YSRQ']);
							$row2['YSDBRQ'] = iconv('UTF-8', 'GBK', $v['YSDBRQ']);
							$row2['JCDRQ'] = iconv('UTF-8', 'GBK', $v['JCDRQ']);
							$row2['KQBJ'] = iconv('UTF-8', 'GBK', $v['KQBJ']);
							$row2['JSGHDWH'] = iconv('UTF-8', 'GBK', $v['JSGHDWH']);
							$row2['YSGHDWH'] = iconv('UTF-8', 'GBK', $v['YSGHDWH']);
							$row2['FLOWID_DHMX'] = iconv('UTF-8', 'GBK', $v['FLOWID_DHMX']);

						}
						
						
						if($fileName == '教材进退明细'){
							$row2['YWBMBH'] = iconv('UTF-8', 'GBK', $v['YWBMBH']);
							$row2['GHDWH'] = iconv('UTF-8', 'GBK', $v['GHDWH']);
							$row2['MC'] = iconv('UTF-8', 'GBK', $v['MC']);
							$row2['DBRQ'] = iconv('UTF-8', 'GBK', $v['DBRQ']);
							$row2['ID'] = iconv('UTF-8', 'GBK', $v['ID']);
							$row2['SM'] = iconv('UTF-8', 'GBK', $v['SM']);
							$row2['DJ'] = iconv('UTF-8', 'GBK', $v['DJ']);
							$row2['ZMY'] = iconv('UTF-8', 'GBK', $v['ZMY']);
							$row2['ZSY'] = iconv('UTF-8', 'GBK', $v['ZSY']);
							$row2['BZ'] = iconv('UTF-8', 'GBK', $v['BZ']);
							$row2['YWPCH'] = iconv('UTF-8', 'GBK', $v['YWPCH']);
							$row2['ZDPCH'] = iconv('UTF-8', 'GBK', $v['ZDPCH']);
							$row2['JSRQ'] = iconv('UTF-8', 'GBK', $v['JSRQ']);
							$row2['JSLX'] = iconv('UTF-8', 'GBK', $v['JSLX']);
							$row2['ZDQH'] = iconv('UTF-8', 'GBK', $v['ZDQH']);
							$row2['ZDXH'] = iconv('UTF-8', 'GBK', $v['ZDXH']);
							$row2['ZDPCH1'] = iconv('UTF-8', 'GBK', $v['ZDPCH1']);
							$row2['TSLSH'] = iconv('UTF-8', 'GBK', $v['TSLSH']);
							$row2['NF'] = iconv('UTF-8', 'GBK', $v['NF']);
							$row2['NFHRQ'] = iconv('UTF-8', 'GBK', $v['NFHRQ']);
							$row2['NJSRQ'] = iconv('UTF-8', 'GBK', $v['NJSRQ']);
							$row2['JSGHDWH'] = iconv('UTF-8', 'GBK', $v['JSGHDWH']);
							$row2['YSGHDWH'] = iconv('UTF-8', 'GBK', $v['YSGHDWH']);
							$row2['ZCS'] = iconv('UTF-8', 'GBK', $v['ZCS']);

						}

						if($fileName == '教材出版社免费让利明细'){
							$row2['YWBMBH'] = iconv('UTF-8', 'GBK', $v['YWBMBH']);
							$row2['GHDWH'] = iconv('UTF-8', 'GBK', $v['GHDWH']);
							$row2['MC'] = iconv('UTF-8', 'GBK', $v['MC']);
							$row2['DBRQ'] = iconv('UTF-8', 'GBK', $v['DBRQ']);
							$row2['ID'] = iconv('UTF-8', 'GBK', $v['ID']);
							$row2['SM'] = iconv('UTF-8', 'GBK', $v['SM']);
							$row2['DJ'] = iconv('UTF-8', 'GBK', $v['DJ']);
							$row2['ZMY'] = iconv('UTF-8', 'GBK', $v['ZMY']);
							$row2['ZSY'] = iconv('UTF-8', 'GBK', $v['ZSY']);
							$row2['BZ'] = iconv('UTF-8', 'GBK', $v['BZ']);
							$row2['YWPCH'] = iconv('UTF-8', 'GBK', $v['YWPCH']);
							$row2['ZDPCH'] = iconv('UTF-8', 'GBK', $v['ZDPCH']);
							$row2['JSRQ'] = iconv('UTF-8', 'GBK', $v['JSRQ']);
							$row2['JSLX'] = iconv('UTF-8', 'GBK', $v['JSLX']);
							$row2['ZDQH'] = iconv('UTF-8', 'GBK', $v['ZDQH']);
							$row2['ZDXH'] = iconv('UTF-8', 'GBK', $v['ZDXH']);
							$row2['ZDPCH1'] = iconv('UTF-8', 'GBK', $v['ZDPCH1']);
							$row2['TSLSH'] = iconv('UTF-8', 'GBK', $v['TSLSH']);
							$row2['NF'] = iconv('UTF-8', 'GBK', $v['NF']);
							$row2['NFHRQ'] = iconv('UTF-8', 'GBK', $v['NFHRQ']);
							$row2['NJSRQ'] = iconv('UTF-8', 'GBK', $v['NJSRQ']);
							$row2['JSGHDWH'] = iconv('UTF-8', 'GBK', $v['JSGHDWH']);
							$row2['YSGHDWH'] = iconv('UTF-8', 'GBK', $v['YSGHDWH']);
							$row2['ZCS'] = iconv('UTF-8', 'GBK', $v['ZCS']);

						}
						
						if($fileName == '教材销退明细'){
							$row2['GHDW'] = iconv('UTF-8', 'GBK', $v['GHDW']);
							$row2['GHDWMC'] = iconv('UTF-8', 'GBK', $v['GHDWMC']);
							$row2['YWBMBH'] = iconv('UTF-8', 'GBK', $v['YWBMBH']);
							$row2['KFBH'] = iconv('UTF-8', 'GBK', $v['KFBH']);
							$row2['DH'] = iconv('UTF-8', 'GBK', $v['DH']);
							$row2['DM'] = iconv('UTF-8', 'GBK', $v['DM']);
							$row2['PCH'] = iconv('UTF-8', 'GBK', $v['PCH']);
							$row2['THRQ'] = iconv('UTF-8', 'GBK', $v['THRQ']);
							$row2['ZMY'] = iconv('UTF-8', 'GBK', $v['ZMY']);
							$row2['ZSY'] = iconv('UTF-8', 'GBK', $v['ZSY']);
							$row2['ZCB'] = iconv('UTF-8', 'GBK', $v['ZCB']);
							$row2['TSLSH'] = iconv('UTF-8', 'GBK', $v['TSLSH']);
							$row2['JSRQ'] = iconv('UTF-8', 'GBK', $v['JSRQ']);
							$row2['JSLX'] = iconv('UTF-8', 'GBK', $v['JSLX']);
							$row2['ZDQH'] = iconv('UTF-8', 'GBK', $v['ZDQH']);
							$row2['NF'] = iconv('UTF-8', 'GBK', $v['NF']);
							$row2['NFHRQ'] = iconv('UTF-8', 'GBK', $v['NFHRQ']);
							$row2['NJSRQ'] = iconv('UTF-8', 'GBK', $v['NJSRQ']);
							$row2['JB'] = iconv('UTF-8', 'GBK', $v['JB']);
							$row2['CWQRPC'] = iconv('UTF-8', 'GBK', $v['CWQRPC']);
							$row2['ZCS'] = iconv('UTF-8', 'GBK', $v['ZCS']);
							$row2['DJ'] = iconv('UTF-8', 'GBK', $v['DJ']);
							$row2['ID'] = iconv('UTF-8', 'GBK', $v['ID']);
							$row2['BB'] = iconv('UTF-8', 'GBK', $v['BB']);
							$row2['SM'] = iconv('UTF-8', 'GBK', $v['SM']);
							$row2['ISBN'] = iconv('UTF-8', 'GBK', $v['ISBN']);
							$row2['CBNY'] = iconv('UTF-8', 'GBK', $v['CBNY']);
						}

						if($fileName == '教材基层店免费让利明细'){
							$row2['YWBMBH'] = iconv('UTF-8', 'GBK', $v['YWBMBH']);
							$row2['KFBH'] = iconv('UTF-8', 'GBK', $v['KFBH']);
							$row2['DH'] = iconv('UTF-8', 'GBK', $v['DH']);
							$row2['DM'] = iconv('UTF-8', 'GBK', $v['DM']);
							$row2['GZPCH'] = iconv('UTF-8', 'GBK', $v['GZPCH']);
							$row2['GZRQ'] = iconv('UTF-8', 'GBK', $v['GZRQ']);
							$row2['ZMY'] = iconv('UTF-8', 'GBK', $v['ZMY']);
							$row2['ZSY'] = iconv('UTF-8', 'GBK', $v['ZSY']);
							$row2['ZCB'] = iconv('UTF-8', 'GBK', $v['ZCB']);
							$row2['TSLSH'] = iconv('UTF-8', 'GBK', $v['TSLSH']);
							$row2['JSRQ'] = iconv('UTF-8', 'GBK', $v['JSRQ']);
							$row2['BZ'] = iconv('UTF-8', 'GBK', $v['BZ']);
							$row2['JSLX'] = iconv('UTF-8', 'GBK', $v['JSLX']);
							$row2['ZDQH'] = iconv('UTF-8', 'GBK', $v['ZDQH']);
							$row2['NF'] = iconv('UTF-8', 'GBK', $v['NF']);
							$row2['NFHRQ'] = iconv('UTF-8', 'GBK', $v['NFHRQ']);
							$row2['NJSRQ'] = iconv('UTF-8', 'GBK', $v['NJSRQ']);
							$row2['JB'] = iconv('UTF-8', 'GBK', $v['JB']);
							$row2['ZCS'] = iconv('UTF-8', 'GBK', $v['ZCS']);

						}
						
						if($fileName == '教材发货明细'){
							$row2['CBNY'] = iconv('UTF-8', 'GBK', $v['CBNY']);
							$row2['GHDW'] = iconv('UTF-8', 'GBK', $v['GHDW']);
							$row2['GHDWMC'] = iconv('UTF-8', 'GBK', $v['GHDWMC']);
							$row2['YWBMBH'] = iconv('UTF-8', 'GBK', $v['YWBMBH']);
							$row2['DH'] = iconv('UTF-8', 'GBK', $v['DH']);
							$row2['MC'] = iconv('UTF-8', 'GBK', $v['MC']);
							$row2['PFPCH'] = iconv('UTF-8', 'GBK', $v['PFPCH']);
							$row2['BZ'] = iconv('UTF-8', 'GBK', $v['BZ']);
							$row2['DBRQ'] = iconv('UTF-8', 'GBK', $v['DBRQ']);
							$row2['PFRQ'] = iconv('UTF-8', 'GBK', $v['PFRQ']);
							$row2['ZMY'] = iconv('UTF-8', 'GBK', $v['ZMY']);
							$row2['ZSY'] = iconv('UTF-8', 'GBK', $v['ZSY']);
							$row2['CBJ'] = iconv('UTF-8', 'GBK', $v['CBJ']);
							$row2['TSLSH'] = iconv('UTF-8', 'GBK', $v['TSLSH']);
							$row2['JSRQ'] = iconv('UTF-8', 'GBK', $v['JSRQ']);
							$row2['SM'] = iconv('UTF-8', 'GBK', $v['SM']);
							$row2['DJ'] = iconv('UTF-8', 'GBK', $v['DJ']);
							$row2['ZDQH'] = iconv('UTF-8', 'GBK', $v['ZDQH']);
							$row2['ZDXH'] = iconv('UTF-8', 'GBK', $v['ZDXH']);
							$row2['KFBH'] = iconv('UTF-8', 'GBK', $v['KFBH']);
							$row2['JSLX'] = iconv('UTF-8', 'GBK', $v['JSLX']);
							$row2['NF'] = iconv('UTF-8', 'GBK', $v['NF']);
							$row2['NFHRQ'] = iconv('UTF-8', 'GBK', $v['NFHRQ']);
							$row2['NJSRQ'] = iconv('UTF-8', 'GBK', $v['NJSRQ']);
							$row2['JB'] = iconv('UTF-8', 'GBK', $v['JB']);
							$row2['CWQRPC'] = iconv('UTF-8', 'GBK', $v['CWQRPC']);
							$row2['YSDBRQ'] = iconv('UTF-8', 'GBK', $v['YSDBRQ']);
							$row2['ZCS'] = iconv('UTF-8', 'GBK', $v['ZCS']);
							
						}

					
						if($fileName == '教材库存明细'){
							$row2['YWBMBH'] = iconv('UTF-8', 'GBK', $v['YWBMBH']);
							$row2['ID'] = iconv('UTF-8', 'GBK', $v['ID']);
							$row2['SM'] = iconv('UTF-8', 'GBK', $v['SM']);
							$row2['DJ'] = iconv('UTF-8', 'GBK', $v['DJ']);
							$row2['BB'] = iconv('UTF-8', 'GBK', $v['BB']);
							$row2['KCCS'] = iconv('UTF-8', 'GBK', $v['KCCS']);
							$row2['MY'] = iconv('UTF-8', 'GBK', $v['MY']);
							$row2['SYS'] = iconv('UTF-8', 'GBK', $v['SYS']);
							$row2['KF'] = iconv('UTF-8', 'GBK', $v['KF']);
							$row2['WL'] = iconv('UTF-8', 'GBK', $v['WL']);
							$row2['FL'] = iconv('UTF-8', 'GBK', $v['FL']);
							$row2['HW'] = iconv('UTF-8', 'GBK', $v['HW']);
							$row2['GHDW'] = iconv('UTF-8', 'GBK', $v['GHDW']);
							$row2['YWY'] = iconv('UTF-8', 'GBK', $v['YWY']);
							$row2['ISBN'] = iconv('UTF-8', 'GBK', $v['ISBN']);
							$row2['BC'] = iconv('UTF-8', 'GBK', $v['BC']);
							$row2['BIANZHE'] = iconv('UTF-8', 'GBK', $v['BIANZHE']);
							$row2['NIAN'] = iconv('UTF-8', 'GBK', $v['NIAN']);
	
						}
						if($fileName == '教材库存明细提前出'){
							$row2['YWBMBH'] = iconv('UTF-8', 'GBK', $v['YWBMBH']);
							$row2['ID'] = iconv('UTF-8', 'GBK', $v['ID']);
							$row2['SM'] = iconv('UTF-8', 'GBK', $v['SM']);
							$row2['DJ'] = iconv('UTF-8', 'GBK', $v['DJ']);
							$row2['BB'] = iconv('UTF-8', 'GBK', $v['BB']);
							$row2['KCCS'] = iconv('UTF-8', 'GBK', $v['KCCS']);
							$row2['MY'] = iconv('UTF-8', 'GBK', $v['MY']);
							$row2['SYS'] = iconv('UTF-8', 'GBK', $v['SYS']);
							$row2['KF'] = iconv('UTF-8', 'GBK', $v['KF']);
							$row2['WL'] = iconv('UTF-8', 'GBK', $v['WL']);
							$row2['FL'] = iconv('UTF-8', 'GBK', $v['FL']);
							$row2['HW'] = iconv('UTF-8', 'GBK', $v['HW']);
							$row2['GHDW'] = iconv('UTF-8', 'GBK', $v['GHDW']);
							$row2['YWY'] = iconv('UTF-8', 'GBK', $v['YWY']);
							$row2['ISBN'] = iconv('UTF-8', 'GBK', $v['ISBN']);
							$row2['BC'] = iconv('UTF-8', 'GBK', $v['BC']);
							$row2['BIANZHE'] = iconv('UTF-8', 'GBK', $v['BIANZHE']);
							$row2['NIAN'] = iconv('UTF-8', 'GBK', $v['NIAN']);
	
						}
						
						if($fileName == '教材配发中间态'){
							$row2['YWBMBH'] = iconv('UTF-8', 'GBK', $v['YWBMBH']);
							$row2['YWBMMC'] = iconv('UTF-8', 'GBK', $v['YWBMMC']);
							$row2['DH'] = iconv('UTF-8', 'GBK', $v['DH']);
							$row2['DM'] = iconv('UTF-8', 'GBK', $v['DM']);
							$row2['ID'] = iconv('UTF-8', 'GBK', $v['ID']);
							$row2['ISBN'] = iconv('UTF-8', 'GBK', $v['ISBN']);
							$row2['SM'] = iconv('UTF-8', 'GBK', $v['SM']);
							$row2['DJ'] = iconv('UTF-8', 'GBK', $v['DJ']);
							$row2['CBNY'] = iconv('UTF-8', 'GBK', $v['CBNY']);
							$row2['PXCS'] = iconv('UTF-8', 'GBK', $v['PXCS']);
							$row2['ZMY'] = iconv('UTF-8', 'GBK', $v['ZMY']);
							$row2['SYS'] = iconv('UTF-8', 'GBK', $v['SYS']);
							$row2['CBJ'] = iconv('UTF-8', 'GBK', $v['CBJ']);
							$row2['PXDH'] = iconv('UTF-8', 'GBK', $v['PXDH']);
							$row2['BZ'] = iconv('UTF-8', 'GBK', $v['BZ']);
							$row2['ZDH'] = iconv('UTF-8', 'GBK', $v['ZDH']);
							$row2['SYLX'] = iconv('UTF-8', 'GBK', $v['SYLX']);
							$row2['SL'] = iconv('UTF-8', 'GBK', $v['SL']);
							$row2['ZJXS'] = iconv('UTF-8', 'GBK', $v['ZJXS']);
							$row2['QX'] = iconv('UTF-8', 'GBK', $v['QX']);
							$row2['DJBH'] = iconv('UTF-8', 'GBK', $v['DJBH']);
							$row2['KFBH'] = iconv('UTF-8', 'GBK', $v['KFBH']);
							$row2['BJLSH'] = iconv('UTF-8', 'GBK', $v['BJLSH']);
							$row2['CYBH'] = iconv('UTF-8', 'GBK', $v['CYBH']);
							$row2['FLOWID'] = iconv('UTF-8', 'GBK', $v['FLOWID']);
							$row2['ZDQH'] = iconv('UTF-8', 'GBK', $v['ZDQH']);
							$row2['ZDXH'] = iconv('UTF-8', 'GBK', $v['ZDXH']);
							$row2['ZDQH1'] = iconv('UTF-8', 'GBK', $v['ZDQH1']);
							$row2['ZDXH1'] = iconv('UTF-8', 'GBK', $v['ZDXH1']);
							$row2['PFRQ'] = iconv('UTF-8', 'GBK', $v['PFRQ']);
						}
						
						if($fileName == '教材库房转移中间态明细'){
							$row2['ID'] = iconv('UTF-8', 'GBK', $v['ID']);
							$row2['SM'] = iconv('UTF-8', 'GBK', $v['SM']);
							$row2['ISBN'] = iconv('UTF-8', 'GBK', $v['ISBN']);
							$row2['DJ'] = iconv('UTF-8', 'GBK', $v['DJ']);
							$row2['BB'] = iconv('UTF-8', 'GBK', $v['BB']);
							$row2['GHDW'] = iconv('UTF-8', 'GBK', $v['GHDW']);
							$row2['YWY'] = iconv('UTF-8', 'GBK', $v['YWY']);
							$row2['CBNY'] = iconv('UTF-8', 'GBK', $v['CBNY']);
							$row2['YWLX'] = iconv('UTF-8', 'GBK', $v['YWLX']);
							$row2['CS'] = iconv('UTF-8', 'GBK', $v['SUM(CS)']);
							$row2['MY'] = iconv('UTF-8', 'GBK', $v['MY']);
							$row2['SY'] = iconv('UTF-8', 'GBK', $v['SY']);
						}
						
						if($fileName == '教材损益明细'){
							$row2['KFBH'] = iconv('UTF-8', 'GBK', $v['KFBH']);
							$row2['SM'] = iconv('UTF-8', 'GBK', $v['SM']);
							$row2['CRKCS'] = iconv('UTF-8', 'GBK', $v['CRKCS']);
							$row2['DJ'] = iconv('UTF-8', 'GBK', $v['DJ']); 
							$row2['ZMY'] = iconv('UTF-8', 'GBK', $v['ZMY']);
							$row2['CRKSY'] = iconv('UTF-8', 'GBK', $v['CRKSY']);
							$row2['CRKRQ'] = iconv('UTF-8', 'GBK', $v['CRKRQ']);
	
						}
						if($fileName == '教材当月已结算-到货已结-到货'){
							$row2['YWBMBH'] = iconv('UTF-8', 'GBK', $v['YWBMBH']);
							$row2['GHDWH'] = iconv('UTF-8', 'GBK', $v['GHDWH']);
							$row2['MC'] = iconv('UTF-8', 'GBK', $v['MC']);
							$row2['DHPCH'] = iconv('UTF-8', 'GBK', $v['DHPCH']);
							$row2['SHRQ'] = iconv('UTF-8', 'GBK', $v['SHRQ']);
							$row2['YSDJ'] = iconv('UTF-8', 'GBK', $v['YSDJ']);
							$row2['SSDH'] = iconv('UTF-8', 'GBK', $v['SSDH']);
							$row2['ZDQH'] = iconv('UTF-8', 'GBK', $v['ZDQH']);
							$row2['ZDXH'] = iconv('UTF-8', 'GBK', $v['ZDXH']);
							$row2['SM'] = iconv('UTF-8', 'GBK', $v['SM']);
							$row2['DJ'] = iconv('UTF-8', 'GBK', $v['DJ']);
							$row2['JXSSSL'] = iconv('UTF-8', 'GBK', $v['JXSSSL']);
							$row2['MY'] = iconv('UTF-8', 'GBK', $v['MY']);
							$row2['SYS'] = iconv('UTF-8', 'GBK', $v['SYS']);
							$row2['ZDPCH'] = iconv('UTF-8', 'GBK', $v['ZDPCH']);
							$row2['JSRQ'] = iconv('UTF-8', 'GBK', $v['JSRQ']);
							$row2['JSLX'] = iconv('UTF-8', 'GBK', $v['JSLX']);
							$row2['ZDPCH1'] = iconv('UTF-8', 'GBK', $v['ZDPCH1']);
							$row2['TSLSH'] = iconv('UTF-8', 'GBK', $v['TSLSH']);
							$row2['YSRQ'] = iconv('UTF-8', 'GBK', $v['YSRQ']);
							$row2['YSDBRQ'] = iconv('UTF-8', 'GBK', $v['YSDBRQ']);
							$row2['JCDRQ'] = iconv('UTF-8', 'GBK', $v['JCDRQ']);
							$row2['KQBJ'] = iconv('UTF-8', 'GBK', $v['KQBJ']);
							$row2['JSGHDWH'] = iconv('UTF-8', 'GBK', $v['JSGHDWH']);
							$row2['YSGHDWH'] = iconv('UTF-8', 'GBK', $v['YSGHDWH']);
							$row2['FLOWID_DHMX'] = iconv('UTF-8', 'GBK', $v['FLOWID_DHMX']);
						}
						if($fileName == '教材当月已结算-到货已结-进退'){
							$row2['YWBMBH'] = iconv('UTF-8', 'GBK', $v['YWBMBH']);
							$row2['GHDWH'] = iconv('UTF-8', 'GBK', $v['GHDWH']);
							$row2['MC'] = iconv('UTF-8', 'GBK', $v['MC']);
							$row2['DBRQ'] = iconv('UTF-8', 'GBK', $v['DBRQ']);
							$row2['ID'] = iconv('UTF-8', 'GBK', $v['ID']);
							$row2['SM'] = iconv('UTF-8', 'GBK', $v['SM']);
							$row2['DJ'] = iconv('UTF-8', 'GBK', $v['DJ']);
							$row2['ZMY'] = iconv('UTF-8', 'GBK', $v['ZMY']);
							$row2['ZSY'] = iconv('UTF-8', 'GBK', $v['ZSY']);
							$row2['BZ'] = iconv('UTF-8', 'GBK', $v['BZ']);
							$row2['YWPCH'] = iconv('UTF-8', 'GBK', $v['YWPCH']);
							$row2['ZDPCH'] = iconv('UTF-8', 'GBK', $v['ZDPCH']);
							$row2['JSRQ'] = iconv('UTF-8', 'GBK', $v['JSRQ']);
							$row2['JSLX'] = iconv('UTF-8', 'GBK', $v['JSLX']);
							$row2['ZDQH'] = iconv('UTF-8', 'GBK', $v['ZDQH']);
							$row2['ZDPCH1'] = iconv('UTF-8', 'GBK', $v['ZDPCH1']);
							$row2['TSLSH'] = iconv('UTF-8', 'GBK', $v['TSLSH']);
							$row2['NF'] = iconv('UTF-8', 'GBK', $v['NF']);
							$row2['NFHRQ'] = iconv('UTF-8', 'GBK', $v['NFHRQ']);
							$row2['NJSRQ'] = iconv('UTF-8', 'GBK', $v['NJSRQ']);
							$row2['JSGHDWH'] = iconv('UTF-8', 'GBK', $v['JSGHDWH']);
							$row2['YSGHDWH'] = iconv('UTF-8', 'GBK', $v['YSGHDWH']);
							$row2['ZCS'] = iconv('UTF-8', 'GBK', $v['ZCS']);
						}
						if($fileName == '教材当月已结算-到货已结-退货'){
							$row2['YWBMBH'] = iconv('UTF-8', 'GBK', $v['YWBMBH']);
							$row2['GHDWH'] = iconv('UTF-8', 'GBK', $v['GHDWH']);
							$row2['MC'] = iconv('UTF-8', 'GBK', $v['MC']);
							$row2['ID'] = iconv('UTF-8', 'GBK', $v['ID']);
							$row2['DBRQ'] = iconv('UTF-8', 'GBK', $v['DBRQ']);
							$row2['SM'] = iconv('UTF-8', 'GBK', $v['SM']);
							$row2['DJ'] = iconv('UTF-8', 'GBK', $v['DJ']);
							$row2['ZMY'] = iconv('UTF-8', 'GBK', $v['ZMY']);
							$row2['ZSY'] = iconv('UTF-8', 'GBK', $v['ZSY']);
							$row2['BZ'] = iconv('UTF-8', 'GBK', $v['BZ']);
							$row2['YWPCH'] = iconv('UTF-8', 'GBK', $v['YWPCH']);
							$row2['JSRQ'] = iconv('UTF-8', 'GBK', $v['JSRQ']);
							$row2['ZDPCH'] = iconv('UTF-8', 'GBK', $v['ZDPCH']);
							$row2['JSLX'] = iconv('UTF-8', 'GBK', $v['JSLX']);
							$row2['ZDQH'] = iconv('UTF-8', 'GBK', $v['ZDQH']);
							$row2['ZDXH'] = iconv('UTF-8', 'GBK', $v['ZDXH']);
							$row2['ZDPCH1'] = iconv('UTF-8', 'GBK', $v['ZDPCH1']);
							$row2['TSLSH'] = iconv('UTF-8', 'GBK', $v['TSLSH']);
							$row2['NF'] = iconv('UTF-8', 'GBK', $v['NF']);
							$row2['NFHRQ'] = iconv('UTF-8', 'GBK', $v['NFHRQ']);
							$row2['NJSRQ'] = iconv('UTF-8', 'GBK', $v['NJSRQ']);
							$row2['JSGHDWH'] = iconv('UTF-8', 'GBK', $v['JSGHDWH']);
							$row2['YSGHDWH'] = iconv('UTF-8', 'GBK', $v['YSGHDWH']);
							$row2['ZCS'] = iconv('UTF-8', 'GBK', $v['ZCS']);
							
						}
						if($fileName == '教材当月已结算-到货已结-更正'){
							$row2['YWBMBH'] = iconv('UTF-8', 'GBK', $v['YWBMBH']);
							$row2['DH'] = iconv('UTF-8', 'GBK', $v['DH']);
							$row2['MC'] = iconv('UTF-8', 'GBK', $v['MC']);
							$row2['PFPCH'] = iconv('UTF-8', 'GBK', $v['PFPCH']);
							$row2['BZ'] = iconv('UTF-8', 'GBK', $v['BZ']);
							$row2['DBRQ'] = iconv('UTF-8', 'GBK', $v['DBRQ']);
							$row2['PFRQ'] = iconv('UTF-8', 'GBK', $v['PFRQ']);
							$row2['ZMY'] = iconv('UTF-8', 'GBK', $v['ZMY']);
							$row2['ZSY'] = iconv('UTF-8', 'GBK', $v['ZSY']);
							$row2['CBJ'] = iconv('UTF-8', 'GBK', $v['CBJ']);
							$row2['TSLSH'] = iconv('UTF-8', 'GBK', $v['TSLSH']);
							$row2['JSRQ'] = iconv('UTF-8', 'GBK', $v['JSRQ']);
							$row2['SM'] = iconv('UTF-8', 'GBK', $v['SM']);
							$row2['DJ'] = iconv('UTF-8', 'GBK', $v['DJ']);
							$row2['ZDQH'] = iconv('UTF-8', 'GBK', $v['ZDQH']);
							$row2['ZDXH'] = iconv('UTF-8', 'GBK', $v['ZDXH']);
							$row2['ZDPCH1'] = iconv('UTF-8', 'GBK', $v['ZDPCH1']);
							$row2['KFBH'] = iconv('UTF-8', 'GBK', $v['KFBH']);
							$row2['JSLX'] = iconv('UTF-8', 'GBK', $v['JSLX']);
							$row2['NF'] = iconv('UTF-8', 'GBK', $v['NF']);
							$row2['NFHRQ'] = iconv('UTF-8', 'GBK', $v['NFHRQ']);
							$row2['NJSRQ'] = iconv('UTF-8', 'GBK', $v['NJSRQ']);
							$row2['JB'] = iconv('UTF-8', 'GBK', $v['JB']);
							$row2['CWQRPC'] = iconv('UTF-8', 'GBK', $v['CWQRPC']);
							$row2['YSDBRQ'] = iconv('UTF-8', 'GBK', $v['YSDBRQ']);
							$row2['ZCS'] = iconv('UTF-8', 'GBK', $v['ZCS']);
						}
						if($fileName == '教材当月已结算-发货已结-到货'){
							$row2['CBNY'] = iconv('UTF-8', 'GBK', $v['CBNY']);
							$row2['GHDW'] = iconv('UTF-8', 'GBK', $v['GHDW']);
							$row2['GHDWMC'] = iconv('UTF-8', 'GBK', $v['GHDWMC']);
							$row2['YWBMBH'] = iconv('UTF-8', 'GBK', $v['YWBMBH']);
							$row2['DH'] = iconv('UTF-8', 'GBK', $v['DH']);
							$row2['MC'] = iconv('UTF-8', 'GBK', $v['MC']);
							$row2['PFPCH'] = iconv('UTF-8', 'GBK', $v['PFPCH']);
							$row2['BZ'] = iconv('UTF-8', 'GBK', $v['BZ']);
							$row2['DBRQ'] = iconv('UTF-8', 'GBK', $v['DBRQ']);
							$row2['PFRQ'] = iconv('UTF-8', 'GBK', $v['PFRQ']);
							$row2['ZMY'] = iconv('UTF-8', 'GBK', $v['ZMY']);
							$row2['ZSY'] = iconv('UTF-8', 'GBK', $v['ZSY']);
							$row2['CBJ'] = iconv('UTF-8', 'GBK', $v['CBJ']);
							$row2['TSLSH'] = iconv('UTF-8', 'GBK', $v['TSLSH']);
							$row2['JSRQ'] = iconv('UTF-8', 'GBK', $v['JSRQ']);
							$row2['SM'] = iconv('UTF-8', 'GBK', $v['SM']);
							$row2['DJ'] = iconv('UTF-8', 'GBK', $v['DJ']);
							$row2['ZDQH'] = iconv('UTF-8', 'GBK', $v['ZDQH']);
							$row2['ZDXH'] = iconv('UTF-8', 'GBK', $v['ZDXH']);
							$row2['KFBH'] = iconv('UTF-8', 'GBK', $v['KFBH']);
							$row2['JSLX'] = iconv('UTF-8', 'GBK', $v['JSLX']);
							$row2['NF'] = iconv('UTF-8', 'GBK', $v['NF']);
							$row2['NFHRQ'] = iconv('UTF-8', 'GBK', $v['NFHRQ']);
							$row2['NJSRQ'] = iconv('UTF-8', 'GBK', $v['NJSRQ']);
							$row2['JB'] = iconv('UTF-8', 'GBK', $v['JB']);
							$row2['CWQRPC'] = iconv('UTF-8', 'GBK', $v['CWQRPC']);
							$row2['YSDBRQ'] = iconv('UTF-8', 'GBK', $v['YSDBRQ']);
							$row2['ZCS'] = iconv('UTF-8', 'GBK', $v['ZCS']);
						}
						if($fileName == '教材当月已结算-发货已结-退货'){
							$row2['CBNY'] = iconv('UTF-8', 'GBK', $v['CBNY']);
							$row2['GHDW'] = iconv('UTF-8', 'GBK', $v['GHDW']);
							$row2['GHDWMC'] = iconv('UTF-8', 'GBK', $v['GHDWMC']);
							$row2['YWBMBH'] = iconv('UTF-8', 'GBK', $v['YWBMBH']);
							$row2['KFBH'] = iconv('UTF-8', 'GBK', $v['KFBH']);
							$row2['DH'] = iconv('UTF-8', 'GBK', $v['DH']);
							$row2['DM'] = iconv('UTF-8', 'GBK', $v['DM']);
							$row2['PCH'] = iconv('UTF-8', 'GBK', $v['PCH']);
							$row2['THRQ'] = iconv('UTF-8', 'GBK', $v['THRQ']);
							$row2['ZMY'] = iconv('UTF-8', 'GBK', $v['ZMY']);
							$row2['ZSY'] = iconv('UTF-8', 'GBK', $v['ZSY']);
							$row2['ZCB'] = iconv('UTF-8', 'GBK', $v['ZCB']);
							$row2['TSLSH'] = iconv('UTF-8', 'GBK', $v['TSLSH']);
							$row2['JSRQ'] = iconv('UTF-8', 'GBK', $v['JSRQ']);
							$row2['JSLX'] = iconv('UTF-8', 'GBK', $v['JSLX']);
							$row2['ZDQH'] = iconv('UTF-8', 'GBK', $v['ZDQH']);
							$row2['NF'] = iconv('UTF-8', 'GBK', $v['NF']);
							$row2['NFHRQ'] = iconv('UTF-8', 'GBK', $v['NFHRQ']);
							$row2['NJSRQ'] = iconv('UTF-8', 'GBK', $v['NJSRQ']);
							$row2['JB'] = iconv('UTF-8', 'GBK', $v['JB']);
							$row2['CWQRPC'] = iconv('UTF-8', 'GBK', $v['CWQRPC']);
							$row2['ZCS'] = iconv('UTF-8', 'GBK', $v['ZCS']);
							$row2['DJ'] = iconv('UTF-8', 'GBK', $v['DJ']);
							$row2['ID'] = iconv('UTF-8', 'GBK', $v['ID']);
							$row2['BB'] = iconv('UTF-8', 'GBK', $v['BB']);
							$row2['SM'] = iconv('UTF-8', 'GBK', $v['SM']);
							$row2['ISBN'] = iconv('UTF-8', 'GBK', $v['ISBN']);
							$row2['CBNY'] = iconv('UTF-8', 'GBK', $v['CBNY']);
						}
						if($fileName == '教材当月已结算-发货已结-更正'){
							$row2['CBNY'] = iconv('UTF-8', 'GBK', $v['CBNY']);
							$row2['GHDW'] = iconv('UTF-8', 'GBK', $v['GHDW']);
							$row2['GHDWMC'] = iconv('UTF-8', 'GBK', $v['GHDWMC']);
							$row2['YWBMBH'] = iconv('UTF-8', 'GBK', $v['YWBMBH']);
							$row2['KFBH'] = iconv('UTF-8', 'GBK', $v['KFBH']);
							$row2['DH'] = iconv('UTF-8', 'GBK', $v['DH']);
							$row2['DM'] = iconv('UTF-8', 'GBK', $v['DM']);
							$row2['GZPCH'] = iconv('UTF-8', 'GBK', $v['GZPCH']);
							$row2['GZRQ'] = iconv('UTF-8', 'GBK', $v['GZRQ']);
							$row2['ZMY'] = iconv('UTF-8', 'GBK', $v['ZMY']);
							$row2['ZSY'] = iconv('UTF-8', 'GBK', $v['ZSY']);
							$row2['ZCB'] = iconv('UTF-8', 'GBK', $v['ZCB']);
							$row2['TSLSH'] = iconv('UTF-8', 'GBK', $v['TSLSH']);
							$row2['JSRQ'] = iconv('UTF-8', 'GBK', $v['JSRQ']);
							$row2['BZ'] = iconv('UTF-8', 'GBK', $v['BZ']);
							$row2['JSLX'] = iconv('UTF-8', 'GBK', $v['JSLX']);
							$row2['ZDQH'] = iconv('UTF-8', 'GBK', $v['ZDQH']);
							$row2['NF'] = iconv('UTF-8', 'GBK', $v['NF']);
							$row2['NFHRQ'] = iconv('UTF-8', 'GBK', $v['NFHRQ']);
							$row2['NJSRQ'] = iconv('UTF-8', 'GBK', $v['NJSRQ']);
							$row2['JB'] = iconv('UTF-8', 'GBK', $v['JB']);
							$row2['ZCS'] = iconv('UTF-8', 'GBK', $v['ZCS']);
						}
						
						
						if($fileName == '教材应付款-到货未结明细'){
							$row2['YWBMBH'] = iconv('UTF-8', 'GBK', $v['YWBMBH']);
							$row2['GHDWH'] = iconv('UTF-8', 'GBK', $v['GHDWH']);
							$row2['MC'] = iconv('UTF-8', 'GBK', $v['MC']);
							$row2['DHPCH'] = iconv('UTF-8', 'GBK', $v['DHPCH']);
							$row2['SHRQ'] = iconv('UTF-8', 'GBK', $v['SHRQ']);
							$row2['YSDJ'] = iconv('UTF-8', 'GBK', $v['YSDJ']);
							$row2['SSDH'] = iconv('UTF-8', 'GBK', $v['SSDH']);
							$row2['ZDQH'] = iconv('UTF-8', 'GBK', $v['ZDQH']);
							$row2['ZDXH'] = iconv('UTF-8', 'GBK', $v['ZDXH']);
							$row2['SM'] = iconv('UTF-8', 'GBK', $v['SM']);
							$row2['DJ'] = iconv('UTF-8', 'GBK', $v['DJ']);
							$row2['JXSSSL'] = iconv('UTF-8', 'GBK', $v['JXSSSL']);
							$row2['MY'] = iconv('UTF-8', 'GBK', $v['MY']);
							$row2['SYS'] = iconv('UTF-8', 'GBK', $v['SYS']);
							$row2['ZDPCH'] = iconv('UTF-8', 'GBK', $v['ZDPCH']);
							$row2['JSRQ'] = iconv('UTF-8', 'GBK', $v['JSRQ']);
							$row2['JSLX'] = iconv('UTF-8', 'GBK', $v['JSLX']);
							$row2['ZDPCH1'] = iconv('UTF-8', 'GBK', $v['ZDPCH1']);
							$row2['TSLSH'] = iconv('UTF-8', 'GBK', $v['TSLSH']);
							$row2['YSDBRQ'] = iconv('UTF-8', 'GBK', $v['YSDBRQ']);
							$row2['JCDRQ'] = iconv('UTF-8', 'GBK', $v['JCDRQ']);
							$row2['KQBJ'] = iconv('UTF-8', 'GBK', $v['KQBJ']);
							$row2['JSGHDWH'] = iconv('UTF-8', 'GBK', $v['JSGHDWH']);
							$row2['YSGHDWH'] = iconv('UTF-8', 'GBK', $v['YSGHDWH']);
							$row2['FLOWID_DHMX'] = iconv('UTF-8', 'GBK', $v['FLOWID_DHMX']);
							$row2['SHRQ'] = iconv('UTF-8', 'GBK', $v['SHRQ']);
							$row2['YSRQ'] = iconv('UTF-8', 'GBK', $v['YSRQ']);
						}
						if($fileName == '教材应付款-进退未结明细'){
							$row2['YWBMBH'] = iconv('UTF-8', 'GBK', $v['YWBMBH']);
							$row2['GHDWH'] = iconv('UTF-8', 'GBK', $v['GHDWH']);
							$row2['MC'] = iconv('UTF-8', 'GBK', $v['MC']);
							$row2['DBRQ'] = iconv('UTF-8', 'GBK', $v['DBRQ']);
							$row2['ID'] = iconv('UTF-8', 'GBK', $v['ID']);
							$row2['SM'] = iconv('UTF-8', 'GBK', $v['SM']);
							$row2['DJ'] = iconv('UTF-8', 'GBK', $v['DJ']);
							$row2['ZMY'] = iconv('UTF-8', 'GBK', $v['ZMY']);
							$row2['ZSY'] = iconv('UTF-8', 'GBK', $v['ZSY']);
							$row2['BZ'] = iconv('UTF-8', 'GBK', $v['BZ']);
							$row2['YWPCH'] = iconv('UTF-8', 'GBK', $v['YWPCH']);
							$row2['ZDPCH'] = iconv('UTF-8', 'GBK', $v['ZDPCH']);
							$row2['JSRQ'] = iconv('UTF-8', 'GBK', $v['JSRQ']);
							$row2['JSLX'] = iconv('UTF-8', 'GBK', $v['JSLX']);
							$row2['ZDQH'] = iconv('UTF-8', 'GBK', $v['ZDQH']);
							$row2['ZDXH'] = iconv('UTF-8', 'GBK', $v['ZDXH']);
							$row2['ZDPCH1'] = iconv('UTF-8', 'GBK', $v['ZDPCH1']);
							$row2['TSLSH'] = iconv('UTF-8', 'GBK', $v['TSLSH']);
							$row2['NF'] = iconv('UTF-8', 'GBK', $v['NF']);
							$row2['NFHRQ'] = iconv('UTF-8', 'GBK', $v['NFHRQ']);
							$row2['NJSRQ'] = iconv('UTF-8', 'GBK', $v['NJSRQ']);
							$row2['JSGHDWH'] = iconv('UTF-8', 'GBK', $v['JSGHDWH']);
							$row2['YSGHDWH'] = iconv('UTF-8', 'GBK', $v['YSGHDWH']);
							$row2['ZCS'] = iconv('UTF-8', 'GBK', $v['ZCS']);
							
						}
						if($fileName == '教材应付款-出版社让利未结'){
							$row2['YWBMBH'] = iconv('UTF-8', 'GBK', $v['YWBMBH']);
							$row2['GHDWH'] = iconv('UTF-8', 'GBK', $v['GHDWH']);
							$row2['MC'] = iconv('UTF-8', 'GBK', $v['MC']);
							$row2['DHPCH'] = iconv('UTF-8', 'GBK', $v['DHPCH']);
							$row2['SHRQ'] = iconv('UTF-8', 'GBK', $v['SHRQ']);
							$row2['YSDJ'] = iconv('UTF-8', 'GBK', $v['YSDJ']);
							$row2['SSDH'] = iconv('UTF-8', 'GBK', $v['SSDH']);
							$row2['ZDQH'] = iconv('UTF-8', 'GBK', $v['ZDQH']);
							$row2['ZDXH'] = iconv('UTF-8', 'GBK', $v['ZDXH']);
							$row2['SM'] = iconv('UTF-8', 'GBK', $v['SM']);
							$row2['BZ'] = iconv('UTF-8', 'GBK', $v['BZ']);
							$row2['DJ'] = iconv('UTF-8', 'GBK', $v['DJ']);
							$row2['JXSSSL'] = iconv('UTF-8', 'GBK', $v['JXSSSL']);
							$row2['MY'] = iconv('UTF-8', 'GBK', 0);
							$row2['SYS'] = iconv('UTF-8', 'GBK', $v['SYS']);
							$row2['ZDPCH'] = iconv('UTF-8', 'GBK', $v['ZDPCH']);
							$row2['JSRQ'] = iconv('UTF-8', 'GBK', $v['JSRQ']);
							$row2['JSLX'] = iconv('UTF-8', 'GBK', $v['JSLX']);
							$row2['JSGHDWH'] = iconv('UTF-8', 'GBK', $v['JSGHDWH']);
							$row2['YSGHDWH'] = iconv('UTF-8', 'GBK', $v['YSGHDWH']);
							$row2['TSLSH'] = iconv('UTF-8', 'GBK', $v['TSLSH']);
						}
						if($fileName == '教材应收款-发货未结明细'){
							$row2['CBNY'] = iconv('UTF-8', 'GBK', $v['CBNY']);
							$row2['GHDW'] = iconv('UTF-8', 'GBK', $v['GHDW']);
							$row2['GHDWMC'] = iconv('UTF-8', 'GBK', $v['GHDWMC']);
							$row2['YWBMBH'] = iconv('UTF-8', 'GBK', $v['YWBMBH']);
							$row2['DH'] = iconv('UTF-8', 'GBK', $v['DH']);
							$row2['MC'] = iconv('UTF-8', 'GBK', $v['MC']);
							$row2['PFPCH'] = iconv('UTF-8', 'GBK', $v['PFPCH']);
							$row2['BZ'] = iconv('UTF-8', 'GBK', $v['BZ']);
							$row2['DBRQ'] = iconv('UTF-8', 'GBK', $v['DBRQ']);
							$row2['PFRQ'] = iconv('UTF-8', 'GBK', $v['PFRQ']);
							$row2['ZMY'] = iconv('UTF-8', 'GBK', $v['ZMY']);
							$row2['ZSY'] = iconv('UTF-8', 'GBK', $v['ZSY']);
							$row2['CBJ'] = iconv('UTF-8', 'GBK', $v['CBJ']);
							$row2['TSLSH'] = iconv('UTF-8', 'GBK', $v['TSLSH']);
							$row2['JSRQ'] = iconv('UTF-8', 'GBK', $v['JSRQ']);
							$row2['SM'] = iconv('UTF-8', 'GBK', $v['SM']);
							$row2['DJ'] = iconv('UTF-8', 'GBK', $v['DJ']);
							$row2['ZDQH'] = iconv('UTF-8', 'GBK', $v['ZDQH']);
							$row2['ZDXH'] = iconv('UTF-8', 'GBK', $v['ZDXH']);
							$row2['KFBH'] = iconv('UTF-8', 'GBK', $v['KFBH']);
							$row2['JSLX'] = iconv('UTF-8', 'GBK', $v['JSLX']);
							$row2['NF'] = iconv('UTF-8', 'GBK', $v['NF']);
							$row2['NFHRQ'] = iconv('UTF-8', 'GBK', $v['NFHRQ']);
							$row2['NJSRQ'] = iconv('UTF-8', 'GBK', $v['NJSRQ']);
							$row2['JB'] = iconv('UTF-8', 'GBK', $v['JB']);
							$row2['CWQRPC'] = iconv('UTF-8', 'GBK', $v['CWQRPC']);
							$row2['YSDBRQ'] = iconv('UTF-8', 'GBK', $v['YSDBRQ']);
							$row2['ZCS'] = iconv('UTF-8', 'GBK', $v['ZCS']);
							
						}
						if($fileName == '教材应收款-销退未结明细'){
							$row2['CBNY'] = iconv('UTF-8', 'GBK', $v['CBNY']);
							$row2['GHDW'] = iconv('UTF-8', 'GBK', $v['GHDW']);
							$row2['GHDWMC'] = iconv('UTF-8', 'GBK', $v['GHDWMC']);
							$row2['YWBMBH'] = iconv('UTF-8', 'GBK', $v['YWBMBH']);
							$row2['KFBH'] = iconv('UTF-8', 'GBK', $v['KFBH']);
							$row2['DH'] = iconv('UTF-8', 'GBK', $v['DH']);
							$row2['DM'] = iconv('UTF-8', 'GBK', $v['DM']);
							$row2['PCH'] = iconv('UTF-8', 'GBK', $v['PCH']);
							$row2['THRQ'] = iconv('UTF-8', 'GBK', $v['THRQ']);
							$row2['ZMY'] = iconv('UTF-8', 'GBK', $v['ZMY']);
							$row2['ZSY'] = iconv('UTF-8', 'GBK', $v['ZSY']);
							$row2['ZCB'] = iconv('UTF-8', 'GBK', $v['ZCB']);
							$row2['TSLSH'] = iconv('UTF-8', 'GBK', $v['TSLSH']);
							$row2['JSRQ'] = iconv('UTF-8', 'GBK', $v['JSRQ']);
							$row2['JSLX'] = iconv('UTF-8', 'GBK', $v['JSLX']);
							$row2['ZDQH'] = iconv('UTF-8', 'GBK', $v['ZDQH']);
							$row2['NF'] = iconv('UTF-8', 'GBK', $v['NF']);
							$row2['NFHRQ'] = iconv('UTF-8', 'GBK', $v['NFHRQ']);
							$row2['NJSRQ'] = iconv('UTF-8', 'GBK', $v['NJSRQ']);
							$row2['JB'] = iconv('UTF-8', 'GBK', $v['JB']);
							$row2['CWQRPC'] = iconv('UTF-8', 'GBK', $v['CWQRPC']);
							$row2['ZCS'] = iconv('UTF-8', 'GBK', $v['ZCS']);
							$row2['DJ'] = iconv('UTF-8', 'GBK', $v['DJ']);

						}
						if($fileName == '教材应收款-更正未结明细'){
							$row2['YWBMBH'] = iconv('UTF-8', 'GBK', $v['YWBMBH']);
							$row2['KFBH'] = iconv('UTF-8', 'GBK', $v['KFBH']);
							$row2['DH'] = iconv('UTF-8', 'GBK', $v['DH']);
							$row2['DM'] = iconv('UTF-8', 'GBK', $v['DM']);
							$row2['GZPCH'] = iconv('UTF-8', 'GBK', $v['GZPCH']);
							$row2['GZRQ'] = iconv('UTF-8', 'GBK', $v['GZRQ']);
							$row2['ZMY'] = iconv('UTF-8', 'GBK', $v['ZMY']);
							$row2['ZSY'] = iconv('UTF-8', 'GBK', $v['ZSY']);
							$row2['ZCB'] = iconv('UTF-8', 'GBK', $v['ZCB']);
							$row2['TSLSH'] = iconv('UTF-8', 'GBK', $v['TSLSH']);
							$row2['JSRQ'] = iconv('UTF-8', 'GBK', $v['JSRQ']);
							$row2['BZ'] = iconv('UTF-8', 'GBK', $v['BZ']);
							$row2['JSLX'] = iconv('UTF-8', 'GBK', $v['JSLX']);
							$row2['ZDQH'] = iconv('UTF-8', 'GBK', $v['ZDQH']);
							$row2['NF'] = iconv('UTF-8', 'GBK', $v['NF']);
							$row2['NFHRQ'] = iconv('UTF-8', 'GBK', $v['NFHRQ']);
							$row2['NJSRQ'] = iconv('UTF-8', 'GBK', $v['NJSRQ']);
							$row2['JB'] = iconv('UTF-8', 'GBK', $v['JB']);
							$row2['ZCS'] = iconv('UTF-8', 'GBK', $v['ZCS']);

						}

					}

					
					if($yewugongsi==3){  //连锁
						if($fileName == '差异-更正差异'){
							$row2['ZMY'] = iconv('UTF-8', 'GBK', $v['ZMY']);
							$row2['ZSY'] = iconv('UTF-8', 'GBK', $v['ZSY']);
							$row2['YWBMBH'] = iconv('UTF-8', 'GBK', $v['YWBMBH']);
							
						}
						if($fileName == '差异-到货'){
							$row2['LX'] = iconv('UTF-8', 'GBK', $v['LX']);
							$row2['ZMY'] = iconv('UTF-8', 'GBK', $v['ZMY']);
							$row2['ZSY'] = iconv('UTF-8', 'GBK', $v['ZSY']);
							$row2['YWBMBH'] = iconv('UTF-8', 'GBK', $v['YWBMBH']);
	
						}
						if($fileName == '差异-发货'){
							$row2['LX'] = iconv('UTF-8', 'GBK', $v['LX']);
							$row2['SUM(ZMY)'] = iconv('UTF-8', 'GBK', $v['SUM(ZMY)']);
							$row2['SUM(CBJ)'] = iconv('UTF-8', 'GBK', $v['SUM(CBJ)']);
							$row2['YWBMBH'] = iconv('UTF-8', 'GBK', $v['YWBMBH']);
	
						}
						
						if($fileName == '库存明细'){
							$row2['YWBMBH'] = iconv('UTF-8', 'GBK', $v['业务部门编号']);
							$row2['ID'] = iconv('UTF-8', 'GBK', $v['ID']);
							$row2['ISBN'] = iconv('UTF-8', 'GBK', $v['ISBN']);
							$row2['SM'] = iconv('UTF-8', 'GBK', $v['书名']);
							$row2['BB'] = iconv('UTF-8', 'GBK', $v['版本']);
							$row2['BBMC'] = iconv('UTF-8', 'GBK', $v['版本名称']);
							$row2['CS'] = iconv('UTF-8', 'GBK', $v['册数']);
							$row2['MY'] = iconv('UTF-8', 'GBK', $v['码洋']);
							$row2['SY'] = iconv('UTF-8', 'GBK', $v['实样']);
							$row2['BHSSY'] = iconv('UTF-8', 'GBK', $v['不含税实样']);
							$row2['DJ'] = iconv('UTF-8', 'GBK', $v['定价']);
							$row2['GHDW'] = iconv('UTF-8', 'GBK', $v['供货单位']);
							$row2['GHDWMC'] = iconv('UTF-8', 'GBK', $v['供货单位名称']);
							$row2['YSNY'] = iconv('UTF-8', 'GBK', $v['原始年月']);
							$row2['CBNY'] = iconv('UTF-8', 'GBK', $v['出版年月']);
							$row2['BC'] = iconv('UTF-8', 'GBK', $v['版次']);
							$row2['FL'] = iconv('UTF-8', 'GBK', $v['分类']);
							$row2['FLBH'] = iconv('UTF-8', 'GBK', $v['分类编号']);
							$row2['LBBH'] = iconv('UTF-8', 'GBK', $v['类别编号']);
							$row2['LB'] = iconv('UTF-8', 'GBK', $v['类别']);
	
						}
						if($fileName == '应付款-采购未结-明细'){
							$row2['DBRQ'] = iconv('UTF-8', 'GBK', $v['DBRQ']);
							$row2['YSDJ'] = iconv('UTF-8', 'GBK', $v['YSDJ']);
							$row2['YSRQ'] = iconv('UTF-8', 'GBK', $v['YSRQ']);
							$row2['DM'] = iconv('UTF-8', 'GBK', $v['DM']);
							$row2['ZPZ'] = iconv('UTF-8', 'GBK', $v['ZPZ']);
							$row2['ZCS'] = iconv('UTF-8', 'GBK', $v['ZCS']);
							$row2['ZMY'] = iconv('UTF-8', 'GBK', $v['ZMY']);
							$row2['ZSY'] = iconv('UTF-8', 'GBK', $v['ZSY']);
							$row2['BHSZSY'] = iconv('UTF-8', 'GBK', $v['BHSZSY']);
							$row2['YWBMMC'] = iconv('UTF-8', 'GBK', $v['YWBMMC']);
							$row2['BZ'] = iconv('UTF-8', 'GBK', $v['BZ']);
							$row2['DJLX'] = iconv('UTF-8', 'GBK', $v['DJLX']);
							$row2['SL1'] = iconv('UTF-8', 'GBK', $v['SL1']);
	
						}
						if($fileName == '应收款-发出未结-明细'){
							$row2['DBRQ'] = iconv('UTF-8', 'GBK', $v['DBRQ']);
							$row2['PFPCH'] = iconv('UTF-8', 'GBK', $v['PFPCH']);
							$row2['DM'] = iconv('UTF-8', 'GBK', $v['DM']);
							$row2['JSDM'] = iconv('UTF-8', 'GBK', $v['JSDM']);
							$row2['ZPZ'] = iconv('UTF-8', 'GBK', $v['ZPZ']);
							$row2['ZCS'] = iconv('UTF-8', 'GBK', $v['ZCS']);
							$row2['ZMY'] = iconv('UTF-8', 'GBK', $v['ZMY']);
							$row2['ZSY'] = iconv('UTF-8', 'GBK', $v['ZSY']);
							$row2['CBJ'] = iconv('UTF-8', 'GBK', $v['CBJ']);
							$row2['BHSZSY'] = iconv('UTF-8', 'GBK', $v['BHSZSY']);
							$row2['BHSCBJ'] = iconv('UTF-8', 'GBK', $v['BHSCBJ']);
							$row2['YWBMMC'] = iconv('UTF-8', 'GBK', $v['YWBMMC']);
							$row2['YWBMBH'] = iconv('UTF-8', 'GBK', $v['YWBMBH']);
							$row2['BZ'] = iconv('UTF-8', 'GBK', $v['BZ']);
							$row2['DJLX'] = iconv('UTF-8', 'GBK', $v['DJLX']);
							$row2['SL1'] = iconv('UTF-8', 'GBK', $v['SL1']);
							$row2['DQ'] = iconv('UTF-8', 'GBK', $v['DQ']);

						}
						if($fileName == '应付款月报'){
							$row2['TJRQ'] = iconv('UTF-8', 'GBK', $v['QMRQ']);
							$row2['TJNY'] = iconv('UTF-8', 'GBK', $v['QCRQ']);
							$row2['GHDWH'] = '('.iconv('UTF-8', 'GBK', $v['GHDWH']).')'.iconv('UTF-8', 'GBK', $v['GHDWHMC']);
							$row2['YWBMBH'] = iconv('UTF-8', 'GBK', $v['YWBMBH']);
							$row2['QCMY'] = iconv('UTF-8', 'GBK', $v['QCMY']);
							$row2['QCSY'] = iconv('UTF-8', 'GBK', $v['QCSY']);
							$row2['BQZJMY'] = iconv('UTF-8', 'GBK', $v['BQZJMY']);
							$row2['BQZJSY'] = iconv('UTF-8', 'GBK', $v['BQZJSY']);
							$row2['BQJSMY'] = iconv('UTF-8', 'GBK', $v['BQJSMY']);
							$row2['BQJSSY'] = iconv('UTF-8', 'GBK', $v['BQJSSY']);
							$row2['QMMY'] = iconv('UTF-8', 'GBK', $v['QMMY']);
							$row2['QMSY'] = iconv('UTF-8', 'GBK', $v['QMSY']);

						}
						if($fileName == '应付款月报含税率'){
							$row2['QMRQ'] = iconv('UTF-8', 'GBK', $v['QMRQ']);
							$row2['QCRQ'] = iconv('UTF-8', 'GBK', $v['QCRQ']);
							$row2['YWBMBH'] = iconv('UTF-8', 'GBK', $v['YWBMBH']);
							$row2['GHDWH'] = '('.iconv('UTF-8', 'GBK', $v['GHDWH']).')'.iconv('UTF-8', 'GBK', $v['GHDWHMC']);
							$row2['SL'] = iconv('UTF-8', 'GBK', $v['SL']);
							$row2['QCMY'] = iconv('UTF-8', 'GBK', $v['QCMY']);
							$row2['QCSY'] = iconv('UTF-8', 'GBK', $v['QCSY']);
							$row2['BQZJMY'] = iconv('UTF-8', 'GBK', $v['BQZJMY']);
							$row2['BQZJSY'] = iconv('UTF-8', 'GBK', $v['BQZJSY']);
							$row2['BQJSMY'] = iconv('UTF-8', 'GBK', $v['BQJSMY']);
							$row2['BQJSSY'] = iconv('UTF-8', 'GBK', $v['BQJSSY']);
							$row2['QMMY'] = iconv('UTF-8', 'GBK', $v['QMMY']);
							$row2['QMSY'] = iconv('UTF-8', 'GBK', $v['QMSY']);
						}
						if($fileName == '应收款月报'){
							$row2['QMRQ'] = iconv('UTF-8', 'GBK', $v['QMRQ']);
							$row2['QCRQ'] = iconv('UTF-8', 'GBK', $v['QCRQ']);
							$row2['YWBMBH'] = iconv('UTF-8', 'GBK', $v['YWBMBH']);
							$row2['DH'] = iconv('UTF-8', 'GBK', $v['DH']);
							$row2['SL'] = iconv('UTF-8', 'GBK', $v['SL']);
							$row2['QCMY'] = iconv('UTF-8', 'GBK', $v['QCMY']);
							$row2['QCSY'] = iconv('UTF-8', 'GBK', $v['QCSY']);
							$row2['QCCBJ'] = iconv('UTF-8', 'GBK', $v['QCCBJ']);
							$row2['BQZJMY'] = iconv('UTF-8', 'GBK', $v['BQZJMY']);
							$row2['BQZJSY'] = iconv('UTF-8', 'GBK', $v['BQZJSY']);
							$row2['BQZJCBJ'] = iconv('UTF-8', 'GBK', $v['BQZJCBJ']);
							$row2['BQJSMY'] = iconv('UTF-8', 'GBK', $v['BQJSMY']);
							$row2['BQJSSY'] = iconv('UTF-8', 'GBK', $v['BQJSSY']);
							$row2['BQJSCBJ'] = iconv('UTF-8', 'GBK', $v['BQJSCBJ']);
							$row2['QMMY'] = iconv('UTF-8', 'GBK', $v['QMMY']);
							$row2['QMSY'] = iconv('UTF-8', 'GBK', $v['QMSY']);
							$row2['QMCBJ'] = iconv('UTF-8', 'GBK', $v['QMCBJ']);
						}
						if($fileName == '应收款月报含税率'){
							$row2['QMRQ'] = iconv('UTF-8', 'GBK', $v['QMRQ']);
							$row2['QCRQ'] = iconv('UTF-8', 'GBK', $v['QCRQ']);
							$row2['DH'] = iconv('UTF-8', 'GBK', $v['DH']);
							$row2['YWBMBH'] = iconv('UTF-8', 'GBK', $v['YWBMBH']);
							$row2['SL'] = iconv('UTF-8', 'GBK', $v['SL']);
							$row2['QCMY'] = iconv('UTF-8', 'GBK', $v['QCMY']);
							$row2['QCSY'] = iconv('UTF-8', 'GBK', $v['QCSY']);
							$row2['QCCBJ'] = iconv('UTF-8', 'GBK', $v['QCCBJ']);
							$row2['BQZJMY'] = iconv('UTF-8', 'GBK', $v['BQZJMY']);
							$row2['BQZJSY'] = iconv('UTF-8', 'GBK', $v['BQZJSY']);
							$row2['BQZJCBJ'] = iconv('UTF-8', 'GBK', $v['BQZJCBJ']);
							$row2['BQJSMY'] = iconv('UTF-8', 'GBK', $v['BQJSMY']);
							$row2['BQJSSY'] = iconv('UTF-8', 'GBK', $v['BQJSSY']);
							$row2['QMMY'] = iconv('UTF-8', 'GBK', $v['QMMY']);
							$row2['QMSY'] = iconv('UTF-8', 'GBK', $v['QMSY']);
							$row2['QMCBJ'] = iconv('UTF-8', 'GBK', $v['QMCBJ']);
							$row2['DQMY'] = iconv('UTF-8', 'GBK', $v['DQMY']);
							$row2['DQSY'] = iconv('UTF-8', 'GBK', $v['DQSY']);
							$row2['DQCBJ'] = iconv('UTF-8', 'GBK', $v['DQCBJ']);
							
						}
						if($fileName == '本期流水'){
							$row2['SYMY'] = iconv('UTF-8', 'GBK', $v['损益码洋']);
							$row2['SYSY'] = iconv('UTF-8', 'GBK', $v['损益实样']);
							$row2['BFMY'] = iconv('UTF-8', 'GBK', $v['报废码洋']);
							$row2['BFSY'] = iconv('UTF-8', 'GBK', $v['报废实样']);
							$row2['CDHMY'] = iconv('UTF-8', 'GBK', $v['纯到货码洋']);
							$row2['CDHSY'] = iconv('UTF-8', 'GBK', $v['纯到货实样']);
							$row2['CFHMY'] = iconv('UTF-8', 'GBK', $v['纯发货码洋']);
							$row2['CFHSY'] = iconv('UTF-8', 'GBK', $v['纯发货实洋']);
							
	
						}
						if($fileName == '汇总数据'){
							$row2['QCRQ'] = iconv('UTF-8', 'GBK', $v['期初日期']);
							$row2['QMRQ'] = iconv('UTF-8', 'GBK', $v['期末日期']);
							$row2['YWBMBH'] = iconv('UTF-8', 'GBK', $v['业务部门编号']);
							$row2['ZMY'] = iconv('UTF-8', 'GBK', $v['总码洋']);
							$row2['ZSY'] = iconv('UTF-8', 'GBK', $v['总实样']);
							$row2['CBJ'] = iconv('UTF-8', 'GBK', $v['成本价']);
							$row2['YWLX'] = iconv('UTF-8', 'GBK', $v['类型']);
	
						}
						
						
					}	
					fputcsv($fp, $row2);
				 }
				//每1万条数据就刷新缓冲区
				unset($row2);
				ob_flush();
				flush();
			}
		}
		exit;//20180731 edit by zhang 导出的表会有html页面显示，加一个exit
   }

   //连锁-库存明细 用getpage方法找不到数据 只好用 query
   	public function articleAccessLog1($sql,$fileName,$title,$total,$yewugongsi)   //需要 sql语句  表名 ，导出表的名称 ，设置标题$title  $total 数据总条数
	{
		set_time_limit(0);
		ini_set('memory_limit', '-1');
		ini_set("max_execution_time", "0");
		if($yewugongsi==3){
			$ip='172.30.153.63/xhsddb';
			$port='1521';
			$user= 'dbsl';
			$pass= 'dbsl';
			$charset='utf8';
			pc_base::load_app_class('oracle_admin','admin',0);//数据库
			$ora=new oracle_admin($user,$pass,$ip,$charset);
		}

		$total = $total;
		//$fileName = date('YmdHi', time());
		$fileName = $fileName;
		header('Content-Type: application/vnd.ms-execl');
		header('Content-Disposition: attachment;filename="' . $fileName . '.csv"');
		$begin = microtime(true);
		$fp = fopen('php://output', 'a');
		
		$step = ceil($total / $nums);
		$title = $title ;
		foreach($title as $key => $item) {
			$title[$key] = iconv('UTF-8', 'GBK', $item);
		}
		fputcsv($fp, $title);
			  $sql= $sql;
			  $row1 = $ora->query($sql);
			if($row1) {
				$row2=array();				
				foreach($row1 as $k => $v) {
					$row2['YWBMBH'] = iconv('UTF-8', 'GBK', $v['业务部门编号']);
					$row2['ID'] = iconv('UTF-8', 'GBK', $v['ID']);
					$row2['ISBN'] = iconv('UTF-8', 'GBK', $v['ISBN']);
					$row2['SM'] = iconv('UTF-8', 'GBK', $v['书名']);
					$row2['BB'] = iconv('UTF-8', 'GBK', $v['版本']);
					$row2['BBMC'] = iconv('UTF-8', 'GBK', $v['版本名称']);
					$row2['CS'] = iconv('UTF-8', 'GBK', $v['册数']);
					$row2['MY'] = iconv('UTF-8', 'GBK', $v['码洋']);
					$row2['SY'] = iconv('UTF-8', 'GBK', $v['实样']);
					$row2['BHSSY'] = iconv('UTF-8', 'GBK', $v['不含税实样']);
					$row2['DJ'] = iconv('UTF-8', 'GBK', $v['定价']);
					$row2['GHDW'] = iconv('UTF-8', 'GBK', $v['供货单位']);
					$row2['GHDWMC'] = iconv('UTF-8', 'GBK', $v['供货单位名称']);
					$row2['YSNY'] = iconv('UTF-8', 'GBK', $v['原始年月']);
					$row2['CBNY'] = iconv('UTF-8', 'GBK', $v['出版年月']);
					$row2['BC'] = iconv('UTF-8', 'GBK', $v['版次']);
					$row2['FL'] = iconv('UTF-8', 'GBK', $v['分类']);
					$row2['FLBH'] = iconv('UTF-8', 'GBK', $v['分类编号']);
					$row2['LBBH'] = iconv('UTF-8', 'GBK', $v['类别编号']);
					$row2['LB'] = iconv('UTF-8', 'GBK', $v['类别']);
					fputcsv($fp, $row2);
				}
				unset($row2);
				ob_flush();
				flush();
			}
		exit;
   }

   
   
   //yuebao()此方法导出导服务器的指定位置。
	/*需要sql语句 
    *表名 导出表的名称   
    *设置标题$title
	*$total 数据总条数
	*$timetype月份Y，季度J，年度N类型  YJN可不是我定的，我可不喜欢这样的命名。
	*/
	public function yuebao($sql,$fileName,$title,$total,$yewugongsi,$user_hwbm,$timetype)   
	{
		set_time_limit(0);
		ini_set('memory_limit', '-1');
		ini_set("max_execution_time", "0");
		$host='172.30.153.63';
		$ip='172.30.153.63/xhsddb';
		$port='1521';
		$user= 'dbjczc';
		$pass= 'dbjczc';
		$charset='utf8';
		pc_base::load_app_class('oracle_admin','admin',0);//数据库
		$ora=new oracle_admin($user,$pass,$ip,$charset);
		if($yewugongsi==3){
			$ip='172.30.153.63/xhsddb';
			$port='1521';
			$user= 'dbsl';
			$pass= 'dbsl';
			$charset='utf8';
			pc_base::load_app_class('oracle_admin','admin',0);//数据库
			$ora=new oracle_admin($user,$pass,$ip,$charset);
		}

		$total = $total;
		$fileName = $fileName;
		$user_hwbm = $user_hwbm; //登录的用户名
		//header('Content-Type: application/vnd.ms-execl');
		//header('Content-Disposition: attachment;filename="' . $fileName . '.csv"');
		 
		$begin = microtime(true);
		//打开php标准输出流
		//以写入追加的方式打开
		//$fp = fopen('php://output', 'a');
		//2018-08-02 edit by zhang  要把导出的文件存放到服务器，然后部门直接下载就好。
		$lujing = jcOption($timetype,$yewugongsi); // 获取到文件存放地址，以及文件名拼接需要的时间类型（月份，季度，年度）
		$path = PHPCMS_PATH.'uploadfile/'.$lujing['path'];
		
		//$path = PHPCMS_PATH.'uploadfile/csv/' ;
		if(!file_exists($path)) {
			//exit('不存在此路径');
            mkdir( iconv('UTF-8','GBK',$path), 0777, true );
        }
		$filepath = $path.$user_hwbm.'-'.iconv("UTF-8","gbk",$lujing['file_path']).'-'.iconv("UTF-8","gbk",$fileName).'.csv';  //$timetype 为一月份  一季度  1-6
		if($filepath){ 
			unlink($filepath); //如果存在此文件，那就删掉，然后下载最新的
		}
		$fp = fopen($filepath,"a"); //打开csv文件，如果不存在则创建
		
		//我们每次取1万条数据，分100步来执行
		//如果线上环境无法支持一次性读取1万条数据，可把$nums调小，$step相应增大。
		if($fileName=='库存明细' || $fileName=='应付款-采购未结-明细' || $fileName=='应收款-发出未结-明细'){
			$nums = 20000 ;
		}else{
			$nums = 5000 ;
		}
		$step = ceil($total / $nums);
		//$step=10;
		//设置标题
		//$title = array('发运计划批次', '发运批次','运号','收货店','发货部门','业务部门','中转部门','发运计划日期','发运日期','收货日期','总件数','重量','承运人','状态','业务类型','车号','微信昵称','签收地','图片','异常','异常处理');
		$title = $title ;
		foreach($title as $key => $item) {
			$title[$key] = iconv('UTF-8', 'GBK', $item);
		}
		//将标题写到标准输出中
		fputcsv($fp, $title);
		 
		for($s = 1; $s <= $step; ++$s) {
			  $sql= $sql;
			  $row1 = $ora->getpage($sql,$total,$s,$nums);
				if($row1) {
                    $row2=array();				
					foreach($row1 as $k => $v) {
						
					if($yewugongsi==1){ //教辅
						if($fileName == '教辅月报'){
							$row2['YWBMBH'] = iconv('UTF-8', 'GBK', $v['YWBMBH']);
							$row2['LX'] = iconv('UTF-8', 'GBK', $v['LX']);
							$row2['BH'] = iconv('UTF-8', 'GBK', $v['BH']);
							$row2['DMMC'] = iconv('UTF-8', 'GBK', $v['DMMC']);
							$row2['QCWJMY'] = iconv('UTF-8', 'GBK', $v['QCWJMY']);
							$row2['QCWJSY'] = iconv('UTF-8', 'GBK', $v['QCWJSY']);
							$row2['QCWJCB'] = iconv('UTF-8', 'GBK', $v['QCWJCB']);
							$row2['JFMY'] = iconv('UTF-8', 'GBK', $v['JFMY']);
							$row2['JFSY'] = iconv('UTF-8', 'GBK', $v['JFSY']);
							$row2['JFCB'] = iconv('UTF-8', 'GBK', $v['JFCB']);
							$row2['JSMY'] = iconv('UTF-8', 'GBK', $v['JSMY']);
							$row2['JSSY'] = iconv('UTF-8', 'GBK', $v['JSSY']);
							$row2['JSCB'] = iconv('UTF-8', 'GBK', $v['JSCB']);
							$row2['QMWJMY'] = iconv('UTF-8', 'GBK', $v['QMWJMY']);
							$row2['QMWJSY'] = iconv('UTF-8', 'GBK', $v['QMWJSY']);
							$row2['QMWJCB'] = iconv('UTF-8', 'GBK', $v['QMWJCB']);
							$row2['SYLX'] = iconv('UTF-8', 'GBK', $v['SYLX']);
							$row2['BB'] = iconv('UTF-8', 'GBK', $v['BB']);
							$row2['MINDATE'] = iconv('UTF-8', 'GBK', $v['MINDATE']);
							$row2['MAXDATE'] = iconv('UTF-8', 'GBK', $v['MAXDATE']);
							$row2['NY'] = iconv('UTF-8', 'GBK', $v['NY']);
							$row2['BJSMY'] = iconv('UTF-8', 'GBK', $v['BJSMY']);
							$row2['BJSSY'] = iconv('UTF-8', 'GBK', $v['BJSSY']);

						}
						if($fileName == '教辅到货明细'){
							$row2['YWBMBH'] = iconv('UTF-8', 'GBK', $v['YWBMBH']);
							$row2['GHDWH'] = iconv('UTF-8', 'GBK', $v['GHDWH']);
							$row2['MC'] = iconv('UTF-8', 'GBK', $v['MC']);
							$row2['DHPCH'] = iconv('UTF-8', 'GBK', $v['DHPCH']);
							$row2['SHRQ'] = iconv('UTF-8', 'GBK', $v['SHRQ']);
							$row2['YSDJ'] = iconv('UTF-8', 'GBK', $v['YSDJ']);
							$row2['SSDH'] = iconv('UTF-8', 'GBK', $v['SSDH']);
							$row2['ZDQH'] = iconv('UTF-8', 'GBK', $v['ZDQH']);
							$row2['ZDXH'] = iconv('UTF-8', 'GBK', $v['ZDXH']);
							$row2['SM'] = iconv('UTF-8', 'GBK', $v['SM']);
							$row2['DJ'] = iconv('UTF-8', 'GBK', $v['DJ']);
							$row2['JXSSSL'] = iconv('UTF-8', 'GBK', $v['JXSSSL']);
							$row2['MY'] = iconv('UTF-8', 'GBK', $v['MY']);
							$row2['SYS'] = iconv('UTF-8', 'GBK', $v['SYS']);
							$row2['ZDPCH'] = iconv('UTF-8', 'GBK', $v['ZDPCH']);
							$row2['JSRQ'] = iconv('UTF-8', 'GBK', $v['JSRQ']);
							$row2['JSLX'] = iconv('UTF-8', 'GBK', $v['JSLX']);
							$row2['ZDPCH1'] = iconv('UTF-8', 'GBK', $v['ZDPCH1']);
							$row2['TSLSH'] = iconv('UTF-8', 'GBK', $v['TSLSH']);
							$row2['YSRQ'] = iconv('UTF-8', 'GBK', $v['YSRQ']);
							$row2['YSDBRQ'] = iconv('UTF-8', 'GBK', $v['YSDBRQ']);
							$row2['JCDRQ'] = iconv('UTF-8', 'GBK', $v['JCDRQ']);
							$row2['KQBJ'] = iconv('UTF-8', 'GBK', $v['KQBJ']);
							$row2['JSGHDWH'] = iconv('UTF-8', 'GBK', $v['JSGHDWH']);
							$row2['YSGHDWH'] = iconv('UTF-8', 'GBK', $v['YSGHDWH']);
							$row2['FLOWID_DHMX'] = iconv('UTF-8', 'GBK', $v['FLOWID_DHMX']);

						}
						
						if($fileName == '教辅进退明细'){
							$row2['YWBMBH'] = iconv('UTF-8', 'GBK', $v['YWBMBH']);
							$row2['GHDWH'] = iconv('UTF-8', 'GBK', $v['GHDWH']);
							$row2['MC'] = iconv('UTF-8', 'GBK', $v['MC']);
							$row2['DBRQ'] = iconv('UTF-8', 'GBK', $v['DBRQ']);
							$row2['ID'] = iconv('UTF-8', 'GBK', $v['ID']);
							$row2['SM'] = iconv('UTF-8', 'GBK', $v['SM']);
							$row2['DJ'] = iconv('UTF-8', 'GBK', $v['DJ']);
							$row2['ZMY'] = iconv('UTF-8', 'GBK', $v['ZMY']);
							$row2['ZSY'] = iconv('UTF-8', 'GBK', $v['ZSY']);
							$row2['BZ'] = iconv('UTF-8', 'GBK', $v['BZ']);
							$row2['YWPCH'] = iconv('UTF-8', 'GBK', $v['YWPCH']);
							$row2['ZDPCH'] = iconv('UTF-8', 'GBK', $v['ZDPCH']);
							$row2['JSRQ'] = iconv('UTF-8', 'GBK', $v['JSRQ']);
							$row2['JSLX'] = iconv('UTF-8', 'GBK', $v['JSLX']);
							$row2['ZDQH'] = iconv('UTF-8', 'GBK', $v['ZDQH']);
							$row2['ZDXH'] = iconv('UTF-8', 'GBK', $v['ZDXH']);
							$row2['ZDPCH1'] = iconv('UTF-8', 'GBK', $v['ZDPCH1']);
							$row2['TSLSH'] = iconv('UTF-8', 'GBK', $v['TSLSH']);
							$row2['NF'] = iconv('UTF-8', 'GBK', $v['NF']);
							$row2['NFHRQ'] = iconv('UTF-8', 'GBK', $v['NFHRQ']);
							$row2['NJSRQ'] = iconv('UTF-8', 'GBK', $v['NJSRQ']);
							$row2['JSGHDWH'] = iconv('UTF-8', 'GBK', $v['JSGHDWH']);
							$row2['YSGHDWH'] = iconv('UTF-8', 'GBK', $v['YSGHDWH']);
							$row2['ZCS'] = iconv('UTF-8', 'GBK', $v['ZCS']);

						}

						if($fileName == '教辅出版社免费让利明细'){
							$row2['YWBMBH'] = iconv('UTF-8', 'GBK', $v['YWBMBH']);
							$row2['GHDWH'] = iconv('UTF-8', 'GBK', $v['GHDWH']);
							$row2['MC'] = iconv('UTF-8', 'GBK', $v['MC']);
							$row2['DHPCH'] = iconv('UTF-8', 'GBK', $v['DHPCH']);
							$row2['SHRQ'] = iconv('UTF-8', 'GBK', $v['SHRQ']);
							$row2['YSDJ'] = iconv('UTF-8', 'GBK', $v['YSDJ']);
							$row2['SSDH'] = iconv('UTF-8', 'GBK', $v['SSDH']);
							$row2['ZDQH'] = iconv('UTF-8', 'GBK', $v['ZDQH']);
							$row2['ZDXH'] = iconv('UTF-8', 'GBK', $v['ZDXH']);
							$row2['SM'] = iconv('UTF-8', 'GBK', $v['SM']);
							$row2['DJ'] = iconv('UTF-8', 'GBK', $v['DJ']);
							$row2['JXSSSL'] = iconv('UTF-8', 'GBK', $v['JXSSSL']);
							$row2['MY'] = iconv('UTF-8', 'GBK', $v['MY']);
							$row2['SYS'] = iconv('UTF-8', 'GBK', $v['SYS']);
							$row2['ZDPCH'] = iconv('UTF-8', 'GBK', $v['ZDPCH']);
							$row2['JSRQ'] = iconv('UTF-8', 'GBK', $v['JSRQ']);
							$row2['JSLX'] = iconv('UTF-8', 'GBK', $v['JSLX']);
							$row2['JSGHDWH'] = iconv('UTF-8', 'GBK', $v['JSGHDWH']);
							$row2['YSGHDWH'] = iconv('UTF-8', 'GBK', $v['YSGHDWH']);
							$row2['TSLSH'] = iconv('UTF-8', 'GBK', $v['TSLSH']);


						}
						
						if($fileName == '教辅销退明细'){
							$row2['GHDW'] = iconv('UTF-8', 'GBK', $v['GHDW']);
							$row2['GHDWMC'] = iconv('UTF-8', 'GBK', $v['GHDWMC']);
							$row2['YWBMBH'] = iconv('UTF-8', 'GBK', $v['YWBMBH']);
							$row2['KFBH'] = iconv('UTF-8', 'GBK', $v['KFBH']);
							$row2['DH'] = iconv('UTF-8', 'GBK', $v['DH']);
							$row2['DM'] = iconv('UTF-8', 'GBK', $v['DM']);
							$row2['PCH'] = iconv('UTF-8', 'GBK', $v['PCH']);
							$row2['THRQ'] = iconv('UTF-8', 'GBK', $v['THRQ']);
							$row2['ZMY'] = iconv('UTF-8', 'GBK', $v['ZMY']);
							$row2['ZSY'] = iconv('UTF-8', 'GBK', $v['ZSY']);
							$row2['ZCB'] = iconv('UTF-8', 'GBK', $v['ZCB']);
							$row2['TSLSH'] = iconv('UTF-8', 'GBK', $v['TSLSH']);
							$row2['JSRQ'] = iconv('UTF-8', 'GBK', $v['JSRQ']);
							$row2['JSLX'] = iconv('UTF-8', 'GBK', $v['JSLX']);
							$row2['ZDQH'] = iconv('UTF-8', 'GBK', $v['ZDQH']);
							$row2['NF'] = iconv('UTF-8', 'GBK', $v['NF']);
							$row2['NFHRQ'] = iconv('UTF-8', 'GBK', $v['NFHRQ']);
							$row2['NJSRQ'] = iconv('UTF-8', 'GBK', $v['NJSRQ']);
							$row2['JB'] = iconv('UTF-8', 'GBK', $v['JB']);
							$row2['CWQRPC'] = iconv('UTF-8', 'GBK', $v['CWQRPC']);
							$row2['ZCS'] = iconv('UTF-8', 'GBK', $v['ZCS']);
							$row2['DJ'] = iconv('UTF-8', 'GBK', $v['DJ']);
							$row2['ID'] = iconv('UTF-8', 'GBK', $v['ID']);
							$row2['BB'] = iconv('UTF-8', 'GBK', $v['BB']);
							$row2['SM'] = iconv('UTF-8', 'GBK', $v['SM']);
							$row2['ISBN'] = iconv('UTF-8', 'GBK', $v['ISBN']);
							$row2['CBNY'] = iconv('UTF-8', 'GBK', $v['CBNY']);
						}

						if($fileName == '教辅基层店免费让利明细'){
							$row2['YWBMBH'] = iconv('UTF-8', 'GBK', $v['YWBMBH']);
							$row2['KFBH'] = iconv('UTF-8', 'GBK', $v['KFBH']);
							$row2['DH'] = iconv('UTF-8', 'GBK', $v['DH']);
							$row2['DM'] = iconv('UTF-8', 'GBK', $v['DM']);
							$row2['GZPCH'] = iconv('UTF-8', 'GBK', $v['GZPCH']);
							$row2['GZRQ'] = iconv('UTF-8', 'GBK', $v['GZRQ']);
							$row2['ZMY'] = iconv('UTF-8', 'GBK', $v['ZMY']);
							$row2['ZSY'] = iconv('UTF-8', 'GBK', $v['ZSY']);
							$row2['ZCB'] = iconv('UTF-8', 'GBK', $v['ZCB']);
							$row2['TSLSH'] = iconv('UTF-8', 'GBK', $v['TSLSH']);
							$row2['JSRQ'] = iconv('UTF-8', 'GBK', $v['JSRQ']);
							$row2['BZ'] = iconv('UTF-8', 'GBK', $v['BZ']);
							$row2['JSLX'] = iconv('UTF-8', 'GBK', $v['JSLX']);
							$row2['ZDQH'] = iconv('UTF-8', 'GBK', $v['ZDQH']);
							$row2['NF'] = iconv('UTF-8', 'GBK', $v['NF']);
							$row2['NFHRQ'] = iconv('UTF-8', 'GBK', $v['NFHRQ']);
							$row2['NJSRQ'] = iconv('UTF-8', 'GBK', $v['NJSRQ']);
							$row2['JB'] = iconv('UTF-8', 'GBK', $v['JB']);
							$row2['ZCS'] = iconv('UTF-8', 'GBK', $v['ZCS']);

						}
						
						if($fileName == '教辅发货明细'){
							$row2['CBNY'] = iconv('UTF-8', 'GBK', $v['CBNY']);
							$row2['GHDW'] = iconv('UTF-8', 'GBK', $v['GHDW']);
							$row2['GHDWMC'] = iconv('UTF-8', 'GBK', $v['GHDWMC']);
							$row2['YWBMBH'] = iconv('UTF-8', 'GBK', $v['YWBMBH']);
							$row2['DH'] = iconv('UTF-8', 'GBK', $v['DH']);
							$row2['MC'] = iconv('UTF-8', 'GBK', $v['MC']);
							$row2['PFPCH'] = iconv('UTF-8', 'GBK', $v['PFPCH']);
							$row2['BZ'] = iconv('UTF-8', 'GBK', $v['BZ']);
							$row2['DBRQ'] = iconv('UTF-8', 'GBK', $v['DBRQ']);
							$row2['PFRQ'] = iconv('UTF-8', 'GBK', $v['PFRQ']);
							$row2['ZMY'] = iconv('UTF-8', 'GBK', $v['ZMY']);
							$row2['ZSY'] = iconv('UTF-8', 'GBK', $v['ZSY']);
							$row2['CBJ'] = iconv('UTF-8', 'GBK', $v['CBJ']);
							$row2['TSLSH'] = iconv('UTF-8', 'GBK', $v['TSLSH']);
							$row2['JSRQ'] = iconv('UTF-8', 'GBK', $v['JSRQ']);
							$row2['SM'] = iconv('UTF-8', 'GBK', $v['SM']);
							$row2['DJ'] = iconv('UTF-8', 'GBK', $v['DJ']);
							$row2['ZDQH'] = iconv('UTF-8', 'GBK', $v['ZDQH']);
							$row2['ZDXH'] = iconv('UTF-8', 'GBK', $v['ZDXH']);
							$row2['KFBH'] = iconv('UTF-8', 'GBK', $v['KFBH']);
							$row2['JSLX'] = iconv('UTF-8', 'GBK', $v['JSLX']);
							$row2['NF'] = iconv('UTF-8', 'GBK', $v['NF']);
							$row2['NFHRQ'] = iconv('UTF-8', 'GBK', $v['NFHRQ']);
							$row2['NJSRQ'] = iconv('UTF-8', 'GBK', $v['NJSRQ']);
							$row2['JB'] = iconv('UTF-8', 'GBK', $v['JB']);
							$row2['CWQRPC'] = iconv('UTF-8', 'GBK', $v['CWQRPC']);
							$row2['YSDBRQ'] = iconv('UTF-8', 'GBK', $v['YSDBRQ']);
							$row2['ZCS'] = iconv('UTF-8', 'GBK', $v['ZCS']);
							
						}

					
						if($fileName == '教辅库存明细'){
							$row2['YWBMBH'] = iconv('UTF-8', 'GBK', $v['YWBMBH']);
							$row2['ID'] = iconv('UTF-8', 'GBK', $v['ID']);
							$row2['SM'] = iconv('UTF-8', 'GBK', $v['SM']);
							$row2['DJ'] = iconv('UTF-8', 'GBK', $v['DJ']);
							$row2['BB'] = iconv('UTF-8', 'GBK', $v['BB']);
							$row2['KCCS'] = iconv('UTF-8', 'GBK', $v['KCCS']);
							$row2['MY'] = iconv('UTF-8', 'GBK', $v['MY']);
							$row2['SYS'] = iconv('UTF-8', 'GBK', $v['SYS']);
							$row2['KF'] = iconv('UTF-8', 'GBK', $v['KF']);
							$row2['WL'] = iconv('UTF-8', 'GBK', $v['WL']);
							$row2['FL'] = iconv('UTF-8', 'GBK', $v['FL']);
							$row2['HW'] = iconv('UTF-8', 'GBK', $v['HW']);
							$row2['GHDW'] = iconv('UTF-8', 'GBK', $v['GHDW']);
							$row2['YWY'] = iconv('UTF-8', 'GBK', $v['YWY']);
							$row2['ISBN'] = iconv('UTF-8', 'GBK', $v['ISBN']);
							$row2['BC'] = iconv('UTF-8', 'GBK', $v['BC']);
							$row2['BIANZHE'] = iconv('UTF-8', 'GBK', $v['BIANZHE']);
							$row2['NIAN'] = iconv('UTF-8', 'GBK', $v['NIAN']);
	
						}
						if($fileName == '教辅库存明细提前出'){
							$row2['YWBMBH'] = iconv('UTF-8', 'GBK', $v['YWBMBH']);
							$row2['ID'] = iconv('UTF-8', 'GBK', $v['ID']);
							$row2['SM'] = iconv('UTF-8', 'GBK', $v['SM']);
							$row2['DJ'] = iconv('UTF-8', 'GBK', $v['DJ']);
							$row2['BB'] = iconv('UTF-8', 'GBK', $v['BB']);
							$row2['KCCS'] = iconv('UTF-8', 'GBK', $v['KCCS']);
							$row2['MY'] = iconv('UTF-8', 'GBK', $v['MY']);
							$row2['SYS'] = iconv('UTF-8', 'GBK', $v['SYS']);
							$row2['KF'] = iconv('UTF-8', 'GBK', $v['KF']);
							$row2['WL'] = iconv('UTF-8', 'GBK', $v['WL']);
							$row2['FL'] = iconv('UTF-8', 'GBK', $v['FL']);
							$row2['HW'] = iconv('UTF-8', 'GBK', $v['HW']);
							$row2['GHDW'] = iconv('UTF-8', 'GBK', $v['GHDW']);
							$row2['YWY'] = iconv('UTF-8', 'GBK', $v['YWY']);
							$row2['ISBN'] = iconv('UTF-8', 'GBK', $v['ISBN']);
							$row2['BC'] = iconv('UTF-8', 'GBK', $v['BC']);
							$row2['BIANZHE'] = iconv('UTF-8', 'GBK', $v['BIANZHE']);
							$row2['NIAN'] = iconv('UTF-8', 'GBK', $v['NIAN']);
	
						}
						if($fileName == '教辅配发中间态'){
							$row2['YWBMBH'] = iconv('UTF-8', 'GBK', $v['YWBMBH']);
							$row2['YWBMMC'] = iconv('UTF-8', 'GBK', $v['YWBMMC']);
							$row2['DH'] = iconv('UTF-8', 'GBK', $v['DH']);
							$row2['DM'] = iconv('UTF-8', 'GBK', $v['DM']);
							$row2['ID'] = iconv('UTF-8', 'GBK', $v['ID']);
							$row2['ISBN'] = iconv('UTF-8', 'GBK', $v['ISBN']);
							$row2['SM'] = iconv('UTF-8', 'GBK', $v['SM']);
							$row2['DJ'] = iconv('UTF-8', 'GBK', $v['DJ']);
							$row2['CBNY'] = iconv('UTF-8', 'GBK', $v['CBNY']);
							$row2['PXCS'] = iconv('UTF-8', 'GBK', $v['PXCS']);
							$row2['ZMY'] = iconv('UTF-8', 'GBK', $v['ZMY']);
							$row2['SYS'] = iconv('UTF-8', 'GBK', $v['SYS']);
							$row2['CBJ'] = iconv('UTF-8', 'GBK', $v['CBJ']);
							$row2['PXDH'] = iconv('UTF-8', 'GBK', $v['PXDH']);
							$row2['BZ'] = iconv('UTF-8', 'GBK', $v['BZ']);
							$row2['ZDH'] = iconv('UTF-8', 'GBK', $v['ZDH']);
							$row2['SYLX'] = iconv('UTF-8', 'GBK', $v['SYLX']);
							$row2['SL'] = iconv('UTF-8', 'GBK', $v['SL']);
							$row2['ZJXS'] = iconv('UTF-8', 'GBK', $v['ZJXS']);
							$row2['QX'] = iconv('UTF-8', 'GBK', $v['QX']);
							$row2['DJBH'] = iconv('UTF-8', 'GBK', $v['DJBH']);
							$row2['KFBH'] = iconv('UTF-8', 'GBK', $v['KFBH']);
							$row2['BJLSH'] = iconv('UTF-8', 'GBK', $v['BJLSH']);
							$row2['CYBH'] = iconv('UTF-8', 'GBK', $v['CYBH']);
							$row2['FLOWID'] = iconv('UTF-8', 'GBK', $v['FLOWID']);
							$row2['ZDQH'] = iconv('UTF-8', 'GBK', $v['ZDQH']);
							$row2['ZDXH'] = iconv('UTF-8', 'GBK', $v['ZDXH']);
							$row2['ZDQH1'] = iconv('UTF-8', 'GBK', $v['ZDQH1']);
							$row2['ZDXH1'] = iconv('UTF-8', 'GBK', $v['ZDXH1']);
							$row2['PFRQ'] = iconv('UTF-8', 'GBK', $v['PFRQ']);
						}
						
						if($fileName == '教辅库房转移中间态明细'){
							$row2['ID'] = iconv('UTF-8', 'GBK', $v['ID']);
							$row2['SM'] = iconv('UTF-8', 'GBK', $v['SM']);
							$row2['ISBN'] = iconv('UTF-8', 'GBK', $v['ISBN']);
							$row2['DJ'] = iconv('UTF-8', 'GBK', $v['DJ']);
							$row2['BB'] = iconv('UTF-8', 'GBK', $v['BB']);
							$row2['GHDW'] = iconv('UTF-8', 'GBK', $v['GHDW']);
							$row2['YWY'] = iconv('UTF-8', 'GBK', $v['YWY']);
							$row2['CBNY'] = iconv('UTF-8', 'GBK', $v['CBNY']);
							$row2['YWLX'] = iconv('UTF-8', 'GBK', $v['YWLX']);
							$row2['CS'] = iconv('UTF-8', 'GBK', $v['SUM(CS)']);
							$row2['MY'] = iconv('UTF-8', 'GBK', $v['MY']);
							$row2['SY'] = iconv('UTF-8', 'GBK', $v['SY']);
						}
						
						if($fileName == '教辅损益明细'){
							$row2['KFBH'] = iconv('UTF-8', 'GBK', $v['KFBH']);
							$row2['SM'] = iconv('UTF-8', 'GBK', $v['SM']);
							$row2['CRKCS'] = iconv('UTF-8', 'GBK', $v['CRKCS']);
							$row2['DJ'] = iconv('UTF-8', 'GBK', $v['DJ']); 
							$row2['ZMY'] = iconv('UTF-8', 'GBK', $v['ZMY']);
							$row2['CRKSY'] = iconv('UTF-8', 'GBK', $v['CRKSY']);
							$row2['CRKRQ'] = iconv('UTF-8', 'GBK', $v['CRKRQ']);
	
						}
						if($fileName == '教辅当月已结算-到货已结-到货'){
							$row2['YWBMBH'] = iconv('UTF-8', 'GBK', $v['YWBMBH']);
							$row2['GHDWH'] = iconv('UTF-8', 'GBK', $v['GHDWH']);
							$row2['MC'] = iconv('UTF-8', 'GBK', $v['MC']);
							$row2['DHPCH'] = iconv('UTF-8', 'GBK', $v['DHPCH']);
							$row2['SHRQ'] = iconv('UTF-8', 'GBK', $v['SHRQ']);
							$row2['YSDJ'] = iconv('UTF-8', 'GBK', $v['YSDJ']);
							$row2['SSDH'] = iconv('UTF-8', 'GBK', $v['SSDH']);
							$row2['ZDQH'] = iconv('UTF-8', 'GBK', $v['ZDQH']);
							$row2['ZDXH'] = iconv('UTF-8', 'GBK', $v['ZDXH']);
							$row2['SM'] = iconv('UTF-8', 'GBK', $v['SM']);
							$row2['DJ'] = iconv('UTF-8', 'GBK', $v['DJ']);
							$row2['JXSSSL'] = iconv('UTF-8', 'GBK', $v['JXSSSL']);
							$row2['MY'] = iconv('UTF-8', 'GBK', $v['MY']);
							$row2['SYS'] = iconv('UTF-8', 'GBK', $v['SYS']);
							$row2['ZDPCH'] = iconv('UTF-8', 'GBK', $v['ZDPCH']);
							$row2['JSRQ'] = iconv('UTF-8', 'GBK', $v['JSRQ']);
							$row2['JSLX'] = iconv('UTF-8', 'GBK', $v['JSLX']);
							$row2['ZDPCH1'] = iconv('UTF-8', 'GBK', $v['ZDPCH1']);
							$row2['TSLSH'] = iconv('UTF-8', 'GBK', $v['TSLSH']);
							$row2['YSRQ'] = iconv('UTF-8', 'GBK', $v['YSRQ']);
							$row2['YSDBRQ'] = iconv('UTF-8', 'GBK', $v['YSDBRQ']);
							$row2['JCDRQ'] = iconv('UTF-8', 'GBK', $v['JCDRQ']);
							$row2['KQBJ'] = iconv('UTF-8', 'GBK', $v['KQBJ']);
							$row2['JSGHDWH'] = iconv('UTF-8', 'GBK', $v['JSGHDWH']);
							$row2['YSGHDWH'] = iconv('UTF-8', 'GBK', $v['YSGHDWH']);
							$row2['FLOWID_DHMX'] = iconv('UTF-8', 'GBK', $v['FLOWID_DHMX']);
						}
						if($fileName == '教辅当月已结算-到货已结-进退'){
							$row2['YWBMBH'] = iconv('UTF-8', 'GBK', $v['YWBMBH']);
							$row2['GHDWH'] = iconv('UTF-8', 'GBK', $v['GHDWH']);
							$row2['MC'] = iconv('UTF-8', 'GBK', $v['MC']);
							$row2['DBRQ'] = iconv('UTF-8', 'GBK', $v['DBRQ']);
							$row2['ID'] = iconv('UTF-8', 'GBK', $v['ID']);
							$row2['SM'] = iconv('UTF-8', 'GBK', $v['SM']);
							$row2['DJ'] = iconv('UTF-8', 'GBK', $v['DJ']);
							$row2['ZMY'] = iconv('UTF-8', 'GBK', $v['ZMY']);
							$row2['ZSY'] = iconv('UTF-8', 'GBK', $v['ZSY']);
							$row2['BZ'] = iconv('UTF-8', 'GBK', $v['BZ']);
							$row2['YWPCH'] = iconv('UTF-8', 'GBK', $v['YWPCH']);
							$row2['ZDPCH'] = iconv('UTF-8', 'GBK', $v['ZDPCH']);
							$row2['JSRQ'] = iconv('UTF-8', 'GBK', $v['JSRQ']);
							$row2['JSLX'] = iconv('UTF-8', 'GBK', $v['JSLX']);
							$row2['ZDQH'] = iconv('UTF-8', 'GBK', $v['ZDQH']);
							$row2['ZDPCH1'] = iconv('UTF-8', 'GBK', $v['ZDPCH1']);
							$row2['TSLSH'] = iconv('UTF-8', 'GBK', $v['TSLSH']);
							$row2['NF'] = iconv('UTF-8', 'GBK', $v['NF']);
							$row2['NFHRQ'] = iconv('UTF-8', 'GBK', $v['NFHRQ']);
							$row2['NJSRQ'] = iconv('UTF-8', 'GBK', $v['NJSRQ']);
							$row2['JSGHDWH'] = iconv('UTF-8', 'GBK', $v['JSGHDWH']);
							$row2['YSGHDWH'] = iconv('UTF-8', 'GBK', $v['YSGHDWH']);
							$row2['ZCS'] = iconv('UTF-8', 'GBK', $v['ZCS']);
						}
						if($fileName == '教辅当月已结算-到货已结-退货'){
							$row2['YWBMBH'] = iconv('UTF-8', 'GBK', $v['YWBMBH']);
							$row2['GHDWH'] = iconv('UTF-8', 'GBK', $v['GHDWH']);
							$row2['MC'] = iconv('UTF-8', 'GBK', $v['MC']);
							$row2['ID'] = iconv('UTF-8', 'GBK', $v['ID']);
							$row2['DBRQ'] = iconv('UTF-8', 'GBK', $v['DBRQ']);
							$row2['SM'] = iconv('UTF-8', 'GBK', $v['SM']);
							$row2['DJ'] = iconv('UTF-8', 'GBK', $v['DJ']);
							$row2['ZMY'] = iconv('UTF-8', 'GBK', $v['ZMY']);
							$row2['ZSY'] = iconv('UTF-8', 'GBK', $v['ZSY']);
							$row2['BZ'] = iconv('UTF-8', 'GBK', $v['BZ']);
							$row2['YWPCH'] = iconv('UTF-8', 'GBK', $v['YWPCH']);
							$row2['JSRQ'] = iconv('UTF-8', 'GBK', $v['JSRQ']);
							$row2['ZDPCH'] = iconv('UTF-8', 'GBK', $v['ZDPCH']);
							$row2['JSLX'] = iconv('UTF-8', 'GBK', $v['JSLX']);
							$row2['ZDQH'] = iconv('UTF-8', 'GBK', $v['ZDQH']);
							$row2['ZDXH'] = iconv('UTF-8', 'GBK', $v['ZDXH']);
							$row2['ZDPCH1'] = iconv('UTF-8', 'GBK', $v['ZDPCH1']);
							$row2['TSLSH'] = iconv('UTF-8', 'GBK', $v['TSLSH']);
							$row2['NF'] = iconv('UTF-8', 'GBK', $v['NF']);
							$row2['NFHRQ'] = iconv('UTF-8', 'GBK', $v['NFHRQ']);
							$row2['NJSRQ'] = iconv('UTF-8', 'GBK', $v['NJSRQ']);
							$row2['JSGHDWH'] = iconv('UTF-8', 'GBK', $v['JSGHDWH']);
							$row2['YSGHDWH'] = iconv('UTF-8', 'GBK', $v['YSGHDWH']);
							$row2['ZCS'] = iconv('UTF-8', 'GBK', $v['ZCS']);
							
						}
						if($fileName == '教辅当月已结算-到货已结-更正'){
							$row2['YWBMBH'] = iconv('UTF-8', 'GBK', $v['YWBMBH']);
							$row2['DH'] = iconv('UTF-8', 'GBK', $v['DH']);
							$row2['MC'] = iconv('UTF-8', 'GBK', $v['MC']);
							$row2['PFPCH'] = iconv('UTF-8', 'GBK', $v['PFPCH']);
							$row2['BZ'] = iconv('UTF-8', 'GBK', $v['BZ']);
							$row2['DBRQ'] = iconv('UTF-8', 'GBK', $v['DBRQ']);
							$row2['PFRQ'] = iconv('UTF-8', 'GBK', $v['PFRQ']);
							$row2['ZMY'] = iconv('UTF-8', 'GBK', $v['ZMY']);
							$row2['ZSY'] = iconv('UTF-8', 'GBK', $v['ZSY']);
							$row2['CBJ'] = iconv('UTF-8', 'GBK', $v['CBJ']);
							$row2['TSLSH'] = iconv('UTF-8', 'GBK', $v['TSLSH']);
							$row2['JSRQ'] = iconv('UTF-8', 'GBK', $v['JSRQ']);
							$row2['SM'] = iconv('UTF-8', 'GBK', $v['SM']);
							$row2['DJ'] = iconv('UTF-8', 'GBK', $v['DJ']);
							$row2['ZDQH'] = iconv('UTF-8', 'GBK', $v['ZDQH']);
							$row2['ZDXH'] = iconv('UTF-8', 'GBK', $v['ZDXH']);
							$row2['ZDPCH1'] = iconv('UTF-8', 'GBK', $v['ZDPCH1']);
							$row2['KFBH'] = iconv('UTF-8', 'GBK', $v['KFBH']);
							$row2['JSLX'] = iconv('UTF-8', 'GBK', $v['JSLX']);
							$row2['NF'] = iconv('UTF-8', 'GBK', $v['NF']);
							$row2['NFHRQ'] = iconv('UTF-8', 'GBK', $v['NFHRQ']);
							$row2['NJSRQ'] = iconv('UTF-8', 'GBK', $v['NJSRQ']);
							$row2['JB'] = iconv('UTF-8', 'GBK', $v['JB']);
							$row2['CWQRPC'] = iconv('UTF-8', 'GBK', $v['CWQRPC']);
							$row2['YSDBRQ'] = iconv('UTF-8', 'GBK', $v['YSDBRQ']);
							$row2['ZCS'] = iconv('UTF-8', 'GBK', $v['ZCS']);
						}
						if($fileName == '教辅当月已结算-发货已结-到货'){
							$row2['CBNY'] = iconv('UTF-8', 'GBK', $v['CBNY']);
							$row2['GHDW'] = iconv('UTF-8', 'GBK', $v['GHDW']);
							$row2['GHDWMC'] = iconv('UTF-8', 'GBK', $v['GHDWMC']);
							$row2['YWBMBH'] = iconv('UTF-8', 'GBK', $v['YWBMBH']);
							$row2['DH'] = iconv('UTF-8', 'GBK', $v['DH']);
							$row2['MC'] = iconv('UTF-8', 'GBK', $v['MC']);
							$row2['PFPCH'] = iconv('UTF-8', 'GBK', $v['PFPCH']);
							$row2['BZ'] = iconv('UTF-8', 'GBK', $v['BZ']);
							$row2['DBRQ'] = iconv('UTF-8', 'GBK', $v['DBRQ']);
							$row2['PFRQ'] = iconv('UTF-8', 'GBK', $v['PFRQ']);
							$row2['ZMY'] = iconv('UTF-8', 'GBK', $v['ZMY']);
							$row2['ZSY'] = iconv('UTF-8', 'GBK', $v['ZSY']);
							$row2['CBJ'] = iconv('UTF-8', 'GBK', $v['CBJ']);
							$row2['TSLSH'] = iconv('UTF-8', 'GBK', $v['TSLSH']);
							$row2['JSRQ'] = iconv('UTF-8', 'GBK', $v['JSRQ']);
							$row2['SM'] = iconv('UTF-8', 'GBK', $v['SM']);
							$row2['DJ'] = iconv('UTF-8', 'GBK', $v['DJ']);
							$row2['ZDQH'] = iconv('UTF-8', 'GBK', $v['ZDQH']);
							$row2['ZDXH'] = iconv('UTF-8', 'GBK', $v['ZDXH']);
							$row2['KFBH'] = iconv('UTF-8', 'GBK', $v['KFBH']);
							$row2['JSLX'] = iconv('UTF-8', 'GBK', $v['JSLX']);
							$row2['NF'] = iconv('UTF-8', 'GBK', $v['NF']);
							$row2['NFHRQ'] = iconv('UTF-8', 'GBK', $v['NFHRQ']);
							$row2['NJSRQ'] = iconv('UTF-8', 'GBK', $v['NJSRQ']);
							$row2['JB'] = iconv('UTF-8', 'GBK', $v['JB']);
							$row2['CWQRPC'] = iconv('UTF-8', 'GBK', $v['CWQRPC']);
							$row2['YSDBRQ'] = iconv('UTF-8', 'GBK', $v['YSDBRQ']);
							$row2['ZCS'] = iconv('UTF-8', 'GBK', $v['ZCS']);
						}
						if($fileName == '教辅当月已结算-发货已结-退货'){
							$row2['CBNY'] = iconv('UTF-8', 'GBK', $v['CBNY']);
							$row2['GHDW'] = iconv('UTF-8', 'GBK', $v['GHDW']);
							$row2['GHDWMC'] = iconv('UTF-8', 'GBK', $v['GHDWMC']);
							$row2['YWBMBH'] = iconv('UTF-8', 'GBK', $v['YWBMBH']);
							$row2['KFBH'] = iconv('UTF-8', 'GBK', $v['KFBH']);
							$row2['DH'] = iconv('UTF-8', 'GBK', $v['DH']);
							$row2['DM'] = iconv('UTF-8', 'GBK', $v['DM']);
							$row2['PCH'] = iconv('UTF-8', 'GBK', $v['PCH']);
							$row2['THRQ'] = iconv('UTF-8', 'GBK', $v['THRQ']);
							$row2['ZMY'] = iconv('UTF-8', 'GBK', $v['ZMY']);
							$row2['ZSY'] = iconv('UTF-8', 'GBK', $v['ZSY']);
							$row2['ZCB'] = iconv('UTF-8', 'GBK', $v['ZCB']);
							$row2['TSLSH'] = iconv('UTF-8', 'GBK', $v['TSLSH']);
							$row2['JSRQ'] = iconv('UTF-8', 'GBK', $v['JSRQ']);
							$row2['JSLX'] = iconv('UTF-8', 'GBK', $v['JSLX']);
							$row2['ZDQH'] = iconv('UTF-8', 'GBK', $v['ZDQH']);
							$row2['NF'] = iconv('UTF-8', 'GBK', $v['NF']);
							$row2['NFHRQ'] = iconv('UTF-8', 'GBK', $v['NFHRQ']);
							$row2['NJSRQ'] = iconv('UTF-8', 'GBK', $v['NJSRQ']);
							$row2['JB'] = iconv('UTF-8', 'GBK', $v['JB']);
							$row2['CWQRPC'] = iconv('UTF-8', 'GBK', $v['CWQRPC']);
							$row2['ZCS'] = iconv('UTF-8', 'GBK', $v['ZCS']);
							$row2['DJ'] = iconv('UTF-8', 'GBK', $v['DJ']);
							$row2['ID'] = iconv('UTF-8', 'GBK', $v['ID']);
							$row2['BB'] = iconv('UTF-8', 'GBK', $v['BB']);
							$row2['SM'] = iconv('UTF-8', 'GBK', $v['SM']);
							$row2['ISBN'] = iconv('UTF-8', 'GBK', $v['ISBN']);
							$row2['CBNY'] = iconv('UTF-8', 'GBK', $v['CBNY']);
						}
						if($fileName == '教辅当月已结算-发货已结-更正'){
							$row2['CBNY'] = iconv('UTF-8', 'GBK', $v['CBNY']);
							$row2['GHDW'] = iconv('UTF-8', 'GBK', $v['GHDW']);
							$row2['GHDWMC'] = iconv('UTF-8', 'GBK', $v['GHDWMC']);
							$row2['YWBMBH'] = iconv('UTF-8', 'GBK', $v['YWBMBH']);
							$row2['KFBH'] = iconv('UTF-8', 'GBK', $v['KFBH']);
							$row2['DH'] = iconv('UTF-8', 'GBK', $v['DH']);
							$row2['DM'] = iconv('UTF-8', 'GBK', $v['DM']);
							$row2['GZPCH'] = iconv('UTF-8', 'GBK', $v['GZPCH']);
							$row2['GZRQ'] = iconv('UTF-8', 'GBK', $v['GZRQ']);
							$row2['ZMY'] = iconv('UTF-8', 'GBK', $v['ZMY']);
							$row2['ZSY'] = iconv('UTF-8', 'GBK', $v['ZSY']);
							$row2['ZCB'] = iconv('UTF-8', 'GBK', $v['ZCB']);
							$row2['TSLSH'] = iconv('UTF-8', 'GBK', $v['TSLSH']);
							$row2['JSRQ'] = iconv('UTF-8', 'GBK', $v['JSRQ']);
							$row2['BZ'] = iconv('UTF-8', 'GBK', $v['BZ']);
							$row2['JSLX'] = iconv('UTF-8', 'GBK', $v['JSLX']);
							$row2['ZDQH'] = iconv('UTF-8', 'GBK', $v['ZDQH']);
							$row2['NF'] = iconv('UTF-8', 'GBK', $v['NF']);
							$row2['NFHRQ'] = iconv('UTF-8', 'GBK', $v['NFHRQ']);
							$row2['NJSRQ'] = iconv('UTF-8', 'GBK', $v['NJSRQ']);
							$row2['JB'] = iconv('UTF-8', 'GBK', $v['JB']);
							$row2['ZCS'] = iconv('UTF-8', 'GBK', $v['ZCS']);
						}
						
						
						if($fileName == '教辅应付款-到货未结明细'){
							$row2['YWBMBH'] = iconv('UTF-8', 'GBK', $v['YWBMBH']);
							$row2['GHDWH'] = iconv('UTF-8', 'GBK', $v['GHDWH']);
							$row2['MC'] = iconv('UTF-8', 'GBK', $v['MC']);
							$row2['DHPCH'] = iconv('UTF-8', 'GBK', $v['DHPCH']);
							$row2['SHRQ'] = iconv('UTF-8', 'GBK', $v['SHRQ']);
							$row2['YSDJ'] = iconv('UTF-8', 'GBK', $v['YSDJ']);
							$row2['SSDH'] = iconv('UTF-8', 'GBK', $v['SSDH']);
							$row2['ZDQH'] = iconv('UTF-8', 'GBK', $v['ZDQH']);
							$row2['ZDXH'] = iconv('UTF-8', 'GBK', $v['ZDXH']);
							$row2['SM'] = iconv('UTF-8', 'GBK', $v['SM']);
							$row2['DJ'] = iconv('UTF-8', 'GBK', $v['DJ']);
							$row2['JXSSSL'] = iconv('UTF-8', 'GBK', $v['JXSSSL']);
							$row2['MY'] = iconv('UTF-8', 'GBK', $v['MY']);
							$row2['SYS'] = iconv('UTF-8', 'GBK', $v['SYS']);
							$row2['ZDPCH'] = iconv('UTF-8', 'GBK', $v['ZDPCH']);
							$row2['JSRQ'] = iconv('UTF-8', 'GBK', $v['JSRQ']);
							$row2['JSLX'] = iconv('UTF-8', 'GBK', $v['JSLX']);
							$row2['ZDPCH1'] = iconv('UTF-8', 'GBK', $v['ZDPCH1']);
							$row2['TSLSH'] = iconv('UTF-8', 'GBK', $v['TSLSH']);
							$row2['YSDBRQ'] = iconv('UTF-8', 'GBK', $v['YSDBRQ']);
							$row2['JCDRQ'] = iconv('UTF-8', 'GBK', $v['JCDRQ']);
							$row2['KQBJ'] = iconv('UTF-8', 'GBK', $v['KQBJ']);
							$row2['JSGHDWH'] = iconv('UTF-8', 'GBK', $v['JSGHDWH']);
							$row2['YSGHDWH'] = iconv('UTF-8', 'GBK', $v['YSGHDWH']);
							$row2['FLOWID_DHMX'] = iconv('UTF-8', 'GBK', $v['FLOWID_DHMX']);
							$row2['SHRQ'] = iconv('UTF-8', 'GBK', $v['SHRQ']);
							$row2['YSRQ'] = iconv('UTF-8', 'GBK', $v['YSRQ']);
						}
						if($fileName == '教辅应付款-进退未结明细'){
							$row2['YWBMBH'] = iconv('UTF-8', 'GBK', $v['YWBMBH']);
							$row2['GHDWH'] = iconv('UTF-8', 'GBK', $v['GHDWH']);
							$row2['MC'] = iconv('UTF-8', 'GBK', $v['MC']);
							$row2['DBRQ'] = iconv('UTF-8', 'GBK', $v['DBRQ']);
							$row2['ID'] = iconv('UTF-8', 'GBK', $v['ID']);
							$row2['SM'] = iconv('UTF-8', 'GBK', $v['SM']);
							$row2['DJ'] = iconv('UTF-8', 'GBK', $v['DJ']);
							$row2['ZMY'] = iconv('UTF-8', 'GBK', $v['ZMY']);
							$row2['ZSY'] = iconv('UTF-8', 'GBK', $v['ZSY']);
							$row2['BZ'] = iconv('UTF-8', 'GBK', $v['BZ']);
							$row2['YWPCH'] = iconv('UTF-8', 'GBK', $v['YWPCH']);
							$row2['ZDPCH'] = iconv('UTF-8', 'GBK', $v['ZDPCH']);
							$row2['JSRQ'] = iconv('UTF-8', 'GBK', $v['JSRQ']);
							$row2['JSLX'] = iconv('UTF-8', 'GBK', $v['JSLX']);
							$row2['ZDQH'] = iconv('UTF-8', 'GBK', $v['ZDQH']);
							$row2['ZDXH'] = iconv('UTF-8', 'GBK', $v['ZDXH']);
							$row2['ZDPCH1'] = iconv('UTF-8', 'GBK', $v['ZDPCH1']);
							$row2['TSLSH'] = iconv('UTF-8', 'GBK', $v['TSLSH']);
							$row2['NF'] = iconv('UTF-8', 'GBK', $v['NF']);
							$row2['NFHRQ'] = iconv('UTF-8', 'GBK', $v['NFHRQ']);
							$row2['NJSRQ'] = iconv('UTF-8', 'GBK', $v['NJSRQ']);
							$row2['JSGHDWH'] = iconv('UTF-8', 'GBK', $v['JSGHDWH']);
							$row2['YSGHDWH'] = iconv('UTF-8', 'GBK', $v['YSGHDWH']);
							$row2['ZCS'] = iconv('UTF-8', 'GBK', $v['ZCS']);
							
						}
						
						if($fileName == '教辅应收款-发货未结明细'){
							$row2['CBNY'] = iconv('UTF-8', 'GBK', $v['CBNY']);
							$row2['GHDW'] = iconv('UTF-8', 'GBK', $v['GHDW']);
							$row2['GHDWMC'] = iconv('UTF-8', 'GBK', $v['GHDWMC']);
							$row2['YWBMBH'] = iconv('UTF-8', 'GBK', $v['YWBMBH']);
							$row2['DH'] = iconv('UTF-8', 'GBK', $v['DH']);
							$row2['MC'] = iconv('UTF-8', 'GBK', $v['MC']);
							$row2['PFPCH'] = iconv('UTF-8', 'GBK', $v['PFPCH']);
							$row2['BZ'] = iconv('UTF-8', 'GBK', $v['BZ']);
							$row2['DBRQ'] = iconv('UTF-8', 'GBK', $v['DBRQ']);
							$row2['PFRQ'] = iconv('UTF-8', 'GBK', $v['PFRQ']);
							$row2['ZMY'] = iconv('UTF-8', 'GBK', $v['ZMY']);
							$row2['ZSY'] = iconv('UTF-8', 'GBK', $v['ZSY']);
							$row2['CBJ'] = iconv('UTF-8', 'GBK', $v['CBJ']);
							$row2['TSLSH'] = iconv('UTF-8', 'GBK', $v['TSLSH']);
							$row2['JSRQ'] = iconv('UTF-8', 'GBK', $v['JSRQ']);
							$row2['SM'] = iconv('UTF-8', 'GBK', $v['SM']);
							$row2['DJ'] = iconv('UTF-8', 'GBK', $v['DJ']);
							$row2['ZDQH'] = iconv('UTF-8', 'GBK', $v['ZDQH']);
							$row2['ZDXH'] = iconv('UTF-8', 'GBK', $v['ZDXH']);
							$row2['KFBH'] = iconv('UTF-8', 'GBK', $v['KFBH']);
							$row2['JSLX'] = iconv('UTF-8', 'GBK', $v['JSLX']);
							$row2['NF'] = iconv('UTF-8', 'GBK', $v['NF']);
							$row2['NFHRQ'] = iconv('UTF-8', 'GBK', $v['NFHRQ']);
							$row2['NJSRQ'] = iconv('UTF-8', 'GBK', $v['NJSRQ']);
							$row2['JB'] = iconv('UTF-8', 'GBK', $v['JB']);
							$row2['CWQRPC'] = iconv('UTF-8', 'GBK', $v['CWQRPC']);
							$row2['YSDBRQ'] = iconv('UTF-8', 'GBK', $v['YSDBRQ']);
							$row2['ZCS'] = iconv('UTF-8', 'GBK', $v['ZCS']);
							
						}
						if($fileName == '教辅应收款-销退未结明细'){
							$row2['CBNY'] = iconv('UTF-8', 'GBK', $v['CBNY']);
							$row2['GHDW'] = iconv('UTF-8', 'GBK', $v['GHDW']);
							$row2['GHDWMC'] = iconv('UTF-8', 'GBK', $v['GHDWMC']);
							$row2['YWBMBH'] = iconv('UTF-8', 'GBK', $v['YWBMBH']);
							$row2['KFBH'] = iconv('UTF-8', 'GBK', $v['KFBH']);
							$row2['DH'] = iconv('UTF-8', 'GBK', $v['DH']);
							$row2['DM'] = iconv('UTF-8', 'GBK', $v['DM']);
							$row2['PCH'] = iconv('UTF-8', 'GBK', $v['PCH']);
							$row2['THRQ'] = iconv('UTF-8', 'GBK', $v['THRQ']);
							$row2['ZMY'] = iconv('UTF-8', 'GBK', $v['ZMY']);
							$row2['ZSY'] = iconv('UTF-8', 'GBK', $v['ZSY']);
							$row2['ZCB'] = iconv('UTF-8', 'GBK', $v['ZCB']);
							$row2['TSLSH'] = iconv('UTF-8', 'GBK', $v['TSLSH']);
							$row2['JSRQ'] = iconv('UTF-8', 'GBK', $v['JSRQ']);
							$row2['JSLX'] = iconv('UTF-8', 'GBK', $v['JSLX']);
							$row2['ZDQH'] = iconv('UTF-8', 'GBK', $v['ZDQH']);
							$row2['NF'] = iconv('UTF-8', 'GBK', $v['NF']);
							$row2['NFHRQ'] = iconv('UTF-8', 'GBK', $v['NFHRQ']);
							$row2['NJSRQ'] = iconv('UTF-8', 'GBK', $v['NJSRQ']);
							$row2['JB'] = iconv('UTF-8', 'GBK', $v['JB']);
							$row2['CWQRPC'] = iconv('UTF-8', 'GBK', $v['CWQRPC']);
							$row2['ZCS'] = iconv('UTF-8', 'GBK', $v['ZCS']);
							$row2['DJ'] = iconv('UTF-8', 'GBK', $v['DJ']);

						}
						if($fileName == '教辅应收款-更正未结明细'){
							$row2['YWBMBH'] = iconv('UTF-8', 'GBK', $v['YWBMBH']);
							$row2['KFBH'] = iconv('UTF-8', 'GBK', $v['KFBH']);
							$row2['DH'] = iconv('UTF-8', 'GBK', $v['DH']);
							$row2['DM'] = iconv('UTF-8', 'GBK', $v['DM']);
							$row2['GZPCH'] = iconv('UTF-8', 'GBK', $v['GZPCH']);
							$row2['GZRQ'] = iconv('UTF-8', 'GBK', $v['GZRQ']);
							$row2['ZMY'] = iconv('UTF-8', 'GBK', $v['ZMY']);
							$row2['ZSY'] = iconv('UTF-8', 'GBK', $v['ZSY']);
							$row2['ZCB'] = iconv('UTF-8', 'GBK', $v['ZCB']);
							$row2['TSLSH'] = iconv('UTF-8', 'GBK', $v['TSLSH']);
							$row2['JSRQ'] = iconv('UTF-8', 'GBK', $v['JSRQ']);
							$row2['BZ'] = iconv('UTF-8', 'GBK', $v['BZ']);
							$row2['JSLX'] = iconv('UTF-8', 'GBK', $v['JSLX']);
							$row2['ZDQH'] = iconv('UTF-8', 'GBK', $v['ZDQH']);
							$row2['NF'] = iconv('UTF-8', 'GBK', $v['NF']);
							$row2['NFHRQ'] = iconv('UTF-8', 'GBK', $v['NFHRQ']);
							$row2['NJSRQ'] = iconv('UTF-8', 'GBK', $v['NJSRQ']);
							$row2['JB'] = iconv('UTF-8', 'GBK', $v['JB']);
							$row2['ZCS'] = iconv('UTF-8', 'GBK', $v['ZCS']);

						}
						
						
					}
					
					if($yewugongsi==2){ //教材
						if($fileName == '教材月报'){
							$row2['YWBMBH'] = iconv('UTF-8', 'GBK', $v['YWBMBH']);
							$row2['LX'] = iconv('UTF-8', 'GBK', $v['LX']);
							$row2['BH'] = iconv('UTF-8', 'GBK', $v['BH']);
							$row2['DMMC'] = iconv('UTF-8', 'GBK', $v['DMMC']);
							$row2['QCWJMY'] = iconv('UTF-8', 'GBK', $v['QCWJMY']);
							$row2['QCWJSY'] = iconv('UTF-8', 'GBK', $v['QCWJSY']);
							$row2['QCWJCB'] = iconv('UTF-8', 'GBK', $v['QCWJCB']);
							$row2['JFMY'] = iconv('UTF-8', 'GBK', $v['JFMY']);
							$row2['JFSY'] = iconv('UTF-8', 'GBK', $v['JFSY']);
							$row2['JFCB'] = iconv('UTF-8', 'GBK', $v['JFCB']);
							$row2['JSMY'] = iconv('UTF-8', 'GBK', $v['JSMY']);
							$row2['JSSY'] = iconv('UTF-8', 'GBK', $v['JSSY']);
							$row2['JSCB'] = iconv('UTF-8', 'GBK', $v['JSCB']);
							$row2['QMWJMY'] = iconv('UTF-8', 'GBK', $v['QMWJMY']);
							$row2['QMWJSY'] = iconv('UTF-8', 'GBK', $v['QMWJSY']);
							$row2['QMWJCB'] = iconv('UTF-8', 'GBK', $v['QMWJCB']);
							$row2['SYLX'] = iconv('UTF-8', 'GBK', $v['SYLX']);
							$row2['BB'] = iconv('UTF-8', 'GBK', $v['BB']);
							$row2['MINDATE'] = iconv('UTF-8', 'GBK', $v['MINDATE']);
							$row2['MAXDATE'] = iconv('UTF-8', 'GBK', $v['MAXDATE']);
							$row2['NY'] = iconv('UTF-8', 'GBK', $v['NY']);
							$row2['BJSMY'] = iconv('UTF-8', 'GBK', $v['BJSMY']);
							$row2['BJSSY'] = iconv('UTF-8', 'GBK', $v['BJSSY']);

						}
						if($fileName == '教材到货明细'){
							$row2['YWBMBH'] = iconv('UTF-8', 'GBK', $v['YWBMBH']);
							$row2['GHDWH'] = iconv('UTF-8', 'GBK', $v['GHDWH']);
							$row2['MC'] = iconv('UTF-8', 'GBK', $v['MC']);
							$row2['DHPCH'] = iconv('UTF-8', 'GBK', $v['DHPCH']);
							$row2['SHRQ'] = iconv('UTF-8', 'GBK', $v['SHRQ']);
							$row2['YSDJ'] = iconv('UTF-8', 'GBK', $v['YSDJ']);
							$row2['SSDH'] = iconv('UTF-8', 'GBK', $v['SSDH']);
							$row2['ZDQH'] = iconv('UTF-8', 'GBK', $v['ZDQH']);
							$row2['ZDXH'] = iconv('UTF-8', 'GBK', $v['ZDXH']);
							$row2['SM'] = iconv('UTF-8', 'GBK', $v['SM']);
							$row2['DJ'] = iconv('UTF-8', 'GBK', $v['DJ']);
							$row2['JXSSSL'] = iconv('UTF-8', 'GBK', $v['JXSSSL']);
							$row2['MY'] = iconv('UTF-8', 'GBK', $v['MY']);
							$row2['SYS'] = iconv('UTF-8', 'GBK', $v['SYS']);
							$row2['ZDPCH'] = iconv('UTF-8', 'GBK', $v['ZDPCH']);
							$row2['JSRQ'] = iconv('UTF-8', 'GBK', $v['JSRQ']);
							$row2['JSLX'] = iconv('UTF-8', 'GBK', $v['JSLX']);
							$row2['ZDPCH1'] = iconv('UTF-8', 'GBK', $v['ZDPCH1']);
							$row2['TSLSH'] = iconv('UTF-8', 'GBK', $v['TSLSH']);
							$row2['YSRQ'] = iconv('UTF-8', 'GBK', $v['YSRQ']);
							$row2['YSDBRQ'] = iconv('UTF-8', 'GBK', $v['YSDBRQ']);
							$row2['JCDRQ'] = iconv('UTF-8', 'GBK', $v['JCDRQ']);
							$row2['KQBJ'] = iconv('UTF-8', 'GBK', $v['KQBJ']);
							$row2['JSGHDWH'] = iconv('UTF-8', 'GBK', $v['JSGHDWH']);
							$row2['YSGHDWH'] = iconv('UTF-8', 'GBK', $v['YSGHDWH']);
							$row2['FLOWID_DHMX'] = iconv('UTF-8', 'GBK', $v['FLOWID_DHMX']);

						}
						
						
						if($fileName == '教材进退明细'){
							$row2['YWBMBH'] = iconv('UTF-8', 'GBK', $v['YWBMBH']);
							$row2['GHDWH'] = iconv('UTF-8', 'GBK', $v['GHDWH']);
							$row2['MC'] = iconv('UTF-8', 'GBK', $v['MC']);
							$row2['DBRQ'] = iconv('UTF-8', 'GBK', $v['DBRQ']);
							$row2['ID'] = iconv('UTF-8', 'GBK', $v['ID']);
							$row2['SM'] = iconv('UTF-8', 'GBK', $v['SM']);
							$row2['DJ'] = iconv('UTF-8', 'GBK', $v['DJ']);
							$row2['ZMY'] = iconv('UTF-8', 'GBK', $v['ZMY']);
							$row2['ZSY'] = iconv('UTF-8', 'GBK', $v['ZSY']);
							$row2['BZ'] = iconv('UTF-8', 'GBK', $v['BZ']);
							$row2['YWPCH'] = iconv('UTF-8', 'GBK', $v['YWPCH']);
							$row2['ZDPCH'] = iconv('UTF-8', 'GBK', $v['ZDPCH']);
							$row2['JSRQ'] = iconv('UTF-8', 'GBK', $v['JSRQ']);
							$row2['JSLX'] = iconv('UTF-8', 'GBK', $v['JSLX']);
							$row2['ZDQH'] = iconv('UTF-8', 'GBK', $v['ZDQH']);
							$row2['ZDXH'] = iconv('UTF-8', 'GBK', $v['ZDXH']);
							$row2['ZDPCH1'] = iconv('UTF-8', 'GBK', $v['ZDPCH1']);
							$row2['TSLSH'] = iconv('UTF-8', 'GBK', $v['TSLSH']);
							$row2['NF'] = iconv('UTF-8', 'GBK', $v['NF']);
							$row2['NFHRQ'] = iconv('UTF-8', 'GBK', $v['NFHRQ']);
							$row2['NJSRQ'] = iconv('UTF-8', 'GBK', $v['NJSRQ']);
							$row2['JSGHDWH'] = iconv('UTF-8', 'GBK', $v['JSGHDWH']);
							$row2['YSGHDWH'] = iconv('UTF-8', 'GBK', $v['YSGHDWH']);
							$row2['ZCS'] = iconv('UTF-8', 'GBK', $v['ZCS']);

						}

						if($fileName == '教材出版社免费让利明细'){
							$row2['YWBMBH'] = iconv('UTF-8', 'GBK', $v['YWBMBH']);
							$row2['GHDWH'] = iconv('UTF-8', 'GBK', $v['GHDWH']);
							$row2['MC'] = iconv('UTF-8', 'GBK', $v['MC']);
							$row2['DBRQ'] = iconv('UTF-8', 'GBK', $v['DBRQ']);
							$row2['ID'] = iconv('UTF-8', 'GBK', $v['ID']);
							$row2['SM'] = iconv('UTF-8', 'GBK', $v['SM']);
							$row2['DJ'] = iconv('UTF-8', 'GBK', $v['DJ']);
							$row2['ZMY'] = iconv('UTF-8', 'GBK', $v['ZMY']);
							$row2['ZSY'] = iconv('UTF-8', 'GBK', $v['ZSY']);
							$row2['BZ'] = iconv('UTF-8', 'GBK', $v['BZ']);
							$row2['YWPCH'] = iconv('UTF-8', 'GBK', $v['YWPCH']);
							$row2['ZDPCH'] = iconv('UTF-8', 'GBK', $v['ZDPCH']);
							$row2['JSRQ'] = iconv('UTF-8', 'GBK', $v['JSRQ']);
							$row2['JSLX'] = iconv('UTF-8', 'GBK', $v['JSLX']);
							$row2['ZDQH'] = iconv('UTF-8', 'GBK', $v['ZDQH']);
							$row2['ZDXH'] = iconv('UTF-8', 'GBK', $v['ZDXH']);
							$row2['ZDPCH1'] = iconv('UTF-8', 'GBK', $v['ZDPCH1']);
							$row2['TSLSH'] = iconv('UTF-8', 'GBK', $v['TSLSH']);
							$row2['NF'] = iconv('UTF-8', 'GBK', $v['NF']);
							$row2['NFHRQ'] = iconv('UTF-8', 'GBK', $v['NFHRQ']);
							$row2['NJSRQ'] = iconv('UTF-8', 'GBK', $v['NJSRQ']);
							$row2['JSGHDWH'] = iconv('UTF-8', 'GBK', $v['JSGHDWH']);
							$row2['YSGHDWH'] = iconv('UTF-8', 'GBK', $v['YSGHDWH']);
							$row2['ZSC'] = iconv('UTF-8', 'GBK', $v['ZSC']);

						}
						
						if($fileName == '教材销退明细'){
							$row2['GHDW'] = iconv('UTF-8', 'GBK', $v['GHDW']);
							$row2['GHDWMC'] = iconv('UTF-8', 'GBK', $v['GHDWMC']);
							$row2['YWBMBH'] = iconv('UTF-8', 'GBK', $v['YWBMBH']);
							$row2['KFBH'] = iconv('UTF-8', 'GBK', $v['KFBH']);
							$row2['DH'] = iconv('UTF-8', 'GBK', $v['DH']);
							$row2['DM'] = iconv('UTF-8', 'GBK', $v['DM']);
							$row2['PCH'] = iconv('UTF-8', 'GBK', $v['PCH']);
							$row2['THRQ'] = iconv('UTF-8', 'GBK', $v['THRQ']);
							$row2['ZMY'] = iconv('UTF-8', 'GBK', $v['ZMY']);
							$row2['ZSY'] = iconv('UTF-8', 'GBK', $v['ZSY']);
							$row2['ZCB'] = iconv('UTF-8', 'GBK', $v['ZCB']);
							$row2['TSLSH'] = iconv('UTF-8', 'GBK', $v['TSLSH']);
							$row2['JSRQ'] = iconv('UTF-8', 'GBK', $v['JSRQ']);
							$row2['JSLX'] = iconv('UTF-8', 'GBK', $v['JSLX']);
							$row2['ZDQH'] = iconv('UTF-8', 'GBK', $v['ZDQH']);
							$row2['NF'] = iconv('UTF-8', 'GBK', $v['NF']);
							$row2['NFHRQ'] = iconv('UTF-8', 'GBK', $v['NFHRQ']);
							$row2['NJSRQ'] = iconv('UTF-8', 'GBK', $v['NJSRQ']);
							$row2['JB'] = iconv('UTF-8', 'GBK', $v['JB']);
							$row2['CWQRPC'] = iconv('UTF-8', 'GBK', $v['CWQRPC']);
							$row2['ZCS'] = iconv('UTF-8', 'GBK', $v['ZCS']);
							$row2['DJ'] = iconv('UTF-8', 'GBK', $v['DJ']);
							$row2['ID'] = iconv('UTF-8', 'GBK', $v['ID']);
							$row2['BB'] = iconv('UTF-8', 'GBK', $v['BB']);
							$row2['SM'] = iconv('UTF-8', 'GBK', $v['SM']);
							$row2['ISBN'] = iconv('UTF-8', 'GBK', $v['ISBN']);
							$row2['CBNY'] = iconv('UTF-8', 'GBK', $v['CBNY']);
						}

						if($fileName == '教材基层店免费让利明细'){
							$row2['YWBMBH'] = iconv('UTF-8', 'GBK', $v['YWBMBH']);
							$row2['KFBH'] = iconv('UTF-8', 'GBK', $v['KFBH']);
							$row2['DH'] = iconv('UTF-8', 'GBK', $v['DH']);
							$row2['DM'] = iconv('UTF-8', 'GBK', $v['DM']);
							$row2['GZPCH'] = iconv('UTF-8', 'GBK', $v['GZPCH']);
							$row2['GZRQ'] = iconv('UTF-8', 'GBK', $v['GZRQ']);
							$row2['ZMY'] = iconv('UTF-8', 'GBK', $v['ZMY']);
							$row2['ZSY'] = iconv('UTF-8', 'GBK', $v['ZSY']);
							$row2['ZCB'] = iconv('UTF-8', 'GBK', $v['ZCB']);
							$row2['TSLSH'] = iconv('UTF-8', 'GBK', $v['TSLSH']);
							$row2['JSRQ'] = iconv('UTF-8', 'GBK', $v['JSRQ']);
							$row2['BZ'] = iconv('UTF-8', 'GBK', $v['BZ']);
							$row2['JSLX'] = iconv('UTF-8', 'GBK', $v['JSLX']);
							$row2['ZDQH'] = iconv('UTF-8', 'GBK', $v['ZDQH']);
							$row2['NF'] = iconv('UTF-8', 'GBK', $v['NF']);
							$row2['NFHRQ'] = iconv('UTF-8', 'GBK', $v['NFHRQ']);
							$row2['NJSRQ'] = iconv('UTF-8', 'GBK', $v['NJSRQ']);
							$row2['JB'] = iconv('UTF-8', 'GBK', $v['JB']);
							$row2['ZCS'] = iconv('UTF-8', 'GBK', $v['ZCS']);

						}
						
						if($fileName == '教材发货明细'){
							$row2['CBNY'] = iconv('UTF-8', 'GBK', $v['CBNY']);
							$row2['GHDW'] = iconv('UTF-8', 'GBK', $v['GHDW']);
							$row2['GHDWMC'] = iconv('UTF-8', 'GBK', $v['GHDWMC']);
							$row2['YWBMBH'] = iconv('UTF-8', 'GBK', $v['YWBMBH']);
							$row2['DH'] = iconv('UTF-8', 'GBK', $v['DH']);
							$row2['MC'] = iconv('UTF-8', 'GBK', $v['MC']);
							$row2['PFPCH'] = iconv('UTF-8', 'GBK', $v['PFPCH']);
							$row2['BZ'] = iconv('UTF-8', 'GBK', $v['BZ']);
							$row2['DBRQ'] = iconv('UTF-8', 'GBK', $v['DBRQ']);
							$row2['PFRQ'] = iconv('UTF-8', 'GBK', $v['PFRQ']);
							$row2['ZMY'] = iconv('UTF-8', 'GBK', $v['ZMY']);
							$row2['ZSY'] = iconv('UTF-8', 'GBK', $v['ZSY']);
							$row2['CBJ'] = iconv('UTF-8', 'GBK', $v['CBJ']);
							$row2['TSLSH'] = iconv('UTF-8', 'GBK', $v['TSLSH']);
							$row2['JSRQ'] = iconv('UTF-8', 'GBK', $v['JSRQ']);
							$row2['SM'] = iconv('UTF-8', 'GBK', $v['SM']);
							$row2['DJ'] = iconv('UTF-8', 'GBK', $v['DJ']);
							$row2['ZDQH'] = iconv('UTF-8', 'GBK', $v['ZDQH']);
							$row2['ZDXH'] = iconv('UTF-8', 'GBK', $v['ZDXH']);
							$row2['KFBH'] = iconv('UTF-8', 'GBK', $v['KFBH']);
							$row2['JSLX'] = iconv('UTF-8', 'GBK', $v['JSLX']);
							$row2['NF'] = iconv('UTF-8', 'GBK', $v['NF']);
							$row2['NFHRQ'] = iconv('UTF-8', 'GBK', $v['NFHRQ']);
							$row2['NJSRQ'] = iconv('UTF-8', 'GBK', $v['NJSRQ']);
							$row2['JB'] = iconv('UTF-8', 'GBK', $v['JB']);
							$row2['CWQRPC'] = iconv('UTF-8', 'GBK', $v['CWQRPC']);
							$row2['YSDBRQ'] = iconv('UTF-8', 'GBK', $v['YSDBRQ']);
							$row2['ZCS'] = iconv('UTF-8', 'GBK', $v['ZCS']);
							
						}

					
						if($fileName == '教材库存明细'){
							$row2['YWBMBH'] = iconv('UTF-8', 'GBK', $v['YWBMBH']);
							$row2['ID'] = iconv('UTF-8', 'GBK', $v['ID']);
							$row2['SM'] = iconv('UTF-8', 'GBK', $v['SM']);
							$row2['DJ'] = iconv('UTF-8', 'GBK', $v['DJ']);
							$row2['BB'] = iconv('UTF-8', 'GBK', $v['BB']);
							$row2['KCCS'] = iconv('UTF-8', 'GBK', $v['KCCS']);
							$row2['MY'] = iconv('UTF-8', 'GBK', $v['MY']);
							$row2['SYS'] = iconv('UTF-8', 'GBK', $v['SYS']);
							$row2['KF'] = iconv('UTF-8', 'GBK', $v['KF']);
							$row2['WL'] = iconv('UTF-8', 'GBK', $v['WL']);
							$row2['FL'] = iconv('UTF-8', 'GBK', $v['FL']);
							$row2['HW'] = iconv('UTF-8', 'GBK', $v['HW']);
							$row2['GHDW'] = iconv('UTF-8', 'GBK', $v['GHDW']);
							$row2['YWY'] = iconv('UTF-8', 'GBK', $v['YWY']);
							$row2['ISBN'] = iconv('UTF-8', 'GBK', $v['ISBN']);
							$row2['BC'] = iconv('UTF-8', 'GBK', $v['BC']);
							$row2['BIANZHE'] = iconv('UTF-8', 'GBK', $v['BIANZHE']);
							$row2['NIAN'] = iconv('UTF-8', 'GBK', $v['NIAN']);
	
						}
						if($fileName == '教材库存明细提前出'){
							$row2['YWBMBH'] = iconv('UTF-8', 'GBK', $v['YWBMBH']);
							$row2['ID'] = iconv('UTF-8', 'GBK', $v['ID']);
							$row2['SM'] = iconv('UTF-8', 'GBK', $v['SM']);
							$row2['DJ'] = iconv('UTF-8', 'GBK', $v['DJ']);
							$row2['BB'] = iconv('UTF-8', 'GBK', $v['BB']);
							$row2['KCCS'] = iconv('UTF-8', 'GBK', $v['KCCS']);
							$row2['MY'] = iconv('UTF-8', 'GBK', $v['MY']);
							$row2['SYS'] = iconv('UTF-8', 'GBK', $v['SYS']);
							$row2['KF'] = iconv('UTF-8', 'GBK', $v['KF']);
							$row2['WL'] = iconv('UTF-8', 'GBK', $v['WL']);
							$row2['FL'] = iconv('UTF-8', 'GBK', $v['FL']);
							$row2['HW'] = iconv('UTF-8', 'GBK', $v['HW']);
							$row2['GHDW'] = iconv('UTF-8', 'GBK', $v['GHDW']);
							$row2['YWY'] = iconv('UTF-8', 'GBK', $v['YWY']);
							$row2['ISBN'] = iconv('UTF-8', 'GBK', $v['ISBN']);
							$row2['BC'] = iconv('UTF-8', 'GBK', $v['BC']);
							$row2['BIANZHE'] = iconv('UTF-8', 'GBK', $v['BIANZHE']);
							$row2['NIAN'] = iconv('UTF-8', 'GBK', $v['NIAN']);
	
						}
						
						if($fileName == '教材配发中间态'){
							$row2['YWBMBH'] = iconv('UTF-8', 'GBK', $v['YWBMBH']);
							$row2['YWBMMC'] = iconv('UTF-8', 'GBK', $v['YWBMMC']);
							$row2['DH'] = iconv('UTF-8', 'GBK', $v['DH']);
							$row2['DM'] = iconv('UTF-8', 'GBK', $v['DM']);
							$row2['ID'] = iconv('UTF-8', 'GBK', $v['ID']);
							$row2['ISBN'] = iconv('UTF-8', 'GBK', $v['ISBN']);
							$row2['SM'] = iconv('UTF-8', 'GBK', $v['SM']);
							$row2['DJ'] = iconv('UTF-8', 'GBK', $v['DJ']);
							$row2['CBNY'] = iconv('UTF-8', 'GBK', $v['CBNY']);
							$row2['PXCS'] = iconv('UTF-8', 'GBK', $v['PXCS']);
							$row2['ZMY'] = iconv('UTF-8', 'GBK', $v['ZMY']);
							$row2['SYS'] = iconv('UTF-8', 'GBK', $v['SYS']);
							$row2['CBJ'] = iconv('UTF-8', 'GBK', $v['CBJ']);
							$row2['PXDH'] = iconv('UTF-8', 'GBK', $v['PXDH']);
							$row2['BZ'] = iconv('UTF-8', 'GBK', $v['BZ']);
							$row2['ZDH'] = iconv('UTF-8', 'GBK', $v['ZDH']);
							$row2['SYLX'] = iconv('UTF-8', 'GBK', $v['SYLX']);
							$row2['SL'] = iconv('UTF-8', 'GBK', $v['SL']);
							$row2['ZJXS'] = iconv('UTF-8', 'GBK', $v['ZJXS']);
							$row2['QX'] = iconv('UTF-8', 'GBK', $v['QX']);
							$row2['DJBH'] = iconv('UTF-8', 'GBK', $v['DJBH']);
							$row2['KFBH'] = iconv('UTF-8', 'GBK', $v['KFBH']);
							$row2['BJLSH'] = iconv('UTF-8', 'GBK', $v['BJLSH']);
							$row2['CYBH'] = iconv('UTF-8', 'GBK', $v['CYBH']);
							$row2['FLOWID'] = iconv('UTF-8', 'GBK', $v['FLOWID']);
							$row2['ZDQH'] = iconv('UTF-8', 'GBK', $v['ZDQH']);
							$row2['ZDXH'] = iconv('UTF-8', 'GBK', $v['ZDXH']);
							$row2['ZDQH1'] = iconv('UTF-8', 'GBK', $v['ZDQH1']);
							$row2['ZDXH1'] = iconv('UTF-8', 'GBK', $v['ZDXH1']);
							$row2['PFRQ'] = iconv('UTF-8', 'GBK', $v['PFRQ']);
						}
						
						if($fileName == '教材库房转移中间态明细'){
							$row2['ID'] = iconv('UTF-8', 'GBK', $v['ID']);
							$row2['SM'] = iconv('UTF-8', 'GBK', $v['SM']);
							$row2['ISBN'] = iconv('UTF-8', 'GBK', $v['ISBN']);
							$row2['DJ'] = iconv('UTF-8', 'GBK', $v['DJ']);
							$row2['BB'] = iconv('UTF-8', 'GBK', $v['BB']);
							$row2['GHDW'] = iconv('UTF-8', 'GBK', $v['GHDW']);
							$row2['YWY'] = iconv('UTF-8', 'GBK', $v['YWY']);
							$row2['CBNY'] = iconv('UTF-8', 'GBK', $v['CBNY']);
							$row2['YWLX'] = iconv('UTF-8', 'GBK', $v['YWLX']);
							$row2['CS'] = iconv('UTF-8', 'GBK', $v['SUM(CS)']);
							$row2['MY'] = iconv('UTF-8', 'GBK', $v['MY']);
							$row2['SY'] = iconv('UTF-8', 'GBK', $v['SY']);
						}
						
						if($fileName == '教材损益明细'){
							$row2['KFBH'] = iconv('UTF-8', 'GBK', $v['KFBH']);
							$row2['SM'] = iconv('UTF-8', 'GBK', $v['SM']);
							$row2['CRKCS'] = iconv('UTF-8', 'GBK', $v['CRKCS']);
							$row2['DJ'] = iconv('UTF-8', 'GBK', $v['DJ']); 
							$row2['ZMY'] = iconv('UTF-8', 'GBK', $v['ZMY']);
							$row2['CRKSY'] = iconv('UTF-8', 'GBK', $v['CRKSY']);
							$row2['CRKRQ'] = iconv('UTF-8', 'GBK', $v['CRKRQ']);
	
						}
						if($fileName == '教材当月已结算-到货已结-到货'){
							$row2['YWBMBH'] = iconv('UTF-8', 'GBK', $v['YWBMBH']);
							$row2['GHDWH'] = iconv('UTF-8', 'GBK', $v['GHDWH']);
							$row2['MC'] = iconv('UTF-8', 'GBK', $v['MC']);
							$row2['DHPCH'] = iconv('UTF-8', 'GBK', $v['DHPCH']);
							$row2['SHRQ'] = iconv('UTF-8', 'GBK', $v['SHRQ']);
							$row2['YSDJ'] = iconv('UTF-8', 'GBK', $v['YSDJ']);
							$row2['SSDH'] = iconv('UTF-8', 'GBK', $v['SSDH']);
							$row2['ZDQH'] = iconv('UTF-8', 'GBK', $v['ZDQH']);
							$row2['ZDXH'] = iconv('UTF-8', 'GBK', $v['ZDXH']);
							$row2['SM'] = iconv('UTF-8', 'GBK', $v['SM']);
							$row2['DJ'] = iconv('UTF-8', 'GBK', $v['DJ']);
							$row2['JXSSSL'] = iconv('UTF-8', 'GBK', $v['JXSSSL']);
							$row2['MY'] = iconv('UTF-8', 'GBK', $v['MY']);
							$row2['SYS'] = iconv('UTF-8', 'GBK', $v['SYS']);
							$row2['ZDPCH'] = iconv('UTF-8', 'GBK', $v['ZDPCH']);
							$row2['JSRQ'] = iconv('UTF-8', 'GBK', $v['JSRQ']);
							$row2['JSLX'] = iconv('UTF-8', 'GBK', $v['JSLX']);
							$row2['ZDPCH1'] = iconv('UTF-8', 'GBK', $v['ZDPCH1']);
							$row2['TSLSH'] = iconv('UTF-8', 'GBK', $v['TSLSH']);
							$row2['YSRQ'] = iconv('UTF-8', 'GBK', $v['YSRQ']);
							$row2['YSDBRQ'] = iconv('UTF-8', 'GBK', $v['YSDBRQ']);
							$row2['JCDRQ'] = iconv('UTF-8', 'GBK', $v['JCDRQ']);
							$row2['KQBJ'] = iconv('UTF-8', 'GBK', $v['KQBJ']);
							$row2['JSGHDWH'] = iconv('UTF-8', 'GBK', $v['JSGHDWH']);
							$row2['YSGHDWH'] = iconv('UTF-8', 'GBK', $v['YSGHDWH']);
							$row2['FLOWID_DHMX'] = iconv('UTF-8', 'GBK', $v['FLOWID_DHMX']);
						}
						if($fileName == '教材当月已结算-到货已结-进退'){
							$row2['YWBMBH'] = iconv('UTF-8', 'GBK', $v['YWBMBH']);
							$row2['GHDWH'] = iconv('UTF-8', 'GBK', $v['GHDWH']);
							$row2['MC'] = iconv('UTF-8', 'GBK', $v['MC']);
							$row2['DBRQ'] = iconv('UTF-8', 'GBK', $v['DBRQ']);
							$row2['ID'] = iconv('UTF-8', 'GBK', $v['ID']);
							$row2['SM'] = iconv('UTF-8', 'GBK', $v['SM']);
							$row2['DJ'] = iconv('UTF-8', 'GBK', $v['DJ']);
							$row2['ZMY'] = iconv('UTF-8', 'GBK', $v['ZMY']);
							$row2['ZSY'] = iconv('UTF-8', 'GBK', $v['ZSY']);
							$row2['BZ'] = iconv('UTF-8', 'GBK', $v['BZ']);
							$row2['YWPCH'] = iconv('UTF-8', 'GBK', $v['YWPCH']);
							$row2['ZDPCH'] = iconv('UTF-8', 'GBK', $v['ZDPCH']);
							$row2['JSRQ'] = iconv('UTF-8', 'GBK', $v['JSRQ']);
							$row2['JSLX'] = iconv('UTF-8', 'GBK', $v['JSLX']);
							$row2['ZDQH'] = iconv('UTF-8', 'GBK', $v['ZDQH']);
							$row2['ZDPCH1'] = iconv('UTF-8', 'GBK', $v['ZDPCH1']);
							$row2['TSLSH'] = iconv('UTF-8', 'GBK', $v['TSLSH']);
							$row2['NF'] = iconv('UTF-8', 'GBK', $v['NF']);
							$row2['NFHRQ'] = iconv('UTF-8', 'GBK', $v['NFHRQ']);
							$row2['NJSRQ'] = iconv('UTF-8', 'GBK', $v['NJSRQ']);
							$row2['JSGHDWH'] = iconv('UTF-8', 'GBK', $v['JSGHDWH']);
							$row2['YSGHDWH'] = iconv('UTF-8', 'GBK', $v['YSGHDWH']);
							$row2['ZCS'] = iconv('UTF-8', 'GBK', $v['ZCS']);
						}
						if($fileName == '教材当月已结算-到货已结-退货'){
							$row2['YWBMBH'] = iconv('UTF-8', 'GBK', $v['YWBMBH']);
							$row2['GHDWH'] = iconv('UTF-8', 'GBK', $v['GHDWH']);
							$row2['MC'] = iconv('UTF-8', 'GBK', $v['MC']);
							$row2['ID'] = iconv('UTF-8', 'GBK', $v['ID']);
							$row2['DBRQ'] = iconv('UTF-8', 'GBK', $v['DBRQ']);
							$row2['SM'] = iconv('UTF-8', 'GBK', $v['SM']);
							$row2['DJ'] = iconv('UTF-8', 'GBK', $v['DJ']);
							$row2['ZMY'] = iconv('UTF-8', 'GBK', $v['ZMY']);
							$row2['ZSY'] = iconv('UTF-8', 'GBK', $v['ZSY']);
							$row2['BZ'] = iconv('UTF-8', 'GBK', $v['BZ']);
							$row2['YWPCH'] = iconv('UTF-8', 'GBK', $v['YWPCH']);
							$row2['JSRQ'] = iconv('UTF-8', 'GBK', $v['JSRQ']);
							$row2['ZDPCH'] = iconv('UTF-8', 'GBK', $v['ZDPCH']);
							$row2['JSLX'] = iconv('UTF-8', 'GBK', $v['JSLX']);
							$row2['ZDQH'] = iconv('UTF-8', 'GBK', $v['ZDQH']);
							$row2['ZDXH'] = iconv('UTF-8', 'GBK', $v['ZDXH']);
							$row2['ZDPCH1'] = iconv('UTF-8', 'GBK', $v['ZDPCH1']);
							$row2['TSLSH'] = iconv('UTF-8', 'GBK', $v['TSLSH']);
							$row2['NF'] = iconv('UTF-8', 'GBK', $v['NF']);
							$row2['NFHRQ'] = iconv('UTF-8', 'GBK', $v['NFHRQ']);
							$row2['NJSRQ'] = iconv('UTF-8', 'GBK', $v['NJSRQ']);
							$row2['JSGHDWH'] = iconv('UTF-8', 'GBK', $v['JSGHDWH']);
							$row2['YSGHDWH'] = iconv('UTF-8', 'GBK', $v['YSGHDWH']);
							$row2['ZCS'] = iconv('UTF-8', 'GBK', $v['ZCS']);
							
						}
						if($fileName == '教材当月已结算-到货已结-更正'){
							$row2['YWBMBH'] = iconv('UTF-8', 'GBK', $v['YWBMBH']);
							$row2['DH'] = iconv('UTF-8', 'GBK', $v['DH']);
							$row2['MC'] = iconv('UTF-8', 'GBK', $v['MC']);
							$row2['PFPCH'] = iconv('UTF-8', 'GBK', $v['PFPCH']);
							$row2['BZ'] = iconv('UTF-8', 'GBK', $v['BZ']);
							$row2['DBRQ'] = iconv('UTF-8', 'GBK', $v['DBRQ']);
							$row2['PFRQ'] = iconv('UTF-8', 'GBK', $v['PFRQ']);
							$row2['ZMY'] = iconv('UTF-8', 'GBK', $v['ZMY']);
							$row2['ZSY'] = iconv('UTF-8', 'GBK', $v['ZSY']);
							$row2['CBJ'] = iconv('UTF-8', 'GBK', $v['CBJ']);
							$row2['TSLSH'] = iconv('UTF-8', 'GBK', $v['TSLSH']);
							$row2['JSRQ'] = iconv('UTF-8', 'GBK', $v['JSRQ']);
							$row2['SM'] = iconv('UTF-8', 'GBK', $v['SM']);
							$row2['DJ'] = iconv('UTF-8', 'GBK', $v['DJ']);
							$row2['ZDQH'] = iconv('UTF-8', 'GBK', $v['ZDQH']);
							$row2['ZDXH'] = iconv('UTF-8', 'GBK', $v['ZDXH']);
							$row2['ZDPCH1'] = iconv('UTF-8', 'GBK', $v['ZDPCH1']);
							$row2['KFBH'] = iconv('UTF-8', 'GBK', $v['KFBH']);
							$row2['JSLX'] = iconv('UTF-8', 'GBK', $v['JSLX']);
							$row2['NF'] = iconv('UTF-8', 'GBK', $v['NF']);
							$row2['NFHRQ'] = iconv('UTF-8', 'GBK', $v['NFHRQ']);
							$row2['NJSRQ'] = iconv('UTF-8', 'GBK', $v['NJSRQ']);
							$row2['JB'] = iconv('UTF-8', 'GBK', $v['JB']);
							$row2['CWQRPC'] = iconv('UTF-8', 'GBK', $v['CWQRPC']);
							$row2['YSDBRQ'] = iconv('UTF-8', 'GBK', $v['YSDBRQ']);
							$row2['ZCS'] = iconv('UTF-8', 'GBK', $v['ZCS']);
						}
						if($fileName == '教材当月已结算-发货已结-到货'){
							$row2['CBNY'] = iconv('UTF-8', 'GBK', $v['CBNY']);
							$row2['GHDW'] = iconv('UTF-8', 'GBK', $v['GHDW']);
							$row2['GHDWMC'] = iconv('UTF-8', 'GBK', $v['GHDWMC']);
							$row2['YWBMBH'] = iconv('UTF-8', 'GBK', $v['YWBMBH']);
							$row2['DH'] = iconv('UTF-8', 'GBK', $v['DH']);
							$row2['MC'] = iconv('UTF-8', 'GBK', $v['MC']);
							$row2['PFPCH'] = iconv('UTF-8', 'GBK', $v['PFPCH']);
							$row2['BZ'] = iconv('UTF-8', 'GBK', $v['BZ']);
							$row2['DBRQ'] = iconv('UTF-8', 'GBK', $v['DBRQ']);
							$row2['PFRQ'] = iconv('UTF-8', 'GBK', $v['PFRQ']);
							$row2['ZMY'] = iconv('UTF-8', 'GBK', $v['ZMY']);
							$row2['ZSY'] = iconv('UTF-8', 'GBK', $v['ZSY']);
							$row2['CBJ'] = iconv('UTF-8', 'GBK', $v['CBJ']);
							$row2['TSLSH'] = iconv('UTF-8', 'GBK', $v['TSLSH']);
							$row2['JSRQ'] = iconv('UTF-8', 'GBK', $v['JSRQ']);
							$row2['SM'] = iconv('UTF-8', 'GBK', $v['SM']);
							$row2['DJ'] = iconv('UTF-8', 'GBK', $v['DJ']);
							$row2['ZDQH'] = iconv('UTF-8', 'GBK', $v['ZDQH']);
							$row2['ZDXH'] = iconv('UTF-8', 'GBK', $v['ZDXH']);
							$row2['KFBH'] = iconv('UTF-8', 'GBK', $v['KFBH']);
							$row2['JSLX'] = iconv('UTF-8', 'GBK', $v['JSLX']);
							$row2['NF'] = iconv('UTF-8', 'GBK', $v['NF']);
							$row2['NFHRQ'] = iconv('UTF-8', 'GBK', $v['NFHRQ']);
							$row2['NJSRQ'] = iconv('UTF-8', 'GBK', $v['NJSRQ']);
							$row2['JB'] = iconv('UTF-8', 'GBK', $v['JB']);
							$row2['CWQRPC'] = iconv('UTF-8', 'GBK', $v['CWQRPC']);
							$row2['YSDBRQ'] = iconv('UTF-8', 'GBK', $v['YSDBRQ']);
							$row2['ZCS'] = iconv('UTF-8', 'GBK', $v['ZCS']);
						}
						if($fileName == '教材当月已结算-发货已结-退货'){
							$row2['CBNY'] = iconv('UTF-8', 'GBK', $v['CBNY']);
							$row2['GHDW'] = iconv('UTF-8', 'GBK', $v['GHDW']);
							$row2['GHDWMC'] = iconv('UTF-8', 'GBK', $v['GHDWMC']);
							$row2['YWBMBH'] = iconv('UTF-8', 'GBK', $v['YWBMBH']);
							$row2['KFBH'] = iconv('UTF-8', 'GBK', $v['KFBH']);
							$row2['DH'] = iconv('UTF-8', 'GBK', $v['DH']);
							$row2['DM'] = iconv('UTF-8', 'GBK', $v['DM']);
							$row2['PCH'] = iconv('UTF-8', 'GBK', $v['PCH']);
							$row2['THRQ'] = iconv('UTF-8', 'GBK', $v['THRQ']);
							$row2['ZMY'] = iconv('UTF-8', 'GBK', $v['ZMY']);
							$row2['ZSY'] = iconv('UTF-8', 'GBK', $v['ZSY']);
							$row2['ZCB'] = iconv('UTF-8', 'GBK', $v['ZCB']);
							$row2['TSLSH'] = iconv('UTF-8', 'GBK', $v['TSLSH']);
							$row2['JSRQ'] = iconv('UTF-8', 'GBK', $v['JSRQ']);
							$row2['JSLX'] = iconv('UTF-8', 'GBK', $v['JSLX']);
							$row2['ZDQH'] = iconv('UTF-8', 'GBK', $v['ZDQH']);
							$row2['NF'] = iconv('UTF-8', 'GBK', $v['NF']);
							$row2['NFHRQ'] = iconv('UTF-8', 'GBK', $v['NFHRQ']);
							$row2['NJSRQ'] = iconv('UTF-8', 'GBK', $v['NJSRQ']);
							$row2['JB'] = iconv('UTF-8', 'GBK', $v['JB']);
							$row2['CWQRPC'] = iconv('UTF-8', 'GBK', $v['CWQRPC']);
							$row2['ZCS'] = iconv('UTF-8', 'GBK', $v['ZCS']);
							$row2['DJ'] = iconv('UTF-8', 'GBK', $v['DJ']);
							$row2['ID'] = iconv('UTF-8', 'GBK', $v['ID']);
							$row2['BB'] = iconv('UTF-8', 'GBK', $v['BB']);
							$row2['SM'] = iconv('UTF-8', 'GBK', $v['SM']);
							$row2['ISBN'] = iconv('UTF-8', 'GBK', $v['ISBN']);
							$row2['CBNY'] = iconv('UTF-8', 'GBK', $v['CBNY']);
						}
						if($fileName == '教材当月已结算-发货已结-更正'){
							$row2['CBNY'] = iconv('UTF-8', 'GBK', $v['CBNY']);
							$row2['GHDW'] = iconv('UTF-8', 'GBK', $v['GHDW']);
							$row2['GHDWMC'] = iconv('UTF-8', 'GBK', $v['GHDWMC']);
							$row2['YWBMBH'] = iconv('UTF-8', 'GBK', $v['YWBMBH']);
							$row2['KFBH'] = iconv('UTF-8', 'GBK', $v['KFBH']);
							$row2['DH'] = iconv('UTF-8', 'GBK', $v['DH']);
							$row2['DM'] = iconv('UTF-8', 'GBK', $v['DM']);
							$row2['GZPCH'] = iconv('UTF-8', 'GBK', $v['GZPCH']);
							$row2['GZRQ'] = iconv('UTF-8', 'GBK', $v['GZRQ']);
							$row2['ZMY'] = iconv('UTF-8', 'GBK', $v['ZMY']);
							$row2['ZSY'] = iconv('UTF-8', 'GBK', $v['ZSY']);
							$row2['ZCB'] = iconv('UTF-8', 'GBK', $v['ZCB']);
							$row2['TSLSH'] = iconv('UTF-8', 'GBK', $v['TSLSH']);
							$row2['JSRQ'] = iconv('UTF-8', 'GBK', $v['JSRQ']);
							$row2['BZ'] = iconv('UTF-8', 'GBK', $v['BZ']);
							$row2['JSLX'] = iconv('UTF-8', 'GBK', $v['JSLX']);
							$row2['ZDQH'] = iconv('UTF-8', 'GBK', $v['ZDQH']);
							$row2['NF'] = iconv('UTF-8', 'GBK', $v['NF']);
							$row2['NFHRQ'] = iconv('UTF-8', 'GBK', $v['NFHRQ']);
							$row2['NJSRQ'] = iconv('UTF-8', 'GBK', $v['NJSRQ']);
							$row2['JB'] = iconv('UTF-8', 'GBK', $v['JB']);
							$row2['ZCS'] = iconv('UTF-8', 'GBK', $v['ZCS']);
						}
						
						
						if($fileName == '教材应付款-到货未结明细'){
							$row2['YWBMBH'] = iconv('UTF-8', 'GBK', $v['YWBMBH']);
							$row2['GHDWH'] = iconv('UTF-8', 'GBK', $v['GHDWH']);
							$row2['MC'] = iconv('UTF-8', 'GBK', $v['MC']);
							$row2['DHPCH'] = iconv('UTF-8', 'GBK', $v['DHPCH']);
							$row2['SHRQ'] = iconv('UTF-8', 'GBK', $v['SHRQ']);
							$row2['YSDJ'] = iconv('UTF-8', 'GBK', $v['YSDJ']);
							$row2['SSDH'] = iconv('UTF-8', 'GBK', $v['SSDH']);
							$row2['ZDQH'] = iconv('UTF-8', 'GBK', $v['ZDQH']);
							$row2['ZDXH'] = iconv('UTF-8', 'GBK', $v['ZDXH']);
							$row2['SM'] = iconv('UTF-8', 'GBK', $v['SM']);
							$row2['DJ'] = iconv('UTF-8', 'GBK', $v['DJ']);
							$row2['JXSSSL'] = iconv('UTF-8', 'GBK', $v['JXSSSL']);
							$row2['MY'] = iconv('UTF-8', 'GBK', $v['MY']);
							$row2['SYS'] = iconv('UTF-8', 'GBK', $v['SYS']);
							$row2['ZDPCH'] = iconv('UTF-8', 'GBK', $v['ZDPCH']);
							$row2['JSRQ'] = iconv('UTF-8', 'GBK', $v['JSRQ']);
							$row2['JSLX'] = iconv('UTF-8', 'GBK', $v['JSLX']);
							$row2['ZDPCH1'] = iconv('UTF-8', 'GBK', $v['ZDPCH1']);
							$row2['TSLSH'] = iconv('UTF-8', 'GBK', $v['TSLSH']);
							$row2['YSDBRQ'] = iconv('UTF-8', 'GBK', $v['YSDBRQ']);
							$row2['JCDRQ'] = iconv('UTF-8', 'GBK', $v['JCDRQ']);
							$row2['KQBJ'] = iconv('UTF-8', 'GBK', $v['KQBJ']);
							$row2['JSGHDWH'] = iconv('UTF-8', 'GBK', $v['JSGHDWH']);
							$row2['YSGHDWH'] = iconv('UTF-8', 'GBK', $v['YSGHDWH']);
							$row2['FLOWID_DHMX'] = iconv('UTF-8', 'GBK', $v['FLOWID_DHMX']);
							$row2['SHRQ'] = iconv('UTF-8', 'GBK', $v['SHRQ']);
							$row2['YSRQ'] = iconv('UTF-8', 'GBK', $v['YSRQ']);
						}
						if($fileName == '教材应付款-进退未结明细'){
							$row2['YWBMBH'] = iconv('UTF-8', 'GBK', $v['YWBMBH']);
							$row2['GHDWH'] = iconv('UTF-8', 'GBK', $v['GHDWH']);
							$row2['MC'] = iconv('UTF-8', 'GBK', $v['MC']);
							$row2['DBRQ'] = iconv('UTF-8', 'GBK', $v['DBRQ']);
							$row2['ID'] = iconv('UTF-8', 'GBK', $v['ID']);
							$row2['SM'] = iconv('UTF-8', 'GBK', $v['SM']);
							$row2['DJ'] = iconv('UTF-8', 'GBK', $v['DJ']);
							$row2['ZMY'] = iconv('UTF-8', 'GBK', $v['ZMY']);
							$row2['ZSY'] = iconv('UTF-8', 'GBK', $v['ZSY']);
							$row2['BZ'] = iconv('UTF-8', 'GBK', $v['BZ']);
							$row2['YWPCH'] = iconv('UTF-8', 'GBK', $v['YWPCH']);
							$row2['ZDPCH'] = iconv('UTF-8', 'GBK', $v['ZDPCH']);
							$row2['JSRQ'] = iconv('UTF-8', 'GBK', $v['JSRQ']);
							$row2['JSLX'] = iconv('UTF-8', 'GBK', $v['JSLX']);
							$row2['ZDQH'] = iconv('UTF-8', 'GBK', $v['ZDQH']);
							$row2['ZDXH'] = iconv('UTF-8', 'GBK', $v['ZDXH']);
							$row2['ZDPCH1'] = iconv('UTF-8', 'GBK', $v['ZDPCH1']);
							$row2['TSLSH'] = iconv('UTF-8', 'GBK', $v['TSLSH']);
							$row2['NF'] = iconv('UTF-8', 'GBK', $v['NF']);
							$row2['NFHRQ'] = iconv('UTF-8', 'GBK', $v['NFHRQ']);
							$row2['NJSRQ'] = iconv('UTF-8', 'GBK', $v['NJSRQ']);
							$row2['JSGHDWH'] = iconv('UTF-8', 'GBK', $v['JSGHDWH']);
							$row2['YSGHDWH'] = iconv('UTF-8', 'GBK', $v['YSGHDWH']);
							$row2['ZCS'] = iconv('UTF-8', 'GBK', $v['ZCS']);
							
						}
						if($fileName == '教材应付款-出版社让利未结'){
							$row2['YWBMBH'] = iconv('UTF-8', 'GBK', $v['YWBMBH']);
							$row2['GHDWH'] = iconv('UTF-8', 'GBK', $v['GHDWH']);
							$row2['MC'] = iconv('UTF-8', 'GBK', $v['MC']);
							$row2['DHPCH'] = iconv('UTF-8', 'GBK', $v['DHPCH']);
							$row2['SHRQ'] = iconv('UTF-8', 'GBK', $v['SHRQ']);
							$row2['YSDJ'] = iconv('UTF-8', 'GBK', $v['YSDJ']);
							$row2['SSDH'] = iconv('UTF-8', 'GBK', $v['SSDH']);
							$row2['ZDQH'] = iconv('UTF-8', 'GBK', $v['ZDQH']);
							$row2['ZDXH'] = iconv('UTF-8', 'GBK', $v['ZDXH']);
							$row2['SM'] = iconv('UTF-8', 'GBK', $v['SM']);
							$row2['BZ'] = iconv('UTF-8', 'GBK', $v['BZ']);
							$row2['DJ'] = iconv('UTF-8', 'GBK', $v['DJ']);
							$row2['JXSSSL'] = iconv('UTF-8', 'GBK', $v['JXSSSL']);
							$row2['MY'] = iconv('UTF-8', 'GBK', 0);
							$row2['SYS'] = iconv('UTF-8', 'GBK', $v['SYS']);
							$row2['ZDPCH'] = iconv('UTF-8', 'GBK', $v['ZDPCH']);
							$row2['JSRQ'] = iconv('UTF-8', 'GBK', $v['JSRQ']);
							$row2['JSLX'] = iconv('UTF-8', 'GBK', $v['JSLX']);
							$row2['JSGHDWH'] = iconv('UTF-8', 'GBK', $v['JSGHDWH']);
							$row2['YSGHDWH'] = iconv('UTF-8', 'GBK', $v['YSGHDWH']);
							$row2['TSLSH'] = iconv('UTF-8', 'GBK', $v['TSLSH']);
						}
						if($fileName == '教材应收款-发货未结明细'){
							$row2['CBNY'] = iconv('UTF-8', 'GBK', $v['CBNY']);
							$row2['GHDW'] = iconv('UTF-8', 'GBK', $v['GHDW']);
							$row2['GHDWMC'] = iconv('UTF-8', 'GBK', $v['GHDWMC']);
							$row2['YWBMBH'] = iconv('UTF-8', 'GBK', $v['YWBMBH']);
							$row2['DH'] = iconv('UTF-8', 'GBK', $v['DH']);
							$row2['MC'] = iconv('UTF-8', 'GBK', $v['MC']);
							$row2['PFPCH'] = iconv('UTF-8', 'GBK', $v['PFPCH']);
							$row2['BZ'] = iconv('UTF-8', 'GBK', $v['BZ']);
							$row2['DBRQ'] = iconv('UTF-8', 'GBK', $v['DBRQ']);
							$row2['PFRQ'] = iconv('UTF-8', 'GBK', $v['PFRQ']);
							$row2['ZMY'] = iconv('UTF-8', 'GBK', $v['ZMY']);
							$row2['ZSY'] = iconv('UTF-8', 'GBK', $v['ZSY']);
							$row2['CBJ'] = iconv('UTF-8', 'GBK', $v['CBJ']);
							$row2['TSLSH'] = iconv('UTF-8', 'GBK', $v['TSLSH']);
							$row2['JSRQ'] = iconv('UTF-8', 'GBK', $v['JSRQ']);
							$row2['SM'] = iconv('UTF-8', 'GBK', $v['SM']);
							$row2['DJ'] = iconv('UTF-8', 'GBK', $v['DJ']);
							$row2['ZDQH'] = iconv('UTF-8', 'GBK', $v['ZDQH']);
							$row2['ZDXH'] = iconv('UTF-8', 'GBK', $v['ZDXH']);
							$row2['KFBH'] = iconv('UTF-8', 'GBK', $v['KFBH']);
							$row2['JSLX'] = iconv('UTF-8', 'GBK', $v['JSLX']);
							$row2['NF'] = iconv('UTF-8', 'GBK', $v['NF']);
							$row2['NFHRQ'] = iconv('UTF-8', 'GBK', $v['NFHRQ']);
							$row2['NJSRQ'] = iconv('UTF-8', 'GBK', $v['NJSRQ']);
							$row2['JB'] = iconv('UTF-8', 'GBK', $v['JB']);
							$row2['CWQRPC'] = iconv('UTF-8', 'GBK', $v['CWQRPC']);
							$row2['YSDBRQ'] = iconv('UTF-8', 'GBK', $v['YSDBRQ']);
							$row2['ZCS'] = iconv('UTF-8', 'GBK', $v['ZCS']);
							
						}
						if($fileName == '教材应收款-销退未结明细'){
							$row2['CBNY'] = iconv('UTF-8', 'GBK', $v['CBNY']);
							$row2['GHDW'] = iconv('UTF-8', 'GBK', $v['GHDW']);
							$row2['GHDWMC'] = iconv('UTF-8', 'GBK', $v['GHDWMC']);
							$row2['YWBMBH'] = iconv('UTF-8', 'GBK', $v['YWBMBH']);
							$row2['KFBH'] = iconv('UTF-8', 'GBK', $v['KFBH']);
							$row2['DH'] = iconv('UTF-8', 'GBK', $v['DH']);
							$row2['DM'] = iconv('UTF-8', 'GBK', $v['DM']);
							$row2['PCH'] = iconv('UTF-8', 'GBK', $v['PCH']);
							$row2['THRQ'] = iconv('UTF-8', 'GBK', $v['THRQ']);
							$row2['ZMY'] = iconv('UTF-8', 'GBK', $v['ZMY']);
							$row2['ZSY'] = iconv('UTF-8', 'GBK', $v['ZSY']);
							$row2['ZCB'] = iconv('UTF-8', 'GBK', $v['ZCB']);
							$row2['TSLSH'] = iconv('UTF-8', 'GBK', $v['TSLSH']);
							$row2['JSRQ'] = iconv('UTF-8', 'GBK', $v['JSRQ']);
							$row2['JSLX'] = iconv('UTF-8', 'GBK', $v['JSLX']);
							$row2['ZDQH'] = iconv('UTF-8', 'GBK', $v['ZDQH']);
							$row2['NF'] = iconv('UTF-8', 'GBK', $v['NF']);
							$row2['NFHRQ'] = iconv('UTF-8', 'GBK', $v['NFHRQ']);
							$row2['NJSRQ'] = iconv('UTF-8', 'GBK', $v['NJSRQ']);
							$row2['JB'] = iconv('UTF-8', 'GBK', $v['JB']);
							$row2['CWQRPC'] = iconv('UTF-8', 'GBK', $v['CWQRPC']);
							$row2['ZCS'] = iconv('UTF-8', 'GBK', $v['ZCS']);
							$row2['DJ'] = iconv('UTF-8', 'GBK', $v['DJ']);

						}
						if($fileName == '教材应收款-更正未结明细'){
							$row2['YWBMBH'] = iconv('UTF-8', 'GBK', $v['YWBMBH']);
							$row2['KFBH'] = iconv('UTF-8', 'GBK', $v['KFBH']);
							$row2['DH'] = iconv('UTF-8', 'GBK', $v['DH']);
							$row2['DM'] = iconv('UTF-8', 'GBK', $v['DM']);
							$row2['GZPCH'] = iconv('UTF-8', 'GBK', $v['GZPCH']);
							$row2['GZRQ'] = iconv('UTF-8', 'GBK', $v['GZRQ']);
							$row2['ZMY'] = iconv('UTF-8', 'GBK', $v['ZMY']);
							$row2['ZSY'] = iconv('UTF-8', 'GBK', $v['ZSY']);
							$row2['ZCB'] = iconv('UTF-8', 'GBK', $v['ZCB']);
							$row2['TSLSH'] = iconv('UTF-8', 'GBK', $v['TSLSH']);
							$row2['JSRQ'] = iconv('UTF-8', 'GBK', $v['JSRQ']);
							$row2['BZ'] = iconv('UTF-8', 'GBK', $v['BZ']);
							$row2['JSLX'] = iconv('UTF-8', 'GBK', $v['JSLX']);
							$row2['ZDQH'] = iconv('UTF-8', 'GBK', $v['ZDQH']);
							$row2['NF'] = iconv('UTF-8', 'GBK', $v['NF']);
							$row2['NFHRQ'] = iconv('UTF-8', 'GBK', $v['NFHRQ']);
							$row2['NJSRQ'] = iconv('UTF-8', 'GBK', $v['NJSRQ']);
							$row2['JB'] = iconv('UTF-8', 'GBK', $v['JB']);
							$row2['ZCS'] = iconv('UTF-8', 'GBK', $v['ZCS']);

						}


					}
					if($yewugongsi==3){  //连锁
						if($fileName == '差异-更正差异'){
							$row2['ZMY'] = iconv('UTF-8', 'GBK', $v['ZMY']);
							$row2['ZSY'] = iconv('UTF-8', 'GBK', $v['ZSY']);
							$row2['YWBMBH'] = iconv('UTF-8', 'GBK', $v['YWBMBH']);
							
						}
						if($fileName == '差异-到货'){
							$row2['LX'] = iconv('UTF-8', 'GBK', $v['LX']);
							$row2['ZMY'] = iconv('UTF-8', 'GBK', $v['ZMY']);
							$row2['ZSY'] = iconv('UTF-8', 'GBK', $v['ZSY']);
							$row2['YWBMBH'] = iconv('UTF-8', 'GBK', $v['YWBMBH']);
	
						}
						if($fileName == '差异-发货'){
							$row2['LX'] = iconv('UTF-8', 'GBK', $v['LX']);
							$row2['SUM(ZMY)'] = iconv('UTF-8', 'GBK', $v['SUM(ZMY)']);
							$row2['SUM(CBJ)'] = iconv('UTF-8', 'GBK', $v['SUM(CBJ)']);
							$row2['YWBMBH'] = iconv('UTF-8', 'GBK', $v['YWBMBH']);
	
						}
						
						if($fileName == '库存明细'){
							$row2['YWBMBH'] = iconv('UTF-8', 'GBK', $v['业务部门编号']);
							$row2['ID'] = iconv('UTF-8', 'GBK', $v['ID']);
							$row2['ISBN'] = iconv('UTF-8', 'GBK', $v['ISBN']);
							$row2['SM'] = iconv('UTF-8', 'GBK', $v['书名']);
							$row2['BB'] = iconv('UTF-8', 'GBK', $v['版本']);
							$row2['BBMC'] = iconv('UTF-8', 'GBK', $v['版本名称']);
							$row2['CS'] = iconv('UTF-8', 'GBK', $v['册数']);
							$row2['MY'] = iconv('UTF-8', 'GBK', $v['码洋']);
							$row2['SY'] = iconv('UTF-8', 'GBK', $v['实样']);
							$row2['BHSSY'] = iconv('UTF-8', 'GBK', $v['不含税实样']);
							$row2['DJ'] = iconv('UTF-8', 'GBK', $v['定价']);
							$row2['GHDW'] = iconv('UTF-8', 'GBK', $v['供货单位']);
							$row2['GHDWMC'] = iconv('UTF-8', 'GBK', $v['供货单位名称']);
							$row2['YSNY'] = iconv('UTF-8', 'GBK', $v['原始年月']);
							$row2['CBNY'] = iconv('UTF-8', 'GBK', $v['出版年月']);
							$row2['BC'] = iconv('UTF-8', 'GBK', $v['版次']);
							$row2['FL'] = iconv('UTF-8', 'GBK', $v['分类']);
							$row2['FLBH'] = iconv('UTF-8', 'GBK', $v['分类编号']);
							$row2['LBBH'] = iconv('UTF-8', 'GBK', $v['类别编号']);
							$row2['LB'] = iconv('UTF-8', 'GBK', $v['类别']);
	
						}
						if($fileName == '应付款-采购未结-明细'){
							$row2['DBRQ'] = iconv('UTF-8', 'GBK', $v['DBRQ']);
							$row2['YSDJ'] = iconv('UTF-8', 'GBK', $v['YSDJ']);
							$row2['YSRQ'] = iconv('UTF-8', 'GBK', $v['YSRQ']);
							$row2['DM'] = iconv('UTF-8', 'GBK', $v['DM']);
							$row2['ZPZ'] = iconv('UTF-8', 'GBK', $v['ZPZ']);
							$row2['ZCS'] = iconv('UTF-8', 'GBK', $v['ZCS']);
							$row2['ZMY'] = iconv('UTF-8', 'GBK', $v['ZMY']);
							$row2['ZSY'] = iconv('UTF-8', 'GBK', $v['ZSY']);
							$row2['BHSZSY'] = iconv('UTF-8', 'GBK', $v['BHSZSY']);
							$row2['YWBMMC'] = iconv('UTF-8', 'GBK', $v['YWBMMC']);
							$row2['BZ'] = iconv('UTF-8', 'GBK', $v['BZ']);
							$row2['DJLX'] = iconv('UTF-8', 'GBK', $v['DJLX']);
							$row2['SL1'] = iconv('UTF-8', 'GBK', $v['SL1']);
	
						}
						if($fileName == '应收款-发出未结-明细'){
							$row2['DBRQ'] = iconv('UTF-8', 'GBK', $v['DBRQ']);
							$row2['PFPCH'] = iconv('UTF-8', 'GBK', $v['PFPCH']);
							$row2['DM'] = iconv('UTF-8', 'GBK', $v['DM']);
							$row2['JSDM'] = iconv('UTF-8', 'GBK', $v['JSDM']);
							$row2['ZPZ'] = iconv('UTF-8', 'GBK', $v['ZPZ']);
							$row2['ZCS'] = iconv('UTF-8', 'GBK', $v['ZCS']);
							$row2['ZMY'] = iconv('UTF-8', 'GBK', $v['ZMY']);
							$row2['ZSY'] = iconv('UTF-8', 'GBK', $v['ZSY']);
							$row2['CBJ'] = iconv('UTF-8', 'GBK', $v['CBJ']);
							$row2['BHSZSY'] = iconv('UTF-8', 'GBK', $v['BHSZSY']);
							$row2['BHSCBJ'] = iconv('UTF-8', 'GBK', $v['BHSCBJ']);
							$row2['YWBMMC'] = iconv('UTF-8', 'GBK', $v['YWBMMC']);
							$row2['YWBMBH'] = iconv('UTF-8', 'GBK', $v['YWBMBH']);
							$row2['BZ'] = iconv('UTF-8', 'GBK', $v['BZ']);
							$row2['DJLX'] = iconv('UTF-8', 'GBK', $v['DJLX']);
							$row2['SL1'] = iconv('UTF-8', 'GBK', $v['SL1']);
							$row2['DQ'] = iconv('UTF-8', 'GBK', $v['DQ']);

						}
						if($fileName == '应付款月报'){
							$row2['TJRQ'] = iconv('UTF-8', 'GBK', $v['QMRQ']);
							$row2['TJNY'] = iconv('UTF-8', 'GBK', $v['QCRQ']);
							$row2['GHDWH'] = '('.iconv('UTF-8', 'GBK', $v['GHDWH']).')'.iconv('UTF-8', 'GBK', $v['GHDWHMC']);
							$row2['YWBMBH'] = iconv('UTF-8', 'GBK', $v['YWBMBH']);
							$row2['QCMY'] = iconv('UTF-8', 'GBK', $v['QCMY']);
							$row2['QCSY'] = iconv('UTF-8', 'GBK', $v['QCSY']);
							$row2['BQZJMY'] = iconv('UTF-8', 'GBK', $v['BQZJMY']);
							$row2['BQZJSY'] = iconv('UTF-8', 'GBK', $v['BQZJSY']);
							$row2['BQJSMY'] = iconv('UTF-8', 'GBK', $v['BQJSMY']);
							$row2['BQJSSY'] = iconv('UTF-8', 'GBK', $v['BQJSSY']);
							$row2['QMMY'] = iconv('UTF-8', 'GBK', $v['QMMY']);
							$row2['QMSY'] = iconv('UTF-8', 'GBK', $v['QMSY']);
						}
						if($fileName == '应付款月报含税率'){
							$row2['QMRQ'] = iconv('UTF-8', 'GBK', $v['QMRQ']);
							$row2['QCRQ'] = iconv('UTF-8', 'GBK', $v['QCRQ']);
							$row2['YWBMBH'] = iconv('UTF-8', 'GBK', $v['YWBMBH']);
							$row2['GHDWH'] = '('.iconv('UTF-8', 'GBK', $v['GHDWH']).')'.iconv('UTF-8', 'GBK', $v['GHDWHMC']);
							$row2['SL'] = iconv('UTF-8', 'GBK', $v['SL']);
							$row2['QCMY'] = iconv('UTF-8', 'GBK', $v['QCMY']);
							$row2['QCSY'] = iconv('UTF-8', 'GBK', $v['QCSY']);
							$row2['BQZJMY'] = iconv('UTF-8', 'GBK', $v['BQZJMY']);
							$row2['BQZJSY'] = iconv('UTF-8', 'GBK', $v['BQZJSY']);
							$row2['BQJSMY'] = iconv('UTF-8', 'GBK', $v['BQJSMY']);
							$row2['BQJSSY'] = iconv('UTF-8', 'GBK', $v['BQJSSY']);
							$row2['QMMY'] = iconv('UTF-8', 'GBK', $v['QMMY']);
							$row2['QMSY'] = iconv('UTF-8', 'GBK', $v['QMSY']);
						}
						if($fileName == '应收款月报'){
							$row2['QMRQ'] = iconv('UTF-8', 'GBK', $v['QMRQ']);
							$row2['QCRQ'] = iconv('UTF-8', 'GBK', $v['QCRQ']);
							$row2['YWBMBH'] = iconv('UTF-8', 'GBK', $v['YWBMBH']);
							$row2['DH'] = iconv('UTF-8', 'GBK', $v['DH']);
							$row2['SL'] = iconv('UTF-8', 'GBK', $v['SL']);
							$row2['QCMY'] = iconv('UTF-8', 'GBK', $v['QCMY']);
							$row2['QCSY'] = iconv('UTF-8', 'GBK', $v['QCSY']);
							$row2['QCCBJ'] = iconv('UTF-8', 'GBK', $v['QCCBJ']);
							$row2['BQZJMY'] = iconv('UTF-8', 'GBK', $v['BQZJMY']);
							$row2['BQZJSY'] = iconv('UTF-8', 'GBK', $v['BQZJSY']);
							$row2['BQZJCBJ'] = iconv('UTF-8', 'GBK', $v['BQZJCBJ']);
							$row2['BQJSMY'] = iconv('UTF-8', 'GBK', $v['BQJSMY']);
							$row2['BQJSSY'] = iconv('UTF-8', 'GBK', $v['BQJSSY']);
							$row2['BQJSCBJ'] = iconv('UTF-8', 'GBK', $v['BQJSCBJ']);
							$row2['QMMY'] = iconv('UTF-8', 'GBK', $v['QMMY']);
							$row2['QMSY'] = iconv('UTF-8', 'GBK', $v['QMSY']);
							$row2['QMCBJ'] = iconv('UTF-8', 'GBK', $v['QMCBJ']);
						}
						if($fileName == '应收款月报含税率'){
							$row2['QMRQ'] = iconv('UTF-8', 'GBK', $v['QMRQ']);
							$row2['QCRQ'] = iconv('UTF-8', 'GBK', $v['QCRQ']);
							$row2['DH'] = iconv('UTF-8', 'GBK', $v['DH']);
							$row2['YWBMBH'] = iconv('UTF-8', 'GBK', $v['YWBMBH']);
							$row2['SL'] = iconv('UTF-8', 'GBK', $v['SL']);
							$row2['QCMY'] = iconv('UTF-8', 'GBK', $v['QCMY']);
							$row2['QCSY'] = iconv('UTF-8', 'GBK', $v['QCSY']);
							$row2['QCCBJ'] = iconv('UTF-8', 'GBK', $v['QCCBJ']);
							$row2['BQZJMY'] = iconv('UTF-8', 'GBK', $v['BQZJMY']);
							$row2['BQZJSY'] = iconv('UTF-8', 'GBK', $v['BQZJSY']);
							$row2['BQZJCBJ'] = iconv('UTF-8', 'GBK', $v['BQZJCBJ']);
							$row2['BQJSMY'] = iconv('UTF-8', 'GBK', $v['BQJSMY']);
							$row2['BQJSSY'] = iconv('UTF-8', 'GBK', $v['BQJSSY']);
							$row2['QMMY'] = iconv('UTF-8', 'GBK', $v['QMMY']);
							$row2['QMSY'] = iconv('UTF-8', 'GBK', $v['QMSY']);
							$row2['QMCBJ'] = iconv('UTF-8', 'GBK', $v['QMCBJ']);
							$row2['DQMY'] = iconv('UTF-8', 'GBK', $v['DQMY']);
							$row2['DQSY'] = iconv('UTF-8', 'GBK', $v['DQSY']);
							$row2['DQCBJ'] = iconv('UTF-8', 'GBK', $v['DQCBJ']);
						}
						if($fileName == '本期流水'){
							$row2['SYMY'] = iconv('UTF-8', 'GBK', $v['损益码洋']);
							$row2['SYSY'] = iconv('UTF-8', 'GBK', $v['损益实样']);
							$row2['BFMY'] = iconv('UTF-8', 'GBK', $v['报废码洋']);
							$row2['BFSY'] = iconv('UTF-8', 'GBK', $v['报废实样']);
							$row2['CDHMY'] = iconv('UTF-8', 'GBK', $v['纯到货码洋']);
							$row2['CDHSY'] = iconv('UTF-8', 'GBK', $v['纯到货实样']);
							$row2['CFHMY'] = iconv('UTF-8', 'GBK', $v['纯发货码洋']);
							$row2['CFHSY'] = iconv('UTF-8', 'GBK', $v['纯发货实洋']);
	
						}
						if($fileName == '汇总数据'){
							$row2['QCRQ'] = iconv('UTF-8', 'GBK', $v['期初日期']);
							$row2['QMRQ'] = iconv('UTF-8', 'GBK', $v['期末日期']);
							$row2['YWBMBH'] = iconv('UTF-8', 'GBK', $v['业务部门编号']);
							$row2['ZMY'] = iconv('UTF-8', 'GBK', $v['总码洋']);
							$row2['ZSY'] = iconv('UTF-8', 'GBK', $v['总实样']);
							$row2['CBJ'] = iconv('UTF-8', 'GBK', $v['成本价']);
							$row2['YWLX'] = iconv('UTF-8', 'GBK', $v['类型']);
	
						}

						
						
						
						
					}

					fputcsv($fp,$row2);
				 }
				//每1万条数据就刷新缓冲区
				unset($row2);
				ob_flush();
				flush();
			}
		}
		//exit;
		showmessage('下载成功！', HTTP_REFERER);//改成后退不刷新页面。。用js做
   }
	
	//连锁-库存明细 导出到服务器指定位置
public function yuebao1($sql,$fileName,$title,$total,$yewugongsi,$user_hwbm,$timetype)   
	{
		set_time_limit(0);
		ini_set('memory_limit', '-1');
		ini_set("max_execution_time", "0");
		if($yewugongsi==3){
			$ip='172.30.153.63/xhsddb';
			$port='1521';
			$user= 'dbsl';
			$pass= 'dbsl';
			$charset='utf8';
			pc_base::load_app_class('oracle_admin','admin',0);//数据库
			$ora=new oracle_admin($user,$pass,$ip,$charset);
		}
		$total = $total;
		$fileName = $fileName;
		$user_hwbm = $user_hwbm; //登录的用户名
		$begin = microtime(true);
		$lujing = jcOption($timetype,$yewugongsi); // 获取到文件存放地址，以及文件名拼接需要的时间类型（月份，季度，年度）
		$path = PHPCMS_PATH.'uploadfile/'.$lujing['path'];
		
		//$path = PHPCMS_PATH.'uploadfile/csv/' ;
		if(!file_exists($path)) {
			//exit('不存在此路径');
            mkdir( iconv('UTF-8','GBK',$path), 0777, true );
        }
		$filepath = $path.$user_hwbm.'-'.iconv("UTF-8","gbk",$lujing['file_path']).'-'.iconv("UTF-8","gbk",$fileName).'.csv';  //$timetype 为一月份  一季度  1-6
		if($filepath){ 
			unlink($filepath); //如果存在此文件，那就删掉，然后下载最新的
		}
		$fp = fopen($filepath,"a"); //打开csv文件，如果不存在则创建
		
		$nums = 5000 ;
		$step = ceil($total / $nums);
		$title = $title ;
		foreach($title as $key => $item) {
			$title[$key] = iconv('UTF-8', 'GBK', $item);
		}
		fputcsv($fp, $title);
			  $sql= $sql;
			  $row1 = $ora->query($sql);
				if($row1) {
                    $row2=array();				
					foreach($row1 as $k => $v) {
					$row2['YWBMBH'] = iconv('UTF-8', 'GBK', $v['业务部门编号']);
					$row2['ID'] = iconv('UTF-8', 'GBK', $v['ID']);
					$row2['ISBN'] = iconv('UTF-8', 'GBK', $v['ISBN']);
					$row2['SM'] = iconv('UTF-8', 'GBK', $v['书名']);
					$row2['BB'] = iconv('UTF-8', 'GBK', $v['版本']);
					$row2['BBMC'] = iconv('UTF-8', 'GBK', $v['版本名称']);
					$row2['CS'] = iconv('UTF-8', 'GBK', $v['册数']);
					$row2['MY'] = iconv('UTF-8', 'GBK', $v['码洋']);
					$row2['SY'] = iconv('UTF-8', 'GBK', $v['实样']);
					$row2['BHSSY'] = iconv('UTF-8', 'GBK', $v['不含税实样']);
					$row2['DJ'] = iconv('UTF-8', 'GBK', $v['定价']);
					$row2['GHDW'] = iconv('UTF-8', 'GBK', $v['供货单位']);
					$row2['GHDWMC'] = iconv('UTF-8', 'GBK', $v['供货单位名称']);
					$row2['YSNY'] = iconv('UTF-8', 'GBK', $v['原始年月']);
					$row2['CBNY'] = iconv('UTF-8', 'GBK', $v['出版年月']);
					$row2['BC'] = iconv('UTF-8', 'GBK', $v['版次']);
					$row2['FL'] = iconv('UTF-8', 'GBK', $v['分类']);
					$row2['FLBH'] = iconv('UTF-8', 'GBK', $v['分类编号']);
					$row2['LBBH'] = iconv('UTF-8', 'GBK', $v['类别编号']);
					$row2['LB'] = iconv('UTF-8', 'GBK', $v['类别']);
					fputcsv($fp,$row2);
				 }
				//每1万条数据就刷新缓冲区
				unset($row2);
				ob_flush();
				flush();
			}
		//showmessage('下载成功！', HTTP_REFERER);//改成后退不刷新页面。。用js做
   }

	
	public function login() {
		if(isset($_GET['dosubmit'])) {
			
			//不为口令卡验证
			if (!isset($_GET['card'])) {
				$username = isset($_POST['username']) ? trim($_POST['username']) : showmessage(L('nameerror'),HTTP_REFERER);
//				$code = isset($_POST['code']) && trim($_POST['code']) ? trim($_POST['code']) : showmessage(L('input_code'), HTTP_REFERER);
//				if ($_SESSION['code'] != strtolower($code)) {
//					$_SESSION['code'] = '';
//					showmessage(L('code_error'), HTTP_REFERER);
//				}
//				$_SESSION['code'] = '';
			} else { //口令卡验证
				if (!isset($_SESSION['card_verif']) || $_SESSION['card_verif'] != 1) {
					showmessage(L('your_password_card_is_not_validate'), '?m=admin&c=index&a=public_card');
				}
				$username = $_SESSION['card_username'] ? $_SESSION['card_username'] :  showmessage(L('nameerror'),HTTP_REFERER);
			}
			if(!is_username($username)){
				showmessage(L('username_illegal'), HTTP_REFERER);
			}
			//密码错误剩余重试次数
			$this->times_db = pc_base::load_model('times_model');
			$rtime = $this->times_db->get_one(array('username'=>$username,'isadmin'=>1));
			$maxloginfailedtimes = getcache('common','commons');
			$maxloginfailedtimes = (int)$maxloginfailedtimes['maxloginfailedtimes'];

			if($rtime['times'] >= $maxloginfailedtimes) {
				$minute = 60-floor((SYS_TIME-$rtime['logintime'])/60);
				if($minute>0) showmessage(L('wait_1_hour',array('minute'=>$minute)));
			}
			//查询帐号
			$r = $this->db->get_one(array('username'=>$username));
			if(!$r) showmessage(L('user_not_exist'),'?m=admin&c=index&a=login');
			$password = md5(md5(trim((!isset($_GET['card']) ? $_POST['password'] : $_SESSION['card_password']))).$r['encrypt']);
			
			if($r['password'] != $password) {
				$ip = ip();
				if($rtime && $rtime['times'] < $maxloginfailedtimes) {
					$times = $maxloginfailedtimes-intval($rtime['times']);
					$this->times_db->update(array('ip'=>$ip,'isadmin'=>1,'times'=>'+=1'),array('username'=>$username));
				} else {
					$this->times_db->delete(array('username'=>$username,'isadmin'=>1));
					$this->times_db->insert(array('username'=>$username,'ip'=>$ip,'isadmin'=>1,'logintime'=>SYS_TIME,'times'=>1));
					$times = $maxloginfailedtimes;
				}
				showmessage(L('password_error',array('times'=>$times)),'?m=admin&c=index&a=login',3000);
			}
			$this->times_db->delete(array('username'=>$username));
			
			//查看是否使用口令卡
			if (!isset($_GET['card']) && $r['card'] && pc_base::load_config('system', 'safe_card') == 1) {
				$_SESSION['card_username'] = $username;
				$_SESSION['card_password'] = $_POST['password'];
				header("location:?m=admin&c=index&a=public_card");
				exit;
			} elseif (isset($_GET['card']) && pc_base::load_config('system', 'safe_card') == 1 && $r['card']) {//对口令卡进行验证
				isset($_SESSION['card_username']) ? $_SESSION['card_username'] = '' : '';
				isset($_SESSION['card_password']) ? $_SESSION['card_password'] = '' : '';
				isset($_SESSION['card_password']) ? $_SESSION['card_verif'] = '' : '';
			}
			
			$this->db->update(array('lastloginip'=>ip(),'lastlogintime'=>SYS_TIME),array('userid'=>$r['userid']));
			$_SESSION['userid'] = $r['userid'];
			$_SESSION['roleid'] = $r['roleid'];
			$_SESSION['pc_hash'] = random(6,'abcdefghigklmnopqrstuvwxwyABCDEFGHIGKLMNOPQRSTUVWXWY0123456789');
			$_SESSION['lock_screen'] = 0;
			$default_siteid = self::return_siteid();
			$cookie_time = SYS_TIME+86400*30*10;
			if(!$r['lang']) $r['lang'] = 'zh-cn';
			param::set_cookie('admin_username',$username,$cookie_time);
			param::set_cookie('siteid', $default_siteid,$cookie_time);
			param::set_cookie('userid', $r['userid'],$cookie_time);
			param::set_cookie('admin_email', $r['email'],$cookie_time);
			param::set_cookie('sys_lang', $r['lang'],$cookie_time);
            header("location: ?m=admin&c=index");
			//showmessage(L('login_success'),'?m=admin&c=index');
			//同步登陆vms,先检查是否启用了vms
			$video_setting = getcache('video', 'video');
			if ($video_setting['sn'] && $video_setting['skey']) {
				$vmsapi = pc_base::load_app_class('ku6api', 'video');
				$vmsapi->member_login_vms();
			}
		} else {

                $userid = $_SESSION['userid'];

			pc_base::load_sys_class('form', '', 0);
			include $this->admin_tpl('login');
		}
	}
	
	public function public_card() {
		$username = $_SESSION['card_username'] ? $_SESSION['card_username'] :  showmessage(L('nameerror'),HTTP_REFERER);
		$r = $this->db->get_one(array('username'=>$username));
		if(!$r) showmessage(L('user_not_exist'),'?m=admin&c=index&a=login');
		if (isset($_GET['dosubmit'])) {
			pc_base::load_app_class('card', 'admin', 0);
			$result = card::verification($r['card'], $_POST['code'], $_POST['rand']);
			$_SESSION['card_verif'] = 1;
			header("location:?m=admin&c=index&a=login&dosubmit=1&card=1");
			exit;
		}
		pc_base::load_app_class('card', 'admin', 0);
		$rand = card::authe_rand($r['card']);
		include $this->admin_tpl('login_card');
	}
	
	public function public_logout() {
		$_SESSION['userid'] = 0;
		$_SESSION['roleid'] = 0;
		param::set_cookie('admin_username','');
		param::set_cookie('userid',0);
		
		//退出phpsso
		$phpsso_api_url = pc_base::load_config('system', 'phpsso_api_url');
		$phpsso_logout = '<script type="text/javascript" src="'.$phpsso_api_url.'/api.php?op=logout" reload="1"></script>';
		echo '<script type="text/javascript">alert("admin@exit");</script>';

		showmessage(L('logout_success').$phpsso_logout,'?m=admin&c=index&a=login');
	}
	
	//左侧菜单
	public function public_menu_left() {
		$menuid = intval($_GET['menuid']);
		$datas = admin::admin_menu($menuid);
		if (isset($_GET['parentid']) && $parentid = intval($_GET['parentid']) ? intval($_GET['parentid']) : 10) {
			foreach($datas as $_value) {
	        	if($parentid==$_value['id']) {
	        		echo '<li id="_M'.$_value['id'].'" class="on top_menu"><a href="javascript:_M('.$_value['id'].',\'?m='.$_value['m'].'&c='.$_value['c'].'&a='.$_value['a'].'\')" hidefocus="true" style="outline:none;">'.L($_value['name']).'</a></li>';
	        		
	        	} else {
	        		echo '<li id="_M'.$_value['id'].'" class="top_menu"><a href="javascript:_M('.$_value['id'].',\'?m='.$_value['m'].'&c='.$_value['c'].'&a='.$_value['a'].'\')"  hidefocus="true" style="outline:none;">'.L($_value['name']).'</a></li>';
	        	}      	
	        }
		} else {
			include $this->admin_tpl('left');
		}
		
	}
	//当前位置
	public function public_current_pos() {
		echo admin::current_pos($_GET['menuid']);
		exit;
	}
	
	/**
	 * 设置站点ID COOKIE
	 */
	public function public_set_siteid() {
		$siteid = isset($_GET['siteid']) && intval($_GET['siteid']) ? intval($_GET['siteid']) : exit('0'); 
		param::set_cookie('siteid', $siteid);
		exit('1');
	}
	
	public function public_ajax_add_panel() {
		$menuid = isset($_POST['menuid']) ? $_POST['menuid'] : exit('0');
		$menuarr = $this->menu_db->get_one(array('id'=>$menuid));
		$url = '?m='.$menuarr['m'].'&c='.$menuarr['c'].'&a='.$menuarr['a'].'&'.$menuarr['data'];
		$data = array('menuid'=>$menuid, 'userid'=>$_SESSION['userid'], 'name'=>$menuarr['name'], 'url'=>$url, 'datetime'=>SYS_TIME);
		$this->panel_db->insert($data, '', 1);
		$panelarr = $this->panel_db->listinfo(array('userid'=>$_SESSION['userid']), "datetime");
		foreach($panelarr as $v) {
			echo "<span><a onclick='paneladdclass(this);' target='right' href='".$v['url'].'&menuid='.$v['menuid']."&pc_hash=".$_SESSION['pc_hash']."'>".L($v['name'])."</a>  <a class='panel-delete' href='javascript:delete_panel(".$v['menuid'].");'></a></span>";
		}
		exit;
	}
	
	public function public_ajax_delete_panel() {
		$menuid = isset($_POST['menuid']) ? $_POST['menuid'] : exit('0');
		$this->panel_db->delete(array('menuid'=>$menuid, 'userid'=>$_SESSION['userid']));

		$panelarr = $this->panel_db->listinfo(array('userid'=>$_SESSION['userid']), "datetime");
		foreach($panelarr as $v) {
			echo "<span><a onclick='paneladdclass(this);' target='right' href='".$v['url']."&pc_hash=".$_SESSION['pc_hash']."'>".L($v['name'])."</a> <a class='panel-delete' href='javascript:delete_panel(".$v['menuid'].");'></a></span>";
		}
		exit;
	}
	public function public_main() {
		pc_base::load_app_func('global');
		pc_base::load_app_func('admin');
		define('PC_VERSION', pc_base::load_config('version','pc_version'));
		define('PC_RELEASE', pc_base::load_config('version','pc_release'));	
	
		$admin_username = param::get_cookie('admin_username');
		$roles = getcache('role','commons');
		$userid = $_SESSION['userid'];
		$rolename = $roles[$_SESSION['roleid']];
		$r = $this->db->get_one(array('userid'=>$userid));
		$logintime = $r['lastlogintime'];
		$loginip = $r['lastloginip'];
		$sysinfo = get_sysinfo();
		$sysinfo['mysqlv'] = $this->db->version();
		$show_header = $show_pc_hash = 1;
		/*检测框架目录可写性*/
		$pc_writeable = is_writable(PC_PATH.'base.php');
		$common_cache = getcache('common','commons');
		$logsize_warning = errorlog_size() > $common_cache['errorlog_size'] ? '1' : '0';
		$adminpanel = $this->panel_db->select(array('userid'=>$userid), '*',20 , 'datetime');
		$product_copyright = '天迈科技有限公司';
		$programmer = '马玉辉、张明雪、李天会、张国胜';
 		$designer = '张国胜';
		ob_start();
		include $this->admin_tpl('main');
		$data = ob_get_contents();
		ob_end_clean();
		system_information($data);
	}
	/**
	 * 维持 session 登陆状态
	 */
	public function public_session_life() {
		$userid = $_SESSION['userid'];
		return true;
	}
	/**
	 * 锁屏
	 */
	public function public_lock_screen() {
		$_SESSION['lock_screen'] = 1;
	}
	public function public_login_screenlock() {
		if(empty($_GET['lock_password'])) showmessage(L('password_can_not_be_empty'));
		//密码错误剩余重试次数
		$this->times_db = pc_base::load_model('times_model');
		$username = param::get_cookie('admin_username');
		$maxloginfailedtimes = getcache('common','commons');
		$maxloginfailedtimes = (int)$maxloginfailedtimes['maxloginfailedtimes'];
		
		$rtime = $this->times_db->get_one(array('username'=>$username,'isadmin'=>1));
		if($rtime['times'] > $maxloginfailedtimes-1) {
			$minute = 60-floor((SYS_TIME-$rtime['logintime'])/60);
			exit('3');
		}
		//查询帐号
		$r = $this->db->get_one(array('userid'=>$_SESSION['userid']));
		$password = md5(md5($_GET['lock_password']).$r['encrypt']);
		if($r['password'] != $password) {
			$ip = ip();
			if($rtime && $rtime['times']<$maxloginfailedtimes) {
				$times = $maxloginfailedtimes-intval($rtime['times']);
				$this->times_db->update(array('ip'=>$ip,'isadmin'=>1,'times'=>'+=1'),array('username'=>$username));
			} else {
				$this->times_db->insert(array('username'=>$username,'ip'=>$ip,'isadmin'=>1,'logintime'=>SYS_TIME,'times'=>1));
				$times = $maxloginfailedtimes;
			}
			exit('2|'.$times);//密码错误
		}
		$this->times_db->delete(array('username'=>$username));
		$_SESSION['lock_screen'] = 0;
		exit('1');
	}
	
	//后台站点地图
	public function public_map() {
		 $array = admin::admin_menu(0);
		 $menu = array();
		 foreach ($array as $k=>$v) {
		 	$menu[$v['id']] = $v;
		 	$menu[$v['id']]['childmenus'] = admin::admin_menu($v['id']);
		 }
		 $show_header = true;
		 include $this->admin_tpl('map');
	}
	
	/**
	 * 
	 * 读取盛大接扣获取appid和secretkey
	 */
	public function public_snda_status() {
		//引入盛大接口
		if(!strstr(pc_base::load_config('snda','snda_status'), '|')) {
			$this->site_db = pc_base::load_model('site_model');
			$uuid_arr = $this->site_db->get_one(array('siteid'=>1), 'uuid');
			$uuid = $uuid_arr['uuid'];
			$snda_check_url = "http://open.sdo.com/phpcms?cmsid=".$uuid."&sitedomain=".$_SERVER['SERVER_NAME'];

			$snda_res_json = @file_get_contents($snda_check_url);
			$snda_res = json_decode($snda_res_json, 1);

			if(!isset($snda_res[err]) && !empty($snda_res['appid'])) {
				$appid = $snda_res['appid'];
				$secretkey = $snda_res['secretkey'];
				set_config(array('snda_status'=>$appid.'|'.$secretkey), 'snda');
			}
		}
	}

	/**
	 * @设置网站模式 设置了模式后，后台仅出现在此模式中的菜单
	 */
	public function public_set_model() {
		$model = $_GET['site_model'];
		if (!$model) {
			param::set_cookie('site_model','');
		} else {
			$models = pc_base::load_config('model_config');
			if (in_array($model, array_keys($models))) {
				param::set_cookie('site_model', $model);
			} else {
				param::set_cookie('site_model','');
			}
		}
		$menudb = pc_base::load_model('menu_model');
		$where = array('parentid'=>0,'display'=>1);
		if ($model) {
			$where[$model] = 1;
 		}
		$result =$menudb->select($where,'id',1000,'listorder ASC');
		$menuids = array();
		if (is_array($result)) {
			foreach ($result as $r) {
				$menuids[] = $r['id'];
			}
		}
		exit(json_encode($menuids));
	}

}
?>