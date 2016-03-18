<?php namespace Helstern\Nomsky\Analyze;

use Helstern\Nomsky\Grammar\Conversions;
use Helstern\Nomsky\Grammar\Grammar;
use Helstern\Nomsky\Grammar\Symbol;
use Helstern\Nomsky\Grammar\Symbol\EpsilonSymbol;
use Helstern\Nomsky\GrammarAnalysis\Algorithms\EmptySetCalculator;
use Helstern\Nomsky\GrammarAnalysis\Algorithms\FirstSetCalculator;
use Helstern\Nomsky\GrammarAnalysis\Algorithms\FollowSetCalculator;
use Helstern\Nomsky\GrammarAnalysis\Algorithms\PredictSetCalculator;
use Helstern\Nomsky\GrammarAnalysis\ParseSets;
use Helstern\Nomsky\GrammarAnalysis\ParseTable\LLParseTableBuilder;
use Helstern\Nomsky\GrammarAnalysis\Production\ArraySet;
use Helstern\Nomsky\GrammarAnalysis\Production\Normalizer;

class LLAnalyzer
{
    /**
     * @param \Helstern\Nomsky\Grammar\Grammar $grammar
     * @param \Helstern\Nomsky\Grammar\Symbol\ArraySet $newNonTerminals
     *
     * @return array|\Helstern\Nomsky\GrammarAnalysis\Production\NormalizedProduction[]
     */
    private function normalize(Grammar $grammar, Symbol\ArraySet $newNonTerminals)
    {
        $conversions = new Conversions();
        $bnf = $conversions->ebnfToBnf($grammar);

        $normalizer = new Normalizer();
        $productions = $normalizer->normalizeList($bnf);

        foreach ($productions as $production) {
            $nonTerminal = $production->getLeftHandSide();
            $newNonTerminals->add($nonTerminal);
        }

        return $productions;
    }

    /**
     * Returns the lookahead level
     *
     * @param \Helstern\Nomsky\Grammar\Grammar $grammar
     *
     * @return int
     */
    public function analyze(Grammar $grammar)
    {
        $start = $grammar->getStartSymbol();

        $nonTerminals = new Symbol\ArraySet();
        $productions = $this->normalize($grammar, $nonTerminals);

        $nonTerminalsList = iterator_to_array($nonTerminals->getIterator());

        $emptySet = new Symbol\ArraySet();
        $firstSets = new ParseSets\ParseSets($nonTerminalsList);
        $followSets = new ParseSets\ParseSets($nonTerminalsList);
        $setsFactory = new ParseSets\SetsFactory();
        $lookAheadSets = $setsFactory->createEmptyLookAheadSets();

        $this->fillParseSets($start, $productions, $emptySet, $firstSets, $followSets, $lookAheadSets);

        $terminals = $grammar->getTerminals();
        if ($emptySet->count()) {
            $terminals[] = new EpsilonSymbol();
        }
        $nonTerminalsList = iterator_to_array($nonTerminals->getIterator());
        $lookahead = $this->calculateLookAhead($lookAheadSets, $terminals, $nonTerminalsList);
        return $lookahead;
    }

    /**
     * @param \Helstern\Nomsky\GrammarAnalysis\ParseSets\LookAheadSets $lookaheadSets
     * @param array $terminals
     * @param array $nonTerminals
     *
     * @return int
     */
    public function calculateLookAhead(ParseSets\LookAheadSets $lookaheadSets, array $terminals, array $nonTerminals)
    {
        $llTableBuilder = new LLParseTableBuilder();
        $llTable = $llTableBuilder
            ->addLookAheadSets($lookaheadSets)
            ->addTerminals($terminals)
            ->addNonTerminals($nonTerminals)
        ;
        $llTable->build();

        return 1;
    }

    /**
     * @param \Helstern\Nomsky\Grammar\Symbol\Symbol $start
     * @param array $productions
     * @param \Helstern\Nomsky\Grammar\Symbol\SymbolSet $emptySet
     * @param \Helstern\Nomsky\GrammarAnalysis\ParseSets\ParseSets $firstSets
     * @param \Helstern\Nomsky\GrammarAnalysis\ParseSets\ParseSets $followSets
     * @param \Helstern\Nomsky\GrammarAnalysis\ParseSets\LookAheadSets $lookaheadSets
     */
    private function fillParseSets(
        Symbol\Symbol $start,
        array $productions,
        Symbol\SymbolSet $emptySet,
        ParseSets\ParseSets $firstSets,
        ParseSets\ParseSets $followSets,
        ParseSets\LookAheadSets $lookaheadSets
    ) {
        $generator = new ParseSets\SetsGenerator(
            new EmptySetCalculator(),
            new FirstSetCalculator(),
            new FollowSetCalculator(new FirstSetCalculator()),
            new PredictSetCalculator(new FirstSetCalculator())
        );

        $generator
            ->generateEmptySet($productions, $emptySet)
            ->generateFirstSets($productions, $firstSets, $emptySet)
            ->generateFollowSets($productions, $start, $followSets, $firstSets)
            ->generateLookAheadSets($productions, $lookaheadSets, $firstSets, $followSets)
        ;
    }

}
