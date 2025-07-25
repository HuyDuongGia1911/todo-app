@php
    $stats = $summary->stats ?? [];
    $mergedTasks = $mergedTasks ?? []; 
@endphp

<table>
    <td colspan="2" style="font-weight: bold; font-size: 16px;">
    Tổng kết tháng {{ \Carbon\Carbon::parse($summary->month)->format('m/Y') }}
</td>

    <tr><td colspan="2"></td></tr>

    <tr>
        <td style="font-weight: bold;">Tiêu đề</td>
        <td>{{ $summary->title }}</td>
    </tr>
    <tr>
        <td style="font-weight: bold;">Đã chốt lúc</td>
        <td>{{ $summary->locked_at ?? 'Chưa chốt' }}</td>
    </tr>

    <tr><td colspan="2"></td></tr>

    <tr>
        <td style="font-weight: bold; vertical-align: top;">Nội dung</td>
        <td>{!! nl2br(e($summary->content)) !!}</td>
    </tr>

    <tr><td colspan="2"></td></tr>

    <tr>
        <td colspan="2" style="font-weight: bold;">Thống kê</td>
    </tr>
    <tr>
        <td>Tổng số task</td>
        <td>{{ $stats['total'] ?? 0 }}</td>
    </tr>
    <tr>
        <td>Đã hoàn thành</td>
        <td>{{ $stats['done'] ?? 0 }}</td>
    </tr>
    <tr>
        <td>Chưa hoàn thành</td>
        <td>{{ $stats['pending'] ?? 0 }}</td>
    </tr>
    <tr>
        <td>Quá hạn</td>
        <td>{{ $stats['overdue'] ?? 0 }}</td>
    </tr>

    <tr><td colspan="2"></td></tr>

    <tr>
        <td colspan="2" style="font-weight: bold;">Công việc trong tháng</td>
    </tr>
</table>

<table border="1">
    <thead>
        <tr>
            <th>STT</th>
            <th>Tên task</th>
            <th>Tiến độ</th>
            <th>Ngày thực hiện</th>
        </tr>
    </thead>
    <tbody>
        @foreach($mergedTasks as $idx => $task)
            <tr>
                <td>{{ $idx + 1 }}</td>
                <td>{{ $task['title'] }}</td>
                <td>{{ $task['progress'] }}</td>
                <td>{{ implode(', ', $task['dates'] ?? []) }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
