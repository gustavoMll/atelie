<?php
require __DIR__ . '/vendor/autoload.php';

use Spipu\Html2Pdf\Html2Pdf;
use Spipu\Html2Pdf\Exception\Html2PdfException;
use Spipu\Html2Pdf\Exception\ExceptionFormatter;

try {
    // Inicializando HTML2PDF
    $html2pdf = new Html2Pdf();

    // Definindo o conteúdo HTML a ser convertido para PDF
    $conteudo = '
    <style>
        h1 { text-align: center; color: #333; }
        p { font-size: 16px; }
    </style>
    <h1>Termos de Serviço</h1>
    <p>Aqui estão os termos de serviço que você pode ler e revisar.</p>
    ';

    // Convertendo o conteúdo HTML para PDF
    $html2pdf->writeHTML($conteudo);

    // Exibindo o PDF para o navegador
    $html2pdf->output('termos.pdf', 'D');
} catch (Html2PdfException $e) {
    $formatter = new ExceptionFormatter($e);
    echo $formatter->getHtmlMessage();
}
