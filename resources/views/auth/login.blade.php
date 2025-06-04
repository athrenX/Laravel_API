<!DOCTYPE html>
<html>
<head>
    <title>Login Admin</title>
</head>
<body>
    <h2>Login Admin</h2>
    @if ($errors->any())
        <div style="color:red;">
            {{ $errors->first() }}
        </div>
    @endif
    <form method="POST" action="{{ route('login') }}">
        @csrf
        <label>Email:</label><br>
        <input type="email" name="email"><br>
        <label>Password:</label><br>
        <input type="password" name="password"><br><br>
        <button type="submit">Login</button>
    </form>
</body>
</html>
