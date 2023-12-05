<?php

require_once (JPATH_ROOT . '/libraries/xlsxwriter/xlsxwriter.class.php');

$user = JFactory::getUser()->id;
// if($user == 84 || $user == 5015 || $user == 4873 || $user == 2818 || $user == 2818 || $user == 1169){}else{
//     echo "Acesso negado.";
//     die;
// }
$where = "WHERE eu.id NOT IN (SELECT id FROM edu_users WHERE name LIKE '%test%' OR name LIKE '%super%' OR email LIKE '%biup%' OR email LIKE '%test%' OR email LIKE '%plusoft%' OR email LIKE '%edusense%' or email like '%somadev%' or email like '%desativado%' or name like '%desativado%') "; // declarando variavel where para pode usar na query de forma condicionalmente

if ($_GET['email']){
    $email = $_GET['email'];
    if ( $where == "" ){
        $where = "WHERE eu.email = '$email' ";
    }else{
        $where .= "AND eu.email = '$email' ";
    } 
}
if ($_GET['imersao']){
    $imersao = "'";
    $imersao .= str_replace(";", "','", $_GET['imersao']);
    $imersao .= "'";
    if ( $where == "" ){
        $where = "WHERE eu.imersao IN ($imersao) ";
    }else{
        $where .= "AND eu.imersao IN ($imersao) ";
    } 
}

if($_GET['begindate']){
    $begindate = $_GET['begindate'];
    // setando uma data final e inicial para caso venha vazio
    if (!$date_end){
        $date_end = '3000-12-30';
    }
    if ( $where == "" ){
        $where = "WHERE eguts.created_at BETWEEN '$begindate' AND '$date_end' ";
    }else{
        $where .= "AND eguts.created_at BETWEEN '$begindate' AND '$date_end' ";
    } 
}
if($_GET['trail']){
    $trail = "'";
    $trail .= str_replace(";", "','", $_GET['trail']);
    $trail .= "'";

    if ( $where == "" ){
        $where = "WHERE egt.id IN ($trail) ";
    }else{
        $where .= "AND egt.id IN ($trail) ";
    } 
}

if($_GET['status_trail']){
    $status_trail = "'";
    $status_trail .= str_replace(";", "','", $_GET['status_trail']);
    $status_trail .= "'";
    if ( $where == "" ){
        $where = "WHERE eguts.status IN ($status_trail) ";
    }else{
        $where .= "AND eguts.status IN ($status_trail) ";
    } 
}


$db = JFactory::getDBO();
$sql = 
    "SELECT 
    eu.email,
    CASE   
        WHEN eu.imersao = '0' THEN 'BIFLOW'
        WHEN eu.imersao = '' then ''
        ELSE eu.imersao 
    END AS 'Grupo' ,
    egt.id,
    egt.name,
    CASE
        WHEN eguts.status = 'incomplete' THEN 'Incompleto'
        WHEN eguts.status = 'completed' THEN 'Completo'
        WHEN eguts.status = 'not attempted' THEN 'Não Iniciado'
    END AS 'Status',
    eguts.created_at AS 'Acesso',
    CASE
        WHEN eguts.status != 'completed' THEN '' 
    ELSE eguts.updated_at
    END AS 'Conclusao'
    FROM
        edu_users eu 
    INNER JOIN edu_guru_user_trail_stats eguts
        ON eu.id = eguts.user_id 
    INNER JOIN edu_guru_trail egt 
        ON eguts.trail_id = egt.id ".$where;

        // echo json_encode($sql);
        // die();

$db->setQuery($sql);
$result = $db->loadObjectList();

$writer = new XLSXWriter();

$styles1 = array( 'font'=>'Arial','font-size'=>11,'font-style'=>'bold', 'fill'=>'#DDD', 'halign'=>'center', 'border'=>'bottom','border-style'=>'thin');
$styles2 = array( 'font'=>'Arial','font-size'=>11,'font-style'=>'regular', 'halign'=>'left', 'border'=>'top,right,bottom,left','border-style'=>'thin', 'height'=>'13');

$writer->writeSheetHeader(
    'Relatorio', array(
        'E-mail'=>'string',
        'Grupo'=>'string',                                                                       
        'ID aula'=>'string',
        'Nome'=>'string',
        'Status'=>'string',                                                                         
        'Primeiro acesso'=>'datetime',
        'Data conclusao'=>'datetime'
    ),
    [
        'auto_filter'=>true,
        'widths'=>[35,25,15,30,18,22, 22],
        'freeze_rows'=>1, 'freeze_columns'=>0,
        $styles1, $styles1, $styles1, $styles1, $styles1, $styles1, $styles1
    ]
);

foreach($result as $resultUser){							
    $writer->writeSheetRow('Relatorio', $rowdata = array(                                                                                               
        $resultUser->email,
        $resultUser->Grupo,                            
        $resultUser->id,                            
        $resultUser->name,                            
        $resultUser->Status,                            
        $resultUser->Acesso, 
        $resultUser->Conclusao
    ), $styles2);   
}

$now = date("d_m_Y");
$filename = "relatorio_cursos-$now.xlsx";

$writer->writeToFile($filename);

if (file_exists($filename)) {
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="'.basename($filename).'"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($filename));
    
    readfile($filename);
    unlink($filename);
    header('Set-Cookie: fileDownload=true; path=/',time()-20);
    exit;
} 
die;
?>