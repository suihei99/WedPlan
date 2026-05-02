<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Budget Report - WebPlan</title>
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

        .section-title {
            margin: 0.8rem 0 0.2rem;
            font-size: 1.08rem;
            font-weight: 700;
        }

        .category {
            border: 1px solid var(--line);
            border-radius: 12px;
            overflow: hidden;
            break-inside: avoid;
        }

        .category-head {
            background: var(--soft);
            border-bottom: 1px solid var(--line);
            padding: 0.8rem 1rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
        }

        .category-head h3 {
            margin: 0;
            font-size: 1rem;
        }

        .category-meta {
            color: var(--muted);
            font-size: 0.82rem;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.85rem;
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
            background: #fff;
        }

        .num {
            text-align: right;
            white-space: nowrap;
        }

        .empty {
            padding: 0.8rem 1rem;
            color: var(--muted);
            font-size: 0.85rem;
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

            .category {
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>
    @php
        $rows = collect($report['categories'] ?? []);
        $totalBudgetLimit = (float) ($summary['effective_budget_limit'] ?? 0);
        $totalAllocated = (float) ($summary['total_allocated'] ?? 0);
        $totalSpent = (float) ($summary['total_spent'] ?? 0);
        $remaining = (float) ($summary['remaining'] ?? 0);
    @endphp

    <div class="report-wrap">
        <div class="print-tools">
            <button type="button" class="print-btn" onclick="window.print()">Print / Save as PDF</button>
        </div>

        <header class="report-head">
            <h1 class="report-title">Wedding Budget & Expenses Report</h1>
            <p class="report-sub">
                Couple: {{ $couple->partner_1_name ?? 'Partner 1' }} & {{ $couple->partner_2_name ?? 'Partner 2' }}
                | Generated: {{ $report['generated_at'] ?? now()->format('Y-m-d H:i') }}
            </p>
        </header>

        <section class="summary-grid">
            <article class="summary-card">
                <small>Total Budget Limit</small>
                <strong>RM {{ number_format($totalBudgetLimit, 2) }}</strong>
            </article>
            <article class="summary-card">
                <small>Total Allocated</small>
                <strong>RM {{ number_format($totalAllocated, 2) }}</strong>
            </article>
            <article class="summary-card">
                <small>Total Spent</small>
                <strong>RM {{ number_format($totalSpent, 2) }}</strong>
            </article>
            <article class="summary-card">
                <small>Remaining</small>
                <strong>RM {{ number_format($remaining, 2) }}</strong>
            </article>
        </section>

        <h2 class="section-title">Category Breakdown</h2>

        @forelse($rows as $row)
            <section class="category">
                <header class="category-head">
                    <div>
                        <h3>{{ $row['category_name'] }}</h3>
                        <div class="category-meta">
                            Allocated: RM {{ number_format((float) ($row['allocated_amount'] ?? 0), 2) }} |
                            Spent: RM {{ number_format((float) ($row['spent_amount'] ?? 0), 2) }} |
                            {{ ($row['is_overspent'] ?? false)
                                ? 'Over Budget'
                                : 'Remaining: RM ' . number_format((float) ($row['remaining_amount'] ?? 0), 2) }}
                        </div>
                    </div>
                </header>

                @if(!empty($row['expenses']))
                    <table>
                        <thead>
                            <tr>
                                <th>Expense</th>
                                <th>Date Paid</th>
                                <th>Payment</th>
                                <th>Description</th>
                                <th class="num">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($row['expenses'] as $expense)
                                <tr>
                                    <td>{{ $expense['expense_name'] }}</td>
                                    <td>{{ $expense['date_paid'] ?? '-' }}</td>
                                    <td>{{ $expense['payment_method'] ?? '-' }}</td>
                                    <td>{{ $expense['description'] ?? '-' }}</td>
                                    <td class="num">RM {{ number_format((float) ($expense['amount'] ?? 0), 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p class="empty">No expenses recorded for this category.</p>
                @endif
            </section>
        @empty
            <p class="empty">No budget categories available.</p>
        @endforelse
    </div>
</body>
</html>
