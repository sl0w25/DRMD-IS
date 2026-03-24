<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>SITREP REPORT</title>
    <style>
        @page {
            margin: -40px 50px 20px 50px;
            counter-increment: page;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 15px;
            line-height: 1.5;
            margin-top: 130px;  /* Space for fixed header */
            margin-bottom: 60px; /* Space for fixed footer */
        }

        header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #000;
            padding: 10px 0;
            background: #fff;
            z-index: 10;
        }

        footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: 20px;
            font-size: 9px;
            text-align: right;
            border-top: 1px solid #000;
            padding: 5px 10px;
        }

        .pagenum:before {
            content: " Page " counter(page);
         }

        section {
            margin-top: 20px;
            margin-bottom: 20px;
            page-break-inside: avoid; /* Prevent splitting inside section */
        }

        h1, h2, h3 {
            margin: 0;
        }

        h1 {
            font-size: 20px;
            font-weight: bold;
        }

        h2 {
            font-size: 18px;
            font-weight: bold;
            color: #002060;
            margin-top: 15px;
            margin-bottom: 5px;
        }

        h3 {
            font-size: 15px;
            font-weight: bold;
            margin-left: 20px;
            margin-top: 10px;
            margin-bottom: 2px;
        }

        p {
            margin-bottom: 6px;
            text-align: justify;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            margin-bottom: 15px;
        }
    </style>
</head>
<header style="position: fixed; top: 0; left: 0; right: 0;
               display: flex; justify-content: space-between; align-items: center;
               border-bottom: 1px solid #000; padding: 70px 50px; background: #fff; z-index: 20;">
    <!-- Left Logo -->
    <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('/storage/images/dswd2.png'))) }}"  style="position: absolute; left: 1px;"
         alt="DSWD Logo" width="140" height="50">

    <!-- Center Logo -->
    <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('/storage/images/pilipinas.png'))) }}"  style="position: absolute; left: 150px;"
         alt="Pilipinas Logo" width="60" height="45">

    <!-- Right Logo -->
    <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('/storage/images/dromic.png'))) }}" style="position: absolute; left: 550px;"
         alt="DROMIC Logo" width="150" height="50">
</header>

