<?php

/*
*定时导出任务
*/
header('Content-Type: text/html; charset=utf-8'); 
require_once 'D:/wamp/www2/dingshi/global.func.php';
require_once 'D:/wamp/www2/dingshi/oracle_admin.class.php';

//require_once './dingshi/global.func.php';
//require_once './dingshi/oracle_admin.class.php';

set_time_limit(0);
ini_set('memory_limit', '-1');
ini_set("max_execution_time", "0");
$time = get_monthDs();



//$start = date('Y-m-d',$time['start_time']);
//$end = date('Y-m-d',$time['end_time_next']); 
//$timetype = jiyuefen(); //类似一月份，二月份
$start = '2018-01-01';
$end = '2018-10-01';
$timetype = '九月份'; //类似一月份，二月份

		 
		 
		 
		 

$yewugongsi = 2;//教材公司
$admin_username = '010001';

$host='172.30.153.63';
$ip='172.30.153.63/xhsddb';
$port='1521';
$user= 'dbjczc';
$pass= 'dbjczc';
$charset='utf8';
$ora=new oracle_admin($user,$pass,$ip,$charset);


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
						$res = $ora->query($sql_gc);
						$sql_gc = "Insert Into T_MONTH_HZB_NIAN_TMP_ZDQH
        (lx, bh, jfmy, jfsy, jfcb, bb, YWBMBH, ZDQH)
        Select 'DH', GHDWH, 0, Sum(Sys), Sum(sys), 'MF', YWBMBH, SUBSTR(ZDQH, 1, 4)
          From T_MONTH_DHJSB_mf
         Where SHRQ >= Date '$start'
           And SHRQ < Date '$end'
         Group By YWBMBH, GHDWH, SUBSTR(ZDQH, 1, 4)";
						$ora->query($sql_gc);
						$sql_gc = "Insert Into T_MONTH_HZB_NIAN_TMP_ZDQH
        (lx, bh, jfmy, jfsy, jfcb, bb, YWBMBH, ZDQH)
        Select 'DH', GHDWH, Sum(Zmy), Sum(ZSY), Sum(ZSY), 'JT', YWBMBH, SUBSTR(ZDQH, 1, 4)
          From T_MONTH_JTJSB
         Where DBRQ >= Date '$start'
           And DBRQ < Date '$end'
         Group By GHDWH, YWBMBH, SUBSTR(ZDQH, 1, 4)";
						$ora->query($sql_gc);
						$sql_gc = "Insert Into T_MONTH_HZB_NIAN_TMP_ZDQH
        (lx, bh, jSmy, jSsy, jscb, bb, YWBMBH, ZDQH)
        Select 'DH', GHDWH, Sum(my), Sum(Sys), Sum(Sys), 'DH', YWBMBH, SUBSTR(ZDQH, 1, 4)
          From T_MONTH_DHJSB
         Where JSRQ >= Date '$start'
           And JSRQ < Date '$end'
         Group By GHDWH, YWBMBH, SUBSTR(ZDQH, 1, 4)";
						$ora->query($sql_gc);
						$sql_gc = "Insert Into T_MONTH_HZB_NIAN_TMP_ZDQH
        (lx, bh, jSmy, jSsy, jscb, bb, YWBMBH, ZDQH)
        Select 'DH', GHDWH, 0, Sum(Sys), Sum(Sys), 'MF', YWBMBH, SUBSTR(ZDQH, 1, 4)
          From t_month_dhjsb_mf
         Where JSRQ >= Date '$start'
           And JSRQ < Date '$end'
         Group By GHDWH, YWBMBH, SUBSTR(ZDQH, 1, 4)";
						$ora->query($sql_gc);
						$sql_gc = "Insert Into T_MONTH_HZB_NIAN_TMP_ZDQH
        (lx, bh, jsmy, jssy, jscb, bb, YWBMBH, ZDQH)
        Select 'DH', GHDWH, Sum(Zmy), Sum(ZSY), Sum(zsy), 'JT', YWBMBH, SUBSTR(ZDQH, 1, 4)
          From T_MONTH_JTJSB
         Where JSRQ >= Date '$start'
           And JSRQ < Date '$end'
         Group By GHDWH, YWBMBH, SUBSTR(ZDQH, 1, 4)";
						$ora->query($sql_gc);
						$sql_gc = "Insert Into T_MONTH_HZB_NIAN_TMP_ZDQH
        (lx, YWBMBH, bh, qcwjmy, qcwjsy, qcwjcb, bb, ZDQH)
        Select 'DH', YWBMBH, GHDWH, Sum(my), Sum(Sys), Sum(Sys), 'DH', SUBSTR(ZDQH, 1, 4)
          From T_MONTH_DHJSB
         Where shrq < Date '$start'
           And (jsrq Is Null Or jsrq >= Date '$start')
         Group By YWBMBH, GHDWH, SUBSTR(ZDQH, 1, 4)";
						$ora->query($sql_gc);
						
						$sql_gc = "Insert Into T_MONTH_HZB_NIAN_TMP_ZDQH
        (lx, YWBMBH, bh, qcwjmy, qcwjsy, qcwjcb, bb, ZDQH)
        Select 'DH', YWBMBH, GHDWH, Sum(Zmy), Sum(ZSY), Sum(ZSY), 'JT', SUBSTR(ZDQH, 1, 4)
          From T_MONTH_JTJSB
         Where DBrq < Date '$start'
           And (jsrq Is Null Or jsrq >= Date '$start')
         Group By YWBMBH, GHDWH, SUBSTR(ZDQH, 1, 4)";
						$ora->query($sql_gc);

						$sql_gc = "Insert Into T_MONTH_HZB_NIAN_TMP_ZDQH
        (lx, YWBMBH, bh, qcwjmy, qcwjsy, qcwjcb, bb, ZDQH)
        Select 'DH', YWBMBH, GHDWH, 0, Sum(Sys), Sum(Sys), 'MF', SUBSTR(ZDQH, 1, 4)
          From t_month_dhjsb_mf
         Where shrq < Date '$start'
           And (JSRQ Is Null Or jsrq >= Date '$start')
         Group By YWBMBH, GHDWH, SUBSTR(ZDQH, 1, 4)";
						$ora->query($sql_gc);
						$sql_gc = "Insert Into T_MONTH_HZB_NIAN_TMP_ZDQH
        (lx, YWBMBH, bh, qmwjmy, qmwjsy, qmwjcb, bb, ZDQH)
        Select 'DH', YWBMBH, GHDWH, Sum(my), Sum(Sys), Sum(Sys), 'DH', SUBSTR(ZDQH, 1, 4)
          From T_MONTH_DHJSB
         Where shrq < Date '$end'
           And (jsrq Is Null Or jsrq >= Date '$end')
         Group By YWBMBH, GHDWH, SUBSTR(ZDQH, 1, 4)";
						$ora->query($sql_gc);
						
						$sql_gc = "Insert Into T_MONTH_HZB_NIAN_TMP_ZDQH
        (lx, YWBMBH, bh, qmwjmy, qmwjsy, qmwjcb, bb, ZDQH)
        Select 'DH', YWBMBH, GHDWH, 0, Sum(Sys), Sum(Sys), 'MF', SUBSTR(ZDQH, 1, 4)
          From t_month_dhjsb_mf
         Where shrq < Date '$end'
           And (JSRQ Is Null Or jsrq >= Date '$end')
         Group By YWBMBH, GHDWH, SUBSTR(ZDQH, 1, 4)";
						$ora->query($sql_gc);
						
						$sql_gc = "Insert Into T_MONTH_HZB_NIAN_TMP_ZDQH
        (lx, YWBMBH, bh, qmwjmy, qmwjsy, qmwjcb, bb, ZDQH)
        Select 'DH', YWBMBH, GHDWH, Sum(Zmy), Sum(ZSY), Sum(ZSY), 'JT', SUBSTR(ZDQH, 1, 4)
          From T_MONTH_JTJSB
         Where jsrq Is Null
            Or jsrq >= Date '$end'
         Group By YWBMBH, GHDWH, SUBSTR(ZDQH, 1, 4)";
						$ora->query($sql_gc);
						$sql_gc = "Insert Into T_MONTH_HZB_NIAN_TMP_ZDQH
        (lx, bh, jfmy, jfsy, jfcb, bb, YWBMBH, ZDQH)
        Select 'FH', DH, Sum(Zmy), Sum(ZSY), Sum(cbj), 'FH', YWBMBH, SUBSTR(ZDQH, 1, 4)
          From T_MONTH_FHJSB
         Where DBRQ >= Date '$start'
           And DBRQ < Date '$end'
         Group By DH, YWBMBH, SUBSTR(ZDQH, 1, 4)";
						$ora->query($sql_gc);
						$sql_gc = "Insert Into T_MONTH_HZB_NIAN_TMP_ZDQH
        (lx, bh, jfmy, jfsy, jfcb, bb, YWBMBH, ZDQH)
        Select 'FH', dH, Sum(Zmy), Sum(ZSY), Sum(zcb), 'XT', YWBMBH, SUBSTR(ZDQH, 1, 4)
          From T_MONTH_xTJSB
         Where THRQ >= Date '$start'
           And THRQ < Date '$end'
         Group By dh, YWBMBH, SUBSTR(ZDQH, 1, 4)";
						$ora->query($sql_gc);
						$sql_gc = "Insert Into T_MONTH_HZB_NIAN_TMP_ZDQH
        (lx, bh, jfmy, jfsy, jfcb, bb, YWBMBH, ZDQH)
        Select 'FH', dH, Sum(Zmy), Sum(ZSY), Sum(zcb), 'GZ', YWBMBH, SUBSTR(ZDQH, 1, 4)
          From T_MONTH_fHgzJSB
         Where gzRQ >= Date '$start'
           And gzRQ < Date '$end'
         Group By dh, YWBMBH, SUBSTR(ZDQH, 1, 4)";
						$ora->query($sql_gc);
						$sql_gc = "Insert Into T_MONTH_HZB_NIAN_TMP_ZDQH
        (lx, bh, jsmy, jssy, jscb, bb, YWBMBH, ZDQH)
        Select 'FH', DH, Sum(Zmy), Sum(ZSY), Sum(cbj), 'FH', YWBMBH, SUBSTR(ZDQH, 1, 4)
          From T_MONTH_FHJSB
         Where jsRQ >= Date '$start'
           And jsRQ < Date '$end'
         Group By DH, YWBMBH, SUBSTR(ZDQH, 1, 4)";
						$ora->query($sql_gc);
						$sql_gc = "Insert Into T_MONTH_HZB_NIAN_TMP_ZDQH
        (lx, bh, jsmy, jssy, jscb, bb, YWBMBH, ZDQH)
        Select 'FH', dH, Sum(Zmy), Sum(ZSY), Sum(zcb), 'XT', YWBMBH, SUBSTR(ZDQH, 1, 4)
          From T_MONTH_xTJSB
         Where jsRQ >= Date '$start'
           And jsRQ < Date '$end'
         Group By dh, YWBMBH, SUBSTR(ZDQH, 1, 4)";
						$ora->query($sql_gc);
						
						
						$sql_gc = "Insert Into T_MONTH_HZB_NIAN_TMP_ZDQH
        (lx, bh, jsmy, jssy, jscb, bb, YWBMBH, ZDQH)
        Select 'FH', dH, Sum(Zmy), Sum(ZSY), Sum(zcb), 'GZ', YWBMBH, SUBSTR(ZDQH, 1, 4)
          From T_MONTH_fhgzJSB
         Where jsRQ >= Date '$start'
           And jsRQ < Date '$end'
         Group By dh, YWBMBH, SUBSTR(ZDQH, 1, 4)";
						$ora->query($sql_gc);
						
						$sql_gc = "Insert Into T_MONTH_HZB_NIAN_TMP_ZDQH
        (lx, bh, qcwjmy, qcwjsy, qcwjcb, bb, YWBMBH, ZDQH)
        Select 'FH', dH, Sum(Zmy), Sum(ZSY), Sum(cbj), 'FH', YWBMBH, SUBSTR(ZDQH, 1, 4)
          From T_MONTH_fHJSB
         Where dbrq < Date '$start'
           And (tslsh Is Null Or jsRQ >= Date '$start')
         Group By dh, YWBMBH, SUBSTR(ZDQH, 1, 4)";
						$ora->query($sql_gc);
						$sql_gc = "Insert Into T_MONTH_HZB_NIAN_TMP_ZDQH
        (lx, bh, qcwjmy, qcwjsy, qcwjcb, bb, YWBMBH, ZDQH)
        Select 'FH', dH, Sum(Zmy), Sum(ZSY), Sum(zcb), 'XT', YWBMBH, SUBSTR(ZDQH, 1, 4)
          From T_MONTH_xTJSB
         Where thrq < Date '$start'
           And (tslsh Is Null Or jsRQ >= Date '$start')
         Group By dh, YWBMBH, SUBSTR(ZDQH, 1, 4)";
						$ora->query($sql_gc);
						$sql_gc = "Insert Into T_MONTH_HZB_NIAN_TMP_ZDQH
        (lx, bh, qcwjmy, qcwjsy, qcwjcb, bb, YWBMBH, ZDQH)
        Select 'FH', DH, Sum(Zmy), Sum(ZSY), Sum(zcb), 'GZ', YWBMBH, SUBSTR(ZDQH, 1, 4)
          From T_MONTH_FHGZJSB
         Where gzrq < Date '$start'
           And (tslsh Is Null Or jsRQ >= Date '$start')
         Group By DH, YWBMBH, SUBSTR(ZDQH, 1, 4)";
						$ora->query($sql_gc);
						$sql_gc = "Insert Into T_MONTH_HZB_NIAN_TMP_ZDQH
        (lx, bh, qmwjmy, qmwjsy, qmwjcb, bb, YWBMBH, ZDQH)
        Select 'FH', dH, Sum(Zmy), Sum(ZSY), Sum(cbj), 'FH', YWBMBH, SUBSTR(ZDQH, 1, 4)
          From T_MONTH_fHJSB
         Where tslsh Is Null
            Or jsRQ >= Date '$end'
         Group By dh, YWBMBH, SUBSTR(ZDQH, 1, 4)";
						$ora->query($sql_gc);
						$sql_gc = "Insert Into T_MONTH_HZB_NIAN_TMP_ZDQH
        (lx, bh, qmwjmy, qmwjsy, qmwjcb, bb, YWBMBH, ZDQH)
        Select 'FH', dH, Sum(Zmy), Sum(ZSY), Sum(zcb), 'XT', YWBMBH, SUBSTR(ZDQH, 1, 4)
          From T_MONTH_xTJSB
         Where tslsh Is Null
            Or jsRQ >= Date '$end'
         Group By dh, YWBMBH, SUBSTR(ZDQH, 1, 4)";
						$ora->query($sql_gc);
						$sql_gc = "Insert Into T_MONTH_HZB_NIAN_TMP_ZDQH
        (lx, bh, qmwjmy, qmwjsy, qmwjcb, bb, YWBMBH, ZDQH)
        Select 'FH', DH, Sum(Zmy), Sum(ZSY), Sum(zcb), 'GZ', YWBMBH, SUBSTR(ZDQH, 1, 4)
          From T_MONTH_FHGZJSB
         Where tslsh Is Null
            Or jsRQ >= Date '$end'
         Group By DH, YWBMBH, SUBSTR(ZDQH, 1, 4)";
						$ora->query($sql_gc);
						$sql_gc = "Insert Into T_MONTH_HZB_NIAN_ZDQH
        (LX, BH, YWBMBH, qcwjmy, qcwjsy, JFMY, JFSY, JSMY, JSSY, QMWJMY, QMWJSY, ZDQH)
        Select lx, bh, ywbmbh, Sum(nvl(qcwjmy, 0)), Sum(nvl(qcwjsy, 0)), Sum(nvl(jfmy, 0)),
               Sum(nvl(jfsy, 0)), Sum(nvl(jsmy, 0)), Sum(nvl(jssy, 0)),
               Sum(nvl(qmwjmy, 0)), Sum(nvl(qmwjsy, 0)), ZDQH
          From T_MONTH_HZB_NIAN_TMP_ZDQH
         Group By ywbmbh, lx, bh, ZDQH";
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
						$ora->query($sql_gc);
						$sql_gc = "Update T_MONTH_HZB_NIAN_ZDQH T
       Set DMMC = (Select DM From T_DM@zczm_jc Where DH = T.BH)
     Where LX = 'FH'";
						$ora->query($sql_gc);
						
						$sql_gc = "Update T_MONTH_HZB_NIAN_ZDQH T
       Set DMMC = (Select MC From T_GHDW@zczm_jc Where BH = T.BH)
     Where LX = 'DH'";
						$ora->query($sql_gc);
						$sql_gc = "Update T_MONTH_HZB_NIAN T
       Set DMMC = (Select DM From T_DM@zczm_jc Where DH = T.BH)
     Where LX = 'FH'";
						$ora->query($sql_gc);
						$sql_gc = "Update T_MONTH_HZB_NIAN T
       Set DMMC = (Select MC From T_GHDW@zczm_jc Where BH = T.BH)
     Where LX = 'DH'";
						$ora->query($sql_gc);

						
//1
$fileName = '教材月报';
$lujing = jcOptionDs($timetype,$yewugongsi); // 获取到文件存放地址，以及文件名拼接需要的时间类型（月份，季度，年度）
$path = __DIR__.'/uploadfile/'.$lujing['path'];
if(!file_exists($path)) {
	mkdir( iconv('UTF-8','GBK',$path), 0777, true );
}
$filepath = $path.$admin_username.'-'.iconv("UTF-8","gbk",$lujing['file_path']).'-'.iconv("UTF-8","gbk",$fileName).'.csv';  //$timetype 为一月份  一季度  1-6
if(!file_exists($filepath)){ 
	$sql="Select * From t_Month_Hzb_Nian Where ywbmbh='$admin_username'";
	$row=$ora->query($sql);
	$total = count($row);
	$title = array('YWBMBH','LX','BH','DMMC','QCWJMY','QCWJSY','QCWJCB','JFMY','JFSY','JFCB','JSMY','JSSY','JSCB','QMWJMY','QMWJSY','QMWJCB','SYLX','BB','MINDATE','MAXDATE','NY','BJSMY','BJSSY');
	yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username,$timetype);
}
//2
$fileName = '教材到货明细';
$lujing = jcOptionDs($timetype,$yewugongsi); // 获取到文件存放地址，以及文件名拼接需要的时间类型（月份，季度，年度）
$path = __DIR__.'/uploadfile/'.$lujing['path'];
if(!file_exists($path)) {
	mkdir( iconv('UTF-8','GBK',$path), 0777, true );
}
$filepath = $path.$admin_username.'-'.iconv("UTF-8","gbk",$lujing['file_path']).'-'.iconv("UTF-8","gbk",$fileName).'.csv';  //$timetype 为一月份  一季度  1-6
if(!file_exists($filepath)){ 
$sql="Select YWBMBH,GHDWH,MC,DHPCH,YSDJ,SSDH,ZDQH,ZDXH,SM,DJ,JXSSSL,MY,SYS,ZDPCH,JSRQ,JSLX,ZDPCH1,TSLSH,YSDBRQ,JCDRQ,KQBJ,JSGHDWH,YSGHDWH,FLOWID_DHMX,to_char(t_month_dhjsb.SHRQ, 'YYYY-MM-DD HH24:MI:SS') as SHRQ ,to_char(t_month_dhjsb.YSRQ, 'YYYY-MM-DD HH24:MI:SS') as YSRQ From t_month_dhjsb Where ywbmbh='$admin_username' And shrq>=Date'$start' and shrq < Date'$end'";
$row=$ora->query($sql);
$total = count($row);
$title = array('YWBMBH','GHDWH','MC','DHPCH','SHRQ','YSDJ','SSDH','ZDQH','ZDXH','SM','DJ','JXSSSL','MY','SYS','ZDPCH','JSRQ','JSLX','ZDPCH1','TSLSH','YSRQ','YSDBRQ','JCDRQ','KQBJ','JSGHDWH','YSGHDWH','FLOWID_DHMX');
yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username,$timetype);
}
//3
$fileName = '教材进退明细';
$lujing = jcOptionDs($timetype,$yewugongsi); // 获取到文件存放地址，以及文件名拼接需要的时间类型（月份，季度，年度）
$path = __DIR__.'/uploadfile/'.$lujing['path'];
if(!file_exists($path)) {
	mkdir( iconv('UTF-8','GBK',$path), 0777, true );
}
$filepath = $path.$admin_username.'-'.iconv("UTF-8","gbk",$lujing['file_path']).'-'.iconv("UTF-8","gbk",$fileName).'.csv';  //$timetype 为一月份  一季度  1-6
if(!file_exists($filepath)){ 
$sql="Select YWBMBH,GHDWH,MC,ID,SM,DJ,ZMY,ZSY,BZ,YWPCH,ZDPCH,JSRQ,JSLX,ZDQH,ZDXH,ZDPCH1,TSLSH,NF,NFHRQ,NJSRQ,JSGHDWH,YSGHDWH,ZCS,to_char(t_month_jtjsb.DBRQ, 'YYYY-MM-DD HH24:MI:SS') as DBRQ
 From t_month_jtjsb Where ywbmbh='$admin_username' And dbrq>=Date'$start' and dbrq < Date'$end'";
$row=$ora->query($sql);
$total = count($row);
$title = array('YWBMBH','GHDWH','MC','DBRQ','ID','SM','DJ','ZMY','ZSY','BZ','YWPCH','ZDPCH','JSRQ','JSLX','ZDQH','ZDXH','ZDPCH1','TSLSH','NF','NFHRQ','NJSRQ','JSGHDWH','YSGHDWH','ZCS');
yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username,$timetype);
}


//4
$fileName = '教材出版社免费让利明细';
$lujing = jcOptionDs($timetype,$yewugongsi); // 获取到文件存放地址，以及文件名拼接需要的时间类型（月份，季度，年度）
$path = __DIR__.'/uploadfile/'.$lujing['path'];
if(!file_exists($path)) {
	mkdir( iconv('UTF-8','GBK',$path), 0777, true );
}
$filepath = $path.$admin_username.'-'.iconv("UTF-8","gbk",$lujing['file_path']).'-'.iconv("UTF-8","gbk",$fileName).'.csv';  //$timetype 为一月份  一季度  1-6
if(!file_exists($filepath)){ 
$sql="Select YWBMBH,GHDWH,MC,DHPCH,SHRQ,YSDJ,SSDH,ZDQH,ZDXH,SM,DJ,JXSSSL,MY,SYS,ZDPCH,JSRQ,JSLX,JSGHDWH,YSGHDWH,TSLSH
 From t_month_dhjsb_mf Where ywbmbh='$admin_username' And shrq>=Date'$start' and shrq < Date'$end'";
$row=$ora->query($sql);
$total = count($row);
$title = array('YWBMBH','GHDWH','MC','DHPCH','SHRQ','YSDJ','SSDH','ZDQH','ZDXH','SM','DJ','JXSSSL','MY','SYS','ZDPCH','JSRQ','JSLX','JSGHDWH','YSGHDWH','TSLSH');
yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username,$timetype);
}
//5
$fileName = '教材销退明细';
$lujing = jcOptionDs($timetype,$yewugongsi); // 获取到文件存放地址，以及文件名拼接需要的时间类型（月份，季度，年度）
$path = __DIR__.'/uploadfile/'.$lujing['path'];
if(!file_exists($path)) {
	mkdir( iconv('UTF-8','GBK',$path), 0777, true );
}
$filepath = $path.$admin_username.'-'.iconv("UTF-8","gbk",$lujing['file_path']).'-'.iconv("UTF-8","gbk",$fileName).'.csv';  //$timetype 为一月份  一季度  1-6
if(!file_exists($filepath)){ 
//$sql="Select ghdw,ghdwmc,YWBMBH,KFBH,DH,DM,PCH,ZMY,ZSY,ZCB,TSLSH,JSLX,ZDQH,NF,NFHRQ,NJSRQ,JB,CWQRPC,ZCS,DJ,ID,BB,SM,ISBN,CBNY,to_char(t_month_xtjsb.THRQ, 'YYYY-MM-DD HH24:MI:SS') as THRQ From t_month_xtjsb Where ywbmbh='$admin_username' And thrq>=Date'$start' and thrq < Date'$end'";
$sql="Select ghdw,ghdwmc,YWBMBH,KFBH,DH,DM,PCH,ZMY,ZSY,ZCB,TSLSH,JSLX,ZDQH,NF,NFHRQ,NJSRQ,JB,CWQRPC,ZCS,DJ,ID,BB,SM,ISBN,CBNY,to_char(t_month_xtjsb.THRQ, 'YYYY-MM-DD HH24:MI:SS') as THRQ,to_char(t_month_xtjsb.JSRQ, 'YYYY-MM-DD HH24:MI:SS') as JSRQ From t_month_xtjsb Where ywbmbh='$admin_username' And thrq>=Date'$start' and thrq < Date'$end'";

//echo $sql;exit;
$row=$ora->query($sql);
$total = count($row);
$title = array('GHDW','GHDWMC','YWBMBH','KFBH','DH','DM','PCH','THRQ','ZMY','ZSY','ZCB','TSLSH','JSRQ','JSLX','ZDQH','NF','NFHRQ','NJSRQ','JB','CWQRPC','ZCS','DJ','ID','BB','SM','ISBN','CBNY');
yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username,$timetype);
}

//6
$fileName = '教材基层店免费让利明细';
$lujing = jcOptionDs($timetype,$yewugongsi); // 获取到文件存放地址，以及文件名拼接需要的时间类型（月份，季度，年度）
$path = __DIR__.'/uploadfile/'.$lujing['path'];
if(!file_exists($path)) {
	mkdir( iconv('UTF-8','GBK',$path), 0777, true );
}
$filepath = $path.$admin_username.'-'.iconv("UTF-8","gbk",$lujing['file_path']).'-'.iconv("UTF-8","gbk",$fileName).'.csv';  //$timetype 为一月份  一季度  1-6
if(!file_exists($filepath)){ 
$sql="Select * From t_month_fhgzjsb Where ywbmbh='$admin_username' And gzrq>=Date'$start' and gzrq < Date'$end'";
$row=$ora->query($sql);
$total = count($row);
$title = array('YWBMBH','KFBH','DH','DM','GZPCH','GZRQ','ZMY','ZSY','ZCB','TSLSH','JSRQ','BZ','JSLX','ZDQH','NF','NFHRQ','NJSRQ','JB','ZCS');
yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username,$timetype);
}
//7
$fileName = '教材发货明细';
$lujing = jcOptionDs($timetype,$yewugongsi); // 获取到文件存放地址，以及文件名拼接需要的时间类型（月份，季度，年度）
$path = __DIR__.'/uploadfile/'.$lujing['path'];
if(!file_exists($path)) {
	mkdir( iconv('UTF-8','GBK',$path), 0777, true );
}
$filepath = $path.$admin_username.'-'.iconv("UTF-8","gbk",$lujing['file_path']).'-'.iconv("UTF-8","gbk",$fileName).'.csv';  //$timetype 为一月份  一季度  1-6
if(!file_exists($filepath)){ 
$sql="Select cbny,ghdw,ghdwmc,YWBMBH,DH,MC,PFPCH,BZ,ZMY,ZSY,CBJ,TSLSH,JSRQ,SM,DJ,ZDQH,ZDXH,KFBH,JSLX,NF,NFHRQ,NJSRQ,JB,CWQRPC,YSDBRQ,ZCS,to_char(t_month_fhjsb.DBRQ, 'YYYY-MM-DD HH24:MI:SS') as DBRQ
,to_char(t_month_fhjsb.PFRQ, 'YYYY-MM-DD HH24:MI:SS') as PFRQ From t_month_fhjsb Where ywbmbh='$admin_username' And dbrq>=Date'$start' and dbrq < Date'$end'";
$row=$ora->query($sql);
$total = count($row);
$title = array('CBNY','GHDW','GHDWMC','YWBMBH','DH','MC','PFPCH','BZ','DBRQ','PFRQ','ZMY','ZSY','CBJ','TSLSH','JSRQ','SM','DJ','ZDQH','ZDXH','KFBH','JSLX','NF','NFHRQ','NJSRQ','JB','CWQRPC','YSDBRQ','ZCS');
yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username,$timetype);
}
//8
$fileName = '教材库存明细';
$lujing = jcOptionDs($timetype,$yewugongsi); // 获取到文件存放地址，以及文件名拼接需要的时间类型（月份，季度，年度）
$path = __DIR__.'/uploadfile/'.$lujing['path'];
if(!file_exists($path)) {
	mkdir( iconv('UTF-8','GBK',$path), 0777, true );
}
$filepath = $path.$admin_username.'-'.iconv("UTF-8","gbk",$lujing['file_path']).'-'.iconv("UTF-8","gbk",$fileName).'.csv';  //$timetype 为一月份  一季度  1-6
if(!file_exists($filepath)){ 
$dangyue = dy_month(); //当月第一天
$start_kc = date('Y-m-d',$dangyue['start_time']);
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
   From t_Hd_Kcsl_Bf@zczm_jc t Where kccs<>0 And ywbmbh='$admin_username' And bfsj>=Date'$start_kc'";
$row=$ora->query($sql);
$total = count($row);
$title = array('YWBMBH','ID','SM','DJ','版别','KCCS','MY','SYS','KF','WL','分类','HW','供货单位','YWY','ISBN','版次','编者','年');
yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username,$timetype);
}


