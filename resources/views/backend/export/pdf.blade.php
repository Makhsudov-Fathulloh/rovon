<!DOCTYPE html>
<html lang="uz">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta charset="UTF-8">

    <style>
        body {
            font-family: "DejaVu Sans", sans-serif;
            font-size: 12px;
            color: #000;
        }

        /* HEADER */
        .header {
            width: 100%;
            margin-bottom: 15px;
        }

        .header td {
            border: none;
            vertical-align: middle;
        }

        .logo {
            width: 90px;
        }

        .company-info {
            text-align: center;
        }

        .company-info h2 {
            margin: 0;
            font-size: 16px;
        }

        .company-info p {
            margin: 2px 0;
            font-size: 10px;
        }

        /* TABLE */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 1rem;
        }

        th, td {
            border: 1px solid #000;
            padding: 4px;
            text-align: center;
        }

        th {
            background: #f0f0f0;
            font-weight: bold;
        }

        /* TOTALS */
        .totals {
            margin-top: 15px;
        }

        /* FOOTER */
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            font-size: 9px;
            text-align: center;
        }
    </style>
</head>
<body>

{{-- HEADER --}}
<table class="header">
    <tr>
        <td width="20%" align="right">
            <img src="{{ public_path('images/logo-text_.png') }}" class="logo">
        </td>

        <td width="10%"></td>

        <td width="50%" class="company-info">
            <h2>{{ $header['title'] }}</h2>
            <p><strong>Малумот тури:</strong> {{ $header['subtitle'] }} </p>
            <p><strong> Ҳисоботи санаси:</strong> {{ now()->format('d.m.Y H:i') }}</p>
        </td>

        <td width="10%"></td>

        <td width="20%" align="left">
            <img src="{{ public_path('images/logo-text_.png') }}" class="logo">
        </td>
    </tr>
</table>

{{-- DATA TABLE --}}
<table>
    <thead>
        <tr>
            @foreach($columns as $label)
                <th>{{ $label }}</th>
            @endforeach
        </tr>
    </thead>

    <tbody>
        @foreach($data as $item)
            <tr>
                @foreach($columns as $key => $label)
                    @php
                        if (str_contains($key, ':')) {
                            [$field, $format] = explode(':', $key);
                            $value = data_get($item, $field);

                            $value = match ($format) {
                                'datetime' => optional($value)->format('Y-m-d H:i:s'),
                                'date'     => optional($value)->format('Y-m-d'),

                                // ***** YANGI QISM: NARXNING DINAMIK FORMATI *********
                                'price_format' => \App\Helpers\PriceHelper::format(
                                    $value,
                                    data_get($item, 'currency') // Currency ni qator ob'ektidan oladi
                                ),
                                // **************************************************

                                default    => $value,
                            };
                        } else {
                            $value = data_get($item, $key);
                        }

                        $value = $value === 0 ? '0' : $value;
                    @endphp
                    <td>{{ $value }}</td>
                @endforeach
            </tr>
        @endforeach
    </tbody>
</table>

{{-- TOTALS --}}
@if($totals)
    <div class="totals">
        @foreach($totals as $label => $value)
            <div>
                <strong>{{ $label }}</strong> {{ $value }}
            </div>
        @endforeach
    </div>
@endif

{{-- FOOTER --}}
<div class="footer">
      Ушбу тизим
      <strong>Castle Systems</strong> томонидан жорий этилган.<br>
      Дастурий таъминот:
      <strong>Castle Systems IT Company</strong> |
      www.castlesystems.uz
</div>

</body>
</html>
