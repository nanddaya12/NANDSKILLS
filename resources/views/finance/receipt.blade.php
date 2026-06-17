<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt - {{ $invoice->invoice_number }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #2D3748;
            margin: 0;
            padding: 40px;
            background-color: #FFFFFF;
            font-size: 14px;
            line-height: 1.5;
        }
        .invoice-box {
            max-width: 800px;
            margin: auto;
            border: 1px solid #E2E8F0;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 2px solid #EDF2F7;
            padding-bottom: 30px;
            margin-bottom: 30px;
        }
        .brand {
            font-size: 24px;
            font-weight: 800;
            color: #1A365D;
            letter-spacing: -0.5px;
        }
        .brand-sub {
            color: #718096;
            font-size: 12px;
            margin-top: 4px;
        }
        .details-title {
            font-size: 18px;
            font-weight: 700;
            color: #2D3748;
            text-align: right;
        }
        .details-num {
            font-size: 14px;
            color: #4A5568;
            margin-top: 6px;
            text-align: right;
        }
        .meta-grid {
            display: grid;
            grid-cols: 2;
            gap: 20px;
            margin-bottom: 40px;
        }
        .meta-col {
            width: 48%;
            float: left;
        }
        .meta-col-right {
            width: 48%;
            float: right;
            text-align: right;
        }
        .clear {
            clear: both;
        }
        .meta-title {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #A0AEC0;
            margin-bottom: 8px;
        }
        .meta-value {
            font-size: 14px;
            font-weight: 600;
            color: #2D3748;
        }
        .table-items {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 40px;
        }
        .table-items th {
            background-color: #F7FAFC;
            color: #718096;
            text-transform: uppercase;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.5px;
            padding: 12px 16px;
            text-align: left;
            border-bottom: 2px solid #E2E8F0;
        }
        .table-items td {
            padding: 16px;
            border-bottom: 1px solid #EDF2F7;
            color: #4A5568;
        }
        .totals-panel {
            width: 300px;
            float: right;
            margin-bottom: 30px;
        }
        .totals-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            font-size: 14px;
        }
        .totals-row-final {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            font-size: 18px;
            font-weight: 800;
            border-top: 2px solid #E2E8F0;
            color: #1A365D;
        }
        .footer {
            text-align: center;
            border-top: 1px dashed #E2E8F0;
            padding-top: 30px;
            margin-top: 50px;
            font-size: 12px;
            color: #A0AEC0;
        }
        .btn-print {
            background-color: #3182CE;
            color: #FFFFFF;
            border: none;
            padding: 10px 20px;
            font-size: 14px;
            font-weight: 600;
            border-radius: 6px;
            cursor: pointer;
            float: right;
            margin-top: 20px;
            margin-bottom: 20px;
        }
        .btn-print:hover {
            background-color: #2B6CB0;
        }
        @media print {
            body {
                padding: 0;
            }
            .invoice-box {
                border: none;
                box-shadow: none;
                padding: 0;
            }
            .btn-print {
                display: none;
            }
        }
    </style>
</head>
<body>

    <button onclick="window.print()" class="btn-print">Print Receipt</button>
    <div class="clear"></div>

    <div class="invoice-box">
        <div class="header">
            <div>
                <div class="brand">{{ $invoice->tenant->name ?? 'NANDSKILLS ACADEMY' }}</div>
                <div class="brand-sub">Multi-Tenant Education Operating System</div>
            </div>
            <div>
                <div class="details-title">OFFICIAL RECEIPT</div>
                <div class="details-num">Invoice ID: <strong>{{ $invoice->invoice_number }}</strong></div>
            </div>
        </div>

        <div class="meta-grid">
            <div class="meta-col">
                <div class="meta-title">Billed To:</div>
                <div class="meta-value">{{ $invoice->studentFee->student->user->first_name }} {{ $invoice->studentFee->student->user->last_name }}</div>
                <div class="brand-sub" style="margin-top: 4px;">Student ID: {{ $invoice->studentFee->student->roll_number }}</div>
            </div>
            <div class="meta-col-right">
                <div class="meta-title">Invoice Date:</div>
                <div class="meta-value">{{ $invoice->created_at->format('M d, Y') }}</div>
                <div class="meta-title" style="margin-top: 15px;">Due Date:</div>
                <div class="meta-value">{{ $invoice->due_date->format('M d, Y') }}</div>
            </div>
        </div>
        <div class="clear"></div>

        <table class="table-items">
            <thead>
                <tr>
                    <th>Description</th>
                    <th style="text-align: right;">Base Amount</th>
                    <th style="text-align: right;">Scholarship/Discount</th>
                    <th style="text-align: right;">Total Paid</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <strong>{{ $invoice->studentFee->feeStructure->name }}</strong>
                        <div class="brand-sub">{{ $invoice->studentFee->feeStructure->description }}</div>
                    </td>
                    <td style="text-align: right;">${{ number_format($invoice->amount, 2) }}</td>
                    <td style="text-align: right;">-${{ number_format($invoice->studentFee->discount_amount + $invoice->studentFee->scholarship_amount, 2) }}</td>
                    <td style="text-align: right; font-weight: 700; color: #2B6CB0;">${{ number_format($invoice->total, 2) }}</td>
                </tr>
            </tbody>
        </table>

        <div class="totals-panel">
            <div class="totals-row">
                <span>Subtotal:</span>
                <span>${{ number_format($invoice->amount, 2) }}</span>
            </div>
            <div class="totals-row">
                <span>Discounts:</span>
                <span>-${{ number_format($invoice->studentFee->discount_amount + $invoice->studentFee->scholarship_amount, 2) }}</span>
            </div>
            @if($invoice->tax > 0)
                <div class="totals-row">
                    <span>Late Penalties:</span>
                    <span>+${{ number_format($invoice->tax, 2) }}</span>
                </div>
            @endif
            <div class="totals-row-final">
                <span>Total Due:</span>
                <span>${{ number_format($invoice->total, 2) }}</span>
            </div>
        </div>
        <div class="clear"></div>

        <div class="footer">
            Thank you for your payment. For any billing inquiries, please contact the academy finance desk.<br>
            <span style="font-weight: bold; margin-top: 10px; display: inline-block;">{{ $invoice->tenant->name ?? 'NANDSKILLS ACADEMY' }}</span>
        </div>
    </div>

</body>
</html>