$fileName = '教材库存明细提前出';
$lujing = jcOptionDs($timetype,$yewugongsi); // 获取到文件存放地址，以及文件名拼接需要的时间类型（月份，季度，年度）
$path = __DIR__.'/uploadfile/'.$lujing['path'];
if(!file_exists($path)) {
	mkdir( iconv('UTF-8','GBK',$path), 0777, true );
}
$filepath = $path.$admin_username.'-'.iconv("UTF-8","gbk",$lujing['file_path']).'-'.iconv("UTF-8","gbk",$fileName).'.csv';  //$timetype 为一月份  一季度  1-6
if(!file_exists($filepath)){ 
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
$row=$ora->query($sql);
$total = count($row);
$title = array('YWBMBH','ID','SM','DJ','版别','KCCS','MY','SYS','KF','WL','分类','HW','供货单位','YWY','ISBN','版次','编者','年');
yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username,$timetype);
}


//9
$fileName = '教材配发中间态';
$lujing = jcOptionDs($timetype,$yewugongsi); // 获取到文件存放地址，以及文件名拼接需要的时间类型（月份，季度，年度）
$path = __DIR__.'/uploadfile/'.$lujing['path'];
if(!file_exists($path)) {
	mkdir( iconv('UTF-8','GBK',$path), 0777, true );
}
$filepath = $path.$admin_username.'-'.iconv("UTF-8","gbk",$lujing['file_path']).'-'.iconv("UTF-8","gbk",$fileName).'.csv';  //$timetype 为一月份  一季度  1-6
if(!file_exists($filepath)){ 
$dangyue = dy_month(); //当月第一天
$start_pf = date('Y-m-d',$dangyue['start_time']);
$sql="Select Ywbmbh ,(select dm from t_dm@zczm_jc where dh=a.ywbmbh) as ywbmmc,dh,(select dm from t_dm@zczm_jc where dh=a.dh) as dm,
a.id,(select isbn from t_kcsm@zczm_jc where a.id=id) as isbn,(select sm from t_kcsm@zczm_jc where a.id=id) as sm,
(select dj from t_kcsm@zczm_jc where a.id=id) as dj,(Select round(cbny) From t_kcsm@zczm_jc Where Id=a.id)As cbny,a.pxcs,(select dj*a.pxcs from t_kcsm@zczm_jc where a.id=id) as zmy ,a.sys,a.cbj,
a.pxdh,a.bz,a.zdh,a.sylx,a.sl,a.zjxs,a.qx,a.djbh,a.kfbh,A.Bjlsh,a.cybh,A.Flowid,a.zdqh,a.zdxh,a.zdqh1,a.zdxh1,
to_char(a.pfrq, 'YYYY-MM-DD HH24:MI:SS') as pfrq From t_hd_pfmx@zczm_jc a Where bfsj>=Date'$start_pf' and ckbj='1' and ywbmbh='$admin_username'";
$row=$ora->query($sql);
$total = count($row);
$title = array('YWBMBH','YWBMMC','DH','DM','ID','ISBN','SM','DJ','CBNY','PXCS','ZMY','SYS','CBJ','PXDH','BZ','ZDH','SYLX','SL','ZJXS','QX','DJBH','KFBH','BJLSH','CYBH','FLOWID','ZDQH','ZDXH','ZDQH1','ZDXH1','PFRQ');
yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username,$timetype);
}
//10
$fileName = '教材库房转移中间态明细';
$lujing = jcOptionDs($timetype,$yewugongsi); // 获取到文件存放地址，以及文件名拼接需要的时间类型（月份，季度，年度）
$path = __DIR__.'/uploadfile/'.$lujing['path'];
if(!file_exists($path)) {
	mkdir( iconv('UTF-8','GBK',$path), 0777, true );
}
$filepath = $path.$admin_username.'-'.iconv("UTF-8","gbk",$lujing['file_path']).'-'.iconv("UTF-8","gbk",$fileName).'.csv';  //$timetype 为一月份  一季度  1-6
if(!file_exists($filepath)){ 
$sql="Select Id ,(Select sm From t_kcsm@zczm_jc Where Id=A.id)As sm,(Select isbn From t_kcsm@zczm_jc Where Id=A.id)As  isbn,
(Select dj From t_kcsm@zczm_jc Where Id=A.id)As dj,(Select min(mc) From t_bb@zczm_jc Where bh=(Select bb From t_kcsm@zczm_jc Where Id=A.id))As bb,
		  (Select mc From t_ghdw@zczm_jc Where bh=(Select min(ghdwh) From t_dhls_jx@zczm_jc Where Id=A.id))As ghdw,(Select Name From t_user@zczm_jc Where usr_id=(Select ywryh From t_kcsm@zczm_jc Where Id=A.id))As ywy,cbny,ywlx,Sum(cs),Sum(cs*(Select dj From t_kcsm@zczm_jc Where Id=a.id))As MY,Sum(sy)As SY From (
Select YWLX,Id,(Select round(cbny) From t_kcsm@zczm_jc Where Id=t_crkls.id)As cbny,
Sum(CRKCS)CS,sum(crksy)sy From t_crkls@zczm_jc Where ywbmbh='$admin_username' And (YWLX='YC' Or YWLX='DC' )And crkrq>=Date'2018-07-01' And crkrq < Date'$end' Group By ywlx,Id
Union All Select YWLX,Id,(Select round(cbny) From t_kcsm@zczm_jc Where Id=t_crkls.id)As cbny,Sum(-CRKCS)CS,sum(-crksy)sy From t_crkls@zczm_jc Where ywbmbh='$admin_username' And ( YWLX='YR'Or YWLX='DR' )
And crkrq >= Date'2018-07-01' And crkrq < Date'$end' Group By ywlx,Id) A Group By Id,ywlx,cbny Having Sum(cs)<>0						
";
$row=$ora->query($sql);
$total = count($row);
$title = array('ID','SM','ISBN','DJ','版别','供货单位','YWY','CBNY','YWLX','SUM(CS)','MY','SY');
yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username,$timetype);
}
//11
$fileName = '教材损益明细';
$lujing = jcOptionDs($timetype,$yewugongsi); // 获取到文件存放地址，以及文件名拼接需要的时间类型（月份，季度，年度）
$path = __DIR__.'/uploadfile/'.$lujing['path'];
if(!file_exists($path)) {
	mkdir( iconv('UTF-8','GBK',$path), 0777, true );
}
$filepath = $path.$admin_username.'-'.iconv("UTF-8","gbk",$lujing['file_path']).'-'.iconv("UTF-8","gbk",$fileName).'.csv';  //$timetype 为一月份  一季度  1-6
if(!file_exists($filepath)){ 
$sql="Select KFBH,(Select SM From T_KCSM@zczm_jc Where Id=T.ID) As SM,CRKCS,(Select dj From t_kcsm@zczm_jc Where T.id=Id)as dj,(crkcs*(Select dj From t_kcsm@zczm_jc Where T.id=Id)) zmy,crksy,crkrq
From t_crkls@zczm_jc T Where  crkrq>=Date'$start' And crkrq < Date'$end'  And ywbmbh='$admin_username' And ywlx='SY'	";
$row=$ora->query($sql);
$total = count($row);
$title = array('KFBH','SM','CRKCS','DJ','ZMY','CRKSY','CRKRQ');
yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username,$timetype);
}
//12
$fileName = '教材当月已结算-到货已结-到货';
$lujing = jcOptionDs($timetype,$yewugongsi); // 获取到文件存放地址，以及文件名拼接需要的时间类型（月份，季度，年度）
$path = __DIR__.'/uploadfile/'.$lujing['path'];
if(!file_exists($path)) {
	mkdir( iconv('UTF-8','GBK',$path), 0777, true );
}
$filepath = $path.$admin_username.'-'.iconv("UTF-8","gbk",$lujing['file_path']).'-'.iconv("UTF-8","gbk",$fileName).'.csv';  //$timetype 为一月份  一季度  1-6
if(!file_exists($filepath)){ 
$sql="Select YWBMBH,GHDWH,MC,DHPCH,YSDJ,SSDH,ZDQH,ZDXH,SM,DJ,JXSSSL,MY,SYS,ZDPCH,JSRQ,JSLX,ZDPCH1,TSLSH,YSDBRQ,JCDRQ,KQBJ,JSGHDWH,YSGHDWH,FLOWID_DHMX,to_char(t_Month_Dhjsb.SHRQ, 'YYYY-MM-DD HH24:MI:SS') as SHRQ,to_char(t_Month_Dhjsb.YSRQ, 'YYYY-MM-DD HH24:MI:SS') as YSRQ From t_Month_Dhjsb Where jsrq>=Date'$start' And jsrq < Date'$end' And ywbmbh='$admin_username'";
$row=$ora->query($sql);
$total = count($row);
$title = array('YWBMBH','GHDWH','MC','DHPCH','SHRQ','YSDJ','SSDH','ZDQH','ZDXH','SM','DJ','JXSSSL','MY','SYS','ZDPCH','JSRQ','JSLX','ZDPCH1','TSLSH','YSRQ','YSDBRQ','JCDRQ','KQBJ','JSGHDWH','YSGHDWH','FLOWID_DHMX');
yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username,$timetype);
}


$fileName = '教材当月已结算-到货已结-进退';
$lujing = jcOptionDs($timetype,$yewugongsi); // 获取到文件存放地址，以及文件名拼接需要的时间类型（月份，季度，年度）
$path = __DIR__.'/uploadfile/'.$lujing['path'];
if(!file_exists($path)) {
	mkdir( iconv('UTF-8','GBK',$path), 0777, true );
}
$filepath = $path.$admin_username.'-'.iconv("UTF-8","gbk",$lujing['file_path']).'-'.iconv("UTF-8","gbk",$fileName).'.csv';  //$timetype 为一月份  一季度  1-6
if(!file_exists($filepath)){ 
$sql="Select YWBMBH,GHDWH,MC,ID,SM,DJ,ZMY,ZSY,BZ,YWPCH,ZDPCH, JSLX,ZDQH,ZDPCH1,TSLSH,NF,NFHRQ,NJSRQ,JSGHDWH,YSGHDWH,ZCS,to_char(t_Month_jtjsb.DBRQ, 'YYYY-MM-DD HH24:MI:SS') as DBRQ,to_char(t_Month_jtjsb.JSRQ, 'YYYY-MM-DD HH24:MI:SS') as JSRQ  From t_Month_jtjsb Where jsrq>=Date'$start' And jsrq < Date'$end' And ywbmbh='$admin_username'";
$row=$ora->query($sql);
$total = count($row);
$title = array('YWBMBH','GHDWH','MC','DBRQ','ID','SM','DJ','ZMY','ZSY','BZ','YWPCH','ZDPCH','JSRQ','JSLX','ZDQH','ZDPCH1','TSLSH','NF','NFHRQ','NJSRQ','JSGHDWH','YSGHDWH','ZCS');
yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username,$timetype);
}




//15
$fileName = '教材当月已结算-发货已结-到货';
$lujing = jcOptionDs($timetype,$yewugongsi); // 获取到文件存放地址，以及文件名拼接需要的时间类型（月份，季度，年度）
$path = __DIR__.'/uploadfile/'.$lujing['path'];
if(!file_exists($path)) {
	mkdir( iconv('UTF-8','GBK',$path), 0777, true );
}
$filepath = $path.$admin_username.'-'.iconv("UTF-8","gbk",$lujing['file_path']).'-'.iconv("UTF-8","gbk",$fileName).'.csv';  //$timetype 为一月份  一季度  1-6
if(!file_exists($filepath)){ 
$sql="Select cbny,ghdw,ghdwmc,YWBMBH,DH,MC,PFPCH,BZ,ZMY,ZSY,CBJ,TSLSH,SM,DJ,ZDQH,ZDXH,KFBH,JSLX,NF,NFHRQ,NJSRQ,JB,CWQRPC,YSDBRQ,ZCS,to_char(t_Month_fhjsb.DBRQ, 'YYYY-MM-DD HH24:MI:SS') as DBRQ,to_char(t_Month_fhjsb.PFRQ, 'YYYY-MM-DD HH24:MI:SS') as PFRQ ,to_char(t_Month_fhjsb.JSRQ, 'YYYY-MM-DD HH24:MI:SS') as JSRQ  From t_Month_fhjsb Where jsrq>=Date'$start' And jsrq < Date'$end' And ywbmbh='$admin_username'";
$row=$ora->query($sql);
$total = count($row);
$title = array('CBNY','GHDW','GHDWMC','YWBMBH','DH','MC','PFPCH','BZ','DBRQ','PFRQ','ZMY','ZSY','CBJ','TSLSH','JSRQ','SM','DJ','ZDQH','ZDXH','KFBH','JSLX','NF','NFHRQ','NJSRQ','JB','CWQRPC','YSDBRQ','ZCS');
yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username,$timetype);
}
//16
$fileName = '教材当月已结算-发货已结-退货';
$lujing = jcOptionDs($timetype,$yewugongsi); // 获取到文件存放地址，以及文件名拼接需要的时间类型（月份，季度，年度）
$path = __DIR__.'/uploadfile/'.$lujing['path'];
if(!file_exists($path)) {
	mkdir( iconv('UTF-8','GBK',$path), 0777, true );
}
$filepath = $path.$admin_username.'-'.iconv("UTF-8","gbk",$lujing['file_path']).'-'.iconv("UTF-8","gbk",$fileName).'.csv';  //$timetype 为一月份  一季度  1-6
if(!file_exists($filepath)){ 
$sql="Select * From t_Month_xtjsb Where jsrq>=Date'$start' And jsrq < Date'$end' And ywbmbh='$admin_username'";
$row=$ora->query($sql);
$total = count($row);
$title = array('GHDW','GHDWMC','YWBMBH','KFBH','DH','DM','PCH','THRQ','ZMY','ZSY','ZCB','TSLSH','JSRQ','JSLX','ZDQH','NF','NFHRQ','NJSRQ','JB','CWQRPC','ZCS','DJ','ID','BB','SM','ISBN','CBNY');
yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username,$timetype);
}
//17
$fileName = '教材当月已结算-发货已结-更正';
$lujing = jcOptionDs($timetype,$yewugongsi); // 获取到文件存放地址，以及文件名拼接需要的时间类型（月份，季度，年度）
$path = __DIR__.'/uploadfile/'.$lujing['path'];
if(!file_exists($path)) {
	mkdir( iconv('UTF-8','GBK',$path), 0777, true );
}
$filepath = $path.$admin_username.'-'.iconv("UTF-8","gbk",$lujing['file_path']).'-'.iconv("UTF-8","gbk",$fileName).'.csv';  //$timetype 为一月份  一季度  1-6
if(!file_exists($filepath)){ 
$sql="Select * From t_Month_Fhgzjsb Where jsrq>=Date'$start' And jsrq < Date'$end' And ywbmbh='$admin_username'";
$row=$ora->query($sql);
$total = count($row);
$title = array('CBNY','GHDW','GHDWMC','YWBMBH','KFBH','DH','DM','GZPCH','GZRQ','ZMY','ZSY','ZCB','TSLSH','JSRQ','BZ','JSLX','ZDQH','NF','NFHRQ','NJSRQ','JB','ZCS');
yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username,$timetype);
}
//18
$fileName = '教材应付款-到货未结明细';
$lujing = jcOptionDs($timetype,$yewugongsi); // 获取到文件存放地址，以及文件名拼接需要的时间类型（月份，季度，年度）
$path = __DIR__.'/uploadfile/'.$lujing['path'];
if(!file_exists($path)) {
	mkdir( iconv('UTF-8','GBK',$path), 0777, true );
}
$filepath = $path.$admin_username.'-'.iconv("UTF-8","gbk",$lujing['file_path']).'-'.iconv("UTF-8","gbk",$fileName).'.csv';  //$timetype 为一月份  一季度  1-6
if(!file_exists($filepath)){ 
$sql="Select YWBMBH,GHDWH,MC,DHPCH,YSDJ,SSDH,ZDQH,ZDXH,SM,DJ,JXSSSL,MY,SYS,ZDPCH,JSRQ,JSLX,ZDPCH1,TSLSH,YSDBRQ,JCDRQ,KQBJ,JSGHDWH,YSGHDWH,FLOWID_DHMX,to_char(t_Month_Dhjsb.SHRQ, 'YYYY-MM-DD HH24:MI:SS') as SHRQ,to_char(t_Month_Dhjsb.YSRQ, 'YYYY-MM-DD HH24:MI:SS') as YSRQ  From t_Month_Dhjsb Where ywbmbh='$admin_username' And jsrq Is Null";
$row=$ora->query($sql);
$total = count($row);
$title = array('YWBMBH','GHDWH','MC','DHPCH','SHRQ','YSDJ','SSDH','ZDQH','ZDXH','SM','DJ','JXSSSL','MY','SYS','ZDPCH','JSRQ','JSLX','ZDPCH1','TSLSH','YSRQ','YSDBRQ','JCDRQ','KQBJ','JSGHDWH','YSGHDWH','FLOWID_DHMX');
yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username,$timetype);
}
//19
$fileName = '教材应付款-进退未结明细';
$lujing = jcOptionDs($timetype,$yewugongsi); // 获取到文件存放地址，以及文件名拼接需要的时间类型（月份，季度，年度）
$path = __DIR__.'/uploadfile/'.$lujing['path'];
if(!file_exists($path)) {
	mkdir( iconv('UTF-8','GBK',$path), 0777, true );
}
$filepath = $path.$admin_username.'-'.iconv("UTF-8","gbk",$lujing['file_path']).'-'.iconv("UTF-8","gbk",$fileName).'.csv';  //$timetype 为一月份  一季度  1-6
if(!file_exists($filepath)){ 
$sql="Select YWBMBH,GHDWH,MC,ID,SM,DJ,ZMY,ZSY,BZ,YWPCH,ZDPCH,JSRQ,JSLX,ZDQH,ZDXH,ZDPCH1,TSLSH,NF,NFHRQ,NJSRQ,JSGHDWH,YSGHDWH,ZCS,to_char(t_Month_jtjsb.DBRQ, 'YYYY-MM-DD HH24:MI:SS') as DBRQ From t_Month_jtjsb Where ywbmbh='$admin_username' And jsrq Is Null";
$row=$ora->query($sql);
$total = count($row);
$title = array('YWBMBH','GHDWH','MC','DBRQ','ID','SM','DJ','ZMY','ZSY','BZ','YWPCH','ZDPCH','JSRQ','JSLX','ZDQH','ZDXH','ZDPCH1','TSLSH','NF','NFHRQ','NJSRQ','JSGHDWH','YSGHDWH','ZCS');
yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username,$timetype);
}

//19+
$fileName = '教材应付款-出版社让利未结';
$lujing = jcOptionDs($timetype,$yewugongsi); // 获取到文件存放地址，以及文件名拼接需要的时间类型（月份，季度，年度）
$path = __DIR__.'/uploadfile/'.$lujing['path'];
if(!file_exists($path)) {
	mkdir( iconv('UTF-8','GBK',$path), 0777, true );
}
$filepath = $path.$admin_username.'-'.iconv("UTF-8","gbk",$lujing['file_path']).'-'.iconv("UTF-8","gbk",$fileName).'.csv';  //$timetype 为一月份  一季度  1-6
if(!file_exists($filepath)){ 
$sql="Select YWBMBH,GHDWH,MC,DHPCH,YSDJ,SSDH,ZDQH,ZDXH,SM,DJ,JXSSSL,MY,SYS,ZDPCH,JSRQ,JSLX,JSGHDWH,YSGHDWH,TSLSH,to_char(t_month_dhjsb_mf.SHRQ, 'YYYY-MM-DD HH24:MI:SS') as SHRQ From t_month_dhjsb_mf  Where jsrq Is Null And ywbmbh='$admin_username'";
$row=$ora->query($sql);
$total = count($row);
$title = array('YWBMBH','GHDWH','MC','DHPCH','SHRQ','YSDJ','SSDH','ZDQH','ZDXH','SM','BZ','DJ','JXSSSL','MY','SYS','ZDPCH','JSRQ','JSLX','JSGHDWH','YSGHDWH','TSLSH');
yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username,$timetype);
}



//20
$fileName = '教材应收款-发货未结明细';
$lujing = jcOptionDs($timetype,$yewugongsi); // 获取到文件存放地址，以及文件名拼接需要的时间类型（月份，季度，年度）
$path = __DIR__.'/uploadfile/'.$lujing['path'];
if(!file_exists($path)) {
	mkdir( iconv('UTF-8','GBK',$path), 0777, true );
}
$filepath = $path.$admin_username.'-'.iconv("UTF-8","gbk",$lujing['file_path']).'-'.iconv("UTF-8","gbk",$fileName).'.csv';  //$timetype 为一月份  一季度  1-6
if(!file_exists($filepath)){ 
$sql="Select cbny,ghdw,ghdwmc,YWBMBH,DH,MC,PFPCH,BZ,ZMY,ZSY,CBJ,TSLSH,JSRQ,SM,DJ,ZDQH,ZDXH,KFBH,JSLX,NF,NFHRQ,NJSRQ,JB,CWQRPC,YSDBRQ,ZCS,to_char(t_Month_fhjsb.DBRQ, 'YYYY-MM-DD HH24:MI:SS') as DBRQ,to_char(t_Month_fhjsb.PFRQ, 'YYYY-MM-DD HH24:MI:SS') as PFRQ From t_Month_fhjsb Where ywbmbh='$admin_username' And jsrq Is Null";
$row=$ora->query($sql);
$total = count($row);
$title = array('CBNY','GHDW','GHDWMC','YWBMBH','DH','MC','PFPCH','BZ','DBRQ','PFRQ','ZMY','ZSY','CBJ','TSLSH','JSRQ','SM','DJ','ZDQH','ZDXH','KFBH','JSLX','NF','NFHRQ','NJSRQ','JB','CWQRPC','YSDBRQ','ZCS');
yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username,$timetype);
}
//21
$fileName = '教材应收款-销退未结明细';
$lujing = jcOptionDs($timetype,$yewugongsi); // 获取到文件存放地址，以及文件名拼接需要的时间类型（月份，季度，年度）
$path = __DIR__.'/uploadfile/'.$lujing['path'];
if(!file_exists($path)) {
	mkdir( iconv('UTF-8','GBK',$path), 0777, true );
}
$filepath = $path.$admin_username.'-'.iconv("UTF-8","gbk",$lujing['file_path']).'-'.iconv("UTF-8","gbk",$fileName).'.csv';  //$timetype 为一月份  一季度  1-6
if(!file_exists($filepath)){ 
$sql="Select cbny,ghdw,ghdwmc,YWBMBH,KFBH,DH,DM,PCH,ZMY,ZSY,ZCB,TSLSH,JSRQ,JSLX,ZDQH,NF,NFHRQ,NJSRQ,JB,CWQRPC,ZCS,DJ,to_char(t_Month_Xtjsb.THRQ, 'YYYY-MM-DD HH24:MI:SS') as THRQ From t_Month_Xtjsb Where ywbmbh='$admin_username' And jsrq Is Null";
$row=$ora->query($sql);
$total = count($row);
$title = array('CBNY','GHDW','GHDWMC','YWBMBH','KFBH','DH','DM','PCH','THRQ','ZMY','ZSY','ZCB','TSLSH','JSRQ','JSLX','ZDQH','NF','NFHRQ','NJSRQ','JB','CWQRPC','ZCS','DJ');
yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username,$timetype);
}
//22
$fileName = '教材应收款-更正未结明细';
$lujing = jcOptionDs($timetype,$yewugongsi); // 获取到文件存放地址，以及文件名拼接需要的时间类型（月份，季度，年度）
$path = __DIR__.'/uploadfile/'.$lujing['path'];
if(!file_exists($path)) {
	mkdir( iconv('UTF-8','GBK',$path), 0777, true );
}
$filepath = $path.$admin_username.'-'.iconv("UTF-8","gbk",$lujing['file_path']).'-'.iconv("UTF-8","gbk",$fileName).'.csv';  //$timetype 为一月份  一季度  1-6
if(!file_exists($filepath)){ 
$sql="Select YWBMBH,KFBH,DH,DM,GZPCH,ZMY,ZSY,ZCB,TSLSH,JSRQ,BZ,JSLX,ZDQH,NF,NFHRQ,NJSRQ,JB,ZCS,to_char(t_Month_fhgzjsb.GZRQ, 'YYYY-MM-DD HH24:MI:SS') as GZRQ From t_Month_fhgzjsb Where ywbmbh='$admin_username' And jsrq Is Null";
$row=$ora->query($sql);
$total = count($row);
$title = array('YWBMBH','KFBH','DH','DM','GZPCH','GZRQ','ZMY','ZSY','ZCB','TSLSH','JSRQ','BZ','JSLX','ZDQH','NF','NFHRQ','NJSRQ','JB','ZCS');
yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username,$timetype);
}

$yewugongsi = 1;//教辅公司

$abc = array('020001','020002','020003','020004','020005');
$admin_username = implode(',',$abc);


