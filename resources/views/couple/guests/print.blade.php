<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guest List Report - WebPlan</title>
    <style>
        :root {
            --ink: #1f1a1d;
            --muted: #6e5b63;
            --line: #e9d6de;
            --soft: #fff7fa;
            --brand: #d54c6d;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 2rem;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            color: var(--ink);
            background: #fff;
        }

        .report-wrap {
            max-width: 980px;
            margin: 0 auto;
            display: grid;
            gap: 1rem;
        }

        .report-head {
            border: 1px solid var(--line);
            border-radius: 12px;
            padding: 1rem 1.2rem;
            background: linear-gradient(145deg, #fff9fb 0%, #ffeef3 100%);
        }

        .report-title {
            margin: 0;
            font-size: 1.5rem;
            font-weight: 800;
        }

        .report-sub {
            margin: 0.35rem 0 0;
            color: var(--muted);
            font-size: 0.92rem;
        }

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 0.7rem;
        }

        .summary-card {
            border: 1px solid var(--line);
            border-radius: 10px;
            padding: 0.8rem;
            background: #fff;
        }

        .summary-card small {
            color: var(--muted);
            display: block;
            font-size: 0.78rem;
        }

        .summary-card strong {
            display: block;
            margin-top: 0.25rem;
            font-size: 1rem;
            color: var(--brand);
        }

        .print-tools {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 0.5rem;
        }

        .print-btn {
            border: 0;
            border-radius: 8px;
            padding: 0.55rem 0.9rem;
            background: var(--brand);
            color: #fff;
            font-weight: 600;
            cursor: pointer;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.85rem;
            border: 1px solid var(--line);
            border-radius: 12px;
            overflow: hidden;
        }

        th,
        td {
            padding: 0.55rem 0.7rem;
            border-bottom: 1px solid #f2e7ec;
            vertical-align: top;
            text-align: left;
        }

        th {
            font-size: 0.76rem;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            color: var(--muted);
            background: var(--soft);
        }

        .pill {
            display: inline-flex;
            align-items: center;
            border-radius: 999px;
            padding: 0.18rem 0.58rem;
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
        }

        .pill.is-confirmed {
            background: #edfdf0;
            color: #28603a;
        }

        .pill.is-pending {
            background: #fef9f0;
            color: #8b5f1c;
        }

        .pill.is-declined {
            background: #fff1f4;
            color: #9f2943;
        }

        .muted {
            color: var(--muted);
        }

        @media print {
            body {
                padding: 0;
            }

            .print-tools {
                display: none;
            }

            .report-wrap {
                max-width: 100%;
                gap: 0.7rem;
            }
        }
    </style>
</head>
<body>
    @php
        $rows = collect($report['guests'] ?? []);
    @endphp

    <div class="report-wrap">
        <div class="print-tools">
            <button type="button" class="print-btn" onclick="window.print()">Print / Save as PDF</button>
        </div>

        <header class="report-head">
            <h1 class="report-title">Wedding Guest List Report</h1>
            <p class="report-sub">
                Couple: {{ $couple->partner_1_name ?? 'Partner 1' }} & {{ $couple->partner_2_name ?? 'Partner 2' }}
                | Generated: {{ $report['generated_at'] ?? now()->format('Y-m-d H:i') }}
            </p>
        </header>

        <section class="summary-grid">
            <article class="summary-card">
                <small>Total Guests</small>
                <strong>{{ (int) ($report['total_guests'] ?? 0) }}</strong>
            </article>
            <article class="summary-card">
                <small>Confirmed</small>
                <strong>{{ (int) ($report['confirmed_guests'] ?? 0) }}</strong>
            </article>
            <article class="summary-card">
                <small>Pending</small>
                <strong>{{ (int) ($report['pending_guests'] ?? 0) }}</strong>
            </article>
            <article class="summary-card">
                <small>Declined</small>
                <strong>{{ (int) ($report['declined_guests'] ?? 0) }}</strong>
            </article>
        </section>

        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Phone</th>
                    <th>Pax</th>
                    <th>Invite Code</th>
                    <th>RSVP</th>
                    <th>QR Ready</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rows as $guest)
                    @php
                        $status = strtolower($guest['rsvp_status'] ?? 'pending');
                        $statusClass = $status === 'confirmed' ? 'is-confirmed' : ($status === 'declined' ? 'is-declined' : 'is-pending');
                    @endphp
                    <tr>
                        <td>{{ $guest['name'] ?? '-' }}</td>
                        <td>{{ $guest['phone'] ?? '-' }}</td>
                        <td>{{ (int) ($guest['pax_count'] ?? 1) }}</td>
                        <td>{{ $guest['invite_code'] ?? '-' }}</td>
                        <td><span class="pill {{ $statusClass }}">{{ ucfirst($status) }}</span></td>
                        <td>{{ !empty($guest['qr_ready']) ? 'Yes' : 'No' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="muted">No guests available.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</body>
</html>
