<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <title>Quake Warning</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>
    <body>

        <table border="1">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Latitude</th>
                    <th>Longitude</th>
                    <th>Magnitude</th>
                    <th>Depth</th>
                    <th>From</th>
                    <th>Description</th>
                </tr>
            </thead>
            <tbody>
                @foreach($contents['quakes'] as $quake)
                <tr>
                    <td>{{date('M j, Y, h:i:s',strtotime($quake['date']))}}</td>
                    <td>{{$quake['lat']}}</td>
                    <td>{{$quake['lng']}}</td>
                    <td>{{$quake['mag']}}</td>
                    <td>{{$quake['depth']}}</td>
                    <td>{{$quake['from']}}</td>
                    <td>{{$quake['description']}}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

    </body>
</html>