$fileName = '教辅应付款-到货未结明细';
$lujing = jcOptionDs($timetype,$yewugongsi); // 获取到文件存放地址，以及文件名拼接需要的时间类型（月份，季度，年度）
$path = __DIR__.'/uploadfile/'.$lujing['path'];
if(!file_exists($path)) {
	mkdir( iconv('UTF-8','GBK',$path), 0777, true );
}
$filepath = $path.$admin_username.'-'.iconv("UTF-8","gbk",$lujing['file_path']).'-'.iconv("UTF-8","gbk",$fileName).'.csv';  //$timetype 为一月份  一季度  1-6
if(!file_exists($filepath)){ 
$sql="Select YWBMBH,GHDWH,MC,DHPCH,YSDJ,SSDH,ZDQH,ZDXH,SM,DJ,JXSSSL,MY,SYS,ZDPCH,JSRQ,JSLX,ZDPCH1,TSLSH,YSDBRQ,JCDRQ,KQBJ,JSGHDWH,YSGHDWH,FLOWID_DHMX,to_char(t_Month_Dhjsb.SHRQ, 'YYYY-MM-DD HH24:MI:SS') as SHRQ,to_char(t_Month_Dhjsb.YSRQ, 'YYYY-MM-DD HH24:MI:SS') as YSRQ  From t_Month_Dhjsb Where ywbmbh in ($admin_username) And jsrq Is Null";
$row=$ora->query($sql);
$total = count($row);
$title = array('YWBMBH','GHDWH','MC','DHPCH','SHRQ','YSDJ','SSDH','ZDQH','ZDXH','SM','DJ','JXSSSL','MY','SYS','ZDPCH','JSRQ','JSLX','ZDPCH1','TSLSH','YSRQ','YSDBRQ','JCDRQ','KQBJ','JSGHDWH','YSGHDWH','FLOWID_DHMX');
yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username,$timetype);
}
//exit;
//19
$fileName = '教辅应付款-进退未结明细';
$lujing = jcOptionDs($timetype,$yewugongsi); // 获取到文件存放地址，以及文件名拼接需要的时间类型（月份，季度，年度）
$path = __DIR__.'/uploadfile/'.$lujing['path'];
if(!file_exists($path)) {
	mkdir( iconv('UTF-8','GBK',$path), 0777, true );
}
$filepath = $path.$admin_username.'-'.iconv("UTF-8","gbk",$lujing['file_path']).'-'.iconv("UTF-8","gbk",$fileName).'.csv';  //$timetype 为一月份  一季度  1-6
if(!file_exists($filepath)){ 
$sql="Select YWBMBH,GHDWH,MC,ID,SM,DJ,ZMY,ZSY,BZ,YWPCH,ZDPCH,JSRQ,JSLX,ZDQH,ZDXH,ZDPCH1,TSLSH,NF,NFHRQ,NJSRQ,JSGHDWH,YSGHDWH,ZCS,to_char(t_Month_jtjsb.DBRQ, 'YYYY-MM-DD HH24:MI:SS') as DBRQ From t_Month_jtjsb Where ywbmbh in ($admin_username) And jsrq Is Null";
$row=$ora->query($sql);
$total = count($row);
$title = array('YWBMBH','GHDWH','MC','DBRQ','ID','SM','DJ','ZMY','ZSY','BZ','YWPCH','ZDPCH','JSRQ','JSLX','ZDQH','ZDXH','ZDPCH1','TSLSH','NF','NFHRQ','NJSRQ','JSGHDWH','YSGHDWH','ZCS');
yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username,$timetype);
}
//20
set_time_limit(0);
ini_set('memory_limit', '-1');
ini_set("max_execution_time", "0");

$fileName = '教辅应收款-发货未结明细';
$lujing = jcOptionDs($timetype,$yewugongsi); // 获取到文件存放地址，以及文件名拼接需要的时间类型（月份，季度，年度）
$path = __DIR__.'/uploadfile/'.$lujing['path'];
if(!file_exists($path)) {
	mkdir( iconv('UTF-8','GBK',$path), 0777, true );
}
$filepath = $path.$admin_username.'-'.iconv("UTF-8","gbk",$lujing['file_path']).'-'.iconv("UTF-8","gbk",$fileName).'.csv';  //$timetype 为一月份  一季度  1-6
if(!file_exists($filepath)){ 
$sql="Select cbny,ghdw,ghdwmc,YWBMBH,DH,MC,PFPCH,BZ,ZMY,ZSY,CBJ,TSLSH,JSRQ,SM,DJ,ZDQH,ZDXH,KFBH,JSLX,NF,NFHRQ,NJSRQ,JB,CWQRPC,YSDBRQ,ZCS,to_char(t_Month_fhjsb.DBRQ, 'YYYY-MM-DD HH24:MI:SS') as DBRQ,to_char(t_Month_fhjsb.PFRQ, 'YYYY-MM-DD HH24:MI:SS') as PFRQ From t_Month_fhjsb Where ywbmbh in ($admin_username) And jsrq Is Null";
$row=$ora->query($sql);
$total = count($row);
$title = array('CBNY','GHDW','GHDWMC','YWBMBH','DH','MC','PFPCH','BZ','DBRQ','PFRQ','ZMY','ZSY','CBJ','TSLSH','JSRQ','SM','DJ','ZDQH','ZDXH','KFBH','JSLX','NF','NFHRQ','NJSRQ','JB','CWQRPC','YSDBRQ','ZCS');
yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username,$timetype);
}
//21
$fileName = '教辅应收款-销退未结明细';
$lujing = jcOptionDs($timetype,$yewugongsi); // 获取到文件存放地址，以及文件名拼接需要的时间类型（月份，季度，年度）
$path = __DIR__.'/uploadfile/'.$lujing['path'];
if(!file_exists($path)) {
	mkdir( iconv('UTF-8','GBK',$path), 0777, true );
}
$filepath = $path.$admin_username.'-'.iconv("UTF-8","gbk",$lujing['file_path']).'-'.iconv("UTF-8","gbk",$fileName).'.csv';  //$timetype 为一月份  一季度  1-6
if(!file_exists($filepath)){ 
$sql="Select cbny,ghdw,ghdwmc,YWBMBH,KFBH,DH,DM,PCH,ZMY,ZSY,ZCB,TSLSH,JSRQ,JSLX,ZDQH,NF,NFHRQ,NJSRQ,JB,CWQRPC,ZCS,DJ,to_char(t_Month_Xtjsb.THRQ, 'YYYY-MM-DD HH24:MI:SS') as THRQ From t_Month_Xtjsb Where ywbmbh in ($admin_username) And jsrq Is Null";
$row=$ora->query($sql);
$total = count($row);
$title = array('CBNY','GHDW','GHDWMC','YWBMBH','KFBH','DH','DM','PCH','THRQ','ZMY','ZSY','ZCB','TSLSH','JSRQ','JSLX','ZDQH','NF','NFHRQ','NJSRQ','JB','CWQRPC','ZCS','DJ');
yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username,$timetype);
}
//22
$fileName = '教辅应收款-更正未结明细';
$lujing = jcOptionDs($timetype,$yewugongsi); // 获取到文件存放地址，以及文件名拼接需要的时间类型（月份，季度，年度）
$path = __DIR__.'/uploadfile/'.$lujing['path'];
if(!file_exists($path)) {
	mkdir( iconv('UTF-8','GBK',$path), 0777, true );
}
$filepath = $path.$admin_username.'-'.iconv("UTF-8","gbk",$lujing['file_path']).'-'.iconv("UTF-8","gbk",$fileName).'.csv';  //$timetype 为一月份  一季度  1-6
if(!file_exists($filepath)){ 
$sql="Select YWBMBH,KFBH,DH,DM,GZPCH,ZMY,ZSY,ZCB,TSLSH,JSRQ,BZ,JSLX,ZDQH,NF,NFHRQ,NJSRQ,JB,ZCS,to_char(t_Month_fhgzjsb.GZRQ, 'YYYY-MM-DD HH24:MI:SS') as GZRQ From t_Month_fhgzjsb Where ywbmbh in ($admin_username) And jsrq Is Null";
$row=$ora->query($sql);
$total = count($row);
$title = array('YWBMBH','KFBH','DH','DM','GZPCH','GZRQ','ZMY','ZSY','ZCB','TSLSH','JSRQ','BZ','JSLX','ZDQH','NF','NFHRQ','NJSRQ','JB','ZCS');
yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username,$timetype);
}
//exit;


//1
$fileName = '教辅月报';
$lujing = jcOptionDs($timetype,$yewugongsi); // 获取到文件存放地址，以及文件名拼接需要的时间类型（月份，季度，年度）
$path = __DIR__.'/uploadfile/'.$lujing['path'];
if(!file_exists($path)) {
	mkdir( iconv('UTF-8','GBK',$path), 0777, true );
}
$filepath = $path.$admin_username.'-'.iconv("UTF-8","gbk",$lujing['file_path']).'-'.iconv("UTF-8","gbk",$fileName).'.csv';  //$timetype 为一月份  一季度  1-6
if(!file_exists($filepath)){ 
	$sql="Select * From t_Month_Hzb_Nian Where ywbmbh in ($admin_username)";
	$row=$ora->query($sql);
	$total = count($row);
	$title = array('YWBMBH','LX','BH','DMMC','QCWJMY','QCWJSY','QCWJCB','JFMY','JFSY','JFCB','JSMY','JSSY','JSCB','QMWJMY','QMWJSY','QMWJCB','SYLX','BB','MINDATE','MAXDATE','NY','BJSMY','BJSSY');
	yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username,$timetype);
}
//2
$fileName = '教辅到货明细';
$lujing = jcOptionDs($timetype,$yewugongsi); // 获取到文件存放地址，以及文件名拼接需要的时间类型（月份，季度，年度）
$path = __DIR__.'/uploadfile/'.$lujing['path'];
if(!file_exists($path)) {
	mkdir( iconv('UTF-8','GBK',$path), 0777, true );
}
$filepath = $path.$admin_username.'-'.iconv("UTF-8","gbk",$lujing['file_path']).'-'.iconv("UTF-8","gbk",$fileName).'.csv';  //$timetype 为一月份  一季度  1-6
if(!file_exists($filepath)){ 
$sql="Select YWBMBH,GHDWH,MC,DHPCH,YSDJ,SSDH,ZDQH,ZDXH,SM,DJ,JXSSSL,MY,SYS,ZDPCH,JSRQ,JSLX,ZDPCH1,TSLSH,YSDBRQ,JCDRQ,KQBJ,JSGHDWH,YSGHDWH,FLOWID_DHMX,to_char(t_month_dhjsb.SHRQ, 'YYYY-MM-DD HH24:MI:SS') as SHRQ ,to_char(t_month_dhjsb.YSRQ, 'YYYY-MM-DD HH24:MI:SS') as YSRQ From t_month_dhjsb Where ywbmbh in ($admin_username) And shrq>=Date'$start' and shrq< Date'$end'";
$row=$ora->query($sql);
$total = count($row);
$title = array('YWBMBH','GHDWH','MC','DHPCH','SHRQ','YSDJ','SSDH','ZDQH','ZDXH','SM','DJ','JXSSSL','MY','SYS','ZDPCH','JSRQ','JSLX','ZDPCH1','TSLSH','YSRQ','YSDBRQ','JCDRQ','KQBJ','JSGHDWH','YSGHDWH','FLOWID_DHMX');
yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username,$timetype);
}
//3
$fileName = '教辅进退明细';
$lujing = jcOptionDs($timetype,$yewugongsi); // 获取到文件存放地址，以及文件名拼接需要的时间类型（月份，季度，年度）
$path = __DIR__.'/uploadfile/'.$lujing['path'];
if(!file_exists($path)) {
	mkdir( iconv('UTF-8','GBK',$path), 0777, true );
}
$filepath = $path.$admin_username.'-'.iconv("UTF-8","gbk",$lujing['file_path']).'-'.iconv("UTF-8","gbk",$fileName).'.csv';  //$timetype 为一月份  一季度  1-6
if(!file_exists($filepath)){ 
$sql="Select YWBMBH,GHDWH,MC,ID,SM,DJ,ZMY,ZSY,BZ,YWPCH,ZDPCH,JSRQ,JSLX,ZDQH,ZDXH,ZDPCH1,TSLSH,NF,NFHRQ,NJSRQ,JSGHDWH,YSGHDWH,ZCS,to_char(t_month_jtjsb.DBRQ, 'YYYY-MM-DD HH24:MI:SS') as DBRQ
 From t_month_jtjsb Where ywbmbh in ($admin_username) And dbrq>=Date'$start' and dbrq< Date'$end'";
$row=$ora->query($sql);
$total = count($row);
$title = array('YWBMBH','GHDWH','MC','DBRQ','ID','SM','DJ','ZMY','ZSY','BZ','YWPCH','ZDPCH','JSRQ','JSLX','ZDQH','ZDXH','ZDPCH1','TSLSH','NF','NFHRQ','NJSRQ','JSGHDWH','YSGHDWH','ZCS');
yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username,$timetype);
}
//4
$fileName = '教辅出版社免费让利明细';
$lujing = jcOptionDs($timetype,$yewugongsi); // 获取到文件存放地址，以及文件名拼接需要的时间类型（月份，季度，年度）
$path = __DIR__.'/uploadfile/'.$lujing['path'];
if(!file_exists($path)) {
	mkdir( iconv('UTF-8','GBK',$path), 0777, true );
}
$filepath = $path.$admin_username.'-'.iconv("UTF-8","gbk",$lujing['file_path']).'-'.iconv("UTF-8","gbk",$fileName).'.csv';  //$timetype 为一月份  一季度  1-6
if(!file_exists($filepath)){ 
$sql="Select YWBMBH,GHDWH,MC,DHPCH,SHRQ,YSDJ,SSDH,ZDQH,ZDXH,SM,DJ,JXSSSL,MY,SYS,ZDPCH,JSRQ,JSLX,JSGHDWH,YSGHDWH,TSLSH
 From t_month_dhjsb_mf Where ywbmbh in ($admin_username) And shrq>=Date'$start' and shrq< Date'$end'";
$row=$ora->query($sql);
$total = count($row);
$title = array('YWBMBH','GHDWH','MC','DHPCH','SHRQ','YSDJ','SSDH','ZDQH','ZDXH','SM','DJ','JXSSSL','MY','SYS','ZDPCH','JSRQ','JSLX','JSGHDWH','YSGHDWH','TSLSH');
yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username,$timetype);
}
//5
$fileName = '教辅销退明细';
$lujing = jcOptionDs($timetype,$yewugongsi); // 获取到文件存放地址，以及文件名拼接需要的时间类型（月份，季度，年度）
$path = __DIR__.'/uploadfile/'.$lujing['path'];
if(!file_exists($path)) {
	mkdir( iconv('UTF-8','GBK',$path), 0777, true );
}
$filepath = $path.$admin_username.'-'.iconv("UTF-8","gbk",$lujing['file_path']).'-'.iconv("UTF-8","gbk",$fileName).'.csv';  //$timetype 为一月份  一季度  1-6
if(!file_exists($filepath)){ 
//$sql="Select ghdw,ghdwmc,YWBMBH,KFBH,DH,DM,PCH,ZMY,ZSY,ZCB,TSLSH,JSRQ,JSLX,ZDQH,NF,NFHRQ,NJSRQ,JB,CWQRPC,ZCS,DJ,ID,BB,SM,ISBN,CBNY,to_char(t_month_xtjsb.THRQ, 'YYYY-MM-DD HH24:MI:SS') as THRQ From t_month_xtjsb Where ywbmbh in ($admin_username) And thrq>=Date'$start' and thrq< Date'$end'";
$sql="Select ghdw,ghdwmc,YWBMBH,KFBH,DH,DM,PCH,ZMY,ZSY,ZCB,TSLSH,JSLX,ZDQH,NF,NFHRQ,NJSRQ,JB,CWQRPC,ZCS,DJ,ID,BB,SM,ISBN,CBNY,to_char(t_month_xtjsb.THRQ, 'YYYY-MM-DD HH24:MI:SS') as THRQ,to_char(t_month_xtjsb.JSRQ, 'YYYY-MM-DD HH24:MI:SS') as JSRQ From t_month_xtjsb Where ywbmbh in ($admin_username) And thrq>=Date'$start' and thrq< Date'$end'";
$row=$ora->query($sql);
$total = count($row);
$title = array('GHDW','GHDWMC','YWBMBH','KFBH','DH','DM','PCH','THRQ','ZMY','ZSY','ZCB','TSLSH','JSRQ','JSLX','ZDQH','NF','NFHRQ','NJSRQ','JB','CWQRPC','ZCS','DJ','ID','BB','SM','ISBN','CBNY');
yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username,$timetype);
}
//6
$fileName = '教辅基层店免费让利明细';
$lujing = jcOptionDs($timetype,$yewugongsi); // 获取到文件存放地址，以及文件名拼接需要的时间类型（月份，季度，年度）
$path = __DIR__.'/uploadfile/'.$lujing['path'];
if(!file_exists($path)) {
	mkdir( iconv('UTF-8','GBK',$path), 0777, true );
}
$filepath = $path.$admin_username.'-'.iconv("UTF-8","gbk",$lujing['file_path']).'-'.iconv("UTF-8","gbk",$fileName).'.csv';  //$timetype 为一月份  一季度  1-6
if(!file_exists($filepath)){ 
$sql="Select * From t_month_fhgzjsb Where ywbmbh in ($admin_username) And gzrq>=Date'$start' and gzrq< Date'$end'";
$row=$ora->query($sql);
$total = count($row);
$title = array('YWBMBH','KFBH','DH','DM','GZPCH','GZRQ','ZMY','ZSY','ZCB','TSLSH','JSRQ','BZ','JSLX','ZDQH','NF','NFHRQ','NJSRQ','JB','ZCS');
yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username,$timetype);
}
//7
$fileName = '教辅发货明细';
$lujing = jcOptionDs($timetype,$yewugongsi); // 获取到文件存放地址，以及文件名拼接需要的时间类型（月份，季度，年度）
$path = __DIR__.'/uploadfile/'.$lujing['path'];
if(!file_exists($path)) {
	mkdir( iconv('UTF-8','GBK',$path), 0777, true );
}
$filepath = $path.$admin_username.'-'.iconv("UTF-8","gbk",$lujing['file_path']).'-'.iconv("UTF-8","gbk",$fileName).'.csv';  //$timetype 为一月份  一季度  1-6
if(!file_exists($filepath)){ 
$sql="Select cbny,ghdw,ghdwmc,YWBMBH,DH,MC,PFPCH,BZ,ZMY,ZSY,CBJ,TSLSH,JSRQ,SM,DJ,ZDQH,ZDXH,KFBH,JSLX,NF,NFHRQ,NJSRQ,JB,CWQRPC,YSDBRQ,ZCS,to_char(t_month_fhjsb.DBRQ, 'YYYY-MM-DD HH24:MI:SS') as DBRQ
,to_char(t_month_fhjsb.PFRQ, 'YYYY-MM-DD HH24:MI:SS') as PFRQ From t_month_fhjsb Where ywbmbh in ($admin_username) And dbrq>=Date'$start' and dbrq< Date'$end'";
$row=$ora->query($sql);
$total = count($row);
$title = array('CBNY','GHDW','GHDWMC','YWBMBH','DH','MC','PFPCH','BZ','DBRQ','PFRQ','ZMY','ZSY','CBJ','TSLSH','JSRQ','SM','DJ','ZDQH','ZDXH','KFBH','JSLX','NF','NFHRQ','NJSRQ','JB','CWQRPC','YSDBRQ','ZCS');
yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username,$timetype);
}
//8
$fileName = '教辅库存明细';
$lujing = jcOptionDs($timetype,$yewugongsi); // 获取到文件存放地址，以及文件名拼接需要的时间类型（月份，季度，年度）
$path = __DIR__.'/uploadfile/'.$lujing['path'];
if(!file_exists($path)) {
	mkdir( iconv('UTF-8','GBK',$path), 0777, true );
}
$filepath = $path.$admin_username.'-'.iconv("UTF-8","gbk",$lujing['file_path']).'-'.iconv("UTF-8","gbk",$fileName).'.csv';  //$timetype 为一月份  一季度  1-6
if(!file_exists($filepath)){ 
$dangyue = dy_month(); //当月第一天
$start_kc = date('Y-m-d',$dangyue['start_time']);
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
	   From t_Hd_Kcsl_Bf@zczm_jc t Where kccs<>0 And ywbmbh in ($admin_username) And bfsj>=Date'$start_kc'";
$row=$ora->query($sql);
$total = count($row);
$title = array('YWBMBH','ID','SM','DJ','版别','KCCS','MY','SYS','KF','WL','分类','HW','供货单位','YWY','ISBN','版次','编者','年');
yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username,$timetype);
}


$fileName = '教辅库存明细提前出';
$lujing = jcOptionDs($timetype,$yewugongsi); // 获取到文件存放地址，以及文件名拼接需要的时间类型（月份，季度，年度）
$path = __DIR__.'/uploadfile/'.$lujing['path'];
if(!file_exists($path)) {
	mkdir( iconv('UTF-8','GBK',$path), 0777, true );
}
$filepath = $path.$admin_username.'-'.iconv("UTF-8","gbk",$lujing['file_path']).'-'.iconv("UTF-8","gbk",$fileName).'.csv';  //$timetype 为一月份  一季度  1-6
if(!file_exists($filepath)){ 
$dangyue = dy_month(); //当月第一天
$start_kc = date('Y-m-d',$dangyue['start_time']);
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
$row=$ora->query($sql);
$total = count($row);
$title = array('YWBMBH','ID','SM','DJ','版别','KCCS','MY','SYS','KF','WL','分类','HW','供货单位','YWY','ISBN','版次','编者','年');
yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username,$timetype);
}












//9
$fileName = '教辅配发中间态';
$lujing = jcOptionDs($timetype,$yewugongsi); // 获取到文件存放地址，以及文件名拼接需要的时间类型（月份，季度，年度）
$path = __DIR__.'/uploadfile/'.$lujing['path'];
if(!file_exists($path)) {
	mkdir( iconv('UTF-8','GBK',$path), 0777, true );
}
$filepath = $path.$admin_username.'-'.iconv("UTF-8","gbk",$lujing['file_path']).'-'.iconv("UTF-8","gbk",$fileName).'.csv';  //$timetype 为一月份  一季度  1-6
if(!file_exists($filepath)){ 
$dangyue = dy_month(); //当月第一天
$start_pf = date('Y-m-d',$dangyue['start_time']);
$sql="Select Ywbmbh ,(select dm from t_dm@zczm_jc where dh=a.ywbmbh) as ywbmmc,dh,(select dm from t_dm@zczm_jc where dh=a.dh) as dm,
a.id,(select isbn from t_kcsm@zczm_jc where a.id=id) as isbn,(select sm from t_kcsm@zczm_jc where a.id=id) as sm,
(select dj from t_kcsm@zczm_jc where a.id=id) as dj,(Select round(cbny) From t_kcsm@zczm_jc Where Id=a.id)As cbny,a.pxcs,(select dj*a.pxcs from t_kcsm@zczm_jc where a.id=id) as zmy ,a.sys,a.cbj,
a.pxdh,a.bz,a.zdh,a.sylx,a.sl,a.zjxs,a.qx,a.djbh,a.kfbh,A.Bjlsh,a.cybh,A.Flowid,a.zdqh,a.zdxh,a.zdqh1,a.zdxh1,
to_char(a.pfrq, 'YYYY-MM-DD HH24:MI:SS') as pfrq From t_hd_pfmx@zczm_jc a Where bfsj>=Date'$start_pf' and ckbj='1' and ywbmbh in ($admin_username)";
$row=$ora->query($sql);
$total = count($row);
$title = array('YWBMBH','YWBMMC','DH','DM','ID','ISBN','SM','DJ','CBNY','PXCS','ZMY','SYS','CBJ','PXDH','BZ','ZDH','SYLX','SL','ZJXS','QX','DJBH','KFBH','BJLSH','CYBH','FLOWID','ZDQH','ZDXH','ZDQH1','ZDXH1','PFRQ');
yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username,$timetype);
}
//10
$fileName = '教辅库房转移中间态明细';
$lujing = jcOptionDs($timetype,$yewugongsi); // 获取到文件存放地址，以及文件名拼接需要的时间类型（月份，季度，年度）
$path = __DIR__.'/uploadfile/'.$lujing['path'];
if(!file_exists($path)) {
	mkdir( iconv('UTF-8','GBK',$path), 0777, true );
}
$filepath = $path.$admin_username.'-'.iconv("UTF-8","gbk",$lujing['file_path']).'-'.iconv("UTF-8","gbk",$fileName).'.csv';  //$timetype 为一月份  一季度  1-6
if(!file_exists($filepath)){ 
$sql="Select Id ,(Select sm From t_kcsm@zczm_jc Where Id=A.id)As sm,(Select isbn From t_kcsm@zczm_jc Where Id=A.id)As  isbn,
(Select dj From t_kcsm@zczm_jc Where Id=A.id)As dj,(Select min(mc) From t_bb@zczm_jc Where bh=(Select bb From t_kcsm@zczm_jc Where Id=A.id))As bb,
		  (Select mc From t_ghdw@zczm_jc Where bh=(Select min(ghdwh) From t_dhls_jx@zczm_jc Where Id=A.id))As ghdw,(Select Name From t_user@zczm_jc Where usr_id=(Select ywryh From t_kcsm@zczm_jc Where Id=A.id))As ywy,cbny,ywlx,Sum(cs),Sum(cs*(Select dj From t_kcsm@zczm_jc Where Id=a.id))As MY,Sum(sy)As SY From (
Select YWLX,Id,(Select round(cbny) From t_kcsm@zczm_jc Where Id=t_crkls.id)As cbny,
Sum(CRKCS)CS,sum(crksy)sy From t_crkls@zczm_jc Where ywbmbh in ($admin_username) And (YWLX='YC' Or YWLX='DC' )And crkrq>=Date'2018-07-01' And crkrq < Date'$end' Group By ywlx,Id
Union All Select YWLX,Id,(Select round(cbny) From t_kcsm@zczm_jc Where Id=t_crkls.id)As cbny,Sum(-CRKCS)CS,sum(-crksy)sy From t_crkls@zczm_jc Where ywbmbh in ($admin_username) And ( YWLX='YR'Or YWLX='DR' )
And crkrq>=Date'2018-07-01' And crkrq < Date'$end' Group By ywlx,Id) A Group By Id,ywlx,cbny Having Sum(cs)<>0";
$row=$ora->query($sql);
$total = count($row);
$title = array('ID','SM','ISBN','DJ','版别','供货单位','YWY','CBNY','YWLX','SUM(CS)','MY','SY');
yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username,$timetype);
}
//11
$fileName = '教辅损益明细';
$lujing = jcOptionDs($timetype,$yewugongsi); // 获取到文件存放地址，以及文件名拼接需要的时间类型（月份，季度，年度）
$path = __DIR__.'/uploadfile/'.$lujing['path'];
if(!file_exists($path)) {
	mkdir( iconv('UTF-8','GBK',$path), 0777, true );
}
$filepath = $path.$admin_username.'-'.iconv("UTF-8","gbk",$lujing['file_path']).'-'.iconv("UTF-8","gbk",$fileName).'.csv';  //$timetype 为一月份  一季度  1-6
if(!file_exists($filepath)){ 
$sql="Select KFBH,(Select SM From T_KCSM@zczm_jc Where Id=T.ID) As SM,CRKCS,(Select dj From t_kcsm@zczm_jc Where T.id=Id)as dj,(crkcs*(Select dj From t_kcsm@zczm_jc Where T.id=Id)) zmy,crksy,crkrq
From t_crkls@zczm_jc T Where  crkrq>=Date'$start' And crkrq < Date'$end' And ywbmbh in ($admin_username) And ywlx='SY'	";
$row=$ora->query($sql);
$total = count($row);
$title = array('KFBH','SM','CRKCS','DJ','ZMY','CRKSY','CRKRQ');
yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username,$timetype);
}
//12
$fileName = '教辅当月已结算-到货已结-到货';
$lujing = jcOptionDs($timetype,$yewugongsi); // 获取到文件存放地址，以及文件名拼接需要的时间类型（月份，季度，年度）
$path = __DIR__.'/uploadfile/'.$lujing['path'];
if(!file_exists($path)) {
	mkdir( iconv('UTF-8','GBK',$path), 0777, true );
}
$filepath = $path.$admin_username.'-'.iconv("UTF-8","gbk",$lujing['file_path']).'-'.iconv("UTF-8","gbk",$fileName).'.csv';  //$timetype 为一月份  一季度  1-6
if(!file_exists($filepath)){ 
$sql="Select YWBMBH,GHDWH,MC,DHPCH,YSDJ,SSDH,ZDQH,ZDXH,SM,DJ,JXSSSL,MY,SYS,ZDPCH,JSRQ,JSLX,ZDPCH1,TSLSH,YSDBRQ,JCDRQ,KQBJ,JSGHDWH,YSGHDWH,FLOWID_DHMX,to_char(t_Month_Dhjsb.SHRQ, 'YYYY-MM-DD HH24:MI:SS') as SHRQ,to_char(t_Month_Dhjsb.YSRQ, 'YYYY-MM-DD HH24:MI:SS') as YSRQ From t_Month_Dhjsb Where jsrq>=Date'$start' And jsrq < Date'$end' And ywbmbh in ($admin_username)";
$row=$ora->query($sql);
$total = count($row);
$title = array('YWBMBH','GHDWH','MC','DHPCH','SHRQ','YSDJ','SSDH','ZDQH','ZDXH','SM','DJ','JXSSSL','MY','SYS','ZDPCH','JSRQ','JSLX','ZDPCH1','TSLSH','YSRQ','YSDBRQ','JCDRQ','KQBJ','JSGHDWH','YSGHDWH','FLOWID_DHMX');
yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username,$timetype);
}

