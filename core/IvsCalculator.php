<?php

/**
 * Calculadora do Índice de Vulnerabilidade Social (IVS) baseada no modelo do IPEA
 * (Atlas da Vulnerabilidade Social nos Municípios Brasileiros - Costa & Marguti, 2015).
 *
 * O IVS varia de 0,000 a 1,000 (quanto maior, maior a vulnerabilidade) e é composto
 * pela média aritmética de três dimensões:
 *  - IVS Infraestrutura Urbana
 *  - IVS Capital Humano
 *  - IVS Renda e Trabalho
 *
 * Faixas oficiais de classificação do IPEA:
 *  - 0,000 a 0,200: Muito Baixa
 *  - 0,201 a 0,300: Baixa
 *  - 0,301 a 0,400: Média
 *  - 0,401 a 0,500: Alta
 *  - 0,501 a 1,000: Muito Alta
 */
class IvsCalculator
{
    public const VULNERABILITY_THRESHOLD = 0.401; // Alta e Muito Alta vulnerabilidade (IPEA)

    /**
     * Normaliza texto para comparação segura (minúsculas e sem espaços extras).
     */
    private static function norm($value): string
    {
        return mb_strtolower(trim((string)($value ?? '')), 'UTF-8');
    }

    /**
     * Garante que o valor fique entre 0.0 e 1.0 com 3 casas decimais.
     */
    private static function clamp(float $value): float
    {
        return round(max(0.0, min(1.0, $value)), 3);
    }

    /**
     * Calcula IVS Infraestrutura Urbana (0.000 a 1.000) a partir das colunas de `alunos`.
     */
    public static function calcInfraestruturaUrbana(array $row): float
    {
        $score = 0.0;

        // 1. Zona de residência (peso até 0.40)
        $zona = self::norm($row['zona_residencia'] ?? '');
        if ($zona === 'rural') {
            $score += 0.40;
        } elseif ($zona === 'urbana') {
            $score += 0.05;
        } else {
            $score += 0.10;
        }

        // 2. Acesso à internet (peso até 0.30)
        $internet = self::norm($row['acesso_internet'] ?? '');
        if ($internet === '') {
            $score += 0.10;
        } elseif (str_contains($internet, 'não possui') || str_contains($internet, 'nao possui') || str_contains($internet, 'sem acesso')) {
            $score += 0.30;
        } elseif (
            str_contains($internet, 'dados') ||
            str_contains($internet, 'móveis') ||
            str_contains($internet, 'moveis') ||
            str_contains($internet, 'rádio') ||
            str_contains($internet, 'radio') ||
            str_contains($internet, 'outro')
        ) {
            $score += 0.15;
        }

        // 3. Dispositivo principal de acesso (peso até 0.15)
        $dispositivo = self::norm($row['dispositivo_principal'] ?? '');
        if ($dispositivo === '') {
            $score += 0.05;
        } elseif (str_contains($dispositivo, 'não possui') || str_contains($dispositivo, 'nao possui')) {
            $score += 0.15;
        } elseif (str_contains($dispositivo, 'celular')) {
            $score += 0.12;
        } elseif (str_contains($dispositivo, 'tablet') || str_contains($dispositivo, 'outro')) {
            $score += 0.08;
        }

        // 4. Comunidade tradicional / isolamento territorial (peso até 0.15)
        $comunidade = self::norm($row['comunidade_tradicional'] ?? '');
        if ($comunidade !== '' && $comunidade !== 'nenhuma' && $comunidade !== 'não' && $comunidade !== 'nao') {
            $score += 0.15;
        }

        return self::clamp($score);
    }

    /**
     * Calcula IVS Capital Humano (0.000 a 1.000) a partir das colunas de `alunos`.
     */
    public static function calcCapitalHumano(array $row): float
    {
        $score = 0.0;

        // 1. Nível de escolaridade (peso até 0.35)
        $esc = self::norm($row['nivel_escolaridade'] ?? '');
        if ($esc === '' || str_contains($esc, 'não informado') || str_contains($esc, 'nao informado')) {
            $score += 0.15;
        } elseif (str_contains($esc, 'fundamental incompleto')) {
            $score += 0.35;
        } elseif (str_contains($esc, 'fundamental completo')) {
            $score += 0.28;
        } elseif (str_contains($esc, 'médio incompleto') || str_contains($esc, 'medio incompleto')) {
            $score += 0.20;
        } elseif (str_contains($esc, 'médio completo') || str_contains($esc, 'medio completo')) {
            $score += 0.12;
        } elseif (str_contains($esc, 'superior incompleto')) {
            $score += 0.06;
        }

        // 2. Tipo de escola do ensino médio (peso até 0.20)
        $escola = self::norm($row['tipo_escola_ensino_medio'] ?? '');
        if ($escola === '') {
            $score += 0.10;
        } elseif (str_contains($escola, 'pública') || str_contains($escola, 'publica')) {
            $score += 0.20;
        } elseif (str_contains($escola, 'bolsista') || str_contains($escola, 'outro') || str_contains($escola, 'não se aplica') || str_contains($escola, 'nao se aplica')) {
            $score += 0.10;
        }

        // 3. Filhos / sobrecarga de cuidado familiar (peso até 0.25)
        $possuiFilhos = self::norm($row['possui_filhos'] ?? '');
        $qtdFilhos = (int)($row['quantidade_filhos'] ?? 0);
        if ($possuiFilhos === 'sim' || $qtdFilhos > 0) {
            if ($qtdFilhos >= 3) {
                $score += 0.25;
            } elseif ($qtdFilhos === 2) {
                $score += 0.20;
            } else {
                $score += 0.15;
            }
        }

        // 4. Pessoa com deficiência (peso até 0.20)
        $pcd = self::norm($row['possui_deficiencia'] ?? '');
        if ($pcd === 'sim') {
            $score += 0.20;
        }

        return self::clamp($score);
    }

