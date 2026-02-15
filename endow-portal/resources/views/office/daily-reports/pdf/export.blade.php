<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Daily Reports - Employee Performance Summary</title>
    <style>
        @page {
            margin: 15mm 10mm;
        }
        
        body {
            font-family: 'DejaVu Sans', 'Arial', sans-serif;
            font-size: 9pt;
            color: #000000;
            line-height: 1.3;
            margin: 0;
            padding: 0;
        }
        
        .header {
            text-align: center;
            margin-bottom: 15px;
            padding-bottom: 8px;
            border-bottom: 2px solid #000000;
        }
        
        .header h1 {
            color: #000000;
            font-size: 16pt;
            margin: 0 0 3px 0;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .header .subtitle {
            color: #000000;
            font-size: 9pt;
            margin: 0;
        }
        
        .export-info {
            background: #f5f5f5;
            padding: 6px 8px;
            margin-bottom: 12px;
            border: 1px solid #000000;
            font-size: 8pt;
        }
        
        .export-info table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .export-info td {
            padding: 2px 5px;
        }
        
        .export-info td:first-child {
            font-weight: bold;
            width: 120px;
        }
        
        .summary-stats {
            margin-bottom: 15px;
            text-align: center;
        }
        
        .summary-stats table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #000;
        }
        
        .summary-stats th {
            background: #000;
            color: #fff;
            padding: 5px;
            font-size: 8pt;
            font-weight: bold;
        }
        
        .summary-stats td {
            border: 1px solid #000;
            padding: 5px;
            text-align: center;
            font-size: 10pt;
            font-weight: bold;
        }
        
        .report {
            margin-bottom: 12px;
            page-break-inside: avoid;
            border: 1px solid #000000;
            padding: 8px;
        }
        
        .report-header {
            margin-bottom: 6px;
            padding-bottom: 4px;
            border-bottom: 1px solid #000000;
        }
        
        .report-number {
            font-size: 7pt;
            font-weight: bold;
            background: #000;
            color: #fff;
            padding: 2px 6px;
            display: inline-block;
            margin-bottom: 3px;
        }
        
        .report-title {
            font-size: 11pt;
            font-weight: bold;
            color: #000;
            margin: 0 0 3px 0;
        }
        
        .report-meta {
            font-size: 7pt;
            color: #000;
            margin: 3px 0;
        }
        
        .report-meta-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 7pt;
            margin-top: 3px;
        }
        
        .report-meta-table td {
            padding: 2px 5px;
            border: 1px solid #ccc;
        }
        
        .report-meta-table td:first-child {
            font-weight: bold;
            width: 80px;
            background: #f0f0f0;
        }
        
        .label {
            display: inline-block;
            padding: 1px 5px;
            border: 1px solid #000;
            font-size: 7pt;
            font-weight: bold;
            text-transform: uppercase;
            margin-right: 4px;
        }
        
        .label-priority {
            background: #000;
            color: #fff;
        }
        
        .label-status {
            background: #fff;
            color: #000;
        }
        
        .report-content {
            margin: 6px 0;
            padding: 6px;
            background: #fafafa;
            border-left: 2px solid #000;
            font-size: 8pt;
            line-height: 1.4;
        }
        
        .section-title {
            font-weight: bold;
            color: #000;
            margin: 5px 0 3px 0;
            font-size: 8pt;
            text-transform: uppercase;
        }
        
        .tags {
            margin: 4px 0;
        }
        
        .tag {
            display: inline-block;
            padding: 1px 4px;
            border: 1px solid #000;
            font-size: 7pt;
            margin-right: 3px;
            margin-bottom: 2px;
        }
        
        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 7pt;
            color: #000;
            padding-top: 5px;
            border-top: 1px solid #000;
        }
        
        .page-break {
            page-break-after: always;
        }
        
        .text-bold {
            font-weight: bold;
        }
        
        .divider {
            border-bottom: 1px dashed #000;
            margin: 8px 0;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>Endow Connect | Daily Reports </h1>
        <div class="subtitle">Generated on {{ $exportDate->format('F d, Y \a\t h:i A') }}</div>
    </div>

    <!-- Export Summary Information -->
    <div class="export-info">
        <table>
            <tr>
                <td><strong>Total Reports:</strong></td>
                <td>{{ $reports->count() }}</td>
                <td><strong>Report Period:</strong></td>
                <td>
                    @if(!empty($filters['start_date']) && !empty($filters['end_date']))
                        {{ \Carbon\Carbon::parse($filters['start_date'])->format('M d, Y') }} - {{ \Carbon\Carbon::parse($filters['end_date'])->format('M d, Y') }}
                    @else
                        All Time
                    @endif
                </td>
            </tr>
            @if(!empty($filters['user_id']))
            <tr>
                <td><strong>Employee:</strong></td>
                <td colspan="3">{{ $reports->first()->submittedBy->name ?? 'N/A' }} ({{ $reports->first()->submittedBy->email ?? 'N/A' }})</td>
            </tr>
            @endif
            @if(!empty($filters['department']))
            <tr>
                <td><strong>Department:</strong></td>
                <td colspan="3">{{ $reports->first()->department->name ?? 'All Departments' }}</td>
            </tr>
            @endif
        </table>
    </div>

    <!-- Summary Statistics -->
    @php
        $statusCounts = [
            'draft' => $reports->where('status', 'draft')->count(),
            'submitted' => $reports->where('status', 'submitted')->count(),
            'pending_review' => $reports->where('status', 'pending_review')->count(),
            'in_progress' => $reports->where('status', 'in_progress')->count(),
            'approved' => $reports->where('status', 'approved')->count(),
            'rejected' => $reports->where('status', 'rejected')->count(),
            'completed' => $reports->where('status', 'completed')->count(),
        ];
        $priorityCounts = [
            'urgent' => $reports->where('priority', 'urgent')->count(),
            'high' => $reports->where('priority', 'high')->count(),
            'normal' => $reports->where('priority', 'normal')->count(),
            'low' => $reports->where('priority', 'low')->count(),
        ];
    @endphp
    
    <div class="summary-stats">
        <table>
            <thead>
                <tr>
                    <th>DRAFT</th>
                    <th>SUBMITTED</th>
                    <th>IN PROGRESS</th>
                    <th>APPROVED</th>
                    <th>REJECTED</th>
                    <th>COMPLETED</th>
                    <th>URGENT</th>
                    <th>HIGH</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $statusCounts['draft'] }}</td>
                    <td>{{ $statusCounts['submitted'] }}</td>
                    <td>{{ $statusCounts['in_progress'] }}</td>
                    <td>{{ $statusCounts['approved'] }}</td>
                    <td>{{ $statusCounts['rejected'] }}</td>
                    <td>{{ $statusCounts['completed'] }}</td>
                    <td>{{ $priorityCounts['urgent'] }}</td>
                    <td>{{ $priorityCounts['high'] }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Reports List -->
    @if($reports->count() > 0)
        @foreach($reports as $index => $report)
        <div class="report">
            <div class="report-header">
                <span class="report-number">REPORT #{{ $report->id }}</span>
                <div class="report-title">{{ $report->title }}</div>
                
                <table class="report-meta-table">
                    <tr>
                        <td>Employee</td>
                        <td>{{ $report->submittedBy->name ?? 'N/A' }}</td>
                        <td>Department</td>
                        <td>{{ $report->department->name ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td>Report Date</td>
                        <td>{{ $report->report_date->format('M d, Y') }}</td>
                        <td>Submitted</td>
                        <td>{{ $report->created_at->format('M d, Y h:i A') }}</td>
                    </tr>
                    <tr>
                        <td>Status</td>
                        <td><span class="label label-status">{{ strtoupper(str_replace('_', ' ', $report->status)) }}</span></td>
                        <td>Priority</td>
                        <td>
                            @if($report->priority)
                            <span class="label label-priority">{{ strtoupper($report->priority) }}</span>
                            @else
                            <span class="label label-status">NORMAL</span>
                            @endif
                        </td>
                    </tr>
                </table>
            </div>

            <!-- Work Description/Activities -->
            <div class="section-title">📋 Work Activities & Tasks Performed:</div>
            <div class="report-content">
                {!! strip_tags($report->description, '<p><br><b><strong><i><em><ul><ol><li>') !!}
            </div>

            <!-- Work Assignments Section -->
            @if($report->workAssignments && $report->workAssignments->count() > 0)
            <div class="section-title">✓ Linked Work Assignments ({{ $report->workAssignments->count() }}):</div>
            <div style="margin: 5px 0; padding: 5px; background: #f9f9f9; border: 1px solid #ddd;">
                @foreach($report->workAssignments as $idx => $assignment)
                <div style="margin: 4px 0; padding: 4px; border-left: 3px solid {{ $assignment->status === 'completed' ? '#198754' : '#0d6efd' }}; background: #fff; font-size: 8pt;">
                    <div style="font-weight: bold; margin-bottom: 2px;">
                        {{ $idx + 1 }}. {{ $assignment->title }}
                        <span style="padding: 1px 4px; border: 1px solid #000; font-size: 7pt; margin-left: 5px;">
                            {{ strtoupper($assignment->priority ?? 'NORMAL') }}
                        </span>
                        <span style="padding: 1px 4px; background: {{ $assignment->status === 'completed' ? '#198754' : '#0dcaf0' }}; color: #fff; font-size: 7pt; margin-left: 3px;">
                            {{ strtoupper(str_replace('_', ' ', $assignment->status)) }}
                        </span>
                    </div>
                    <div style="color: #555; font-size: 7pt; margin: 2px 0;">
                        {{ Str::limit($assignment->description, 120) }}
                    </div>
                    <div style="font-size: 7pt; color: #666;">
                        <strong>Assigned by:</strong> {{ $assignment->assignedBy->name ?? 'N/A' }}
                        @if($assignment->due_date)
                        | <strong>Due:</strong> {{ $assignment->due_date->format('M d, Y') }}
                        @endif
                        @if($assignment->completed_at)
                        | <strong>Completed:</strong> {{ $assignment->completed_at->format('M d, Y') }}
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
            @endif

            @if($report->tags)
            <div class="section-title">🏷️ Categories/Tags:</div>
            <div class="tags">
                @if(is_array($report->tags))
                    @foreach($report->tags as $tag)
                        <span class="tag">{{ $tag }}</span>
                    @endforeach
                @else
                    @foreach(explode(',', $report->tags) as $tag)
                        <span class="tag">{{ trim($tag) }}</span>
                    @endforeach
                @endif
            </div>
            @endif

            <!-- Approval/Review Information -->
            @if($report->approved_by || $report->review_comment || $report->rejection_reason)
            <div class="divider"></div>
            
            @if($report->approved_by)
            <div class="section-title">✓ Manager Approval:</div>
            <div style="font-size: 8pt; margin: 3px 0;">
                <strong>Approved by:</strong> {{ $report->approvedBy->name ?? 'N/A' }} on {{ $report->approved_at ? $report->approved_at->format('M d, Y h:i A') : 'N/A' }}
            </div>
            @endif
            
            @if($report->review_comment)
            <div class="section-title">💬 Manager Comments:</div>
            <div class="report-content">{{ $report->review_comment }}</div>
            @endif
            
            @if($report->rejection_reason)
            <div class="section-title">❌ Rejection Reason:</div>
            <div class="report-content" style="border-left-color: #000; background: #f0f0f0;">
                <strong>{{ $report->rejection_reason }}</strong>
            </div>
            @endif
            @endif
        </div>

        @if(($index + 1) % 4 === 0 && $index + 1 < $reports->count())
        <div class="page-break"></div>
        @endif
        @endforeach
    @else
        <div style="text-align: center; padding: 30px; color: #000; border: 1px solid #000; background: #f5f5f5;">
            <p style="font-size: 11pt; font-weight: bold; margin: 0;">No reports found matching the specified filters.</p>
        </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        <div><strong>Endow Connect</strong> - Employee Daily Reports</div>
        <div>Report Generated: {{ $exportDate->format('F d, Y \a\t h:i A') }} | Total Reports: {{ $reports->count() }} ></span></div>
    </div>
</body>
</html>