$fileName = '教辅当月已结算-到货已结-进退';
$lujing = jcOptionDs($timetype,$yewugongsi); // 获取到文件存放地址，以及文件名拼接需要的时间类型（月份，季度，年度）
$path = __DIR__.'/uploadfile/'.$lujing['path'];
if(!file_exists($path)) {
	mkdir( iconv('UTF-8','GBK',$path), 0777, true );
}
$filepath = $path.$admin_username.'-'.iconv("UTF-8","gbk",$lujing['file_path']).'-'.iconv("UTF-8","gbk",$fileName).'.csv';  //$timetype 为一月份  一季度  1-6
if(!file_exists($filepath)){ 
$sql="Select YWBMBH,GHDWH,MC,ID,SM,DJ,ZMY,ZSY,BZ,YWPCH,ZDPCH, JSLX,ZDQH,ZDPCH1,TSLSH,NF,NFHRQ,NJSRQ,JSGHDWH,YSGHDWH,ZCS,to_char(t_Month_jtjsb.DBRQ, 'YYYY-MM-DD HH24:MI:SS') as DBRQ,to_char(t_Month_jtjsb.JSRQ, 'YYYY-MM-DD HH24:MI:SS') as JSRQ  From t_Month_jtjsb Where jsrq>=Date'$start' And jsrq < Date'$end' And ywbmbh in ($admin_username)";
$row=$ora->query($sql);
$total = count($row);
$title = array('YWBMBH','GHDWH','MC','DBRQ','ID','SM','DJ','ZMY','ZSY','BZ','YWPCH','ZDPCH','JSRQ','JSLX','ZDQH','ZDPCH1','TSLSH','NF','NFHRQ','NJSRQ','JSGHDWH','YSGHDWH','ZCS');
yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username,$timetype);
}

//13
$fileName = '教辅当月已结算-到货已结-退货';
$lujing = jcOptionDs($timetype,$yewugongsi); // 获取到文件存放地址，以及文件名拼接需要的时间类型（月份，季度，年度）
$path = __DIR__.'/uploadfile/'.$lujing['path'];
if(!file_exists($path)) {
	mkdir( iconv('UTF-8','GBK',$path), 0777, true );
}
$filepath = $path.$admin_username.'-'.iconv("UTF-8","gbk",$lujing['file_path']).'-'.iconv("UTF-8","gbk",$fileName).'.csv';  //$timetype 为一月份  一季度  1-6
if(!file_exists($filepath)){ 
$sql="Select YWBMBH,GHDWH,MC,ID,SM,DJ,ZMY,ZSY,BZ,YWPCH,ZDPCH,JSLX,ZDQH,ZDXH,ZDPCH1,TSLSH,NF,NFHRQ,NJSRQ,JSGHDWH,YSGHDWH,ZCS,to_char(t_Month_jtjsb.DBRQ, 'YYYY-MM-DD HH24:MI:SS') as DBRQ,to_char(t_Month_jtjsb.JSRQ, 'YYYY-MM-DD HH24:MI:SS') as JSRQ From t_Month_jtjsb Where jsrq>=Date'$start' And jsrq < Date'$end' And ywbmbh in ($admin_username)";
$row=$ora->query($sql);
$total = count($row);
$title = array('YWBMBH','GHDWH','MC','ID','DBRQ','SM','DJ','ZMY','ZSY','BZ','YWPCH','JSRQ','ZDPCH','JSLX','ZDQH','ZDXH','ZDPCH1','TSLSH','NF','NFHRQ','NJSRQ','JSGHDWH','YSGHDWH','ZCS');
yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username,$timetype);
}
//14
$fileName = '教辅当月已结算-到货已结-更正';
$lujing = jcOptionDs($timetype,$yewugongsi); // 获取到文件存放地址，以及文件名拼接需要的时间类型（月份，季度，年度）
$path = __DIR__.'/uploadfile/'.$lujing['path'];
if(!file_exists($path)) {
	mkdir( iconv('UTF-8','GBK',$path), 0777, true );
}
$filepath = $path.$admin_username.'-'.iconv("UTF-8","gbk",$lujing['file_path']).'-'.iconv("UTF-8","gbk",$fileName).'.csv';  //$timetype 为一月份  一季度  1-6
if(!file_exists($filepath)){ 
$sql="Select YWBMBH,DH,MC,PFPCH,BZ,ZMY,ZSY,CBJ,TSLSH,SM,DJ,ZDQH,ZDXH,KFBH,JSLX,NF,NFHRQ,NJSRQ,JB,CWQRPC,YSDBRQ,ZCS,to_char(t_Month_fhjsb.DBRQ, 'YYYY-MM-DD HH24:MI:SS') as DBRQ,to_char(t_Month_fhjsb.PFRQ, 'YYYY-MM-DD HH24:MI:SS') as PFRQ ,to_char(t_Month_fhjsb.JSRQ, 'YYYY-MM-DD HH24:MI:SS') as JSRQ From t_Month_fhjsb Where jsrq>=Date'$start' And jsrq < Date'$end' And ywbmbh in ($admin_username)";
$row=$ora->query($sql);
$total = count($row);
$title = array('YWBMBH','DH','MC','PFPCH','BZ','DBRQ','PFRQ','ZMY','ZSY','CBJ','TSLSH','JSRQ','SM','DJ','ZDQH','ZDXH','KFBH','JSLX','NF','NFHRQ','NJSRQ','JB','CWQRPC','YSDBRQ','ZCS');
yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username,$timetype);
}

//15
$fileName = '教辅当月已结算-发货已结-到货';
$lujing = jcOptionDs($timetype,$yewugongsi); // 获取到文件存放地址，以及文件名拼接需要的时间类型（月份，季度，年度）
$path = __DIR__.'/uploadfile/'.$lujing['path'];
if(!file_exists($path)) {
	mkdir( iconv('UTF-8','GBK',$path), 0777, true );
}
$filepath = $path.$admin_username.'-'.iconv("UTF-8","gbk",$lujing['file_path']).'-'.iconv("UTF-8","gbk",$fileName).'.csv';  //$timetype 为一月份  一季度  1-6
if(!file_exists($filepath)){ 
$sql="Select cbny,ghdw,ghdwmc,YWBMBH,DH,MC,PFPCH,BZ,ZMY,ZSY,CBJ,TSLSH,SM,DJ,ZDQH,ZDXH,KFBH,JSLX,NF,NFHRQ,NJSRQ,JB,CWQRPC,YSDBRQ,ZCS,to_char(t_Month_fhjsb.DBRQ, 'YYYY-MM-DD HH24:MI:SS') as DBRQ,to_char(t_Month_fhjsb.PFRQ, 'YYYY-MM-DD HH24:MI:SS') as PFRQ ,to_char(t_Month_fhjsb.JSRQ, 'YYYY-MM-DD HH24:MI:SS') as JSRQ  From t_Month_fhjsb Where jsrq>=Date'$start' And jsrq < Date'$end' And ywbmbh in ($admin_username)";
$row=$ora->query($sql);
$total = count($row);
$title = array('CBNY','GHDW','GHDWMC','YWBMBH','DH','MC','PFPCH','BZ','DBRQ','PFRQ','ZMY','ZSY','CBJ','TSLSH','JSRQ','SM','DJ','ZDQH','ZDXH','KFBH','JSLX','NF','NFHRQ','NJSRQ','JB','CWQRPC','YSDBRQ','ZCS');
yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username,$timetype);
}
//16
$fileName = '教辅当月已结算-发货已结-退货';
$lujing = jcOptionDs($timetype,$yewugongsi); // 获取到文件存放地址，以及文件名拼接需要的时间类型（月份，季度，年度）
$path = __DIR__.'/uploadfile/'.$lujing['path'];
if(!file_exists($path)) {
	mkdir( iconv('UTF-8','GBK',$path), 0777, true );
}
$filepath = $path.$admin_username.'-'.iconv("UTF-8","gbk",$lujing['file_path']).'-'.iconv("UTF-8","gbk",$fileName).'.csv';  //$timetype 为一月份  一季度  1-6
if(!file_exists($filepath)){ 
$sql="Select * From t_Month_xtjsb Where jsrq>=Date'$start' And jsrq < Date'$end' And ywbmbh in ($admin_username)";
$row=$ora->query($sql);
$total = count($row);
$title = array('CBNY','GHDW','GHDWMC','YWBMBH','KFBH','DH','DM','PCH','THRQ','ZMY','ZSY','ZCB','TSLSH','JSRQ','JSLX','ZDQH','NF','NFHRQ','NJSRQ','JB','CWQRPC','ZCS','DJ','ID','BB','SM','ISBN','CBNY');
yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username,$timetype);
}
//17
$fileName = '教辅当月已结算-发货已结-更正';
$lujing = jcOptionDs($timetype,$yewugongsi); // 获取到文件存放地址，以及文件名拼接需要的时间类型（月份，季度，年度）
$path = __DIR__.'/uploadfile/'.$lujing['path'];
if(!file_exists($path)) {
	mkdir( iconv('UTF-8','GBK',$path), 0777, true );
}
$filepath = $path.$admin_username.'-'.iconv("UTF-8","gbk",$lujing['file_path']).'-'.iconv("UTF-8","gbk",$fileName).'.csv';  //$timetype 为一月份  一季度  1-6
if(!file_exists($filepath)){ 
$sql="Select * From t_Month_Fhgzjsb Where jsrq>=Date'$start' And jsrq < Date'$end' And ywbmbh in ($admin_username)";
$row=$ora->query($sql);
$total = count($row);
$title = array('CBNY','GHDW','GHDWMC','YWBMBH','KFBH','DH','DM','GZPCH','GZRQ','ZMY','ZSY','ZCB','TSLSH','JSRQ','BZ','JSLX','ZDQH','NF','NFHRQ','NJSRQ','JB','ZCS');
yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username,$timetype);
}
//18
//set_time_limit(0);
//ini_set('memory_limit', '-1');
//ini_set("max_execution_time", "0");
/*

$fileName = '教辅应付款-到货未结明细';
$lujing = jcOptionDs($timetype,$yewugongsi); // 获取到文件存放地址，以及文件名拼接需要的时间类型（月份，季度，年度）
$path = __DIR__.'/uploadfile/'.$lujing['path'];
if(!file_exists($path)) {
	mkdir( iconv('UTF-8','GBK',$path), 0777, true );
}
$filepath = $path.$admin_username.'-'.iconv("UTF-8","gbk",$lujing['file_path']).'-'.iconv("UTF-8","gbk",$fileName).'.csv';  //$timetype 为一月份  一季度  1-6
if(!file_exists($filepath)){ 
$sql="Select YWBMBH,GHDWH,MC,DHPCH,YSDJ,SSDH,ZDQH,ZDXH,SM,DJ,JXSSSL,MY,SYS,ZDPCH,JSRQ,JSLX,ZDPCH1,TSLSH,YSDBRQ,JCDRQ,KQBJ,JSGHDWH,YSGHDWH,FLOWID_DHMX,to_char(t_Month_Dhjsb.SHRQ, 'YYYY-MM-DD HH24:MI:SS') as SHRQ,to_char(t_Month_Dhjsb.YSRQ, 'YYYY-MM-DD HH24:MI:SS') as YSRQ  From t_Month_Dhjsb Where ywbmbh in ($admin_username) And jsrq Is Null";
$row=$ora->query($sql);
$total = count($row);
$title = array('YWBMBH','GHDWH','MC','DHPCH','SHRQ','YSDJ','SSDH','ZDQH','ZDXH','SM','DJ','JXSSSL','MY','SYS','ZDPCH','JSRQ','JSLX','ZDPCH1','TSLSH','YSRQ','YSDBRQ','JCDRQ','KQBJ','JSGHDWH','YSGHDWH','FLOWID_DHMX');
yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username,$timetype);
}
//exit;
//19
$fileName = '教辅应付款-进退未结明细';
$lujing = jcOptionDs($timetype,$yewugongsi); // 获取到文件存放地址，以及文件名拼接需要的时间类型（月份，季度，年度）
$path = __DIR__.'/uploadfile/'.$lujing['path'];
if(!file_exists($path)) {
	mkdir( iconv('UTF-8','GBK',$path), 0777, true );
}
$filepath = $path.$admin_username.'-'.iconv("UTF-8","gbk",$lujing['file_path']).'-'.iconv("UTF-8","gbk",$fileName).'.csv';  //$timetype 为一月份  一季度  1-6
if(!file_exists($filepath)){ 
$sql="Select YWBMBH,GHDWH,MC,ID,SM,DJ,ZMY,ZSY,BZ,YWPCH,ZDPCH,JSRQ,JSLX,ZDQH,ZDXH,ZDPCH1,TSLSH,NF,NFHRQ,NJSRQ,JSGHDWH,YSGHDWH,ZCS,to_char(t_Month_jtjsb.DBRQ, 'YYYY-MM-DD HH24:MI:SS') as DBRQ From t_Month_jtjsb Where ywbmbh in ($admin_username) And jsrq Is Null";
$row=$ora->query($sql);
$total = count($row);
$title = array('YWBMBH','GHDWH','MC','DBRQ','ID','SM','DJ','ZMY','ZSY','BZ','YWPCH','ZDPCH','JSRQ','JSLX','ZDQH','ZDXH','ZDPCH1','TSLSH','NF','NFHRQ','NJSRQ','JSGHDWH','YSGHDWH','ZCS');
yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username,$timetype);
}
//20
set_time_limit(0);
ini_set('memory_limit', '-1');
ini_set("max_execution_time", "0");

$fileName = '教辅应收款-发货未结明细';
$lujing = jcOptionDs($timetype,$yewugongsi); // 获取到文件存放地址，以及文件名拼接需要的时间类型（月份，季度，年度）
$path = __DIR__.'/uploadfile/'.$lujing['path'];
if(!file_exists($path)) {
	mkdir( iconv('UTF-8','GBK',$path), 0777, true );
}
$filepath = $path.$admin_username.'-'.iconv("UTF-8","gbk",$lujing['file_path']).'-'.iconv("UTF-8","gbk",$fileName).'.csv';  //$timetype 为一月份  一季度  1-6
if(!file_exists($filepath)){ 
$sql="Select cbny,ghdw,ghdwmc,YWBMBH,DH,MC,PFPCH,BZ,ZMY,ZSY,CBJ,TSLSH,JSRQ,SM,DJ,ZDQH,ZDXH,KFBH,JSLX,NF,NFHRQ,NJSRQ,JB,CWQRPC,YSDBRQ,ZCS,to_char(t_Month_fhjsb.DBRQ, 'YYYY-MM-DD HH24:MI:SS') as DBRQ,to_char(t_Month_fhjsb.PFRQ, 'YYYY-MM-DD HH24:MI:SS') as PFRQ From t_Month_fhjsb Where ywbmbh in ($admin_username) And jsrq Is Null";
$row=$ora->query($sql);
$total = count($row);
$title = array('CBNY','GHDW','GHDWMC','YWBMBH','DH','MC','PFPCH','BZ','DBRQ','PFRQ','ZMY','ZSY','CBJ','TSLSH','JSRQ','SM','DJ','ZDQH','ZDXH','KFBH','JSLX','NF','NFHRQ','NJSRQ','JB','CWQRPC','YSDBRQ','ZCS');
yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username,$timetype);
}
//21
$fileName = '教辅应收款-销退未结明细';
$lujing = jcOptionDs($timetype,$yewugongsi); // 获取到文件存放地址，以及文件名拼接需要的时间类型（月份，季度，年度）
$path = __DIR__.'/uploadfile/'.$lujing['path'];
if(!file_exists($path)) {
	mkdir( iconv('UTF-8','GBK',$path), 0777, true );
}
$filepath = $path.$admin_username.'-'.iconv("UTF-8","gbk",$lujing['file_path']).'-'.iconv("UTF-8","gbk",$fileName).'.csv';  //$timetype 为一月份  一季度  1-6
if(!file_exists($filepath)){ 
$sql="Select cbny,ghdw,ghdwmc,YWBMBH,KFBH,DH,DM,PCH,ZMY,ZSY,ZCB,TSLSH,JSRQ,JSLX,ZDQH,NF,NFHRQ,NJSRQ,JB,CWQRPC,ZCS,DJ,to_char(t_Month_Xtjsb.THRQ, 'YYYY-MM-DD HH24:MI:SS') as THRQ From t_Month_Xtjsb Where ywbmbh in ($admin_username) And jsrq Is Null";
$row=$ora->query($sql);
$total = count($row);
$title = array('CBNY','GHDW','GHDWMC','YWBMBH','KFBH','DH','DM','PCH','THRQ','ZMY','ZSY','ZCB','TSLSH','JSRQ','JSLX','ZDQH','NF','NFHRQ','NJSRQ','JB','CWQRPC','ZCS','DJ');
yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username,$timetype);
}
//22
$fileName = '教辅应收款-更正未结明细';
$lujing = jcOptionDs($timetype,$yewugongsi); // 获取到文件存放地址，以及文件名拼接需要的时间类型（月份，季度，年度）
$path = __DIR__.'/uploadfile/'.$lujing['path'];
if(!file_exists($path)) {
	mkdir( iconv('UTF-8','GBK',$path), 0777, true );
}
$filepath = $path.$admin_username.'-'.iconv("UTF-8","gbk",$lujing['file_path']).'-'.iconv("UTF-8","gbk",$fileName).'.csv';  //$timetype 为一月份  一季度  1-6
if(!file_exists($filepath)){ 
$sql="Select YWBMBH,KFBH,DH,DM,GZPCH,ZMY,ZSY,ZCB,TSLSH,JSRQ,BZ,JSLX,ZDQH,NF,NFHRQ,NJSRQ,JB,ZCS,to_char(t_Month_fhgzjsb.GZRQ, 'YYYY-MM-DD HH24:MI:SS') as GZRQ From t_Month_fhgzjsb Where ywbmbh in ($admin_username) And jsrq Is Null";
$row=$ora->query($sql);
$total = count($row);
$title = array('YWBMBH','KFBH','DH','DM','GZPCH','GZRQ','ZMY','ZSY','ZCB','TSLSH','JSRQ','BZ','JSLX','ZDQH','NF','NFHRQ','NJSRQ','JB','ZCS');
yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username,$timetype);
}

exit;
*/


exit;

$yewugongsi = 3;//连锁公司
$ip='172.30.153.63/xhsddb';
$port='1521';
$user= 'dbsl';
$pass= 'dbsl';
$charset='utf8';
$ora=new oracle_admin($user,$pass,$ip,$charset);

$users = array('000001','000002','000003','000004','000005','000006','000007','000008','000009','000010','000011','000012','000013','000014');



for($i=0;$i<count($users);$i++){

	$fileName = '差异-更正差异';
	$lujing = jcOptionDs($timetype,$yewugongsi); // 获取到文件存放地址，以及文件名拼接需要的时间类型（月份，季度，年度）
	$path = __DIR__.'/uploadfile/'.$lujing['path'];
	if(!file_exists($path)) {
		mkdir( iconv('UTF-8','GBK',$path), 0777, true );
	}
	$filepath = $path.$users[$i].'-'.iconv("UTF-8","gbk",$lujing['file_path']).'-'.iconv("UTF-8","gbk",$fileName).'.csv';  //$timetype 为一月份  一季度  1-6
	//echo $filepath;exit;
	if(!file_exists($filepath)){ 
		$sql="select 0 As zmy,Sum(cbj) As zsy,ywbmbh from t_xsls_ghdw_gz Where xsrq >= Date '$start' and xslx='2' And xsrq < Date '$end' AND YWBMBH = '$users[$i]'
	 Group By ywbmbh";
		$row=$ora->query($sql);
		$total = count($row);
		$title = array('ZMY','ZSY','YWBMBH');
		yuebao($sql,$fileName,$title,$total,$yewugongsi,$users[$i],$timetype);
	}

}


for($i=0;$i<count($users);$i++){

	$fileName = '差异-到货';
	$lujing = jcOptionDs($timetype,$yewugongsi); // 获取到文件存放地址，以及文件名拼接需要的时间类型（月份，季度，年度）
	$path = __DIR__.'/uploadfile/'.$lujing['path'];
	if(!file_exists($path)) {
		mkdir( iconv('UTF-8','GBK',$path), 0777, true );
	}
	$filepath = $path.$users[$i].'-'.iconv("UTF-8","gbk",$lujing['file_path']).'-'.iconv("UTF-8","gbk",$fileName).'.csv';  //$timetype 为一月份  一季度  1-6
	if(!file_exists($filepath)){ 
		$sql="Select 'dhyh' As lx,0 As zmy,Sum(zsy) As zsy,ywbmbh From t_Jjyhdkhz Where djrq>=Date'$start' And djrq< Date'$end' AND YWBMBH = '$users[$i]' Group By ywbmbh
	Union All Select 'dhbl' As lx,Sum(zmy) As zmy,Sum(zsy) As zsy,ywbmbh From t_bldhdj Where dbrq>=Date'$start' And dbrq< Date'$end' AND YWBMBH = '$users[$i]' Group By ywbmbh";
		$row=$ora->query($sql);
		$total = count($row);
		$title = array('LX','ZMY','ZSY','YWBMBH');
		yuebao($sql,$fileName,$title,$total,$yewugongsi,$users[$i],$timetype);
	}

}
for($i=0;$i<count($users);$i++){

	$fileName = '差异-发货';
	$lujing = jcOptionDs($timetype,$yewugongsi); // 获取到文件存放地址，以及文件名拼接需要的时间类型（月份，季度，年度）
	$path = __DIR__.'/uploadfile/'.$lujing['path'];
	if(!file_exists($path)) {
		mkdir( iconv('UTF-8','GBK',$path), 0777, true );
	}
	$filepath = $path.$users[$i].'-'.iconv("UTF-8","gbk",$lujing['file_path']).'-'.iconv("UTF-8","gbk",$fileName).'.csv';  //$timetype 为一月份  一季度  1-6
	if(!file_exists($filepath)){ 
	$sql="Select 'fhhz1' As lx,Sum(zmy),Sum(cbj),ywbmbh From t_fhhz1 a Where  Not Exists (Select 1 From t_fhhz Where a.pfpch = pfpch) And dbrq >= Date'$start' And dbrq < Date'$end' AND YWBMBH = '$users[$i]'
 Group By ywbmbh
Union All
Select 'fhhz2' As lx,Sum(zmy),Sum(cbj),ywbmbh From t_fhhz2 a Where  Not Exists (Select 1 From t_fhhz Where a.pfpch = pfpch) And dbrq >= Date'$start' And dbrq < Date'$end' AND YWBMBH = '$users[$i]'
 Group By ywbmbh
Union All
Select  'khthhz1' As lx,Sum(zmy),Sum(cbj),ywbmbh From t_khthhz1 a Where  Not Exists (Select * From T_KHTHHZ Where THPCH = a.THPCH)And djlx <>'YH'  And thrq >= Date'$start' And thrq < Date'$end' AND YWBMBH = '$users[$i]'
 Group By ywbmbh
Union All
Select  'khthhz2' As lx,Sum(zmy),Sum(cbj),ywbmbh From t_khthhz2 a Where  Not Exists (Select * From T_KHTHHZ Where THPCH = a.THPCH) And thrq >= Date'$start' And thrq < Date'$end' AND YWBMBH = '$users[$i]'
 Group By ywbmbh";
		$row=$ora->query($sql);
		$total = count($row);
		$title = array('LX','SUM(ZMY)','SUM(CBJ)','YWBMBH');
		yuebao($sql,$fileName,$title,$total,$yewugongsi,$users[$i],$timetype);
	}

}
for($i=0;$i<count($users);$i++){
	set_time_limit(0);
	ini_set('memory_limit', '-1');
	ini_set("max_execution_time", "0");

	$fileName = '库存明细';
	$lujing = jcOptionDs($timetype,$yewugongsi); // 获取到文件存放地址，以及文件名拼接需要的时间类型（月份，季度，年度）
	$path = __DIR__.'/uploadfile/'.$lujing['path'];
	//$start_lskc = kc_month(); //日期格式必须为 201808
	
	$start_lskc = '201809'; //日期格式必须为 201808
	if(!file_exists($path)) {
		mkdir( iconv('UTF-8','GBK',$path), 0777, true );
	}
	$filepath = $path.$users[$i].'-'.iconv("UTF-8","gbk",$lujing['file_path']).'-'.iconv("UTF-8","gbk",$fileName).'.csv';  //$timetype 为一月份  一季度  1-6
	if(!file_exists($filepath)){ 
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
												 And ywbmbh = '$users[$i]') As 供货单位,
										(Select mc
												From t_ghdw
											 Where bh = (Select ghdwh
																		 From t_kcsm_ywbm
																		Where Id = t.id
																			And ywbmbh = '$users[$i]')) As 供货单位名称,
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
											 Where ywbmbh = '$users[$i]'
												 And tjny = '$start_lskc'
											 Group By Id, ywbmbh) t";
		$row=$ora->query($sql);
		$total = count($row);
		//echo $total;exit;
		$title = array('业务部门编号','ID','ISBN','书名','版本','版本名称','册数','码洋','实洋','不含税实洋','定价','供货单位','供货单位名称','出版年月','印刷年月','版次','分类','分类编号','类别编号','类别');
		yuebao($sql,$fileName,$title,$total,$yewugongsi,$users[$i],$timetype);
	}

}

for($i=0;$i<count($users);$i++){
	set_time_limit(0);
	ini_set('memory_limit', '-1');
	ini_set("max_execution_time", "0");
	$fileName = '应付款-采购未结-明细';
	$lujing = jcOptionDs($timetype,$yewugongsi); // 获取到文件存放地址，以及文件名拼接需要的时间类型（月份，季度，年度）
	$path = __DIR__.'/uploadfile/'.$lujing['path'];
	if(!file_exists($path)) {
		mkdir( iconv('UTF-8','GBK',$path), 0777, true );
	}
	$filepath = $path.$users[$i].'-'.iconv("UTF-8","gbk",$lujing['file_path']).'-'.iconv("UTF-8","gbk",$fileName).'.csv';  //$timetype 为一月份  一季度  1-6
	if(!file_exists($filepath)){ 
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
           And a.ywbmbh Like '$users[$i]'
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
           And a.ywbmbh Like '$users[$i]'
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
           And a.ywbmbh Like '$users[$i]'
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
           And a.ywbmbh Like '$users[$i]'
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
           And a.ywbmbh Like '$users[$i]'
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
           And a.ywbmbh Like '$users[$i]'
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
           And a.ywbmbh Like '$users[$i]'
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
           And a.ywbmbh Like '$users[$i]'
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
           And a.ywbmbh Like '$users[$i]'
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
           And a.ywbmbh Like '$users[$i]'
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
           And a.ywbmbh Like '$users[$i]'
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
           And a.ywbmbh Like '$users[$i]'
           And a.djrq < Date '$end'";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);
	
		$sql="Select ysdj, dm, zpz, zcs, zmy, zsy, (zsy / (1 + (a.sl1 * 0.01))) As bhszsy,ywbmmc, bz, djlx,to_char(a.ysrq, 'YYYY-MM-DD HH24:MI:SS') as ysrq, sl1,to_char(a.dbrq, 'YYYY-MM-DD HH24:MI:SS') as dbrq
	 From t_Yfzk_Sjdj a ";
		$row=$ora->query($sql);
		$total = count($row);
		$title = array('DBRQ','YSDJ','YSRQ','DM','ZPZ','ZCS','ZMY','ZSY','BHSZSY','YWBMMC','BZ','DJLX','SL1');
		yuebao($sql,$fileName,$title,$total,$yewugongsi,$users[$i],$timetype);
	}

}

