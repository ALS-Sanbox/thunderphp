<?php
// views/view.php

?>
<div style="padding:20px;max-width:600px;margin:auto;background: #eee;">
    <center>
        <h1><?= htmlspecialchars($title) ?></h1>
        <p><?= htmlspecialchars($message) ?></p>
    </center>
</div>

<div>
    <h1>{{ title }}</h1>
    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
            </tr>
        </thead>
        <tbody>
            {% for record in records %}
                <tr>
                    <td>{{ record.id }}</td>
                    <td>{{ record.name }}</td>
                    <td>{{ record.email }}</td>
                </tr>
            {% endfor %}
        </tbody>
    </table>
</div>
