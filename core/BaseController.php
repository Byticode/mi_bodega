<?php

class BaseController {
    
    protected function render(string $view, array $data = []) {
        extract($data, EXTR_SKIP);
        include RUTA_APP . '/views/' . $view;
        exit;
    }

    protected function redirect(string $path = '') {
        redirect($path);
    }

    protected function setFlash(string $type, string $message) {
        set_flash($type, $message);
    }

    protected function normalizeText(string $value): string
    {
        return ucfirst(trim($value));
    }

    protected function validateMinLength(string $value, int $minLength, string $fieldName, string $redirectPath): string
    {
        $clean = trim($value);

        if (strlen($clean) < $minLength) {
            $this->setFlash('error', "{$fieldName} debe tener mínimo {$minLength} caracteres");
            $this->redirect($redirectPath);
        }

        return $clean;
    }

    protected function validateEmail(string $email, string $redirectPath): string
    {
        $clean = trim($email);

        if ($clean !== '' && !filter_var($clean, FILTER_VALIDATE_EMAIL)) {
            $this->setFlash('error', 'El correo no es válido');
            $this->redirect($redirectPath);
        }

        return $clean;
    }

    protected function validateUnique(bool $exists, string $message, string $redirectPath): void
    {
        if ($exists) {
            $this->setFlash('error', $message);
            $this->redirect($redirectPath);
        }
    }

}