for($i=0;$i<count($users);$i++){

	$fileName = '应收款-发出未结-明细';
	$lujing = jcOptionDs($timetype,$yewugongsi); // 获取到文件存放地址，以及文件名拼接需要的时间类型（月份，季度，年度）
	$path = __DIR__.'/uploadfile/'.$lujing['path'];
	if(!file_exists($path)) {
		mkdir( iconv('UTF-8','GBK',$path), 0777, true );
	}
	$filepath = $path.$users[$i].'-'.iconv("UTF-8","gbk",$lujing['file_path']).'-'.iconv("UTF-8","gbk",$fileName).'.csv';  //$timetype 为一月份  一季度  1-6
	if(!file_exists($filepath)){ 
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
           And a.ywbmbh Like '$users[$i]'";
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
           And a.ywbmbh Like '$users[$i]'";
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
         Where a.ywbmbh Like '$users[$i]'
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
           And ywbmbh Like '$users[$i]'";
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
           And a.ywbmbh Like '$users[$i]'";
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
						$ora->query($sql_gc);

						$sql_gc = "Update t_yszk_sjdj a
       Set lb = (Select lb
                    From T_FHHZ1
                   Where pfpch = a.pfpch
                     And rownum = 1)
     Where lb Is Null
       And czybh = 1139";
						$ora->query($sql_gc);
					if($users[$i] == '000004'){
						$sql_gc = "Insert Into t_Yszk_Sjdj_Cy
            (dm, jsdm, ywbmmc, ywbmbh, pfpch, Id, dbrq, Sys, cbj, mxlb, djlb)
            Select a.dm, a.jsdm, a.ywbmmc, a.ywbmbh, a.pfpch, b.id, a.dbrq, b.sys, b.cbj,
                   c.lb, a.lb
              From t_Yszk_Sjdj a, t_fhmx b, t_kcsm c
             Where a.pfpch = b.pfpch
               And c.id = b.id
               And c.lb <> a.lb";
						$ora->query($sql_gc);
						$sql_gc = "Update t_Yszk_Sjdj_Cy t
           Set cy = ((cbj / (1 + (Select sl1 / 100 From t_lb Where bh = t.mxlb))) -
                     (cbj / (1 + (Select sl1 / 100 From t_lb Where bh = t.djlb))))";
						$ora->query($sql_gc);
						
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
 Where ywbmbh = '$users[$i]'";
		$row=$ora->query($sql);
		$total = count($row);
		$title = array('DBRQ','PFPCH','DM','JSDM','ZPZ','ZCS','ZMY','ZSY','CBJ','BHSZSY','BHSCBJ','YWBMMC','YWBMBH','BZ','DJLX','SL1','DQ');
		yuebao($sql,$fileName,$title,$total,$yewugongsi,$users[$i],$timetype);
	}

}

for($i=0;$i<count($users);$i++){
	set_time_limit(0);
	ini_set('memory_limit', '-1');
	ini_set("max_execution_time", "0");
	$fileName = '应付款月报';
	$lujing = jcOptionDs($timetype,$yewugongsi); // 获取到文件存放地址，以及文件名拼接需要的时间类型（月份，季度，年度）
	$path = __DIR__.'/uploadfile/'.$lujing['path'];
	if(!file_exists($path)) {
		mkdir( iconv('UTF-8','GBK',$path), 0777, true );
	}
	$filepath = $path.$users[$i].'-'.iconv("UTF-8","gbk",$lujing['file_path']).'-'.iconv("UTF-8","gbk",$fileName).'.csv';  //$timetype 为一月份  一季度  1-6
	if(!file_exists($filepath)){ 
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
						$ora->query($sql_gc);
						
						$sql_gc = "Insert Into t_yFzk_month_tmp_clj
        (BQZJmy, BQZJsy, ghdwh, ywbmbh, YWLX, QCRQ, QMRQ)
        Select Sum(zmy), Sum(zsy), ghdwh, ywbmbh, 'BL',  '',''
          From t_bldhdj t
         Where dbrq < Date '$end'
           And dbrq >= Date '$start'
         Group By ywbmbh, ghdwh";
						$ora->query($sql_gc);

						$sql_gc = "Insert Into t_yFzk_month_tmp_clj
        (BQZJmy, BQZJsy, ghdwh, ywbmbh, YWLX, QCRQ, QMRQ)
        Select Sum(zmy), Sum(zsy), ghdwh, ywbmbh, 'DH', '',''
          From t_dhdj_cf t
         Where dbrq < Date '$end'
           And dbrq >= Date '$start'
         Group By ywbmbh, ghdwh";
						$ora->query($sql_gc);
						$sql_gc = "Insert Into t_yFzk_month_tmp_clj
        (BQZJmy, BQZJsy, ghdwh, ywbmbh, YWLX, QCRQ, QMRQ)
        Select Sum(-zmy), Sum(-zsy), hybh, ywbmbh, 'TH',  '',''
          From t_hythhz t
         Where thrq < Date '$end'
           And thrq >= Date '$start'
         Group By ywbmbh, hybh";
						$ora->query($sql_gc);
						$sql_gc = "Insert Into t_yfzk_month_tmp_clj
        (bqzjmy, bqzjsy, ghdwh, ywbmbh, YWLX, QCRQ, QMRQ)
        Select 0, Sum(-zsy), ghdwh, ywbmbh, 'YH', '',''
          From t_Jjyhdkhz t
         Where djrq >= Date '$start'
           And djrq < Date '$end'
         Group By ywbmbh, ghdwh";
						$ora->query($sql_gc);

						$sql_gc = "Insert Into t_yFzk_month_rysj_clj
        (YWBMBH, GHDWH, QCMY, QCSY, BQZJMY, BQZJSY, BQJSMY, BQJSSY, QMMY, QMSY, QCRQ,
         QMRQ)
        Select YWBMBH, GHDWH, Sum(QCMY), Sum(QCSY), Sum(BQZJMY), Sum(BQZJSY), Sum(BQJSMY),
               Sum(BQJSSY), Sum(QMMY), Sum(QMSY), Date '$start', Date '$end'
          From t_yFzk_month_tmp_clj
         Where ghdwh <> 'L00099'
         Group By GHDWH, YWBMBH";
						$ora->query($sql_gc);
	
	
		$sql = "Select ghdwh,(Select mc From t_ghdw Where bh = a.ghdwh) As ghdwhmc,(Select dm From t_dm Where dh =a.ywbmbh) As  ywbmbh,a.qcmy,a.qcsy,a.bqzjmy,a.bqzjsy,a.bqjsmy,a.bqjssy,a.qmmy,a.qmsy,to_char(a.QMRQ, 'YYYY-MM-DD') as QMRQ,to_char(a.QCRQ, 'YYYY-MM-DD') as QCRQ	
 From t_yfzk_month_rysj_clj a where ywbmbh = '$users[$i]'";
		$row=$ora->query($sql);
		$total = count($row);
		$title = array('统计日期','统计年月','供货单位','业务部门','期初码洋','期初实洋','本期增加码洋','本期增加实洋','本期减少码洋','本期减少实洋','期末码洋','期末实洋');
		yuebao($sql,$fileName,$title,$total,$yewugongsi,$users[$i],$timetype);
	}

}

for($i=0;$i<count($users);$i++){
	set_time_limit(0);
	ini_set('memory_limit', '-1');
	ini_set("max_execution_time", "0");
	$fileName = '应付款月报含税率';
	$lujing = jcOptionDs($timetype,$yewugongsi); // 获取到文件存放地址，以及文件名拼接需要的时间类型（月份，季度，年度）
	$path = __DIR__.'/uploadfile/'.$lujing['path'];
	if(!file_exists($path)) {
		mkdir( iconv('UTF-8','GBK',$path), 0777, true );
	}
	$filepath = $path.$users[$i].'-'.iconv("UTF-8","gbk",$lujing['file_path']).'-'.iconv("UTF-8","gbk",$fileName).'.csv';  //$timetype 为一月份  一季度  1-6
	if(!file_exists($filepath)){ 
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
	
		$sql = "Select (Select dm From t_dm Where dh =a.ywbmbh) As YWBMBH,a.ghdwh,(Select mc From t_ghdw Where bh = a.ghdwh) As ghdwhmc,a.SL,a.QCMY,a.QCSY,a.BQZJMY,a.BQZJSY,a.BQJSMY,a.BQJSSY,a.QMMY,a.QMSY,to_char(a.QMRQ, 'YYYY-MM-DD') as QMRQ,to_char(a.QCRQ, 'YYYY-MM-DD') as QCRQ
		From t_yfzk_month_rysj_clj_SL a where ywbmbh = '$users[$i]'";
		$row=$ora->query($sql);
		$total = count($row);
		$title = array('统计日前','统计年月','业务部门','供货单位','税率%','期初码洋','期初实洋','本期增加码洋','本期增加实洋','本期减少码洋','本期减少实洋','期末码洋','期末实洋');
		yuebao($sql,$fileName,$title,$total,$yewugongsi,$users[$i],$timetype);
	}

}


for($i=0;$i<count($users);$i++){
	set_time_limit(0);
	ini_set('memory_limit', '-1');
	ini_set("max_execution_time", "0");
	$fileName = '应收款月报';
	$lujing = jcOptionDs($timetype,$yewugongsi); // 获取到文件存放地址，以及文件名拼接需要的时间类型（月份，季度，年度）
	$path = __DIR__.'/uploadfile/'.$lujing['path'];
	if(!file_exists($path)) {
		mkdir( iconv('UTF-8','GBK',$path), 0777, true );
	}
	$filepath = $path.$users[$i].'-'.iconv("UTF-8","gbk",$lujing['file_path']).'-'.iconv("UTF-8","gbk",$fileName).'.csv';  //$timetype 为一月份  一季度  1-6
	if(!file_exists($filepath)){ 
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
						$ora->query($sql_gc);
						$sql_gc = "Insert Into t_yszk_month_tmp_clj
        (qcmy, qcsy, qccbj, dh, ywbmbh, YWLX, QCRQ, QMRQ)
        Select Sum(zmy), Sum(zsy), Sum(NVL(cbj, 0)), DH, ywbmbh, 'HZ1', '',''
          From t_fhhz1 t
         Where dbrq < Date '$start'
         Group By ywbmbh, DH";
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
						$ora->query($sql_gc);
						
						$sql_gc = "Insert Into t_ySzk_month_tmp_clj
        (BQZJmy, BQZJsy, BQZJcbj, DH, ywbmbh, YWLX, QCRQ, QMRQ)
        Select Sum(zmy), Sum(zsy), Sum(NVL(cbj, 0)), DH, ywbmbh, 'FH', '',''
          From T_FHHZ t
         Where DBRQ < Date '$end'
           And DBRQ >= Date '$start'
         Group By ywbmbh, DH";
						$ora->query($sql_gc);

						$sql_gc = "Insert Into t_ySzk_month_tmp_clj
        (BQZJmy, BQZJsy, BQZJcbj, DH, ywbmbh, YWLX, QCRQ, QMRQ)
        Select Sum(zmy), Sum(zsy), Sum(NVL(cbj, 0)), DH, ywbmbh, 'FH1', '',''
          From T_FHHZ1 t
         Where DBRQ < Date '$end'
           And DBRQ >= Date '$start'
           And Not Exists (Select 1 From t_fhhz Where pfpch = t.pfpch)
         Group By ywbmbh, DH";
						$ora->query($sql_gc);
						$sql_gc = "Insert Into t_ySzk_month_tmp_clj
        (BQZJmy, BQZJsy, BQZJcbj, DH, ywbmbh, YWLX, QCRQ, QMRQ)
        Select Sum(zmy), Sum(zsy), Sum(NVL(cbj, 0)), DH, ywbmbh, 'FH2', '',''
          From T_FHHZ2 t
         Where DBRQ < Date '$end'
           And DBRQ >= Date '$start'
           And Not Exists (Select 1 From t_fhhz Where pfpch = t.pfpch)
         Group By ywbmbh, DH";
						$ora->query($sql_gc);
						$sql_gc = "Insert Into t_ySzk_month_tmp_clj
        (BQZJmy, BQZJsy, BQZJcbj, DH, ywbmbh, YWLX, QCRQ, QMRQ)
        Select Sum(-zmy),
               Sum(-zsy), Sum(NVL(-cbj, 0)), DH, ywbmbh, 'TH', '',''
          From T_KHTHHZ t
         Where THRQ < Date '$end'
           And THRQ >= Date '$start'
         Group By ywbmbh, DH";
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
						$ora->query($sql_gc);
	
	
	$sql = "Select dh,(Select dm From t_dm Where dh =a.ywbmbh) As ywbmbh,a.qcmy,a.qcsy,a.qccbj,a.bqzjmy,a.bqzjsy,a.bqzjcbj,a.bqjsmy,a.bqjssy,a.bqjscbj,a.qmmy,a.qmsy,a.qmcbj,to_char(a.qcrq, 'YYYY-MM-DD') as qcrq
			,to_char(a.qmrq, 'YYYY-MM-DD') as qmrq From t_yszk_month_rysj_clj a where ywbmbh = '$users[$i]'";
	$row=$ora->query($sql);
	$total = count($row);
	$title = array('统计日期','统计年月','业务部门','客户名称','期初码洋','期初实洋','期初成本','本期增加码洋','本期增加实洋','本期增加成本','本期减少码洋','本期减少实洋','本期减少成本','期末码洋','期末实洋','期末成本');
	yuebao($sql,$fileName,$title,$total,$yewugongsi,$users[$i],$timetype);
	}

}

for($i=0;$i<count($users);$i++){
	set_time_limit(0);
	ini_set('memory_limit', '-1');
	ini_set("max_execution_time", "0");
	$fileName = '应收款月报含税率';
	$lujing = jcOptionDs($timetype,$yewugongsi); // 获取到文件存放地址，以及文件名拼接需要的时间类型（月份，季度，年度）
	$path = __DIR__.'/uploadfile/'.$lujing['path'];
	if(!file_exists($path)) {
		mkdir( iconv('UTF-8','GBK',$path), 0777, true );
	}
	$filepath = $path.$users[$i].'-'.iconv("UTF-8","gbk",$lujing['file_path']).'-'.iconv("UTF-8","gbk",$fileName).'.csv';  //$timetype 为一月份  一季度  1-6
	if(!file_exists($filepath)){ 
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
						$ora->query($sql_gc);
						$sql_gc = "Insert Into t_yszk_month_tmp_clj
        (qcmy, qcsy, qccbj, dh, ywbmbh, YWLX, QCRQ, QMRQ, SL)
        Select Sum(zmy), Sum(zsy), Sum(NVL(cbj, 0)), DH, ywbmbh, 'HZ1', '','', SL
          From t_fhhz1 t
         Where dbrq < Date '$start'
         Group By ywbmbh, DH, SL";
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
						$ora->query($sql_gc);
						
						$sql_gc = "Insert Into t_ySzk_month_tmp_clj
        (BQZJmy, BQZJsy, BQZJcbj, DH, ywbmbh, YWLX, QCRQ, QMRQ, SL)
        Select Sum(zmy), Sum(zsy), Sum(NVL(cbj, 0)), DH, ywbmbh, 'FH', '','', SL
          From T_FHHZ t
         Where DBRQ < Date '$end'
           And DBRQ >= Date '$start' And (nvl(zcs,0)<>0 Or nvl(zmy,0)<>0 Or nvl(zsy,0)<>0 Or nvl(cbj,0)<>0)
         Group By ywbmbh, DH, SL";
						$ora->query($sql_gc);

						$sql_gc = "Insert Into t_ySzk_month_tmp_clj
        (BQZJmy, BQZJsy, BQZJcbj, DH, ywbmbh, YWLX, QCRQ, QMRQ, SL)
        Select Sum(zmy), Sum(zsy), Sum(NVL(cbj, 0)), DH, ywbmbh, 'FH1', '','', SL
          From T_FHHZ1 t
         Where DBRQ < Date '$end'
           And DBRQ >= Date '$start'
           And Not Exists (Select 1 From t_fhhz Where pfpch = t.pfpch)
         Group By ywbmbh, DH, SL";
						$ora->query($sql_gc);
						$sql_gc = "Insert Into t_ySzk_month_tmp_clj
        (BQZJmy, BQZJsy, BQZJcbj, DH, ywbmbh, YWLX, QCRQ, QMRQ, SL)
        Select Sum(zmy), Sum(zsy), Sum(NVL(cbj, 0)), DH, ywbmbh, 'FH2', '','', SL
          From T_FHHZ2 t
         Where DBRQ < Date '$end'
           And DBRQ >= Date '$start'
           And Not Exists (Select 1 From t_fhhz Where pfpch = t.pfpch)
         Group By ywbmbh, DH, SL";
						$ora->query($sql_gc);
						$sql_gc = "Insert Into t_ySzk_month_tmp_clj
        (BQZJmy, BQZJsy, BQZJcbj, DH, ywbmbh, YWLX, QCRQ, QMRQ, SL)
        Select Sum(-zmy),
               Sum(-zsy), Sum(NVL(-cbj, 0)), DH, ywbmbh, 'TH', '','', SL
          From T_KHTHHZ t
         Where THRQ < Date '$end'
           And THRQ >= Date '$start'
         Group By ywbmbh, DH, SL";
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
						$ora->query($sql_gc);

	$sql = "Select dh, (Select dm From t_dm Where dh =t_yszk_month_rysj_clj_SL.ywbmbh) As ywbmbh, sl,qcmy, qcsy,qccbj, bqzjmy, bqzjsy, bqzjcbj, bqjsmy, bqjssy, qmmy, qmsy,qmcbj,
			 dqmy, dqsy, dqcbj, to_char(t_yszk_month_rysj_clj_SL.qcrq, 'YYYY-MM-DD') as qcrq
			,to_char(t_yszk_month_rysj_clj_SL.qmrq, 'YYYY-MM-DD') as qmrq From t_yszk_month_rysj_clj_SL where ywbmbh = '$users[$i]'";
	$row=$ora->query($sql);
	$total = count($row);
	$title = array('统计日期','统计年月','客户名称','业务部门','税率%','期初码洋','期初实洋','期初成本','本期增加码洋','本期增加实洋','本期增加成本','本期减少码洋',
	'本期减少实洋','期末码洋','期末实洋','期末成本','当前码洋','当前实洋','当前成本');
	yuebao($sql,$fileName,$title,$total,$yewugongsi,$users[$i],$timetype);
	}

}


for($i=0;$i<count($users);$i++){

	$fileName = '本期流水';
	$lujing = jcOptionDs($timetype,$yewugongsi); // 获取到文件存放地址，以及文件名拼接需要的时间类型（月份，季度，年度）
	$path = __DIR__.'/uploadfile/'.$lujing['path'];
	if(!file_exists($path)) {
		mkdir( iconv('UTF-8','GBK',$path), 0777, true );
	}
	$filepath = $path.$users[$i].'-'.iconv("UTF-8","gbk",$lujing['file_path']).'-'.iconv("UTF-8","gbk",$fileName).'.csv';  //$timetype 为一月份  一季度  1-6
	if(!file_exists($filepath)){ 
	$sql="Select Sum(pkmy) 损益码洋, Sum(pksy)损益实样, Sum(bfmy) 报废码洋, Sum(bfsy) 报废实样, Sum(dhmy - thmy) 纯到货码洋, Sum(dhsy - thsy)纯到货实样,
		   Sum(fhmy - ftmy)纯发货码洋, Sum(fhcbj - thcbj)纯发货实洋
	  From t_Tscw_Pzjxc_day
	 Where ywbmbh = '$users[$i]'
		And rq >= Date '$start'
	   And rq < Date '$end'"; 
	$row=$ora->query($sql);
	$total = count($row);
	$title = array('损益码洋','损益实样','报废码洋','报废实样','纯到货码洋','纯到货实样','纯发货码洋','纯发货实洋');
	yuebao($sql,$fileName,$title,$total,$yewugongsi,$users[$i],$timetype);
	}

}
for($i=0;$i<count($users);$i++){

	$fileName = '汇总数据';
	$lujing = jcOptionDs($timetype,$yewugongsi); // 获取到文件存放地址，以及文件名拼接需要的时间类型（月份，季度，年度）
	$path = __DIR__.'/uploadfile/'.$lujing['path'];
	if(!file_exists($path)) {
		mkdir( iconv('UTF-8','GBK',$path), 0777, true );
	}
	$filepath = $path.$users[$i].'-'.iconv("UTF-8","gbk",$lujing['file_path']).'-'.iconv("UTF-8","gbk",$fileName).'.csv';  //$timetype 为一月份  一季度  1-6
	if(!file_exists($filepath)){ 
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
						$res = $ora->query($sql_gc);
						$sql_gc = "Insert Into t_month_kchd
				(qcrq, qmrq, ywbmbh, zmy, zsy, cbj, ywlx, BJ)
				Select Date '$start' As qcrq, Date '$end' As qmrq, ywbmbh, -sum(ZMY) As zmy,
							 -Sum(zsy) As zsy, -Sum(NVL(ZSY, 0)) As cbj, '退货' As ywlx， '1'
					From t_hythhz t
				 Where thrq >= Date '$start'
					 And thrq < Date '$end'
				 Group By ywbmbh";
						$ora->query($sql_gc);
						$sql_gc = "Insert Into t_month_kchd
				(qcrq, qmrq, ywbmbh, zmy, zsy, cbj, ywlx, BJ)
				Select Date '$start' As qcrq, Date '$end' As qmrq, ywbmbh, Sum(ZMY) As zmy,
							 Sum(zsy) As zsy, Sum(NVL(cbj, 0)) As cbj, '销退' As ywlx， '1'
					From T_KHTHHZ t
				 Where thrq >= Date '$start'
					 And thrq < Date '$end'
				 Group By ywbmbh";
						$ora->query($sql_gc);
						$sql_gc = "Insert Into t_month_kchd
				(qcrq, qmrq, ywbmbh, zmy, zsy, cbj, ywlx，BJ)
				Select Date '$start' As qcrq, Date '$end' As qmrq, ywbmbh, Sum(QCMY) As zmy,
							 Sum(QCSY) As zsy, Sum(NVL(QCSY, 0)) As cbj, '初期' As ywlx， '1'
				
					From T_TSCW_PZJXC_MONTH
				 Where TJNY = TO_CHAR(Date '$start', 'YYYYMM')
				 Group By ywbmbh";
						$ora->query($sql_gc);
						$sql_gc = "Insert Into t_month_kchd
				(qcrq, qmrq, ywbmbh, zmy, zsy, cbj, ywlx，BJ)
				Select Date '$start' As qcrq, Date '$end' As qmrq, ywbmbh, -Sum(QmMY) As zmy,
							 -Sum(QmSY) As zsy, -Sum(NVL(QmSY, 0)) As cbj, '期末' As ywlx， '1'
					From T_TSCW_PZJXC_MONTH
				 Where TJNY = TO_CHAR(Date '$end' - 2, 'YYYYMM')
				
				 Group By ywbmbh";
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
						$ora->query($sql_gc);
						
						$sql_gc = "Insert Into t_month_kchd
				(qcrq, qmrq, ywbmbh, zmy, zsy, cbj, ywlx, BJ)
				Select Date '$start' As qcrq, Date '$end' As qmrq, ywbmbh, 0, Sum(-zsy), Sum(-zsy),
							 '到货优惠', '0'
					From t_Jjyhdkhz t
				 Where djrq >= Date '$start'
					 And djrq < Date '$end'
				 Group By ywbmbh";
						$ora->query($sql_gc);

						$sql_gc = "Insert Into t_month_kchd
				(qcrq, qmrq, ywbmbh, zmy, zsy, cbj, ywlx, BJ)
				Select Date '$start' As qcrq, Date '$end' As qmrq, ywbmbh, Sum(ZMY) As zmy,
							 Sum(zsy) As zsy, Sum(NVL(ZSY, 0)) As cbj, '到货补录' As ywlx， '0'
					From t_bldhdj t
				 Where DBrq >= Date '$start'
					 And DBrq < Date '$end'
				 Group By ywbmbh";
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
			$ora->query($sql_gc);
	$sql="Select to_char(t_month_kchd.qcrq, 'YYYY-MM-DD') as 期初日期, to_char(t_month_kchd.qmrq, 'YYYY-MM-DD') as  期末日期, ywbmbh 业务部门编号,zmy 总码洋,zsy 总实样,cbj 成本价,ywlx 类型 From  t_month_kchd Where ywbmbh = '$users[$i]' And bj = '1'"; 
	$row=$ora->query($sql);
	$total = count($row);
	$title = array('期初日期','期末日期','业务部门编号','总码洋','总实样','成本价','类型');
	yuebao($sql,$fileName,$title,$total,$yewugongsi,$users[$i],$timetype);
	}
}
exit;




//开始开始
$host='172.30.153.63';
$ip='172.30.153.63/xhsddb';
$port='1521';
$user= 'dbjczc';
$pass= 'dbjczc';
$charset='utf8';
$ora=new oracle_admin($user,$pass,$ip,$charset);

$yewugongsi = 2;//教材公司
$admin_username = '010001';
//1
$fileName = '教材月报';
$sql="Select * From t_Month_Hzb_Nian Where ywbmbh='$admin_username'";
$row=$ora->query($sql);
$total = count($row);
$title = array('YWBMBH','LX','BH','DMMC','QCWJMY','QCWJSY','QCWJCB','JFMY','JFSY','JFCB','JSMY','JSSY','JSCB','QMWJMY','QMWJSY','QMWJCB','SYLX','BB','MINDATE','MAXDATE','NY','BJSMY','BJSSY');
yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username,$timetype);
//exit;
//2
$fileName = '教材到货明细';
$sql="Select YWBMBH,GHDWH,MC,DHPCH,YSDJ,SSDH,ZDQH,ZDXH,SM,DJ,JXSSSL,MY,SYS,ZDPCH,JSRQ,JSLX,ZDPCH1,TSLSH,YSDBRQ,JCDRQ,KQBJ,JSGHDWH,YSGHDWH,FLOWID_DHMX,to_char(t_month_dhjsb.SHRQ, 'YYYY-MM-DD HH24:MI:SS') as SHRQ ,to_char(t_month_dhjsb.YSRQ, 'YYYY-MM-DD HH24:MI:SS') as YSRQ From t_month_dhjsb Where ywbmbh='$admin_username' And shrq>=Date'$start' and shrq < Date'$end'";
$row=$ora->query($sql);
$total = count($row);
$title = array('YWBMBH','GHDWH','MC','DHPCH','SHRQ','YSDJ','SSDH','ZDQH','ZDXH','SM','DJ','JXSSSL','MY','SYS','ZDPCH','JSRQ','JSLX','ZDPCH1','TSLSH','YSRQ','YSDBRQ','JCDRQ','KQBJ','JSGHDWH','YSGHDWH','FLOWID_DHMX');
yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username,$timetype);

//3
$fileName = '教材进退明细';
$sql="Select YWBMBH,GHDWH,MC,ID,SM,DJ,ZMY,ZSY,BZ,YWPCH,ZDPCH,JSRQ,JSLX,ZDQH,ZDXH,ZDPCH1,TSLSH,NF,NFHRQ,NJSRQ,JSGHDWH,YSGHDWH,ZCS,to_char(t_month_jtjsb.DBRQ, 'YYYY-MM-DD HH24:MI:SS') as DBRQ
 From t_month_jtjsb Where ywbmbh='$admin_username' And dbrq>=Date'$start' and dbrq < Date'$end'";
