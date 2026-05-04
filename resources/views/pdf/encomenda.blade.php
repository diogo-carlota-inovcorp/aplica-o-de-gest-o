<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Encomenda {{ $encomenda->numero }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            line-height: 1.6;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #ddd;
        }
        .title {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .info {
            margin-bottom: 30px;
        }
        .info table {
            width: 100%;
        }
        .info td {
            padding: 5px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f5f5f5;
            font-weight: bold;
        }
        .total {
            text-align: right;
            font-size: 16px;
            font-weight: bold;
            margin-top: 20px;
            padding-top: 10px;
            border-top: 2px solid #ddd;
        }
        .footer {
            text-align: center;
            margin-top: 50px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            font-size: 12px;
            color: #666;
        }
        .status {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 12px;
            font-weight: bold;
        }
        .status-rascunho { background-color: #fef3c7; color: #92400e; }
        .status-fechado { background-color: #d1fae5; color: #065f46; }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">ENCOMENDA</div>
        <div>Nº {{ $encomenda->numero }}</div>
    </div>

    <div class="info">
        <table>
            <tr>
                <td width="50%"><strong>Data:</strong> {{ \Carbon\Carbon::parse($encomenda->data_encomenda)->format('d/m/Y') }}</td>
                <td width="50%"><strong>Cliente:</strong> {{ $encomenda->cliente?->nome ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td><strong>Estado:</strong>
                    <span class="status status-{{ $encomenda->estado }}">
                        {{ ucfirst($encomenda->estado) }}
                    </span>
                </td>
                <td></td>
            </tr>
        </table>
    </div>

    <h3>Itens da Encomenda</h3>
    <table>
        <thead>
            <tr>
                <th>Referência</th>
                <th>Artigo</th>
                <th>Quantidade</th>
                <th>Preço Unitário</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($encomenda->linhas as $linha)
            <tr>
                <td>{{ $linha->artigo?->referencia ?? 'N/A' }}</td>
                <td>{{ $linha->artigo?->nome ?? 'N/A' }}</td>
                <td>{{ $linha->quantidade }}</td>
                <td>€ {{ number_format($linha->preco_custo, 2, ',', '.') }}</td>
                <td>€ {{ number_format($linha->subtotal, 2, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="total">
        Total: € {{ number_format($encomenda->total, 2, ',', '.') }}
    </div>

    <div class="footer">
        <p>Documento gerado em {{ now()->format('d/m/Y H:i:s') }}</p>
        <p>{{ config('app.name') }} - Sistema de Gestão</p>
    </div>
</body>
</html>
