<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

    {{-- <script src="https://cdn.tailwindcss.com"></script> --}}

    <style>
        [x-cloak] {
            display: none;
        }

        .bg-blue-500 {
            background-color: #3b82f6;
        }
    </style>
</head>

<body>
    {{ $slot }}
</body>

</html>
