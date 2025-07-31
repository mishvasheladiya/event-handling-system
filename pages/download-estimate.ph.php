<?php
require '/../vendor/autoload.php';

$mpdf = new \Mpdf\Mpdf();
ob_start(); // Start buffering HTML output
?>

<!-- Your existing estimate HTML -->
<div style="text-align:center;">
    <h1>Estimate Sheet</h1>
    <p>Customer: John Doe</p>
    <p>Total: ₹5,000</p>
    <!-- Add your full design here -->
</div>

<?php
$html = ob_get_clean(); // Get buffered content
$mpdf->WriteHTML($html);
$mpdf->Output('estimate.pdf', 'D'); // D = force download
?>
