<!DOCTYPE html>
<html>
<head>
    <title>Generate PDF Laravel 8 - NiceSnippets.com</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
</head>
<style type="text/css">
    h2{
        text-align: center;
        font-size:22px;
        margin-bottom:50px;
    }
    body{
        background:#f2f2f2;
    }
    .section{
        margin-top:30px;
        padding:50px;
        background:#fff;
    }
    .pdf-btn{
        margin-top:30px;
    }
</style>
<body>
<div class="container">
    @foreach($para['groups'] as $group)
        <table>
            <tr colspan="3">{{ $group['name'] }}</tr>
            @foreach($group['tgts'] as $tgts)
                @if($tgts['sub'] == 1)
                    <tr>
                        <td>{{ $tgts['numeric'] }}</td>
                        <td>{{ $tgts['teacher_subject_id'] }}</td>
                        <td>{{ $tgts['cabinet'] }}</td>
                    </tr>
                @endif
            @endforeach
        </table>
    @endforeach
</div>
</body>
</html>