<body>

    <section style="text-align: center; margin-bottom: 30px;">
        <h1>DSWD DROMIC Report No. 1 on the {{ $sitrep->incident_type }}<br>
            in Brgy. {{ $sitrep->barangay }}, {{ $sitrep->municipality }}, {{ $sitrep->province }}
        </h1>
        <p style="font-size: 15px; text-align: center; margin-top: -2px;">as of {{ now()->timezone('Asia/Manila')->format('d F Y, h:i A') }}</p>
    </section>


    <section>
        <h2>I. Situation Overview</h2>
        @foreach(preg_split('/(?=In response|Likewise|Meanwhile|Further)/', e($sitrep->overview)) as $line)
            <p style="margin-left: 20px;">{{ $line }}</p>
        @endforeach
    </section>


    <section>
        <h2>II. Status of Affected Areas and Population</h2>
        <p style="margin-left: 20px;">
            A total of <b>{{ $sitrep->affected_families }} families</b> or <b>{{ $sitrep->affected_individuals }} persons</b> are affected
            in Barangay {{ $sitrep->barangay }}, {{ $sitrep->municipality }}, {{ $sitrep->province }}.
        </p>
    </section>


    <section>
        <h2>III. Status of Displaced Population</h2>

        <h3>a. Inside Evacuation Center</h3>
        <p style="margin-left: 40px;">
            There are <b>{{ $sitrep->affected_families }} families</b> or <b>{{ $sitrep->affected_individuals }} persons</b>
            currently taking temporary shelter in <b>{{ $sitrep->open_ec }} evacuation center</b> in {{ $sitrep->province }}.
        </p>

        <h3>b. Outside Evacuation Center</h3>
        <p style="margin-left: 40px;">
            There are <b>{{ $sitrep->displaced_family }} families</b> or <b>{{ $sitrep->displaced_individual }} persons</b>
            temporarily staying with their relatives and/or friends.
        </p>

        <h3>c. Total Displaced Population</h3>
        <p style="margin-left: 40px;">
            There are <b>{{ $sitrep->affected_families }} families</b> or <b>{{ $sitrep->affected_individuals }} persons</b>
            displaced in Barangay {{ $sitrep->barangay }}, {{ $sitrep->municipality }}, {{ $sitrep->province }}.
        </p>
    </section>

    @php
        $totalDamaged = ($sitrep->totally_damaged ?? 0) + ($sitrep->partially_damaged ?? 0);
    @endphp
    <section>
        <h2>IV. Damaged Houses</h2>
        <p style="margin-left: 20px;">
            A total of <b>{{ $totalDamaged }}</b> houses were damaged; of which,
            <b>{{ $sitrep->totally_damaged ?? 0 }}</b> are <b>totally damaged</b> and
            <b>{{ $sitrep->partially_damaged ?? 0 }}</b> are <b>partially damaged</b>
            in Barangay {{ $sitrep->barangay }}, {{ $sitrep->municipality }}, {{ $sitrep->province }}.
        </p>
    </section>


    @php
    $dswd = $sitrep->dswd_cost ?? 0;
    $lgu = $sitrep->lgu_cost ?? 0;
    $ngo = $sitrep->ngo_cost ?? 0;
    $partners = $sitrep->partners_cost ?? 0;

    $total = $dswd + $lgu + $ngo + $partners;
    @endphp

    <section>
        <h2>V. Cost of Humanitarian Assistance Provided</h2>
        <p style="margin-left: 20px; text-align: justify;">
            A total of <span style="font-family: 'DejaVu Sans', sans-serif;">₱</span>{{ number_format($total, 2) }}
            worth of assistance was provided to the affected families; of which,
            <span style="font-family: 'DejaVu Sans', sans-serif;">₱</span>{{ number_format($dswd, 2) }} from DSWD,
            <span style="font-family: 'DejaVu Sans', sans-serif;">₱</span>{{ number_format($lgu, 2) }} from LGUs,
            <span style="font-family: 'DejaVu Sans', sans-serif;">₱</span>{{ number_format($ngo, 2) }} from NGOs,
            and <span style="font-family: 'DejaVu Sans', sans-serif;">₱</span>{{ number_format($partners, 2) }} from other partners.
        </p>
    </section>

    <!-- Signatures -->
    <table style="width: 100%; border-collapse: collapse; border: 0;">
        <tr> <!-- Column headers -->
            <td style="width: 33%; padding: 5px; text-align: left; font-weight: bold; border: 0;"> Releasing Officer: </td>
            <td style="width: 33%; padding: 5px; text-align: left; font-weight: bold; border: 0;"> Noted by: </td>
            <td style="width: 33%; padding: 5px; text-align: left; font-weight: bold; border: 0;"> Approved by: </td>
        </tr>
        <tr> <!-- Names -->
            <td style="width: 33%; padding: 60px 5px 5px 5px; text-align: left; vertical-align: bottom; border: 0; text-decoration: underline; font-weight: bold;"> VILMA R. SERRANO </td>
            <td style="width: 33%; padding: 60px 5px 5px 5px; text-align: left; vertical-align: bottom; border: 0; text-decoration: underline; font-weight: bold;"> ARMONT C. PECINA </td>
            <td style="width: 33%; padding: 60px 5px 5px 5px; text-align: left; vertical-align: bottom; border: 0; text-decoration: underline; font-weight: bold;"> VENUS F. REBULDELA </td>
        </tr>
        <tr> <!-- Titles -->
            <td style="width: 33%; padding: 4px 5px 5px 5px; text-align: left; vertical-align: top; border: 0;"> SWO IV / OIC DRMD Chief </td>
            <td style="width: 33%; padding: 4px 5px 5px 5px; text-align: left; vertical-align: top; border: 0;"> ARD for Operations </td>
            <td style="width: 33%; padding: 4px 5px 5px 5px; text-align: left; vertical-align: top; border: 0;"> Regional Director </td>
        </tr>
    </table>
</body>

<footer>
    <div>
        DSWD DROMIC Report No. 1 on the {{ $sitrep->incident_type }} in {{ $sitrep->barangay }}, {{ $sitrep->municipality }}, {{ $sitrep->province }} as of {{ \Carbon\Carbon::parse(now()->timezone('Asia/Manila'))->format('d F, Y, h:i A') }} | <span class="pagenum"></span>
    </div>
</footer>
</html>
