<?php

namespace App\Controllers;

use App\Auth;
use App\Models\User;
use App\Repositories\MysqlUsersRepository;
use App\Repositories\UsersRepository;
use Ramsey\Uuid\Uuid;

class AuthController
{

    private UsersRepository $usersRepository;

    public function __construct()
    {
        $this->usersRepository = new MysqlUsersRepository();
    }

    public function showRegisterForm()
    {
        require_once "app/Views/register.template.php";
    }

    public function register()
    {
        $this->usersRepository->save(
            new User(
                Uuid::uuid4(),
                $_POST['email'],
                $_POST['name'],
                password_hash($_POST['password_confirmation'], PASSWORD_DEFAULT)
            )
        );
        header('Location: /');
    }

    public function showLoginForm()
    {
        require_once "app/Views/login.template.php";
    }

    public function login()
    {
        if (Auth::loggedIn()) redirect('/');

        $user = $this->usersRepository->getByEmail($_POST['email']);

        if ($user !== null && password_verify($_POST['password'], $user->getPassword())) {
            $_SESSION['authId'] = $user->getId();
            redirect('/tasks');
        }
        redirect('/login');

    }

    public function logout()
    {
        unset($_SESSION['authId ']);
        redirect('/');
    }
}