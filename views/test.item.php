<div class="container">
    <div class="row">
        <div class="col-sm-10 col-sm-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3><?= $questionText; ?></h3>
                </div>
                <div class="panel-body">
                    <p>Залишилось: <span class="seconds">45</span> секунд</p>
                    <form id="question" action="<?= $formAction; ?>" method="post">
                        <div class="form-group">
                            <label for="answer">Відповідь:</label>
                            <textarea class="form-control" id="answer" name="answer" style="width: 100%" rows="10"
                                      autofocus></textarea>
                            <input type="hidden" name="question_id" value="<?= $questionId; ?>">
                        </div>
                        <button type="submit" class="btn btn-primary btn-lg">
                            Відповідь надано, перейти до наступного питання
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function () {

        document.ondragstart = noselect;
        document.onselectstart = noselect;
        document.oncontextmenu = noselect;
        function noselect() {return false;}

        var _Seconds = $('.seconds').text(),
            int;
        int = setInterval(function() {
            if (_Seconds > 0) {
                _Seconds--;
                $('.seconds').text(_Seconds);
            } else {
                clearInterval(int);
                $('#question').submit();
            }
        }, 1000);
    });
</script>