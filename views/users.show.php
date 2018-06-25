<div class="container-fluid">
    <div class="row">
        <div class="col-sm-6 col-sm-offset-3">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 style="display:inline">Ответы пользователя <?= $user['name']; ?></h4>
                </div>
                <div class="panel-body">
                    <?php if (!empty($answers) && is_array($answers)) : ?>
                        <?php foreach ($answers as $item) : ?>
                            <div class="panel panel-default">
                                <div class="panel-heading"><?= $item['question']; ?></div>
                                <div class="panel-body">
                                    <?= $item['answer']['answer']; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <div class="panel-footer"></div>
            </div>
        </div>
    </div>
</div>
