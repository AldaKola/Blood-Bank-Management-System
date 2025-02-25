<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">Set up Google Authenticator</div>

                    <div class="panel-body">
                        <h2 class="panel-title fw-bolder mb-1">Verify your 2FA Code ✉️</h2>
                        <div class="text-center">
                            <p>Scan this QR code with your Google Authenticator App:</p>
                            <div>
                                <img src="<?php echo $inlineUrl; ?>" alt="QRCode">
                            </div>
                        </div>
                        <form action="" method="POST">
                            <div class="form-group">
                                <label for="secret">Enter the code from Google Authenticator App</label>
                                <input type="text" name="secret" class="form-control" required>
                            </div>
                            <br>
                            <button type="submit" class="btn btn-primary">Verify</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
