<?php

declare(strict_types=1);
while (ob_get_level()) {
    ob_end_clean();
}
ob_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
//session_start();

include(__DIR__ . '/../../../WEB/functions/usePDOConnection.php');
include(__DIR__ . '/../../../WEB/libs/pdf/cezpdf/src/Cezpdf.php');

$pdo = $getPDOToOp();

//if (!isset($_SESSION['IDUsuario'])) {
//    echo "No hay sesión activa";
//    exit;
//}

//echo ('<br/> This post content <br/>'.var_export($_POST,true));
//return;
//$input = json_decode(file_get_contents("php://input"), true);
//$id_usuario = $_SESSION['IDUsuario'];
//$idProyecto = 2025040819003013;//$_POST['id_proyect'] ?? null;
if(isset($_POST['id_project']))
    $idProyecto = $_POST['id_project'];
else
    $idProyecto = $_GET['id_project'];

function toUtf8($text) {
    return (mb_detect_encoding($text, 'UTF-8', true) ? $text : mb_convert_encoding($text, 'UTF-8'));
}

try {
    $stms_proyecto = $pdo->prepare("SELECT id, folio, fecha_creacion, project_detail FROM proyectos_cotizados WHERE id = :id_proyecto AND status = 'Activo'");
    $stms_proyecto->execute(['id_proyecto' => $idProyecto]);
    $proyectos = $stms_proyecto->fetchAll(PDO::FETCH_ASSOC);

    if (empty($proyectos)) {
        throw new Exception("No se encontró el proyecto especificado");
    }
    //echo ('<br/> This proyectos content <br/>'.var_export($proyectos,true));

    $pdf = new Cezpdf('a4', 'landscape');

    $pdf->allowedTags .= '|comment:.*?';
    $pdf->ezSetMargins(50, 30, 50, 50);
    $pdf->selectFont('Helvetica');
    $pdf->ezStartPageNumbers(710, 35, 10, '', '{PAGENUM} de {TOTALPAGENUM}', 1, array(0,0,0));


    $PosTablasCompletas=420;
    $TamTablasCompetas=770;
    $PosY=535;
    $TextGreneral=9;
    $contPedidos=1;

	$pdf->ezSetY($PosY);

    $AltoImagen2 = 40;
    $nmbreImagenEmpresa = __DIR__ . "/../../../imgenes/logoABBRojo.png";
/*     list($AnchoRealImagen, $AlturaRealImagen) = getimagesize($nmbreImagenEmpresa);
    $LargoImagen = ($AnchoRealImagen / $AlturaRealImagen) * $AltoImagen2;
    $pdf->addPngFromFile($nmbreImagenEmpresa, 50, 510, $LargoImagen, $AltoImagen2);
 */

    list($AnchoRealImagen, $AlturaRealImagen, $tipo, $atr) = getimagesize($nmbreImagenEmpresa);
    $LargoImagen=($AnchoRealImagen/$AlturaRealImagen)*$AltoImagen2;

    $pdf->addPngFromFile($nmbreImagenEmpresa,710,510,$LargoImagen,$AltoImagen2);

    foreach ($proyectos as $proyecto) {
        //echo ('<br/> This proyecto content <br/>'.var_export($proyecto,true));
        $projectDetail = json_decode($proyecto['project_detail'], true);
        //echo ('<br/> This projectDetail content <br/>'.var_export($projectDetail,true));
        $cliente = $projectDetail['contacto'] ?? 'Sin cliente';
        $fecha = $proyecto['fecha_creacion'] ?? date('Y-m-d');
        $folio = $proyecto['folio'] ?? 'Sin folio';
        $NombreProyecto=$projectDetail["nombre_proyecto"];
	
        $dataEn1[0]["NombreCorto0"]="<b>Oferta: ".$NombreProyecto."</b>";
        $NombreColumnasEn1["NombreCorto0"]='';//$data[0]["NombreCorto"]=$NombreCortoDistribuidor;	
        $JustifiacionColumnasEn1["NombreCorto0"]=array('justification'=>'left');
        $pdf->ezTable($dataEn1, $NombreColumnasEn1, '', array('showHeadings'=>0, 'rowGap' => 1, 'showLines'=>0, 'width'=>$TamTablasCompetas, 'xPos'=>$PosTablasCompletas, 'fontSize' => 14, 'shaded'=> 0, 'textCol'=>array(0.0,0.0,0.0), 'cols'=>$JustifiacionColumnasEn1));
        //unset($data);//=array(NULL);
        //unset($NombreColumnas);//=array(NULL);
        //unset($JustifiacionColumnas);//=array(NULL);
        
        $pdf->setLineStyle(2);
        $pdf->setColor(1.0,0.0,0.05,0);
        $pdf->setStrokeColor(1.0,0.0,0.05,0);
        $pdf->line(40,$pdf->y+16+12,80,$pdf->y+16+12);
        
        
        $pdf->ezSetY($pdf->y-15);
        
        
        $Fecha=substr($fecha, 6, 2)."/".substr($fecha, 4, 2)."/".substr($fecha, 0, 4);
        
        
        
        
        $dataEn2[0]["NombreCorto0"]="<b>Cliente: </b>";
        $NombreColumnasEn2["NombreCorto0"]='';//$data[0]["NombreCorto"]=$NombreCortoDistribuidor;	
        $JustifiacionColumnasEn2["NombreCorto0"]=array('justification'=>'right', 'width'=>150, 'bgcolor'=> [0.7,0.7,0.7]);
        
        $dataEn2[0]["NombreCorto1"]=" ".$projectDetail["contacto"];
        $NombreColumnasEn2["NombreCorto1"]='';//$data[0]["NombreCorto"]=$NombreCortoDistribuidor;	
        $JustifiacionColumnasEn2["NombreCorto1"]=array('justification'=>'left');
        
        
        $dataEn2[0]["NombreCorto2"]="<b>Fecha: </b>";
        $NombreColumnasEn2["NombreCorto2"]='';//$data[0]["NombreCorto"]=$NombreCortoDistribuidor;	
        $JustifiacionColumnasEn2["NombreCorto2"]=array('justification'=>'right', 'width'=>100, 'bgcolor'=> [0.7,0.7,0.7]);
        
        $dataEn2[0]["NombreCorto3"]=" ".$Fecha;
        $NombreColumnasEn2["NombreCorto3"]='';//$data[0]["NombreCorto"]=$NombreCortoDistribuidor;	
        $JustifiacionColumnasEn2["NombreCorto3"]=array('justification'=>'left', 'width'=>100);
        
		$dataEn2[1]["NombreCorto2"]="<b>Folio: </b>";
		$dataEn2[1]["NombreCorto3"]=" ".$folio;
        $pdf->ezTable($dataEn2, $NombreColumnasEn2, '', array('showHeadings'=>0, 'rowGap' => 4, 'showLines'=>1, 'width'=>$TamTablasCompetas, 'xPos'=>$PosTablasCompletas, 'fontSize' => $TextGreneral, 'shaded'=> 0, 'textCol'=>array(0.0,0.0,0.0), 'cols'=>$JustifiacionColumnasEn2));
        	
	$pdf->ezSetY($pdf->y-15);
	
/* 	$dataTitTab[0]["NombreCorto0"]="<b>No. </b>";
	$NombreColumnas["NombreCorto0"]='';//$data[0]["NombreCorto"]=$NombreCortoDistribuidor;	
	$JustifiacionColumnasTitTab["NombreCorto0"]=array('justification'=>'center', 'width'=>30);
	
	$dataTitTab[0]["NombreCorto1"]="<b>Código</b>";
	$NombreColumnas["NombreCorto1"]='';//$data[0]["NombreCorto"]=$NombreCortoDistribuidor;	
	$JustifiacionColumnasTitTab["NombreCorto1"]=array('justification'=>'center', 'width'=>105);
	
	$dataTitTab[0]["NombreCorto2"]="<b>Descripción</b>";
	$NombreColumnas["NombreCorto2"]='';//$data[0]["NombreCorto"]=$NombreCortoDistribuidor;	
	$JustifiacionColumnasTitTab["NombreCorto2"]=array('justification'=>'center', 'width'=>178);
	
	$dataTitTab[0]["NombreCorto3"]="<b>Cant.</b>";
	$NombreColumnas["NombreCorto3"]='';//$data[0]["NombreCorto"]=$NombreCortoDistribuidor;	
	$JustifiacionColumnasTitTab["NombreCorto3"]=array('justification'=>'center', 'width'=>35);

	$dataTitTab[0]["NombreCorto4"]="<b>Precio Unitario de Lista</b>";
	$NombreColumnas["NombreCorto4"]='';//$data[0]["NombreCorto"]=$NombreCortoDistribuidor;	
	$JustifiacionColumnasTitTab["NombreCorto4"]=array('justification'=>'center', 'width'=>75);
	
	$dataTitTab[0]["NombreCorto5"]="<b>Precio Unitario con Descuento</b>";
	$NombreColumnas["NombreCorto5"]='';//$data[0]["NombreCorto"]=$NombreCortoDistribuidor;	
	$JustifiacionColumnasTitTab["NombreCorto5"]=array('justification'=>'center', 'width'=>75);
	
	$dataTitTab[0]["NombreCorto6"]="<b>Subtotal con Descuento</b>";
	$NombreColumnas["NombreCorto6"]='';//$data[0]["NombreCorto"]=$NombreCortoDistribuidor;	
	$JustifiacionColumnasTitTab["NombreCorto6"]=array('justification'=>'center', 'width'=>80);
	
	$dataTitTab[0]["NombreCorto7"]="<b>Tiempo de Entrega</b>";
	$NombreColumnas["NombreCorto7"]='';//$data[0]["NombreCorto"]=$NombreCortoDistribuidor;	
	$JustifiacionColumnasTitTab["NombreCorto7"]=array('justification'=>'center', 'width'=>97);
	
	$dataTitTab[0]["NombreCorto8"]="<b>Existencias en RDC (Stock)</b>";
	$NombreColumnas["NombreCorto8"]='';//$data[0]["NombreCorto"]=$NombreCortoDistribuidor;	
	$JustifiacionColumnasTitTab["NombreCorto8"]=array('justification'=>'center', 'width'=>96);
	
	$pdf->ezTable($dataTitTab, $NombreColumnas, '', array('showHeadings'=>0, 'rowGap' => 9, 'showLines'=>1, 'width'=>$TamTablasCompetas, 'xPos'=>$PosTablasCompletas, 'fontSize' => $TextGreneral, 'shaded'=> 2, 'shadeCol'=>array(1.0,0.0,0.05), 'shadeCol2'=>array(1.0,0.18,0.18), 'textCol'=>array(1.0,1.0,1.0), 'cols'=>$JustifiacionColumnasTitTab));
 */	//unset($data);//=array(NULL);
	//unset($JustifiacionColumnasTitTab);//=array(NULL);
	
/* 	$JustifiacionColumnas["NombreCorto0"]=array('justification'=>'center', 'width'=>30);
	$JustifiacionColumnas["NombreCorto1"]=array('justification'=>'center', 'width'=>105);	
	$JustifiacionColumnas["NombreCorto2"]=array('justification'=>'left');
	$JustifiacionColumnas["NombreCorto3"]=array('justification'=>'right', 'width'=>35);
	$JustifiacionColumnas["NombreCorto4"]=array('justification'=>'right', 'width'=>75);
	$JustifiacionColumnas["NombreCorto5"]=array('justification'=>'right', 'width'=>75);
	$JustifiacionColumnas["NombreCorto6"]=array('justification'=>'right', 'width'=>80);
	$JustifiacionColumnas["NombreCorto7"]=array('justification'=>'left', 'width'=>96);
	$JustifiacionColumnas["NombreCorto8"]=array('justification'=>'left', 'width'=>96); */
        // Encabezado
/*         $headerData = array(
            array(
                'Cliente' => "<b>$cliente</b>",
                'Fecha' => "<b>$fecha</b>"
            ),
            array(
                'Proyecto' => "<b>$folio</b>",
                'ID' => "<b>{$proyecto['id']}</b>"
            )
        );

        $pdf->ezTable($headerData, null, '', array(
            'fontSize' => 10,
            'width' => 770,
            'shaded' => 0,
            'rowGap' => 3,
            'showHeadings' => 0,
            'showLines' => 0 + 1,
            'textCol' => array(0.0, 0.0, 0.0)
        )); */

        $stms_tableros = $pdo->prepare("SELECT * FROM tableros_proyecto WHERE status = 'Activo' AND proyecto_id = :proyecto_id");
        $stms_tableros->execute(['proyecto_id' => $proyecto['id']]);
        $tableros = $stms_tableros->fetchAll(PDO::FETCH_ASSOC);

        if (!empty($tableros)) {
            foreach ($tableros as $tablero) {
                $tab_detail = json_decode($tablero['tab_detail'], true);

                if (isset($tab_detail['componentes']) && is_array($tab_detail['componentes'])) {
                    $tableData = array();
                    $totalPedido = 0;

                    foreach ($tab_detail['componentes'] as $index => $componente) {
                        if (!isset($componente['codigo_producto'])) continue;

                        $codigo = $componente['codigo_producto'];
                        $stms_inventario = $pdo->prepare("SELECT * FROM B_InventarioEquipos WHERE Codigo = :codigo_componente");
                        $stms_inventario->execute(['codigo_componente' => $codigo]);
                        $info_componente = $stms_inventario->fetch(PDO::FETCH_ASSOC);

                        if ($info_componente) {
                            $precioLista = floatval($info_componente['PrecioDeLista']);
                            $descuento = floatval($info_componente['CedulaDescuento']);
                            $cantidad = intval($componente['cantidad'] ?? 1);

                            $precioDescuento = $precioLista * (1 - ($descuento / 100));
                            $subtotal = $precioLista * $cantidad;
                            $totalPedido += $subtotal;

                            $tableData[] = array(
                                'No.' => $index + 1,
                                'Código' => $info_componente['Codigo'],
                                'Descripción' => $info_componente['Descripcion'] ?? '',
                                'Cant.' => $cantidad,
                                'Precio Lista' => '$' . number_format($precioLista, 2),
                                'Precio Desc.' => '$' . number_format($precioDescuento, 2),
                                'Subtotal' => '$' . number_format($subtotal, 2),
                                'Tiempo entrega' => 'De 9 a 12 Semanas',
                                'Existencias en RDC (Stock)' => 'NA'
                            );
                        }
                    }

                    if (!empty($tableData)) {
                        $pdf->ezTable($tableData, null, '', array(
                            'fontSize' => 8,
                            'width' => 770,
                            'shaded' => 2,
                            'titleFontColor' => array(1, 1, 1),
                            'shadeHeading' => 1, 
                            'shadeCol' => array(0.7,0.7,0.7),
                            'shadeCol2' => array(1, 1, 1),
                            'shadeHeadingCol' => array(1, 0, 0),
                            'lineCol' => array(0, 0, 0),
                            'showHeadings' => 1,
                            'rowGap' => 5,
                            'showLines' => 1,
                            'textCol' => array(0.0, 0.0, 0.0)
                        ));
                        $pdf->setColor(0,0,0);
                        $pdf->ezSetY($pdf->y-0); 
                        $totalData = array(
                            array(
                                'Total' => '<b>Total:</b>',
                                'Valor' => '<b>$' . number_format($totalPedido, 2) . '</b>'
                            )
                        );
                        
                        $pdf->ezTable($totalData, null, '', array(
                            'fontSize' => 8,
                            'width' => 115, 
                            'shaded' => 2,
                            'shadeCol' => array(0.85,0.85,0.85),
                            'shadeCol2' => array(0.85,0.85,0.85),
                            'showHeadings' => 0,
                            'rowGap' => 0,
                            'cols' => array(
                                'Total' => array('justification' => 'right', 'width' => 56),
                                'Valor' => array('justification' => 'right', 'width' => 55)
                            ),
                            'xPos' => 559.5
                        ));
                    }
                }
            }
        }
    }

    // Limpiar buffer y enviar PDF
    while (ob_get_level()) {
        ob_end_clean();
    }

    $dir = __DIR__ . '/pdf';
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }

    $pdfContent = $pdf->ezOutput();
    $filePath = $dir . '/proyecto_' . $idProyecto . '.pdf';
    file_put_contents($filePath, $pdfContent);

    header('Content-Type: application/pdf');
    header('Content-Disposition: inline; filename="proyecto_' . $idProyecto . '.pdf"');

    $pdf->ezStream(array(
        'download' => false,
        'compress' => 0
    ));

} catch (Exception $e) {
    while (ob_get_level()) {
        ob_end_clean();
    }
    header('Content-Type: application/json');
    echo json_encode(["error" => "Error: " . $e->getMessage()]);
}

exit;
?>
