<?php

use Illuminate\Database\Capsule\Manager as DB;

class ProfessorController
{
    public function dashboard()
    {
        if (!is_professor()) {
            header('Location: index.php');
            exit;
        }

        $turmas = Oferta::join('professor', 'oferta.id', '=', 'professor.oferta_id')
            ->where('professor.usuarios_id', $_SESSION['user_id'])
            ->orderBy('oferta.nome')
            ->select('oferta.*')
            ->get()
            ->toArray();

        require_once __DIR__ . '/../views/layouts/professor_header.php';
        require_once __DIR__ . '/../views/professor/dashboard.php';
        require_once __DIR__ . '/../views/layouts/admin_footer.php';
    }

    public function lancarFrequencia()
    {
        if (!is_professor()) { header('Location: index.php'); exit; }

        $ofertaId = $_GET['oferta_id'] ?? null;
        if (!$this->checkAccess($ofertaId)) {
            die('Acesso negado a esta turma.');
        }

        $oferta = Oferta::findById($ofertaId);

        $alunos = Aluno::join('alunos_has_oferta', 'alunos.id', '=', 'alunos_has_oferta.alunos_id')
            ->where('alunos_has_oferta.oferta_id', $ofertaId)
            ->where('alunos_has_oferta.status_selecao', 'Aprovado')
            ->orderBy('alunos.nome')
            ->select('alunos.id', 'alunos.nome')
            ->get()
            ->toArray();

        $frequenciasRaw = ListaFrequencia::join('aula', 'lista_frequencia.aula_id', '=', 'aula.id')
            ->where('aula.oferta_id', $ofertaId)
            ->where('lista_frequencia.presenca', 1)
            ->select('lista_frequencia.alunos_id', 'aula.data_aula')
            ->get()
            ->toArray();

        $frequencias = [];
        foreach ($frequenciasRaw as $freq) {
            $frequencias[$freq['alunos_id']][$freq['data_aula']] = true;
        }

        require_once __DIR__ . '/../views/layouts/professor_header.php';
        require_once __DIR__ . '/../views/professor/frequencia.php';
        require_once __DIR__ . '/../views/layouts/admin_footer.php';
    }

    public function consultarFrequencia()
    {
        if (!is_professor()) { header('Location: index.php'); exit; }

        $ofertaId = $_GET['oferta_id'] ?? null;
        if (!$this->checkAccess($ofertaId)) {
            die('Acesso negado a esta turma.');
        }

        $oferta = Oferta::findById($ofertaId);

        $alunos = Aluno::join('alunos_has_oferta', 'alunos.id', '=', 'alunos_has_oferta.alunos_id')
            ->where('alunos_has_oferta.oferta_id', $ofertaId)
            ->where('alunos_has_oferta.status_selecao', 'Aprovado')
            ->orderBy('alunos.nome')
            ->select('alunos.id', 'alunos.nome')
            ->get()
            ->toArray();

        $datas_aulas = Aula::where('oferta_id', $ofertaId)
            ->orderBy('data_aula')
            ->distinct()
            ->pluck('data_aula')
            ->toArray();

        $frequenciasRaw = ListaFrequencia::join('aula', 'lista_frequencia.aula_id', '=', 'aula.id')
            ->where('aula.oferta_id', $ofertaId)
            ->where('lista_frequencia.presenca', 1)
            ->select('lista_frequencia.alunos_id', 'aula.data_aula')
            ->get()
            ->toArray();

        $frequencias = [];
        foreach ($frequenciasRaw as $freq) {
            $frequencias[$freq['alunos_id']][$freq['data_aula']] = true;
        }

        $total_dias_aula       = count($datas_aulas);
        $frequencia_percentual = [];
        if ($total_dias_aula > 0) {
            foreach ($alunos as $aluno) {
                $dias_presente                        = isset($frequencias[$aluno['id']]) ? count($frequencias[$aluno['id']]) : 0;
                $frequencia_percentual[$aluno['id']] = round(($dias_presente / $total_dias_aula) * 100);
            }
        }

        $isReadOnly = true;

        require_once __DIR__ . '/../views/layouts/professor_header.php';
        require_once __DIR__ . '/../views/professor/frequencia.php';
        require_once __DIR__ . '/../views/layouts/admin_footer.php';
    }

