<!DOCTYPE html>
<html>
<head>
    <title>Users Data</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 30px;
            background: #f5f5f5;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 10px;
        }

        th {
            background: #222;
            color: white;
        }

        tr:nth-child(even) {
            background: #f9f9f9;
        }

        h1 {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

    <h1>Users Data</h1>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Email Verified At</th>
                <th>Created At</th>
            </tr>
        </thead>

        <tbody>
            @forelse($users as $user)
                <tr>
                    <td>{{ $user->id ?? '' }}</td>
                    <td>{{ $user->name ?? '' }}</td>
                    <td>{{ $user->email ?? '' }}</td>
                    <td>{{ $user->email_verified_at ?? '' }}</td>
                    <td>{{ $user->created_at ?? '' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">No users found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>