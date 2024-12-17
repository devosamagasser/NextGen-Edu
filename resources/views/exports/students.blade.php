<table>
    <thead>
    <tr>
        <th style="background-color: #1a202c;color: white;font-size: xx-large;font-weight: bold;text-align: center">Name</th>
        <th style="background-color: #1a202c;color: white;font-size: xx-large;font-weight: bold;text-align: center">Code</th>
        <th style="background-color: #1a202c;color: white;font-size: xx-large;font-weight: bold;text-align: center">Email</th>
        <th style="background-color: #1a202c;color: white;font-size: xx-large;font-weight: bold;text-align: center">Department</th>
        <th style="background-color: #1a202c;color: white;font-size: xx-large;font-weight: bold;text-align: center">Semester</th>
    </tr>
    </thead>
    <tbody>
    @foreach($students as $student)
        <tr>
            <td>{{ $student->user->name }}</td>
            <td>{{ $student->uni_code }}</td>
            <td>{{ $student->user->email }}</td>
            <td>{{ $student->department->name }}</td>
            <td>{{ $student->semester->levels }}</td>
            <td>{{ $student->group }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
