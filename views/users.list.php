<div class="container-fluid">
    <div class="row">
        <div class="col-sm-6">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 style="display:inline">Зарегистрированные пользователи флексопечати</h4>
                    <a href="#" class="pull-right" data-toggle="modal" data-target="#addUser">Добавить</a>
                </div>
                <div class="panel-body">
                    <?php if (!empty($flex) && is_array($flex)) : ?>
                        <table class="table table-hover">
                            <thead>
                            <tr>
                                <th>Имя</th>
                                <th>Email</th>
                                <th>Уведомление</th>
                                <th>Принял условия</th>
                                <th>Прошел тест</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($flex as $user) : ?>
                                <tr>
                                    <td><?= $user['name']; ?></td>
                                    <td><?= $user['email']; ?></td>
                                    <td><?= !empty($user['notice']) ? 'да' : 'нет'; ?></td>
                                    <td><?= !empty($user['agree']) ? 'да' : 'нет'; ?></td>
                                    <td><?= !empty($user['complete']) ? 'да' : 'нет'; ?></td>
                                    <td style="white-space: nowrap">
                                        <a href="admin/user/send/<?= $user['id']; ?>"
                                           class="btn btn-success btn-xs send"><span
                                                    class="glyphicon glyphicon-envelope"></span></a>
                                        <a href="admin/user/show/<?= $user['id']; ?>"
                                           class="btn btn-info btn-xs"><span
                                                    class="glyphicon glyphicon-user"></span></a>
                                        <a href="admin/user/delete/<?= $user['id']; ?>"
                                           class="btn btn-danger btn-xs delete"><span
                                                    class="glyphicon glyphicon-trash"></span></a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p>Нет зарегистрированых пользователей</p>
                    <?php endif; ?>
                </div>
                <div class="panel-footer">

                </div>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 style="display:inline">Зарегистрированные пользователи офсетной печати</h4>
                    <a href="#" class="pull-right" data-toggle="modal" data-target="#addUser">Добавить</a>
                </div>
                <div class="panel-body">
                    <?php if (!empty($offset) && is_array($offset)) : ?>
                        <table class="table table-hover">
                            <thead>
                            <tr>
                                <th>Имя</th>
                                <th>Email</th>
                                <th>Уведомление</th>
                                <th>Принял условия</th>
                                <th>Прошел тест</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($offset as $user) : ?>
                                <tr>
                                    <td><?= $user['name']; ?></td>
                                    <td><?= $user['email']; ?></td>
                                    <td><?= !empty($user['notice']) ? 'да' : 'нет'; ?></td>
                                    <td><?= !empty($user['agree']) ? 'да' : 'нет'; ?></td>
                                    <td><?= !empty($user['complete']) ? 'да' : 'нет'; ?></td>
                                    <td style="white-space: nowrap">
                                        <a href="admin/user/send/<?= $user['id']; ?>"
                                           class="btn btn-success btn-xs send"><span
                                                    class="glyphicon glyphicon-envelope"></span></a>
                                        <a href="admin/user/show/<?= $user['id']; ?>"
                                           class="btn btn-info btn-xs"><span
                                                    class="glyphicon glyphicon-user"></span></a>
                                        <a href="admin/user/delete/<?= $user['id']; ?>"
                                           class="btn btn-danger btn-xs delete"><span
                                                    class="glyphicon glyphicon-trash"></span></a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p>Нет зарегистрированых пользователей</p>
                    <?php endif; ?>
                </div>
                <div class="panel-footer">

                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div id="addUser" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <form id="addUserForm" action="admin/add-user" method="post" data-check-email="admin/check-email">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Добавление пользователя</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="name">ФИО:</label>
                        <input type="text" class="form-control" id="name" name="name" autocomplete="off" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email адрес:</label>
                        <input type="email" class="form-control" id="email" name="email" autocomplete="off" required>
                    </div>
                    <div class="form-group">
                        <label class="radio-inline">
                            <input type="radio" name="type" value="flex" required>На флекс</label>
                        <label class="radio-inline">
                            <input type="radio" name="type" value="offset" required>На офсет</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Сохранить</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Отмена</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    $(document).ready(function () {
        var form = $('#addUserForm');
        form.validate({
            rules: {
                email: {
                    remote: {
                        url: form.attr('data-check-email'),
                        type: 'post'
                    }
                }
            },
            messages: {
                email: {
                    remote: 'Этот email уже добавлен'
                }
            },
            submitHandler: function (form) {
                $.ajax({
                    url: form.action,
                    type: form.method,
                    dataType: 'json',
                    data: $(form).serialize()
                }).done(function (response) {
                    if (response['status'] === 1) {
                        location.reload();
                    } else {
                        alert('Что то пошло не так');
                    }
                });
                return false;
            }
        });

        //ajax для удаление строк
        $('.table').on('click', '.delete', function (e) {
            var url = $(this).attr('href'),
                parent = $(this).closest('tr'),
                table = $(this).closest('table');

            if (confirm('Точно удалить?')) {
                parent.remove();
                $.get(url);
                if (table.find('tbody tr').length === 0) {
                    table.replaceWith('<p>Нет зарегистрированых пользователей</p>');
                }

            }
            return false;
        });

        //ajax для удаление строк
        $('.table').on('click', '.send', function (e) {
            e.preventDefault();
            var url = $(this).attr('href'),
                parent = $(this).closest('tr'),
                table = $(this).closest('table');

            if (confirm('Точно отправить сообщение с сылкой на тест?')) {
                $.get(url, function (data) {
                    alert(data);
                    location.reload();
                });
            }
            return false;
        });
    });
</script>