    public function salvarFrequencia()
    {
        if (!is_professor()) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Acesso negado.']);
            exit;
        }

        header('Content-Type: application/json');
        $data = json_decode(file_get_contents('php://input'), true);

        $ofertaId = $data['ofertaId'] ?? null;
        $alunoId  = $data['alunoId']  ?? null;
        $dataAula = $data['dataAula'] ?? null;
        $presente = $data['presente'] ?? false;

        if (!$this->checkAccess($ofertaId) || !$alunoId || !$dataAula) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Dados inválidos ou acesso negado.']);
            exit;
        }

        try {
            DB::connection()->transaction(function () use ($ofertaId, $alunoId, $dataAula, $presente) {
                $aula = Aula::firstOrCreate(
                    ['oferta_id' => $ofertaId, 'data_aula' => $dataAula]
                );

                ListaFrequencia::where('aula_id', $aula->id)
                    ->where('alunos_id', $alunoId)
                    ->delete();

                if ($presente) {
                    ListaFrequencia::create([
                        'aula_id'   => $aula->id,
                        'alunos_id' => $alunoId,
                        'presenca'  => 1,
                    ]);
                }
            });

            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }

    public function notas()
    {
        if (!is_professor()) { header('Location: index.php'); exit; }

        $ofertaId = $_GET['oferta_id'] ?? null;
        if (!$this->checkAccess($ofertaId)) {
            die('Acesso negado a esta turma.');
        }

        $oferta = Oferta::findById($ofertaId);

        $alunos = Aluno::join('alunos_has_oferta', 'alunos.id', '=', 'alunos_has_oferta.alunos_id')
            ->where('alunos_has_oferta.oferta_id', $ofertaId)
            ->where('alunos_has_oferta.status_selecao', 'Aprovado')
            ->orderBy('alunos.nome')
            ->select('alunos.id', 'alunos.nome', 'alunos_has_oferta.nota_final')
            ->get()
            ->toArray();

        require_once __DIR__ . '/../views/layouts/professor_header.php';
        require_once __DIR__ . '/../views/professor/notas.php';
        require_once __DIR__ . '/../views/layouts/admin_footer.php';
    }

    public function salvarNotas()
    {
        if (!is_professor()) { header('Location: index.php'); exit; }

        $ofertaId = $_POST['oferta_id'] ?? null;
        if (!$this->checkAccess($ofertaId)) {
            die('Acesso negado a esta turma.');
        }

        $notas = $_POST['notas'] ?? [];

        foreach ($notas as $alunoId => $nota) {
            $notaFinal = ($nota !== null && $nota !== '')
                ? (float) str_replace(',', '.', $nota)
                : null;

            if ($notaFinal !== null && ($notaFinal < 0 || $notaFinal > 10)) {
                continue;
            }

            $concluiuCurso = $notaFinal !== null ? ($notaFinal >= 5 ? 1 : 0) : null;

            AlunoOferta::where('alunos_id', $alunoId)
                ->where('oferta_id', $ofertaId)
                ->update(['nota_final' => $notaFinal, 'concluiu_curso' => $concluiuCurso]);
        }

        header('Location: /professor/notas?oferta_id=' . $ofertaId . '&success=1');
        exit;
    }

    private function checkAccess($ofertaId)
    {
        if (!is_professor() || !$ofertaId) return false;

        return Professor::where('oferta_id', $ofertaId)
            ->where('usuarios_id', $_SESSION['user_id'])
            ->exists();
    }
}
