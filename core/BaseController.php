<?php

class BaseController
{
    protected function render(string $view, array $data = [])
    {
        extract($data, EXTR_SKIP);
        include RUTA_APP . '/views/' . $view;
        exit;
    }

    protected function redirect(string $path = '')
    {
        redirect($path);
    }

    protected function setFlash(string $type, string $message)
    {
        set_flash($type, $message);
    }

    protected function requireAuth(): void
    {
        if (empty($_SESSION['usuario'])) {
            $this->setFlash('error', 'Debe iniciar sesión para acceder');
            $this->redirect('login');
        }
    }

    protected function requireRole(string $requiredRole, string $errorMessage = 'No tienes permisos de administrador para acceder a este módulo.'): void
    {
        $this->requireAuth();
        $userRole = $_SESSION['usuario']['usuario_rol'] ?? '';
        if ($userRole !== $requiredRole) {
            $this->renderError(403, $errorMessage);
        }
    }

    public function renderError(int $code = 404, string $errorMessage = ''): void
    {
        http_response_code($code);
        $viewFile = RUTA_APP . "/views/errors/{$code}.php";
        if (!file_exists($viewFile)) {
            $viewFile = RUTA_APP . "/views/errors/500.php";
        }
        extract(['errorMessage' => $errorMessage], EXTR_SKIP);
        include $viewFile;
        exit;
    }



    protected function getAuthUserId(): int
    {
        if (!empty($_SESSION['usuario']['usuario_id'])) {
            return (int) $_SESSION['usuario']['usuario_id'];
        }
        return 1; 
    }

    protected function generateCsrfToken(): string
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    protected function validateCsrfToken(?string $token, string $redirectPath): void
    {
        if (empty($token) || empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
            $this->setFlash('error', 'Token de seguridad no válido');
            $this->redirect($redirectPath);
        }
    }

    protected function validateNumericId($id, string $redirectPath): int
    {
        if (!is_numeric($id) || intval($id) <= 0) {
            $this->setFlash('error', 'ID de registro no válido');
            $this->redirect($redirectPath);
        }
        return (int) $id;
    }

    protected function jsonResponse(array $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
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