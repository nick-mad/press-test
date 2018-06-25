<!DOCTYPE html>
<html lang="ru">
<head>
    <title>profi.grade-ua.com</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.17.0/jquery.validate.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.17.0/localization/messages_ru.min.js"></script>
    <style>
        html, body {
            height: 100%;
        }

        body {
            background: url(/images/top_bg_vector.jpg) no-repeat 50% 50% transparent;
            background-size: cover;
            background-attachment: fixed;
        }

        .img-logo {
            height: 40px;
            margin-top: 5px;
        }
    </style>
    <script>
        $.validator.setDefaults({
            highlight: function (element) {
                $(element).closest('.form-group').addClass('has-error');
            },
            unhighlight: function (element) {
                $(element).closest('.form-group').removeClass('has-error');
            },
            errorElement: 'span',
            errorClass: 'help-block',
            errorPlacement: function (error, element) {
                error.addClass('help-block');
                if (element.prop('type') === 'checkbox') {
                    error.insertAfter(element.parent('label'));
                }
                else if (element.prop('type') === 'radio') {

                }
                else {
                    error.insertAfter(element);
                }
            }
        });
    </script>
</head>
<body>
<nav class="navbar navbar-default">
    <div class="separator"></div>
    <div class="container-fluid">
        <div class="navbar-header">
            <a href="<?php echo Flight::request()->base; ?>">
                <img src="<?php echo Flight::request()->base; ?>/../images/logo_dark.png" alt="" class="img-logo"/>
            </a>
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
        </div>
        <div class="collapse navbar-collapse" id="myNavbar">
            <?php if (Flight::get('admin')): ?>
                <ul class="nav navbar-nav navbar-right">
                    <li>
                        <a href="admin/logout">
                            <span class="glyphicon glyphicon-log-in"></span> Выйти
                        </a>
                    </li>
                </ul>
            <?php endif; ?>
        </div>
    </div>
</nav>
<div id="content">
    <?php if (isset($content)) {
        echo $content . PHP_EOL;
    } ?>
</div>
</body>
</html>