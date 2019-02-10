<html>
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <style type="text/css">
        body{ font: 14px sans-serif; }
        .wrapper{ width: 350px; padding: 20px; }
    </style>
</head>
  <body>
    <div class="wrapper" style="align">
        <form method="post" action="send_link.php">
          <p>Enter Email Address To Send Password Link</p>
          <input type="text" class="form-control" name="email"><br />
          <input type="submit" class="btn btn-primary" name="submit_email">
        </form>
    </div>
  </body>
</html>