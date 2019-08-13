<?php
 if ($_GET["ud"]=='1')
	{
	 $query = 'DELETE FROM device WHERE id='.$_GET["id"];
	 $a = mysql_query ($query,$i);
        }
 if ($_POST["conf"]=='1')
	{
	 $query = 'SELECT * FROM device';
	 if ($r = mysql_query ($query,$i))
	 while ($ur = mysql_fetch_row ($r))
		{ 
		 $ust=0;
		 $pthh='ust'.$ur[1];
		 if ($_POST[$pthh]=='on') $ust=1;
		 if ($ust!=$ur[21])
		    {
		     $query = 'UPDATE device SET devtim=devtim,ust='.$ust.' WHERE id='.$ur[1];
		     //echo $query.'<br>';
		     mysql_query ($query,$i);
		    }

		 $xml=0;
		 $pthh='xml'.$ur[1];
		 if ($_POST[$pthh]=='on') $xml=1;
		 if ($xml!=$ur[18])
		    {
		     $query = 'UPDATE device SET devtim=devtim,req='.$xml.' WHERE idd='.$ur[1];
		     //echo $query.'<br>';
		     mysql_query ($query,$i);
		    }		 
		 $pn1=$pn2=$pn3=$pn4=$pn5=0;
		 $n1=$ur[1].'_n1'; $ret=sscanf ($_POST[$n1],"%d.%d.%d",$f1,$f2,$f3); if ($ret==3) $pn1=$f1*0x10000+$f2*0x100+$f3;
		 $n2=$ur[1].'_n2'; $ret=sscanf ($_POST[$n2],"%d.%d.%d",$f1,$f2,$f3); if ($ret==3) $pn2=$f1*0x10000+$f2*0x100+$f3;
		 $n3=$ur[1].'_n3'; $ret=sscanf ($_POST[$n3],"%d.%d.%d",$f1,$f2,$f3); if ($ret==3) $pn3=$f1*0x10000+$f2*0x100+$f3;
		 $n4=$ur[1].'_n4'; $ret=sscanf ($_POST[$n4],"%d.%d.%d",$f1,$f2,$f3); if ($ret==3) $pn4=$f1*0x10000+$f2*0x100+$f3;
		 $n5=$ur[1].'_n5'; $ret=sscanf ($_POST[$n5],"%d.%d.%d",$f1,$f2,$f3); if ($ret==3) $pn5=$f1*0x10000+$f2*0x100+$f3;
		 if ($_POST[$n1])
		    {
		     $query = 'UPDATE dev_mer SET adrr1='.$pn1.',adrr2='.$pn2.',adrr3='.$pn3.',adrr4='.$pn4.',adrr5='.$pn5.' WHERE device='.$ur[1];
		     echo $query.'<br>';
		     mysql_query ($query,$i);
		    }
        	} 
	}

 if ($_POST["frm"]=='1')
	{
	 if ($_POST["nid"]=='1')
		{ 
		 $query = 'SELECT startid FROM devicetype WHERE ids='.$_POST["n2"];
		 if ($a = mysql_query ($query,$i))
		 if ($uy = mysql_fetch_row ($a)) $startidd=$uy[0];

		 $query = 'SELECT MAX(idd) FROM device WHERE type='.$_POST["n2"];
		 if ($a = mysql_query ($query,$i))
		 if ($uy = mysql_fetch_row ($a)) $idd=$uy[0]+1;
		 if ($idd<100) $idd=$startidd+1;

                 if ($_POST["n5"]!='') $idd=$_POST["n5"];
		 if ($_POST["n9"]>0) $adr=$_POST["n9"]; else $adr=0x100;

		 if ($_POST["n1"]=='' || $_POST["n1"]=='1') $_POST["n1"]=1;
		 if ($_POST["n1"]>='1')
			{
			 for ($dd=1; $dd<=$_POST["n1"]; $dd++)
				{
			 	 if ($_POST["n2"]==1) { $name='BIT (flat='.$_POST["n10"].',str='.$_POST["n11"].')'; }
			 	 if ($_POST["n2"]==2) { $name='2IP (flat='.$_POST["n10"].')'; }
			 	 if ($_POST["n2"]==4) { $name='MEE (flat='.$_POST["n10"].')'; }
			 	 if ($_POST["n2"]==5) { $name='IRP (strut='.$_POST["n11"].')'; }
			 	 if ($_POST["n2"]==6) { $name='LK (ent='.$_POST["n11"].',str='.$_POST["n11"].')'; }
			 	 if ($_POST["n2"]==11) $name='Tekon';
				 if ($_POST["n4"]!='') $name=$_POST["n4"];

				 $query = 'INSERT INTO device SET name=\''.$name.'\',type=\''.$_POST["n2"].'\',interface=\''.$_POST["n3"].'\',
						  idd=\''.$idd.'\',protocol=\''.$_POST["n6"].'\',port=\''.$_POST["n7"].'\',
						  speed=\''.$_POST["n8"].'\',adr=\''.$adr.'\',flat=\''.$_POST["n10"].'\'';
				 //echo $query.'<br>';
				 $a = mysql_query ($query,$i);
                                 if ($_POST["n2"]==1)
					{
					 $query = 'INSERT INTO dev_bit SET device=\''.$idd.'\',rf_int_interval=\'3600\',ids_lk=\''.$_POST["n11"].'\',
						  ids_module=\''.$adr.'\',meas_interval=\'60\',integ_meas_cnt=\'60\',
						  flat_number=\''.$_POST["n10"].'\',strut_number=\''.$_POST["n12"].'\'';
					 //echo $query.'<br>';
					 $a = mysql_query ($query,$i);

					 $query = 'INSERT INTO channels SET name=\'Интегральное значение энтальпии\',device=\''.$idd.'\',opr=1,prm=1,pipe=0,shortname=\'Hинт,'.$adr.'\'';
					 $a = mysql_query ($query,$i);
					 $query = 'INSERT INTO channels SET name=\'Накопительное значение энтальпии\',device=\''.$idd.'\',opr=1,prm=1,pipe=1,shortname=\'Hнак,'.$adr.'\'';
					 $a = mysql_query ($query,$i);
					 $query = 'INSERT INTO channels SET name=\'Среднее значение температуры\',device=\''.$idd.'\',opr=1,prm=4,pipe=0,shortname=\'Тср,'.$adr.'\'';
					 $a = mysql_query ($query,$i);
					}
				 $idd++; $adr++;
				}
			}
		}
	}
