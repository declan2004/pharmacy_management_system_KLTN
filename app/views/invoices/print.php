<?php 
/** * @var array $invoice | @var array $details */ 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt #<?= $invoice['invoice_id'] ?></title>
    <style>
        @page { margin: 0; }
        
        body {
            font-family: 'Courier New', Courier, monospace; 
            width: 80mm; 
            margin: 0 auto;
            padding: 5mm;
            font-size: 12px;
            color: #000;
            background-color: #fff;
        }

        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .bold { font-weight: bold; }
        
        .divider { border-top: 1px dashed #000; margin: 5px 0; }
        
        table { width: 100%; border-collapse: collapse; margin: 10px 0; }
        th, td { padding: 4px 0; vertical-align: top; }
        
        .item-name { display: block; font-size: 11px; font-weight: bold; }
        .item-meta { display: block; font-size: 10px; color: #444; }
        
        @media print {
            body { width: 100%; padding: 2mm; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>

    <div class="no-print" style="text-align: center; margin-bottom: 20px;">
        <button onclick="window.print()" style="padding: 10px 20px; font-size: 16px; cursor: pointer;">🖨️ Re-Print</button>
        <p style="font-family: sans-serif; font-size: 12px; color: red;">Bản xem trước. Hệ thống sẽ tự động gọi lệnh in.</p>
    </div>

    <div class="text-center">
        <h2 style="margin: 0; font-size: 18px;">PMS</h2>
        <p style="margin: 3px 0;">123 Healthcare St, Hanoi</p>
        <p style="margin: 3px 0;">Tel: 1900 1234</p>
    </div>
    
    <div class="divider"></div>
    
    <div class="text-center bold" style="font-size: 15px; margin: 8px 0;">RETAIL INVOICE</div>
    
    <div style="font-size: 11px;">
        <p style="margin: 2px 0;">Inv No:  <span class="bold">#<?= $invoice['invoice_id'] ?></span></p>
        <p style="margin: 2px 0;">Date:    <?= date('d/m/Y H:i', strtotime($invoice['invoice_date'])) ?></p>
        <p style="margin: 2px 0;">Cashier: <?= htmlspecialchars($invoice['pharmacist_name']) ?></p>
        <p style="margin: 2px 0;">Method:  <?= $invoice['payment_method'] ?></p>
    </div>
    
    <div class="divider"></div>
    
    <table>
        <thead>
            <tr style="border-bottom: 1px dashed #000;">
                <th style="text-align: left;">Item</th>
                <th class="text-center" style="width: 15%;">Qty</th>
                <th class="text-right" style="width: 25%;">Total</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($details as $item): ?>
                <tr>
                    <td>
                        <span class="item-name"><?= htmlspecialchars($item['medicine_name']) ?></span>
                        <span class="item-meta">Lot: <?= $item['batch_number'] ?> | <?= number_format($item['unit_price'], 0, ',', '.') ?></span>
                    </td>
                    <td class="text-center text-right"><?= $item['quantity'] ?></td>
                    <td class="text-right"><?= number_format($item['subtotal'], 0, ',', '.') ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    
    <div class="divider"></div>
    
    <div style="font-size: 14px; margin-top: 10px;">
        <span class="bold">TOTAL:</span>
        <span class="bold text-right" style="float: right; font-size: 16px;"><?= number_format($invoice['total_amount'], 0, ',', '.') ?> VND</span>
    </div>
    
    <div class="divider" style="margin-top: 10px;"></div>
    
    <div class="text-center" style="margin-top: 15px; font-size: 11px;">
        <p style="margin: 2px 0; font-weight: bold;">Thank you for your visit!</p>
        <p style="margin: 2px 0;">Exchange/Return within 7 days.</p>
        <p style="margin: 2px 0;">Please keep receipt.</p>
    </div>

    <script>
        window.onload = function() {
            setTimeout(() => {
                window.print();
            }, 500);
            
            // Tự động đóng tab sau khi in xong (Tùy chọn, hiện đang comment lại để bạn test)
            // window.onafterprint = function() { window.close(); };
        };
    </script>
</body>
</html>