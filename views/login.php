<div class="container">
    <div class="col-sm-4 col-sm-offset-4">
        <div class="panel panel-primary panel-login">
            <div class="panel-heading"><h3 class="panel-title"><strong>Авторизация </strong></h3></div>
            <div class="panel-body">
                <?php if (isset($error)) { ?>
                    <div class="alert alert-danger alert-dismissable">
                        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                        <strong>Ошибка:<br></strong><?php echo $error; ?>
                    </div>
                <?php } ?>
                <?php if (isset($message)) { ?>
                    <div class="alert alert-success alert-dismissable">
                        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                        <?php echo $message; ?>
                    </div>
                <?php } ?>
                <form id="login_form" action="<?php //echo $formAction; ?>" method="post">
                    <div class="form-group">
                        <label for="password">Пароль</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Войти</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
