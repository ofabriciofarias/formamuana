<?php

/**
 * Classe para implementar um Algoritmo Genético para seleção de candidatos.
 *
 * Este algoritmo evolui uma população de "cromossomos" (seleções de candidatos)
 * para encontrar a combinação que maximiza uma "função de fitness" (soma das pontuações
 * de vulnerabilidade dos candidatos selecionados).
 */
class GeneticAlgorithm {
    private $candidates;      // Todos os candidatos (alunos) inscritos
    private $candidateMap;    // Mapa de ID => dados do candidato para busca rápida
    private $allCandidateIds; // Array com todos os IDs de candidatos
    private $selectionSize;   // Quantidade de vagas a serem preenchidas
    private $fitnessFunction; // Função (callable) que calcula a pontuação de um candidato
    private $populationSize;  // Tamanho da população (quantos cromossomos por geração)
    private $generations;     // Número de gerações para evoluir
    private $mutationRate;    // Taxa de mutação
    private $population;      // A população atual de cromossomos

    /**
     * @param array $candidates Array de candidatos. Cada candidato deve ser um array com uma chave 'id'.
     * @param int $selectionSize O número de candidatos a serem selecionados (tamanho do cromossomo).
     * @param callable $fitnessFunction Uma função que aceita um candidato e retorna sua pontuação (fitness).
     */
    public function __construct(array $candidates, int $selectionSize, callable $fitnessFunction) {
        $this->candidates = $candidates;
        $this->selectionSize = $selectionSize;
        $this->fitnessFunction = $fitnessFunction;

        // Cria um mapa de ID => candidato para busca rápida e um array com todos os IDs
        $this->candidateMap = array_column($this->candidates, null, 'id');
        $this->allCandidateIds = array_keys($this->candidateMap);
    }

    /**
     * Inicializa a população com cromossomos aleatórios.
     * Cada cromossomo é uma seleção válida de candidatos.
     */
    private function initializePopulation() {
        $this->population = [];
        for ($i = 0; $i < $this->populationSize; $i++) {
            $chromosome = $this->allCandidateIds;
            shuffle($chromosome);
            $this->population[] = array_slice($chromosome, 0, $this->selectionSize);
        }
    }

    /**
     * Calcula o fitness total de um cromossomo (a soma do fitness de seus genes/candidatos).
     */
    private function calculateChromosomeFitness(array $chromosome) {
        $totalFitness = 0;
        foreach ($chromosome as $candidateId) {
            $candidateData = $this->candidateMap[$candidateId];
            $totalFitness += call_user_func($this->fitnessFunction, $candidateData);
        }
        return $totalFitness;
    }

    /**
     * Seleciona um cromossomo da população usando o método do torneio.
     */
    private function selection() {
        $tournamentSize = 5;
        $best = null;
        $bestFitness = -1;

        for ($i = 0; $i < $tournamentSize; $i++) {
            $randomChromosome = $this->population[array_rand($this->population)];
            $fitness = $this->calculateChromosomeFitness($randomChromosome);
            if ($fitness > $bestFitness) {
                $bestFitness = $fitness;
                $best = $randomChromosome;
            }
        }
        return $best;
    }

    /**
     * Cria um novo cromossomo (filho) a partir de dois pais.
     */
    private function crossover(array $parent1, array $parent2) {
        $combined = array_unique(array_merge($parent1, $parent2));
        shuffle($combined);
        return array_slice($combined, 0, $this->selectionSize);
    }

    /**
     * Aplica uma mutação aleatória em um cromossomo.
     */
    private function mutation(array $chromosome) {
        if ((mt_rand() / mt_getrandmax()) < $this->mutationRate) {
            $unselected = array_diff($this->allCandidateIds, $chromosome);
            if (!empty($unselected)) {
                $geneToMutateIndex = array_rand($chromosome);
                $swapWithGene = $unselected[array_rand($unselected)];
                $chromosome[$geneToMutateIndex] = $swapWithGene;
            }
        }
        return $chromosome;
    }

    /**
     * Executa o ciclo de evolução do algoritmo genético.
     */
    public function run(int $populationSize, int $generations, float $mutationRate = 0.1) {
        $this->populationSize = $populationSize;
        $this->generations = $generations;
        $this->mutationRate = $mutationRate;

        $this->initializePopulation();

        for ($i = 0; $i < $this->generations; $i++) {
            $newPopulation = [];
            $bestOfGeneration = $this->getBestChromosome();
            $newPopulation[] = $bestOfGeneration['chromosome']; // Elitismo

            while (count($newPopulation) < $this->populationSize) {
                $parent1 = $this->selection();
                $parent2 = $this->selection();
                $offspring = $this->crossover($parent1, $parent2);
                $offspring = $this->mutation($offspring);
                $newPopulation[] = $offspring;
            }
            $this->population = $newPopulation;
        }

        return $this->getBestChromosome();
    }

    /**
     * Encontra e retorna o melhor cromossomo da população atual.
     */
    public function getBestChromosome() {
        $bestChromosome = null;
        $maxFitness = -1;

        foreach ($this->population as $chromosome) {
            $fitness = $this->calculateChromosomeFitness($chromosome);
            if ($fitness > $maxFitness) {
                $maxFitness = $fitness;
                $bestChromosome = $chromosome;
            }
        }

        return ['chromosome' => $bestChromosome, 'fitness' => $maxFitness];
    }
}