$row=$ora->query($sql);
$total = count($row);
$title = array('YWBMBH','GHDWH','MC','DBRQ','ID','SM','DJ','ZMY','ZSY','BZ','YWPCH','ZDPCH','JSRQ','JSLX','ZDQH','ZDXH','ZDPCH1','TSLSH','NF','NFHRQ','NJSRQ','JSGHDWH','YSGHDWH','ZCS');
yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username,$timetype);

//4
$fileName = '教材出版社免费让利明细';
$sql="Select YWBMBH,GHDWH,MC,DHPCH,SHRQ,YSDJ,SSDH,ZDQH,ZDXH,SM,DJ,JXSSSL,MY,SYS,ZDPCH,JSRQ,JSLX,JSGHDWH,YSGHDWH,TSLSH
 From t_month_dhjsb_mf Where ywbmbh='$admin_username' And shrq>=Date'$start' and shrq < Date'$end'";
$row=$ora->query($sql);
$total = count($row);
$title = array('YWBMBH','GHDWH','MC','DHPCH','SHRQ','YSDJ','SSDH','ZDQH','ZDXH','SM','DJ','JXSSSL','MY','SYS','ZDPCH','JSRQ','JSLX','JSGHDWH','YSGHDWH','TSLSH');
yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username,$timetype);

//5 
$fileName = '教材销退明细';
$sql="Select ghdw,ghdwmc,YWBMBH,KFBH,DH,DM,PCH,ZMY,ZSY,ZCB,TSLSH,JSRQ,JSLX,ZDQH,NF,NFHRQ,NJSRQ,JB,CWQRPC,ZCS,DJ,ID,BB,SM,ISBN,CBNY,to_char(t_month_xtjsb.THRQ, 'YYYY-MM-DD HH24:MI:SS') as THRQ From t_month_xtjsb Where ywbmbh='$admin_username' And thrq>=Date'$start' and thrq < Date'$end'";
$row=$ora->query($sql);
$total = count($row);
$title = array('GHDW','GHDWMC','YWBMBH','KFBH','DH','DM','PCH','THRQ','ZMY','ZSY','ZCB','TSLSH','JSRQ','JSLX','ZDQH','NF','NFHRQ','NJSRQ','JB','CWQRPC','ZCS','DJ','ID','BB','SM','ISBN','CBNY');
yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username,$timetype);

//6 
$fileName = '教材基层店免费让利明细';
$sql="Select * From t_month_fhgzjsb Where ywbmbh='$admin_username' And gzrq>=Date'$start' and gzrq < Date'$end'";
$row=$ora->query($sql);
$total = count($row);
$title = array('YWBMBH','KFBH','DH','DM','GZPCH','GZRQ','ZMY','ZSY','ZCB','TSLSH','JSRQ','BZ','JSLX','ZDQH','NF','NFHRQ','NJSRQ','JB','ZCS');
yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username,$timetype);

//7 
$fileName = '教材发货明细';
$sql="Select cbny,ghdw,ghdwmc,YWBMBH,DH,MC,PFPCH,BZ,ZMY,ZSY,CBJ,TSLSH,JSRQ,SM,DJ,ZDQH,ZDXH,KFBH,JSLX,NF,NFHRQ,NJSRQ,JB,CWQRPC,YSDBRQ,ZCS,to_char(t_month_fhjsb.DBRQ, 'YYYY-MM-DD HH24:MI:SS') as DBRQ
,to_char(t_month_fhjsb.PFRQ, 'YYYY-MM-DD HH24:MI:SS') as PFRQ From t_month_fhjsb Where ywbmbh='$admin_username' And dbrq>=Date'$start' and dbrq < Date'$end'";
$row=$ora->query($sql);
$total = count($row);
$title = array('CBNY','GHDW','GHDWMC','YWBMBH','DH','MC','PFPCH','BZ','DBRQ','PFRQ','ZMY','ZSY','CBJ','TSLSH','JSRQ','SM','DJ','ZDQH','ZDXH','KFBH','JSLX','NF','NFHRQ','NJSRQ','JB','CWQRPC','YSDBRQ','ZCS');
yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username,$timetype);

//8 
$fileName = '教材库存明细';
$dangyue = dy_month(); //当月第一天
$start_kc = date('Y-m-d',$dangyue['start_time']);
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
   From t_Hd_Kcsl_Bf@zczm_jc t Where kccs<>0 And ywbmbh='$admin_username' And bfsj>=Date'$start_kc'";
$row=$ora->query($sql);
$total = count($row);
$title = array('YWBMBH','ID','SM','DJ','版别','KCCS','MY','SYS','KF','WL','分类','HW','供货单位','YWY','ISBN','版次','编者','年');
yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username,$timetype);

//9 
$fileName = '教材配发中间态';
$dangyue = dy_month(); //当月第一天
$start_pf = date('Y-m-d',$dangyue['start_time']);
$sql="Select Ywbmbh ,(select dm from t_dm@zczm_jc where dh=a.ywbmbh) as ywbmmc,dh,(select dm from t_dm@zczm_jc where dh=a.dh) as dm,
a.id,(select isbn from t_kcsm@zczm_jc where a.id=id) as isbn,(select sm from t_kcsm@zczm_jc where a.id=id) as sm,
(select dj from t_kcsm@zczm_jc where a.id=id) as dj,(Select round(cbny) From t_kcsm@zczm_jc Where Id=a.id)As cbny,a.pxcs,(select dj*a.pxcs from t_kcsm@zczm_jc where a.id=id) as zmy ,a.sys,a.cbj,
a.pxdh,a.bz,a.zdh,a.sylx,a.sl,a.zjxs,a.qx,a.djbh,a.kfbh,A.Bjlsh,a.cybh,A.Flowid,a.zdqh,a.zdxh,a.zdqh1,a.zdxh1,
to_char(a.pfrq, 'YYYY-MM-DD HH24:MI:SS') as pfrq From t_hd_pfmx@zczm_jc a Where bfsj>=Date'$start_pf' and ckbj='1' and ywbmbh='$admin_username'";
$row=$ora->query($sql);
$total = count($row);
$title = array('YWBMBH','YWBMMC','DH','DM','ID','ISBN','SM','DJ','CBNY','PXCS','ZMY','SYS','CBJ','PXDH','BZ','ZDH','SYLX','SL','ZJXS','QX','DJBH','KFBH','BJLSH','CYBH','FLOWID','ZDQH','ZDXH','ZDQH1','ZDXH1','PFRQ');
yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username,$timetype);

//10
$fileName = '教材库房转移中间态明细';
$sql="Select Id ,(Select sm From t_kcsm@zczm_jc Where Id=A.id)As sm,(Select isbn From t_kcsm@zczm_jc Where Id=A.id)As  isbn,
(Select dj From t_kcsm@zczm_jc Where Id=A.id)As dj,(Select min(mc) From t_bb@zczm_jc Where bh=(Select bb From t_kcsm@zczm_jc Where Id=A.id))As bb,
		  (Select mc From t_ghdw@zczm_jc Where bh=(Select min(ghdwh) From t_dhls_jx@zczm_jc Where Id=A.id))As ghdw,(Select Name From t_user@zczm_jc Where usr_id=(Select ywryh From t_kcsm@zczm_jc Where Id=A.id))As ywy,cbny,ywlx,Sum(cs),Sum(cs*(Select dj From t_kcsm@zczm_jc Where Id=a.id))As MY,Sum(sy)As SY From (
Select YWLX,Id,(Select round(cbny) From t_kcsm@zczm_jc Where Id=t_crkls.id)As cbny,
Sum(CRKCS)CS,sum(crksy)sy From t_crkls@zczm_jc Where ywbmbh='$admin_username' And (YWLX='YC' Or YWLX='DC' )And crkrq>=Date'2018-07-01' And crkrq < Date'$end' Group By ywlx,Id
Union All Select YWLX,Id,(Select round(cbny) From t_kcsm@zczm_jc Where Id=t_crkls.id)As cbny,Sum(-CRKCS)CS,sum(-crksy)sy From t_crkls@zczm_jc Where ywbmbh='$admin_username' And ( YWLX='YR'Or YWLX='DR' )
And crkrq >= Date'2018-07-01' And crkrq < Date'$end' Group By ywlx,Id) A Group By Id,ywlx,cbny Having Sum(cs)<>0						
";
$row=$ora->query($sql);
$total = count($row);
$title = array('ID','SM','ISBN','DJ','版别','供货单位','YWY','CBNY','YWLX','SUM(CS)','MY','SY');
yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username,$timetype);

//11
$fileName = '教材损益明细';
$sql="Select KFBH,(Select SM From T_KCSM@zczm_jc Where Id=T.ID) As SM,CRKCS,(Select dj From t_kcsm@zczm_jc Where T.id=Id)as dj,(crkcs*(Select dj From t_kcsm@zczm_jc Where T.id=Id)) zmy,crksy,crkrq
From t_crkls@zczm_jc T Where  crkrq>=Date'$start' And crkrq < Date'$end'  And ywbmbh='$admin_username' And ywlx='SY'	";
$row=$ora->query($sql);
$total = count($row);
$title = array('KFBH','SM','CRKCS','DJ','ZMY','CRKSY','CRKRQ');
yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username,$timetype);

//12
$fileName = '教材当月已结算-到货已结-到货';
$sql="Select YWBMBH,GHDWH,MC,DHPCH,YSDJ,SSDH,ZDQH,ZDXH,SM,DJ,JXSSSL,MY,SYS,ZDPCH,JSRQ,JSLX,ZDPCH1,TSLSH,YSDBRQ,JCDRQ,KQBJ,JSGHDWH,YSGHDWH,FLOWID_DHMX,to_char(t_Month_Dhjsb.SHRQ, 'YYYY-MM-DD HH24:MI:SS') as SHRQ,to_char(t_Month_Dhjsb.YSRQ, 'YYYY-MM-DD HH24:MI:SS') as YSRQ From t_Month_Dhjsb Where jsrq>=Date'$start' And jsrq < Date'$end' And ywbmbh='$admin_username'";
$row=$ora->query($sql);
$total = count($row);
$title = array('YWBMBH','GHDWH','MC','DHPCH','SHRQ','YSDJ','SSDH','ZDQH','ZDXH','SM','DJ','JXSSSL','MY','SYS','ZDPCH','JSRQ','JSLX','ZDPCH1','TSLSH','YSRQ','YSDBRQ','JCDRQ','KQBJ','JSGHDWH','YSGHDWH','FLOWID_DHMX');
yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username,$timetype);

//13
$fileName = '教材当月已结算-到货已结-进退';
$sql="Select YWBMBH,GHDWH,MC,ID,SM,DJ,ZMY,ZSY,BZ,YWPCH,ZDPCH, JSLX,ZDQH,ZDPCH1,TSLSH,NF,NFHRQ,NJSRQ,JSGHDWH,YSGHDWH,ZCS,to_char(t_Month_jtjsb.DBRQ, 'YYYY-MM-DD HH24:MI:SS') as DBRQ,to_char(t_Month_jtjsb.JSRQ, 'YYYY-MM-DD HH24:MI:SS') as JSRQ  From t_Month_jtjsb Where jsrq>=Date'$start' And jsrq < Date'$end' And ywbmbh='$admin_username'";
$row=$ora->query($sql);
$total = count($row);
$title = array('YWBMBH','GHDWH','MC','DBRQ','ID','SM','DJ','ZMY','ZSY','BZ','YWPCH','ZDPCH','JSRQ','JSLX','ZDQH','ZDPCH1','TSLSH','NF','NFHRQ','NJSRQ','JSGHDWH','YSGHDWH','ZCS');
yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username,$timetype);



//15
$fileName = '教材当月已结算-发货已结-到货';
$sql="Select cbny,ghdw,ghdwmc,YWBMBH,DH,MC,PFPCH,BZ,ZMY,ZSY,CBJ,TSLSH,SM,DJ,ZDQH,ZDXH,KFBH,JSLX,NF,NFHRQ,NJSRQ,JB,CWQRPC,YSDBRQ,ZCS,to_char(t_Month_fhjsb.DBRQ, 'YYYY-MM-DD HH24:MI:SS') as DBRQ,to_char(t_Month_fhjsb.PFRQ, 'YYYY-MM-DD HH24:MI:SS') as PFRQ ,to_char(t_Month_fhjsb.JSRQ, 'YYYY-MM-DD HH24:MI:SS') as JSRQ  From t_Month_fhjsb Where jsrq>=Date'$start' And jsrq < Date'$end' And ywbmbh='$admin_username'";
$row=$ora->query($sql);
$total = count($row);
$title = array('CBNY','GHDW','GHDWMC','YWBMBH','DH','MC','PFPCH','BZ','DBRQ','PFRQ','ZMY','ZSY','CBJ','TSLSH','JSRQ','SM','DJ','ZDQH','ZDXH','KFBH','JSLX','NF','NFHRQ','NJSRQ','JB','CWQRPC','YSDBRQ','ZCS');
yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username,$timetype);



//16
$fileName = '教材当月已结算-发货已结-退货';
$sql="Select * From t_Month_xtjsb Where jsrq>=Date'$start' And jsrq < Date'$end' And ywbmbh='$admin_username'";
$row=$ora->query($sql);
$total = count($row);
$title = array('GHDW','GHDWMC','YWBMBH','KFBH','DH','DM','PCH','THRQ','ZMY','ZSY','ZCB','TSLSH','JSRQ','JSLX','ZDQH','NF','NFHRQ','NJSRQ','JB','CWQRPC','ZCS','DJ','ID','BB','SM','ISBN','CBNY');
yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username,$timetype);

//17
$fileName = '教材当月已结算-发货已结-更正';
$sql="Select * From t_Month_Fhgzjsb Where jsrq>=Date'$start' And jsrq < Date'$end' And ywbmbh='$admin_username'";
$row=$ora->query($sql);
$total = count($row);
$title = array('CBNY','GHDW','GHDWMC','YWBMBH','KFBH','DH','DM','GZPCH','GZRQ','ZMY','ZSY','ZCB','TSLSH','JSRQ','BZ','JSLX','ZDQH','NF','NFHRQ','NJSRQ','JB','ZCS');
yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username,$timetype);

//18
$fileName = '教材应付款-到货未结明细';
$sql="Select YWBMBH,GHDWH,MC,DHPCH,YSDJ,SSDH,ZDQH,ZDXH,SM,DJ,JXSSSL,MY,SYS,ZDPCH,JSRQ,JSLX,ZDPCH1,TSLSH,YSDBRQ,JCDRQ,KQBJ,JSGHDWH,YSGHDWH,FLOWID_DHMX,to_char(t_Month_Dhjsb.SHRQ, 'YYYY-MM-DD HH24:MI:SS') as SHRQ,to_char(t_Month_Dhjsb.YSRQ, 'YYYY-MM-DD HH24:MI:SS') as YSRQ  From t_Month_Dhjsb Where ywbmbh='$admin_username' And jsrq Is Null";
$row=$ora->query($sql);
$total = count($row);
$title = array('YWBMBH','GHDWH','MC','DHPCH','SHRQ','YSDJ','SSDH','ZDQH','ZDXH','SM','DJ','JXSSSL','MY','SYS','ZDPCH','JSRQ','JSLX','ZDPCH1','TSLSH','YSRQ','YSDBRQ','JCDRQ','KQBJ','JSGHDWH','YSGHDWH','FLOWID_DHMX');
yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username,$timetype);

//19
$fileName = '教材应付款-进退未结明细';
$sql="Select YWBMBH,GHDWH,MC,ID,SM,DJ,ZMY,ZSY,BZ,YWPCH,ZDPCH,JSRQ,JSLX,ZDQH,ZDXH,ZDPCH1,TSLSH,NF,NFHRQ,NJSRQ,JSGHDWH,YSGHDWH,ZCS,to_char(t_Month_jtjsb.DBRQ, 'YYYY-MM-DD HH24:MI:SS') as DBRQ From t_Month_jtjsb Where ywbmbh='$admin_username' And jsrq Is Null";
$row=$ora->query($sql);
$total = count($row);
$title = array('YWBMBH','GHDWH','MC','DBRQ','ID','SM','DJ','ZMY','ZSY','BZ','YWPCH','ZDPCH','JSRQ','JSLX','ZDQH','ZDXH','ZDPCH1','TSLSH','NF','NFHRQ','NJSRQ','JSGHDWH','YSGHDWH','ZCS');
yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username,$timetype);



$fileName = '教材应付款-出版社让利未结';
$sql="Select YWBMBH,GHDWH,MC,DHPCH,YSDJ,SSDH,ZDQH,ZDXH,SM,DJ,JXSSSL,MY,SYS,ZDPCH,JSRQ,JSLX,JSGHDWH,YSGHDWH,TSLSH,to_char(t_month_dhjsb_mf.SHRQ, 'YYYY-MM-DD HH24:MI:SS') as SHRQ From t_month_dhjsb_mf  Where jsrq Is Null And ywbmbh='$admin_username'";
$row=$ora->query($sql);
$total = count($row);
$title = array('YWBMBH','GHDWH','MC','DHPCH','SHRQ','YSDJ','SSDH','ZDQH','ZDXH','SM','BZ','DJ','JXSSSL','MY','SYS','ZDPCH','JSRQ','JSLX','JSGHDWH','YSGHDWH','TSLSH');
yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username,$timetype);


//20
$fileName = '教材应收款-发货未结明细';
$sql="Select cbny,ghdw,ghdwmc,YWBMBH,DH,MC,PFPCH,BZ,ZMY,ZSY,CBJ,TSLSH,JSRQ,SM,DJ,ZDQH,ZDXH,KFBH,JSLX,NF,NFHRQ,NJSRQ,JB,CWQRPC,YSDBRQ,ZCS,to_char(t_Month_fhjsb.DBRQ, 'YYYY-MM-DD HH24:MI:SS') as DBRQ,to_char(t_Month_fhjsb.PFRQ, 'YYYY-MM-DD HH24:MI:SS') as PFRQ From t_Month_fhjsb Where ywbmbh='$admin_username' And jsrq Is Null";
$row=$ora->query($sql);
$total = count($row);
$title = array('CBNY','GHDW','GHDWMC','YWBMBH','DH','MC','PFPCH','BZ','DBRQ','PFRQ','ZMY','ZSY','CBJ','TSLSH','JSRQ','SM','DJ','ZDQH','ZDXH','KFBH','JSLX','NF','NFHRQ','NJSRQ','JB','CWQRPC','YSDBRQ','ZCS');
yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username,$timetype);

//21
$fileName = '教材应收款-销退未结明细';
$sql="Select cbny,ghdw,ghdwmc,YWBMBH,KFBH,DH,DM,PCH,ZMY,ZSY,ZCB,TSLSH,JSRQ,JSLX,ZDQH,NF,NFHRQ,NJSRQ,JB,CWQRPC,ZCS,DJ,to_char(t_Month_Xtjsb.THRQ, 'YYYY-MM-DD HH24:MI:SS') as THRQ From t_Month_Xtjsb Where ywbmbh='$admin_username' And jsrq Is Null";
$row=$ora->query($sql);
$total = count($row);
$title = array('CBNY','GHDW','GHDWMC','YWBMBH','KFBH','DH','DM','PCH','THRQ','ZMY','ZSY','ZCB','TSLSH','JSRQ','JSLX','ZDQH','NF','NFHRQ','NJSRQ','JB','CWQRPC','ZCS','DJ');
yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username,$timetype);

//22
$fileName = '教材应收款-更正未结明细';
$sql="Select YWBMBH,KFBH,DH,DM,GZPCH,ZMY,ZSY,ZCB,TSLSH,JSRQ,BZ,JSLX,ZDQH,NF,NFHRQ,NJSRQ,JB,ZCS,to_char(t_Month_fhgzjsb.GZRQ, 'YYYY-MM-DD HH24:MI:SS') as GZRQ From t_Month_fhgzjsb Where ywbmbh='$admin_username' And jsrq Is Null";
$row=$ora->query($sql);
$total = count($row);
$title = array('YWBMBH','KFBH','DH','DM','GZPCH','GZRQ','ZMY','ZSY','ZCB','TSLSH','JSRQ','BZ','JSLX','ZDQH','NF','NFHRQ','NJSRQ','JB','ZCS');
yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username,$timetype);

$yewugongsi = 1;//教辅公司
  
//set_time_limit(0);
//ini_set('memory_limit', '-1');
$host='172.30.153.63';
$ip='172.30.153.63/xhsddb';
$port='1521';
$user= 'dbjczc';
$pass= 'dbjczc';
$charset='utf8';
$ora=new oracle_admin($user,$pass,$ip,$charset);

$abc = array('020001','020002','020003','020004','020005');
$admin_username = implode(',',$abc);
//1
$fileName = '教辅月报';
$sql="Select * From t_Month_Hzb_Nian Where ywbmbh in ($admin_username)";
$row=$ora->query($sql);
$total = count($row);
$title = array('YWBMBH','LX','BH','DMMC','QCWJMY','QCWJSY','QCWJCB','JFMY','JFSY','JFCB','JSMY','JSSY','JSCB','QMWJMY','QMWJSY','QMWJCB','SYLX','BB','MINDATE','MAXDATE','NY','BJSMY','BJSSY');
yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username,$timetype);

//2
$fileName = '教辅到货明细';
$sql="Select YWBMBH,GHDWH,MC,DHPCH,YSDJ,SSDH,ZDQH,ZDXH,SM,DJ,JXSSSL,MY,SYS,ZDPCH,JSRQ,JSLX,ZDPCH1,TSLSH,YSDBRQ,JCDRQ,KQBJ,JSGHDWH,YSGHDWH,FLOWID_DHMX,to_char(t_month_dhjsb.SHRQ, 'YYYY-MM-DD HH24:MI:SS') as SHRQ ,to_char(t_month_dhjsb.YSRQ, 'YYYY-MM-DD HH24:MI:SS') as YSRQ From t_month_dhjsb Where ywbmbh in ($admin_username) And shrq>=Date'$start' and shrq< Date'$end'";
$row=$ora->query($sql);
$total = count($row);
$title = array('YWBMBH','GHDWH','MC','DHPCH','SHRQ','YSDJ','SSDH','ZDQH','ZDXH','SM','DJ','JXSSSL','MY','SYS','ZDPCH','JSRQ','JSLX','ZDPCH1','TSLSH','YSRQ','YSDBRQ','JCDRQ','KQBJ','JSGHDWH','YSGHDWH','FLOWID_DHMX');
yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username,$timetype);

//3
$fileName = '教辅进退明细';
$sql="Select YWBMBH,GHDWH,MC,ID,SM,DJ,ZMY,ZSY,BZ,YWPCH,ZDPCH,JSRQ,JSLX,ZDQH,ZDXH,ZDPCH1,TSLSH,NF,NFHRQ,NJSRQ,JSGHDWH,YSGHDWH,ZCS,to_char(t_month_jtjsb.DBRQ, 'YYYY-MM-DD HH24:MI:SS') as DBRQ
 From t_month_jtjsb Where ywbmbh in ($admin_username) And dbrq>=Date'$start' and dbrq< Date'$end'";
$row=$ora->query($sql);
$total = count($row);
$title = array('YWBMBH','GHDWH','MC','DBRQ','ID','SM','DJ','ZMY','ZSY','BZ','YWPCH','ZDPCH','JSRQ','JSLX','ZDQH','ZDXH','ZDPCH1','TSLSH','NF','NFHRQ','NJSRQ','JSGHDWH','YSGHDWH','ZCS');
yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username,$timetype);

//4
$fileName = '教辅出版社免费让利明细';
$sql="Select YWBMBH,GHDWH,MC,DHPCH,SHRQ,YSDJ,SSDH,ZDQH,ZDXH,SM,DJ,JXSSSL,MY,SYS,ZDPCH,JSRQ,JSLX,JSGHDWH,YSGHDWH,TSLSH
 From t_month_dhjsb_mf Where ywbmbh in ($admin_username) And shrq>=Date'$start' and shrq< Date'$end'";
$row=$ora->query($sql);
$total = count($row);
$title = array('YWBMBH','GHDWH','MC','DHPCH','SHRQ','YSDJ','SSDH','ZDQH','ZDXH','SM','DJ','JXSSSL','MY','SYS','ZDPCH','JSRQ','JSLX','JSGHDWH','YSGHDWH','TSLSH');
yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username,$timetype);

//5
$fileName = '教辅销退明细';
//$sql="Select ghdw,ghdwmc,YWBMBH,KFBH,DH,DM,PCH,ZMY,ZSY,ZCB,TSLSH,JSLX,ZDQH,NF,NFHRQ,NJSRQ,JB,CWQRPC,ZCS,DJ,ID,BB,SM,ISBN,CBNY,to_char(t_month_xtjsb.THRQ, 'YYYY-MM-DD HH24:MI:SS') as THRQ From t_month_xtjsb Where ywbmbh in ($admin_username) And thrq>=Date'$start' and thrq< Date'$end'";

$sql="Select ghdw,ghdwmc,YWBMBH,KFBH,DH,DM,PCH,ZMY,ZSY,ZCB,TSLSH,JSLX,ZDQH,NF,NFHRQ,NJSRQ,JB,CWQRPC,ZCS,DJ,ID,BB,SM,ISBN,CBNY,to_char(t_month_xtjsb.THRQ, 'YYYY-MM-DD HH24:MI:SS') as THRQ,to_char(t_month_xtjsb.JSRQ, 'YYYY-MM-DD HH24:MI:SS') as JSRQ From t_month_xtjsb Where ywbmbh in ($admin_username) And thrq>=Date'$start' and thrq< Date'$end'";

$row=$ora->query($sql);
$total = count($row);
$title = array('GHDW','GHDWMC','YWBMBH','KFBH','DH','DM','PCH','THRQ','ZMY','ZSY','ZCB','TSLSH','JSRQ','JSLX','ZDQH','NF','NFHRQ','NJSRQ','JB','CWQRPC','ZCS','DJ','ID','BB','SM','ISBN','CBNY');
yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username,$timetype);
//6
$fileName = '教辅基层店免费让利明细';
$sql="Select * From t_month_fhgzjsb Where ywbmbh in ($admin_username) And gzrq>=Date'$start' and gzrq< Date'$end'";
$row=$ora->query($sql);
$total = count($row);
$title = array('YWBMBH','KFBH','DH','DM','GZPCH','GZRQ','ZMY','ZSY','ZCB','TSLSH','JSRQ','BZ','JSLX','ZDQH','NF','NFHRQ','NJSRQ','JB','ZCS');
yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username,$timetype);
//7
$fileName = '教辅发货明细';
$sql="Select cbny,ghdw,ghdwmc,YWBMBH,DH,MC,PFPCH,BZ,ZMY,ZSY,CBJ,TSLSH,JSRQ,SM,DJ,ZDQH,ZDXH,KFBH,JSLX,NF,NFHRQ,NJSRQ,JB,CWQRPC,YSDBRQ,ZCS,to_char(t_month_fhjsb.DBRQ, 'YYYY-MM-DD HH24:MI:SS') as DBRQ
,to_char(t_month_fhjsb.PFRQ, 'YYYY-MM-DD HH24:MI:SS') as PFRQ From t_month_fhjsb Where ywbmbh in ($admin_username) And dbrq>=Date'$start' and dbrq< Date'$end'";
$row=$ora->query($sql);
$total = count($row);
$title = array('CBNY','GHDW','GHDWMC','YWBMBH','DH','MC','PFPCH','BZ','DBRQ','PFRQ','ZMY','ZSY','CBJ','TSLSH','JSRQ','SM','DJ','ZDQH','ZDXH','KFBH','JSLX','NF','NFHRQ','NJSRQ','JB','CWQRPC','YSDBRQ','ZCS');
yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username,$timetype);
//8
$fileName = '教辅库存明细';
$dangyue = dy_month(); //当月第一天
$start_kc = date('Y-m-d',$dangyue['start_time']);

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
	   From t_Hd_Kcsl_Bf@zczm_jc t Where kccs<>0 And ywbmbh in ($admin_username) And bfsj>=Date'$start_kc'";
