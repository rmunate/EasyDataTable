---
title: Assign
editLink: true
outline: deep
---

# Assign Same Object

There will surely be cases where the data you need to return to your table is the result of a series of processes and calculations performed in your backend. In these cases, we have provided the following option:

Let's assume you have the following data to return to the frontend, and note that you can have it either in an `Array` or in a Laravel `Collection`:

```php
$data = [
    "status"     => "A",
    "name"       => "JOHN ALEJANDRO DIAZ PINILLA",
    "birth_date" => "1993-11-30",
    "shift"      => "SHIFT 235 HOURS"
]

$data = new \Illuminate\Support\Collection([
    "status"     => "A",
    "name"       => "CARLOS GIOVANNY RODRIGUEZ TRIVIÑO",
    "birth_date" => "1992-10-19",
    "shift"      => "SHIFT 235 HOURS"
])
```

Now, to pass this as values to return to the frontend, you can do it as follows:

```php
$dataTable = new EasyDataTable();
$dataTable->fromData($data)
$dataTable->map(function ($row) {
    return [
        "status"     => $row->status,
        "name"       => $row->name,
        "birth_date" => $row->birth_date,
        "shift"      => $row->shift,
    ];
});
```

As you can see, we have used the `fromData` method to pass the data we had in either the `Array` or the `Collection`.