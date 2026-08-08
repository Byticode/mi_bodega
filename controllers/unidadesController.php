<?php 

class UnidadesController extends BaseController
{
    private $unidadModel;

    public function __construct()
    {
        $this->requireAuth();
        $this->unidadModel = new Unidad();
    }

    public function listar()
    {
        $unidades = $this->unidadModel->listar();
        include RUTA_APP . '/views/unidades/unidades.php';
        exit();
    }

    public function crear()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            list($unidad_nombre, $unidad_abreviatura) = $this->limpiarPOST();

            $verfduplicado = $this->unidadModel->verificarDuplicado($unidad_nombre);

            if ($verfduplicado) {
                $verfduplicadoAbreviatura = $this->unidadModel->verificarDuplicadoAbreviatura($unidad_abreviatura);
                
                if ($verfduplicadoAbreviatura) {
                    $resultado = $this->unidadModel->crear($unidad_nombre, $unidad_abreviatura);
                    if ($resultado) {
                        $this->setFlash('success', 'Unidad creada con éxito');
                        $this->redirect('unidades');
                    }
                } else {
                    $this->setFlash('error', 'Esta abreviatura ya existe');
                    $this->redirect('unidades');
                }
            } else {
                $this->setFlash('error', 'Esta unidad ya existe');
                $this->redirect('unidades');
            }
        } else {
            $this->listar();
        }
    }

    public function editar()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $unidad_id = $this->limpiarVerificarId();

            list($unidad_nombre, $unidad_abreviatura) = $this->limpiarPOST();

            $verifduplicado = $this->unidadModel->verificarDuplicadoId($unidad_nombre, $unidad_id);

            if ($verifduplicado) {
                $verifduplicadoAbreviatura = $this->unidadModel->verificarDuplicadoAbreviaturaId($unidad_abreviatura, $unidad_id);
                
                if ($verifduplicadoAbreviatura) {
                    $resultado = $this->unidadModel->editar($unidad_nombre, $unidad_abreviatura, $unidad_id);
                    
                    if ($resultado) {
                        $this->setFlash('success', 'Unidad editada con éxito');
                        $this->redirect('unidades');
                    }
                } else {
                    $this->setFlash('error', 'Esta abreviatura ya existe');
                    $this->redirect('unidades');
                }
            } else {
                $this->setFlash('error', 'Esta unidad ya existe');
                $this->redirect('unidades');
            }
        } else {
            $unidad_id = $this->limpiarVerificarId();
            $dato = $this->unidadModel->consultarPorId($unidad_id);

            include RUTA_APP . '/views/unidades/unidades-editar.php';
            exit();
        }
    }

    public function limpiarPOST()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre_input = isset($_POST['nombre']) ? ucwords(trim($_POST['nombre'])) : '';
            $abreviatura_input = isset($_POST['abreviatura']) ? strtoupper(trim($_POST['abreviatura'])) : '';

            if (strlen($nombre_input) < 2) {
                $this->setFlash('error', 'El nombre debe tener mínimo 2 caracteres');
                $this->redirect('unidades');
            }

            if (strlen($abreviatura_input) < 1) {
                $this->setFlash('error', 'La abreviatura debe tener al menos 1 caracter');
                $this->redirect('unidades');
            }

            return [$nombre_input, $abreviatura_input];
        }
    }

    public function limpiarVerificarId(): int
    {
        $id = $_GET['id'] ?? null;
        $unidad_id = $this->validateNumericId($id, 'unidades');

        $resultado = $this->unidadModel->limpiarVerificarId($unidad_id);

        if ($resultado) {
            return $unidad_id;
        } else {
            $this->setFlash('error', 'Unidad no encontrada');
            $this->redirect('unidades');
        }
    }
}