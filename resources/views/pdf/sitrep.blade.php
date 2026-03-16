<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>SITREP REPORT</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            line-height: 1.5;
        }

        .h1 {
        width: 100%;
        vertical-align: left;
        position: absolute;
        top: -15px;
        left: -10px;
        font-size: 12px;
        }

        .h2 {
            width: 100%;
            vertical-align: left;
            position: absolute;
            top: -15px;
            left: 120px;
            font-size: 12px;
        }

        .h3 {
            width: 100%;
            /* vertical-align: right; */
            position: absolute;
            top: -15px;
            right: -550px;
            font-size: 12px;
        }

        .section-title {
            font-weight: bold;
            margin-top: 15px;
            margin-bottom: 5px;
        }

        .justify {
            text-align: justify;
            margin-bottom: 1px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            margin-bottom: 15px;
        }

        th, td {
            border: 1px solid #000;
            padding: 5px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <div class="h1">
    <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('/storage/images/dswd2.png'))) }}" alt="" width="130" height="50" />
    </div>

    <div class="h2">
    <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('/storage/images/pilipinas.png'))) }}" alt="" width="60" height="45"/>
    </div>

    <div class="h3">
    <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('/storage/images/dromic.png'))) }}" alt="" width="150" height="50"/>
    </div>
<!-- Header -->


<p><strong>Date Received:</strong> {{ \Carbon\Carbon::parse($sitrep->created_at)->format('F d, Y') }}</p>

<!-- Basic Details -->
<p><strong>Province:</strong> {{ $sitrep->province }}</p>
<p><strong>Municipality:</strong> {{ $sitrep->municipality }}</p>
<p><strong>Barangay:</strong> {{ $sitrep->barangay }}</p>
<p><strong>Incident Type:</strong> {{ $sitrep->affected_families }}</p>

<!-- Situationer -->
<p class="section-title">Situationer:</p>
<p class="justify">
{!! nl2br(
    str_replace(
        ['In response', 'Likewise', 'Meanwhile', 'Further'],
        ['<br>In response', '<br>Likewise', '<br>Meanwhile', '<br>Further'],
        e($sitrep->overview)
    )
) !!}
</p>



</body>
</html>
