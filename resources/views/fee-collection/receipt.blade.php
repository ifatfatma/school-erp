<!DOCTYPE html>
<html>
<head>

    <meta charset="UTF-8">

    <title>
        Fee Receipt - {{ $payment->receipt_no }}
    </title>

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #222;
            margin: 0;
            padding: 20px;
        }

        .receipt {
            width: 100%;
            border: 1px solid #333;
            padding: 20px;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #222;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        .header h1 {
            margin: 0;
            font-size: 22px;
        }

        .header h2 {
            margin: 8px 0 0;
            font-size: 16px;
        }

        .student-info {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .student-info td {
            padding: 7px;
            border: 1px solid #ccc;
        }

        .label {
            font-weight: bold;
            width: 18%;
            background: #f5f5f5;
        }

        .items {
            width: 100%;
            border-collapse: collapse;
        }

        .items th,
        .items td {
            border: 1px solid #333;
            padding: 8px;
        }

        .items th {
            background: #f1f1f1;
        }

        .text-right {
            text-align: right;
        }

        .total {
            font-weight: bold;
            font-size: 14px;
        }

        .remarks {
            margin-top: 20px;
        }

        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 11px;
            color: #666;
        }

    </style>

</head>

<body>

<div class="receipt">

    <div class="header">

        <h1>
            FEE PAYMENT RECEIPT
        </h1>

        <h2>
            {{ $payment->receipt_no }}
        </h2>

    </div>


    <table class="student-info">

        <tr>

            <td class="label">
                Student Name
            </td>

            <td>
                {{ $payment->student?->full_name ?? '-' }}
            </td>

            <td class="label">
                Admission No
            </td>

            <td>
                {{ $payment->student?->admission_no ?? '-' }}
            </td>

        </tr>


        <tr>

            <td class="label">
                Payment Date
            </td>

            <td>
                {{
                    optional(
                        $payment->payment_date
                    )->format('d-m-Y')
                }}
            </td>

            <td class="label">
                Payment Method
            </td>

            <td>
                {{ $payment->payment_method }}
            </td>

        </tr>


        <tr>

            <td class="label">
                Transaction No
            </td>

            <td>
                {{ $payment->transaction_no ?? '-' }}
            </td>

            <td class="label">
                Status
            </td>

            <td>
                {{ $payment->status }}
            </td>

        </tr>

    </table>


    <table class="items">

        <thead>

            <tr>

                <th width="8%">
                    #
                </th>

                <th>
                    Fee Type
                </th>

                <th>
                    Fee Structure
                </th>

                <th width="20%" class="text-right">
                    Amount
                </th>

            </tr>

        </thead>


        <tbody>

            @foreach(
                $payment->items
                as $index => $item
            )

                <tr>

                    <td>
                        {{ $index + 1 }}
                    </td>

                    <td>
                        {{
                            $item->feeDue?->feeType?->name
                            ?? '-'
                        }}
                    </td>

                    <td>
                        {{
                            $item->feeDue?->feeStructure?->name
                            ?? '-'
                        }}
                    </td>

                    <td class="text-right">

                        ₹{{
                            number_format(
                                (float) $item->amount,
                                2
                            )
                        }}

                    </td>

                </tr>

            @endforeach


            <tr>

                <td
                    colspan="3"
                    class="text-right total"
                >
                    Total
                </td>

                <td class="text-right total">

                    ₹{{
                        number_format(
                            (float)
                            $payment->total_amount,
                            2
                        )
                    }}

                </td>

            </tr>

        </tbody>

    </table>


    @if($payment->remarks)

        <div class="remarks">

            <strong>
                Remarks:
            </strong>

            {{ $payment->remarks }}

        </div>

    @endif


    <div class="footer">

        Thank you for your payment.

    </div>

</div>

</body>
</html>