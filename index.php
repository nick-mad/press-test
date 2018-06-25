<?php
require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/config/db.php';
require __DIR__ . '/config/mail.php';

use \RedBeanPHP\R;
use \PHPMailer\PHPMailer\PHPMailer;

R::setup('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASSWD);

session_start();

/*Принятие соглашения и начало теста*/
Flight::route('POST /user/@token/agree', function ($token) {
    $user = R::findOne('users', ' token = ? ', [$token]);
    //если существует пользователь
    if (
        $user &&
        Flight::request()->method === 'POST' &&
        !empty(Flight::request()->data['agree'])
    ) {
        $user['agree'] = 1;
        $user['start'] = date('Y-m-d H:i:s');
        R::store($user);
        Flight::redirect('/user/' . $token);
    } else {
        Flight::redirect('http://profi.grade-ua.com');
    }
});

/*собственно тест*/
Flight::route('/user/@token', function ($token) {
    $user = R::findOne('users', ' token = ? ', [$token]);
    $questions = require __DIR__ . '/config/question.php';
    //если существует пользователь и есть вопросы
    if ($user && $questions) {
        //если тесты начаты то показваем вопрос иначе показываем правила теста
        if ((int)$user['agree'] === 1) {
            if ((int)$user['complete'] === 0) {
                //Определяем ид последнего сохраненного вопроса
                $lastQuestionId = (int)R::getCell('Select max(question_id) FROM  answers WHERE user_id = ?', [$user['id']]);
                $countQuestion = (int)R::getCell('Select count(question_id) FROM  answers WHERE user_id = ?', [$user['id']]);
                if (!$lastQuestionId) {
                    $lastQuestionId = 0;
                }
                $questionId = $lastQuestionId + 1;

                //если такой вопрос существует
                if (!empty($questions[$user['type']][$questionId])) {
                    //Создаем новый ответ
                    $answer = R::dispense('answers');
                    $answer['user_id'] = (int)$user['id'];
                    $answer['question_id'] = (int)$questionId;
                    $answer['answer'] = '';
                    R::store($answer);

                    Flight::render('test.item',
                        array(
                            'questionText' => $questions[$user['type']][$questionId],
                            'questionId' => $questionId,
                            'formAction' => '/user/' . $token . '/save'
                        ), 'content');
                    Flight::render('main', array());
                } else if ($countQuestion === 12) {
                    //если есть 12 ответов то все, на страницу с благодарностями
                    $user['complete'] = 1;
                    $user['end'] = date('Y-m-d H:i:s');
                    R::store($user);

                    Flight::render('test.complete', array(), 'content');
                    Flight::render('main', array());
                }
            } else {
                Flight::render('test.only.one', array(), 'content');
                Flight::render('main', array());
            }
        } else {
            Flight::render('test.agree', array('actionForm' => '/user/' . $token . '/agree'), 'content');
            Flight::render('main', array());
        }
    } else {
        Flight::redirect('http://profi.grade-ua.com');
    }
});

/*сохранение ответа*/
Flight::route('POST /user/@token/save', function ($token) {
    $user = R::findOne('users', ' token = ? ', [$token]);
    $questions = require __DIR__ . '/config/question.php';
    if (
        $user &&
        $questions &&
        (int)$user['agree'] === 1 &&
        Flight::request()->method === 'POST' &&
        isset(Flight::request()->data['answer']) &&
        !empty(Flight::request()->data['question_id']) &&
        !empty($questions[$user['type']][Flight::request()->data['question_id']])
    ) {
        $answer = R::findOne(
            'answers',
            ' user_id = ? AND question_id = ? ',
            [
                $user['id'],
                Flight::request()->data['question_id']
            ]
        );
        if ($answer) {
            $answer['answer'] = Flight::request()->data['answer'];
            $answer['answered_at'] = date('Y-m-d H:i:s');
            R::store($answer);
        }
    }

    Flight::redirect('/user/' . $token);
});

/*Администрирование испытуемых*/
Flight::route('/admin', function () {
    $data['flex'] = R::find('users', ' type = "flex" ORDER BY id');
    $data['offset'] = R::find('users', ' type = "offset" ORDER BY id');

    Flight::render('users.list', $data, 'content');
    Flight::render('main', array());
});

/*добавление пользователя*/
Flight::route('POST /admin/add-user', function () {
    if (Flight::request()->method === 'POST') {
        $user = R::dispense('users');
        $user['name'] = Flight::request()->data['name'];
        $user['email'] = Flight::request()->data['email'];
        $user['type'] = Flight::request()->data['type'];
        $user['notice'] = 0;
        $user['complete'] = 0;
        $user['token'] = md5(Flight::request()->data['email'] . Flight::request()->data['type'] . time());
        $id = R::store($user);

        $response['status'] = 0;
        if ($id) {
            $response['status'] = 1;
        }

        Flight::json($response);
    }
});

/*удаление пользователя*/
Flight::route('/admin/user/delete/@id:[0-9]+', function ($id) {
    R::hunt('users', ' id = ? ', [$id]);
    R::hunt('answers', ' user_id = ? ', [$id]);
    Flight::json('true');
});

/*просмотр результатов тестирования пользователя*/
Flight::route('/admin/user/show/@id:[0-9]+', function ($id) {

    $user = R::findOne('users', ' id = ? ', [$id]);
    $data = array();
    $answers = array();
    if ($user) {
        $questions = require __DIR__ . '/config/question.php';
        $questions = $questions[$user['type']];
        if ($questions && is_array($questions)) {
            foreach ($questions as $q_id => $question) {
                $answers[] = array(
                    'id' => $q_id,
                    'question' => $question,
                    'answer' => R::findOne(
                        'answers',
                        ' user_id = ? AND question_id = ? ',
                        [
                            $user['id'],
                            $q_id
                        ]
                    )
                );
            }
        }
        $data = array(
            'user' => $user,
            'answers' => $answers,
            'type' => $user['type']
        );
    }
    Flight::render('users.show', $data, 'content');
    Flight::render('main', array());
});

/*Проверка на уникальность email*/
Flight::route('POST /admin/check-email', function () {
    $response = 'true';
    if (
        Flight::request()->method === 'POST' &&
        !empty(Flight::request()->data['email'])
    ) {
        $user = R::find('users', ' email = ? ', [Flight::request()->data['email']]);
        if ($user) {
            $response = 'false';
        }
    }
    echo $response;
});

/*Отправка пригласительного пиьсма*/
Flight::route('/admin/user/send/@id:[0-9]+', function ($id) {

    $user = R::findOne('users', ' id = ? ', [$id]);

    if ($user) {
        $message = Flight::view()->fetch('mail',
            array(
                'name' => $user['name'],
                'link' => 'http://profi.grade-ua.com/test/user/' . $user['token']
            )
        );

        $fromEmail = 'org@grade-ua.com';
        $fromName = 'ПРОФІ - всеукраїнський конкурс професіоналів поліграфічної сфери';
        $subject = 'Запрошення на тестування';

        //отправка сообщения
        $mail = new PHPMailer();
        $mail->CharSet = 'UTF-8';
        $mail->Encoding = 'quoted-printable';
        $mail->setFrom($fromEmail, $fromName);
        $mail->addAddress($user['email']);
        $mail->Subject = '=?utf-8?B?' . $mail->encodeString(htmlspecialchars_decode($subject)) . '?=';
        $mail->msgHTML($message);
        $mail->isSMTP();
        $mail->Host = SMTP_SERVER;
        $mail->Port = 25;
        $mail->SMTPAuth = true;
        $mail->Username = SMTP_USERNAME;
        $mail->Password = SMTP_PASSWORD;
        $mail->XMailer = ' ';
        if ($mail->send()) {
            $user['notice'] = 1;
            R::store($user);
            echo 'Сообщение успешно отправлено';
        } else {
            echo $mail->ErrorInfo;
        }
    } else {
        echo 'Что-то пошло не так';
    }
});

/*Авторизация админа*/
Flight::route('/admin/login', function () {

    if (Flight::get('admin')) {
        Flight::redirect('/admin');
    }

    if (
        Flight::request()->method === 'POST' &&
        Flight::request()->data['password'] === '1111'
    ) {
        $_SESSION['is_admin'] = true;
        Flight::redirect('/admin');
    }

    Flight::render('login', array(), 'content');
    Flight::render('main', array());
});

/*Выйти из режима администрирования*/
Flight::route('/admin/logout', function () {
    $_SESSION['is_admin'] = false;
    Flight::redirect('/admin/login');
});

Flight::route('/', function () {
    Flight::redirect('/admin');
});

/*authorisation*/
Flight::before('start', function () {
    $auth = !empty($_SESSION['is_admin']) ? true : false;
    if ($auth) {
        Flight::set('admin', true);
    } else {
        Flight::set('admin', false);
    }

    $route = trim(Flight::request()->url, '/');
    $isAdmin = strpos($route, 'admin') === 0;
    $isLogin = strpos($route, 'admin/login') === 0;

    if ($isAdmin && !$isLogin && !$auth) {
        Flight::redirect('/admin/login');
        exit;
    }
    if ($isLogin && $auth) {
        Flight::redirect('/admin');
    }
});

Flight::map('notFound', function () {
    Flight::redirect('/');
});

Flight::start();
