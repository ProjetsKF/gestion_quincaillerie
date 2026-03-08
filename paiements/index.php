<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>BISIKOMASH - Facturation</title>

    <!-- Custom fonts for this template-->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">
        <link rel="stylesheet" href="../assets/css/style.css">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- Custom styles for this template-->
    <link href="../css/sb-admin-2.min.css" rel="stylesheet">
    <style>

body{
    font-family: Arial, sans-serif;
    background:#f4f4f4;
}

.invoice-container{
    width:900px;
    margin:auto;
    background:white;
    padding:30px;
}

.header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    background:linear-gradient(90deg,#0a87b8,#2bb3e3);
    color:white;
    padding:20px;
}

.logo{
    font-size:18px;
}

.title{
    font-size:40px;
    font-weight:bold;
}

.info-section{
    display:flex;
    justify-content:space-between;
    margin-top:30px;
}

.invoice-to h4{
    margin-bottom:10px;
}

.items-table{
    width:100%;
    border-collapse:collapse;
    margin-top:30px;
}

.items-table th{
    background:#1fa2cf;
    color:white;
    padding:12px;
    text-align:left;
}

.items-table td{
    padding:12px;
    border-bottom:1px solid #ddd;
}

.items-table tbody tr:nth-child(even){
    background:#f1f1f1;
}

.totals{
    display:flex;
    justify-content:space-between;
    margin-top:40px;
}

.payment{
    width:50%;
}

.total-box{
    text-align:right;
}

.grand-total{
    background:#1fa2cf;
    color:white;
    padding:15px;
    font-size:20px;
    font-weight:bold;
    margin-top:10px;
}

.footer{
    margin-top:40px;
    border-top:1px solid #ddd;
    padding-top:20px;
}

</style>

</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

         <!-- MENU -->

                    <?php include("../menu.php"); ?>

        <!-- FIN MENU -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

               
            <!-- Topbar -->

                 <?php include("../topbar.php"); ?>

            <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">

                    <!-- Page Heading -->
                    <h1 class="h3 mb-4 text-gray-800">Facturation</h1>


                    <div class="invoice-container">

<div class="header">

<div class="logo">
    <img src="../img/bisikomashLogo1.png" alt="Logo entreprise" width="190">
</div>

<div class="title">
INVOICE
</div>

</div>


            <div class="info-section">

            <div class="invoice-to">
            <h4>INVOICE TO:</h4>
            <p>John Peterson</p>
            <p>Av 07, Mega Complex, Newyork</p>
            <p>+0123 456 789 000</p>
            </div>

            <div class="invoice-details">
            <p><strong>Invoice No:</strong> 012345</p>
            <p><strong>Account:</strong> 0000 12345 678900</p>
            <p><strong>Date:</strong> 06/02/2026</p>
            </div>

            </div>


<table class="items-table">

                <thead>
                <tr>
                <th>SL</th>
                <th>ITEM DESCRIPTION</th>
                <th>PRICE</th>
                <th>QTY</th>
                <th>TOTAL</th>
                </tr>
                </thead>

<tbody>

                <tr>
                <td>1</td>
                <td>Lorem ipsum</td>
                <td>$10</td>
                <td>1</td>
                <td>$10</td>
                </tr>

                <tr>
                <td>2</td>
                <td>Consectetur adipiscing elit</td>
                <td>$15</td>
                <td>2</td>
                <td>$30</td>
                </tr>

                <tr>
                <td>3</td>
                <td>Duis aute irure dolor</td>
                <td>$60</td>
                <td>1</td>
                <td>$60</td>
                </tr>

                <tr>
                <td>4</td>
                <td>Lorem ipsum</td>
                <td>$11</td>
                <td>6</td>
                <td>$66</td>
                </tr>

<tr>
<td>5</td>
<td>Consectetur adipiscing elit</td>
<td>$20</td>
<td>3</td>
<td>$60</td>
</tr>

<tr>
<td>6</td>
<td>Duis aute irure dolor</td>
<td>$14</td>
<td>4</td>
<td>$56</td>
</tr>

</tbody>

</table>


<div class="totals">

<div class="payment">

<h4>Payment Info:</h4>

<p>Account: 1234567890</p>
<p>A/C Name:</p>
<p>Bank Details: Add your details</p>

</div>


<div class="total-box">

<p>SUB TOTAL : $282.00</p>
<p>TAX : 0.00%</p>

<div class="grand-total">
TOTAL $282.00
</div>

</div>

</div>


<div class="footer">

<h3>Thank you for your business</h3>

<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>

</div>

</div>

                </div>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

             <!-- PIED DE PAGE -->

                     <?php include("../pieds.php"); ?>

             <!-- FIN PIED DE PAGE -->


        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Logout Modal-->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <a class="btn btn-primary" href="login.html">Logout</a>
                </div>
            </div>
        </div>
    </div>

    <script src="../vendor/jquery/jquery.min.js"></script>
<script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../vendor/jquery-easing/jquery.easing.min.js"></script>
<script src="../js/sb-admin-2.min.js"></script>

</body>

</html>