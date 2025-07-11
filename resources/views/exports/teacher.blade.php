<table>
    <thead>
    <tr>
        <th style="background-color: #1a202c;color: white;font-size: xx-large;font-weight: bold;text-align: center">Name</th>
        <th style="background-color: #1a202c;color: white;font-size: xx-large;font-weight: bold;text-align: center">Code</th>
        <th style="background-color: #1a202c;color: white;font-size: xx-large;font-weight: bold;text-align: center">Email</th>
        <th style="background-color: #1a202c;color: white;font-size: xx-large;font-weight: bold;text-align: center">Department</th>
    </tr>
    </thead>
    <tbody>
    @foreach($teachers as $teacher)
        <tr>
            <td>{{ $teacher->user->name }}</td>
            <td>{{ $teacher->uni_code }}</td>
            <td>{{ $teacher->user->email }}</td>
            <td>{{ $teacher->department->name }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
