<?php

require_once '../bd/database.php';
require_once '../vendor/autoload.php';

use Dompdf\Dompdf;

$dompdf = new Dompdf();

/* ===============================
   DONNÉES
================================= */

$sql = "SELECT 
            dc.idcom,
            co.datCom AS date,
            p.designP,
            c.nom,
            c.postnom,
            dc.Qte,
            pa.montant,
            pa.unitMon
        FROM detailscommande dc
        INNER JOIN commande co ON dc.idcom = co.idCom
        INNER JOIN client c ON co.idClt = c.idclt
        INNER JOIN produit p ON dc.idprod = p.idprod
        LEFT JOIN paiement pa ON pa.idCom = co.idCom
        ORDER BY dc.idcom DESC";

$stmt = $pdo->query($sql);
$ventes = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* ===============================
   HTML PDF
================================= */

$html = '
<style>
    body {
        font-family: Arial, sans-serif;
        font-size: 12px;
    }

    h2 {
        text-align: center;
        margin-bottom: 20px;
    }

    table {
        border-collapse: collapse;
        width: 100%;
    }

    th {
        background-color: #4e73df;
        color: white;
        padding: 8px;
        border: 1px solid #ddd;
    }

    td {
        padding: 8px;
        border: 1px solid #ddd;
        text-align: center;
    }

    .paid {
        color: green;
        font-weight: bold;
    }

    .unpaid {
        color: red;
        font-weight: bold;
    }
</style>

<h2>Rapport des ventes</h2>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Produit</th>
            <th>Client</th>
            <th>Quantité</th>
            <th>Montant</th>
            <th>Date</th>
        </tr>
    </thead>
    <tbody>
';

/* ===============================
   CONTENU TABLE
================================= */

foreach ($ventes as $v) {

    $montant = !empty($v['montant']) 
        ? '<span class="paid">'.number_format($v['montant'], 0, ",", " ").' '.$v['unitMon'].'</span>'
        : '<span class="unpaid">Non payé</span>';

    $html .= '
        <tr>
            <td>'.$v['idcom'].'</td>
            <td>'.$v['designP'].'</td>
            <td>'.$v['nom'].' '.$v['postnom'].'</td>
            <td>'.$v['Qte'].'</td>
            <td>'.$montant.'</td>
            <td>'.$v['date'].'</td>
        </tr>';
}

$html .= '
    </tbody>
</table>
';

/* ===============================
   GÉNÉRER PDF
================================= */

$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

/* Télécharger */
$dompdf->stream("rapport_ventes.pdf", ["Attachment" => true]);