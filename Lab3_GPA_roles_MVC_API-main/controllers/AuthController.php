<?php

declare(strict_types=1);

final class AuthController
{
    public function login(): void
    {
        if (Auth::check()) {
            redirect(url(Auth::defaultPageForRole(Auth::role()) ?? 'login'));
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleLoginPost();
            return;
        }
        $this->render('auth/login', ['title' => 'Sign in']);
    }

    private function handleLoginPost(): void
    {
        $email = trim((string) ($_POST['email'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');
        $errors = array_merge(
            Validator::required($email, 'Email'),
            Validator::email($email, 'Email'),
            Validator::required($password, 'Password')
        );
        if ($errors !== []) {
            Flash::set('danger', implode(' ', $errors));
            redirect(url('login'));
        }
        $user = User::findByEmail($email);
        if ($user === null || !password_verify($password, (string) $user['password'])) {
            Flash::set('danger', 'Invalid credentials.');
            redirect(url('login'));
        }
        Auth::login(
            (int) $user['id'],
            (string) $user['name'],
            (string) $user['email'],
            (string) $user['role']
        );
        Flash::set('success', 'Welcome back, ' . $user['name'] . '.');
        redirect(url(Auth::defaultPageForRole((string) $user['role']) ?? 'login'));
    }

    public function logout(): void
    {
        Auth::logout();
        redirect(url('login'));
    }

    /**
     * @param array<string,mixed> $data
     */
    private function render(string $view, array $data): void
    {
        $data['viewFile'] = __DIR__ . '/../views/' . $view . '.php';
        extract($data, EXTR_SKIP);
        require __DIR__ . '/../views/layouts/guest.php';
    }
}