$row=$ora->query($sql);
$total = count($row);
$title = array('YWBMBH','ID','SM','DJ','版别','KCCS','MY','SYS','KF','WL','分类','HW','供货单位','YWY','ISBN','版次','编者','年');
yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username,$timetype);
//9
$fileName = '教辅配发中间态';
$dangyue = dy_month(); //当月第一天
$start_pf = date('Y-m-d',$dangyue['start_time']);
$sql="Select Ywbmbh ,(select dm from t_dm@zczm_jc where dh=a.ywbmbh) as ywbmmc,dh,(select dm from t_dm@zczm_jc where dh=a.dh) as dm,
a.id,(select isbn from t_kcsm@zczm_jc where a.id=id) as isbn,(select sm from t_kcsm@zczm_jc where a.id=id) as sm,
(select dj from t_kcsm@zczm_jc where a.id=id) as dj,(Select round(cbny) From t_kcsm@zczm_jc Where Id=a.id)As cbny,a.pxcs,(select dj*a.pxcs from t_kcsm@zczm_jc where a.id=id) as zmy ,a.sys,a.cbj,
a.pxdh,a.bz,a.zdh,a.sylx,a.sl,a.zjxs,a.qx,a.djbh,a.kfbh,A.Bjlsh,a.cybh,A.Flowid,a.zdqh,a.zdxh,a.zdqh1,a.zdxh1,
to_char(a.pfrq, 'YYYY-MM-DD HH24:MI:SS') as pfrq From t_hd_pfmx@zczm_jc a Where bfsj>=Date'$start_pf' and ckbj='1' and ywbmbh in ($admin_username)";
$row=$ora->query($sql);
$total = count($row);
$title = array('YWBMBH','YWBMMC','DH','DM','ID','ISBN','SM','DJ','CBNY','PXCS','ZMY','SYS','CBJ','PXDH','BZ','ZDH','SYLX','SL','ZJXS','QX','DJBH','KFBH','BJLSH','CYBH','FLOWID','ZDQH','ZDXH','ZDQH1','ZDXH1','PFRQ');
yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username,$timetype);
//10
$fileName = '教辅库房转移中间态明细';
$sql="Select Id ,(Select sm From t_kcsm@zczm_jc Where Id=A.id)As sm,(Select isbn From t_kcsm@zczm_jc Where Id=A.id)As  isbn,
(Select dj From t_kcsm@zczm_jc Where Id=A.id)As dj,(Select min(mc) From t_bb@zczm_jc Where bh=(Select bb From t_kcsm@zczm_jc Where Id=A.id))As bb,
		  (Select mc From t_ghdw@zczm_jc Where bh=(Select min(ghdwh) From t_dhls_jx@zczm_jc Where Id=A.id))As ghdw,(Select Name From t_user@zczm_jc Where usr_id=(Select ywryh From t_kcsm@zczm_jc Where Id=A.id))As ywy,cbny,ywlx,Sum(cs),Sum(cs*(Select dj From t_kcsm@zczm_jc Where Id=a.id))As MY,Sum(sy)As SY From (
Select YWLX,Id,(Select round(cbny) From t_kcsm@zczm_jc Where Id=t_crkls.id)As cbny,
Sum(CRKCS)CS,sum(crksy)sy From t_crkls@zczm_jc Where ywbmbh in ($admin_username) And (YWLX='YC' Or YWLX='DC' )And crkrq>=Date'2018-07-01' And crkrq < Date'$end' Group By ywlx,Id
Union All Select YWLX,Id,(Select round(cbny) From t_kcsm@zczm_jc Where Id=t_crkls.id)As cbny,Sum(-CRKCS)CS,sum(-crksy)sy From t_crkls@zczm_jc Where ywbmbh in ($admin_username) And ( YWLX='YR'Or YWLX='DR' )
And crkrq>=Date'2018-07-01' And crkrq < Date'$end' Group By ywlx,Id) A Group By Id,ywlx,cbny Having Sum(cs)<>0";
$row=$ora->query($sql);
$total = count($row);
$title = array('ID','SM','ISBN','DJ','版别','供货单位','YWY','CBNY','YWLX','SUM(CS)','MY','SY');
yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username,$timetype);

//11
$fileName = '教辅损益明细';
$sql="Select KFBH,(Select SM From T_KCSM@zczm_jc Where Id=T.ID) As SM,CRKCS,(Select dj From t_kcsm@zczm_jc Where T.id=Id)as dj,(crkcs*(Select dj From t_kcsm@zczm_jc Where T.id=Id)) zmy,crksy,crkrq
From t_crkls@zczm_jc T Where  crkrq>=Date'$start' And crkrq < Date'$end' And ywbmbh in ($admin_username) And ywlx='SY'	";
$row=$ora->query($sql);
$total = count($row);
$title = array('KFBH','SM','CRKCS','DJ','ZMY','CRKSY','CRKRQ');
yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username,$timetype);

//12
$fileName = '教辅当月已结算-到货已结-到货';
$sql="Select YWBMBH,GHDWH,MC,DHPCH,YSDJ,SSDH,ZDQH,ZDXH,SM,DJ,JXSSSL,MY,SYS,ZDPCH,JSRQ,JSLX,ZDPCH1,TSLSH,YSDBRQ,JCDRQ,KQBJ,JSGHDWH,YSGHDWH,FLOWID_DHMX,to_char(t_Month_Dhjsb.SHRQ, 'YYYY-MM-DD HH24:MI:SS') as SHRQ,to_char(t_Month_Dhjsb.YSRQ, 'YYYY-MM-DD HH24:MI:SS') as YSRQ From t_Month_Dhjsb Where jsrq>=Date'$start' And jsrq < Date'$end' And ywbmbh in ($admin_username)";
$row=$ora->query($sql);
$total = count($row);
$title = array('YWBMBH','GHDWH','MC','DHPCH','SHRQ','YSDJ','SSDH','ZDQH','ZDXH','SM','DJ','JXSSSL','MY','SYS','ZDPCH','JSRQ','JSLX','ZDPCH1','TSLSH','YSRQ','YSDBRQ','JCDRQ','KQBJ','JSGHDWH','YSGHDWH','FLOWID_DHMX');
yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username,$timetype);

$fileName = '教辅当月已结算-到货已结-进退';
$sql="Select YWBMBH,GHDWH,MC,ID,SM,DJ,ZMY,ZSY,BZ,YWPCH,ZDPCH, JSLX,ZDQH,ZDPCH1,TSLSH,NF,NFHRQ,NJSRQ,JSGHDWH,YSGHDWH,ZCS,to_char(t_Month_jtjsb.DBRQ, 'YYYY-MM-DD HH24:MI:SS') as DBRQ,to_char(t_Month_jtjsb.JSRQ, 'YYYY-MM-DD HH24:MI:SS') as JSRQ  From t_Month_jtjsb Where jsrq>=Date'$start' And jsrq < Date'$end' And ywbmbh in ($admin_username)";
$row=$ora->query($sql);
$total = count($row);
$title = array('YWBMBH','GHDWH','MC','DBRQ','ID','SM','DJ','ZMY','ZSY','BZ','YWPCH','ZDPCH','JSRQ','JSLX','ZDQH','ZDPCH1','TSLSH','NF','NFHRQ','NJSRQ','JSGHDWH','YSGHDWH','ZCS');
yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username,$timetype);



//13
$fileName = '教辅当月已结算-到货已结-退货';
$sql="Select YWBMBH,GHDWH,MC,ID,SM,DJ,ZMY,ZSY,BZ,YWPCH,ZDPCH,JSLX,ZDQH,ZDXH,ZDPCH1,TSLSH,NF,NFHRQ,NJSRQ,JSGHDWH,YSGHDWH,ZCS,to_char(t_Month_jtjsb.DBRQ, 'YYYY-MM-DD HH24:MI:SS') as DBRQ,to_char(t_Month_jtjsb.JSRQ, 'YYYY-MM-DD HH24:MI:SS') as JSRQ From t_Month_jtjsb Where jsrq>=Date'$start' And jsrq < Date'$end' And ywbmbh in ($admin_username)";
$row=$ora->query($sql);
$total = count($row);
$title = array('YWBMBH','GHDWH','MC','ID','DBRQ','SM','DJ','ZMY','ZSY','BZ','YWPCH','JSRQ','ZDPCH','JSLX','ZDQH','ZDXH','ZDPCH1','TSLSH','NF','NFHRQ','NJSRQ','JSGHDWH','YSGHDWH','ZCS');
yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username,$timetype);

//14
$fileName = '教辅当月已结算-到货已结-更正';
$sql="Select YWBMBH,DH,MC,PFPCH,BZ,ZMY,ZSY,CBJ,TSLSH,SM,DJ,ZDQH,ZDXH,KFBH,JSLX,NF,NFHRQ,NJSRQ,JB,CWQRPC,YSDBRQ,ZCS,to_char(t_Month_fhjsb.DBRQ, 'YYYY-MM-DD HH24:MI:SS') as DBRQ,to_char(t_Month_fhjsb.PFRQ, 'YYYY-MM-DD HH24:MI:SS') as PFRQ ,to_char(t_Month_fhjsb.JSRQ, 'YYYY-MM-DD HH24:MI:SS') as JSRQ From t_Month_fhjsb Where jsrq>=Date'$start' And jsrq < Date'$end' And ywbmbh in ($admin_username)";
$row=$ora->query($sql);
$total = count($row);
$title = array('YWBMBH','DH','MC','PFPCH','BZ','DBRQ','PFRQ','ZMY','ZSY','CBJ','TSLSH','JSRQ','SM','DJ','ZDQH','ZDXH','KFBH','JSLX','NF','NFHRQ','NJSRQ','JB','CWQRPC','YSDBRQ','ZCS');
yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username,$timetype);

//15
$fileName = '教辅当月已结算-发货已结-到货';
$sql="Select cbny,ghdw,ghdwmc,YWBMBH,DH,MC,PFPCH,BZ,ZMY,ZSY,CBJ,TSLSH,SM,DJ,ZDQH,ZDXH,KFBH,JSLX,NF,NFHRQ,NJSRQ,JB,CWQRPC,YSDBRQ,ZCS,to_char(t_Month_fhjsb.DBRQ, 'YYYY-MM-DD HH24:MI:SS') as DBRQ,to_char(t_Month_fhjsb.PFRQ, 'YYYY-MM-DD HH24:MI:SS') as PFRQ ,to_char(t_Month_fhjsb.JSRQ, 'YYYY-MM-DD HH24:MI:SS') as JSRQ  From t_Month_fhjsb Where jsrq>=Date'$start' And jsrq < Date'$end' And ywbmbh in ($admin_username)";
$row=$ora->query($sql);
$total = count($row);
$title = array('CBNY','GHDW','GHDWMC','YWBMBH','DH','MC','PFPCH','BZ','DBRQ','PFRQ','ZMY','ZSY','CBJ','TSLSH','JSRQ','SM','DJ','ZDQH','ZDXH','KFBH','JSLX','NF','NFHRQ','NJSRQ','JB','CWQRPC','YSDBRQ','ZCS');
yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username,$timetype);

//16
$fileName = '教辅当月已结算-发货已结-退货';
$sql="Select * From t_Month_xtjsb Where jsrq>=Date'$start' And jsrq < Date'$end' And ywbmbh in ($admin_username)";
$row=$ora->query($sql);
$total = count($row);
$title = array('CBNY','GHDW','GHDWMC','YWBMBH','KFBH','DH','DM','PCH','THRQ','ZMY','ZSY','ZCB','TSLSH','JSRQ','JSLX','ZDQH','NF','NFHRQ','NJSRQ','JB','CWQRPC','ZCS','DJ','ID','BB','SM','ISBN','CBNY');
yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username,$timetype);

//17
$fileName = '教辅当月已结算-发货已结-更正';
$sql="Select * From t_Month_Fhgzjsb Where jsrq>=Date'$start' And jsrq < Date'$end' And ywbmbh in ($admin_username)";
$row=$ora->query($sql);
$total = count($row);
$title = array('CBNY','GHDW','GHDWMC','YWBMBH','KFBH','DH','DM','GZPCH','GZRQ','ZMY','ZSY','ZCB','TSLSH','JSRQ','BZ','JSLX','ZDQH','NF','NFHRQ','NJSRQ','JB','ZCS');
yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username,$timetype);

//18
$fileName = '教辅应付款-到货未结明细';
$sql="Select YWBMBH,GHDWH,MC,DHPCH,YSDJ,SSDH,ZDQH,ZDXH,SM,DJ,JXSSSL,MY,SYS,ZDPCH,JSRQ,JSLX,ZDPCH1,TSLSH,YSDBRQ,JCDRQ,KQBJ,JSGHDWH,YSGHDWH,FLOWID_DHMX,to_char(t_Month_Dhjsb.SHRQ, 'YYYY-MM-DD HH24:MI:SS') as SHRQ,to_char(t_Month_Dhjsb.YSRQ, 'YYYY-MM-DD HH24:MI:SS') as YSRQ  From t_Month_Dhjsb Where ywbmbh in ($admin_username) And jsrq Is Null";
$row=$ora->query($sql);
$total = count($row);
$title = array('YWBMBH','GHDWH','MC','DHPCH','SHRQ','YSDJ','SSDH','ZDQH','ZDXH','SM','DJ','JXSSSL','MY','SYS','ZDPCH','JSRQ','JSLX','ZDPCH1','TSLSH','YSRQ','YSDBRQ','JCDRQ','KQBJ','JSGHDWH','YSGHDWH','FLOWID_DHMX');
yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username,$timetype);

//19
$fileName = '教辅应付款-进退未结明细';
$sql="Select YWBMBH,GHDWH,MC,ID,SM,DJ,ZMY,ZSY,BZ,YWPCH,ZDPCH,JSRQ,JSLX,ZDQH,ZDXH,ZDPCH1,TSLSH,NF,NFHRQ,NJSRQ,JSGHDWH,YSGHDWH,ZCS,to_char(t_Month_jtjsb.DBRQ, 'YYYY-MM-DD HH24:MI:SS') as DBRQ From t_Month_jtjsb Where ywbmbh in ($admin_username) And jsrq Is Null";
$row=$ora->query($sql);
$total = count($row);
$title = array('YWBMBH','GHDWH','MC','DBRQ','ID','SM','DJ','ZMY','ZSY','BZ','YWPCH','ZDPCH','JSRQ','JSLX','ZDQH','ZDXH','ZDPCH1','TSLSH','NF','NFHRQ','NJSRQ','JSGHDWH','YSGHDWH','ZCS');
yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username,$timetype);

//20
$fileName = '教辅应收款-发货未结明细';
$sql="Select cbny,ghdw,ghdwmc,YWBMBH,DH,MC,PFPCH,BZ,ZMY,ZSY,CBJ,TSLSH,JSRQ,SM,DJ,ZDQH,ZDXH,KFBH,JSLX,NF,NFHRQ,NJSRQ,JB,CWQRPC,YSDBRQ,ZCS,to_char(t_Month_fhjsb.DBRQ, 'YYYY-MM-DD HH24:MI:SS') as DBRQ,to_char(t_Month_fhjsb.PFRQ, 'YYYY-MM-DD HH24:MI:SS') as PFRQ From t_Month_fhjsb Where ywbmbh in ($admin_username) And jsrq Is Null";
$row=$ora->query($sql);
$total = count($row);
$title = array('CBNY','GHDW','GHDWMC','YWBMBH','DH','MC','PFPCH','BZ','DBRQ','PFRQ','ZMY','ZSY','CBJ','TSLSH','JSRQ','SM','DJ','ZDQH','ZDXH','KFBH','JSLX','NF','NFHRQ','NJSRQ','JB','CWQRPC','YSDBRQ','ZCS');
yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username,$timetype);

//21
$fileName = '教辅应收款-销退未结明细';
$sql="Select cbny,ghdw,ghdwmc,YWBMBH,KFBH,DH,DM,PCH,ZMY,ZSY,ZCB,TSLSH,JSRQ,JSLX,ZDQH,NF,NFHRQ,NJSRQ,JB,CWQRPC,ZCS,DJ,to_char(t_Month_Xtjsb.THRQ, 'YYYY-MM-DD HH24:MI:SS') as THRQ From t_Month_Xtjsb Where ywbmbh in ($admin_username) And jsrq Is Null";
$row=$ora->query($sql);
$total = count($row);
$title = array('CBNY','GHDW','GHDWMC','YWBMBH','KFBH','DH','DM','PCH','THRQ','ZMY','ZSY','ZCB','TSLSH','JSRQ','JSLX','ZDQH','NF','NFHRQ','NJSRQ','JB','CWQRPC','ZCS','DJ');
yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username,$timetype);

//22
$fileName = '教辅应收款-更正未结明细';
$sql="Select YWBMBH,KFBH,DH,DM,GZPCH,ZMY,ZSY,ZCB,TSLSH,JSRQ,BZ,JSLX,ZDQH,NF,NFHRQ,NJSRQ,JB,ZCS,to_char(t_Month_fhgzjsb.GZRQ, 'YYYY-MM-DD HH24:MI:SS') as GZRQ From t_Month_fhgzjsb Where ywbmbh in ($admin_username) And jsrq Is Null";
$row=$ora->query($sql);
$total = count($row);
$title = array('YWBMBH','KFBH','DH','DM','GZPCH','GZRQ','ZMY','ZSY','ZCB','TSLSH','JSRQ','BZ','JSLX','ZDQH','NF','NFHRQ','NJSRQ','JB','ZCS');
yuebao($sql,$fileName,$title,$total,$yewugongsi,$admin_username,$timetype);



$yewugongsi = 3;//连锁公司
  
$ip='172.30.153.63/xhsddb';
$port='1521';
$user= 'dbsl';
$pass= 'dbsl';
$charset='utf8';
$ora=new oracle_admin($user,$pass,$ip,$charset);