    /**
     * Calcula IVS Renda e Trabalho (0.000 a 1.000) a partir das colunas de `alunos` e `alunos_has_oferta`.
     */
    public static function calcRendaTrabalho(array $row): float
    {
        $score = 0.0;

        // 1. Faixa de renda familiar (peso até 0.40) - suporta slugs e textos legíveis
        $renda = self::norm($row['renda_familiar'] ?? '');
        if ($renda === '' || str_contains($renda, 'nao_sei') || str_contains($renda, 'não sei')) {
            $score += 0.15;
        } elseif ($renda === 'ate_1_sm' || str_contains($renda, 'até 1') || str_contains($renda, 'ate 1')) {
            $score += 0.40;
        } elseif ($renda === 'entre_1_e_2_sm' || str_contains($renda, '1 a 2') || str_contains($renda, '1 e 2')) {
            $score += 0.25;
        } elseif ($renda === 'entre_2_e_3_sm' || str_contains($renda, '2 a 3') || str_contains($renda, '2 e 3')) {
            $score += 0.12;
        } elseif ($renda === 'entre_3_e_5_sm' || str_contains($renda, '3 e 5') || str_contains($renda, 'acima de 3')) {
            $score += 0.04;
        }

        // 2. Composição / densidade familiar (peso até 0.10)
        $membros = (int)($row['numero_membros_familia'] ?? 0);
        if ($membros >= 5) {
            $score += 0.10;
        } elseif ($membros >= 3) {
            $score += 0.05;
        }

        // 3. Situação de ocupação / trabalho remunerado (peso até 0.20)
        $trabalho = self::norm($row['exerce_atividade_remunerada'] ?? '');
        if ($trabalho === 'nao' || $trabalho === 'não') {
            $score += 0.20;
        } elseif ($trabalho === '') {
            $score += 0.05;
        }

        // 4. Beneficiário de programa social (peso até 0.20)
        $progSocial = self::norm($row['beneficiario_programa_social'] ?? '');
        if ($progSocial !== '' && !in_array($progSocial, ['nao', 'não', 'nenhum', 'nenhuma'], true)) {
            $score += 0.20;
        }

        // 5. Responsabilidade econômica familiar (peso até 0.10)
        $partEcon = self::norm($row['participacao_economica_familiar'] ?? '');
        if (str_contains($partEcon, 'responsavel') || str_contains($partEcon, 'responsável')) {
            $score += 0.10;
        } elseif (str_contains($partEcon, 'ajuda')) {
            $score += 0.05;
        }

        return self::clamp($score);
    }

    /**
     * Calcula os 4 índices (Geral + 3 dimensões) e a classificação IPEA.
     */
    public static function calculate(array $row): array
    {
        $infra = self::calcInfraestruturaUrbana($row);
        $capital = self::calcCapitalHumano($row);
        $renda = self::calcRendaTrabalho($row);
        $geral = self::clamp(($infra + $capital + $renda) / 3.0);

        return [
            'ivs_geral' => $geral,
            'ivs_infra' => $infra,
            'ivs_renda' => $renda,
            'ivs_capital' => $capital,
            'faixa_geral' => self::classify($geral),
            'faixa_infra' => self::classify($infra),
            'faixa_renda' => self::classify($renda),
            'faixa_capital' => self::classify($capital),
        ];
    }

    /**
     * Classifica um escore IVS (0.000 a 1.000) conforme faixas oficiais do IPEA.
     */
    public static function classify(float $score): array
    {
        if ($score <= 0.200) {
            return ['label' => 'Muito Baixa', 'slug' => 'muito_baixa', 'color' => '#2ecc71', 'vulnerable' => false];
        }
        if ($score <= 0.300) {
            return ['label' => 'Baixa', 'slug' => 'baixa', 'color' => '#27ae60', 'vulnerable' => false];
        }
        if ($score <= 0.400) {
            return ['label' => 'Média', 'slug' => 'media', 'color' => '#f39c12', 'vulnerable' => false];
        }
        if ($score <= 0.500) {
            return ['label' => 'Alta', 'slug' => 'alta', 'color' => '#e67e22', 'vulnerable' => true];
        }
        return ['label' => 'Muito Alta', 'slug' => 'muito_alta', 'color' => '#e74c3c', 'vulnerable' => true];
    }

    /**
     * Normaliza a zona de residência.
     */
    public static function normalizeZona(?string $zona): string
    {
        $z = self::norm($zona);
        if ($z === 'rural') {
            return 'Rural';
        }
        if ($z === 'urbana') {
            return 'Urbana';
        }
        return 'Não Informada';
    }

    /**
     * Determina se a inscrição foi alcançada (Aprovado).
     */
    public static function isAlcancado(?string $statusSelecao): bool
    {
        return self::norm($statusSelecao) === 'aprovado';
    }
}