?>

	<table class="columnLayout" style="width:940px">
	<tbody><tr>
	<td class="centercol2">
	<div class="bar firstBar">
	<?
	 print '<h2><a class="grey" href="index.php?sel=devices">Devices</a></h2>';
	?>
	<span class="link"><img src="files/dbl_arrow.gif" alt="*" width="19" height="12"></span>
	</div>

	<div id="divFutures" class="mpbox">
	<div>
	<div class="xscroll">
	<table class="datatable_simple js" data-largetable="500" data-extrafields="" data-pagesize="100" width="100%" border="0" cellpadding="1" cellspacing="1">
	<thead><tr class="datatable_header">
	<?php	
	 print '<th class="ds_name sort_none" align="center">ID</th>
		<th class="ds_last sort_none" align="center">Name</th>
	        <th class="ds_change sort_none" align="center">Type</th>
	        <th class="ds_change sort_none" align="center">Obj/Flat</th>
	        <th class="ds_change sort_none" align="center">Ust</th>
	        <th class="ds_change sort_none" align="center">XML</th>
	        <th class="ds_change sort_none" align="center">Route 1</th>
	        <th class="ds_change sort_none" align="center">Route 2</th>
	        <th class="ds_change sort_none" align="center">Route 3</th>
	        <th class="ds_change sort_none" align="center">Route 4</th>
	        <th class="ds_change sort_none" align="center">Route 5</th>
	        <th class="ds_change sort_none" align="center"></th>';
	?>
	</tr></thead>
	<tbody>
	<form name="frm1" method="post" action="index.php?sel=protocol">
	<?php
	 if ($_GET["type"]=='') $query = 'SELECT * FROM device';
	 else $query = 'SELECT * FROM device WHERE type='.$_GET["type"];
	 if ($a = mysql_query ($query,$i))
	 while ($uy = mysql_fetch_row ($a))
		{
		 print '<tr class="">
			<td class="ds_name qb_shad" align="left" nowrap="nowrap">'.$uy[1].'</td>
			<td class="ds_name qb_shad" align="left" nowrap="nowrap"><a href="index.php?sel=channels&device='.$uy[1].'">'.$uy[20].'</a></td>';

		$query = 'SELECT * FROM devicetype WHERE ids='.$uy[8];
	 	if ($a2 = mysql_query ($query,$i))
		if ($uy2 = mysql_fetch_row ($a2)) print '<td class="ds_name qb_shad" align="left" nowrap="nowrap">'.$uy2[1].'</td>';

		print '<td style="" class="ds_last qb_shad" align="center">'.$uy[10].'</td>';

		print '<td style="" class="ds_last qb_shad" align="center"><input type="checkbox" id="ust'.$uy[1].'" name="ust'.$uy[1].'" ';
	        if ($uy[21]) print 'checked';
		print '></td>';

		print '<td style="" class="ds_last qb_shad" align="center"><input type="checkbox" id="xml'.$uy[1].'" name="xml'.$uy[1].'" ';
	        if ($uy[18]) print 'checked';
		print '></td>';

		$query = 'SELECT * FROM objects WHERE id='.$uy[24];
		if ($a2 = mysql_query ($query,$i))
		if ($uy2 = mysql_fetch_row ($a2)) $name=$uy2[1];

		$query = 'SELECT * FROM dev_mer WHERE device='.$uy[1];
		if ($a2 = mysql_query ($query,$i))
		while ($uy2 = mysql_fetch_row ($a2))
		    {
		     $route1=$route2=$route3=$route4=$route5='';
		     $route1=sprintf ("%d.%d.%d",$uy2[4]/0x10000,($uy2[4]&0xff00)/0x100,($uy2[4]&0xff));
		     if ($uy2[5]>0) $route2=sprintf ("%d.%d.%d",$uy2[5]/0x10000,($uy2[5]&0xff00)/0x100,($uy2[5]&0xff));
		     if ($uy2[6]>0) $route3=sprintf ("%d.%d.%d",$uy2[6]/0x10000,($uy2[6]&0xff00)/0x100,($uy2[6]&0xff));
		     if ($uy2[7]>0) $route4=sprintf ("%d.%d.%d",$uy2[7]/0x10000,($uy2[7]&0xff00)/0x100,($uy2[7]&0xff));
		     if ($uy2[8]>0) $route5=sprintf ("%d.%d.%d",$uy2[8]/0x10000,($uy2[8]&0xff00)/0x100,($uy2[8]&0xff));
		     print '<td style="" class="ds_last qb_shad" align="center"><input name="'.$uy2[2].'_n1" size=8 class=log style="height:12px" value="'.$route1.'"></td>';
		     print '<td style="" class="ds_last qb_shad" align="center"><input name="'.$uy2[2].'_n2" size=8 class=log style="height:12px" value="'.$route2.'"></td>';
		     print '<td style="" class="ds_last qb_shad" align="center"><input name="'.$uy2[2].'_n3" size=8 class=log style="height:12px" value="'.$route3.'"></td>';
		     print '<td style="" class="ds_last qb_shad" align="center"><input name="'.$uy2[2].'_n4" size=8 class=log style="height:12px" value="'.$route4.'"></td>';
		     print '<td style="" class="ds_last qb_shad" align="center"><input name="'.$uy2[2].'_n5" size=8 class=log style="height:12px" value="'.$route5.'"></td>';
		    }
		//print '<td style="" class="ds_last qb_shad" align="center"><a href="index.php?sel=devices&ud=1&id='.$uy[0].'"><img border="0" src="files/delete.png"></td>';
		print '</tr>';
		}
	?>
	</tbody></table>
	<table><tr><td><input name="conf" type="submit" value="change" style="font-size:10px"><input name="conf" size=1 style="height:1;width:1;visibility:hidden" value="1"></td></tr></table>
	</form>

</div>
	</div>
	</div>
	</td>
	</tr>
</tbody></table>