$users = array('000001','000002','000003','000004','000005','000006','000007','000008','000009','000010','000011','000012','000013','000014');
for($i=0;$i<count($users);$i++){
	$fileName = '差异-更正差异';
	$sql="select 0 As zmy,Sum(cbj) As zsy,ywbmbh from t_xsls_ghdw_gz Where xsrq >= Date '$start' and xslx='2' And xsrq < Date '$end' AND YWBMBH = '$users[$i]'
 Group By ywbmbh";
	$row=$ora->query($sql);
	$total = count($row);
	$title = array('ZMY','ZSY','YWBMBH');
	yuebao($sql,$fileName,$title,$total,$yewugongsi,$users[$i],$timetype);
	
	$fileName = '差异-到货';
	$sql="Select 'dhyh' As lx,0 As zmy,Sum(zsy) As zsy,ywbmbh From t_Jjyhdkhz Where djrq>=Date'$start' And djrq< Date'$end' AND YWBMBH = '$users[$i]' Group By ywbmbh
Union All Select 'dhbl' As lx,Sum(zmy) As zmy,Sum(zsy) As zsy,ywbmbh From t_bldhdj Where dbrq>=Date'$start' And dbrq< Date'$end' AND YWBMBH = '$users[$i]' Group By ywbmbh";
	$row=$ora->query($sql);
	$total = count($row);
	$title = array('LX','ZMY','ZSY','YWBMBH');
	yuebao($sql,$fileName,$title,$total,$yewugongsi,$users[$i],$timetype);
	
	$fileName = '差异-发货';
	$sql="Select 'fhhz1' As lx,Sum(zmy),Sum(cbj),ywbmbh From t_fhhz1 a Where  Not Exists (Select 1 From t_fhhz Where a.pfpch = pfpch) And dbrq >= Date'$start' And dbrq < Date'$end' AND YWBMBH = '$users[$i]'
 Group By ywbmbh
Union All
Select 'fhhz2' As lx,Sum(zmy),Sum(cbj),ywbmbh From t_fhhz2 a Where  Not Exists (Select 1 From t_fhhz Where a.pfpch = pfpch) And dbrq >= Date'$start' And dbrq < Date'$end' AND YWBMBH = '$users[$i]'
 Group By ywbmbh
Union All
Select  'khthhz1' As lx,Sum(zmy),Sum(cbj),ywbmbh From t_khthhz1 a Where  Not Exists (Select * From T_KHTHHZ Where THPCH = a.THPCH)And djlx <>'YH'  And thrq >= Date'$start' And thrq < Date'$end' AND YWBMBH = '$users[$i]'
 Group By ywbmbh
Union All
Select  'khthhz2' As lx,Sum(zmy),Sum(cbj),ywbmbh From t_khthhz2 a Where  Not Exists (Select * From T_KHTHHZ Where THPCH = a.THPCH) And thrq >= Date'$start' And thrq < Date'$end' AND YWBMBH = '$users[$i]'
 Group By ywbmbh";
	$row=$ora->query($sql);
	$total = count($row);
	$title = array('LX','SUM(ZMY)','SUM(CBJ)','YWBMBH');
	yuebao($sql,$fileName,$title,$total,$yewugongsi,$users[$i],$timetype);
	
	set_time_limit(0);
	ini_set('memory_limit', '-1');
	ini_set("max_execution_time", "0");
	//$start_lskc = kc_month(); //日期格式必须为 201808
	$start_lskc = '201809'; //日期格式必须为 201808
	$fileName = '库存明细';
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
												 And ywbmbh = '$users[$i]') As 供货单位,
										(Select mc
												From t_ghdw
											 Where bh = (Select ghdwh
																		 From t_kcsm_ywbm
																		Where Id = t.id
																			And ywbmbh = '$users[$i]')) As 供货单位名称,
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
											 Where ywbmbh = '$users[$i]'
												 And tjny = '$start_lskc'
											 Group By Id, ywbmbh) t";
	$row=$ora->query($sql);
	$total = count($row);
	$title = array('业务部门编号','ID','ISBN','书名','版本','版本名称','册数','码洋','实洋','不含税实洋','定价','供货单位','供货单位名称','出版年月','印刷年月','版次','分类','分类编号','类别编号','类别');
	yuebao($sql,$fileName,$title,$total,$yewugongsi,$users[$i],$timetype);

	
	$fileName = '应付款-采购未结-明细';
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
           And a.ywbmbh Like '$users[$i]'
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
           And a.ywbmbh Like '$users[$i]'
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
           And a.ywbmbh Like '$users[$i]'
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
           And a.ywbmbh Like '$users[$i]'
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
           And a.ywbmbh Like '$users[$i]'
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
           And a.ywbmbh Like '$users[$i]'
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
           And a.ywbmbh Like '$users[$i]'
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
           And a.ywbmbh Like '$users[$i]'
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
           And a.ywbmbh Like '$users[$i]'
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
           And a.ywbmbh Like '$users[$i]'
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
           And a.ywbmbh Like '$users[$i]'
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
           And a.ywbmbh Like '$users[$i]'
           And a.djrq < Date '$end'";
		 //echo $sql_gc;exit;
						$ora->query($sql_gc);
	$sql="Select ysdj, dm, zpz, zcs, zmy, zsy, (zsy / (1 + (a.sl1 * 0.01))) As bhszsy,ywbmmc, bz, djlx,to_char(a.ysrq, 'YYYY-MM-DD HH24:MI:SS') as ysrq, sl1,to_char(a.dbrq, 'YYYY-MM-DD HH24:MI:SS') as dbrq
 From t_Yfzk_Sjdj a ";
	$row=$ora->query($sql);
	$total = count($row);
	$title = array('DBRQ','YSDJ','YSRQ','DM','ZPZ','ZCS','ZMY','ZSY','BHSZSY','YWBMMC','BZ','DJLX','SL1');
	yuebao($sql,$fileName,$title,$total,$yewugongsi,$users[$i],$timetype);

	$fileName = '应收款-发出未结-明细';
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
           And a.ywbmbh Like '$users[$i]'";
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
           And a.ywbmbh Like '$users[$i]'";
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
         Where a.ywbmbh Like '$users[$i]'
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
           And ywbmbh Like '$users[$i]'";
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
           And a.ywbmbh Like '$users[$i]'";
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
						$ora->query($sql_gc);

						$sql_gc = "Update t_yszk_sjdj a
       Set lb = (Select lb
                    From T_FHHZ1
                   Where pfpch = a.pfpch
                     And rownum = 1)
     Where lb Is Null
       And czybh = 1139";
						$ora->query($sql_gc);
					if($users[$i] == '000004'){
						$sql_gc = "Insert Into t_Yszk_Sjdj_Cy
            (dm, jsdm, ywbmmc, ywbmbh, pfpch, Id, dbrq, Sys, cbj, mxlb, djlb)
            Select a.dm, a.jsdm, a.ywbmmc, a.ywbmbh, a.pfpch, b.id, a.dbrq, b.sys, b.cbj,
                   c.lb, a.lb
              From t_Yszk_Sjdj a, t_fhmx b, t_kcsm c
             Where a.pfpch = b.pfpch
               And c.id = b.id
               And c.lb <> a.lb";
						$ora->query($sql_gc);
						$sql_gc = "Update t_Yszk_Sjdj_Cy t
           Set cy = ((cbj / (1 + (Select sl1 / 100 From t_lb Where bh = t.mxlb))) -
                     (cbj / (1 + (Select sl1 / 100 From t_lb Where bh = t.djlb))))";
						$ora->query($sql_gc);
						
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
 Where ywbmbh = '$users[$i]'";
	$row=$ora->query($sql);
	$total = count($row);
	$title = array('DBRQ','PFPCH','DM','JSDM','ZPZ','ZCS','ZMY','ZSY','CBJ','BHSZSY','BHSCBJ','YWBMMC','YWBMBH','BZ','DJLX','SL1','DQ');
	yuebao($sql,$fileName,$title,$total,$yewugongsi,$users[$i],$timetype);
	
	
	$fileName = '应付款月报';
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
						$ora->query($sql_gc);
						
						$sql_gc = "Insert Into t_yFzk_month_tmp_clj
        (BQZJmy, BQZJsy, ghdwh, ywbmbh, YWLX, QCRQ, QMRQ)
        Select Sum(zmy), Sum(zsy), ghdwh, ywbmbh, 'BL',  '',''
          From t_bldhdj t
         Where dbrq < Date '$end'
           And dbrq >= Date '$start'
         Group By ywbmbh, ghdwh";
						$ora->query($sql_gc);

						$sql_gc = "Insert Into t_yFzk_month_tmp_clj
        (BQZJmy, BQZJsy, ghdwh, ywbmbh, YWLX, QCRQ, QMRQ)
        Select Sum(zmy), Sum(zsy), ghdwh, ywbmbh, 'DH', '',''
          From t_dhdj_cf t
         Where dbrq < Date '$end'
           And dbrq >= Date '$start'
         Group By ywbmbh, ghdwh";
						$ora->query($sql_gc);
						$sql_gc = "Insert Into t_yFzk_month_tmp_clj
        (BQZJmy, BQZJsy, ghdwh, ywbmbh, YWLX, QCRQ, QMRQ)
        Select Sum(-zmy), Sum(-zsy), hybh, ywbmbh, 'TH',  '',''
          From t_hythhz t
         Where thrq < Date '$end'
           And thrq >= Date '$start'
         Group By ywbmbh, hybh";
						$ora->query($sql_gc);
						$sql_gc = "Insert Into t_yfzk_month_tmp_clj
        (bqzjmy, bqzjsy, ghdwh, ywbmbh, YWLX, QCRQ, QMRQ)
        Select 0, Sum(-zsy), ghdwh, ywbmbh, 'YH', '',''
          From t_Jjyhdkhz t
         Where djrq >= Date '$start'
           And djrq < Date '$end'
         Group By ywbmbh, ghdwh";
						$ora->query($sql_gc);

						$sql_gc = "Insert Into t_yFzk_month_rysj_clj
        (YWBMBH, GHDWH, QCMY, QCSY, BQZJMY, BQZJSY, BQJSMY, BQJSSY, QMMY, QMSY, QCRQ,
         QMRQ)
        Select YWBMBH, GHDWH, Sum(QCMY), Sum(QCSY), Sum(BQZJMY), Sum(BQZJSY), Sum(BQJSMY),
               Sum(BQJSSY), Sum(QMMY), Sum(QMSY), Date '$start', Date '$end'
          From t_yFzk_month_tmp_clj
         Where ghdwh <> 'L00099'
         Group By GHDWH, YWBMBH";
						$ora->query($sql_gc);


		$sql = "Select ghdwh,(Select mc From t_ghdw Where bh = a.ghdwh) As ghdwhmc,(Select dm From t_dm Where dh =a.ywbmbh) As  ywbmbh,a.qcmy,a.qcsy,a.bqzjmy,a.bqzjsy,a.bqjsmy,a.bqjssy,a.qmmy,a.qmsy,to_char(a.QMRQ, 'YYYY-MM-DD') as QMRQ,to_char(a.QCRQ, 'YYYY-MM-DD') as QCRQ	
 From t_yfzk_month_rysj_clj a where ywbmbh = '$users[$i]'";
	$row=$ora->query($sql);
	$total = count($row);
	$title = array('统计日期','统计年月','供货单位','业务部门','期初码洋','期初实洋','本期增加码洋','本期增加实洋','本期减少码洋','本期减少实洋','期末码洋','期末实洋');
	yuebao($sql,$fileName,$title,$total,$yewugongsi,$users[$i],$timetype);

	
	$fileName = '应付款月报含税率';
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
		$sql = "Select (Select dm From t_dm Where dh =a.ywbmbh) As YWBMBH,a.ghdwh,(Select mc From t_ghdw Where bh = a.ghdwh) As ghdwhmc,a.SL,a.QCMY,a.QCSY,a.BQZJMY,a.BQZJSY,a.BQJSMY,a.BQJSSY,a.QMMY,a.QMSY,to_char(a.QMRQ, 'YYYY-MM-DD') as QMRQ,to_char(a.QCRQ, 'YYYY-MM-DD') as QCRQ
		From t_yfzk_month_rysj_clj_SL a where ywbmbh = '$users[$i]'";
	$row=$ora->query($sql);
	$total = count($row);
	$title = array('统计日前','统计年月','业务部门','供货单位','税率%','期初码洋','期初实洋','本期增加码洋','本期增加实洋','本期减少码洋','本期减少实洋','期末码洋','期末实洋');
	yuebao($sql,$fileName,$title,$total,$yewugongsi,$users[$i],$timetype);

	
	$fileName = '应收款月报';
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
						$ora->query($sql_gc);
						$sql_gc = "Insert Into t_yszk_month_tmp_clj
        (qcmy, qcsy, qccbj, dh, ywbmbh, YWLX, QCRQ, QMRQ)
        Select Sum(zmy), Sum(zsy), Sum(NVL(cbj, 0)), DH, ywbmbh, 'HZ1', '',''
          From t_fhhz1 t
         Where dbrq < Date '$start'
         Group By ywbmbh, DH";
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
						$ora->query($sql_gc);
						
						$sql_gc = "Insert Into t_ySzk_month_tmp_clj
        (BQZJmy, BQZJsy, BQZJcbj, DH, ywbmbh, YWLX, QCRQ, QMRQ)
        Select Sum(zmy), Sum(zsy), Sum(NVL(cbj, 0)), DH, ywbmbh, 'FH', '',''
          From T_FHHZ t
         Where DBRQ < Date '$end'
           And DBRQ >= Date '$start'
         Group By ywbmbh, DH";
						$ora->query($sql_gc);

						$sql_gc = "Insert Into t_ySzk_month_tmp_clj
        (BQZJmy, BQZJsy, BQZJcbj, DH, ywbmbh, YWLX, QCRQ, QMRQ)
        Select Sum(zmy), Sum(zsy), Sum(NVL(cbj, 0)), DH, ywbmbh, 'FH1', '',''
          From T_FHHZ1 t
         Where DBRQ < Date '$end'
           And DBRQ >= Date '$start'
           And Not Exists (Select 1 From t_fhhz Where pfpch = t.pfpch)
         Group By ywbmbh, DH";
						$ora->query($sql_gc);
						$sql_gc = "Insert Into t_ySzk_month_tmp_clj
        (BQZJmy, BQZJsy, BQZJcbj, DH, ywbmbh, YWLX, QCRQ, QMRQ)
        Select Sum(zmy), Sum(zsy), Sum(NVL(cbj, 0)), DH, ywbmbh, 'FH2', '',''
          From T_FHHZ2 t
         Where DBRQ < Date '$end'
           And DBRQ >= Date '$start'
           And Not Exists (Select 1 From t_fhhz Where pfpch = t.pfpch)
         Group By ywbmbh, DH";
						$ora->query($sql_gc);
						$sql_gc = "Insert Into t_ySzk_month_tmp_clj
        (BQZJmy, BQZJsy, BQZJcbj, DH, ywbmbh, YWLX, QCRQ, QMRQ)
        Select Sum(-zmy),
               Sum(-zsy), Sum(NVL(-cbj, 0)), DH, ywbmbh, 'TH', '',''
          From T_KHTHHZ t
         Where THRQ < Date '$end'
           And THRQ >= Date '$start'
         Group By ywbmbh, DH";
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
						$ora->query($sql_gc);

	$sql = "Select dh,(Select dm From t_dm Where dh =a.ywbmbh) As ywbmbh,a.qcmy,a.qcsy,a.qccbj,a.bqzjmy,a.bqzjsy,a.bqzjcbj,a.bqjsmy,a.bqjssy,a.bqjscbj,a.qmmy,a.qmsy,a.qmcbj,to_char(a.qcrq, 'YYYY-MM-DD') as qcrq
			,to_char(a.qmrq, 'YYYY-MM-DD') as qmrq From t_yszk_month_rysj_clj a where ywbmbh = '$users[$i]'";
	$row=$ora->query($sql);
	$total = count($row);
	$title = array('统计日期','统计年月','业务部门','客户名称','期初码洋','期初实洋','期初成本','本期增加码洋','本期增加实洋','本期增加成本','本期减少码洋','本期减少实洋','本期减少成本','期末码洋','期末实洋','期末成本');
	yuebao($sql,$fileName,$title,$total,$yewugongsi,$users[$i],$timetype);

	
	$fileName = '应收款月报含税率';
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
						$ora->query($sql_gc);
						$sql_gc = "Insert Into t_yszk_month_tmp_clj
        (qcmy, qcsy, qccbj, dh, ywbmbh, YWLX, QCRQ, QMRQ, SL)
        Select Sum(zmy), Sum(zsy), Sum(NVL(cbj, 0)), DH, ywbmbh, 'HZ1', '','', SL
          From t_fhhz1 t
         Where dbrq < Date '$start'
         Group By ywbmbh, DH, SL";
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
						$ora->query($sql_gc);
						
						$sql_gc = "Insert Into t_ySzk_month_tmp_clj
        (BQZJmy, BQZJsy, BQZJcbj, DH, ywbmbh, YWLX, QCRQ, QMRQ, SL)
        Select Sum(zmy), Sum(zsy), Sum(NVL(cbj, 0)), DH, ywbmbh, 'FH', '','', SL
          From T_FHHZ t
         Where DBRQ < Date '$end'
           And DBRQ >= Date '$start' And (nvl(zcs,0)<>0 Or nvl(zmy,0)<>0 Or nvl(zsy,0)<>0 Or nvl(cbj,0)<>0)
         Group By ywbmbh, DH, SL";
						$ora->query($sql_gc);

						$sql_gc = "Insert Into t_ySzk_month_tmp_clj
        (BQZJmy, BQZJsy, BQZJcbj, DH, ywbmbh, YWLX, QCRQ, QMRQ, SL)
        Select Sum(zmy), Sum(zsy), Sum(NVL(cbj, 0)), DH, ywbmbh, 'FH1', '','', SL
          From T_FHHZ1 t
         Where DBRQ < Date '$end'
           And DBRQ >= Date '$start'
           And Not Exists (Select 1 From t_fhhz Where pfpch = t.pfpch)
         Group By ywbmbh, DH, SL";
						$ora->query($sql_gc);
						$sql_gc = "Insert Into t_ySzk_month_tmp_clj
        (BQZJmy, BQZJsy, BQZJcbj, DH, ywbmbh, YWLX, QCRQ, QMRQ, SL)
        Select Sum(zmy), Sum(zsy), Sum(NVL(cbj, 0)), DH, ywbmbh, 'FH2', '','', SL
          From T_FHHZ2 t
         Where DBRQ < Date '$end'
           And DBRQ >= Date '$start'
           And Not Exists (Select 1 From t_fhhz Where pfpch = t.pfpch)
         Group By ywbmbh, DH, SL";
						$ora->query($sql_gc);
						$sql_gc = "Insert Into t_ySzk_month_tmp_clj
        (BQZJmy, BQZJsy, BQZJcbj, DH, ywbmbh, YWLX, QCRQ, QMRQ, SL)
        Select Sum(-zmy),
               Sum(-zsy), Sum(NVL(-cbj, 0)), DH, ywbmbh, 'TH', '','', SL
          From T_KHTHHZ t
         Where THRQ < Date '$end'
           And THRQ >= Date '$start'
         Group By ywbmbh, DH, SL";
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
						$ora->query($sql_gc);

	$sql = "Select dh, (Select dm From t_dm Where dh =t_yszk_month_rysj_clj_SL.ywbmbh) As ywbmbh, sl,qcmy, qcsy,qccbj, bqzjmy, bqzjsy, bqzjcbj, bqjsmy, bqjssy, qmmy, qmsy,qmcbj,
			 dqmy, dqsy, dqcbj, to_char(t_yszk_month_rysj_clj_SL.qcrq, 'YYYY-MM-DD') as qcrq
			,to_char(t_yszk_month_rysj_clj_SL.qmrq, 'YYYY-MM-DD') as qmrq From t_yszk_month_rysj_clj_SL where ywbmbh = '$users[$i]'";
	$row=$ora->query($sql);
	$total = count($row);
	$title = array('统计日期','统计年月','客户名称','业务部门','税率%','期初码洋','期初实洋','期初成本','本期增加码洋','本期增加实洋','本期增加成本','本期减少码洋',
	'本期减少实洋','期末码洋','期末实洋','期末成本','当前码洋','当前实洋','当前成本');
	yuebao($sql,$fileName,$title,$total,$yewugongsi,$users[$i],$timetype);

	
	$fileName = '本期流水';
	$sql="Select Sum(pkmy) 损益码洋, Sum(pksy)损益实样, Sum(bfmy) 报废码洋, Sum(bfsy) 报废实样, Sum(dhmy - thmy) 纯到货码洋, Sum(dhsy - thsy)纯到货实样,
		   Sum(fhmy - ftmy)纯发货码洋, Sum(fhcbj - thcbj)纯发货实洋
	  From t_Tscw_Pzjxc_day
	 Where ywbmbh = '$users[$i]'
		And rq >= Date '$start'
	   And rq < Date '$end'"; 

	$row=$ora->query($sql);
	$total = count($row);
	$title = array('损益码洋','损益实样','报废码洋','报废实样','纯到货码洋','纯到货实样','纯发货码洋','纯发货实洋');
	yuebao($sql,$fileName,$title,$total,$yewugongsi,$users[$i],$timetype);

	$fileName = '汇总数据';
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
						$res = $ora->query($sql_gc);
						$sql_gc = "Insert Into t_month_kchd
				(qcrq, qmrq, ywbmbh, zmy, zsy, cbj, ywlx, BJ)
				Select Date '$start' As qcrq, Date '$end' As qmrq, ywbmbh, -sum(ZMY) As zmy,
							 -Sum(zsy) As zsy, -Sum(NVL(ZSY, 0)) As cbj, '退货' As ywlx， '1'
					From t_hythhz t
				 Where thrq >= Date '$start'
					 And thrq < Date '$end'
				 Group By ywbmbh";
						$ora->query($sql_gc);
						$sql_gc = "Insert Into t_month_kchd
				(qcrq, qmrq, ywbmbh, zmy, zsy, cbj, ywlx, BJ)
				Select Date '$start' As qcrq, Date '$end' As qmrq, ywbmbh, Sum(ZMY) As zmy,
							 Sum(zsy) As zsy, Sum(NVL(cbj, 0)) As cbj, '销退' As ywlx， '1'
					From T_KHTHHZ t
				 Where thrq >= Date '$start'
					 And thrq < Date '$end'
				 Group By ywbmbh";
						$ora->query($sql_gc);
						$sql_gc = "Insert Into t_month_kchd
				(qcrq, qmrq, ywbmbh, zmy, zsy, cbj, ywlx，BJ)
				Select Date '$start' As qcrq, Date '$end' As qmrq, ywbmbh, Sum(QCMY) As zmy,
							 Sum(QCSY) As zsy, Sum(NVL(QCSY, 0)) As cbj, '初期' As ywlx， '1'
				
					From T_TSCW_PZJXC_MONTH
				 Where TJNY = TO_CHAR(Date '$start', 'YYYYMM')
				 Group By ywbmbh";
						$ora->query($sql_gc);
						$sql_gc = "Insert Into t_month_kchd
				(qcrq, qmrq, ywbmbh, zmy, zsy, cbj, ywlx，BJ)
				Select Date '$start' As qcrq, Date '$end' As qmrq, ywbmbh, -Sum(QmMY) As zmy,
							 -Sum(QmSY) As zsy, -Sum(NVL(QmSY, 0)) As cbj, '期末' As ywlx， '1'
					From T_TSCW_PZJXC_MONTH
				 Where TJNY = TO_CHAR(Date '$end' - 2, 'YYYYMM')
				
				 Group By ywbmbh";
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
						$ora->query($sql_gc);
						
						$sql_gc = "Insert Into t_month_kchd
				(qcrq, qmrq, ywbmbh, zmy, zsy, cbj, ywlx, BJ)
				Select Date '$start' As qcrq, Date '$end' As qmrq, ywbmbh, 0, Sum(-zsy), Sum(-zsy),
							 '到货优惠', '0'
					From t_Jjyhdkhz t
				 Where djrq >= Date '$start'
					 And djrq < Date '$end'
				 Group By ywbmbh";
						$ora->query($sql_gc);

						$sql_gc = "Insert Into t_month_kchd
				(qcrq, qmrq, ywbmbh, zmy, zsy, cbj, ywlx, BJ)
				Select Date '$start' As qcrq, Date '$end' As qmrq, ywbmbh, Sum(ZMY) As zmy,
							 Sum(zsy) As zsy, Sum(NVL(ZSY, 0)) As cbj, '到货补录' As ywlx， '0'
					From t_bldhdj t
				 Where DBrq >= Date '$start'
					 And DBrq < Date '$end'
				 Group By ywbmbh";
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
			$ora->query($sql_gc);
	$sql="Select to_char(t_month_kchd.qcrq, 'YYYY-MM-DD') as 期初日期, to_char(t_month_kchd.qmrq, 'YYYY-MM-DD') as  期末日期, ywbmbh 业务部门编号,zmy 总码洋,zsy 总实样,cbj 成本价,ywlx 类型 From  t_month_kchd Where ywbmbh = '$users[$i]' And bj = '1'"; 
	$row=$ora->query($sql);
	$total = count($row);
	$title = array('期初日期','期末日期','业务部门编号','总码洋','总实样','成本价','类型');
	yuebao($sql,$fileName,$title,$total,$yewugongsi,$users[$i],$timetype);
	
}































function yuebao($sql,$fileName,$title,$total,$yewugongsi,$user_hwbm,$timetype) 
{
       
		$host='172.30.153.63';
		$ip='172.30.153.63/xhsddb';
		$port='1521';
		$user= 'dbjczc';
		$pass= 'dbjczc';
		$charset='utf8';
		$ora=new oracle_admin($user,$pass,$ip,$charset);
		if($yewugongsi==3){
			$ip='172.30.153.63/xhsddb';
			$port='1521';
			$user= 'dbsl';
			$pass= 'dbsl';
			$charset='utf8';
			$ora=new oracle_admin($user,$pass,$ip,$charset);
		}
		
		$total = $total;
		$fileName = $fileName;
		$user_hwbm = $user_hwbm; 
		$begin = microtime(true);
	 
		$lujing = jcOptionDs($timetype,$yewugongsi); // 获取到文件存放地址，以及文件名拼接需要的时间类型（月份，季度，年度）
		//print_r($lujing);exit;
		$path = __DIR__.'/uploadfile/'.$lujing['path'];
		//echo $path; exit;
		
		if(!file_exists($path)) {
            mkdir( iconv('UTF-8','GBK',$path), 0777, true );
        }
		//exit('存在此路径');
		$filepath = $path.$user_hwbm.'-'.iconv("UTF-8","gbk",$lujing['file_path']).'-'.iconv("UTF-8","gbk",$fileName).'.csv';  //$timetype 为一月份  一季度  1-6
		//echo $filepath.'----';//exit;
		if($filepath){ 
			//$filepath = $path.$user_hwbm.'-'.iconv("UTF-8","gbk",$lujing['file_path']).'-'.$fileName.'.csv';  //$timetype 为一月份  一季度  1-6
			unlink($filepath); //如果存在此文件，那就删掉，然后下载最新的
		}
		$filepath = $path.$user_hwbm.'-'.iconv("UTF-8","gbk",$lujing['file_path']).'-'.iconv("UTF-8","gbk",$fileName).'.csv';  //$timetype 为一月份  一季度  1-6

		$fp = fopen($filepath,"a"); //打开csv文件，如果不存在则创建
	 
		if($fileName=='库存明细' || $fileName=='应付款-采购未结-明细' || $fileName=='应收款-发出未结-明细' ){
			$nums = 20000 ;
		}else{
			$nums = 5000 ;
		}
	$step = ceil($total / $nums);
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
						if($fileName == '教辅配发明细'){
							$row['YWBMBH'] = iconv('UTF-8', 'GBK', $v['YWBMBH']);
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
			unset($row2);
			ob_flush();
			flush();
		}
	}
}



 /**用于定时任务测试
 *导出到服务器指定位置
 * jcOption() 字母c后面是大写的 o不是零
 * $timestype 直接传递过来的是 eg:一月份  一季度  1-6
 * $yewugongsi 业务公司类型  1教辅  2 教材  3连锁
 */

 function jcOptionDs($timestype,$yewugongsi){
	$a1 = 'csv/';
	if($yewugongsi==1){
		$a2 = 'jf/';
	}elseif($yewugongsi==2){
		$a2 = 'jc/';
	}elseif($yewugongsi==3){
		$a2 = 'ls/';
	}
	$a3 = date('Y').'/';
	
	if($timestype=='一月份'){
		$month = 1;
		$a4 = 'Y'.$a3; //类似 Y2018 J2018 N2018
		$a5 = date('Y').'0'.$month.'/'; //会自动忽略0
		$path = $a1.$a2.$a3.$a4.$a5;
		$file_path  = date('Y').'0'.$month;//对于月份的来说，返回的201801类似这样的文件拼接名字。
		return array('path'=>$path,'file_path'=>$file_path);
	}
	if($timestype=='二月份'){
		$month = 2;
		$a4 = 'Y'.$a3; //类似 Y2018 J2018 N2018
		$a5 = date('Y').'0'.$month.'/'; //会自动忽略0
		$path = $a1.$a2.$a3.$a4.$a5;
		$file_path  = date('Y').'0'.$month;//对于月份的来说，返回的201801类似这样的文件拼接名字。
		return array('path'=>$path,'file_path'=>$file_path);
	}
	if($timestype=='三月份'){
		$month = 3;
		$a4 = 'Y'.$a3; //类似 Y2018 J2018 N2018
		$a5 = date('Y').'0'.$month.'/'; //会自动忽略0
		$path = $a1.$a2.$a3.$a4.$a5;
		$file_path  = date('Y').'0'.$month;//对于月份的来说，返回的201801类似这样的文件拼接名字。
		return array('path'=>$path,'file_path'=>$file_path);
	}
	if($timestype=='四月份'){
		$month = 4;
		$a4 = 'Y'.$a3; //类似 Y2018 J2018 N2018
		$a5 = date('Y').'0'.$month.'/'; //会自动忽略0
		$path = $a1.$a2.$a3.$a4.$a5;
		$file_path  = date('Y').'0'.$month;//对于月份的来说，返回的201801类似这样的文件拼接名字。
		return array('path'=>$path,'file_path'=>$file_path);
	}
	if($timestype=='五月份'){
		$month = 5;
		$a4 = 'Y'.$a3; //类似 Y2018 J2018 N2018
		$a5 = date('Y').'0'.$month; //会自动忽略0
		$path = $a1.$a2.$a3.$a4.$a5;
		$file_path  = date('Y').'0'.$month;//对于月份的来说，返回的201801类似这样的文件拼接名字。
		return array('path'=>$path,'file_path'=>$file_path);
	}
	if($timestype=='六月份'){
		$month = 6;
		$a4 = 'Y'.$a3; //类似 Y2018 J2018 N2018
		$a5 = date('Y').'0'.$month.'/'; //会自动忽略0
		$path = $a1.$a2.$a3.$a4.$a5;
		$file_path  = date('Y').'0'.$month;//对于月份的来说，返回的201801类似这样的文件拼接名字。
		return array('path'=>$path,'file_path'=>$file_path);
	}
	if($timestype=='七月份'){
		$month = 7;
		$a4 = 'Y'.$a3; //类似 Y2018 J2018 N2018
		$a5 = date('Y').'0'.$month.'/'; //会自动忽略0
		$path = $a1.$a2.$a3.$a4.$a5;
		$file_path  = date('Y').'0'.$month;//对于月份的来说，返回的201801类似这样的文件拼接名字。
		return array('path'=>$path,'file_path'=>$file_path);
	}
	if($timestype=='八月份'){
		$month = 8;
		$a4 = 'Y'.$a3; //类似 Y2018 J2018 N2018
		$a5 = date('Y').'0'.$month.'/'; //会自动忽略0
		$path = $a1.$a2.$a3.$a4.$a5;
		$file_path  = date('Y').'0'.$month;//对于月份的来说，返回的201801类似这样的文件拼接名字。
		return array('path'=>$path,'file_path'=>$file_path);
	}
	if($timestype=='九月份'){
		$month = 9;
		$a4 = 'Y'.$a3; //类似 Y2018 J2018 N2018
		$a5 = date('Y').'0'.$month.'/'; //会自动忽略0
		$path = $a1.$a2.$a3.$a4.$a5;
		$file_path  = date('Y').'0'.$month;//对于月份的来说，返回的201801类似这样的文件拼接名字。
		return array('path'=>$path,'file_path'=>$file_path);
	}
	
	if($timestype=='十月份'){
		$month = 10;
		$a4 = 'Y'.$a3; //类似 Y2018 J2018 N2018
		$a5 = date('Y').$month.'/'; //会自动忽略0
		$path = $a1.$a2.$a3.$a4.$a5;
		$file_path  = date('Y').$month;//对于月份的来说，返回的201801类似这样的文件拼接名字。
		return array('path'=>$path,'file_path'=>$file_path);
	}
	if($timestype=='十一月份'){
		$month = 11;
		$a4 = 'Y'.$a3; //类似 Y2018 J2018 N2018
		$a5 = date('Y').$month.'/'; //会自动忽略0
		$path = $a1.$a2.$a3.$a4.$a5;
		$file_path  = date('Y').$month;//对于月份的来说，返回的201801类似这样的文件拼接名字。
		return array('path'=>$path,'file_path'=>$file_path);
	}
	if($timestype=='十二月份'){
		$month = 12;
		$a4 = 'Y'.$a3; //类似 Y2018 J2018 N2018
		$a5 = date('Y').$month.'/'; //会自动忽略0
		$path = $a1.$a2.$a3.$a4.$a5;
		$file_path  = date('Y').$month;//对于月份的来说，返回的201801类似这样的文件拼接名字。
		return array('path'=>$path,'file_path'=>$file_path);
	}
	if($timestype=='一季度'){
		$a4 = 'J'.$a3; //类似 Y2018 J2018 N2018
		$path = $a1.$a2.$a3.$a4;
		$file_path  = $timestype;
		return array('path'=>$path,'file_path'=>$file_path);
	}
	if($timestype=='二季度'){
		$a4 = 'J'.$a3; //类似 Y2018 J2018 N2018
		$path = $a1.$a2.$a3.$a4;
		$file_path  = $timestype;
		return array('path'=>$path,'file_path'=>$file_path);
	}
	if($timestype=='三季度'){
		$a4 = 'J'.$a3; //类似 Y2018 J2018 N2018
		$path = $a1.$a2.$a3.$a4;
		$file_path  = $timestype;
		return array('path'=>$path,'file_path'=>$file_path);
	}
	if($timestype=='四季度'){
		$a4 = 'J'.$a3; //类似 Y2018 J2018 N2018
		$path = $a1.$a2.$a3.$a4;
		$file_path  = $timestype;
		return array('path'=>$path,'file_path'=>$file_path);
	}
	if($timestype=='1-6'){
		$a4 = 'N'.$a3; //类似 Y2018 J2018 N2018
		$path = $a1.$a2.$a3.$a4;
		$file_path  = $timestype;
		return array('path'=>$path,'file_path'=>$file_path);
	}
	if($timestype=='7-12'){
		$a4 = 'N'.$a3; //类似 Y2018 J2018 N2018
		$path = $a1.$a2.$a3.$a4;
		$file_path  = $timestype;
		return array('path'=>$path,'file_path'=>$file_path);
	}
	if($timestype=='1-12'){
		$a4 = 'N'.$a3; //类似 Y2018 J2018 N2018
		$path = $a1.$a2.$a3.$a4;
		$file_path  = $timestype;
		return array('path'=>$path,'file_path'=>$file_path);
	}
	

 } 


function jiyuefen(){
	$a = date("m",time());
	switch ($a){
		case $a=='1':
		  $month = '十二月份';
		  break;
		case $a=='2':
		  $month = '一月份';
		  break;
		case $a=='3':
		  $month ='二月份';
		  break;
		case $a=='4':
		  $month = '三月份';
		  break;
		case $a=='5':
		  $month = '四月份';
		  break;
		case $a=='6':
		  $month = '五月份';
		  break;
		case $a=='7':
		  $month = '六月份';
		  break;
		case $a=='8':
		  $month = '七月份';
		  break;
		case $a=='9':
		  $month = '八月份';
		  break;
		case $a=='10':
		  $month = '九月份';
		  break;
		case $a=='11':
		  $month = '十月份';
		  break;
		case $a=='12':
		  $month = '十一月份';
		  break;
		default:
		  $month = '一月份';
	}
	return($month);

}

 /**
 * Function dy_month
 * 时间转换
 * @param 获取前一个月时间的第一天，下个月的第一天
 */
 function dy_month() {
	$y = date("Y",time());
	$m = date("m",time());
	$d = date("d",time());
	$t0 = date('t');                           // 本月一共有几天
	$start_time  = mktime(0,0,0,$m,1,$y);     // 创建当月开始时间  $m-1获取前一个月的
	$time = array('start_time'=>$start_time);
	return($time);

 }

 /**
 * Function kc_month
 * 时间转换
 * @param 获取前一个月时间的  格式为 201808
 */
 function kc_month() {
	$y = date("Y",time());
	$m = date("m",time())-1;
	if($m<=9 && $m != 1){
		$riqi = $y.'0'.$m;
	}elseif($m == 1){
		$riqi = $y.'12';
	}elseif($m>9){
		$riqi = $y.$m;
	}
	return($riqi);

 }






























?>