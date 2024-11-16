<?php

$html =
'<table cellpadding="5" cellspacing="0" width="60%"> 
<tr>
<td colspan="3" align="center" style="font-size:22px"><b>Ventas de Seguros</b></td>
</tr>
<tr>
<td colspan="3" align="center" style="font-size:15px">
<b>Desde</b>
' .
$fdesdeRep .
'
<b>Hasta</b>
' .
$fhastaRep .
'
</td>
</tr>
<tr>
<td style="background-color:#D11B1E; font-size:15px; color:#FFF"><b>Nombre</b></td>
<td style="background-color:#D11B1E; font-size:15px; color:#FFF"><b>Monto</b></td>
<td style="background-color:#D11B1E; font-size:15px; color:#FFF"><b>Costo</b></td>
</tr>';
$sq = mysql_query("SELECT id FROM  seguros WHERE id !='' ");

$Totalmonto = 0;
$Totalcosto = 0;

while ($p = mysql_fetch_array($sq)) {
if ($UserData[$p['id']]['monto'] > 0) {
    $Totalmonto += $UserData[$p['id']]['monto'];
    $Totalcosto += $UserData[$p['id']]['costo'];

$html .=
    '<tr>
<td style="border-bottom:solid 1px #E3E3E3; background-color:#F2F2F2;">' .
    NomAseg($p['id']) .
    '</td>
<td style="border-bottom:solid 1px #E3E3E3; background-color:#F2F2F2;">$RD ' .
    FormatDinero($UserData[$p['id']]['monto']) .
    '</td>
<td style="border-bottom:solid 1px #E3E3E3; background-color:#F2F2F2;">$RD ' .
    FormatDinero($UserData[$p['id']]['costo']) .
    '</td>
</tr>';
}
}

$html .=
' <tr>
<td>&nbsp;</td>
<td  style="border-bottom:solid 1px #E3E3E3; background-color:#F2F2F2;"><b>$RD ' .
FormatDinero($Totalmonto) .
'</b></td>
<td  style="border-bottom:solid 1px #E3E3E3; background-color:#F2F2F2;"s><b>$RD ' .
FormatDinero($Totalcosto) .
'</b></td>
</tr>
</table>
<br><br>';

$html .=
'<table cellpadding="5" cellspacing="0" width="60%" id="servopc" style="display:none;"> 
<tr>
<td colspan="3" align="center" style="font-size:22px"><b>Ventas de Servicios Opcionales</b></td>
</tr>
<tr>
<td colspan="3" align="center" style="font-size:15px">
<b>Desde</b>
' .
$fdesdeRep .
'
<b>Hasta</b>
' .
$fhastaRep .
'
</td>
</tr>
<tr>
<td style="background-color:#D11B1E; font-size:15px; color:#FFF"><b>Nombre</b></td>
<td style="background-color:#D11B1E; font-size:15px; color:#FFF"><b>Monto</b></td>
<td style="background-color:#D11B1E; font-size:15px; color:#FFF"><b>Costo</b></td>
</tr>';
$sq = mysql_query("SELECT id FROM  servicios WHERE id !='' ");

$TotalmontoSer = 0;
$TotalcostoSer = 0;

while ($p = mysql_fetch_array($sq)) {
$o = '0';
if ($UserData[$p['id']]['monto'] > 0) {
    $o++;

    $TotalmontoSer += $UserData[$p['id']]['monto'];
    $TotalcostoSer += $UserData[$p['id']]['costo'];
    $html .=
        ' <tr>
<td style="border-bottom:solid 1px #E3E3E3; background-color:#F2F2F2;">' .
        ServAdicHistory($p['id']) .
        '</td>
<td style="border-bottom:solid 1px #E3E3E3; background-color:#F2F2F2;">$RD ' .
        FormatDinero($UserData[$p['id']]['monto']) .
        '</td>
<td style="border-bottom:solid 1px #E3E3E3; background-color:#F2F2F2;">$RD ' .
        FormatDinero($UserData[$p['id']]['costo']) .
        '</td>
</tr>';
}
}

if ($o > 0) {
echo '<script> $("#servopc").show(0); </script>';
} else {
echo '<script> $("#servopc").hide(0); </script>';
}

$html .=
'<tr>
<td>&nbsp;</td>
<td  style="border-bottom:solid 1px #E3E3E3; background-color:#F2F2F2;"><b>$RD ' .
FormatDinero($TotalmontoSer) .
'</b></td>
<td  style="border-bottom:solid 1px #E3E3E3; background-color:#F2F2F2;"><b>$RD ' .
FormatDinero($TotalcostoSer) .
'</b></td>
</tr>
</table>';