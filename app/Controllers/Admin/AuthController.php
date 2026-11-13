<?php

namespace App\Controllers\Admin;

use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\Shield\Validation\ValidationRules;

class AuthController extends AdminBaseController
{
    public function login(): string|RedirectResponse
    {
        if (auth()->loggedIn()) {
            return redirect()->to(config('Auth')->loginRedirect())->withCookies();
        }

        return $this->loginView();
    }

    public function attempt(): string|RedirectResponse
    {
        $email = strtolower(trim($this->postString('email')));
        $password = $this->postString('password');
        $credentials = [
            'email'    => $email,
            'password' => $password,
        ];
        $rules = (new ValidationRules())->getLoginRules();

        if (! $this->validateData($credentials, $rules)) {
            return $this->renderLogin($email, null, $this->validator->getErrors());
        }

        $authenticator = auth('session')->getAuthenticator();
        $result = $authenticator->remember(false)->attempt($credentials);

        if (! $result->isOK()) {
            return $this->renderLogin($email, 'Invalid email or password.', [], 401);
        }

        return redirect()->to(config('Auth')->loginRedirect())->withCookies();
    }

    public function logout(): RedirectResponse
    {
        $redirect = config('Auth')->logoutRedirect();
        $this->noStore();
        auth()->logout();

        return redirect()->to($redirect)->withCookies();
    }

    public function forbidden(): string
    {
        $this->noStore();
        $this->response->setStatusCode(403);

        return view('admin/forbidden', [
            'pageTitle' => 'Access denied | Membley Administration',
            'error'     => session('error'),
        ]);
    }

    private function loginView(): string
    {
        return $this->renderLogin('', null, []);
    }

    /**
     * @param array<string, string> $validationErrors
     */
    private function renderLogin(
        string $email,
        ?string $error,
        array $validationErrors,
        int $statusCode = 200,
    ): string {
        $this->noStore();
        $this->response->setStatusCode($statusCode);

        return view('admin/auth/login', [
            'pageTitle'       => 'Administrator sign in | Membley Security Group',
            'email'           => $email,
            'error'           => $error,
            'validationErrors' => $validationErrors,
        ]);
    }

}
