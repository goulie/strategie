<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Membres Non à Jour - {{ $annee }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header h1 {
            color: #dc3545;
            margin-bottom: 5px;
            font-size: 18px;
        }

        .header-info {
            margin-bottom: 15px;
            background: #f8f9fa;
            padding: 10px;
            border-radius: 5px;
        }

        .stats {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
        }

        .stat-item {
            background: #e9ecef;
            padding: 8px 12px;
            border-radius: 5px;
            text-align: center;
            flex: 1;
            margin: 0 5px;
        }

        .stat-value {
            font-weight: bold;
            font-size: 16px;
            color: #dc3545;
        }

        .stat-label {
            font-size: 9px;
            color: #6c757d;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th {
            background-color: #dc3545;
            color: white;
            padding: 8px;
            text-align: left;
            font-size: 9px;
        }

        td {
            padding: 6px;
            border: 1px solid #dee2e6;
            font-size: 9px;
        }

        .status-ajour {
            background-color: #d4edda;
            color: #155724;
            padding: 3px 8px;
            border-radius: 3px;
        }

        .status-partiel {
            background-color: #fff3cd;
            color: #856404;
            padding: 3px 8px;
            border-radius: 3px;
        }

        .status-retard {
            background-color: #f8d7da;
            color: #721c24;
            padding: 3px 8px;
            border-radius: 3px;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .warning {
            background-color: #fff3cd !important;
        }

        .danger {
            background-color: #f8d7da !important;
        }

        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 8px;
            color: #6c757d;
        }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>LISTE DES MEMBRES NON À JOUR - {{ $annee }}</h1>
        <div class="header-info">
            <p><strong>Date d'export :</strong> {{ $dateExport }}</p>
            <p><strong>Année de référence :</strong> {{ $annee }}</p>
            @if ($retardMin > 0)
                <p><strong>Retard minimum :</strong> {{ $retardMin }} jours</p>
            @endif
        </div>
    </div>

    <div class="stats">
        <div class="stat-item">
            <div class="stat-value">{{ $total }}</div>
            <div class="stat-label">Membres non à jour</div>
        </div>
        <div class="stat-item">
            <div class="stat-value">
                {{ $membres->filter(function ($m) use ($annee) {
                        return $m->cotisations->where('annee_cotisation', $annee)->sum('montant') == 0;
                    })->count() }}
            </div>
            <div class="stat-label">Non cotisés</div>
        </div>
        <div class="stat-item">
            <div class="stat-value">
                {{ $membres->filter(function ($m) use ($annee) {
                        $total = $m->cotisations->where('annee_cotisation', $annee)->sum('montant');
                        $attendu = $m->plan_adhesion->price_xof ?? 0;
                        return $total > 0 && $total < $attendu;
                    })->count() }}
            </div>
            <div class="stat-label">Paiements partiels</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th width="5%">ID</th>
                <th width="15%">Membre</th>
                <th width="12%">Plan</th>
                <th width="8%" class="text-right">Montant attendu</th>
                <th width="8%" class="text-right">Payé {{ $annee }}</th>
                <th width="8%" class="text-right">Reste</th>
                <th width="10%">Statut</th>
                <th width="8%">Dernier paiement</th>
                <th width="8%">Jours retard</th>
                <th width="12%">Contact</th>
                <th width="6%">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($membres as $membre)
                @php
                    $totalPaye = $membre->cotisations->where('annee_cotisation', $annee)->sum('montant');
                    $montantAttendu = $membre->plan_adhesion->price_xof ?? 0;
                    $resteAPayer = max(0, $montantAttendu - $totalPaye);
                    $derniereCotisation = $membre->cotisations->first();
                    $joursRetard = 0;

                    if ($derniereCotisation && $derniereCotisation->date_echeance) {
                        $joursRetard = \Carbon\Carbon::parse($derniereCotisation->date_echeance)->diffInDays(
                            $dateReference,
                            false,
                        );
                    }

                    if ($totalPaye >= $montantAttendu) {
                        $statut = 'À jour';
                        $statutClass = 'status-ajour';
                    } elseif ($totalPaye > 0) {
                        $statut = 'Partiel';
                        $statutClass = 'status-partiel';
                    } else {
                        $statut = 'Non cotisé';
                        $statutClass = 'status-retard';
                    }

                    if ($joursRetard > 90) {
                        $rowClass = 'danger';
                    } elseif ($joursRetard > 30) {
                        $rowClass = 'warning';
                    } else {
                        $rowClass = '';
                    }
                @endphp
                <tr class="{{ $rowClass }}">
                    <td class="text-center">{{ $membre->id }}</td>
                    <td>{{ $membre->libelle_membre }}</td>
                    <td>{{ $membre->plan_adhesion->title_plan ?? 'N/A' }}</td>
                    <td class="text-right">{{ number_format($montantAttendu, 0, ',', ' ') }} CFA</td>
                    <td class="text-right">{{ number_format($totalPaye, 0, ',', ' ') }} CFA</td>
                    <td class="text-right">{{ number_format($resteAPayer, 0, ',', ' ') }} CFA</td>
                    <td class="text-center"><span class="{{ $statutClass }}">{{ $statut }}</span></td>
                    <td class="text-center">
                        @if ($derniereCotisation)
                            {{ $derniereCotisation->date_paiement->format('d/m/Y') }}
                        @else
                            Jamais
                        @endif
                    </td>
                    <td class="text-center">
                        @if ($joursRetard > 0)
                            {{ $joursRetard }} jours
                        @else
                            -
                        @endif
                    </td>
                    <td>{{ $membre->telephone ?? 'N/A' }}<br>{{ $membre->email ?? 'N/A' }}</td>
                    <td class="text-center">
                        Relancer<br>Contacter
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Rapport généré automatiquement - © {{ date('Y') }} - Pour usage interne uniquement</p>
        <p>Page 1/1 - Total: {{ $total }} membres</p>
    </div>
</body>

</html>
